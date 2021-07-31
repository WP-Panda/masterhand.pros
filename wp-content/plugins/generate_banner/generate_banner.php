<?php
	/**
	 * The plugin bootstrap file
	 *
	 * @since             1.0.0
	 * @package           Generate_Banner
	 *
	 * @wordpress-plugin
	 * Plugin Name:       Generate banner
	 * Description:
	 * Version:           1.0.0
	 * Author:            web13
	 */

	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */

	//define('PLUGIN_NAME_VERSION', '1.0.0');

	add_action( 'plugins_loaded', 'generate_banner_load', 0 );
	function generate_banner_load() {
		require_once TEMPLATEPATH . '/vendor/autoload.php';
		add_action( 'wp_ajax_save_banner', 'save_banner' );

		add_filter( 'template_include', 'page_banner_template_include', 1 );
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

	;

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