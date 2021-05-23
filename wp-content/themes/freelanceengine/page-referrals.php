<?php
	get_header();

	global $ae_post_factory, $current_user, $user_ID;

	$ae_users  = AE_Users::get_instance();
	$user_data = $ae_users->convert( $current_user->data );

	$path = $_SERVER[ 'HTTPS' ] == 'on' ? 'https://' : 'http://';
	$path .= $_SERVER[ 'HTTP_HOST' ];

	$referrals = 0;
	if ( is_plugin_active( 'referral_code/referral_code.php' ) ) {
		$referrals = get_list_referrals( 'all', $user_ID );
	}
?>
    <script src="/wp-content/plugins/endorse_skill/js/select2.full.min.js"></script>
    <link rel="stylesheet" href="/wp-content/plugins/endorse_skill/css/select2.min.css">

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
					$linkurl    = $user_data->author_url;//'http://master.loc/author/alexey_marat/';
					if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) {
						ADDTOANY_SHARE_SAVE_KIT( compact( 'linkname', 'linkurl' ) );
					}
				?>
                <input type="hidden" id="author_url" value="<?= $linkurl ?>">
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
										<?= get_banner( "banner1" ) ?>
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
										<?= get_banner( "banner2" ) ?>
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
										<?= get_banner( "banner3" ) ?>
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
										<?= get_banner( "banner4" ) ?>
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
										<?= get_banner( "banner5" ) ?>
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
										<?= get_banner( "banner6" ) ?>
                                    </div>
                                </div>
                            </li>
						<?php } ?>
                    </ol>
                    <script src="/wp-content/plugins/generate_banner/js/main.js"></script>
                    <div class="modal fade" id="modal_banner" style="background:rgba(0,0,0,.45);">
                        <div class="modal-dialog" style="max-width: 1200px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"></button>
                                </div>
                                <div class="modal-body banner_form">
                                    <img class="img_show banner_social" src="" alt="banner">
                                    <div class="str_link"></div>
                                    <div class="sharing">
										<?php _e( 'Share via:' ); ?>
										<?php if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) {
											ADDTOANY_SHARE_SAVE_KIT();
										} ?>
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

                <div class="page-referrals_list fre-profile-box">
                    <div class="special-header collapsed blue" data-toggle="collapse" data-target="#referrals-list">
						<?php echo __( 'Your Referrals:', ET_DOMAIN ) ?><i class="fa-angle-down fa"></i>
                    </div>
                    <div id="referrals-list" class="collapse">
						<?php if ( count( $referrals ) == 0 ) { ?>
							<?php _e( "No referrals", ET_DOMAIN ) ?>
						<?php } else {
							foreach ( $referrals as $item ) {
								$profile_id = get_user_meta( $item[ 'user_id' ], 'user_profile_id', true ); ?>
                                <div class="page-referrals_item">
                                    <a href="<?= '/author/' . $item[ 'user_login' ] ?>"><?php echo get_avatar( $item[ 'user_id' ], 70 ); ?></a>
                                    <a class="name"
                                       href="<?= '/author/' . $item[ 'user_login' ] ?>"><?= $item[ 'user_name' ] ?></a>
                                    <span class="status">
                                    <?php $user_status = get_user_pro_status( $item[ 'user_id' ] );
	                                    $visualFlag    = getValueByProperty( $user_status, 'visual_flag' );
	                                    if ( $visualFlag ) {
		                                    $visualFlagNumber = get_user_meta( $item[ 'user_id' ], 'visual_flag', true );
	                                    }
	                                    if ( $user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER ) {
		                                    _e( 'PRO', ET_DOMAIN );
	                                    } ?>
                                    </span>
									<?php if ( $visualFlag ) {
										switch ( $visualFlagNumber ) {
											case 1:
												echo '<span class="status">' . translate( 'Master', ET_DOMAIN ) . '</span>';
												break;
											case 2:
												echo '<span class="status">' . translate( 'Creator', ET_DOMAIN ) . '</span>';
												break;
											case 3:
												echo '<span class="status">' . translate( 'Expert', ET_DOMAIN ) . '</span>';
												break;
										}
									} ?>
                                    <span class="free-rating-new">+<?= getActivityRatingUser( $item[ 'user_id' ] ); ?></span>
                                </div>
							<?php } ?>
						<?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        jQuery(function ($) {


            function generate_poster_ajax(action, template) {

                $('body').addClass('processing');

                $.ajax({
                    type: "POST",
                    url: '/wp-admin/admin-ajax.php',
                    headers: {
                        "Cache-Control": "no-cache, no-store, must-revalidate",
                        "Pragma": "no-cache",
                        "Expires": "0"
                    },
                    data: {template: template, action: 'generate_poster'}
                }).done(function (msg) {
                    $('body').removeClass('processing');

                    if (msg == '0') {
                        if (action == 'show') {
                            window.open(<?= json_encode( $path ) ?> +'/wp-content/plugins/generate_poster/cache/' + template + '_' + <?= json_encode( $user_ID )?> +'.pdf');
                        } else {
                            myWindow = window.open(<?= json_encode( $path ) ?> +'/wp-content/plugins/generate_poster/cache/' + template + '_' + <?= json_encode( $user_ID )?> +'.pdf');
                            myWindow.focus();
                            myWindow.print();
                        }

                        $.ajax({
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

            $('.sharing .addtoany_list .a2a_button_email').click(function () {
                $('.sharing .addtoany_list .a2a_button_email')["0"].href = "mailto:?subject=Check%20this%20out%3A%20Banner&body=" + $('.str_link')["0"].innerHTML
            })
        })
    </script>
    <div class="loading-blur loading">
        <div class="loading-overlay"></div>
        <div class="fre-loading-wrap">
            <div class="fre-loading"></div>
        </div>
    </div>
<?php get_footer();