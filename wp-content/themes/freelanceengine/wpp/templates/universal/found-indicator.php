<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

extract( $args );

if ( ! empty( $found_posts_num ) ) :
	?>


    <div class="row">
        <div class="col-lg-4 col-lg-push-8 col-md-6 col-md-push-6 col-sm-6 col-sm-push-6">
            <button class="fre-submit-btn btn-get-quotes btn-right"><?php _e( 'Get Multiple Quotes', ET_DOMAIN ); ?></button>
        </div>
        <div class="col-lg-8 col-lg-pull-4 col-md-6 col-md-pull-6 col-sm-6 col-sm-pull-6 col-xs-12">
            <div class="fre-profile-result">
				<?php
				if ( (int) $found_posts_num !== 1 ) :
					printf( '<span class="plural">%s</span>', empty( $found_posts_num ) ? $not_found : $plural );
				endif;
				printf( '<div class="visible-xs"></div>' );
				if ( (int) $found_posts_num === 1 ) :
					printf( ' <span class="singular">%s</span>', $singular );
				endif;
				?>
            </div>
        </div>
    </div>
<?php
endif;