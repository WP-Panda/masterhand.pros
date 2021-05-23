<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	/**
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

	// получение страны
	//$country     = get_post_meta( get_the_ID(), 'company_in_country', true );
	$paged       = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$data_params = $_GET;
	if ( empty( $_GET ) ) {
		$GET = 'all';
	}

	$country = $_GET[ 'country' ];

	$company_data = wpp_company_query( $country, $paged, $data_params );
?>
    <div class="fre-page-wrapper section-archive-company wpp-data-div" data-id="<?php echo get_the_ID(); ?>">

		<?php wpp_get_template_part( 'wpp/templates/universal/light-title', [ 'title' => __( 'Companies', 'wpp' ) ] ); ?>

        <div class="fre-page-section">
            <div class="container">
                <div class="page-profile-list-wrap">
                    <div class="fre-profile-list-wrap">
						<?php wpp_get_template_part( 'wpp/templates/filter/filter-companies' ) ?>
                        <div class="fre-company-list-box">

                            <div class="fre-profile-result-sort">
								<?php wpp_get_template_part( 'wpp/templates/universal/found-indicator', $company_data[ 'found_labels' ] ) ?>
                            </div>

                            <ul class="project-list-container company-list-container">
								<?php wpp_get_template_part( 'wpp/templates/companies/company-list', [ 'companies' => $company_data[ 'companies' ] ] ); ?>
                            </ul>

							<?php get_template_part( 'template-js/wpp/modal', 'get-quote' ); ?>
                        </div>
                    </div>
                    <div class="fre-paginations paginations-wrapper">
						<?php
							$paginate_args = [
								'pages'    => ceil( $company_data[ 'found_posts_num' ] / COMPANY_PER_PAGE ),
								'ajax_wpp' => true
							];

							wpp_get_template_part( 'wpp/templates/universal/paginate', $paginate_args ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php get_footer();