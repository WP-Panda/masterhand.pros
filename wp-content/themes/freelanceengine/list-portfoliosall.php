<?php
/**
 * Use for page author.php and page-profile.php
 */
global $wp_query, $ae_post_factory, $post;
$current_user = wp_get_current_user();

$wp_query->query = array_merge( $wp_query->query, [ 'posts_per_page' => 6 ] );

$post_object = $ae_post_factory->get( 'portfolio' );
$is_edit     = false;
if ( is_author() ) {
	$author_id = get_query_var( 'author' );
} else {
	$author_id = get_current_user_id();
	$is_edit   = true;
}

$query_args = [
	'post_status' => 'publish',
	'post_type'   => PORTFOLIO,
	'author'      => $author_id,
	'is_edit'     => $is_edit,
	'meta_query'  => [
		[
			'key'     => 'client',
			'value'   => '',
			'compare' => 'NOT EXISTS'
		]
	]
];

query_posts( $query_args );
if ( have_posts() or $is_edit ) { ?>
    <div class="profile-freelance-portfolio colaps">
        <div class="row">
            <div class="<?php echo $is_edit ? 'col-sm-6' : '' ?> col-xs-12">
                <div data-toggle="collapse" data-target="#portfolio_cat-0" class="freelance-portfolio-title">
					<?php _e( 'Portfolio', ET_DOMAIN ) ?>
					<?php echo $item->name; ?><i class="fa fa-angle-down"></i></div>
            </div>
        </div>

		<?php if ( have_posts() and ! $is_edit ) { ?>
            <ul id="portfolio_cat-0" class="collapse freelance-portfolio-list row">
				<?php
				$postdata = [];
				while ( have_posts() ) {
					the_post();
					$convert    = $post_object->convert( $post, 'thumbnail' );
					$postdata[] = $convert;
					get_template_part( 'template/portfolio', 'item' );
				}
				?>
            </ul>
		<?php } ?>

		<?php
		if ( ! empty( $postdata ) && $wp_query->max_num_pages > 1 ) {
			/**
			 * render post data for js
			 */
			echo '<script type="data/json" class="postdata portfolios-data" >' . json_encode( $postdata ) . '</script>';

			echo '<div class="freelance-portfolio-loadmore">';
			ae_pagination( $wp_query, get_query_var( 'paged' ), 'load_more', 'View more' );
			echo '</div>';
		} ?>
    </div>
<?php }