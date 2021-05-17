<?php

class MailsterHelper {

	/**
	 *
	 *
	 * @param unknown $attach_id (optional)
	 * @param unknown $img_url   (optional)
	 * @param unknown $width    (optional)
	 * @param unknown $height   (optional)
	 * @param unknown $crop     (optional)
	 * @param unknown $original (optional)
	 * @return unknown
	 */
	public function create_image( $attach_id = null, $img_url = null, $width = null, $height = null, $crop = false, $original = false ) {

		$org_url = $img_url;

		$image = apply_filters( 'mailster_pre_create_image', null, $attach_id, $img_url, $width, $height, $crop, $original );
		if ( ! is_null( $image ) ) {
			return $image;
		}

		if ( $attach_id && ! is_numeric( $attach_id ) ) {

			$response = $this->unsplash( 'download_url', $attach_id );

			if ( ! is_wp_error( $response ) && isset( $response->url ) ) {
				$img_url = $response->url;
			} else {
				return false;
			}
		} elseif ( $attach_id ) {

			$attach_id = (int) $attach_id;

			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			if ( ! $image_src ) {
				return false;
			}

			$actual_file_path = get_attached_file( $attach_id );

			if ( ! $width && ! $height ) {
				$orig_size = getimagesize( $actual_file_path );
				$width     = $orig_size[0];
				$height    = $orig_size[1];
			}
		}

		if ( $img_url ) {

			$file_path = parse_url( $img_url );

			if ( file_exists( $img_url ) ) {

				$actual_file_path = $img_url;
				$img_url          = str_replace( ABSPATH, site_url( '/' ), $img_url );

			} elseif ( strpos( $img_url, admin_url( 'admin-ajax' ) ) === 0 ) {

				parse_str( $file_path['query'], $query );
				$width  = $query['w'];
				$height = $query['h'];
				$crop   = $query['c'];

				return apply_filters(
					'mailster_create_image',
					array(
						'id'     => $attach_id,
						'url'    => $img_url,
						'width'  => $width,
						'height' => $height,
						'asp'    => $width / $height,
					),
					$attach_id,
					$img_url,
					$width,
					$height,
					$crop
				);

			} else {

				$actual_file_path = realpath( $_SERVER['DOCUMENT_ROOT'] ) . $file_path['path'];

				/* todo: recognize URLs */
				if ( ! file_exists( $actual_file_path ) ) {

					if ( false !== strpos( $img_url, '//images.unsplash.com' ) ) {
						$query = parse_url( $img_url, PHP_URL_QUERY );
						parse_str( $query, $query_args );

						$unsplash_args = apply_filters( 'mailster_create_image_unsplash_args', array(), $attach_id, $img_url, $width, $height, $crop );

						$args = wp_parse_args(
							$unsplash_args,
							array(
								'w'    => $width,
								'h'    => $height,
								'crop' => $crop,
								'fit'  => $crop ? 'crop' : 'max',
								'dpi'  => 1,
								'q'    => apply_filters( 'jpeg_quality', $query_args['q'] ),
							)
						);

						$img_url = add_query_arg( $args, $img_url );

					} else {
						$height = null;
					}
					$asp = null;

					return apply_filters(
						'mailster_create_image',
						array(
							'id'     => $attach_id,
							'url'    => $img_url,
							'width'  => $width,
							'height' => $height,
							'asp'    => $asp,
						),
						$attach_id,
						$img_url,
						$width,
						$height,
						$crop
					);

				}
			}

			if ( ! file_exists( $actual_file_path ) ) {
				$actual_file_path = ltrim( $file_path['path'], '/' );
				$actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
				if ( ! file_exists( $actual_file_path ) ) {
					$actual_file_path = ABSPATH . str_replace( site_url( '/' ), '', $img_url );
				}
			}

			$orig_size = getimagesize( $actual_file_path );

			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];

		}

		if ( ! $height && isset( $image_src[1] ) && $image_src[1] && isset( $image_src[2] ) && $image_src[2] ) {
			$height = round( $width / ( $image_src[1] / $image_src[2] ) );
		}

		$file_info = pathinfo( $actual_file_path );
		$extension = $file_info['extension'];

		$no_ext_path = trailingslashit( $file_info['dirname'] ) . $file_info['filename'];

		if ( $original ) {
			$new_img_size     = array( $image_src[1], $image_src[2] );
			$resized_img_path = $no_ext_path . '.' . $extension;
		} else {
			if ( $crop ) {
				$new_img_size = array( $width, $height );
			} else {
				$new_img_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
			}
			$resized_img_path = $no_ext_path . '-' . $new_img_size[0] . 'x' . $new_img_size[1] . '.' . $extension;
		}

		$new_img = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

		if ( ! file_exists( $resized_img_path ) && file_exists( $actual_file_path ) ) {

			$image = wp_get_image_editor( $actual_file_path );
			if ( ! is_wp_error( $image ) ) {
				$image->resize( $width, $height, $crop );
				$imageobj     = $image->save();
				$new_img_path = ! is_wp_error( $imageobj ) ? $imageobj['path'] : $actual_file_path;
			} else {
				$new_img_path = $actual_file_path;
			}

			$new_img_size = getimagesize( $new_img_path );
			$new_img      = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

			$meta_data = wp_get_attachment_metadata( $attach_id );
			if ( $meta_data && is_array( $meta_data ) ) {
				$size_id                        = '_mailster-' . $width . 'x' . $height . '|' . $crop;
				$meta_data['sizes'][ $size_id ] = array(
					'file'      => basename( $new_img_path ),
					'width'     => $width,
					'height'    => $height,
					'mime-type' => $new_img_size['mime'],
				);
				wp_update_attachment_metadata( $attach_id, $meta_data );
			}
		}

