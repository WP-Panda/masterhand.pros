<?php
	/*
	Plugin Name: Likes of Users
	Description:
	Version: 1.0.panda
	Last Update: 22.10.2019
	Author:
	Author URI:
	*/
	ini_set( 'display_errors', 1 );

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	define( 'LIKES_USERS_DIR', __DIR__ . '/' );
	define( 'LIKES_USERS_RELATIVE', '/wp-content/plugins/' . basename( __DIR__ ) );
	add_action( 'plugins_loaded', 'likes_users_load', 0 );
	add_action( 'admin_menu', 'add_menu_likes_users', 1 );

	register_activation_hook( __FILE__, 'activateLikesUsers' );
	//add_action('activate_' . basename(__DIR__) . '/index.php', 'activateEndorseSkill', 1);


	function likes_users_load() {
		require_once ABSPATH . 'vendor/autoload.php';
		require_once __DIR__ . '/classes/AutoloadLikesUsers.php';

		add_action( 'wp_ajax_handLike', 'ajaxHandLikeUser' );
		add_action( 'wp_ajax_nopriv_handLike', 'ajaxHandLikeUser' );
		add_action( 'wp_ajax_nopriv_handLike', 'ajaxHandLikeUser' );
		add_action( 'wp_enqueue_scripts', 'assets' );

	}

	function assets() {
		if ( ! is_admin() ) {
			wp_register_script( 'likesUsers', '/wp-content/plugins/likes_users/js/likes_users.js', [], '1.0', true );
			wp_enqueue_style( 'likesUsers', '/wp-content/plugins/likes_users/css/likes_users.css' );
		}
	}

	function add_menu_likes_users() {
		add_menu_page( __( 'Likes of Users', 'likes_users' ), __( 'Likes of Users', 'likes_users' ), 'administrator', 'likes_users', 'likes_users_page', null );
	}

	function likes_users_page() {
		require_once LIKES_USERS_DIR . 'module.php';
	}

	function activateLikesUsers() {
		require_once __DIR__ . '/classes/AutoloadLikesUsers.php';
		AutoloadLikesUsers::init();
		if ( ! \LikesUsers\Module::getInstance()->tbIsExists() ) {
			\LikesUsers\Module::getInstance()->installTb();
		} else {
			\LikesUsers\Module::getInstance()->uninstallTb();
			\LikesUsers\Module::getInstance()->installTb();
		}
	}

	function getLikesPost( $id = 0 ) {
		return (int) get_post_meta( $id, 'likes_users', 1 );
	}

	function getLikesComments( $id = 0 ) {
		return (int) get_comment_meta( $id, 'likes_users', 1 );
	}

	function ajaxHandLikeUser() {
		global $user_ID;

		$userLike = \LikesUsers\Like::getInstance();
		$result   = $userLike->handler( intval( $user_ID ), intval( $_POST[ 'id' ] ), trim( $_POST[ 'type' ] ) );

		if ( $result !== false ) {
			\LikesUsers\Base::outputJSON( $result, 1 );
		}

		\LikesUsers\Base::outputJSON( $userLike->getError() );
	}

	function likesPost( $id ) {
		global $user_ID;

		$count   = getLikesPost( $id );
		$isLiked = false;
		if ( $user_ID ) {
			$isLiked = LikesUsers\Like::getInstance()->findForPost( $user_ID, $id );
		}
		$cssLiked = $isLiked ? ' is-liked' : 'add-like-post';
		?>
        <div class="wrap-likes-users post">
            <div class="likes-users <?= $cssLiked ?>" data-id="<?= $id ?>" title="Like">
				<?= $count ?>
            </div>
        </div>
		<?
	}

	function likesComment( $id ) {
		global $user_ID;

		$count   = getLikesComments( $id );
		$isLiked = false;
		if ( $user_ID ) {
			$isLiked = LikesUsers\Like::getInstance()->findForComment( $user_ID, $id );
		}
		$cssLiked = $isLiked ? ' is-liked' : 'add-like-comment';
		?>
        <div class="wrap-likes-users comment">
            <div class="likes-users <?= $cssLiked ?>" data-id="<?= $id ?>" title="Like">
				<?= $count ?>
            </div>
        </div>
		<?
	}