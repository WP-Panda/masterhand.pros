<?php
/**
 * Template Name: WPP Submit Project
 */

global $user_ID, $ae_post_factory, $option_for_project;
if ( empty( $user_ID )  ) {
	wp_safe_redirect( home_url(), 301 );
}

get_header();

$user_localtion = getLocation( $user_ID );
$user_country   = isset( $user_localtion['country']['name'] );
//$disable_plan   = ae_get_option( 'disable_plan', false );// check disable payment plan or not
premium_options_json();
?>
    <div class="fre-page-wrapper step-post-package">

		<?php wpp_get_template_part( 'wpp/templates/universal/page-title' ); ?>

        <div class="fre-page-section">
            <div class="container">
                <div class="page-post-project-wrap" id="post-place">

					<?php

					//if ( empty( $disable_plan ) ) {
					//	wpp_get_template_part( 'template/post-project-step1' );
					//}

					wpp_get_template_part( 'template/post-project-step3' );

					wpp_get_template_part( 'template/post-project-step4' );

					?>

                </div>
            </div>
        </div>
    </div>
<?php
get_footer();