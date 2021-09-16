<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */
$currenturl        = get_permalink();
$productcategories = get_the_terms( $post->ID, 'faqcat' );
foreach ( $productcategories as $productcategory ) {
	$taxonomy   = $productcategory->taxonomy;
	$terms_id   = $productcategory->term_id;
	$terms_name = $productcategory->name;
}
$args         = [
	'post_type'      => 'faq',
	'post_status'    => 'publish',
	'posts_per_page' => 5,
	'orderby'        => 'ID',
	'order'          => 'ASC',
	'tax_query'      => [
		[
			'taxonomy' => $taxonomy,
			'field'    => 'name',
			'terms'    => $terms_name
		]
	]
];
$partnersList = new WP_Query( $args );


$term        = get_term( $terms_id, $taxonomy );
$termParents = ( $term->parent == 0 ) ? $term : get_term( $term->parent, $taxonomy );
$terms2      = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'parent' => $termParents->term_id ] );


foreach ( $terms2 as $termParent ) {
	$termID   = $termParent->term_id;
	$termLink = get_term_link( $termID );
	$curTerm  = get_queried_object();
	$class    = ( $termParent->name == $curTerm->name ) ? 'active' : '';
}


get_header();

?>

    <div class="container page-container">
        <!-- block control  -->
        <div class="row block-posts block-page">
            <div class="col-sm-4 col-md-3 col-xs-12 hidden-xs page-sidebar" id="right_content">
                <div class="primary-sidebar help-sidebar widget-area" role="complementary">
                    <div class="fre-profile-box">
                        <ul class="help-list">
							<?php foreach ( $terms2 as $termParent ) {
								$termID   = $termParent->term_id;
								$termLink = get_term_link( $termID );
								$curTerm  = get_queried_object();
								$class    = ( $termParent->name == $terms_name ) ? 'active' : '';
								?>
                                <li class="help-item <?php echo $class ?>"><a href="<?php echo $termLink ?>"
                                                                              class="help-link"><?php echo $termParent->name ?></a>
									<?php if ( $termParent->name == $terms_name ) { ?>
                                        <ul class="help-sub__list">
											<?php
											//loop through query
											if ( $partnersList->have_posts() ) {
												while ( $partnersList->have_posts() ) {
													$partnersList->the_post();
													$link = get_permalink(); ?>
                                                    <li class="help-sub__item <?php if ( $currenturl == $link ) { ?>active<?php } ?>">
                                                        <a href="<?php the_permalink() ?>"
                                                           class="help-sub__link"><?php the_title(); ?></a>
                                                    </li>
													<?php
												}
											}
											wp_reset_postdata();
											?>
                                        </ul>
									<?php } ?>
                                </li>
							<?php } ?>
                        </ul>
                    </div>
                </div><!-- #primary-sidebar -->
            </div><!-- RIGHT CONTENT -->
            <div class="col-sm-8 col-md-9 col-xs-12 posts-container" id="left_content">
                <div class="blog-content">
                    <div class="description"><?php the_content(); ?></div>
                    <div class="clearfix"></div>
                    <div class="back-page">
                        <a href="<?php echo get_page_link( '13244' ) ?>">Back to HELP</a>
                    </div>
                </div><!-- end page content -->
            </div><!-- LEFT CONTENT -->
        </div>
    </div>
<?php
get_footer();
