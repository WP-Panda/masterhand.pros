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
wpp_action_template();
global $current_user, $user_ID, $post;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php wp_head(); ?>
    <style>
        .fre-submit-btn:hover[disabled="disabled"], .fre-submit-btn[disabled="disabled"], .fre-submit-btn:disabled, .fre-submit-btn:disabled:hover {
            opacity: 0.5 !important;
            background-color: #2c33c1 !important;
            color: #fff !important;
            cursor: not-allowed;
        }


        .hamburger-menu .avatar {
            border-radius: 50% !important;
            width: 60px !important;
            left: 50% !important;
            height: 60px !important;
            top: -10px !important;
            border: 2px solid #2430c6 !important;
            z-index: 1 !important;
        }

        .hamburger-box {
            height: 20px !important;
            display: inline-block !important;
            position: absolute !important;
            top: 35px !important;
            z-index: 55 !important;
            background-color: #fff !important;
            padding: 5px !important;
            width: 24px !important;
        }

        .hamburger-inner {
            left: 50% !important;
            margin-left: -10px !important;
        }

       /* .fre-notification i {
            font-size: 15px !important;
            padding: 5px !important;
            background: #dcdcdc;
            border-radius: 50%;
            color: #2c33c1;
            position: absolute;
            left: 23px;
            top: -9px;
        }*/

        /*.hamburger-menu span {
            display: block;
            position: absolute;
            background: #f32727;
            top: -8px !important;
            right: -8px !important;
            width: 23px !important;
            height: 23px !important;
            font-size: 15px !important;
            line-height: 23px;
            text-align: center;
            color: #fff;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
        }
*/
        .fre-btn.fre-post-project-next-btn.fre-submit-btn.wpp-submit {
            float: left;
            margin-right: 20px;
        }

        .fre-btn.fre-cancel-btn.wpp-clear-options {
            color: #fff !important;
            background-color: #878787 !important;
            border-color: #878787 !important;
        }

        .fre-btn.fre-cancel-btn.wpp-clear-options:hover {
            background-color: #fff !important;
            color: #878787 !important;
        }
    </style>
</head>
<body <?php body_class(); ?>>
<?php
$author_id = get_query_var( 'author' );
$views_id  = $author_id;
if ( is_author() ) { ?>
    <script>
        jQuery.ajax({type: 'GET', url: '/view.php', data: 'views_id=" . $views_id . "', cache: false});
    </script>
<?php } ?>
<header class="fre-header-wrapper">
    <div class="fre-header-wrap" id="main_header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-3 fre-site-logo">
                    <a class="col-lg-12 col-md-3 col-sm-4 col-xs-7 logo-head" href="/">
						<?php fre_logo( 'site_logo' ) ?>
                        <span>Low-Cost Service Deals</span>
                    </a>

                    <div class="col-xs-5 col-sm-8 col-md-9 hidden-lg fre-hamburger dropdown">

						<?php
						/**
						 * @todo  тут находится функционал уведомлений это, то, что надо будет поменять
						 */
						if ( is_user_logged_in() ) { ?>
                            <a onclick="document.location.href='<?php echo et_get_page_link( 'list-notification' ); ?>'"
                               class="fre-notification notification-tablet">
                                <i class="fa fa-bell-o" aria-hidden="true"></i>
								<?php
								if ( function_exists( 'fre_user_have_notify' ) ) {
									$notify_number = fre_user_have_notify();
									if ( $notify_number ) {
										printf( '<span class="trigger-overlay trigger-notification-2 circle-new">%s</span>', $notify_number );
									}
								} ?>
                            </a>
						<?php } ?>

                        <span class="hamburger-menu">
                             <?php echo get_avatar( $user_ID ); ?>
                            <div class="hamburger hamburger--elastic" tabindex="0"
                                 aria-label="<?php _e( 'Menu', WPP_TEXT_DOMAIN ) ?>" role="button"
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
						<?php wpp_get_template_part( 'wpp/templates/universal/top-nav' ); ?>
                    </div>
					<?php
					$preff = ! is_user_logged_in() ? '-not' : '';
					wpp_get_template_part( 'wpp/templates/profile/nav' . $preff . '-logged', [ 'user_ID' => $user_ID ] );
					?>
                </div>
            </div>
        </div>
    </div>
</header>
<?php
if ( $user_ID ) {
	echo '<script type="data/json"  id="user_id">' . json_encode( [
			'id' => $user_ID,
			'ID' => $user_ID
		] ) . '</script>';
}
?>
<script>
    jQuery(function ($) {



        setTimeout(function () {
             $_num = $('.page-template-page-options-project [type="checkbox"]:checked').length;
        }, 1000);


        $(document).on('change', '.page-template-page-options-project [type="checkbox"],.page-template-page-options-project [type="text"],.page-template-page-options-project [type="number"]', function () {
            var $_num_2 = $('.page-template-page-options-project [type="checkbox"]:checked').length;
            if ($_num_2 === $_num) {
                console.log('===');
                $('.wpp-submit').attr('disabled', 'disabled');
            } else {
                console.log('!===');
                $('.wpp-submit').removeAttr('disabled');
            }
        });
    })
</script>