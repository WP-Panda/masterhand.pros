<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * function wpp_author_open_graph() {
 *
 * if ( is_archive( 'author' ) && ! empty( $_GET['b'] ) ) {
 * $url = get_home_url();
 * $num = $_GET['b'];
 * $name = get_query_var( 'author_name' );
 * $author = get_user_by( 'slug', $name );
 * wpp_d_log("{$url}/media/{$author->ID}/banners/banner{$num}.png" );
 *
 *
 * } ?>
 *
 * <!--<meta property="og:url"           content="<?php echo "{$url}/user/{$name}" ?>" />
 * <meta property="og:type"          content="website" />
 * <meta property="og:title"         content="<?php echo $author->display_name; ?>" />
 * <meta property="og:description"   content="Your description" />
 * <meta property="og:image"         content="<?php echo "{$url}/media/{$author->ID}/banners/banner{$num}.png"; ?>"/>
 * <meta property="og:wpp"         content="<?php echo "{$url}/media/{$author->ID}/banners/banner{$num}.png"; ?>"/>
 *
 * <?php
 * }
 *
 * add_action('wp_head','wpp_author_open_graph');
 */
add_filter( 'wpseo_opengraph_image', 'change_opengraph_image_url' );


function change_opengraph_image_url( $url ) {

	if ( is_archive( 'author' ) && ! empty( $_GET['b'] ) ) {
		$url    = get_home_url();
		$num    = $_GET['b'];
		$name   = get_query_var( 'author_name' );
		$author = get_user_by( 'slug', $name );

		return "{$url}/media/{$author->ID}/banners/banner{$num}.png";

	} else {
		return $url;
	}
}


function change_opengraph_page_url( $url ) {
	if ( is_archive( 'author' ) && ! empty( $_GET['b'] ) ) {
		$url    = get_home_url();
		$num    = $_GET['b'];
		$name   = get_query_var( 'author_name' );
		$author = get_user_by( 'slug', $name );

		return "{$url}/user/{$name}?b={$num}";

	} else {
		return $url;
	}
}

add_filter( 'wpseo_opengraph_url', 'change_opengraph_page_url' );