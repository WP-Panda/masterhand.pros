<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
$box       = ! empty( $not_load );
$referrals = get_list_referrals( 'all', get_current_user_id() );
$class     = empty( $box ) ? ' class="collapse"' : '';
?>

<?php if ( ! empty( $box ) ) : ?>
    <h2 class="page_t" style="margin-top:100px;">
		<?php echo __( 'Your Referrals:', ET_DOMAIN ) ?>
    </h2>
<?php endif; ?>

    <div class="page-referrals_list fre-profile-box">
		<?php if ( empty( $box ) ) : ?>
            <div class="special-header collapsed blue" data-toggle="collapse" data-target="#referrals-list">
				<?php echo __( 'Your Referrals:', ET_DOMAIN ) ?><i class="fa-angle-down fa"></i>
            </div>
		<?php endif; ?>
        <div id="referrals-list"<?php echo $class; ?>>
			<?php if ( empty( $referrals ) ) { ?>
				<?php _e( "No referrals", ET_DOMAIN ) ?>
			<?php } else {
				foreach ( $referrals as $item ) {
					$item       = (object) $item;
					$profile_id = get_user_meta( $item->user_id, 'user_profile_id', true ); ?>
                    <div class="page-referrals_item">
                        <a href="<?php echo '/user/' . $item->user_login ?>"><?php echo get_avatar( $item->user_id, 70 ); ?></a>
                        <a class="name"
                           href="<?php echo '/user/' . $item->user_login ?>"><?php echo $item->user_name ?></a>
                        <span class="status">
                                    <?php $user_status = get_user_pro_status( $item->user_id );
                                    $visualFlag        = getValueByProperty( $user_status, 'visual_flag' );
                                    if ( $visualFlag ) {
	                                    $visualFlagNumber = get_user_meta( $item->user_id, 'visual_flag', true );
                                    }
                                    if ( $user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER ) {
	                                    _e( 'PRO', ET_DOMAIN );
                                    } ?>
                                    </span>
						<?php visual_flag( $visualFlag, $visualFlagNumber ) ?>
                        <span class="free-rating-new">+<?php echo getActivityRatingUser( $item->user_id ); ?></span>
                    </div>
				<?php } ?>
			<?php } ?>
        </div>
		<?php /*if ( empty( $box ) ) : */ ?>
    </div>
<?php