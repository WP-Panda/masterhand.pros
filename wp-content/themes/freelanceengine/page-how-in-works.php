<?php
	/**
	 * Template Name: Page How in Works
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
	get_header();
	global $post;
	$pid      = get_the_ID();
	$slug     = get_post_field( 'post_name', get_post() );
	$parid    = wp_get_post_parent_id( $pid );
	$children = get_pages( [ 'child_of' => $pid ] );
	if ( $parid == 0 ) {
		$search = $pid;
	} else {
		$search = $parid;
	}
	$pages = get_posts( [
		'post_type'   => 'page',
		'post_status' => 'publish',
		'order'       => 'ASC',
		'post_parent' => $search
	] );
?>
    <div class="fre-page-wrapper">
        <div class="how_bl">
            <div class="top-page visible-lg visible-md">
				<?php the_post_thumbnail( 'original' ); ?>
            </div>
            <div class="container">
                <div class="cats-list">
					<?php if ( ! empty( $pages ) ) { ?>
                        <div class="row">
							<?php foreach ( $pages as $pages__value ) { ?>
                                <div class="col-sm-4 col-xs-12">
                                    <div class="profs-cat-list_t text-center <?php if ( $pages__value->ID == $pid ) {
										echo 'active-menu-item';
									} ?>">
                                        <a href="<?php echo get_permalink( $pages__value->ID ); ?>"><?php echo get_the_title( $pages__value->ID ); ?></a>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
					<?php } ?>
                </div>
            </div>

			<?php if ( is_page() && ( $post->post_parent || count( $children ) > 0 ) ) { ?>
				<?php if ( have_rows( 'block-list', get_page_by_path( $slug . '/clients' ) ) ) { ?>
                    <div class="how_bl-list">
						<?php while ( have_rows( 'block-list', get_page_by_path( $slug . '/clients' ) ) ): the_row(); ?>
                            <div class="how_bl-list-item">
								<?php if ( get_sub_field( 'block-image' ) ) { ?>
                                    <div class="how_bl-bg hidden-xs">
                                        <img src="<?php the_sub_field( 'block-image' ); ?>"
                                             alt="<?php strip_tags( the_sub_field( 'block-header' ) ); ?>"/>
                                    </div>
								<?php } ?>
                                <div class="container">
                                    <div class="how_bl-list-item-wp">
                                        <div class="profs-cat_t"><span><?php the_sub_field( 'block-header' ); ?></span>
                                        </div>
										<?php the_sub_field( 'block-text' ); ?>
                                    </div>
                                </div>
                            </div>
						<?php endwhile; ?>
                    </div>
				<?php } ?>
			<?php } else { ?>
				<?php if ( have_rows( 'block-list', get_page_by_path( $slug . '/clients' ) ) ) { ?>
                    <div class="how_bl-list">
						<?php while ( have_rows( 'block-list' ) ): the_row(); ?>
                            <div class="how_bl-list-item">
								<?php if ( get_sub_field( 'block-image' ) ) { ?>
                                    <div class="how_bl-bg hidden-xs">
                                        <img src="<?php the_sub_field( 'block-image' ); ?>"
                                             alt="<?php strip_tags( the_sub_field( 'block-header' ) ); ?>"/>
                                    </div>
								<?php } ?>
                                <div class="container">
                                    <div class="how_bl-list-item-wp">
                                        <div class="profs-cat_t"><span><?php the_sub_field( 'block-header' ); ?></span>
                                        </div>
										<?php the_sub_field( 'block-text' ); ?>
                                    </div>
                                </div>
                            </div>
						<?php endwhile; ?>
                    </div>
				<?php }
			} ?>

			<?php while ( have_rows( 'block-list2' ) ): the_row(); ?>
                <div class="container">
                    <div class="faq_bl">
						<?php if ( get_sub_field( 'block-header' ) ) { ?>
                            <div class="fre-page-title">
                                <div class="profs-cat_t">
                                    <span><?php the_sub_field( 'block-header' ); ?></span>
                                </div>
                            </div>
						<?php } ?>
                        <div class="questions-list">
							<?php $q = 0;
								while ( have_rows( 'question-list' ) ): the_row(); ?>
                                    <div class="questions-list_item">
                                        <div class="questions-list_t" data-toggle="collapse"
                                             data-target="#bl-<?php echo $n2; ?>-q-<?php echo $q; ?>">
											<?php the_sub_field( 'question-header' ); ?><i class="fa-angle-down fa"></i>
                                        </div>
                                        <div id="bl-<?php echo $n2; ?>-q-<?php echo $q; ?>" class="collapse">
											<?php the_sub_field( 'question-text' ); ?>
                                        </div>
                                    </div>
									<?php $q ++; endwhile; ?>
                        </div>
                    </div>
                </div>
			<?php endwhile; ?>

            <div class="container">
                <div class="service-text">
					<?php echo $content; ?>
                </div>
				<?php if ( ( $slug == 'how-it-works' ) || ( $slug == 'client' ) || ( $slug == 'clients' ) ) {
					get_template_part( 'template/get', 'started' );
				} else if ( ( $slug == 'professional' ) || ( $slug == 'professionals' ) ) {
					get_template_part( 'template/get', 'started2' );
				} ?>
            </div>
        </div>

		<?php if ( $slug == 'how-it-works' ) { ?>
            <script>
                $('.how_bl .cats-list .row>div:first-child>.profs-cat-list_t').addClass('active-menu-item');
            </script>
		<?php } ?>
    </div>
<?php get_footer();