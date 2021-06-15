<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;
	extract( $args );
	$href = $args[ 'url' ] ?? 'javascript:void();';
	$text = $args[ 'text' ] ?? __( 'MORE DETAILS', WPP_TEXT_DOMAIN );

	printf( '<div class="fre-blog-item_more"><a href="%s" title="">%s</a></div>', $href, $text );