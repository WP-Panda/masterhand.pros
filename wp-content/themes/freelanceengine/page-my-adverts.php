<?php
global $wpdb, $wp_query, $ae_post_factory, $post, $current_user, $user_ID;

if ( ! is_user_logged_in() ) {
	wp_redirect( et_get_page_link( 'login', [ 'ae_redirect_url' => get_permalink( $post->ID ) ] ) );
}

$user_role = ae_user_role( $user_ID );
if ( $user_role !== FREELANCER ) {
	wp_redirect( home_url( '/' ) );
}

define( 'NO_RESULT', __( '<span class="project-no-results">There are no activities yet.</span>', WPP_TEXT_DOMAIN ) );

if ( ! defined( 'FRE_ADVERT' ) ) {
	define( 'FRE_ADVERT', 'advert' );
}

get_header();
?>

    <div class="fre-page-wrapper my-adverts">
        <div class="fre-page-title">
            <div class="container">
                <h1>
					<?php _e( 'My Special Offers', WPP_TEXT_DOMAIN ) ?>
                </h1>
            </div>
        </div>


        <div class="fre-page-section">
            <div class="container">
				<?php if ( get_user_pro_status( $user_ID ) != PRO_BASIC_STATUS_FREELANCER ) { ?>
                    <div class="btn-wrap">
                        <a class="fre-submit-btn btn-right"
                           href="<?php echo et_get_page_link( "create-advert" ) ?>"><?php _e( 'Add new', WPP_TEXT_DOMAIN ); ?></a>
                    </div>
				<?php } else { ?>
                    <p>Activate Account Pro for Create Ad</p>
                    <a href="/pro" class="btn-submit fre-submit-btn go-to-pro-account"><? _e( 'Activate' ) ?></a>
				<? } ?>
				<?php
				$freelancer_current_project_query = new WP_Query( [
					'post_status'    => [
						'publish',
						'archive'
					],
					'post_type'      => FRE_ADVERT,
					'author'         => $current_user->ID,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'posts_per_page' => 3,
					'paged'          => get_query_var( 'paged' ) ?: 1
				] );
				$post_object                      = $ae_post_factory->get( FRE_ADVERT );
				$no_result_current                = '';
				?>
                <div class="current-freelance-project">
                    <div class="adverts_list">
						<?php
						$postdata = [];
						if ( $freelancer_current_project_query->have_posts() ) {
						while ( $freelancer_current_project_query->have_posts() ) {
							$freelancer_current_project_query->the_post();
							$postdata[] = $post;
							$status     = $post->post_status;
							?>
                            <div class="adverts_list_wp">
                                <div class="adverts_t <?php if ( $status == 'archive' ) {
									echo 'project-status-archive';
								} ?>" data-title="<?= $post->post_title; ?>">
									<?php if ( $status == 'publish' ) { ?>
                                        <a href="<?php echo get_permalink() ?>"><?php echo $post->post_title; ?></a>
                                        <span class="status"><? _e( 'Active' ) ?></span>
									<?php } else {
										echo $post->post_title;
									} ?>
                                </div>
                                <div class="adverts_txt <?php if ( $status == 'archive' ) {
									echo 'project-status-archive';
								} ?>">
									<?php if ( strlen( $post->post_content ) > 50 ) {
										echo substr( strip_tags( $post->post_content ), 0, 49 ) . '...';
									} else {
										echo $post->post_content;
									}
									?>
                                </div>
                                <div class="btn-wrap">
									<? if ( $status == 'publish' ) { ?>
                                        <a class="fre-submit-btn btn-left"
                                           href="<?= get_permalink(); ?>?post_edit"><?php _e( 'Edit' ); ?></a>
                                        <a class="advert-action archive cancel-btn"
                                           data-id="<?= $post->ID; ?>"><?= __( 'Archive', WPP_TEXT_DOMAIN ); ?></a>
										<?
									}
									// else if ( $status == 'archive' ) {
									//												echo '<a class="advert-action" data-action="remove" data-id="' . $post->ID . '">' . __( 'Remove', WPP_TEXT_DOMAIN) . '</a>';
									//											}
									?>
                                </div>
                            </div>
						<?php } ?>
                            <script type="data/json"
                                    id="current_project_post_data"><?php echo json_encode( $postdata ); ?></script>
						<?php } else {
							$no_result_current = NO_RESULT;
						}
						?>
                    </div>
					<?php
					if ( $no_result_current != '' ) {
						echo $no_result_current;
					}
					?>
                </div>
                <div class="fre-paginations paginations-wrapper">
					<?php ae_pagination( $freelancer_current_project_query, get_query_var( 'paged' ) ); ?>
                </div>
				<?php
				wp_reset_postdata();
				wp_reset_query();
				?>
            </div>
        </div>
    </div>
<?php
wp_enqueue_script( '', '/wp-content/themes/freelanceengine/js/ad-freelancer.js', [], false, true );
get_footer();