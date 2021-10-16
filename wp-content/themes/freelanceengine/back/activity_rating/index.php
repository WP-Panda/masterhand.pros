<?php
/*
Plugin Name: Activity Rating
Description:
Version: 1.1
Last Update: 05.03.2020
Author:
Author URI:
*/
//ini_set( 'display_errors', 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ACTIVITY_RATING_DIR', __DIR__ . '/' );

define( 'ACTIVITY_RATING_RELATIVE', '/wp-content/themes/freelanceengine/wpp/modules/' . basename( __DIR__ ) );
add_action( 'init', 'activity_rating_load', 0 );
add_action( 'admin_menu', 'add_menu_activity_rating', 1 );

function activity_rating_load() {
	require_once get_template_directory() . '/wpp/vendor/autoload.php';
	require_once 'classes/AutoloadActivityRating.php';
	AutoloadActivityRating::init();

	ActivityRating\Rating::initActions();
}


function add_menu_activity_rating() {
	add_menu_page(
		__( 'Activity Rating', WPP_TEXT_DOMAIN ),
		__( 'Activity Rating', WPP_TEXT_DOMAIN ),
		'administrator',
		'activity_rating',
		'activity_rating_page',
		null
	);

	//createTbActivityRating();
}

function createTbActivityRating() {
	if ( ! \ActivityRating\Module::getInstance()->tbIsExists() ) {
		\ActivityRating\Module::getInstance()->installTb();
	}
	\ActivityRating\Module::getInstance()->updateTb();
}

function activity_rating_page() {
	require_once ACTIVITY_RATING_DIR . 'module.php';
}

function getActivityRatingUser( $userId = 0 ) {
	if ( userHaveProStatus( $userId ) ) {
		return ActivityRating\Rating::getInstance()->getPro( $userId );
	} else {
		return (int) ActivityRating\Rating::getInstance()->getTotal( $userId );
	}
}

function getActivityProRatingUser( $userId = 0 ) {
	if ( userHaveProStatus( $userId ) ) {
		return ActivityRating\Rating::getInstance()->getPro( $userId, 0 );
	} else {
		return 0;
	}
}

function getActivityDetailUser( $userId = 0 ) {
	$role         = userRole( $userId );
	$listActivity = ActivityRating\Rating::getListActivity( $role );
	$activities   = ActivityRating\Rating::getInstance()->getDetail( $userId );
	$lang         = ActivityRating\Rating::getInstance()->getLang( 'ALL' );

	foreach ( $listActivity as $item ) {

		if ( ! empty( $activities[ $item ] ) ) {
			?>
            <li>
				<?php echo $lang[ $item ]; ?><span>+<?php echo $activities[ $item ]->value; ?></span></li>
            <li>
			<?
		} else {
			?>
            <li>
				<?php echo $lang[ $item ]; ?><span>+0</span></li>
            <li>
			<?
		}
	}
}

function ajaxActivityRatingHistory() {

}

