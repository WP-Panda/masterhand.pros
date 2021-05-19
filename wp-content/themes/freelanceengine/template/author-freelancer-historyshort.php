<?php
	/**
	 * Template part for user bid history block
	 * # This template is loaded in page-profile.php , author.php
	 *
	 * @since   v1.0
	 * @package EngineTheme
	 */
?>
<?php
	global $user_bids, $wp_query;
	$author_id = get_query_var( 'author' );
	$is_author = is_author();
	add_filter( 'posts_orderby', 'fre_reset_order_by_project_status' );
	add_filter( 'posts_where', 'fre_filter_where_bid' );

	$query_args = [
		'post_status'         => [ 'complete' ],
		'post_type'           => BID,
		'author'              => $author_id,
		'accepted'            => 1,
		'filter_work_history' => '',
		'is_author'           => $is_author
	];
	query_posts( $query_args );
	$bid_posts = $wp_query->found_posts;

	global $wp_query, $ae_post_factory;
	$author_id = get_query_var( 'author' );

	$post_object = $ae_post_factory->get( BID );
?>
    <div class="fre-projects_list">
        <div class="freelance-education-title"><?php echo __( 'Projects', ET_DOMAIN ); ?></div>
        <ul>
			<?php
				$postdata = [];
				if ( have_posts() ):
					while ( have_posts() ) {
						the_post();
						$convert    = $post_object->convert( $post, 'thumbnail' );
						$postdata[] = $convert;
						get_template_part( 'template/author-freelancer-historyshort', 'item' );
					}
				endif;
			?>
        </ul>
    </div>
<?php remove_filter( 'posts_where', 'fre_filter_where_bid' );
	remove_filter( 'posts_orderby', 'fre_reset_order_by_project_status' );