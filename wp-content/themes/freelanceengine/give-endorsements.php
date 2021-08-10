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
/**
 * Template Name: Member Profile Page
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
//convert current user
$ae_users  = AE_Users::get_instance();
$user_data = $ae_users->convert( $current_user->data );
$user_role = ae_user_role( $current_user->ID );
//convert current profile
$post_object = $ae_post_factory->get( PROFILE );

$profile_id      = get_user_meta( $user_ID, 'user_profile_id', true );
$user_phone_code = get_user_meta( $user_ID, 'ihs-country-code', true );
$user_phone      = get_user_meta( $user_ID, 'user_phone', true );

$profile = [];
if ( $profile_id ) {
	$profile_post = get_post( $profile_id );
	if ( $profile_post && ! is_wp_error( $profile_post ) ) {
		$profile = $post_object->convert( $profile_post );
	}
}

$isFreelancer = ( ae_user_role( $current_user->ID ) == FREELANCER ) ? 1 : 0;

//get profile skills
$current_skills             = get_the_terms( $profile, 'skill' );
$current_profile_categories = get_the_terms( $profile, 'profile_category' );
//define variables:
$skills         = isset( $profile->tax_input['skill'] ) ? $profile->tax_input['skill'] : [];
$job_title      = isset( $profile->et_professional_title ) ? $profile->et_professional_title : '';
$hour_rate      = isset( $profile->hour_rate ) ? $profile->hour_rate : '';
$currency       = isset( $profile->currency ) ? $profile->currency : '';
$experience     = isset( $profile->et_experience ) ? $profile->et_experience : '';
$hour_rate      = isset( $profile->hour_rate ) ? $profile->hour_rate : '';
$about          = isset( $profile->post_content ) ? $profile->post_content : '';
$display_name   = $user_data->display_name;
$user_available = isset( $user_data->user_available ) && $user_data->user_available == "on" ? 'checked' : '';
//isset( $profile->tax_input['country'][0] ) ? $profile->tax_input['country'][0]->name : '';
$category = isset( $profile->tax_input['project_category'][0] ) ? $profile->tax_input['project_category'][0]->slug : '';

//new start
include $_SERVER['DOCUMENT_ROOT'] . '/dbConfig.php';
$location = getLocation( $user_ID );
//var_dump($location);
//for email
$user_confirm_email = get_user_meta( $user_ID, 'register_status', true );
//new end

get_header();
// Handle email change requests
$user_meta = get_user_meta( $user_ID, 'adminhash', true );


if ( ! empty( $_GET['adminhash'] ) ) {
	if ( is_array( $user_meta ) && $user_meta['hash'] == $_GET['adminhash'] && ! empty( $user_meta['newemail'] ) ) {
		wp_update_user( [
			'ID'         => $user_ID,
			'user_email' => $user_meta['newemail']
		] );
		delete_user_meta( $user_ID, 'adminhash' );
	}
	echo "<script> window.location.href = '" . et_get_page_link( "profile" ) . "'</script>";
} elseif ( ! empty( $_GET['dismiss'] ) && 'new_email' == $_GET['dismiss'] ) {
	delete_user_meta( $user_ID, 'adminhash' );
	echo "<script> window.location.href = '" . et_get_page_link( "profile" ) . "'</script>";
}

$role_template = 'employer';

$projects_worked = get_post_meta( $profile_id, 'total_projects_worked', true );
$project_posted  = fre_count_user_posts_by_type( $user_ID, 'project', '"publish","complete","close","disputing","disputed", "archive" ', true );
$hire_freelancer = fre_count_hire_freelancer( $user_ID );

$currency = ae_get_option( 'currency', [
	'align' => 'left',
	'code'  => 'USD',
	'icon'  => '$'
] );

$user_status = get_user_pro_status( $user_ID );

$personal_cover = getValueByProperty( $user_status, 'personal_cover' );

if ( $personal_cover ) {
	$img_url = get_user_meta( $user_ID, 'cover_url' );
	$style   = '';
	if ( $img_url ) {
		$style = 'style="background-image: url(' . $img_url[0] . '); background-repeat: no-repeat; background-size: 100% 100%;"';
	}
} else {
	$style = '';
}

$visualFlag = getValueByProperty( $user_status, 'visual_flag' );
if ( $visualFlag ) {
	$visualFlagNumber = get_user_meta( $user_ID, 'visual_flag', true );
}


$referral_code   = get_referral_code_by_user( $user_ID );
$count_referrals = get_count_referrals( $user_ID );
$referrals       = get_list_referrals( 'all', $user_ID );
$sponsor_name    = get_sponsor( $user_ID );

?>

    <div class="fre-page-wrapper give-endorsments list-profile-wrapper" <?php echo $style ?>>
        <div class="fre-page-title">
            <div class="container">
                <h1 class="page_t">
					<?php _e( 'SAFEPAY DEALS', ET_DOMAIN ) ?>
                </h1>
            </div>
        </div>
        <div class="fre-page-section">
            <div class="profile-endorsements">
                <div class="container">
					<?php
					global $user_ID, $wpdb;
					$metas               = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM $wpdb->posts WHERE post_author = %s", $user_ID ) );
					$professional_search = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'professional_id' AND meta_value = %s", $user_ID ) );

					$professionals = [];
					$post_title    = [];

					foreach ( $professional_search as $post ) {
						$posts_data      = $wpdb->get_results( $wpdb->prepare( "SELECT post_author, post_title FROM $wpdb->posts WHERE ID = %s", $post->post_id ) );
						$professionals[] = $posts_data[0]->post_author;
						$post_title[]    = $posts_data[0]->post_title;
					}

					foreach ( $metas as $meta ) {
						if ( get_post_meta( $meta->ID, 'professional_id', true ) != '' ) {
							$professionals[] = get_post_meta( $meta->ID, 'professional_id', true );
							$post_title[]    = $meta->post_title;
						}
					}

					$result = [];

					foreach ( $professionals as $i => $k ) {
						$result[ $k ][] = $post_title[ $i ];
					}

					@array_walk( $result, create_function( '&$v', '$v = (count($v) == 1)? array_pop($v): $v;' ) );
					$professionals = array_unique( $professionals ); ?>

					<?php if ( $professionals ) { ?>
                        <div class="table-header">
                            <div class="row">
                                <div class="col-sm-6 col-xs-5"><?php _e( 'Name & Project', ET_DOMAIN ) ?></div>
                                <div class="col-sm-3 col-xs-2"><?php _e( 'Deals', ET_DOMAIN ) ?></div>
                                <div class="col-sm-3 col-xs-5 text-center"><?php _e( 'Status', ET_DOMAIN ) ?></div>
                            </div>
                        </div>

                        <div class="fre-profile-box page-referrals_list">
							<?php $prof_ids = implode( ',', $professionals );
							$query          = new WP_Query( [
								'post_type'      => 'fre_profile',
								'author'         => $prof_ids,
								'posts_per_page' => 1,
								'orderby'        => 'date',
								'order'          => 'desc'
							] );
							while ( $query->have_posts() ) {
								$query->the_post();
								get_template_part( 'template/endors', 'item' );
							}
							if ( $query->max_num_pages > 1 ) { ?>
                                <script>
                                    var ajaxurl = '<?php echo site_url() ?>/wp-admin/admin-ajax.php';
                                    var true_posts = '<?php echo serialize( $query->query_vars ); ?>';
                                    var current_page = <?php echo ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; ?>;
                                    var max_pages = '<?php echo $query->max_num_pages; ?>';

                                </script>
                                <a id="true_loadmore"
                                   class="fre-submit-btn endors_loadmore"><?php echo __( 'Show more', ET_DOMAIN ); ?></a>
							<?php } ?>
                        </div>
						<?php wp_reset_query();
					} else {
						_e( 'No SafePay Deals yet', ET_DOMAIN );
					}
					wpp_get_template_part( 'template/wpp/referer-list', [ 'user_ID' => $user_ID ] )
					?>
                </div>
            </div>
        </div>
    </div>


    <!-- CURRENT PROFILE -->
<?php if ( $profile_id && $profile_post && ! is_wp_error( $profile_post ) ) { ?>
    <script type="data/json" id="current_profile">
        <?php echo json_encode( $profile ) ?>
    </script>
<?php }
if ( ! empty( $current_skills ) ) { ?>
    <script type="data/json" id="current_skills">
        <?php echo json_encode( $current_skills ) ?>
    </script>
<?php }
get_footer();