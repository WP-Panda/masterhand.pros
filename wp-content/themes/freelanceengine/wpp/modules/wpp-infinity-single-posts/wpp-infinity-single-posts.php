<?php

// Молчание - золото
defined( 'ABSPATH' ) || exit;

// Префикс
define( 'WPP_PREF', 'WPP_' );
// Версия
// Дирректория
define( 'WPP_INF_DIR', plugin_dir_path( __FILE__ ) );
// Урл
define( 'WPP_INF_URL', plugin_dir_url( __FILE__ ) );

require_once 'for-template.php';

/**
 * Ресурсы
 */
function wpp_assets() {

	$screen = apply_filters( 'wpp_is_a_single', is_single() );
	if ( ! $screen ) {
		return;
	}

	$preff = defined( 'WP_DEBUG' ) && true === WP_DEBUG ? '' : '.min';

	wp_enqueue_style( WPP_PREF . 'scroll', WPP_INF_URL . "assets/css/wpp-infinity-single-posts{$preff}.css", [], 'WPP_INF_VERSION', 'all' );


	wp_enqueue_script( WPP_PREF . 'scroll', WPP_INF_URL . "assets/js/wpp-infinity-single-posts{$preff}.js", [
		'jquery',
		'bkm-archive-js'
	], 'WPP_INF_VERSION', true );

	$args = [
		'ajax_url'  => admin_url( 'admin-ajax.php' ),
		'container' => '.content',
		'offset'    => 1200,
		'delay'     => 400,
	];

	wp_localize_script( WPP_PREF . 'scroll', 'WppAjax', $args );

}

add_action( 'wp_enqueue_scripts', 'wpp_assets' );


/**
 * Служебный метатег для записей
 */
function wpp_post_meta() {

	$screen = apply_filters( 'wpp_is_a_single', is_single() );

	if ( ! $screen ) {
		return;
	}

	$object_ID = get_queried_object_ID();


	$cats = wp_get_post_terms( $object_ID, 'category', [ 'fields' => 'ids' ] );


	if ( ! $cats ) {
		return;
	}

	$posts = get_posts(
		[
			'exclude'   => [ $object_ID ],
			'nopaging'  => true,
			'tax_query' => [

				[
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => $cats,
				]
			]
		]
	);

	if ( ! $posts ) {
		return;
	}

	$need = wp_list_pluck( $posts, 'ID' );

	printf( '<meta name="wpp-posts-need" content="%s">', implode( ',', $need ) );
}

add_action( 'wp_head', 'wpp_post_meta' );


function wpp_infinity_loading() {

	if ( ! $_POST['posts'] ) {
		wp_send_json_error( [ 'msg' => 'Записей для загрузки - нет!' ] );
	}

	//получает список записей в виде строки, переводит в массив, получает первую нужную, остальое отправляет обратно
	$posts      = explode( ',', $_POST['posts'] );
	$loading    = array_shift( $posts );
	$posts_more = implode( ',', $posts );

	$post = get_post( $loading );

	ob_start();
	$cats = wp_get_post_terms( $post->ID, 'category', [ 'fields' => 'ids' ] );


	$template = (int) $cats[0] === 25 ? 25 : 24;

	wpp_get_template_part( 'templates/cat-' . $template, [ 'post' => $post ] );
	$content = ob_get_clean();

	//отправка рпезультатак
	wp_send_json_success( [
		'post'      => $content,
		'posts'     => $posts_more,
		'permalink' => get_the_permalink( $post->ID )
	] );
}

add_action( 'wp_ajax_wpp_infinity_loading', 'wpp_infinity_loading' );
add_action( 'wp_ajax_nopriv_wpp_infinity_loading', 'wpp_infinity_loading' );