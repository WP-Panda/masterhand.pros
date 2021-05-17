<?php

class MailsterTemplate {

	public $raw;
	public $doc;
	public $data;
	public $modules;

	public $path;
	public $url;

	private $slug;
	private $file;

	private $templatepath;
	private $download_url = 'https://static.mailster.co/templates/mymail.zip';
	private $headers      = array(
		'name'        => 'Template Name',
		'label'       => 'Name',
		'uri'         => 'Template URI',
		'description' => 'Description',
		'author'      => 'Author',
		'author_uri'  => 'Author URI',
		'version'     => 'Version',
	);

	private $rtl;

	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @param unknown $file (optional)
	 */
	public function __construct( $slug = null, $file = 'index.html' ) {

		$this->rtl  = is_rtl();
		$this->file = basename( $file );

		$this->path = MAILSTER_UPLOAD_DIR . '/templates';
		$this->url  = MAILSTER_UPLOAD_URI . '/templates';

		if ( ! is_null( $slug ) ) {
			$this->load_template( $slug );
		}

	}


	/**
	 *
	 *
	 * @param unknown $modules      (optional)
	 * @param unknown $absolute_path (optional)
	 * @return unknown
	 */
	public function get( $modules = true, $absolute_path = false ) {
		if ( ! $modules ) {

			if ( ! $this->doc ) {
				return '';
			}

			$xpath           = new DOMXpath( $this->doc );
			$modulecontainer = $xpath->query( '//*/modules' );

			foreach ( $modulecontainer as $container ) {

				$activemodules = $this->get_modules( true );
				while ( $container->hasChildNodes() ) {
					$container->removeChild( $container->firstChild );
				}
				foreach ( $activemodules as $domElement ) {
					$domNode = $this->doc->importNode( $domElement, true );
					$container->appendChild( $domNode );
				}
			}

			$html = $this->doc->saveHTML();

		} else {

			$html = $this->raw;

		}
		if ( strpos( $html, 'data-editable' ) ) {

			$x    = $this->new_template_language( $html );
			$html = $x->saveHTML();

		}
		if ( $absolute_path ) {
			$html = $this->make_paths_absolute( $html );
		}

		$html = str_replace( array( '%7B', '%7D' ), array( '{', '}' ), $html );

		return $html;
	}


