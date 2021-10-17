<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit;
extract( $args );
?>
<div class="tab-content" id="TabsContent">
    <div class="tab-pane fade in active" id="rating" role="tabpanel" aria-labelledby="rating-tab">
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-8 col-xs-12 fre-profile-rating fre-profile-box">
                <div class="fre-profile-rating_t">
					<?php _e("My rating", ET_DOMAIN ); ?>
                    <span class="total-rating">+<?php echo wpp_get_user_rating( $user_ID ) ?></span>
                </div>
                <ul class="dop">
					<?php
					$out       = '';
					$rating    = get_user_meta( $user_ID, '_wpp_user_rating', true );
					$options   = wpp_rating_config();
					$role_flag = wpp_fre_is_freelancer() ? 'freelancer' : 'employer';
					//do_action( 'wpp_dump', $rating );

					//икс для про опций
					$pro = get_user_pro_status( $user_ID );


					foreach ( $options['fields'] as $option_key => $option_options ) {

						//фикс для про

						if ( ! empty( $option_options['pro_data'] ) && ! in_array( (int) $pro, $option_options['pro_data'] ) ) {
							continue;
						}

						//фикс для отключенных опций
						if ( ! empty( $option_options['disabled'] ) && true === $option_options['disabled'] ) {
							continue;
						}

						if ( $role_flag === $option_options['for'] || 'all' === $option_options['for'] && $option_key !== 'coefficient_pro_status' && $option_key !== 'coefficient_premium_pro_status' ) :
							$out .= sprintf( '<li>%s<span>%s</span></li>', $option_options['label'], ! empty( $rating[ $option_key ] ) ? '+' . $rating[ $option_key ] : 0 );
						endif;
					}

					$rate = wpp_pro_rate_delta($rating['total'],$user_ID);

					printf( '<li>%s<span>%s%s</span></li>', __( 'Coef. rating growth from Premium PRO status', WPP_TEXT_DOMAIN ), ! empty( $rate) ? '+' : '', $rate );

					echo $out;

					?>
                </ul>
            </div>

			<?php
			wpp_get_template_part( 'wpp/templates/profile/tabs/rating-side' );
			?>

            <div class="col-sm-12 col-md-4 col-lg-4 col-xs-12" hidden>
                <div class="category">
					<?php $stposts = [
						'numberposts'      => 3,
						'post_type'        => 'post',
						'orderby'          => 'date',
						'order'            => 'desc',
						'suppress_filters' => true
					];
					$lastposts     = get_posts( $stposts );
					foreach ( $lastposts as $post ) {
						setup_postdata( $post );
						get_template_part( 'template/blog', 'stickynoimg' );
					}
					wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
        <div class="tabs_wp fre-profile-box accpro col-sm-12 col-xs-12">
            <div class="col-sm-9 col-xs-12">
                <div class="tabs_wp_t">
					<?php _e("Account", ET_DOMAIN );
					if ( $user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER ) {
					pro_label(); ?>
                </div>
                <div class="pro-account">
                    <p>
						<?php _e("Your account has $user_pro_name status.", ET_DOMAIN ); ?>
                    </p>
                    <div class="benefits">
						<?php _e("My PRO Benefits:", ET_DOMAIN ) . '<span class="confirmed">' . __( "Enabled", ET_DOMAIN ) . ' (expire on ' . $user_pro_expire_normalize . ')</span>'; ?>
                    </div>
                </div>
				<?php } else { ?>
            </div>
            <div class="pro-account">
                <p>
					<?php _e("Get PRO status to have more benefits.", ET_DOMAIN ); ?>
                </p>
            </div>
			<?php } ?>
        </div><!--col-sm-9 col-xs-12-->
        <div class="col-sm-3 col-xs-12">
            <a href='/pro' class='fre-status unsubmit-btn btn-right'>
				<?php _e( 'Pro status', ET_DOMAIN ) ?>
            </a>
        </div>
    </div>
    <div class="tabs_wp fre-profile-box referalac col-sm-12 col-xs-12">
        <div class="col-sm-9 col-xs-12">
            <div class="tabs_wp_t">
				<?php _e("My Referral Activity", ET_DOMAIN ); ?>
            </div>
            <div class="refcount">
                <p>
					<?php _e("Referrals Connected:", ET_DOMAIN ); ?>
                    <span><?php echo $count_referrals ?></span></p>
            </div>
			<?php //wpp_get_template_part( 'wpp/templates/profile/awards-small' ); ?>
        </div>
        <div class="col-sm-3 col-xs-12">
            <a href='/referrals' class='fre-status unsubmit-btn btn-right'>
				<?php _e( 'Get more referrals', ET_DOMAIN ) ?>
            </a>
            <table class="table table-hover accordion">
                <a href='<?php echo '/give-endorsements' . $sponsor_name ?>'
                   class='unsubmit-btn btn-right'
                   style="margin-top: 10px;">
					<?php _e( 'Give Endorsement', ET_DOMAIN ) ?>
                </a>
            </table>
        </div>
    </div>
</div><!--rating rab-->