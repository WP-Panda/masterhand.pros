<?php
	/**
	 * The plugin bootstrap file
	 *
	 * @since             1.0.0
	 * @package           Banner Generator By Anton
	 *
	 * @wordpress-plugin
	 * Plugin Name:       Banner generator
	 * Description:       Banner and poster generator for /referrals page.
	 * Version:           1.0.0
	 * Authors:           web13, Anton
	 */

	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */

	define('PLUGIN_NAME_VERSION', '1.0.0');

	add_action( 'plugins_loaded', 'generate_banner_load', 0 );

	function generate_banner_load() {
		require_once get_template_directory() .  '/wpp/vendor/autoload.php';
		add_action( 'wp_ajax_save_banner', 'save_banner' );

		add_filter( 'template_include', 'page_banner_template_include', 1 );

		add_action( 'wp_ajax_generate_poster', 'generate_poster' );
		add_action( 'wp_ajax_delete_cache_poster', 'delete_cache_poster' );
	}

	function page_banner_template_include( $template ) {
		if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/referrals' ) !== false ) {
			add_filter( 'wp_title', function() {
				return __( 'Referrals' ) . ' | ';
			}, 1 );
			status_header( 200 );

			$new_template = locate_template( [ 'page-referrals.php' ] );
			if ( ! empty( $new_template ) ) {
				return $new_template;

			}
		}

		return $template;
	}

	function get_banner( $name_poster ) {
		$varsTpl = get_vars_for_banner();

		$pathTpl   = __DIR__ . DIRECTORY_SEPARATOR . 'template/';
		$pathCache = $pathTpl . 'cache';
		if ( ! file_exists( $pathCache ) ) {
			mkdir( $pathCache, 0755, true );
		}
		$fenom = \Fenom::factory( $pathTpl, $pathCache );
		$fenom->setOptions( \Fenom::AUTO_RELOAD );

		return $fenom->fetch( $name_poster . '.html', $varsTpl );
	}

	//заполнение данными
	function get_vars_for_banner() {
		global $user_ID, $ae_post_factory;

		$path                  = ! empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ? 'https://' : 'http://';
		$path                  .= $_SERVER[ 'HTTP_HOST' ];
		$varsTpl[ 'path_inc' ] = $path . '/wp-content/plugins/' . basename( __DIR__ ) . '/template';

		$post_object = $ae_post_factory->get( PROFILE );
		$profile_id  = get_user_meta( $user_ID, 'user_profile_id', true );
		$profile     = [];
		if ( $profile_id ) {
			$profile_post = get_post( $profile_id );
			if ( $profile_post && ! is_wp_error( $profile_post ) ) {
				$profile = $post_object->convert( $profile_post );
			}
		}

		$def_avatar = $varsTpl[ 'path_inc' ] . '/img/avatar.png';
		$avatar_id  = get_user_meta( $user_ID, 'et_avatar', true );
		if ( ! empty( $avatar_id ) ) {
			$varsTpl[ 'avatar_url' ] = wp_get_attachment_image_url( $avatar_id, 'full' );
			if ( ! file_exists( str_replace( $path, $_SERVER[ 'DOCUMENT_ROOT' ], $varsTpl[ 'avatar_url' ] ) ) ) {
				if ( ! file_exists( str_replace( $path, $_SERVER[ 'DOCUMENT_ROOT' ], $def_avatar ) ) ) {
					$varsTpl[ 'avatar_url' ] = get_avatar_url( $user_ID, "default=mystery" );
				} else {
					$varsTpl[ 'avatar_url' ] = $varsTpl[ 'path_inc' ] . '/img/avatar.png';
				}
				//$varsTpl['avatar_url'] = get_avatar_url($user_ID, "default=mystery");
			}
		} else {
			//$varsTpl['avatar_url'] = get_avatar_url($user_ID, "default=mystery");
			if ( ! file_exists( str_replace( $path, $_SERVER[ 'DOCUMENT_ROOT' ], $def_avatar ) ) ) {
				$varsTpl[ 'avatar_url' ] = get_avatar_url( $user_ID, "default=mystery" );
			} else {
				$varsTpl[ 'avatar_url' ] = $varsTpl[ 'path_inc' ] . '/img/avatar.png';
			}
		}

		$user_data                 = get_userdata( $user_ID );
		$varsTpl[ 'display_name' ] = $user_data->display_name;

		$profile_category = ! empty( $profile ) ? $profile->tax_input[ 'project_category' ] : null;
		if ( ! empty( $profile_category ) ) {
			foreach ( $profile_category as $item ) {
				$varsTpl[ 'category' ][] = $item->name;
			}
		} else {
			$varsTpl[ 'category' ][] = 'No categories selected';
		}

		$varsTpl[ 'refer_code' ] = get_referral_code_by_user( $user_ID );

		return $varsTpl;
	}

	function save_banner() {
		if ( ! empty( $_POST ) && ! empty( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'save_banner' && empty( $_POST[ 'img_str' ] ) ) {
			exit;
		}

		global $user_ID;
		$img_str  = $_POST[ 'img_str' ];
		$template = $_POST[ 'template' ];

		$img = str_replace( 'data:image/png;base64,', '', $img_str );
		$img = str_replace( ' ', '+', $img );

		$data = base64_decode( $img );

		$path_inc = $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-content/plugins/' . basename( __DIR__ );
		if ( ! file_exists( $path_inc . '/cache' ) ) {
			mkdir( $path_inc . '/cache' );
		}
		if ( ! file_exists( $path_inc . '/cache/' . $user_ID ) ) {
			mkdir( $path_inc . '/cache/' . $user_ID );
		}
		foreach ( glob( $path_inc . '/cache/' . $user_ID . '/' . $template . '_*' ) as $docFile ) {
			unlink( $docFile );
		}
		$rand      = time();
		$name_file = $template . '_' . $rand . '.png';
		$file      = $path_inc . '/cache/' . $user_ID . '/' . $name_file;

		$success = file_put_contents( $file, $data );

		if ( ! $success ) {
			echo 'error';
		} else {
			echo $user_ID . '/' . $name_file;
		}
		exit;
	}

	function toInt($str = 0)	{
		return is_numeric($str) || is_string($str)? intval($str) : 0;
	}

	/**
	 * get skills user with endorsement
	 * @param $userId
	 * @param int $currentUser
	 * @return array|null|object
	 */
	function for_user($userId, $currentUser = 0) {
		global $wpdb;

		$tb_prefix = $wpdb->prefix;
		define('tbSkill', 'skill');

		$tbEndorseSkill = $tb_prefix . 'skill_endorse';
		$skillSelected = '';
		if($currentUser){
			$skillSelected = "IF((SELECT sel.skill_id FROM {$tbEndorseSkill} sel
			WHERE sel.skill_id = su.id AND sel.user_id = su.user_id AND sel.user_endorse = {toInt($currentUser)}), 1, 0) as endorsed,";
		}
		$sql = "SELECT su.id, su.title,
		{$skillSelected}
		(SELECT COUNT(e.user_endorse) FROM {$tbEndorseSkill} e WHERE e.skill_id = su.id) as endorse
 		FROM {tbSkill} su
		WHERE su.user_id = {toInt($userId)}
		";

		return $wpdb->get_results($sql, ARRAY_A);
	}

	//заполнение постера данными
	function get_vars_for_poster() {
		global $user_ID, $ae_post_factory;

		$path                  = ! empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ? 'https://' : 'http://';
		$path                  .= $_SERVER[ 'HTTP_HOST' ];
		$varsTpl[ 'path_inc' ] = $path . '/wp-content/plugins/' . basename( __DIR__ ) . '/template';
		$varsTpl[ 'user_ID' ]  = $user_ID;

		$user_data   = get_userdata( $user_ID );
		$post_object = $ae_post_factory->get( PROFILE );
		$profile_id  = get_user_meta( $user_ID, 'user_profile_id', true );
		$profile     = [];
		if ( $profile_id ) {
			$profile_post = get_post( $profile_id );
			if ( $profile_post && ! is_wp_error( $profile_post ) ) {
				$profile = $post_object->convert( $profile_post );
			}
		}

		$skills = for_user( $user_ID, 0 );
		if ( ! empty( $skills ) ) {
			$count = count( $skills ) > 8 ? 8 : count( $skills ) - 1;
			for ( $i = 0; $i <= $count; $i ++ ) {
				$varsTpl[ 'skills' ][] = $skills[ $i ][ 'title' ];
			}
		} else {
			$varsTpl[ 'skills' ][] = 'No skills selected';
		}

		$profile_category = ! empty( $profile ) ? $profile->tax_input[ 'project_category' ] : null;
		if ( ! empty( $profile_category ) ) {
			foreach ( $profile_category as $item ) {
				$varsTpl[ 'category' ][] = $item->name;
			}
		} else {
			$varsTpl[ 'category' ][] = 'No categories selected';
		}

		$varsTpl[ 'experience' ]   = ! empty( $profile->et_experience ) ? $profile->et_experience : '<span>0</span>';
		$varsTpl[ 'display_name' ] = $user_data->display_name;

		$avatar_id = get_user_meta( $user_ID, 'et_avatar', true );
		if ( ! empty( $avatar_id ) ) {
			$varsTpl[ 'avatar_url' ] = wp_get_attachment_image_url( $avatar_id, 'full' );
			if ( ! file_exists( str_replace( $path, $_SERVER[ 'DOCUMENT_ROOT' ], $varsTpl[ 'avatar_url' ] ) ) ) {
				$varsTpl[ 'avatar_url' ] = get_avatar_url( $user_ID, "default=mystery" );
			}
		} else {
			$varsTpl[ 'avatar_url' ] = get_avatar_url( $user_ID, "default=mystery" );
		}

		$about = ! empty( $profile_post ) && ! empty( strip_tags( $profile_post->post_content ) ) ? strip_tags( $profile_post->post_content ) : '';
		if ( ! empty( $about ) && strlen( $about ) > 300 ) {
			$str   = substr( $about, 0, 303 );
			$key   = strrpos( $str, '.' );
			$about = substr( $str, 0, $key ) . '...';
		}
		$varsTpl[ 'about' ]      = $about;
		$varsTpl[ 'refer_code' ] = get_referral_code_by_user( $user_ID );
		$varsTpl[ 'qr_code' ]    = 'https://api.qrserver.com/v1/create-qr-code/?data=' . $_SERVER[ "HTTP_HOST" ] . '/register/?code=' . $varsTpl[ 'refer_code' ] . '&size=';

		return $varsTpl;
	}

	function get_poster( $name_poster, $action = 'posters', $type = '.html' ) {
		$varsTpl = get_vars_for_poster();
		switch ( $name_poster ) {
			case 'poster1':
			case 'poster3':
				$varsTpl[ 'qr_code' ] .= '60x60';
				break;
			case 'poster2':
				$varsTpl[ 'qr_code' ] .= '55x55';
				break;
		}

		$pathTpl   = __DIR__ . DIRECTORY_SEPARATOR . $action . '/';
		$pathCache = $pathTpl . 'cache';
		if ( ! file_exists( $pathCache ) ) {
			mkdir( $pathCache, 0755, true );
		}
		$fenom = \Fenom::factory( $pathTpl, $pathCache );
		$fenom->setOptions( \Fenom::AUTO_RELOAD );

		$fenom->display( $name_poster . $type, $varsTpl );
	}

	function generate_poster() {
		if ( ! empty( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'generate_poster' && empty( $_POST[ 'template' ] ) ) {
			wp_send_json_error( [ 'msg' => false ] );
		}

		global $user_ID;
		$template = $_POST[ 'template' ];

		$path_inc = $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-content/plugins/' . basename( __DIR__ );

		$defaultConfig = ( new Mpdf\Config\ConfigVariables )->getDefaults();
		$fontDirs      = $defaultConfig[ 'fontDir' ];

		$defaultFontConfig = ( new Mpdf\Config\FontVariables() )->getDefaults();
		$fontData          = $defaultFontConfig[ 'fontdata' ];

		$mpdf = new Mpdf\Mpdf( [
			'fontDir'  => array_merge( $fontDirs, [
				$path_inc . '/template/assets/fonts',
			] ),
			'fontdata' => array_merge( $fontData, [
				'dinprocond'     => [
					'R' => 'DINPro-Cond.ttf',
				],
				'dinprocondbold' => [
					'R' => 'DINPro-CondBold.ttf',
				]
			] ),
			'debug'    => true,
		] );

		ob_clean();
		ob_start();
		get_poster( 'style_' . $template, 'template', '.css' );
		$style = ob_get_clean();

		ob_start();
		get_poster( $template, 'template' );
		$body = ob_get_clean();

		$mpdf->WriteHTML( $style, Mpdf\HTMLParserMode::HEADER_CSS );
		$mpdf->WriteHTML( $body, Mpdf\HTMLParserMode::HTML_BODY );

		// Output a PDF file directly to the browser
		if ( ! file_exists( $path_inc . '/cache' ) ) {
			mkdir( $path_inc . '/cache' );
		}
		$mpdf->Output( $path_inc . '/cache/' . $template . '_' . $user_ID . '.pdf' );
	}

	function delete_cache_poster() {
		if ( ! empty( $_POST ) && ! empty( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'delete_cache_poster' && empty( $_POST[ 'template' ] ) ) {
			exit;
		}

		global $user_ID;
		$template = $_POST[ 'template' ];

		$path_inc = $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-content/plugins/' . basename( __DIR__ );
		if ( ! file_exists( $path_inc . '/cache' ) ) {
			mkdir( $path_inc . '/cache', 0755, true );
		}

		$filename = realpath( $path_inc . '/cache/' . $template . '_' . $user_ID . '.pdf' );
		if ( is_writable( $filename ) ) {
			unlink( $filename );

			return 0;
		} else {
			return 'no write';
		}
	}
