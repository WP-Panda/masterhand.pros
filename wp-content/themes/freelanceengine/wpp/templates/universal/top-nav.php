<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$args = [
	'theme_location'  => 'et_header_standard',
	'menu'            => '',
	'container'       => '',
	'container_class' => '',
	'container_id'    => '',
	'menu_class'      => 'dropdown-menu',
	'menu_id'         => '',
	'echo'            => true,
	'before'          => '',
	'after'           => '',
	'link_before'     => '',
	'link_after'      => '',
	'items_wrap'      => '%3$s'
];

$preff = ! is_user_logged_in() ? 'not-logged' : ( wpp_fre_is_freelancer() ? 'freelancer' : 'proff' );
?>
<ul class="fre-menu-main">
	<?php
	wpp_get_template_part( 'wpp/templates/profile/nav-header-' . $preff );

	if ( has_nav_menu( 'et_header_standard' ) ) {
		wp_nav_menu( $args );
	}
	?>
</ul>