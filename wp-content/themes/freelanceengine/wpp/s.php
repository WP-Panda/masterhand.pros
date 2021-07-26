<?php

function wpp_upd_post_data_for_map( $post_id ) {
	$vals = [];
	$n    = 1;
	/**
	 * Массив с автонабором имен полей
	 */
	while ( $n <= 10 ) {
		$val_t = get_option( 'top' . $n );
		$val_m = get_option( 'news' . $n );

		if ( ! empty( $val_m ) ) {
			$vals[] = (int) $val_m;
		}

		if ( ! empty( $val_t ) ) {
			$vals[] = (int) $val_t;
		}

		$n ++;
	}

	if ( in_array( (int) $post_id, $vals ) ) {
		wp_update_post( wp_slash( [ 'ID' => get_option( 'page_on_front' ) ] ) );
	}


}

add_action( 'save_post', 'wpp_upd_post_data_for_map' );
add_action( 'wp_delete_post', 'wpp_upd_post_data_for_map' );
add_action( 'wp_publish_post', 'wpp_upd_post_data_for_map' );

function wpp_updated_mode_date() {

	$keys = [];
	$n    = 1;

	/**
	 * Массив с автонабором имен полей
	 */
	while ( $n <= 10 ) {
		$keys[] = 'top' . $n;
		$keys[] = 'news' . $n;
		$n ++;
	}

	/**
	 * Тут ID домашней страницы,
	 * если она задана не через настройки,
	 * то можно просто прописать ее ID
	 */
	$home_page_ID = get_option( 'page_on_front' );

	$customizer_data = json_decode( wp_unslash( $_REQUEST['customized'] ), true );


	foreach ( $customizer_data as $data_key => $data_val ) {

		if ( in_array( $data_key, $keys ) ) {

			wp_update_post( wp_slash( [ 'ID' => $home_page_ID ] ) );

			break;
		}

	}

}

add_action( 'customize_save', 'wpp_updated_mode_date' );


add_action( 'save_post', 'sitemaps' );
add_action( 'wp_delete_post', 'sitemaps' );
add_action( 'wp_publish_post', 'sitemaps' );


function sitemaps() {

	global $wpdb;

	$home_page_ID = get_option( 'page_on_front' );
	$out          = '';

	$wrap = <<<WRAP
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	%s
</urlset>
WRAP;


	$item = <<<ITEM
<url>
	<loc>%s</loc>
	<lastmod>%s</lastmod>
	<priority>%s</priority>
</url>
ITEM;


	//Главная
	$out .= sprintf( $item, get_home_url(), get_the_modified_date( 'Y-m-d H:i:s', $home_page_ID ), '1.0' );

	//Cтраницы
	$pages = get_pages( [
		'post_type'   => 'page',
		'post_status' => 'publish',
	] );

	foreach ( $pages as $post ) {
		setup_postdata( $post );

		if ( (int) $home_page_ID === (int) $post->ID ) {
			continue;
		}
		$out .= sprintf( $item, urldecode( get_the_permalink( $post->ID ) ), get_the_modified_date( 'Y-m-d H:i:s', $post->ID ), '0.9' );
	}
	wp_reset_postdata();

	//Категории
	$all_categories = get_categories( [ 'hide_empty' => 0 ] );

	foreach ( $all_categories as $single_cat ) {
		$out .= sprintf( $item, urldecode( get_category_link( $single_cat->term_id ) ), get_metadata( 'term', $single_cat->term_id, 'txseo_seo_modified_date', 1 ), '0.9' );
	}


	//Посты
	$_posts = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_type`='post' AND `post_status`='publish'" );
	foreach ( $_posts as $key => $row ) {
		$post_id    = $row->ID;
		$post_title = $row->post_name;
		$bread      = '';
		$sep        = '/';
		$terms      = get_the_terms( $post_id, 'category' );
		if ( ! empty( $terms ) ) {
			$bread .= urldecode( $terms[0]->slug );
			$bread .= '' . $sep . '';
			$bread .= $post_title;
		}

		if ( ! empty( $bread ) ) {
			$bread = strtr( $bread, " ", "_" );
			$bread = mb_strtolower( $bread );
			$out   .= sprintf( $item, home_url( '/' . $bread ), $row->post_modified_gmt, '0.8' );
		}
	}

	$map = sprintf( $wrap, $out );

	//Пишем в файл sitemaps.xml
	file_put_contents( '../sitemap.xml', $map );

	$out = 'User-agent: *
Disallow: /a*
Disallow: /b*
Disallow: /c*
Disallow: /d*
Disallow: /e*
Disallow: /f*
Disallow: /g*
Disallow: /h*
Disallow: /i*
Disallow: /j*
Disallow: /k*
Disallow: /l*
Disallow: /m*
Disallow: /n*
Disallow: /o*
Disallow: /p*
Disallow: /q*
Disallow: /r*
Disallow: /s*
Disallow: /t*
Disallow: /u*
Disallow: /v*
Disallow: /w*
Disallow: /x*
Disallow: /y*
Disallow: /z*
Disallow: /?*
Disallow: /.*
Disallow: //*
Disallow: /-*
Disallow: /[*
Disallow: /]*
Disallow: /_*
Disallow: /=*
Disallow: /&*
Disallow: /%*
Disallow: /;*
Disallow: /:*
Disallow: /#*
Disallow: /@*
Disallow: /~*
Disallow: /0*
Disallow: /1*
Disallow: /2*
Disallow: /3*
Disallow: /4*
Disallow: /5*
Disallow: /6*
Disallow: /7*
Disallow: /8*
Disallow: /9*
Allow: /robots.txt$
Allow: /sitemap.xml$
Allow: /$';
	$out .= "\r\n";

	//Категории
	$all_categories = get_categories( 'hide_empty=0' );

	foreach ( $all_categories as $single_cat ) {

		$cat_slug = $single_cat->slug;
		$out      .= "Allow: /" . $cat_slug . "/$\r\n";
	}

	//Посты
	foreach ( $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'posts where (post_type = "page" or post_type = "post") and post_status = "publish"' ) as $key => $row ) {
		$post_id    = $row->ID;
		$post_title = $row->post_name;
		$bread      = '';
		$sep        = '/';
		$terms      = get_the_terms( $post_id, 'category' );
		if ( is_array( $terms ) && $terms !== [] ) {
			$bread .= ( $terms[0]->slug );
			$bread .= '' . $sep . '';
			$bread .= urlencode( $post_title );
		}
		if ( $bread != '' ) {
			$bread = strtr( $bread, " ", "_" );
			$bread = mb_strtolower( $bread );
			$out   .= "Allow: /" . $bread . "/$\r\n";
		}
	}

	//Пишем в файл robots.txt
	file_put_contents( '../robots.txt', $out );
}