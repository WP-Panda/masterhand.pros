<?php
/**
 * Template Name: FAQ-help
 *
 * @package    WordPress
 * @subpackage Twenty_Fourteen
 * @since      Twenty Fourteen 1.0
 */

get_header();
?>
    <div class="fre-page-wrapper">


    <div class="help-bg" style="background-image: url(<?php if ( get_field( "banner_picture" ) ) {
		the_field( "banner_picture" );
	} ?>);">
        <div class="container">
            <div class="help-bg__content">
				<?php if ( get_field( "banner_heading" ) ) { ?>
                    <h1 class="help-bg__title"><?php echo the_field( "banner_heading" ); ?></h1>
				<?php } ?>
				<?php if ( get_field( "description_banner" ) ) { ?>
                    <div class="help-bg__description">
						<?php echo the_field( "description_banner" ); ?>
                    </div>
				<?php } ?>
            </div>
        </div>
    </div>

    <div class="how_bl">
        <div class="help-post hidden-xs">
            <div class="container">
				<?php
				$top_faq_title = get_field( 'top_faq_title' );
				if ( $top_faq_title ) { ?>
                    <div class="help-post__block_title"><?php echo $top_faq_title; ?></div>
				<?php } ?>
				<?php
				$featured_posts = get_field( 'top_faq_list' );
				if ( $featured_posts ): ?>
                    <div class="help-post__row">
						<?php
						$k = 1;
						foreach ( $featured_posts as $post ):

							// Setup this post for WP functions (variable must be named $post).
							setup_postdata( $post ); ?>
                            <div class="help-post__item">
                                <div class="help-post__image">
									<?php if ( has_post_thumbnail() ) { ?>
                                        <img src="<?php the_post_thumbnail_url(); ?>"
                                             alt="post-<?php echo $k ?>">
									<?php } else { ?>
                                        <img src="<?php echo get_template_directory_uri() . '/img/noimg.png' ?>"
                                             alt="no_img">
									<?php } ?>
                                </div>
                                <a class="help-post__title"
                                   href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								<?php if ( has_excerpt() ) { ?>
                                    <div class="help-post__excerpt">
										<?php echo get_the_excerpt(); ?>
                                    </div>
								<?php } ?>
                            </div>

							<?php $k ++; endforeach; ?>
                    </div>
					<?php
					// Reset the global post object so that the rest of the page works correctly.
					wp_reset_postdata(); ?>
				<?php endif; ?>
            </div>
        </div>
        <div class="help-catalog">
            <div class="container">
				<?php
				$rows = get_field( 'tabs_item' );
				if ( $rows ){ ?>
                <div class="help-tabs">
                    <ul class="help-tab__headers" id="Tabs" role="tablist">
						<?php
						$i = 0;
						foreach ( $rows as $row ) { ?>
							<?php if ( $i == 0 ) { ?>
                                <li class="nav-item help-tab__header_item active">
							<?php } else { ?>
                                <li class="nav-item help-tab__header_item">
							<?php }
						$i ++ ?>
                            <a class="nav-link help-tab__header_link" data-toggle="tab"
                               href="#tab-<?php echo $row['tab_id'] ?>" role="tab">
								<?php echo $row['tab_header']; ?>
                            </a>
                            </li>
						<?php } ?>
                    </ul>
                </div>
                <div class="tab-content help-tab__content">
					<?php
					$j = 0;
					foreach ( $rows

					as $row ) { ?>
				<?php if ( $j == 0 ){ ?>
                    <div id="tab-<?php echo $row['tab_id'] ?>" class="tab-pane help-tab__pane fade active in">
						<?php } else { ?>
                        <div id="tab-<?php echo $row['tab_id'] ?>" class="tab-pane help-tab__pane fade">
							<?php }
							$j ++ ?>
							<?php
							$category_list = $row['category_list'];
							$taxonomy      = esc_html( $category_list->taxonomy );
							$terms_id      = $category_list->term_id;
							$terms         = get_terms( [
								'taxonomy'   => $taxonomy,
								'hide_empty' => 0,
								'parent'     => $terms_id
							] );
							$colc          = count( $terms );
							$coln          = round( ( count( $terms ) ) / 3 );
							$i             = 0;
							?>


                            <div class="help-tab__content_list">
								<?php foreach ( $terms as $term ) {
									$termid       = $term->term_id;
									$term_link    = get_term_link( $termid );
									$termschild   = get_terms( [
										'taxonomy'   => $taxonomy,
										'hide_empty' => 0,
										'parent'     => $termid
									] );
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
												'terms'    => $term->name
											]
										]
									];
									$partnersList = new WP_Query( $args );
									?>
                                    <div class="help-tab__item">
                                        <img class="help-tab__icon"
                                             src="<?php the_field( 'catic', $taxonomy . '_' . $termid ); ?>">
                                        <div class="help-tab__catalog  <?php if ( $termschild && ! is_wp_error( $termschild ) ) : ?> faa-parent <?php endif; ?> animated-hover">
                                            <a class="help-tab__catalog_title"
                                               href="<?php echo $term_link ?>"><?php echo $term->name; ?></a>
                                            <!--                                            <div class="profs-cat_sublist">-->
                                            <ul class="help-tab__catalog_list">
												<?php
												//loop through query
												if ( $partnersList->have_posts() ) {
													while ( $partnersList->have_posts() ) {
														$partnersList->the_post();
														?>
                                                        <li class="help-tab__catalog_item">
                                                            <a class="help-tab__catalog_list"
                                                               href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                        </li>

														<?php
													}
												}

												wp_reset_postdata();

												?>
                                            </ul>
                                        </div>
                                    </div>
								<?php }
								$i ++;
								$colc --; ?>

                            </div>
                        </div>
						<?php } ?>
                    </div>
					<?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php get_footer();