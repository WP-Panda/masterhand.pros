<?php
/**
 * Template Name: Profiles by category
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link       http://codex.wordpress.org/Template_Hierarchy
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */
global $wp_query, $ae_post_factory, $post;
get_header();
?>

    <div class="profs-cat page-profs">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <div class="profs-cat_t">
                        <h1><?php echo __( 'Profiles by category', ET_DOMAIN ); ?></h1>
						<?php _e( 'Select a category for your task', ET_DOMAIN ) ?>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12 hidden-xs">
                    <div class="profs-cat_desc">
                        <span><?php echo get_theme_mod( "title_ncat" ); ?></span>
                        <div class="profs-cat_desc_text"><?php echo get_theme_mod( "title_prcat" ); ?></div>
                    </div>
                </div>
            </div>
			<?php
			$taxonomy = 'project_category';
			$terms    = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'parent' => 0 ] );
			$colc     = count( $terms );
			$coln     = round( ( count( $terms ) ) / 3 );
			$i        = 0;
			if ( $terms && ! is_wp_error( $terms ) ) :?>
                <div class="row visible-lg visible-md visible-xs">
                    <div class="col-md-4 col-lg-4 col-xs-12">
						<?php foreach ( $terms

						as $term ) {
						$termid     = $term->term_id;
						$termschild = get_terms( [
							'taxonomy'   => $taxonomy,
							'hide_empty' => 0,
							'parent'     => $termid
						] ); ?>
                        <div
                                class="profs-cat-list  <?php if ( $termschild && ! is_wp_error( $termschild ) ) : ?> faa-parent <?php endif; ?> animated-hover">
							<?php if ( $termschild && ! is_wp_error( $termschild ) ) { ?>
                                <span
                                        style="background:#fff url(<?php the_field( 'catic', $taxonomy . '_' . $termid ); ?>) 40px center no-repeat;"><?php echo $term->name; ?></span>

							<?php } else { ?>
                                <a href="<?php echo bloginfo( 'url' ) . "/profile_category/" . $term->slug; ?>"
                                   style="background:#fff url(<?php the_field( 'catic', $taxonomy . '_' . $termid ); ?>) 40px center no-repeat;"><?php echo $term->name; ?></a>
							<?php } ?>
                            <div class="profs-cat_sublist">
								<?php $max_show = 10;
								$count_all_sub  = count( $termschild );
								$count_show     = $count_all_sub > $max_show ? $max_show : $count_all_sub; ?>
								<?php for ( $key = 0; $key < $max_show; $key ++ ) { ?>
                                    <div>
                                        <a href="<?php echo bloginfo( 'url' ) . "/profile_category/" . $termschild[ $key ]->slug; ?>"><?php echo $termschild[ $key ]->name; ?></a>
                                    </div>
								<?php } ?>
								<?php if ( $count_all_sub > $count_show ) { ?>
                                    <div class="all_sub_cat">
										<?php for ( $key = $max_show; $key < $count_all_sub; $key ++ ) { ?>
                                            <div class="noactive">
                                                <a href="<?php echo bloginfo( 'url' ) . "/profile_category/" . $termschild[ $key ]->slug; ?>"><?php echo $termschild[ $key ]->name; ?></a>
                                            </div>
										<?php } ?>
                                        <span class="text_count"><em>All</em> <?php echo $count_all_sub ?> Subcategories
                                        <i class="fa fa-angle-down"></i>
                                    </span>
                                    </div>
								<?php } ?>
                            </div>
                        </div>

						<?php $i ++;
						$colc --;
						if ( ( $i == $coln ) && ( $colc != 0 ) ) { ?>
                    </div>
                    <div class="col-md-4 col-lg-4 col-xs-12">
						<?php
						$i = 0;
						} ?>

						<?php } ?>
                    </div>
                </div>
                <div class="row visible-sm">
					<?php $coln2 = round( ( count( $terms ) ) / 2 );
					$colc2       = count( $terms );
					$i2          = 0; ?>
                    <div class="col-sm-6">
						<?php foreach ( $terms

						as $term ) {
						$termid     = $term->term_id;
						$termschild = get_terms( [
							'taxonomy'   => $taxonomy,
							'hide_empty' => 0,
							'parent'     => $termid
						] ); ?>
                        <div
                                class="profs-cat-list  <?php if ( $termschild && ! is_wp_error( $termschild ) ) : ?> faa-parent <?php endif; ?> animated-hover">
							<?php if ( $termschild && ! is_wp_error( $termschild ) ) { ?>
                                <span
                                        style="background:#fff url(<?php the_field( 'catic', $taxonomy . '_' . $termid ); ?>) 40px center no-repeat;"><?php echo $term->name; ?></span>
							<?php } else { ?>
                                <a href="<?php echo bloginfo( 'url' ) . "/profile_category/" . $term->slug; ?>"
                                   style="background:#fff url(<?php the_field( 'catic', $taxonomy . '_' . $termid ); ?>) 40px center no-repeat;"><?php echo $term->name; ?></a>
							<?php } ?>
                            <div class="profs-cat_sublist">
								<?php $max_show = 10;
								$count_all_sub  = count( $termschild );
								$count_show     = $count_all_sub > $max_show ? $max_show : $count_all_sub; ?>
								<?php for ( $key = 0; $key < $max_show; $key ++ ) { ?>
                                    <div>
                                        <a href="<?php echo bloginfo( 'url' ) . "/profile_category/" . $termschild[ $key ]->slug; ?>"><?php echo $termschild[ $key ]->name; ?></a>
                                    </div>
								<?php } ?>
								<?php if ( $count_all_sub > $count_show ) { ?>
                                    <div class="all_sub_cat">
										<?php for ( $key = $max_show; $key < $count_all_sub; $key ++ ) { ?>
                                            <div class="noactive">
                                                <a href="<?php echo bloginfo( 'url' ) . "/profile_category/" . $termschild[ $key ]->slug; ?>"><?php echo $termschild[ $key ]->name; ?></a>
                                            </div>
										<?php } ?>
                                        <span class="text_count"><em>All</em> <?php echo $count_all_sub ?> Subcategories
                                        <i class="fa fa-angle-down"></i>
                                    </span>
                                    </div>
								<?php } ?>
                            </div>
                        </div>

						<?php $i2 ++;
						$colc2 --;
						if ( ( $i2 == $coln2 ) && ( $colc2 != 0 ) ) { ?>
                    </div>
                    <div class="col-sm-6">
						<?php
						$i2 = 0;
						} ?>

						<?php } ?>
                    </div>
                </div>
			<?php endif; ?>
        </div>
    </div>
<?php get_footer();