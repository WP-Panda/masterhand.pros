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
        <div class="profile-freelance-info top cnt-profile-hide row" id="cnt-profile-default"
             style="display: block">
            <div class="col-sm-2 col-xs-4 text-center avatar_wp">
				<?php echo get_avatar( $user_data->ID, 145 ); ?>
            </div>
            <div class="col-lg-3 col-sm-4 col-md-3 col-xs-8 no-pad">
                <div class="col-sm-12 col-md-12 col-lg-7 col-xs-12 freelance-name">
					<?php echo $display_name ?>
					<?php if ( $user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER ) {
						echo '<span class="status">' . translate( 'PRO', ET_DOMAIN ) . ' </span>';
						echo '<div class="status_expire">Expire: ' . $user_pro_expire . '</div>';
					}
					wpp_get_template_part( 'wpp/templates/profile/status-label', [ 'visualFlagNumber' => $visualFlagNumber ] );
					?>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-6 col-xs-12 free-rating">
					<?php HTML_review_rating_user( $user_ID ) ?>
                </div>
                <div class="col-sm-12 col-xs-12 freelance-profile-country">
					<?php
					if ( $location && ! empty( $location['country'] ) ) {
						$str = [];
						foreach ( $location as $key => $item ) {
							if ( ! empty( $item['name'] ) ) {
								$str[] = $item['name'];
							}
						}
						echo ! empty( $str ) ? implode( ' - ', $str ) : 'Error';
					} else { ?>
						<?php echo '<i>' . __( 'No country information', ET_DOMAIN ) . '</i>'; ?>
						<?php
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
                    <span><?php echo $referral_code; ?></span>
                </div>
                <script>
                    function selectText(doc, elementId, text) {
                        var range, selection;
                        text.innerText = '<?php echo $url ?>' + text.innerText;
                        if (doc.body.createTextRange) {
                            range = document.body.createTextRange();
                            range.moveToElementText(text);
                            range.select();
                        } else if (window.getSelection) {
                            selection = window.getSelection();
                            range = document.createRange();
                            range.selectNodeContents(text);
                            selection.removeAllRanges();
                            selection.addRange(range);
                        }
                    }

                    document.getElementsByClassName('copy')[0].click(function () {
                        var doc = document,
                            text = doc.getElementById(this.id),
                            str = text.innerText;

                        selectText(doc, this.id, text);
                        doc.execCommand("copy");
                        doc.getElementById(this.id).innerText = str;
                        alert("text copied")
                    });

                </script>
                <a href='/pro' class='fre-status'>
					<?php _e( 'Change Account Pro', ET_DOMAIN ) ?>
                </a>
                <a href="<?php echo $user_data->author_url ?>" id="link_as_others">
					<?php _e( 'View my profile as others', ET_DOMAIN ) ?>
                </a>
            </div>
        </div>

    </div>
</div>