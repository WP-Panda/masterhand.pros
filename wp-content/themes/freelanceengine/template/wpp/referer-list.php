<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );

$metas = get_sponsor_id( $user_ID );

if ( ! empty( $metas ) ) : ?>

    <div class="fre-page-title">
        <h2 class="page_t"><?php _e( 'REFERER', ET_DOMAIN ) ?></h2>
    </div>

    <div class="table-header">
        <div class="row">
            <div class="col-sm-9 col-xs-7"><?php _e( 'Name', ET_DOMAIN ) ?></div>
            <div class="col-sm-3 col-xs-5 text-center"><?php _e( 'Status', ET_DOMAIN ) ?></div>
        </div>
    </div>

    <div class="page-referrals_list page-reffers-list fre-profile-box">
        <div class="page-referrals_item" data-id="<?php echo $metas ?>">
            <div class="row">
                <div class="col-sm-9 col-xs-7">
                    <a class="hidden-xs" href="<?php echo get_author_posts_url( $metas ) ?>">
						<?php echo get_avatar( $metas, 70 ); ?>
                    </a>
                    <a class="name" href="<?php echo get_author_posts_url( $metas ) ?>">
						<?php echo get_the_author_meta( 'display_name', $metas ) ?>
                    </a>
					<?php $user_status = get_user_pro_status( $metas );
					if ( userHaveProStatus( $metas ) ) {
						echo '<span class="status">' . translate( 'PRO', ET_DOMAIN ) . '</span>';
					} ?>
                    <span class="rating-new">+<?php echo getActivityRatingUser( $metas ) ?></span>
                </div>
                <div class="col-sm-3 col-xs-5 text-center endors <?php echo ! empty( WPP_Skills_User::getInstance()->is_emdorsment( $metas ) ) ? 'Endorsed' : 'Not Endorsed'; ?>">
					<?php echo ! empty( WPP_Skills_User::getInstance()->is_emdorsment( $metas ) ) ? 'Endorsed' : 'Not Endorsed'; ?>
                </div>
            </div>
        </div>
    </div>

<?php endif;