	/**
	 *
	 *
	 * @param unknown $html
	 * @return unknown
	 */
	private function make_paths_absolute( $html ) {

		preg_match_all( "/(src|background)=[\"'](.*)[\"']/Ui", $html, $images );
		preg_match_all( "/@import[ ]*['\"]{0,}(url\()*['\"]*([^;'\"\)]*)['\"\)]*/ui", $html, $assets );
		$images = array_unique( array_merge( $images[2], $assets[2] ) );
		foreach ( $images as $image ) {
			if ( empty( $image ) ) {
				continue;
			}
			if ( substr( $image, 0, 7 ) == 'http://' ) {
				continue;
			}

			if ( substr( $image, 0, 8 ) == 'https://' ) {
				continue;
			}

			$html = str_replace( $image, $this->url . '/' . $this->slug . '/' . $image, $html );
		}
		return $html;
	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function load_template( $slug = '' ) {

		$slug = basename( $slug );

		$this->templatepath = $this->path . '/' . $slug;
		$this->templateurl  = $this->url . '/' . $slug;

		$file = $this->templatepath . '/' . $this->file;

		if ( $this->rtl ) {
			$rtlfile = str_replace( '.html', '-rtl.html', $file );
			if ( file_exists( $rtlfile ) ) {
				$file = $rtlfile;
			}
		}

		if ( 'mymail' == $slug && ! file_exists( $file ) ) {
			mailster( 'templates' )->renew_default_template( 'mymail' );
		}

		if ( ! class_exists( 'DOMDocument' ) ) {
			wp_die( "PHP Fatal error: Class 'DOMDocument' not found" );
		}

		$doc                  = new DOMDocument();
		$doc->validateOnParse = true;
		$doc->formatOutput    = true;

		if ( file_exists( $file ) ) {
			$data = file_get_contents( $file );
			$data = str_replace( '//dummy.newsletter-plugin.com/', '//dummy.mailster.co/', $data );
		} else {
			$data = '{headline}<br>{content}';
		}

		$i_error = libxml_use_internal_errors( true );
		$doc->loadHTML( $data );
		libxml_clear_errors();
		libxml_use_internal_errors( $i_error );

		$doc = $this->new_template_language( $doc );

		if ( $logo_id = mailster_option( 'logo' ) ) {
			$xpath     = new DOMXPath( $doc );
			$logos     = $xpath->query( '//*/img[@label="Logo" or @label="logo" or @label="Your Logo"]' );
			$high_dpi  = mailster_option( 'high_dpi' ) ? 2 : 1;
			$logo_link = mailster_option( 'logo_link' );

			foreach ( $logos as $logo ) {

				$src    = $logo->getAttribute( 'src' );
				$width  = $logo->getAttribute( 'width' );
				$height = $logo->getAttribute( 'height' );

				if ( ! $src || ! $height || ! $width ) {
					continue;
				}

				$new_logo = mailster( 'helper' )->create_image( $logo_id, null, $width * $high_dpi, null, false );

				if ( ! $new_logo ) {
					continue;
				}
				$logo->setAttribute( 'data-id', $new_logo['id'] );
				$logo->setAttribute( 'width', $width );
				if ( $new_logo['asp'] ) {
					$logo->setAttribute( 'height', round( $width / $new_logo['asp'] ) );
				}
				$logo->setAttribute( 'src', $new_logo['url'] );

				if ( $logo_link ) {
					$link = $doc->createElement( 'a' );
					$link->setAttribute( 'href', $logo_link );
					$logo->parentNode->replaceChild( $link, $logo );
					$link->appendChild( $logo );
				}
			}
		}

		if ( $services = mailster_option( 'services' ) ) {

			$xpath   = new DOMXPath( $doc );
			$buttons = $xpath->query( '//*/a[@label="Social Media Button"]' );

			if ( $buttons->length ) {

				$base_path = $this->templatepath . '/img/social/';
				$base_url  = $this->templateurl . '/img/social/';
				if ( ! is_dir( $base_path ) ) {
					$base_path = MAILSTER_DIR . 'templates/mymail/img/social/';
					$base_url  = MAILSTER_URI . 'templates/mymail/img/social/';
				}

				$high_dpi = mailster_option( 'high_dpi' ) ? 2 : 1;

				$parent = $buttons->item( 0 )->parentNode;

				foreach ( $buttons as $button ) {

					$button->parentNode->removeChild( $button );

				}

				foreach ( $services as $service => $username ) {

					$url = mailster( 'helper' )->get_social_link( $service, $username );

					$icon = $base_path . 'dark/' . $service . '.png';
					if ( ! file_exists( $icon ) ) {
						$icon = $base_path . 'light/' . $service . '.png';
						if ( ! file_exists( $icon ) ) {
							$icon = $base_path . $service . '.png';
							if ( ! file_exists( $icon ) ) {
								continue;
							}
						}
					}

					$dimensions = getimagesize( $icon );

					if ( ! $dimensions ) {
						continue;
					}

					$img  = $doc->createElement( 'img' );
					$link = $doc->createElement( 'a' );

					$width  = round( $dimensions[0] / $high_dpi );
					$height = round( $dimensions[1] / $high_dpi );

					$img->setAttribute( 'src', str_replace( $base_path, $base_url, $icon ) );
					$img->setAttribute( 'width', $width );
					$img->setAttribute( 'height', $height );
					$img->setAttribute( 'style', "max-width:{$width}px;max-height:{$height}px;display:inline;" );
					$img->setAttribute( 'class', 'social' );
					$img->setAttribute( 'alt', esc_attr( sprintf( esc_html__( 'Share this on %s', 'mailster' ), ucwords( $service ) ) ) );

					$link->setAttribute( 'href', $url );
					$link->setAttribute( 'editable', '' );
					$link->setAttribute( 'label', ucwords( $service ) );
					$link->appendChild( $img );

					$parent->appendChild( $link );

				}
			}
		}

		$raw = $doc->saveHTML();

		$data = $this->get_template_data( $file );
		if ( $data['name'] ) {
			$raw        = preg_replace( '#<!--(.*?)-->#s', '', $raw, 1 );
			$this->data = $data;
		}

		$this->slug = $slug;
		$this->doc  = $doc;
		$this->raw  = $raw;

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function remove_template( $slug = '' ) {

		$this->templatepath = $this->path . '/' . $slug;

		if ( ! file_exists( $this->templatepath . '/index.html' ) ) {
			return false;
		}

		mailster_require_filesystem();

		global $wp_filesystem;
		if ( $wp_filesystem->delete( $this->templatepath, true ) ) {
			mailster( 'templates' )->remove_screenshot( $slug );
			return true;
		}

		return false;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function upload_template() {
		$result = wp_handle_upload(
			$_FILES['templatefile'],
			array(
				'mimes' => array( 'zip' => 'multipart/x-zip' ),
			)
		);
		if ( isset( $result['error'] ) ) {
			return $result;
		}

		mailster_require_filesystem();

		$tempfolder = MAILSTER_UPLOAD_DIR . '/uploads';

		wp_mkdir_p( $tempfolder );

		return mailster( 'templates' )->unzip_template( $result['file'], $tempfolder );

	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @param unknown $content   (optional)
	 * @param unknown $modules   (optional)
	 * @param unknown $active    (optional)
	 * @param unknown $overwrite (optional)
	 * @return unknown
	 */
	public function create_new( $name, $content = '', $modules = true, $active = false, $overwrite = false ) {

		if ( ! $this->slug ) {
			return false;
		}

		$filename = strtolower( sanitize_file_name( str_replace( '&amp;', '', $name ) ) . '.html' );

		if ( $name == esc_html__( 'Base', 'mailster' ) ) {
			$filename = 'index.html';
		}

		if ( $name == esc_html__( 'Notification', 'mailster' ) ) {
			$filename = 'notification.html';
		}

		if ( ! $overwrite && file_exists( $this->templatepath . '/' . $filename ) ) {
			$filename = str_replace( '.html', '-' . uniqid() . '.html', $filename );
		}

		$pre = '<!--' . "\n\n";

		foreach ( $this->data as $k => $v ) {
			$pre .= "\t" . $this->headers[ $k ] . ': ' . ( $k == 'label' ? $name : $v ) . "\n";
		}

		$pre .= "\n-->\n";

		if ( $has_modules = preg_match( '#<modules[^>]*>(.*)</modules>#s', $content, $hits ) ) {

			$original_modules_html = $modules ? $this->get_modules_html() : '';
			$custom_modules        = $hits[1];
			// remove all active
			$custom_modules = preg_replace( '#<module([^>]+)?( active="([^"]*)?")([^>]+)?>#', '<module$1$4>', $custom_modules );

			// add active
			if ( $active ) {
				$custom_modules = preg_replace( '#<module([^>]+)?>#', '<module$1 active>', $custom_modules );
			}

			$content = str_replace( $hits[0], '<modules>' . $custom_modules . $original_modules_html . '</modules>', $content );

		}

		// remove absolute path to images from the template
		$content = str_replace( 'src="' . $this->url . '/' . $this->slug . '/', 'src="', $content );

		$content = str_replace( array( '%7B', '%7D' ), array( '{', '}' ), $content );

		$content = mailster()->sanitize_content( $content );

		global $wp_filesystem;
		mailster_require_filesystem();

		if ( $wp_filesystem->put_contents( $this->templatepath . '/' . $filename, $pre . $content, FS_CHMOD_FILE ) ) {
			return $filename;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $activeonly (optional)
	 * @return unknown
	 */
	public function get_module_list( $activeonly = false ) {

		$modules = $this->get_modules( $activeonly );
		$count   = $modules->length;
		$list    = array();

		if ( ! $count ) {
			return $list;
		}

		$labels = array(
			'full size image'          => esc_html_x( 'Full Size Image', 'common module name', 'mailster' ),
			'intro'                    => esc_html_x( 'Intro', 'common module name', 'mailster' ),
			'separator'                => esc_html_x( 'Separator', 'common module name', 'mailster' ),
			'separator with button'    => esc_html_x( 'Separator with button', 'common module name', 'mailster' ),
			'full size text invert'    => esc_html_x( 'Full Size Text Invert', 'common module name', 'mailster' ),
			'iphone promotion'         => esc_html_x( 'iPhone Promotion', 'common module name', 'mailster' ),
			'macbook promotion'        => esc_html_x( 'Macbook Promotion', 'common module name', 'mailster' ),
			'quotation'                => esc_html_x( 'Quotation', 'common module name', 'mailster' ),
			'quotation left'           => esc_html_x( 'Quotation left', 'common module name', 'mailster' ),
			'quotation right'          => esc_html_x( 'Quotation right', 'common module name', 'mailster' ),
			'plans'                    => esc_html_x( 'Plans', 'common module name', 'mailster' ),
			'1/2 image full'           => sprintf( esc_html_x( '%s Image Full', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/2 text invert'          => sprintf( esc_html_x( '%s Text Invert', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/3 image full'           => sprintf( esc_html_x( '%s Image Full', 'common module name', 'mailster' ), '&#x2153;' ),
			'1/3 text invert'          => sprintf( esc_html_x( '%s Text Invert', 'common module name', 'mailster' ), '&#x2153;' ),
			'1/4 image full'           => sprintf( esc_html_x( '%s Image Full', 'common module name', 'mailster' ), '&#xBC;' ),
			'1/4 text invert'          => sprintf( esc_html_x( '%s Text Invert', 'common module name', 'mailster' ), '&#xBC;' ),
			'image on the left'        => esc_html_x( 'Image on the Left', 'common module name', 'mailster' ),
			'image on the right'       => esc_html_x( 'Image on the Right', 'common module name', 'mailster' ),
			'1/2 image on the left'    => sprintf( esc_html_x( '%s Image on the Left', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/2 image on the right'   => sprintf( esc_html_x( '%s Image on the Right', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/3 image on the left'    => sprintf( esc_html_x( '%s Image on the Left', 'common module name', 'mailster' ), '&#x2153;' ),
			'1/3 image on the right'   => sprintf( esc_html_x( '%s Image on the Right', 'common module name', 'mailster' ), '&#x2153;' ),
			'1/4 image on the left'    => sprintf( esc_html_x( '%s Image on the Left', 'common module name', 'mailster' ), '&#xBC;' ),
			'1/4 image on the right'   => sprintf( esc_html_x( '%s Image on the Right', 'common module name', 'mailster' ), '&#xBC;' ),
			'1/2 floating image left'  => sprintf( esc_html_x( '%s Floating Image left', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/2 floating image right' => sprintf( esc_html_x( '%s Floating Image right', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/2 image features left'  => sprintf( esc_html_x( '%s Image Features left', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/2 image features right' => sprintf( esc_html_x( '%s Image Features right', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/3 image features left'  => sprintf( esc_html_x( '%s Image Features left', 'common module name', 'mailster' ), '&#x2153;' ),
			'1/3 image features right' => sprintf( esc_html_x( '%s Image Features right', 'common module name', 'mailster' ), '&#x2153;' ),
			'1/1 text'                 => sprintf( esc_html_x( '%s Text', 'common module name', 'mailster' ), '1/1' ),
			'1/2 text'                 => sprintf( esc_html_x( '%s Text', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/3 text'                 => sprintf( esc_html_x( '%s Text', 'common module name', 'mailster' ), '&#x2153;' ),
			'1/4 text'                 => sprintf( esc_html_x( '%s Text', 'common module name', 'mailster' ), '&#xBC;' ),
			'1/1 column'               => sprintf( esc_html_x( '%s Text', 'common module name', 'mailster' ), '1/1' ),
			'1/2 column'               => sprintf( esc_html_x( '%s Text', 'common module name', 'mailster' ), '&#xBD;' ),
			'1/3 column'               => sprintf( esc_html_x( '%s Text', 'common module name', 'mailster' ), '&#x2153;' ),
			'1/4 column'               => sprintf( esc_html_x( '%s Text', 'common module name', 'mailster' ), '&#xBC;' ),
		);

		$screenshots = $this->get_module_screenshots();

		for ( $i = 0; $i < $count; $i++ ) {
			$label = $modules->item( $i )->getAttribute( 'label' );
			if ( isset( $labels[ strtolower( $label ) ] ) ) {
				$label = $labels[ strtolower( $label ) ];
			} elseif ( empty( $label ) ) {
				$label = sprintf( esc_html__( 'Module %s', 'mailster' ), '#' . ( $i + 1 ) );
			}
			$list[] = array(
				'name' => $label,
				'html' => $this->make_paths_absolute( $this->get_html_from_node( $modules->item( $i ) ) ),
			);
		}

		return $list;

	}


	/**
	 *
	 *
	 * @param unknown $activeonly (optional)
	 * @param unknown $separator  (optional)
	 * @return unknown
	 */
	public function get_modules_html( $activeonly = false, $separator = "\n\n" ) {

		return $this->make_paths_absolute( $this->get_html_from_nodes( $this->get_modules( $activeonly ), $separator ) );
	}


	/**
	 *
	 *
	 * @param unknown $activeonly (optional)
	 * @return unknown
	 */
	public function get_modules( $activeonly = false ) {

		if ( ! $this->slug ) {
			return false;
		}

		$xpath = new DOMXpath( $this->doc );

		$modules = ( $activeonly )
			? $xpath->query( '//*/module[@active]' )
			: $xpath->query( '//*/module' );

		return $modules;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_styles() {
		if ( ! $this->raw ) {
			return '';
		}

		preg_match_all( '#<style[^>]*>(.*?)<\/style>#is', $this->raw, $matches );
		$style = '';
		if ( ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $styleblock ) {
				$style .= $styleblock;
			}
		}

		return $style;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_head() {
		if ( ! $this->raw ) {
			return '';
		}

		if ( $pos = strpos( $this->raw, '<body' ) ) {
			return $this->make_paths_absolute( trim( substr( $this->raw, 0, $pos ) ) );
		}
		return '';

	}


	/**
	 *
	 *
	 * @param unknown $html (optional)
	 * @return unknown
	 */
	public function get_background_links( $html = '' ) {
		if ( empty( $html ) ) {
			$html = $this->raw;
		}

		if ( ! $html ) {
			return array();
		}

		preg_match_all( "/background=[\"'](.*)[\"']/Ui", $html, $links );

		return array_filter( array_unique( $links[1] ) );
	}


	/**
	 *
	 *
	 * @param unknown $doc_or_html
	 * @return unknown
	 */
	public function new_template_language( $doc_or_html ) {

		if ( ! is_string( $doc_or_html ) ) {
			$doc = $doc_or_html;
		} else {
			$i_error = libxml_use_internal_errors( true );

			$doc                  = new DOMDocument();
			$doc->validateOnParse = true;
			$doc->loadHTML( $doc_or_html );

			libxml_clear_errors();
			libxml_use_internal_errors( $i_error );

		}
		$xpath = new DOMXpath( $doc );

		// check if it's a new template
		$is_new_template = $doc->getElementsByTagName( 'single' );

		if ( $is_new_template->length ) {
			return $doc;
		}

		// Module container
		$modulecontainer = $xpath->query( "//*/div[@class='modulecontainer']" );

		foreach ( $modulecontainer as $container ) {

			$this->dom_rename_element( $container, 'modules', false );

		}

		// Modules
		$modules = $xpath->query( "//*/div[contains(concat(' ',normalize-space(@class),' '),' module ')]" );

		foreach ( $modules as $module ) {

			$label = $module->getAttribute( 'data-module' );
			$module->setAttribute( 'label', $label );
			$module->removeAttribute( 'data-module' );
			if ( $module->hasAttribute( 'data-auto' ) ) {
				$module->setAttribute( 'auto', null );
			}

			$this->dom_rename_element( $module, 'module' );

		}

		// images, editable
		$images = $xpath->query( '//*/img[@data-editable]' );

		foreach ( $images as $image ) {

			$label = $image->getAttribute( 'data-editable' );
			$image->setAttribute( 'editable', null );
			if ( $label ) {
				$image->setAttribute( 'label', $label );
			}

			$image->removeAttribute( 'data-editable' );

		}

		// other editable stuff
		$editables = $xpath->query( '//*[@data-editable]' );

		foreach ( $editables as $editable ) {

			$label = $editable->getAttribute( 'data-editable' );
			$editable->removeAttribute( 'data-editable' );
			if ( $label ) {
				$editable->setAttribute( 'label', $label );
			}

			if ( $editable->hasAttribute( 'data-multi' ) ) {

				$editable->removeAttribute( 'data-multi' );
				$this->dom_rename_element( $editable, 'multi' );
			} else {

				$this->dom_rename_element( $editable, 'single' );
			}
		}

		// wrap a diff around (for old templates)
		$editables = $doc->getElementsByTagName( 'single' );

		$div = $doc->createElement( 'div' );

		foreach ( $editables as $editable ) {

			$div_clone = $div->cloneNode();
			$editable->parentNode->replaceChild( $div_clone, $editable );
			$div_clone->appendChild( $editable );

		}
		$editables = $doc->getElementsByTagName( 'multi' );

		foreach ( $editables as $editable ) {

			$div_clone = $div->cloneNode();
			$editable->parentNode->replaceChild( $div_clone, $editable );
			$div_clone->appendChild( $editable );

		}

		// repeatable areas
		$repeatables = $xpath->query( '//*/*[@data-repeatable]' );

		foreach ( $repeatables as $repeatable ) {

			$label = $repeatable->getAttribute( 'data-repeatable' );
			$repeatable->setAttribute( 'repeatable', null );
			$repeatable->removeAttribute( 'data-repeatable' );

		}

		// buttons and buttongroups
		$buttons = $xpath->query( '//*/buttons' );

		if ( ! $buttons->length ) {

			$buttons = $xpath->query( "//*/div[@class='btn']" );

			foreach ( $buttons as $button ) {

				$button->removeAttribute( 'class' );
				$this->dom_rename_element( $button, 'buttons' );

			}

			$buttons = $doc->getElementsByTagName( 'buttons' );

			$new_div = $doc->createElement( 'div' );
			$new_div->setAttribute( 'class', 'btn' );

			foreach ( $buttons as $button ) {

				$div_clone = $new_div->cloneNode();
				$button->parentNode->replaceChild( $div_clone, $button );
				$div_clone->appendChild( $button );

				$children = $button->childNodes;
				foreach ( $children as $child ) {
					if ( strtolower( $child->nodeName ) == 'a' ) {
						$achildren = $child->childNodes;
						foreach ( $achildren as $achild ) {
							if ( strtolower( $achild->nodeName ) == 'img' ) {
								$label = $achild->getAttribute( 'label' );
								$achild->removeAttribute( 'editable' );
							}
						}

						$child->setAttribute( 'editable', null );
						$child->setAttribute( 'label', $label );
					}
				}
			}
		}

		$styles = $doc->getElementsByTagName( 'style' );

		foreach ( $styles as $style ) {

			$style->nodeValue = str_replace( 'img{outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;display:block;}', 'img{outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;display:block;max-width:100%;}', $style->nodeValue );

		}

		return $doc;

	}


	/**
	 *
	 *
	 * @param unknown $slugsonly (optional)
	 * @return unknown
	 */
	public function get_templates( $slugsonly = false ) {

		$templates = array();
		$files     = list_files( $this->path );
		sort( $files );
		foreach ( $files as $file ) {
			if ( basename( $file ) == 'index.html' ) {

				$filename = str_replace( $this->path . '/', '', $file );
				$slug     = dirname( $filename );
				if ( ! $slugsonly ) {
					$templates[ $slug ] = $this->get_template_data( $file );
				} else {
					$templates[] = $slug;
				}
			}
		}
		return $templates;

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function get_files( $slug = '' ) {

		if ( empty( $slug ) ) {
			$slug = $this->slug;
		}

		$templates = array();
		$files     = list_files( $this->path . '/' . $slug, 1 );

		sort( $files );

		$list = array(
			'index.html' => $this->get_template_data( $this->path . '/' . $slug . '/index.html' ),
		);

		if ( file_exists( $this->path . '/' . $slug . '/notification.html' ) ) {
			$list['notification.html'] = $this->get_template_data( $this->path . '/' . $slug . '/notification.html' );
		}

		foreach ( $files as $file ) {

			if ( strpos( $file, '.html' ) && is_file( $file ) ) {
				$list[ basename( $file ) ] = $this->get_template_data( $file );
			}
		}

		return $list;

	}


	/**
	 *
	 *
	 * @param unknown $slugsonly (optional)
	 * @return unknown
	 */
	public function get_versions( $slugsonly = false ) {

		$templates = $this->get_templates();
		$return    = array();
		foreach ( $templates as $slug => $data ) {

			$return[ $slug ] = $data['version'];
		}

		return $return;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_updates() {
		$updates = get_site_transient( 'mailster_updates' );
		if ( isset( $updates['templates'] ) ) {
			$updates = $updates['templates'];
		} else {
			$updates = array();
		}
		return $updates;
	}


	/**
	 *
	 *
	 * @param unknown $basefolder (optional)
	 */
	public function buttons( $basefolder = 'img' ) {

		$root = list_files( $this->path . '/' . $this->slug . '/' . $basefolder, 1 );

		sort( $root );
		$folders = array();

		// common_button_folder_names in use for __($name, 'mailster')
		esc_html__( 'light', 'mailster' );
		esc_html__( 'dark', 'mailster' );

		foreach ( $root as $file ) {

			if ( ! is_dir( $file ) ) {
				continue;
			}

			$rootbtn = '';

			?>
		<div class="button-nav-wrap">
			<?php
			$nav   = $btn = '';
			$id    = basename( $file );
			$files = list_files( dirname( $file ) . '/' . $id, 1 );
			natsort( $files );
			foreach ( $files as $file ) {
				if ( is_dir( $file ) ) {
					$file      = str_replace( '//', '/', $file );
					$name      = basename( $file );
					$folders[] = $name;
					$nav      .= '<a class="nav-tab" href="#buttons-' . $id . '-' . $name . '">' . __( $name, 'mailster' ) . '</a>';
					$btn      .= $this->list_buttons( substr( $file, 0, -1 ), $id );
				} else {
					if ( ! in_array( strrchr( $file, '.' ), array( '.png', '.gif', '.jpg', '.jpeg' ) ) ) {
						continue;
					}

					if ( $rootbtn ) {
						continue;
					}

					$rootbtn = $this->list_buttons( dirname( $file ), 'root' );

				}
			}

			if ( $nav ) :
				?>
		<div id="button-nav-<?php echo $id; ?>" class="button-nav nav-tab-wrapper hide-if-no-js" data-folders="<?php echo implode( '-', $folders ); ?>"><?php echo $nav; ?></div>
				<?php
			endif;
			echo $btn;
			?>
		</div>


			<?php if ( $rootbtn ) : ?>
		<div class="button-nav-wrap button-nav-wrap-root"><?php echo $rootbtn; ?></div>
				<?php
		endif;

		}

	}


	/**
	 *
	 *
	 * @param unknown $folder
	 * @param unknown $id
	 * @return unknown
	 */
	public function list_buttons( $folder, $id ) {

		$files = list_files( $folder, 1 );

		$btn = '<ul class="buttons buttons-' . basename( $folder ) . '" id="tab-buttons-' . $id . '-' . basename( $folder ) . '">';

		foreach ( $files as $file ) {

			if ( is_dir( $file ) ) {
				continue;
			}

			if ( ! in_array( strrchr( $file, '.' ), array( '.png', '.gif', '.jpg', '.jpeg' ) ) ) {
				continue;
			}

			$filename = str_replace( $folder . '/', '', $file );
			$service  = substr( $filename, 0, strrpos( $filename, '.' ) );
			$btn     .= '<li><a class="btnsrc" title="' . $service . '" data-link="' . mailster( 'helper' )->get_social_link( $service, 'USERNAME' ) . '"><img src="' . str_replace( $this->path . '/', $this->url . '/', $file ) . '"></a></li>';

		}

		$btn .= '</ul>';

		return $btn;

	}


	/**
	 *
	 *
	 * @param unknown $file (optional)
	 * @return unknown
	 */
	public function get_raw_template( $file = 'index.html' ) {
		if ( ! file_exists( $this->path . '/' . $this->slug . '/' . $file ) ) {
			return false;
		}

		return file_get_contents( $this->path . '/' . $this->slug . '/' . $file );
	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @param unknown $file (optional)
	 * @return unknown
	 */
	public function get_module_screenshots( $slug = null, $file = null ) {

		if ( ! mailster_option( 'module_thumbnails' ) ) {
			return false;
		}

		$modules = $this->get_modules();

		if ( ! $modules->length ) {
			return;
		}

		if ( is_null( $slug ) ) {
			$slug = $this->slug;
		}

		if ( is_null( $file ) ) {
			$file = $this->file;
		}

		$filedir = MAILSTER_UPLOAD_DIR . '/templates/' . $slug . '/' . $file;
		$fileuri = MAILSTER_UPLOAD_URI . '/templates/' . $slug . '/' . $file;

		// prevent error output as 7.4 throws deprecate notice
		// $hash = hash( 'crc32', md5_file( $filedir ) );
		$hash = @base_convert( md5_file( $filedir ), 10, 36 );

		$screenshot_modules_folder     = MAILSTER_UPLOAD_DIR . '/screenshots/' . $slug . '/modules/' . $hash;
		$screenshot_modules_folder_uri = MAILSTER_UPLOAD_URI . '/screenshots/' . $slug . '/modules/' . $hash;

		if ( ! is_dir( $screenshot_modules_folder ) ) {

			mailster( 'templates' )->schedule_screenshot( $slug, $file, true, 0, false );

			return array();

		}

		$files = list_files( $screenshot_modules_folder, 1 );
		natsort( $files );
		$files = array_values( $files );

		$return = array();

		foreach ( $files as $screenshotfile ) {
			$return[] = $hash . '/' . basename( $screenshotfile );
		}

		if ( count( $return ) < $modules->length ) {
			mailster( 'templates' )->schedule_screenshot( $slug, $file, true, 10, false );
		}

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $nodes
	 * @param unknown $separator (optional)
	 * @return unknown
	 */
	private function get_html_from_nodes( $nodes, $separator = '' ) {

		$parts = array();

		if ( ! $nodes ) {
			return '';
		}

		foreach ( $nodes as $node ) {
			$parts[] = $this->get_html_from_node( $node );
		}

		return implode( $separator, $parts );
	}


	/**
	 *
	 *
	 * @param unknown $node
	 * @return unknown
	 */
	private function get_html_from_node( $node ) {

		$html = $node->ownerDocument->saveXML( $node );

		// remove CDATA elements (keep content)
		$html = preg_replace( '~<!\[CDATA\[\s*|\s*\]\]>~', '', $html );
		return $html;

	}


	/**
	 *
	 *
	 * @param object  $node
	 * @param unknown $name
	 * @param unknown $attributes (optional)
	 * @return unknown
	 */
	private function dom_rename_element( DOMElement $node, $name, $attributes = true ) {

		$renamed = $node->ownerDocument->createElement( $name );

		if ( $attributes ) {
			foreach ( $node->attributes as $attribute ) {
				$renamed->setAttribute( $attribute->nodeName, $attribute->nodeValue );
			}
		}
		while ( $node->firstChild ) {
			$renamed->appendChild( $node->firstChild );
		}

		return $node->parentNode->replaceChild( $renamed, $node );
	}


	/**
	 *
	 *
	 * @param unknown $file
	 * @return unknown
	 */
	private function get_template_data( $file ) {

		return mailster( 'templates' )->get_template_data( $file );

	}


}
