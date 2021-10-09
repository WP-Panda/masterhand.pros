<?php
global $wp_query, $ae_post_factory;
$post_object = $ae_post_factory->get( COMPANY );

$wp_query->query['post_status'] = 'publish';
$wp_query->query['post_type']   = COMPANY;
$wp_query->query['pagename']    = '';
$loop                           = new WP_Query( $wp_query->query );
?>

    <div class="project-list-container company-list-container">
		<?php
		$postdata = [];
		foreach ( $loop->posts as $item ) {
			$convert    = $post_object->convert( $item );
			$postdata[] = $convert;
			if ( $convert->post_status == 'publish' ) {
				get_template_part( 'template/company', 'item' );
			}
		}
		?>
    </div>
    <div class="profile-no-result" style="display: none;">
        <div class="profile-content-none">
            <p><?php _e( 'There are no results that match your search!', ET_DOMAIN ); ?></p>
            <ul>
                <li><?php _e( 'Try more general terms', ET_DOMAIN ) ?></li>
                <li><?php _e( 'Try another search method', ET_DOMAIN ) ?></li>
                <li><?php _e( 'Try to search by keyword', ET_DOMAIN ) ?></li>
            </ul>
        </div>
    </div>
<?php wp_reset_query(); ?>
<?php
/**
 * render post data for js
 */
echo '<script type="data/json" class="postdata" >' . json_encode( $postdata ) . '</script>';

get_template_part( 'template-js/wpp/modal', 'get-quote' );