<?php

	/**
	 * The plugin bootstrap file
	 *
	 * @since             1.0.0
	 * @package           Generate_Poster
	 *
	 * @wordpress-plugin
	 * Plugin Name:       Generate poster
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

	add_action( 'plugins_loaded', 'generate_poster_load', 0 );
	function generate_poster_load() {
		require_once ABSPATH . 'vendor/autoload.php';
		add_action( 'wp_ajax_generate_poster', 'generate_poster' );
		add_action( 'wp_ajax_delete_cache_poster', 'delete_cache_poster' );
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

		$skills = EndorseSkill\Endorse::getInstance()->forUser( $user_ID, 0 );
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

		require_once ABSPATH . '/vendor/autoload.php';

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