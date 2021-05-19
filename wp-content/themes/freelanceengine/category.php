<?php
	/**
	 * The category template file
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
	get_header();

	$queried_object = get_queried_object();
	$taxonomy       = $queried_object->taxonomy;
	$term_id        = $queried_object->term_id;
	$homeid         = get_option( 'page_on_front' );
?>
    <div class="fre-page-wrapper">
        <div class="container">

            <div class="fre-blog">
				<?php if ( $term_id == 1 ) {
					$blog_category1 = get_field( 'blog_category_1', $homeid );
					$blog_category2 = get_field( 'blog_category_2', $homeid );
					$blog_category3 = get_field( 'blog_category_3', $homeid );
					$blog_category4 = get_field( 'blog_category_4', $homeid );
					?>
                    <div class="cats-list">
						<?php
							$taxonomy = 'category';
							$terms    = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'parent' => 1 ] );
							if ( $terms && ! is_wp_error( $terms ) ) :?>
                                <div class="row">
									<?php foreach ( $terms as $term ) {
										$termid = $term->term_id; ?>
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="profs-cat-list_t text-center">
                                                <a href="<?php echo get_term_link( $term->slug, $taxonomy ); ?>"
                                                   style="background:#fff url(<?php the_field( 'catic', $taxonomy . '_' . $termid ); ?>) 40px center no-repeat;">
													<?php echo $term->name; ?></a>
                                            </div>
                                        </div>
									<?php } ?>
                                </div>
							<?php endif; ?>
                    </div>

                    <div class="fre-blog-list-sticky">
                        <div class="row">
							<?php $sticky = get_option( 'sticky_posts' ); ?>
							<?php $query = new WP_Query( [ 'posts_per_page'      => 1,
							                               'ignore_sticky_posts' => 1,
							                               'post_type'           => 'post',
							                               'post_status'         => 'publish',
							                               'post__in'            => $sticky,
							                               'orderby'             => 'date',
							                               'order'               => 'desc'
							] ); ?>
                            <div class="col-sm-7 col-md-8 col-lg-8 col-xs-12 fre-blog-list-sticky_main">
								<?php while ( $query->have_posts() ) {
									$query->the_post();
									get_template_part( 'template/blog', 'sticky' );
								} ?>
                            </div>
							<?php wp_reset_query(); ?>
                            <div class="col-sm-5 col-md-4 col-lg-4 col-xs-12">
								<?php $query = new WP_Query( [ 'posts_per_page'      => 3,
								                               'offset'              => 1,
								                               'ignore_sticky_posts' => 1,
								                               'post_type'           => 'post',
								                               'post_status'         => 'publish',
								                               'post__in'            => $sticky,
								                               'orderby'             => 'date',
								                               'order'               => 'desc'
								] ); ?>
								<?php while ( $query->have_posts() ) {
									$query->the_post();
									get_template_part( 'template/blog', 'stickynoimg' );
								} ?>
								<?php wp_reset_query(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="fre-blog-fst_bl">
						<?php $query1 = new WP_Query( [ 'post_type'      => 'post',
						                                'post_status'    => 'publish',
						                                'posts_per_page' => 10,
						                                'category__in'   => $blog_category1
						] ); ?>
						<? if ( $query1 && ! is_wp_error( $query1 ) ) {
							$term1 = get_term( $blog_category1, 'category' ); ?>
                            <div class="profs-cat_t"><span><?php echo $term1->name; ?></span></div>
                            <div class="fre-blog-list owl-carousel">
								<?php while ( $query1->have_posts() ) {
									$query1->the_post();
									get_template_part( 'template/blog', 'item' );
								} ?>
                            </div>
						<?php }
							wp_reset_query(); ?>
                    </div>
                    <div class="fre-blog-snd_bl">
						<?php $query2 = new WP_Query( [ 'post_type'      => 'post',
						                                'post_status'    => 'publish',
						                                'posts_per_page' => 10,
						                                'cat'            => $blog_category2
						] );
							$count    = $query2->post_count;
							if ( $query2 && ! is_wp_error( $query2 ) ) {
								$term2 = get_term( $blog_category2, 'category' ); ?>
                                <div class="profs-cat_t"><span><?php echo $term2->name; ?></span></div>
                                <div class="fre-blog-list owl-carousel hidden-xs">
                                    <div class="fre-blog-list-item row">
										<?php $i = 0;
											while ( $query2->have_posts() ) {
											$query2->the_post();
											get_template_part( 'template/blog', 'item2' );
											$i ++;
											if ( $i == 6 ) { ?>
                                    </div>
                                    <div class="fre-blog-list-item row">
										<?php $i = 0;
											}
											} ?>
                                    </div>
                                </div>
                                <div class="fre-blog-list owl-carousel hidden-sm">
                                    <div class="fre-blog-list-item row">
										<?php $i = 0;
											while ( $query2->have_posts() ) {
											$query2->the_post();
											get_template_part( 'template/blog', 'item2' );
											$i ++;
											if ( $i == 3 ) { ?>
                                    </div>
                                    <div class="fre-blog-list-item row">
										<?php $i = 0;
											}
											} ?>
                                    </div>
                                </div>
							<?php }
							wp_reset_query(); ?>
                    </div>
                    <div class="fre-blog-snd_bl">
						<?php $query3 = new WP_Query( [ 'post_type'      => 'post',
						                                'post_status'    => 'publish',
						                                'posts_per_page' => 10,
						                                'cat'            => $blog_category3
						] ); ?>
						<? if ( $query3 && ! is_wp_error( $query3 ) ) {
							$term3 = get_term( $blog_category3, 'category' ); ?>
                            <div class="profs-cat_t"><span><?php echo $term3->name; ?></span></div>

                            <div class="fre-blog-list owl-carousel hidden-xs">
                                <div class="fre-blog-list-item row">
									<?php $i = 0;
										while ( $query3->have_posts() ) {
										$query3->the_post();
										get_template_part( 'template/blog', 'item2' );
										$i ++;
										if ( $i == 6 ) { ?>
                                </div>
                                <div class="fre-blog-list-item row">
									<?php $i = 0;
										}
										} ?>
                                </div>
                            </div>
                            <div class="fre-blog-list owl-carousel hidden-sm">
                                <div class="fre-blog-list-item row">
									<?php $i = 0;
										while ( $query3->have_posts() ) {
										$query3->the_post();
										get_template_part( 'template/blog', 'item2' );
										$i ++;
										if ( $i == 3 ) { ?>
                                </div>
                                <div class="fre-blog-list-item row">
									<?php $i = 0;
										}
										} ?>
                                </div>
                            </div>
						<?php }
							wp_reset_query(); ?>
                    </div>
                    <div class="fre-blog-thd_bl">
						<?php $query4 = new WP_Query( [ 'post_type'      => 'post',
						                                'post_status'    => 'publish',
						                                'posts_per_page' => 10,
						                                'cat'            => $blog_category4
						] ); ?>
						<? if ( $query4 && ! is_wp_error( $query4 ) ) {
							$term4 = get_term( $blog_category4, 'category' ); ?>
                            <div class="profs-cat_t"><span><?php echo $term4->name; ?></span></div>

                            <div class="fre-blog-list owl-carousel hidden-xs">
                                <div class="fre-blog-list-item row">
									<?php $i = 0;
										while ( $query4->have_posts() ) {
										$query4->the_post();
										get_template_part( 'template/blog', 'item3' );
										$i ++;
										if ( $i == 6 ) { ?>
                                </div>
                                <div class="fre-blog-list-item row">
									<?php $i = 0;
										}
										} ?>
                                </div>
                            </div>
                            <div class="fre-blog-list owl-carousel hidden-sm">
                                <div class="fre-blog-list-item row">
									<?php $i = 0;
										while ( $query4->have_posts() ) {
										$query4->the_post();
										get_template_part( 'template/blog', 'item3' );
										$i ++;
										if ( $i == 2 ) { ?>
                                </div>
                                <div class="fre-blog-list-item row">
									<?php $i = 0;
										}
										} ?>
                                </div>
                            </div>
						<?php }
							wp_reset_query(); ?>

                        <!-- Mailster subscribtion form -->
                        <div class="fre-blog-subscribe-form mailster-subscribe__block">
                            <div class='fre-blog-subscribe-form_title'><?php echo __( 'Subscribe', ET_DOMAIN ) ?></div>
                            <div class="emaillist">
								<?php echo mailster_form( 3 ); ?>
                            </div>
                        </div>

                    </div>
				<?php } else { ?>
                    <div class="sub-category">
                        <div class="cats-list">
							<?php
								$term_base = get_term( 1, $taxonomy );
								$terms     = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'parent' => 1 ] );
								if ( $terms && ! is_wp_error( $terms ) ) :?>
                                    <div class="row">
                                        <div class="col-sm-3 col-xs-12">
                                            <div class="profs-cat-list_t text-center">
                                                <a href="<?php echo get_term_link( 1, $taxonomy ); ?>">
                                                    <i class="blog-icon"
                                                       style="background: rgba(255,255,255,0.1) url(<?php the_field( 'catic', $taxonomy . '_1' ); ?>) center no-repeat;"></i>
                                                    <span><?php echo $term_base->name; ?></span></a>
                                            </div>
                                        </div>
										<?php foreach ( $terms as $term ) {
											$termid = $term->term_id; ?>
                                            <div class="col-sm-3 col-xs-6">
												<?php if ( is_category( $termid ) ): ?>
                                                    <div class="profs-cat-list_t text-center">
                                                        <a href="<?php echo get_term_link( $term->slug, $taxonomy ); ?>"
                                                           class="active">
															<?php echo $term->name; ?></a>
                                                    </div>
												<? else: ?>
                                                    <div class="profs-cat-list_t text-center">
                                                        <a href="<?php echo get_term_link( $term->slug, $taxonomy ); ?>">
															<?php echo $term->name; ?></a>
                                                    </div>
												<? endif; ?>
                                            </div>
										<?php } ?>
                                    </div>
								<?php endif; ?>
                        </div>
                        <div class="profs-cat_t"><span><?php single_cat_title(); ?></span></div>
                        <div class="fre-blog-list-sticky">
                            <div class="row">
								<?php $sticky = get_option( 'sticky_posts' );
									$query    = new WP_Query( [ 'cat'                 => $term_id,
									                            'posts_per_page'      => 2,
									                            'ignore_sticky_posts' => 1,
									                            'post_status'         => 'publish',
									                            'post__in'            => $sticky,
									                            'orderby'             => 'date',
									                            'order'               => 'desc'
									] );
									while ( $query->have_posts() ) {
										$query->the_post();
										$notin[] = get_the_ID(); ?>
                                        <div class="col-sm-6 col-xs-12 fre-blog-item_main">
											<?php get_template_part( 'template/blog', 'stickynocat' ); ?>
                                        </div>
									<?php }
									wp_reset_query(); ?>
                            </div>
                            <div class="row">
								<?php $query = new WP_Query( [ 'cat'                 => $term_id,
								                               'posts_per_page'      => 3,
								                               'offset'              => 2,
								                               'ignore_sticky_posts' => 1,
								                               'post_status'         => 'publish',
								                               'post__in'            => $sticky,
								                               'orderby'             => 'date',
								                               'order'               => 'desc'
								] );
									while ( $query->have_posts() ) {
										$query->the_post();
										$notin[] = get_the_ID(); ?>
                                        <div class="col-sm-4 hidden-xs fre-blog-item_submain">
											<?php get_template_part( 'template/blog', 'stickydate' ); ?>
                                        </div>
									<?php }
									wp_reset_query(); ?>
                            </div>
                        </div>

						<? /* old subscription plugin
            <div class="fre-blog-subscribe-form">
                <?php
                $current_category = get_queried_object_id();
                $categories = [
                        '9875' => '[email-subscribers-form id="1"]',
                        '9955' => '[email-subscribers-form id="2"]',
                        '9956' => '[email-subscribers-form id="3"]',
                        '9874' => '[email-subscribers-form id="4"]',
                ];
                $category_name = get_cat_name( $current_category );

                echo "<div class='fre-blog-subscribe-form_title'>Subscribe to category ". $category_name . "</div>";
                echo do_shortcode($categories[$current_category])
                ?>
            </div>
            */ ?>

                        <div class="profs-cat_t"><span><?php echo __( 'Latest articles', ET_DOMAIN ); ?></span></div>
                        <div class="fre-blog-snd_bl">
							<?php $query = new WP_Query( [ 'post_type'           => 'post',
							                               'post_status'         => 'publish',
							                               'posts_per_page'      => 4,
							                               'cat'                 => $term_id,
							                               'ignore_sticky_posts' => 1,
							                               'orderby'             => 'date',
							                               'order'               => 'desc',
							                               'post__not_in'        => $notin
							] );
							?>
                            <div class="fre-blog-list">
                                <div class="fre-blog-list-item row">
									<?php $i = 0;
										while ( $query->have_posts() ) {
											$query->the_post();
											get_template_part( 'template/blog', 'item4' );
										} ?>
									<?php if ( $query->max_num_pages > 1 ) : ?>
                                        <script>
                                            var ajaxurl = '<?php echo site_url() ?>/wp-admin/admin-ajax.php';
                                            var true_posts = '<?php echo serialize( $query->query_vars ); ?>';
                                            var current_page = <?php echo ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; ?>;
                                            var max_pages = '<?php echo $query->max_num_pages; ?>';

                                        </script>
                                        <a id="true_loadmore"
                                           class="fre-submit-btn blog_loadmore"><?php echo __( 'Show more', ET_DOMAIN ); ?></a>
									<?php endif; ?>
                                </div>
                            </div>
							<?php wp_reset_query(); ?>
                        </div>

                        <!-- Mailster subscribtion form -->
                        <div class="fre-blog-subscribe-form mailster-subscribe__block">
                            <div class='fre-blog-subscribe-form_title'>Subscribe</div>
                            <div class="emaillist">
								<?php echo mailster_form( 3 ); ?>
                            </div>
                        </div>

                    </div>
				<?php } ?>
            </div>
        </div>
    </div>
<?php get_footer();
