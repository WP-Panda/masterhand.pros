<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
if ( empty( $title ) ) {
	if ( is_singular() ) {
		global $post;
		$title = $post->post_title;
	} else {
		$title = '';
	}
}
if ( ! empty( $title ) ) : ?>
    <div class="fre-page-title profs-cat">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <div class="profs-cat_t">
                        <h1><?php echo $title; ?></h1>
                    </div>
                </div>
                <div class="col-sm-6 hidden-xs">
                </div>
            </div>
        </div>
    </div>
<?php endif;