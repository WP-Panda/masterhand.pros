<?php
/**
 * Template Name: Page Post Project
 */
global $user_ID;
$project = ! empty( $_GET['id'] ) ? get_post( $_GET['id'] ) : false;
if ( empty( $user_ID ) || empty( $project ) || ( ! empty( $project ) && (int) $project->post_author !== (int) $user_ID ) ) {
	wp_safe_redirect( home_url(), 301 );
}

/*wp_update_post( array(
	'ID'          => $_GET['id'],
	'post_status' => 'draft'
) );*/

get_header();
?>
    <div class="fre-page-wrapper step-post-package edit-options">

        <div class="fre-page-title">
            <div class="container">
                <h1><?php the_title(); ?></h1>
            </div>
        </div>

        <div class="fre-page-section">
            <div class="container">
                <div class="page-post-project-wrap" id="post-place">
					<?php

					global $ae_post_factory, $option_for_project;
					$ae_pack = $ae_post_factory->get( 'pack' );
					$packs   = $ae_pack->fetch( 'pack' );

					$user_status = get_user_pro_status( $user_ID );
					$options     = getOptionsEmployer();

					$pro_em_functions = [];
					foreach ( $packs as $key => $package ) {
						$key_option = array_search( $package->sku, $options );
						if ( $key_option !== false ) {
							unset( $packs[ $key ] );
							$pro_em_functions[ $key_option ] = [
								'sku'   => $options[ $key_option ],
								'price' => getValueByProperty( $user_status, $package->sku )
							];
						}
					}
					ksort( $pro_em_functions );

					echo '<script type="data/json" id="pro_em_functions">' . json_encode( $pro_em_functions ) . '</script>';

					// check disable payment plan or not
					//$disable_plan = ae_get_option( 'disable_plan', false );

					get_template_part( 'template/option-project', 'step3' );

					//	if ( ! $disable_plan ) {
					get_template_part( 'template/post-project', 'step4' );
					//	}
					?>

                </div>
            </div>
        </div>
    </div>
<?php
get_footer();