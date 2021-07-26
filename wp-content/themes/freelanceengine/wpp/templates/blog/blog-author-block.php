<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

extract( $args );

if ( ! empty( $author ) ) :
	?>

    <div class="row blog-wrapper">
        <div class="col-sm-2 col-xs-3 avatar-author">
            <div class="stories-img"
                 style="background:url(<?php echo get_avatar_url( $author ); ?>) center no-repeat;"></div>
        </div>
        <div class="col-sm-3 col-xs-9"><?php the_author(); ?></div>
        <div class="col-sm-3 col-xs-9 date"><?php the_date(); ?></div>
    </div>
<?php endif;