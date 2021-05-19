<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */
global $current_user;
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<?php global $user_ID; ?>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--old favicon commented<?php ae_favicon(); ?>-->
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.masterhand.pro/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.masterhand.pro/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.masterhand.pro/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="https://www.masterhand.pro/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="https://www.masterhand.pro/favicon.png">
    <link rel="manifest" href="https://www.masterhand.pro/site.webmanifest">
	<?php

		wp_head();
		if ( function_exists( 'et_render_less_style' ) ) {
			//et_render_less_style();
		}

	?>
</head>

<body <?php body_class(); ?>>

<?php global $post;

	$author_id = get_query_var( 'author' );
	$views_id  = $author_id;//intval($post->ID);

	if ( is_author() ) {
		echo '<script >' . "\n";

		echo '/* <![CDATA[ */' . "\n";
		echo "jQuery.ajax({type:'GET',url:'/view.php',data:'views_id=" . $views_id . "',cache:false});" . "\n";

		echo '/* ]]> */' . "\n";
		echo '</script>' . "\n";
	} ?>

<header class="fre-header-wrapper">
    <div class="fre-header-wrap" id="main_header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-3 fre-site-logo">
                    <a class="col-lg-12 col-md-3 col-sm-4 col-xs-7 logo-head" href="/"><?php fre_logo( 'site_logo' ) ?>
                        <span>Low-Cost Service Deals</span></a>
                    <div class="col-xs-5 col-sm-8 col-md-9 hidden-lg fre-hamburger dropdown">
						<?php if ( is_user_logged_in() ) { ?>
                            <a onclick="document.location.href='<?php echo et_get_page_link( 'list-notification' ); ?>'"
                               class="fre-notification notification-tablet">
                                <i class="fa fa-bell-o" aria-hidden="true"></i>
								<?php $notify_number = 0;
									if ( function_exists( 'fre_user_have_notify' ) ) {
										$notify_number = fre_user_have_notify();
										if ( $notify_number ) {
											echo '<span class="trigger-overlay trigger-notification-2 circle-new">' . $notify_number . '</span>';
										}
									} ?>
                            </a>
						<?php } ?>
                        <span class="hamburger-menu">
                                <div class="hamburger hamburger--elastic" tabindex="0" aria-label="Menu" role="button"
                                     aria-controls="navigation">
                                    <div class="hamburger-box">
                                        <div class="hamburger-inner"></div>
                                    </div>
                                </div>
                        </span>
                    </div>
                </div>

                <div class="col-lg-9 col-sm-9 col-md-9 col-xs-9 fixed">
                    <div class="hidden-lg col-md-12 col-sm-12 col-xs-12 fre-site-logo">
                        <a href="/"><?php fre_logo( 'site_logo' ) ?></a>
                    </div>

                    <div class="col-lg-10 col-md-12 col-sm-12 col-xs-12 fre-menu-top">
                        <ul class="fre-menu-main">
                            <!-- Menu freelancer -->
							<?php if ( ! is_user_logged_in() ) { ?>
                                <li class="fre-menu-freelancer dropdown">
                                    <a><?php _e( 'Professionals', ET_DOMAIN ); ?></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?php echo get_post_type_archive_link( PROJECT ); ?>"><?php _e( 'Find Projects', ET_DOMAIN ); ?></a>
                                        </li>
										<?php if ( fre_check_register() ) { ?>
                                            <li>
                                                <a href="<?php echo et_get_page_link( 'register' ) . '?role=freelancer'; ?>"><?php _e( 'Create Profile', ET_DOMAIN ); ?></a>
                                            </li>
										<?php } ?>
                                    </ul>
                                </li>
                                <li class="fre-menu-employer dropdown">
                                    <a><?php _e( 'Clients', ET_DOMAIN ); ?></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?php echo et_get_page_link( 'login' ) . '?ae_redirect_url=' . urlencode( et_get_page_link( 'submit-project' ) ); ?>"><?php _e( 'Post a Project', ET_DOMAIN ); ?></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo get_post_type_archive_link( PROFILE ); ?>"><?php _e( 'Find Professionals', ET_DOMAIN ); ?></a>
                                        </li>
                                    </ul>
                                </li>
							<?php } else { ?>

								<?php if ( ae_user_role( $user_ID ) == FREELANCER ) { ?>
                                    <li class="fre-menu-freelancer dropdown-empty">
                                        <a href="<?php echo et_get_page_link( "my-project" ); ?>"><?php _e( 'My project', ET_DOMAIN ); ?></a>
                                    </li>
                                    <li class="fre-menu-employer dropdown-empty">
                                        <a href="<?php echo get_post_type_archive_link( PROJECT ); ?>"><?php _e( 'Projects', ET_DOMAIN ); ?></a>
                                    </li>
								<?php } else { ?>
                                    <li class="fre-menu-employer dropdown">
                                        <a><?php _e( 'Projects', ET_DOMAIN ); ?></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="<?php echo et_get_page_link( "my-project" ); ?>"><?php _e( 'All Projects Posted', ET_DOMAIN ); ?></a>
                                            </li>
                                            <li>
                                                <a href="<?php echo et_get_page_link( 'submit-project' ); ?>"><?php _e( 'Post a Project', ET_DOMAIN ); ?></a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="fre-menu-employer dropdown-empty">
                                        <a href="<?php echo get_post_type_archive_link( PROFILE ); ?>"><?php _e( 'Professionals', ET_DOMAIN ); ?></a>
                                    </li>
								<?php } ?>
							<?php } ?>
                            <!-- Main Menu -->
							<?php if ( has_nav_menu( 'et_header_standard' ) ) { ?>

								<?php
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
								wp_nav_menu( $args );
								?>

							<?php } ?>
                            <!-- Main Menu -->
                        </ul>
                    </div>
					<?php if ( ! is_user_logged_in() ) { ?>
                        <div class="col-sm-12 col-md-12 col-lg-2 col-xs-12 fre-account-wrap">
                            <div class="fre-login-wrap">
                                <ul class="fre-login row">
                                    <li class="col-sm-12 col-md-12 col-lg-6">
                                        <a href="<?php echo et_get_page_link( "login" ) ?>"><?php _e( 'Login', ET_DOMAIN ); ?></a>
                                    </li>
									<?php if ( fre_check_register() ) { ?>
                                        <li class="col-sm-12 col-md-12 col-lg-6">
                                            <a href="<?php echo et_get_page_link( "register" ) ?>"><?php _e( 'Sign up', ET_DOMAIN ); ?></a>
                                        </li>
									<?php } ?>
                                </ul>
                            </div>
                        </div>
					<?php } else { ?>
                        <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12 fre-account-wrap dropdown">
                            <a class="fre-notification dropdown-toggle" data-toggle="dropdown" href="">
                                <i class="fa fa-bell-o" aria-hidden="true"></i>
								<?php $notify_number = 0;
									if ( function_exists( 'fre_user_have_notify' ) ) {
										$notify_number = fre_user_have_notify();
										if ( $notify_number ) {
											echo '<span class="trigger-overlay trigger-notification-2 circle-new">' . $notify_number . '</span>';
										}
									} ?>
                            </a>
							<?php fre_user_notification( $user_ID, 1, 5 ); ?>
                            <div class="fre-account dropdown">
                                <div class="fre-account-info dropdown-toggle" data-toggle="dropdown">
                                <span class="hamburger-menu">
                                    <div class="hamburger hamburger--elastic" tabindex="0" aria-label="Menu"
                                         role="button"
                                         aria-controls="navigation">
                                        <div class="hamburger-box">
                                            <div class="hamburger-inner"></div>
                                        </div>
                                    </div>
                                </span>
                                </div>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo et_get_page_link( "profile" ) ?>"><?php _e( 'My profile', ET_DOMAIN ); ?></a>
                                    </li>
									<?php if ( ae_user_role( $user_ID ) == FREELANCER ) { ?>
                                        <li>
                                            <a href="/my-adverts/"><?php _e( 'My Special Offers', ET_DOMAIN ); ?></a>
                                        </li>
									<?php } else { ?>
                                        <li>
                                            <a href="/special-offers/"><?php _e( 'Special Offers', ET_DOMAIN ); ?></a>
                                        </li>
									<?php } ?>
									<?php do_action( 'fre_header_before_notify' ); ?>
                                    <li><a href="<?php echo wp_logout_url(); ?>"><?php _e( 'Logout', ET_DOMAIN ); ?></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
					<?php } ?>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- MENU DOOR / END -->
<?php
	global $user_ID;
	if ( $user_ID ) {
		echo '<script type="data/json"  id="user_id">' . json_encode( [
				'id' => $user_ID,
				'ID' => $user_ID
			] ) . '</script>';
	}