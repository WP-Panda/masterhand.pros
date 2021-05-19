<?php
	/**
	 * Use for page author.php and page-profile.php
	 */
	global $wp_query, $ae_post_factory, $post;
	$current_user = wp_get_current_user();

	$wp_query->query = array_merge( $wp_query->query, [ 'posts_per_page' => 6 ] );

	$post_object = $ae_post_factory->get( 'portfolio' );
	//$is_edit = false;
	if ( is_author() ) {
		$author_id = get_query_var( 'author' );
	} else {
		$author_id = get_current_user_id();
		//    $is_edit = true;
	}

	$query_args = [
		// 'post_parent' => $convert->ID,
		'posts_per_page' => 6,
		'post_status'    => 'publish',
		'post_type'      => PORTFOLIO,
		'author'         => $author_id,
		//    'is_edit' => $is_edit,
		'meta_query'     => [
			[
				'key'   => 'client',
				'value' => 1,
			]
		]
	];

	query_posts( $query_args );
	//echo '<pre>' . var_dump($current_user) . '</pre>';

	//new2
	//if (function_exists('count_portfolio')) {
	//    $cp = count_portfolio($current_user);
	//    // $cp = 2;
	//}


	//new2

	if ( have_posts() ) {
		?>
        <div class="profile-freelance-portfolio">
            <div class="row">

                <div class="col-sm-6 col-xs-12">
                    <div class="freelance-portfolio-title"><?php _e( 'My clients:', ET_DOMAIN ) ?></div>
                </div>
            </div>

            <ul class="freelance-portfolio-list row">
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

			<?php
				if ( ! empty( $postdata ) && $wp_query->max_num_pages > 1 ) {
					/**
					 * render post data for js
					 */
					echo '<script type="data/json" class="postdata portfolios-data" >' . json_encode( $postdata ) . '</script>';

					echo '<div class="freelance-portfolio-loadmore">';
					ae_pagination( $wp_query, get_query_var( 'paged' ), 'load_more', 'View more' );
					echo '</div>';
				}
			?>
        </div>
	<?php }