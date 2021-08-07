<?php
global $wp_query, $ae_post_factory, $post, $user_ID, $show_bid_info;

$post_object = $ae_post_factory->get( PROJECT );
$project     = $post_object->current_post;

//$number_bids = (int) get_number_bids( get_the_ID() ); // 1.8.5
add_filter( 'posts_join', 'fre_join_status_user_bid' );
add_filter( 'posts_orderby', 'fre_order_by_bid_status' );

$bid_query = new WP_Query( [
	'post_type'      => BID,
	'post_parent'    => get_the_ID(),
	'post_status'    => [
		'publish',
		'complete',
		'accept',
		'unaccept',
		'disputing',
		'disputed',
		'archive',
		'hide'
	],
	'posts_per_page' => - 1,
	//		'paged' => get_query_var('paged') ?: 1
] );
//wpp_dump($bid_query);
remove_filter( 'posts_join', 'fre_join_status_user_bid' );
remove_filter( 'posts_orderby', 'fre_order_by_bid_status' );
$bid_data = [];

/*wpp_dump( get_user_meta( get_current_user_id(), 'register_status', true ) );*/

?>

<div id="project-detail-bidding" class="project-detail-box no-padding">
    <div class="freelancer-bidding-head">
        <div class="row">
            <div class="col-md-10 col-sm-9 col-xs-12">
                <div class="row">
                    <div class="col-md-8 col-sm-12 col-xs-12">
                        <div class="col-free-bidding"><?php printf( __( 'Professional bidding (%s)', ET_DOMAIN ), $bid_query->found_posts ); ?></div>
                    </div>
                    <div class="col-lg-4 col-md-4 visible-md visible-lg">
                        <div class="col-free-reputation"><?php _e( 'Reputation', ET_DOMAIN ); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 visible-md visible-lg">
                <div class="col-free-bid"><?php _e( 'Bid', ET_DOMAIN ); ?></div>
            </div>
        </div>
    </div>

    <div class="freelancer-bidding">
		<?php

		if ( $bid_query->have_posts() ) {
			$post_object = $ae_post_factory->get( BID );

			while ( $bid_query->have_posts() ) {
				$bid_query->the_post();
				$convert = $post_object->convert( $post );
				get_template_part( 'template/bidding', 'item' );
			}
		} else {
			get_template_part( 'template/bid', 'not-item' );
		}
		?>
    </div>
    <!--	<div class="fre-paginations paginations-wrapper">-->
    <!--		<div class="paginations">-->
    <!--			--><?php
	//			ae_pagination( $bid_query, get_query_var( 'paged' ) ); ?>
    <!--		</div>-->
    <!--	</div>-->
	<?php
	wp_reset_postdata();
	wp_reset_query();
	?>
</div>