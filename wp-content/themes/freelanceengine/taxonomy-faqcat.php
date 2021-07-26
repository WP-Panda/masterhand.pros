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


$taxonomy_list = get_queried_object();
$taxonomy      = $taxonomy_list->taxonomy;
$terms_id      = $taxonomy_list->term_id;
$args          = [
	'post_type'      => 'faq',
	'post_status'    => 'publish',
	'posts_per_page' => 5,
	'orderby'        => 'ID',
	'order'          => 'ASC',
	'tax_query'      => [
		[
			'taxonomy' => $taxonomy,
			'field'    => 'name',
			'terms'    => $taxonomy_list->name
		]
	]
];
$partnersList  = new WP_Query( $args );

$previous = "javascript:history.go(-1)";
if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
	$previous = $_SERVER['HTTP_REFERER'];
}


$term        = get_term( $terms_id, $taxonomy );
$termParents = ( $term->parent == 0 ) ? $term : get_term( $term->parent, $taxonomy );
$terms2      = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'parent' => $termParents->term_id ] );

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
								$class    = ( $termParent->name == $curTerm->name ) ? 'active' : '';
								?>
                                <li class="help-item <?php echo $class ?>"><a href="<?php echo $termLink ?>"
                                                                              class="help-link"><?php echo $termParent->name ?></a>
                                </li>
							<?php } ?>
                        </ul>
                    </div>
                </div><!-- #primary-sidebar -->

            </div><!-- RIGHT CONTENT -->
            <div class="col-sm-8 col-md-9 col-xs-12 posts-container" id="left_content">
                <div class="blog-content help-content">
                    <div class="help-content-title">
                        <img src="<?php the_field( 'catic', $taxonomy . '_' . $terms_id ); ?>">
                        <h1 class="help-title"><?php single_cat_title(); ?></h1>
                    </div>
                    <ul class="help-sub_menu">
						<?php
						//loop through query
						if ( $partnersList->have_posts() ) {
							while ( $partnersList->have_posts() ) {
								$partnersList->the_post();
								?>
                                <li class="help-sub_menu__item">
                                    <a class="help-sub_menu__link"
                                       href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </li>

								<?php
							}
						}

						wp_reset_postdata();

						?>
                    </ul>
                    <div class="clearfix"></div>
                    <div class="back-page">
                        <a href="<?php echo get_page_link( '13244' ) ?>">Back to HELP</a>
                    </div>
                </div><!-- end page content -->
            </div><!-- LEFT CONTENT -->
        </div>
        <!--// block control  -->
    </div>

<?php
get_footer();
?>