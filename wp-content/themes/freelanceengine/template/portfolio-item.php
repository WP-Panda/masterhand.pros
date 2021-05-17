<?php
/**
 * The template for displaying user porfolio in profile details, edit profiles
 * @since 1.0
 * @package FreelanceEngine
 * @category Template
 */
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get( PORTFOLIO );
$current     = $post_object->current_post;
$author_id   = get_query_var( 'author' );
if ( ! $current ) {
	return;
}
$profile_id = get_user_meta( $author_id, 'user_profile_id', true );

$is_edit = get_query_var('is_edit');
?>

<li class="col-sm-4 col-md-3 col-lg-3 col-sx-12">
    <div class="freelance-portfolio-wrap" id="portfolio_item_<?php echo $current->ID ?>">
        <div class="freelance-portfolio" style="background:url(<?php echo $current->the_post_thumbnail_full;?>) center no-repeat;">
            <a href="javascript:void(0)" class="fre-view-portfolio-new" data-id="<?php echo $current->ID ?>">
            </a>
        </div>
        <div class="portfolio-title">
            <a class="fre-view-portfolio-new" href="javascript:void(0)"
               data-id="<?php echo $current->ID ?>"> <?php echo $current->post_title ?> </a>
        </div>
		<?php if ( $is_edit ) { ?>
            <div class="portfolio-action">
                <a href="javascript:void(0)" class=" fre-submit-btn btn-center edit-portfolio" data-id="<?php echo $current->ID ?>"><?php _e( 'Edit', ET_DOMAIN ) ?></a>

                <a href="javascript:void(0)" class="fre-cancel-btn btn-center remove_portfolio" data-portfolio_id="<?php echo $current->ID ?>"><?php _e( 'Remove', ET_DOMAIN ) ?></a>
            </div>
		<?php } ?>
    </div>
</li>