		return apply_filters(
			'mailster_create_image',
			array(
				'id'     => $attach_id,
				'url'    => $new_img,
				'width'  => $new_img_size[0],
				'height' => $new_img_size[1],
				'asp'    => $new_img_size[1] ? $new_img_size[0] / $new_img_size[1] : null,
			),
			$attach_id,
			$img_url,
			$width,
			$height,
			$crop
		);

	}



	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function get_wpuser_meta_fields( $force = false ) {

		global $wpdb;

		$cache_key = 'mailster_wpuser_meta_fields';

		if ( $force || false === ( $meta_values = get_transient( $cache_key ) ) ) {
			$exclude = array( 'comment_shortcuts', 'first_name', 'last_name', 'nickname', 'use_ssl', 'default_password_nag', 'dismissed_wp_pointers', 'rich_editing', 'show_admin_bar_front', 'show_welcome_panel', 'admin_color', 'screen_layout_dashboard', 'screen_layout_newsletter', 'show_try_gutenberg_panel', 'syntax_highlighting', 'locale', 'sites_network_per_page' );

			$meta_values = $wpdb->get_col( "SELECT meta_key FROM {$wpdb->usermeta} WHERE meta_value NOT LIKE '%:{%' GROUP BY meta_key ORDER BY meta_key ASC" );
			$meta_values = preg_grep( '/^(?!' . preg_quote( $wpdb->base_prefix ) . ')/', $meta_values );
			$meta_values = array_diff( $meta_values, $exclude );
			$meta_values = array_values( $meta_values );

			set_transient( $cache_key, $meta_values, DAY_IN_SECONDS );

		}

		return $meta_values;

	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function get_addons( $force = false ) {

		if ( $force || false === ( $addons = get_transient( 'mailster_addons' ) ) ) {

			$url = 'https://static.mailster.co/v1/addons.json';

			$response = wp_remote_get( $url, array() );

			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );

			if ( is_wp_error( $response ) ) {
				set_transient( 'mailster_addons', $response, 360 );
				return $response;
			}

			$addons = json_decode( $response_body );
			set_transient( 'mailster_addons', $addons, DAY_IN_SECONDS );
		}

		return $addons;

	}


	/**
	 *
	 *
	 * @param unknown $plugin
	 * @return unknown
	 */
	public function install_plugin( $plugin ) {

		$plugins     = array_keys( get_plugins() );
		$pluginslugs = preg_replace( '/^(.*)\/.*$/', '$1', $plugins );

		// already installed
		if ( in_array( $plugin, $pluginslugs ) ) {
			return true;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin,
				'fields' => array(
					'short_description' => false,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			wp_die( $api );
		}

		$title        = esc_html__( 'Plugin Install', 'mailster' );
		$parent_file  = 'plugins.php';
		$submenu_file = 'plugin-install.php';

		$title = sprintf( esc_html__( 'Installing Plugin: %s', 'mailster' ), $api->name . ' ' . $api->version );
		$nonce = 'install-plugin_' . $plugin;
		$url   = 'update.php?action=install-plugin&plugin=' . urlencode( $plugin );

		$type = 'web'; // Install plugin type, From Web or an Upload.

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		return $upgrader->install( $api->download_link );

	}


	/**
	 *
	 *
	 * @param unknown $plugin
	 * @return unknown
	 */
	public function activate_plugin( $plugin ) {

		$plugins = array_keys( get_plugins() );

		$plugin = array_values( preg_grep( '/^' . $plugin . '\/.*$/', $plugins ) );
		if ( empty( $plugin ) ) {
			return false;
		}

		$plugin = $plugin[0];

		if ( is_plugin_active( $plugin ) ) {
			return true;
		}

		activate_plugin( $plugin );

		return is_plugin_active( $plugin );

	}


	/**
	 *
	 *
	 * @param unknown $args      (optional)
	 * @param unknown $countonly (optional)
	 * @return unknown
	 */
	public function link_query( $args = array(), $countonly = false ) {

		global $wpdb;

		$pts      = get_post_types( array( 'public' => true ), 'objects' );
		$pt_names = array_keys( $pts );

		$defaults = array(
			'post_type'              => $pt_names,
			'suppress_filters'       => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status'            => 'publish',
			'order'                  => 'DESC',
			'orderby'                => 'post_date',
			'posts_per_page'         => -1,
			'offset'                 => 0,
		);

		$query = wp_parse_args( $args, $defaults );

		if ( isset( $args['s'] ) ) {
			$query['s'] = $args['s'];
		}

		if ( $countonly ) {
			// Do main query with only one result to reduce server load
			$get_posts = new WP_Query(
				wp_parse_args(
					array(
						'posts_per_page' => 1,
						'offset'         => 0,
					),
					$query
				)
			);
			return $wpdb->query( str_ireplace( 'LIMIT 0, 1', '', $get_posts->request ) );
		}

		// Do main query.
		$get_posts = new WP_Query( $query );

		$sql = str_replace( 'posts.ID', 'posts.*', $get_posts->request );

		$posts = $wpdb->get_results( $sql );

		// Build results.
		$results = array();
		foreach ( $posts as $post ) {
			if ( 'post' == $post->post_type ) {
				$info = mysql2date( esc_html__( 'Y/m/d', 'mailster' ), $post->post_date );
			} else {
				$info = $pts[ $post->post_type ]->labels->singular_name;
			}

			$results[] = array(
				'ID'        => $post->ID,
				'title'     => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
				'permalink' => get_permalink( $post->ID ),
				'info'      => $info,
			);
		}

		return $results;
	}


	/**
	 *
	 *
	 * @param unknown $username           (optional)
	 * @param unknown $only_with_username (optional)
	 * @return unknown
	 */
	public function get_social_links( $username = '', $only_with_username = false ) {

		global $wp_rewrite;

		$links = array(
			'amazon'      => 'https://amazon.com',
			'android'     => 'https://android.com',
			'apple'       => 'https://apple.com',
			'appstore'    => 'https://apple.com',
			'behance'     => 'https://www.behance.net/%%USERNAME%%',
			'blogger'     => 'https://%%USERNAME%%.blogspot.com/',
			'delicious'   => 'https://delicious.com/%%USERNAME%%',
			'deviantart'  => 'https://%%USERNAME%%.deviantart.com',
			'digg'        => 'https://digg.com/users/%%USERNAME%%',
			'dribbble'    => 'https://dribbble.com/%%USERNAME%%',
			'drive'       => 'https://drive.google.com',
			'dropbox'     => 'https://dropbox.com',
			'ebay'        => 'https://www.ebay.com',
			'facebook'    => 'https://facebook.com/%%USERNAME%%',
			'flickr'      => 'https://www.flickr.com/photos/%%USERNAME%%',
			'forrst'      => 'https://forrst.me/%%USERNAME%%',
			'google'      => 'https://www.google.com',
			'googleplus'  => 'https://plus.google.com/%%USERNAME%%',
			'html5'       => 'https://html5.com',
			'instagram'   => 'https://instagram.com/%%USERNAME%%',
			'lastfm'      => 'https://www.lastfm.de/user/%%USERNAME%%',
			'linkedin'    => 'https://www.linkedin.com/%%USERNAME%%',
			'myspace'     => 'https://www.myspace.com/%%USERNAME%%',
			'paypal'      => 'https://paypal.com',
			'picasa'      => 'https://picasa.com',
			'pinterest'   => 'https://pinterest.com/%%USERNAME%%',
			'rss'         => $wp_rewrite ? get_bloginfo( 'rss2_url' ) : '',
			'skype'       => 'skype:%%USERNAME%%',
			'soundcloud'  => 'https://soundcloud.com/%%USERNAME%%',
			'stumbleupon' => 'https://stumbleupon.com',
			'technorati'  => 'https://technorati.com',
			'tumblr'      => 'https://%%USERNAME%%.tumblr.com',
			'twitter'     => 'https://twitter.com/%%USERNAME%%',
			'vimeo'       => 'https://vimeo.com/%%USERNAME%%',
			'windows'     => 'https://microsoft.com',
			'windows_8'   => 'https://microsoft.com',
			'wordpress'   => 'https://profiles.wordpress.org/%%USERNAME%%',
			'yahoo'       => 'https://yahoo.com',
			'youtube'     => 'https://youtube.com/%%USERNAME%%',
		);

		$links = apply_filters( 'mailster_get_social_links', $links );

		if ( $only_with_username ) {
			$links = preg_grep( '/%%USERNAME%%/', $links );
		}

		$links = str_replace( '%%USERNAME%%', $username, $links );

		return $links;

	}


	/**
	 *
	 *
	 * @param unknown $service
	 * @param unknown $username           (optional)
	 * @param unknown $only_with_username (optional)
	 * @return unknown
	 */
	public function get_social_link( $service, $username = '', $only_with_username = false ) {

		$links = $this->get_social_links( $username, $only_with_username );

		$link = ( isset( $links[ $service ] ) ) ? $links[ $service ] : '';

		return $link;

	}


	/**
	 *
	 *
	 * @param unknown $utc_start
	 * @param unknown $interval
	 * @param unknown $time_frame
	 * @param unknown $weekdays   (optional)
	 * @param unknown $in_future  (optional)
	 * @return unknown
	 */
	public function get_next_date_in_future( $utc_start, $interval, $time_frame, $weekdays = array(), $in_future = true ) {

		// in local time
		$offset     = $this->gmt_offset( true );
		$now        = time() + $offset;
		$utc_start += $offset;
		$times      = 1;

		// must be in future and starttime in the past
		if ( $in_future && $utc_start - $now < 0 ) {
			// get how many $time_frame are in the time between now and the starttime
			switch ( $time_frame ) {
				case 'year':
					$count = date( 'Y', $now ) - date( 'Y', $utc_start );
					break;
				case 'month':
					$count = ( date( 'Y', $now ) - date( 'Y', $utc_start ) ) * 12 + ( date( 'm', $now ) - date( 'm', $utc_start ) );
					break;
				case 'week':
					$count = floor( ( ( $now - $utc_start ) / 86400 ) / 7 );
					break;
				case 'day':
					$count = floor( ( $now - $utc_start ) / 86400 );
					break;
				case 'hour':
					$count = floor( ( $now - $utc_start ) / 3600 );
					break;
				default:
					$count = $interval;
					break;
			}

			$times = $interval ? ceil( $count / $interval ) : 0;
		}

		$nextdate = strtotime( date( 'Y-m-d H:i:s', $utc_start ) . ' +' . ( $interval * $times ) . " {$time_frame}" );

		// add a single entity if date is still in the past or just now
		if ( $in_future && ( $nextdate - $now < 0 || $nextdate == $utc_start ) ) {
			$nextdate = strtotime( date( 'Y-m-d H:i:s', $utc_start ) . ' +' . ( $interval * $times + $interval ) . " {$time_frame}" );
		}

		if ( ! empty( $weekdays ) && count( $weekdays ) < 7 ) {

			$dayofweek = date( 'w', $nextdate );
			$i         = 0;
			if ( ! $interval ) {
				$interval = 1;
			}

			while ( ! in_array( $dayofweek, $weekdays ) ) {

				// try next $time_frame
				// if week go day by day otherwise infinity loop
				if ( 'week' == $time_frame ) {
					$nextdate = strtotime( '+1 day', $nextdate );
				} else {
					$nextdate = strtotime( "+{$interval} {$time_frame}", $nextdate );
				}
				$dayofweek = date( 'w', $nextdate );

				// force a break to prevent infinity loops
				if ( $i > 500 ) {
					break;
				}

				$i++;
			}
		}

		// return as UTC
		return $nextdate - $offset;

	}


	/**
	 *
	 *
	 * @param unknown $post_type (optional)
	 * @param unknown $labels    (optional)
	 * @param unknown $names     (optional)
	 * @param unknown $values    (optional)
	 * @return unknown
	 */
	public function get_post_term_dropdown( $post_type = 'post', $labels = true, $names = false, $values = array() ) {

		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$html = '';

		$taxwraps = array();

		foreach ( $taxonomies as $id => $taxonomy ) {
			$tax = '<div class="dynamic_embed_options_taxonomy_container">' . ( $labels ? '<label class="dynamic_embed_options_taxonomy_label">' . $taxonomy->labels->name . ': </label>' : '' ) . '<span class="dynamic_embed_options_taxonomy_wrap">';

			$cats = get_categories(
				array(
					'hide_empty' => false,
					'taxonomy'   => $id,
					'type'       => $post_type,
					'orderby'    => 'id',
					'number'     => 999,
				)
			);

			if ( ! isset( $values[ $id ] ) ) {
				$values[ $id ] = array( '-1' );
			}

			$selects = array();

			foreach ( $values[ $id ] as $term ) {
				$select  = '<select class="dynamic_embed_options_taxonomy check-for-posts" ' . ( $names ? 'name="mailster_data[autoresponder][terms][' . $id . '][]"' : '' ) . '>';
				$select .= '<option value="-1">' . sprintf( esc_html__( 'any %s', 'mailster' ), $taxonomy->labels->singular_name ) . '</option>';
				foreach ( $cats as $cat ) {
					$select .= '<option value="' . $cat->term_id . '" ' . selected( $cat->term_id, $term, false ) . '>' . $cat->name . '</option>';
				}
				$select   .= '</select>';
				$selects[] = $select;
			}

			$tax .= implode( ' ' . esc_html__( 'or', 'mailster' ) . ' ', $selects );

			$tax .= '</span><div class="mailster-list-operator"><span class="operator-and">' . esc_html__( 'and', 'mailster' ) . '</span></div></div>';

			$taxwraps[] = $tax;
		}

		$html = ( ! empty( $taxwraps ) )
			? implode(
				( $labels
				? '<label class="dynamic_embed_options_taxonomy_label">&nbsp;</label>'
				: '' ),
				$taxwraps
			)
			: '';

		return $html;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function social_services() {
		include MAILSTER_DIR . 'includes/social_services.php';

		return $mailster_social_services;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function using_permalinks() {
		global $wp_rewrite;
		return apply_filters( 'mailster_using_permalinks', is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_first_form_id() {
		global $wpdb;
		return (int) $wpdb->get_var( "SELECT ID FROM {$wpdb->prefix}mailster_forms ORDER BY ID ASC LIMIT 1" );
	}


	/**
	 *
	 *
	 * @param unknown $attachemnt_id
	 * @param unknown $fieldname
	 * @param unknown $size          (optional)
	 */
	public function notifcation_template_dropdown( $selected, $fieldname, $disabled = false ) {

		$templatefiles = mailster( 'templates' )->get_files( mailster_option( 'default_template' ) );

		if ( isset( $templatefiles['index.html'] ) ) {
			unset( $templatefiles['index.html'] );
		}

		?>
		<select name="<?php echo esc_attr( $fieldname ); ?>" <?php echo $disabled ? 'disabled' : ''; ?>>
				<option value="-1" <?php selected( -1 == $selected ); ?>><?php esc_html_e( 'Plain Text (no template file)', 'mailster' ); ?></option>
		<?php foreach ( $templatefiles as $slug => $filedata ) : ?>
				<option value="<?php echo $slug; ?>"<?php selected( $slug == $selected ); ?>><?php echo esc_attr( $filedata['label'] ); ?> (<?php echo esc_html( $slug ); ?>)</option>
		<?php endforeach; ?>
		</select>
		<?php
	}


	/**
	 *
	 *
	 * @param unknown $attachemnt_id
	 * @param unknown $fieldname
	 * @param unknown $size          (optional)
	 */
	public function media_editor_link( $attachemnt_id, $fieldname, $size = 'thumbnail' ) {

		$image_url = wp_get_attachment_image_src( $attachemnt_id, $size );

		if ( ! function_exists( 'wpview_media_sandbox_styles' ) ) { // since 4.0
			?>
			<?php if ( $image_url ) : ?>
			<img src="<?php echo esc_attr( $image_url[0] ); ?>" width="150">
			<?php endif; ?>
			<label><?php esc_html_e( 'Image ID', 'mailster' ); ?>:
			<input class="small-text" type="text" name="<?php echo esc_attr( $fieldname ); ?>" value="<?php echo esc_attr( $attachemnt_id ); ?>"></label>

			<?php
		} else {
			wp_enqueue_media();
			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'mailster-media-editor-link', MAILSTER_URI . 'assets/js/media-editor-link-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );
			wp_enqueue_style( 'mailster-media-editor-link', MAILSTER_URI . 'assets/css/media-editor-link-style' . $suffix . '.css', array(), MAILSTER_VERSION );

			$classes = array( 'media-editor-link' );

			$image_url = $image_url ? $image_url[0] : '';
			if ( $image_url ) {
				$classes[] = 'media-editor-link-has-image';
			}

			?>

			<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" title="<?php esc_attr_e( 'Change Image', 'mailster' ); ?>" data-title="<?php esc_attr_e( 'Add Image', 'mailster' ); ?>">
				<img class="media-editor-link-img"<?php echo $image_url ? ' src="' . esc_attr( $image_url ) . '"' : ''; ?>>
				<a class="media-editor-link-select button" href="#"><?php esc_html_e( 'Select Image', 'mailster' ); ?></a>
				<a class="media-editor-link-remove" href="#" title="<?php esc_attr_e( 'Remove Image', 'mailster' ); ?>">&#10005;</a>
				<input class="media-editor-link-input" type="hidden" name="<?php echo esc_attr( $fieldname ); ?>" value="<?php echo esc_attr( $attachemnt_id ); ?>">
			</div>

			<?php

		}

	}


	/**
	 *
	 *
	 * @param unknown $in_seconds (optional)
	 * @param unknown $timestamp  (optional)
	 * @return unknown
	 */
	public function gmt_offset( $in_seconds = false, $timestamp = null ) {

		$offset = get_option( 'gmt_offset' );

		if ( $offset == '' ) {
			$tzstring = get_option( 'timezone_string' );
			$current  = date_default_timezone_get();
			date_default_timezone_set( $tzstring );
			$offset = date( 'Z' ) / 3600;
			date_default_timezone_set( $current );
		}

		// check if timestamp has DST
		if ( ! is_null( $timestamp ) ) {
			$l = localtime( $timestamp, true );
			if ( $l['tm_isdst'] ) {
				$offset++;
			}
		}

		return $in_seconds ? $offset * 3600 : (int) $offset;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function dateformat() {

		$format = get_option( 'date_format' );

		return apply_filters( 'mailster_dateformat', $format );

	}

	/**
	 *
	 *
	 * @return unknown
	 */
	public function timeformat() {

		$format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

		return apply_filters( 'mailster_timeformat', $format );

	}


	/**
	 *
	 *
	 * @param unknown $value
	 * @param unknown $format   (optional)
	 * @return unknown
	 */
	public function do_timestamp( $value, $format = null ) {
		if ( is_null( $format ) ) {
			$format = $this->timeformat();
		}
		$timestamp = is_numeric( $value ) ? strtotime( '@' . $value ) : strtotime( '' . $value );
		if ( false !== $timestamp ) {
			$value = date( $format, $timestamp );
		} elseif ( is_numeric( $value ) ) {
			$value = date( $format, $value );
		} else {
			$value = '';
		}

		return $value;
	}

	/**
	 *
	 *
	 * @param unknown $string
	 * @param unknown $last   (optional)
	 * @return unknown
	 */
	public function get_timestamp_by_string( $string, $last = false ) {

		$offset           = $this->gmt_offset();
		$current_timezone = date_default_timezone_get();
		date_default_timezone_set( 'UTC' );

		$day = strtotime( $offset . ' hours' );

		switch ( $string ) {
			case 'day':
				$str = ( $last ? 'yesterday' : 'tomorrow' ) . ' midnight';
				break;
			case 'week':
				$str = $last ? 'last sunday -' . ( 7 - get_option( 'start_of_week', 1 ) ) . ' days' : 'next sunday +' . get_option( 'start_of_week', 1 ) . ' days';
				break;
			case 'month':
				$str = 'midnight first day of ' . ( $last ? 'last' : 'next' ) . ' month';
				break;
		}

		$utcMidnight  = strtotime( $str, $day );
		$zoneMidnight = strtotime( ( $offset * -1 ) . ' hours', $utcMidnight );

		date_default_timezone_set( $current_timezone );
		return $zoneMidnight;

	}


	/**
	 *
	 *
	 * @param unknown $html
	 * @param unknown $body (optional)
	 * @return unknown
	 */
	public function format_html( $html, $body = false ) {

		$doc = new DOMDocument();

		$doc->preserveWhiteSpace = false;
		$i_error                 = libxml_use_internal_errors( true );
		$doc->loadHTML( $html );
		libxml_clear_errors();
		libxml_use_internal_errors( $i_error );

		$doc->formatOutput = true;
		// remove <!DOCTYPE
		$doc->removeChild( $doc->doctype );
		// remove <html><body></body></html>
		if ( ! $body ) {
			$doc->replaceChild( $doc->firstChild->firstChild->firstChild, $doc->firstChild );
		}

		return trim( $doc->saveHTML() );

	}


	/**
	 *
	 *
	 * @param unknown $status
	 * @param unknown $original (optional)
	 * @return unknown
	 */
	public function get_bounce_message( $status, $original = null ) {

		$res = apply_filters( 'mailster_get_bounce_message', null, $status, $original );
		if ( ! is_null( $res ) ) {
			return $res;
		}

		include MAILSTER_DIR . 'classes/libs/bounce/bounce_statuscodes.php';

		if ( is_null( $original ) ) {
			$original = $status;
		}

		if ( isset( $status_code_classes[ $status ] ) ) {
			$message = $status_code_classes[ $status ];
			return '[' . $message['title'] . '] ' . $message['descr'];
		}
		if ( isset( $status_code_subclasses[ $status ] ) ) {
			$message = $status_code_subclasses[ $status ];
			return '[' . $message['title'] . '] ' . $message['descr'];
		}

		if ( $status = substr( $status, 0, strrpos( $status, '.' ) ) ) {
			return $this->get_bounce_message( $status, $original );
		}

		return $original;

	}


	/**
	 *
	 *
	 * @param unknown $status
	 * @param unknown $original (optional)
	 * @return unknown
	 */
	public function get_unsubscribe_message( $status, $original = null ) {

		$res = apply_filters( 'mailster_get_unsubscribe_message', null, $status, $original );
		if ( ! is_null( $res ) ) {
			return $res;
		}

		if ( is_null( $original ) ) {
			$original = $status;
		}

		switch ( $status ) {
			case 'list_unsubscribe':
			case 'list_unsubscribe_list':
				return esc_html__( 'The user clicked on the unsubscribe option in the Mail application', 'mailster' );
			case 'link_unsubscribe':
			case 'link_unsubscribe_list':
				return esc_html__( 'The user clicked on an unsubscribe link in the campaign.', 'mailster' );
			case 'email_unsubscribe':
			case 'email_unsubscribe_list':
				return esc_html__( 'The user canceled the subscription via the website.', 'mailster' );
			case 'spam_complaint':
			case 'spam_complaint_list':
				return esc_html__( 'The user marked this message as Spam in the Mail application.', 'mailster' );
		}

		return $status;

	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @return unknown
	 */
	public function prepare_content( $content ) {

		if ( empty( $content ) ) {
			return false;
		}

		preg_match_all( '#(<img.*?)(width="(\d+)")(.*?>)#', $content, $images );
		foreach ( $images[0] as $i => $image ) {
			$oldstyle  = '';
			$styleattr = '';
			if ( preg_match( '#style="([^"]*)"#', $image, $style ) ) {
				$oldstyle  = $style[1];
				$styleattr = $style[0];
			}
			$imgstr  = str_replace( $styleattr, '', $images[1][ $i ] . 'style="width:' . $images[3][ $i ] . 'px;' . $oldstyle . '" ' . $images[2][ $i ] . $images[4][ $i ] );
			$content = str_replace( $image, $imgstr, $content );
		}

		// custom styles
		$content = $this->add_mailster_styles( $content );

		// handle shortcodes
		$content = $this->handle_shortcodes( $content );

		return apply_filters( 'mailster_prepare_content', $content );

	}

	/**
	 *
	 *
	 * @param unknown $content
	 * @return unknown
	 */
	public function handle_shortcodes( $org_content ) {

		global $shortcode_tags;

		$key = 'handle_shortcodes_' . md5( $org_content );

		if ( ! ( $content = mailster_cache_get( $key ) ) ) {

			if ( ! ( apply_filters( 'mailster_strip_shortcodes', ! mailster_option( 'shortcodes' ) ) ) ) {
				$org_content = do_shortcode( $org_content );
			}
			if ( $shortcodes = apply_filters( 'mailster_strip_shortcode_tags', array_keys( $shortcode_tags ) ) ) {
				$pattern = '/\[(\/)?(' . implode( '|', $shortcodes ) . ')([^\]]*)\]/';

				// remove short codes but keep content
				$content = preg_replace( $pattern, '', $org_content );
			}

			$content = apply_filters( 'mailster_handle_shortcodes', $content );
			mailster_cache_set( $key, $content );

		}

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @return unknown
	 */
	public function inline_style( $content ) {
		return $this->inline_css( $content );
	}


	public function inline_css( $content ) {

		// save comments with conditional stuff
		preg_match_all( '#<!--\s?\[\s?if(.*)?>(.*)?<!\[endif\]-->#sU', $content, $comments );

		$commentid = uniqid();
		foreach ( $comments[0] as $i => $comment ) {
			$content = str_replace( $comment, '<!--Mailster:html_comment_' . $i . '_' . $commentid . '-->', $content );
		}
		// get all style blocks
		if ( preg_match_all( '#<style([^><]*)>(.*?)</style>#is', $content, $originalstyles ) ) {

			$apply_styles = array();

			// strip media queries
			foreach ( $originalstyles[2] as $i => $styleblock ) {
				// skip embeded styles
				if ( false !== strpos( $originalstyles[1][ $i ], 'data-embed' ) ) {
					continue;
				}
				$mediaBlocks = $this->parseMediaBlocks( $styleblock );
				if ( ! empty( $mediaBlocks ) ) {
					$apply_styles[] = trim( str_replace( $mediaBlocks, '', $originalstyles[2][ $i ] ) );
				} else {
					$apply_styles[] = trim( $originalstyles[2][ $i ] );
				}
			}

			if ( $has_data_image = preg_match_all( '/url\(data:image.*\)/', $content, $data_images ) ) {
				foreach ( $data_images[0] as $i => $data_image ) {
					$content = str_replace( $data_image, '/*Mailster:html_data_image_' . $i . '*/', $content );
				}
			}

			require MAILSTER_DIR . 'classes/libs/InlineStyle/autoload.php';

			$i_error = libxml_use_internal_errors( true );
			$htmldoc = new \InlineStyle\InlineStyle( $content );

			$htmldoc->applyStylesheet( $apply_styles );

			$html = $htmldoc->getHTML();
			libxml_clear_errors();
			libxml_use_internal_errors( $i_error );

			// convert urlencode back for links with unallowed characters (only images)
			preg_match_all( "/(src|background)=[\"'](.*)[\"']/Ui", $html, $urls );
			$urls = ! empty( $urls[2] ) ? array_unique( $urls[2] ) : array();
			foreach ( $urls as $url ) {
				$html = str_replace( $url, rawurldecode( $url ), $html );
			}
			$content = $html;

			if ( $has_data_image ) {
				foreach ( $data_images[0] as $i => $data_image ) {
					$content = str_replace( '/*Mailster:html_data_image_' . $i . '*/', $data_image, $content );
				}
			}

			$content = str_replace( array( '%7B', '%7D' ), array( '{', '}' ), $content );

		}

		foreach ( $comments[0] as $i => $comment ) {
			$content = str_replace( '<!--Mailster:html_comment_' . $i . '_' . $commentid . '-->', $comment, $content );
		}

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $css
	 * @return unknown
	 */
	private function parseMediaBlocks( $css ) {

		$mediaBlocks = array();

		$start = 0;
		while ( ( $start = strpos( $css, '@media', $start ) ) !== false ) {
			// stack to manage brackets
			$s = array();

			// get the first opening bracket
			$i = strpos( $css, '{', $start );

			// if $i is false, then there is probably a css syntax error
			if ( $i !== false ) {
				// push bracket onto stack
				array_push( $s, $css[ $i ] );

				// move past first bracket
				$i++;

				while ( ! empty( $s ) ) {
					// if the character is an opening bracket, push it onto the stack, otherwise pop the stack
					if ( $css[ $i ] == '{' ) {

						array_push( $s, '{' );

					} elseif ( $css[ $i ] == '}' ) {

						array_pop( $s );
					}

					$i++;
				}

				// cut the media block out of the css and store
				$mediaBlocks[] = substr( $css, $start, ( $i + 1 ) - $start );

				// set the new $start to the end of the block
				$start = $i;
			}
		}

		return $mediaBlocks;
	}


	public function add_style( $callback, $args, $embed = false ) {

		global $mailster_mystyles;

		if ( is_callable( $callback ) ) {

		} elseif ( is_array( $callback ) ) {
			if ( ! method_exists( $callback[0], $callback[1] ) ) {
				return false;
			}
		} else {
			if ( ! function_exists( $callback ) ) {
				return false;
			}
		}

		$type = $embed ? 'embed' : 'inline';

		if ( ! isset( $mailster_mystyles ) ) {
			$mailster_mystyles = array();
		}
		if ( ! isset( $mailster_mystyles[ $type ] ) ) {
			$mailster_mystyles[ $type ] = array();
		}

		$mailster_mystyles[ $type ][] = call_user_func_array( $callback, $args );

		return true;

	}


	public function get_mailster_styles( $echo = false ) {

		// custom styles
		global $mailster_mystyles;

		if ( ! did_action( 'mailster_add_style' ) ) {
			do_action( 'mailster_add_style' );
		}

		$mailster_styles = '';

		if ( $mailster_mystyles ) {
			foreach ( $mailster_mystyles as $type => $styles ) {
				foreach ( $styles as $style ) {
					$mailster_styles .= '<style type="text/css"' . ( 'embed' == $type ? ' data-embed' : '' ) . '>' . "\n" . $style . "\n" . '</style>' . "\n";
				}
			}
		}

		if ( ! $echo ) {
			return $mailster_styles;
		}
		echo $mailster_styles;
	}

	public function add_mailster_styles( $content ) {
		$content = str_replace( '</head>', $this->get_mailster_styles() . '</head>', $content );
		return $content;
	}


	/**
	 *
	 *
	 * @param unknown $handle
	 */
	public function wp_print_embedded_scripts( $handle ) {

		global $wp_scripts;

		if ( ! $wp_scripts->registered[ $handle ] ) {
			return;
		}

		$path = untrailingslashit( ABSPATH );

		foreach ( $wp_scripts->registered[ $handle ]->deps as $h ) {
			$this->wp_print_embedded_scripts( $h );
		}

		if ( isset( $wp_scripts->registered[ $handle ]->extra['data'] ) ) {
			echo '<script>' . $wp_scripts->registered[ $handle ]->extra['data'] . '</script>';
		}

		ob_start();

		( file_exists( $path . $wp_scripts->registered[ $handle ]->src ) )
			? include $path . $wp_scripts->registered[ $handle ]->src
			: include str_replace( MAILSTER_URI, MAILSTER_DIR, $wp_scripts->registered[ $handle ]->src );
		$output = ob_get_contents();

		ob_end_clean();

		echo "<script id='$handle'>$output</script>";

	}


	/**
	 *
	 *
	 * @param unknown $handle
	 */
	public function wp_print_embedded_styles( $handle ) {

		global $wp_styles;

		if ( ! $wp_styles->registered[ $handle ] ) {
			return;
		}

		$path   = untrailingslashit( ABSPATH );
		$before = '';
		$after  = '';

		foreach ( $wp_styles->registered[ $handle ]->deps as $h ) {
			$this->wp_print_embedded_styles( $h );
		}
		foreach ( $wp_styles->registered[ $handle ]->extra as $type => $styles ) {
			switch ( $type ) {
				case 'before':
					$before .= implode( ' ', $styles );
					break;
				case 'after':
					$after .= implode( ' ', $styles );
					break;
			}
		}

		ob_start();

		( file_exists( $path . $wp_styles->registered[ $handle ]->src ) )
			? include $path . $wp_styles->registered[ $handle ]->src
			: include str_replace( MAILSTER_URI, MAILSTER_DIR, $wp_styles->registered[ $handle ]->src );
		$output = ob_get_contents();

		ob_end_clean();

		preg_match_all( '#url\((\'|")?([^\'"]+)(\'|")?\)#i', $output, $urls );
		$base = trailingslashit( dirname( $wp_styles->registered[ $handle ]->src ) );
		foreach ( $urls[0] as $i => $url ) {
			if ( substr( $urls[2][ $i ], 0, 5 ) == 'data:' ) {
				continue;
			}

			$output = str_replace( 'url(' . $urls[1][ $i ] . $urls[2][ $i ] . $urls[3][ $i ] . ')', 'url(' . $urls[1][ $i ] . $base . $urls[2][ $i ] . $urls[3][ $i ] . ')', $output );
		}

		echo "<style id='$handle' type='text/css'>{$before}{$output}{$after}</style>";

	}


	/**
	 *
	 *
	 * @param unknown $filename
	 * @param unknown $data     (optional)
	 * @param unknown $flags    (optional)
	 * @return unknown
	 */
	public function file_put_contents( $filename, $data = '', $flags = 'w' ) {

		mailster_require_filesystem();

		if ( ! is_dir( dirname( $filename ) ) ) {
			wp_mkdir_p( dirname( $filename ) );
		}

		if ( $file_handle = fopen( $filename, $flags ) ) {
			fwrite( $file_handle, $data );
			fclose( $file_handle );
		}

		return is_file( $filename );

	}


	/**
	 *
	 *
	 * @param unknown $folder         (optional)
	 * @param unknown $prevent_access (optional)
	 * @return unknown
	 */
	public function mkdir( $folder = '', $prevent_access = true ) {

		mailster_require_filesystem();

		if ( 0 === strrpos( $folder, ABSPATH ) ) {
			$path = trailingslashit( $folder );
		} else {
			$path = trailingslashit( trailingslashit( MAILSTER_UPLOAD_DIR ) . $folder );
		}

		if ( ! is_dir( $path ) ) {

			if ( ! wp_mkdir_p( $path ) ) {
				return false;
			}
		}

		if ( $prevent_access ) {
			if ( ! file_exists( $path . 'index.html' ) ) {
				$this->file_put_contents( $path . 'index.html', '<!DOCTYPE html><html><head><title>.</title><meta name="robots" content="noindex,nofollow"></head></html>' );
			}
		}
		return $path;

	}


	/**
	 *
	 *
	 * @param unknown $host
	 * @param unknown $type  (optional)
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function dns_query( $host, $type = 'ANY', $force = true ) {

		$type = strtoupper( $type );

		$key = 'mailster_dns_' . $host;

		if ( $force || false === ( $records = get_transient( $key ) ) ) {

			// request TXT first
			@dns_get_record( $host, DNS_TXT );
			$records = @dns_get_record( $host, DNS_ALL - DNS_PTR );

			set_transient( $key, $records, 90 );

		}

		if ( ! is_array( $records ) ) {
			return false;
		}

		$return = array();

		foreach ( $records as $record ) {
			if ( $type == $record['type'] || $type == 'ANY' ) {
				$return[] = (object) $record;
			}
		}

		return $return;

	}


	public function in_timeframe( $timestamp = null ) {

		if ( is_null( $timestamp ) ) {
			$timestamp = current_time( 'timestamp' );
		}

		$from = mailster_option( 'time_frame_from', 0 );
		$to   = mailster_option( 'time_frame_to', 0 );
		$days = mailster_option( 'time_frame_day' );
		$hour = date( 'G', $timestamp );
		$day  = date( 'w', $timestamp );

		// no weekday at all or current day is not in the list
		if ( empty( $days ) || ! in_array( $day, $days ) ) {
			return false;
		}

		// further check if not 24h
		if ( abs( $from - $to ) ) {

			$t_from = strtotime( $from . ':00' );
			$t_to   = strtotime( $to . ':00' );

			// current hour is smaller as the requested from one => set it to yesterday
			if ( $hour < $from ) {
				$t_from = strtotime( 'yesterday ' . $from . ':00' );

				// to is smaller as from so after midnight => set as tomorrow
			} elseif ( $to < $from ) {
				$t_to = strtotime( 'tomorrow ' . $to . ':00' );
			}

			// check if its in the range
			if ( $t_from > $timestamp || $timestamp > $t_to ) {
				return false;
			}
		}

		return true;

	}
	/**
	 *
	 *
	 * @return unknown
	 */
	public function got_url_rewrite() {

		$got_url_rewrite = true;

		if ( ! function_exists( 'got_url_rewrite' ) ) {
			require_once ABSPATH . 'wp-admin/includes/misc.php';
		}

		if ( function_exists( 'got_url_rewrite' ) ) {
			$got_url_rewrite = got_url_rewrite();
		}

		return $got_url_rewrite;

	}


	/**
	 *
	 *
	 * @param unknown $obj
	 * @return unknown
	 */
	public function object_to_array( $obj ) {
		if ( is_object( $obj ) ) {
			$obj = (array) $obj;
		}

		if ( is_array( $obj ) ) {
			$new = array();
			foreach ( $obj as $key => $val ) {
				$new[ $key ] = $this->object_to_array( $val );
			}
		} else {
			$new = $obj;
		}

		return $new;

	}


	/**
	 *
	 *
	 * @param unknown $public_only (optional)
	 * @param unknown $output      (optional)
	 * @param unknown $exclude     (optional)
	 * @return unknown
	 */
	public function get_post_types( $public_only = true, $output = 'names', $exclude = array( 'attachment', 'newsletter' ) ) {

		$post_types = get_post_types( array( 'public' => $public_only ), $output );

		if ( ! empty( $exclude ) ) {
			$post_types = array_diff_key( $post_types, array_flip( $exclude ) );
		}

		return apply_filters( 'mailster_post_types', $post_types, $output );

	}

	/**
	 *
	 *
	 * @param unknown $public_only (optional)
	 * @param unknown $output      (optional)
	 * @param unknown $exclude     (optional)
	 * @return unknown
	 */
	public function get_dynamic_post_types( $public_only = true, $output = 'names', $exclude = array( 'attachment', 'newsletter' ) ) {

		if ( ! did_action( 'mailster_register_dynamic_post_type' ) ) {
			do_action( 'mailster_register_dynamic_post_type' );
		}

		return apply_filters( 'mailster_dynamic_post_types', $this->get_post_types( $public_only, $output, $exclude ), $output );

	}

	/**
	 *
	 *
	 * @param unknown $url
	 * @param unknown $item           (optional)
	 * @param unknown $cache_duration (optional)
	 * @return unknown
	 */
	public function feed( $url, $item = null, $cache_duration = null ) {

		$feed_id = md5( trim( $url ) );

		if ( ! ( $posts = mailster_cache_get( 'feed_' . $feed_id ) ) ) {
			if ( ! class_exists( 'SimplePie', false ) ) {
				require_once ABSPATH . WPINC . '/class-simplepie.php';
			}

			$feed = new SimplePie();

			if ( is_null( $cache_duration ) ) {
				$cache_duration = apply_filters( 'mailster_feed_cache_duration', 360 );
			}

			if ( ! $cache_duration || false === ( $body = get_transient( 'mailster_feed_' . $feed_id ) ) ) {

				$response = wp_remote_get( $url, array( 'timeout' => 10 ) );
				$code     = wp_remote_retrieve_response_code( $response );

				if ( $code != 200 ) {
					$response = new WP_Error( 'mailster-feed-error', sprintf( esc_html__( 'The server responded with error code %d.', 'mailster' ), $code ) );
				}

				if ( is_wp_error( $response ) ) {
					if ( ! is_admin() ) {
						mailster_notice( sprintf( esc_html__( 'There\'s a problem receiving the feed from `%1$s`: %2$s', 'mailster' ), $url, $response->get_error_message() ), 'error', $cache_duration, $feed_id );
					}
					return $response;
				}

				mailster_remove_notice( $feed_id );

				$body = wp_remote_retrieve_body( $response );

				// remove this as it makes the feed invalid
				$body = str_replace( ' xmlns="com-wordpress:feed-additions:1"', '', $body );

				set_transient( 'mailster_feed_' . $feed_id, $body, $cache_duration );

			}

			$feed->set_raw_data( $body );

			$feed->set_autodiscovery_level( SIMPLEPIE_LOCATOR_ALL );

			$feed->strip_htmltags( apply_filters( 'mailster_feed_strip_htmltags', $feed->strip_htmltags ) );
			$feed->strip_attributes( apply_filters( 'mailster_feed_strip_attributes', $feed->strip_attributes ) );

			$feed->init();
			$feed->set_output_encoding( get_option( 'blog_charset' ) );

			if ( $feed->error() ) {
				set_transient( 'mailster_feed_' . $feed_id, $body, 0 );
				return new WP_Error( 'simplepie-error', $feed->error() );
			}

			if ( is_wp_error( $feed ) ) {
				return $feed;
			}

			$max_items = apply_filters( 'mailster_feed_max_items', 100 );
			$max_items = $feed->get_item_quantity( (int) $max_items );

			if ( $item >= $max_items ) {
				return new WP_Error( 'feed_to_short', sprintf( esc_html__( 'The feed only contains %d items', 'mailster' ), $max_items ) );
			}

			$rss_items = $feed->get_items( 0, $max_items );

			$posts = array();

			$gmt_offset = $this->gmt_offset( true );

			foreach ( $rss_items as $id => $rss_item ) {

				$post_content = $rss_item->get_content();
				$post_excerpt = $rss_item->get_description();

				preg_match_all( '/<img[^>]*src="(.*?(?:\.png|\.jpg|\.gif))"[^>]*>/i', $post_content . $post_excerpt, $images );
				if ( ! empty( $images[0] ) ) {
					$post_image = $images[1][0];
				} else {
					$post_image = false;
				}
				$author    = $rss_item->get_author();
				$category  = $rss_item->get_categories();
				$permalink = $rss_item->get_permalink();
				$category  = wp_list_pluck( (array) $category, 'term' );
				$comments  = $rss_item->get_item_tags( 'http://purl.org/rss/1.0/modules/slash/', 'comments' );
				if ( isset( $comments[0]['data'] ) ) {
					$comment_count = (int) $comments[0]['data'];
				} else {
					$comment_count = 0;
				}

				$gmt_date     = $rss_item->get_gmdate( 'U' );
				$gmt_modified = $rss_item->get_updated_gmdate( 'U' );

				$post = new WP_Post(
					(object) array(
						'post_type'         => 'mailster_rss',
						'post_title'        => $rss_item->get_title(),
						'post_name'         => basename( parse_url( $permalink, PHP_URL_PATH ) ),
						'post_image'        => $post_image,
						'post_author'       => $author ? $author->name : '',
						'post_author_link'  => $author ? $author->link : '',
						'post_author_email' => $author ? $author->email : '',
						'post_permalink'    => $permalink,
						'post_excerpt'      => $post_excerpt,
						'post_content'      => $post_content,
						'post_category'     => $category,
						'post_date'         => date( 'Y-m-d H:i:s', $gmt_date + $gmt_offset ),
						'post_date_gmt'     => date( 'Y-m-d H:i:s', $gmt_date ),
						'post_modified'     => date( 'Y-m-d H:i:s', $gmt_date + $gmt_offset ),
						'post_modified_gmt' => date( 'Y-m-d H:i:s', $gmt_date ),
						'comment_count'     => $comment_count,
					)
				);

				if ( ! $post->post_modified ) {
					$post->post_modified = $post->post_date;
				}
				if ( ! $post->post_modified_gmt ) {
					$post->post_modified_gmt = $post->post_date_gmt;
				}

				$posts[ $id ] = $post;
			}

			mailster_cache_set( 'feed_' . $feed_id, $posts );
		}

		if ( ! is_null( $item ) ) {
			return isset( $posts[ $item ] ) ? $posts[ $item ] : new WP_Error( 'no_item', sprintf( esc_html__( 'Feed item #%d does not exist', 'mailster' ), $item ) );
		}

		return $posts;

	}


	/**
	 *
	 *
	 * @param unknown $timestamp
	 * @param unknown $url
	 * @return unknown
	 */
	public function new_feed_since( $timestamp, $url, $cache_duration = null ) {

		$feed = $this->feed( $url, 0, $cache_duration );

		if ( is_wp_error( $feed ) ) {
			return $feed;
		}
		$last = strtotime( $feed->post_date_gmt );

		if ( is_null( $timestamp ) ) {
			return $last;
		}

		if ( $last > $timestamp ) {
			return $last;
		}

		return false;

	}

	/**
	 *
	 *
	 * @param unknown $timestamp
	 * @param unknown $url
	 * @return unknown
	 */
	public function get_feed_since( $timestamp, $url, $cache_duration = null ) {

		$posts = $this->feed( $url, null, $cache_duration );
		if ( is_wp_error( $posts ) ) {
			return false;
		}

		$return = array();

		foreach ( $posts as $post ) {
			if ( strtotime( $post->post_date_gmt ) > $timestamp ) {
				$return[] = $post;
			}
		}

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $url
	 * @return unknown
	 */
	public function get_meta_tags_from_url( $url, $fields = null, $force = false ) {

		$tags      = null;
		$cache_key = 'mailster_meta_tags_' . md5( $url );

		if ( $force || false === ( $tags = get_transient( $cache_key ) ) ) {
			$response = wp_remote_get( $url, array( 'timeout' => 5 ) );

			$tags = array();

			if ( ! is_wp_error( $response ) ) {
				$body    = wp_remote_retrieve_body( $response );
				$pattern = '~<\s*meta\s(?=[^>]*?\b(?:name|property|http-equiv)\s*=\s*(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=)))[^>]*?\bcontent\s*=\s*(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))[^>]*>~ix';
				if ( preg_match_all( $pattern, $body, $out ) ) {
					$tags = array_combine( $out[1], $out[2] );
				}
			}

			set_transient( $cache_key, $tags, DAY_IN_SECONDS );

		}

		if ( ! is_null( $fields ) ) {
			if ( ! is_array( $fields ) ) {
				$fields = array( $fields );
			}
			foreach ( $fields as $field ) {
				if ( isset( $tags[ $field ] ) ) {
					return $tags[ $field ];
				}
			}

			return false;
		}

		return $tags;

	}


	/**
	 *
	 *
	 * @param unknown $org_string
	 * @param unknown $length     (optional)
	 * @param unknown $more       (optional)
	 * @return unknown
	 */
	public function get_excerpt( $org_string, $length = null, $more = null ) {

		if ( is_null( $length ) ) {
			$length = 55;
		}

		$excerpt = apply_filters( 'mymail_pre_get_excerpt', apply_filters( 'mailster_pre_get_excerpt', null, $org_string, $length, $more ), $org_string, $length, $more );
		if ( is_string( $excerpt ) ) {
			return $excerpt;
		}

		$stripped_string = mailster( 'helper' )->handle_shortcodes( $org_string );

		$string            = str_replace( "\n", '<!--Mailster:newline-->', $stripped_string );
		$string            = html_entity_decode( wp_trim_words( htmlentities( $string ), $length, $more ) );
		$maybe_broken_html = str_replace( '<!--Mailster:newline-->', "\n", $string );

		if ( $maybe_broken_html !== $org_string ) {
			$doc = new DOMDocument();
			// Note the meta charset is used to prevent UTF-8 data from being interpreted as Latin1, thus corrupting it
			$html  = '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"></head><body>';
			$html .= $maybe_broken_html;
			$html .= '</body></html>';

			$i_error = libxml_use_internal_errors( true );
			$doc->loadHTML( $html );
			libxml_clear_errors();
			libxml_use_internal_errors( $i_error );

			$body = $doc->getElementsByTagName( 'body' )->item( 0 );

			$excerpt = $doc->saveHTML( $body );
		} else {
			$excerpt = $stripped_string;
		}

		$excerpt = trim( strip_tags( $excerpt, '<p><br><a><strong><em><i><b><ul><ol><li><span>' ) );

		return apply_filters( 'mymail_get_excerpt', apply_filters( 'mailster_get_excerpt', $excerpt, $org_string, $length, $more ), $org_string, $length, $more );

	}



	/**
	 *
	 *
	 * @param unknown $html
	 * @param unknown $linksonly (optional)
	 * @return unknown
	 */
	public function plain_text( $html, $linksonly = false ) {

		// allow to hook into this method
		$result = apply_filters( 'mymail_plain_text', apply_filters( 'mailster_plain_text', null, $html, $linksonly ), $html, $linksonly );
		if ( ! is_null( $result ) ) {
			return $result;
		}

		if ( $linksonly ) {
			$links = '/< *a[^>]*href *= *"([^#]*)"[^>]*>(.*)< *\/ *a *>/Uis';
			$text  = preg_replace( $links, '${2} [${1}]', $html );
			$text  = str_replace( array( ' ', '&nbsp;' ), ' ', strip_tags( $text ) );
			$text  = @html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );

			return trim( $text );

		} else {
			require_once MAILSTER_DIR . 'classes/libs/class.html2text.php';
			$htmlconverter = new \MailsterHtml2Text\Html2Text(
				$html,
				array(
					'width'    => 200,
					'do_links' => 'table',
				)
			);

			$text = trim( $htmlconverter->get_text() );
			$text = preg_replace( '/\s*$^\s*/mu', "\r\n", $text );
			$text = preg_replace( '/[ \t]+/u', ' ', $text );

			return $text;

		}

	}


	public function strip_structure_html( $content ) {

		if ( ! empty( $content ) ) {
			// template language stuff
			$content = preg_replace( '#<(modules?|buttons|multi|single)([^>]*)>#', '', $content );
			$content = preg_replace( '#<\/(modules?|buttons|multi|single)>#', '', $content );

			// remove comments
			$content = preg_replace( '#<!-- (.*) -->\s*#', '', $content );
		}

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $serialized_string
	 * @return unknown
	 */
	public function unserialize( $serialized_string ) {

		$object = maybe_unserialize( $serialized_string );
		if ( empty( $object ) ) {
			$d = html_entity_decode( $serialized_string, ENT_QUOTES, 'UTF-8' );

			$d = preg_replace_callback(
				'!s:(\d+):"(.*?)";!',
				function( $matches ) {
					return 's:' . strlen( $matches[2] ) . ':"' . $matches[2] . '";';
				},
				$d
			);

			$object = maybe_unserialize( $d );
		}

		return $object;

	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @param unknown $args    (optional)
	 * @return unknown
	 */
	public function dialog( $content, $args = array() ) {

		if ( $is_file = is_file( MAILSTER_DIR . 'views/dialogs/' . basename( $content ) . '.php' ) ) {
			$content = MAILSTER_DIR . 'views/dialogs/' . basename( $content ) . '.php';
		}

		$defaults = array(
			'id'           => uniqid(),
			'button_label' => esc_html__( 'Ok, got it!', 'mailster' ),
			'classes'      => array(),
		);

		if ( is_string( $args ) ) {
			$args = array( 'id' => $args );
		}

		$args       = wp_parse_args( $args, $defaults );
		$args['id'] = 'mailster-' . $args['id'];

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'mailster-dialog', MAILSTER_URI . 'assets/js/dialog-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );
		wp_enqueue_style( 'mailster-dialog', MAILSTER_URI . 'assets/css/dialog-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		?>
		<div id="<?php echo esc_attr( $args['id'] ); ?>" class="mailster-notification-dialog notification-dialog-wrap <?php echo esc_attr( $args['id'] ); ?> hidden <?php echo implode( ' ', $args['classes'] ); ?>">
			<div class="notification-dialog-background"></div>
			<div class="notification-dialog" role="dialog" aria-labelledby="<?php echo esc_attr( $args['id'] ); ?>" tabindex="0">
				<div class="notification-dialog-content <?php echo esc_attr( $args['id'] ); ?>-content">
					<?php if ( $is_file ) : ?>
						<?php include $content; ?>
					<?php else : ?>
						<?php echo $content; ?>
					<?php endif; ?>
				</div>
				<div class="notification-dialog-footer">
					<?php foreach ( $args['buttons'] as $button ) : ?>
						<?php
						$button = wp_parse_args(
							$button,
							array(
								'href'    => '#',
								'classes' => '',
								'label'   => 'Submit',
							)
						);
						?>
						<a class="<?php echo esc_attr( implode( ' ', (array) $button['classes'] ) ); ?>" href="<?php echo esc_attr( $button['href'] ); ?>"><?php echo esc_html( $button['label'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}


	public function unsplash( $command, $args = array() ) {

		$endpoint = 'https://api.unsplash.com/';

		$key = sanitize_key( apply_filters( 'mailster_unsplash_client_id', 'ba3e2af91c8c44d00cb70fe6217dcf021f7350633c323876ffa561a1dfbfc25f' ) );

		switch ( $command ) {
			case 'search':
				$path     = 'search/photos';
				$defaults = array( 'per_page' => 30 );
				$args     = wp_parse_args( $args, $defaults );
				if ( empty( $args['query'] ) ) {
					unset( $args['query'] );
					$path = 'photos';
				} else {
					$args['query'] = urlencode( $args['query'] );
				}
				if ( isset( $args['offset'] ) ) {
					$args['page'] = floor( $args['offset'] / $args['per_page'] ) + 1;
					unset( $args['offset'] );
				}
				break;
			case 'download_url':
				$path = 'photos/' . $args . '/download';
				$args = array();
				break;
			default:
				return new WP_Error( 'err', esc_html__( 'Command not supported', 'mailster' ) );
				break;
		}

		$headers = array( 'Authorization' => 'Client-ID ' . $key );

		$url = add_query_arg( $args, $endpoint . $path );

		$cache_key = 'mailster_unsplash_' . md5( $url );

		if ( false === ( $body = get_transient( $cache_key ) ) ) {
			$response = wp_remote_get( $url, array( 'headers' => $headers ) );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$code = wp_remote_retrieve_response_code( $response );
			if ( $code != 200 ) {
				return new WP_Error( $code, $body );
			}

			$body = wp_remote_retrieve_body( $response );

			set_transient( $cache_key, $body, HOUR_IN_SECONDS * 6 );
		}

		return json_decode( $body );

	}


}
