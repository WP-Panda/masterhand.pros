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
					<?php echo __( "My rating", ET_DOMAIN ); ?>
                    <span class="total-rating">+<?php echo wpp_get_user_rating( $user_ID ) ?></span>
                </div>
               <!-- <ul class="pro-dop">
                    <li>
						<?php /*echo __( "PRO status ", ET_DOMAIN ); */?>
                        <span class="pro-rating">+<?php /*echo getActivityProRatingUser( $user_ID ) */?></span>
                    </li>
                </ul>-->
                <ul class="dop">
					<?php //getActivityDetailUser( $user_ID ) ?>

					<?php
					$rating    = get_user_meta( $user_ID, '_wpp_user_rating', true );
					$options   = wpp_rating_config();
					$role_flag = wpp_fre_is_freelancer() ? 'freelancer' : 'employer';
					wpp_dump( $rating);
					foreach ( $options['fields'] as $option_key => $option_options ) {
						if ( $role_flag === $option_options['for'] || 'all' === $option_options['for'] ) :
							printf( '<li>%s<span>%s</span></li>', $option_options['label'], ! empty( $rating[ $option_key ] ) ? '+' . $rating[ $option_key ] : 0 );
						endif;
					}
			//		wpp_dump( $rating );
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
					<?php echo __( "Account", ET_DOMAIN );
					if ( $user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER ) {
					pro_label(); ?>
                </div>
                <div class="pro-account">
                    <p>
						<?php echo __( "Your account has $user_pro_name status.", ET_DOMAIN ); ?>
                    </p>
                    <div class="benefits">
						<?php echo __( "My PRO Benefits:", ET_DOMAIN ) . '<span class="confirmed">' . __( "Enabled", ET_DOMAIN ) . ' (expire on ' . $user_pro_expire_normalize . ')</span>'; ?>
                    </div>
                </div>
				<?php } else { ?>
            </div>
            <div class="pro-account">
                <p>
					<?php echo __( "Get PRO status to have more benefits.", ET_DOMAIN ); ?>
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
				<?php echo __( "My Referral Activity", ET_DOMAIN ); ?>
            </div>
            <div class="refcount">
                <p>
					<?php echo __( "Referrals Connected:", ET_DOMAIN ); ?>
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