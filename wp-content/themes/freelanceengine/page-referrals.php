<?php
get_header();

global $ae_post_factory, $current_user, $user_ID;

$ae_users  = AE_Users::get_instance();
$user_data = $ae_users->convert( $current_user->data );


$path = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
$path .= $_SERVER['HTTP_HOST'];
?>
    <script src="/wp-content/plugins/endorse_skill/js/select2.full.min.js"></script>
    <link rel="stylesheet" href="/wp-content/plugins/endorse_skill/css/select2.min.css">-

    <div class="fre-page-wrapper page-referrals">
        <div class="fre-page-title">
            <div class="container">
                <h1 class="page_t"><?php the_title(); ?></h1>
            </div>
        </div>

        <div class="fre-page-section">
            <div class="container">
                <div class="fre-profile-box page-referrals_txt">
					<?php if ( get_field( 'blue-header' ) ) { ?>
                        <div class="special-header blue">
							<?php the_field( 'blue-header' ) ?>
                        </div>
					<?php } ?>
					<?php the_content(); ?>
                </div>

				<?php if ( get_field( 'email-txt' ) ) { ?>
                    <div class="page-referrals-email">
						<?php if ( get_field( 'email-header' ) ) { ?>
                            <div class="special-header">
								<?php the_field( 'email-header' ) ?>
                            </div>
						<?php } ?>
						<?php the_field( 'email-txt' ) ?>
                    </div>
				<?php } ?>

				<?php $linkname = '';
				$linkurl        = $user_data->author_url;//'http://master.loc/author/alexey_marat/';
				if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) {
					ADDTOANY_SHARE_SAVE_KIT( compact( 'linkname', 'linkurl' ) );
				}
				?>
                <input type="hidden" id="author_url" value="<?php echo $linkurl ?>">
				<?php if ( get_field( 'network-txt' ) ) { ?>
                    <div class="page-referrals-network">
						<?php if ( get_field( 'network-header' ) ) { ?>
                            <div class="special-header">
								<?php the_field( 'network-header' ) ?>
                            </div>
						<?php } ?>
						<?php the_field( 'network-txt' ) ?>
                    </div>
				<?php } ?>

				<?php $isFreelancer = ( ae_user_role( $user_ID ) == FREELANCER ) ? 1 : 0; ?>
                <div class="page-referrals_bnr text-center">
                    <ol class="row">
						<?php if ( fre_share_role() || $isFreelancer ) { ?>
                            <li class="col-sm-4 col-xs-12">
                                <img src="/wp-content/plugins/generate_banner/template/banner1.png" alt="bn1"
                                     width="250px">
                                <button class="getImgBtn fre-submit-btn btn-center"
                                        data-name="banner1"><?php _e( 'GENERATE PERSONAL BANNER' ); ?></button>
                                <div style="">
                                    <div id="content_banner1" data-template="banner1">
										<?php echo get_banner( "banner1" ) ?>
                                    </div>
                                </div>
                            </li>
                            <li class="col-sm-4 col-xs-12">
                                <img src="/wp-content/plugins/generate_banner/template/banner2.png" alt="bnr2"
                                     width="250px">
                                <button class="getImgBtn fre-submit-btn btn-center"
                                        data-name="banner2"><?php _e( 'GENERATE PERSONAL BANNER' ); ?></button>
                                <div style="">
                                    <div id="content_banner2" data-template="banner2">
										<?php echo get_banner( "banner2" ) ?>
                                    </div>
                                </div>
                            </li>
                            <li class="col-sm-4 col-xs-12">
                                <img src="/wp-content/plugins/generate_banner/template/banner3.png" alt="bnr3"
                                     width="250px">
                                <button class="getImgBtn fre-submit-btn btn-center"
                                        data-name="banner3"><?php _e( 'GENERATE PERSONAL BANNER' ); ?></button>
                                <div style="">
                                    <div id="content_banner3" data-template="banner3">
										<?php echo get_banner( "banner3" ) ?>
                                    </div>
                                </div>
                            </li>
						<?php } else { ?>
                            <li class="col-sm-4 col-xs-12">
                                <img src="/wp-content/plugins/generate_banner/template/banner4.png" alt="bn4"
                                     width="250px">
                                <button class="getImgBtn fre-submit-btn btn-center"
                                        data-name="banner4"><?php _e( 'GENERATE PERSONAL BANNER' ); ?></button>
                                <div style="">
                                    <div id="content_banner4" data-template="banner4">
										<?php echo get_banner( "banner4" ) ?>
                                    </div>
                                </div>
                            </li>
                            <li class="col-sm-4 col-xs-12">
                                <img src="/wp-content/plugins/generate_banner/template/banner5.png" alt="bnr5"
                                     width="250px">
                                <button class="getImgBtn fre-submit-btn btn-center"
                                        data-name="banner5"><?php _e( 'GENERATE PERSONAL BANNER' ); ?></button>
                                <div style="">
                                    <div id="content_banner5" data-template="banner5">
										<?php echo get_banner( "banner5" ) ?>
                                    </div>
                                </div>
                            </li>
                            <li class="col-sm-4 col-xs-12">
                                <img src="/wp-content/plugins/generate_banner/template/banner6.png" alt="bnr6"
                                     width="250px">
                                <button class="getImgBtn fre-submit-btn btn-center"
                                        data-name="banner6"><?php _e( 'GENERATE PERSONAL BANNER' ); ?></button>
                                <div style="">
                                    <div id="content_banner6" data-template="banner6">
										<?php echo get_banner( "banner6" ) ?>
                                    </div>
                                </div>
                            </li>
						<?php } ?>
                    </ol>
                    <script src="/wp-content/plugins/generate_banner/js/main.js?5"></script>
					<?php $user_page = get_home_url() . '/user/' . $user_data->user_nicename; ?>
                    <div class="modal fade" id="modal_banner" style="background:rgba(0,0,0,.45);">
                        <div class="modal-dialog" style="max-width: 1200px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"></button>
                                </div>
                                <div class="modal-body banner_form">
                                    <img class="img_show banner_social" src="" alt="banner">
                                    <div class="wpp-sharing">
										<?php _e( 'Share via:' ); ?>
                                        <div class="wpp-share-box">
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $user_page; ?>"
                                               class="wpp-share-btn wpp-facebook-btn" rel="nofollow"
                                               onclick="window.open(jQuery(this).attr('href'), 'fbShareWindow', 'height=450, width=550, top=' + (jQuery(window).height() / 2 - 275) + ', left=' + (jQuery(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0'); return false;">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                    <path fill="#FFF"
                                                          d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"></path>
                                                </svg>
                                            </a>
                                            <a href="https://twitter.com/intent/tweet?url=<?php echo $user_page; ?>"
                                               class="wpp-share-btn wpp-twitter-btn" rel="nofollow"
                                               onclick="window.open(jQuery(this).attr('href'), 'twShareWindow', 'height=450, width=550, top=' + (jQuery(window).height() / 2 - 275) + ', left=' + (jQuery(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0'); return false;">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                    <path fill="#FFF"
                                                          d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.03.7.1 1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6 1.45-.6 2.3 0 1.56.8 2.95 2 3.77-.74-.03-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.44-.67.2-1.37.2-2.06.08.58 1.8 2.26 3.12 4.25 3.16C5.78 18.1 3.37 18.74 1 18.46c2 1.3 4.4 2.04 6.97 2.04 8.35 0 12.92-6.92 12.92-12.93 0-.2 0-.4-.02-.6.9-.63 1.96-1.22 2.56-2.14z"></path>
                                                </svg>
                                            </a>
                                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $user_page; ?>"
                                               class="wpp-share-btn wpp-linkedin-btn" rel="nofollow"
                                               onclick="window.open(jQuery(this).attr('href'), 'twShareWindow', 'height=450, width=550, top=' + (jQuery(window).height() / 2 - 275) + ', left=' + (jQuery(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0'); return false;">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                    <path fill="#FFF"
                                                          d="M6.5 21.5h-5v-13h5v13zM4 6.5C2.5 6.5 1.5 5.3 1.5 4s1-2.4 2.5-2.4c1.6 0 2.5 1 2.6 2.5 0 1.4-1 2.5-2.6 2.5zm11.5 6c-1 0-2 1-2 2v7h-5v-13h5V10s1.6-1.5 4-1.5c3 0 5 2.2 5 6.3v6.7h-5v-7c0-1-1-2-2-2z"></path>
                                                </svg>
                                            </a>
                                            <!-- <a href="<?php /*echo $user_page; */ ?>" class="wpp-share-btn wpp-emailn-btn" rel="nofollow">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                    <path fill="#FFF" d="M22 4H2C.9 4 0 4.9 0 6v12c0 1.1.9 2 2 2h20c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM7.25 14.43l-3.5 2c-.08.05-.17.07-.25.07-.17 0-.34-.1-.43-.25-.14-.24-.06-.55.18-.68l3.5-2c.24-.14.55-.06.68.18.14.24.06.55-.18.68zm4.75.07c-.1 0-.2-.03-.27-.08l-8.5-5.5c-.23-.15-.3-.46-.15-.7.15-.22.46-.3.7-.14L12 13.4l8.23-5.32c.23-.15.54-.08.7.15.14.23.07.54-.16.7l-8.5 5.5c-.08.04-.17.07-.27.07zm8.93 1.75c-.1.16-.26.25-.43.25-.08 0-.17-.02-.25-.07l-3.5-2c-.24-.13-.32-.44-.18-.68s.44-.32.68-.18l3.5 2c.24.13.32.44.18.68z"></path>
                                                </svg>
                                            </a>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<? get_template_part( 'template-js/modal', 'send-banner-to-emails' ); ?>
                </div>

				<?php if ( fre_share_role() || $isFreelancer ) { ?>

					<?php if ( get_field( 'poster-txt' ) ) { ?>
                        <div class="page-referrals-network">
							<?php if ( get_field( 'poster-header' ) ) { ?>
                                <div class="special-header">
									<?php the_field( 'poster-header' ) ?>
                                </div>
							<?php } ?>
							<?php the_field( 'poster-txt' ) ?>
                        </div>
					<?php } ?>

                    <div class="page-referrals_posters text-center">
                        <ol class="row">
                            <li class="col-sm-4 col-xs-12">
                                <img src="/wp-content/plugins/generate_poster/template/assets/poster1.png" alt="poster1"
                                     width="250px">
                                <button class="generate fre-submit-btn btn-center"
                                        onclick="generate_poster_ajax('print', 'poster1')">print
                                </button>
                                <p><?php _e( "or", ET_DOMAIN ) ?></p>
                                <button class="generate fre-submit-btn btn-center"
                                        onclick="generate_poster_ajax('show', 'poster1')">download
                                </button>
                            </li>
                            <li class="col-sm-4 col-xs-12">
                                <img src="/wp-content/plugins/generate_poster/template/assets/poster3.png" alt="poster3"
                                     width="250px">
                                <button class="generate fre-submit-btn btn-center"
                                        onclick="generate_poster_ajax('print', 'poster3')">print
                                </button>
                                <p><?php _e( "or", ET_DOMAIN ) ?></p>
                                <button class="generate fre-submit-btn btn-center"
                                        onclick="generate_poster_ajax('show', 'poster3')">download
                                </button>
                            </li>
                            <li class="col-sm-4 col-xs-12">
                                <img src="/wp-content/plugins/generate_poster/template/assets/poster2.png" alt="poster2"
                                     width="250px">
                                <button class="generate fre-submit-btn btn-center"
                                        onclick="generate_poster_ajax('print', 'poster2')">print
                                </button>
                                <p><?php _e( "or", ET_DOMAIN ) ?></p>
                                <button class="generate fre-submit-btn btn-center"
                                        onclick="generate_poster_ajax('show', 'poster2')">download
                                </button>
                            </li>
                        </ol>
                    </div>
				<?php } ?>

				<?php wpp_get_template_part( 'wpp/templates/profile/refferal-list' ); ?>
            </div>
        </div>
    </div>


    <script>
        // jQuery(function ($) {


        function generate_poster_ajax(action, template) {

            jQuery('body').addClass('processing');

            jQuery.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                headers: {
                    "Cache-Control": "no-cache, no-store, must-revalidate",
                    "Pragma": "no-cache",
                    "Expires": "0"
                },
                data: {template: template, action: 'generate_poster'}
            }).done(function (msg) {
                jQuery('body').removeClass('processing');

                if (msg == '0') {
                    if (action == 'show') {
                        window.open(<?php echo json_encode( $path ) ?> +'/wp-content/plugins/generate_poster/cache/' + template + '_' + <?php echo json_encode( $user_ID )?> +'.pdf');
                    } else {
                        myWindow = window.open(<?php echo json_encode( $path ) ?> +'/wp-content/plugins/generate_poster/cache/' + template + '_' + <?php echo json_encode( $user_ID )?> +'.pdf');
                        myWindow.focus();
                        myWindow.print();
                    }

                    jQuery.ajax({
                        type: "POST",
                        url: '/wp-admin/admin-ajax.php',
                        data: {template: template, action: 'delete_cache_poster'}
                    }).done(function (msg) {
                        if (msg !== '0') {
                            console.log(msg)
                        }
                    });
                } else console.log(msg);
            });
        }

        jQuery('.sharing .addtoany_list .a2a_button_email').click(function () {
            jQuery('.sharing .addtoany_list .a2a_button_email')["0"].href = "mailto:?subject=Check%20this%20out%3A%20Banner&body=" + jQuery('.str_link')["0"].innerHTML
        })
        // })
    </script>
    <div class="loading-blur loading">
        <div class="loading-overlay"></div>
        <div class="fre-loading-wrap">
            <div class="fre-loading"></div>
        </div>
    </div>
<?php get_footer();