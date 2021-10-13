<?php
/**
 * The Template for displaying all single posts
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */

get_header();
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object = $ae_post_factory->get( PROJECT );
$convert     = $post_object->convert( $post );

$bid_Id = get_post_meta( $post->ID, 'accepted', true );
$safe   = get_post_meta( $bid_Id, 'fre_bid_order', true );
//$safe = !empty($safe);
$bid_author = get_post( $bid_Id )->post_author;

/*do_action( 'wpp_dump',wpp_get_user_rating( $bid_author));
do_action( 'wpp_dump', $safe );
do_action( 'wpp_dump', $bid_author );
do_action( 'wpp_dump', get_user_pro_status( $bid_author ) );*/

if ( have_posts() ) {
	the_post();
	global $post;


	$bid_query = new WP_Query( [
		'post_type'      => 'bid',
		'post_parent'    => $post->ID,
		'post_status'    => 'any',
		'posts_per_page' => - 1,
		//'paged' => get_query_var('paged') ?: 1
	] );

	if ( $bid_query->have_posts() ) {
		$post_object = $ae_post_factory->get( BID );

		while ( $bid_query->have_posts() ) {
			$bid_query->the_post();

		}
	} else {
	}
	wp_reset_query();
	?>

    <div class="fre-page-wrapper">
        <div class="container">
            <div class="fre-project-detail-wrap">
				<?php
				if ( isset( $_REQUEST['workspace'] ) && $_REQUEST['workspace'] ) {
					get_template_part( 'template/project-workspace', 'info' );
					get_template_part( 'template/project-workspace', 'content' );
				} else {
					if ( isset( $_REQUEST['dispute'] ) && $_REQUEST['dispute'] ) {
						get_template_part( 'template/project', 'report' );
					} else {
						get_template_part( 'template/single-project', 'info' );
						get_template_part( 'template/single-project', 'content' );
						get_template_part( 'template/single-project', 'bidding' );
					}
				}
				echo '<script type="data/json" id="project_data">' . json_encode( $convert ) . '</script>';
				?>
            </div>
        </div>
    </div>
<?php }
get_footer();