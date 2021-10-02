<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
?>

<div class="fre-profile-box">
    <div class="profile-freelance-info-wrap active">
        <div class="profile-freelance-info top cnt-profile-hide row" id="cnt-profile-default" style="display: block">
            <div class="col-sm-2 col-xs-4 text-center avatar_wp">
				<?php echo get_avatar( $user_data->ID, 145 ); ?>
            </div>
            <div class="col-lg-3 col-sm-4 col-md-3 col-xs-8 no-pad">
                <div class="col-sm-12 col-md-12 col-lg-7 col-xs-12 freelance-name">
					<?php echo $display_name;
					if ( ! empty( $user_status ) && $user_status !== PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER ) {
						pro_label();
						status_expire( $user_pro_expire );
					}
					wpp_get_template_part( 'wpp/templates/profile/status-label', [ 'visualFlagNumber' => $visualFlagNumber ] );
					?>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-6 col-xs-12 free-rating">
					<?php HTML_review_rating_user( $user_ID ) ?>
                </div>
                <div class="col-sm-12 col-xs-12 freelance-profile-country">
					<?php
					if ( ! empty( $location ) && ! empty( $location['country'] ) ) {
						$str = [];
						foreach ( $location as $key => $item ) {
							if ( ! empty( $item['name'] ) ) {
								$str[] = $item['name'];
							}
						}
						echo ! empty( $str ) ? implode( ' - ', $str ) : 'Error';
					} else {
						echo '<i>' . __( 'No country information', ET_DOMAIN ) . '</i>';
					}
					?>
                </div>
				<?php if ( fre_share_role() || ae_user_role( $user_data->ID ) == FREELANCER ) { ?>
                    <div class="col-sm-6 hidden-xs skill">
						<?php echo ! empty( $profile->experience ) ? $profile->experience : '<span>0</span>' . __( 'years experience', ET_DOMAIN ); ?></div>
                    <div class="col-sm-6 hidden-xs skill">
						<?php printf( __( '<span>%s</span> projects worked', ET_DOMAIN ), intval( $projects_worked ) ); ?> </div>
				<?php } else { ?>
                    <div class="col-sm-6 hidden-xs skill">
						<?php printf( __( '<span>%s</span> projects posted', ET_DOMAIN ), intval( $project_posted ) ); ?></div>
                    <div class="col-sm-6 hidden-xs skill">
						<?php printf( __( '<span>%s</span> professionals hired', ET_DOMAIN ), intval( $hire_freelancer ) ); ?></div>
				<?php } ?>
            </div>

			<?php if ( fre_share_role() || ae_user_role( $user_data->ID ) == FREELANCER ) { ?>
                <div class="hidden-sm col-xs-12">
                    <div class="col-xs-6 skill">
						<?php echo ! empty( $profile->experience ) ? $profile->experience : '<span>0</span>' . __( 'years experience', ET_DOMAIN ); ?></div>
                    <div class="col-xs-6 skill">
						<?php printf( __( '<span>%s</span> projects worked', ET_DOMAIN ), intval( $projects_worked ) ); ?> </div>
                </div>
			<?php } else { ?>
                <div class="hidden-sm col-xs-12">
                    <div class="col-xs-6 skill">
						<?php printf( __( '<span>%s</span> projects posted', ET_DOMAIN ), intval( $project_posted ) ); ?></div>
                    <div class="col-xs-6 skill">
						<?php printf( __( '<span>%s</span> professionals hired', ET_DOMAIN ), intval( $hire_freelancer ) ); ?></div>
                </div>
			<?php } ?>

            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 else-info">
                <div class="rating-new">
					<?php echo __( 'Rating:', ET_DOMAIN ); ?>
                    <span>+<?php echo getActivityRatingUser( $user_ID ) ?></span>
                </div>
                <div class="secure-deals">
                    <a href="/give-endorsements">
						<?php echo __( 'SafePay Deals:', ET_DOMAIN ); ?>
                    </a><span><?php echo ( get_user_meta( $user_ID, 'safe_deals_count', 1 ) == '' ) ? 0 : get_user_meta( $user_ID, 'safe_deals_count', 1 ) ?></span>
                </div>
                <div class="reviews">
					<?php echo __( 'Reviews:', ET_DOMAIN ); ?>
                    <span><?php echo get_count_reviews_user( $user_ID ); ?></span>
                </div>
                <div class="city">
					<?php if ( $location && ! empty( $location['country'] ) ) {
						$str = [];
						foreach ( $location as $key => $item ) {
							if ( ! empty( $item['name'] ) ) {
								$str[] = $item['name'];
							}
						}
						echo ! empty( $str ) ? __( 'City:', ET_DOMAIN ) . '<span>' . $str[2] . '</span>' : '';
					} ?>
                </div>
            </div>

            <div class="col-md-2 col-sm-3 col-lg-2 col-xs-12 skills">
                <div class="skill col-sm-12 col-xs-6">
					<?php echo __( 'skills & endorsements', ET_DOMAIN ); ?>
                    <span><?php echo countEndorseSkillsUser( $user_ID ) ?></span>
                </div>
                <div class="skill col-sm-12 col-xs-6">
					<?php echo __( 'awards', ET_DOMAIN ); ?><span>0</span>
                </div>
            </div>

            <div class="col-sm-3 col-md-3 col-lg-2 col-xs-12 fre-profile_refinfo">
                <span><?php echo __( 'My referral code:', ET_DOMAIN ) ?></span>
				<?php $url = $_SERVER["HTTP_HOST"] . '/register/?code='; ?>
                <div id="Text" class="copy refnumber">
                    <span class="wpp-copy-btn"
                          data-clipboard-text="<?php echo $referral_code; ?>"><?php echo $referral_code; ?></span>
                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                         viewBox="0 0 488.3 488.3" style="enable-background:new 0 0 488.3 488.3;" xml:space="preserve">
<g>
    <g>
        <path d="M314.25,85.4h-227c-21.3,0-38.6,17.3-38.6,38.6v325.7c0,21.3,17.3,38.6,38.6,38.6h227c21.3,0,38.6-17.3,38.6-38.6V124
			C352.75,102.7,335.45,85.4,314.25,85.4z M325.75,449.6c0,6.4-5.2,11.6-11.6,11.6h-227c-6.4,0-11.6-5.2-11.6-11.6V124
			c0-6.4,5.2-11.6,11.6-11.6h227c6.4,0,11.6,5.2,11.6,11.6V449.6z"/>
        <path d="M401.05,0h-227c-21.3,0-38.6,17.3-38.6,38.6c0,7.5,6,13.5,13.5,13.5s13.5-6,13.5-13.5c0-6.4,5.2-11.6,11.6-11.6h227
			c6.4,0,11.6,5.2,11.6,11.6v325.7c0,6.4-5.2,11.6-11.6,11.6c-7.5,0-13.5,6-13.5,13.5s6,13.5,13.5,13.5c21.3,0,38.6-17.3,38.6-38.6
			V38.6C439.65,17.3,422.35,0,401.05,0z"/>
    </g>
</g>
</svg>
                </div>
                <a href='/pro' class='fre-status'>
					<?php _e( 'Change Account Pro', ET_DOMAIN ) ?>
                </a>
                <a href="<?php echo $user_data->author_url ?>" id="link_as_others">
					<?php _e( 'View my profile as others', ET_DOMAIN ) ?>
                </a>
            </div>
        </div>

    </div>