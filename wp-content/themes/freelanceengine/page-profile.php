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
global $wp_query, $ae_post_factory, $post, $current_user, $wpp_fr;

$ae_users = AE_Users::get_instance();

$user_data   = $ae_users->convert( $current_user->data );
$user_role   = ae_user_role( $wpp_fr->user );
$post_object = $ae_post_factory->get( PROFILE );
$usered_data = new Wpp_En_User();
$data        = $usered_data->get_user_data();

$profile_id = $data->profile_id ?? '';


$profile = [];
if ( $profile_id ) {
	$profile_post = get_post( $profile_id );

	if ( $profile_post && ! is_wp_error( $profile_post ) ) {
		$profile = $post_object->convert( $profile_post );
	}
}

$current_profile_categories = get_the_terms( $profile, 'project_category' );

$job_title          = $profile->et_professional_title ?? '';
$hour_rate          = $profile->hour_rate ?? '';
$currency           = $profile->currency ?? '';
$experience         = $profile->et_experience ?? '';
$hour_rate          = $profile->hour_rate ?? '';
$about              = $profile->post_content ?? '';
$display_name       = $user_data->display_name;
$user_available     = isset( $user_data->user_available ) && $user_data->user_available == "on" ? 'checked' : '';
$user_phone_code    = $data->ihs_country_code ?? '';
$user_phone         = $data->user_phone ?? '';
$user_confirm_email = $data->register_status ?? '';

include $_SERVER['DOCUMENT_ROOT'] . '/dbConfig.php';
$location = getLocation( $user_ID );

get_header();
// Handle email change requests
$user_meta = get_user_meta( $user_ID, 'adminhash', true );

$currency = ae_get_option( 'currency', [
	'align' => 'left',
	'code'  => 'USD',
	'icon'  => '$'
] );

$projects_worked = get_post_meta( $profile_id, 'total_projects_worked', true );
$project_posted  = fre_count_user_posts_by_type( $user_ID, 'project', '"publish","complete","close","disputing","disputed", "archive" ', true );
$hire_freelancer = fre_count_hire_freelancer( $user_ID );
$role_template   = 'employer';
$user_status     = get_user_pro_status( $user_ID );
if ( $user_status ) {
	$user_pro_expire           = get_user_pro_expire( $user_ID );
	$user_pro_expire           = strtotime( $user_pro_expire );
	$user_pro_expire_normalize = date( 'F d, Y', $user_pro_expire );
	$user_pro_expire           = date( 'M-d-Y', $user_pro_expire );
	$user_pro_name             = get_user_pro_name( $user_ID );
}

$personal_cover = getValueByProperty( $user_status, 'personal_cover' );

if ( $personal_cover ) {
	$img_url = get_user_meta( $user_ID, 'cover_url' );
	if ( $img_url ) {
		$style = 'style="background-image: url(' . $img_url[0] . '); background-repeat: no-repeat; background-size: 100% 100%;"';
	}
}

// Тут надо прямо разбираться!!!!!
$visualFlag = getValueByProperty( $user_status, 'visual_flag' );

if ( $visualFlag ) {
	$visualFlagNumber = get_user_meta( $user_ID, 'visual_flag', true );
}

$visualFlagNumber = $visualFlagNumber ?? 0;
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

$is_company = get_user_meta( $user_ID, 'is_company', true );


$referral_code   = get_referral_code_by_user( $user_ID );
$count_referrals = get_count_referrals( $user_ID );
$referrals       = get_list_referrals( 'all', $user_ID );
$sponsor_name    = get_sponsor( $user_ID );


$data_args = [
	'wpp_data'                  => $data,
	'user_data'                 => $user_data,
	'display_name'              => $display_name,
	'user_status'               => $user_status,
	'user_pro_expire'           => $user_pro_expire,
	'user_ID'                   => $user_ID,
	'visualFlagNumber'          => $visualFlagNumber,
	'location'                  => $location,
	'profile'                   => $profile,
	'projects_worked'           => $projects_worked,
	'project_posted'            => $project_posted,
	'hire_freelancer'           => $hire_freelancer,
	'referral_code'             => $referral_code,
	'user_pro_name'             => $user_pro_name,
	'count_referrals'           => $count_referrals,
	'sponsor_name'              => $sponsor_name,
	'user_pro_expire_normalize' => $user_pro_expire_normalize,
	'user_phone_code'           => $user_phone_code,
	'user_phone'                => $user_phone,
	'user_confirm_email'        => $user_confirm_email,
	'profile_id'                => $profile_id,
	'hour_rate'                 => $hour_rate,
	'is_company'                => $is_company,
	'about'                     => $about,
	'visualFlag'                => $visualFlag,
	'personal_cover'            => '',
	'experience'                => $experience
];


$referal = get_referral( $user_ID );


$referal = wp_list_pluck($referal, 'user_id');
$referal[] = get_sponsor_id( $user_ID );
$key = array_search($user_ID,$referal);
if( isset($key)) {
    unset($referal[$key]);
}
wpp_dump($referal);

$employer_previous_project_query = new WP_Query( [
	'post_status'      => [ 'complete', 'disputed' ],
	'is_author'        => true,
	'post_type'        => PROJECT,
	'author'           => $user_ID,
	'suppress_filters' => true,
	'orderby'          => 'date',
	'order'            => 'DESC'
] );

?>

    <div class="fre-page-wrapper list-profile-wrapper" <?php echo $style ?? ''; ?>>
		<?php wpp_get_template_part( 'wpp/templates/universal/page-h1', [ 'title' => __( 'My Profile', ET_DOMAIN ) ] ); ?>

        <div class="fre-page-section">
            <div class="container">
                <div class="profile-freelance-wrap">
					<?php

					wpp_get_template_part( 'wpp/templates/profile/notis/confirm-email', $data_args );
					wpp_get_template_part( 'wpp/templates/profile/notis/first-login', $data_args );
					wpp_get_template_part( 'wpp/templates/profile/profile-box', $data_args );

					$linkname = '';

					$linkurl = $user_data->author_url;

					$linkref = $_SERVER["HTTP_HOST"] . '/register/?code=' . $referral_code;

					if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) {
						ADDTOANY_SHARE_SAVE_KIT( compact( 'linkname', 'linkurl' ) );
					} ?>

                    <script>
                        var a2a_config = a2a_config || {};
                        a2a_config.templates = a2a_config.templates || {};
                        var link = <?php echo json_encode( $linkref ); ?>

                            a2a_config.templates.email = {
                                subject: "Check this out: ${title}",
                                body: "Click the link by registry with referral code:\n" + link
                            };
                    </script>


					<?php wpp_get_template_part( 'wpp/templates/profile/skills-box', $data_args );

					if ( fre_share_role() || wpp_fre_is_freelancer() ) { ?>
                        <div class="profile-freelance-available hidden">
                            <div class="fre-input-field">
                                <input type="checkbox" <?php /*echo $user_available; */ ?>
                                       class="js-switch user-available"
                                       name="user_available"/>
                                <span class="user-status-text text <?php /*echo $user_available ? 'yes' : 'no' */ ?>"></span>
                            </div>
                            <div class="fre-input-field">
                                <label for="fre-switch-user-available" class="fre-switch">
                                    <input id="fre-switch-user-available" type="checkbox" checked>
                                    <div class="fre-switch-slider"></div>
                                </label>
                            </div>
                        </div>
					<?php }
					wpp_get_template_part( 'wpp/templates/profile/tabs/tabs-head', $data_args );
					wpp_get_template_part( 'wpp/templates/profile/tabs/rating', $data_args );
					wpp_get_template_part( 'wpp/templates/profile/tabs/review', $data_args );
					wpp_get_template_part( 'wpp/templates/profile/tabs/setting', $data_args );
					?>
                </div>

            </div>
        </div>
    </div>


<?php if ( $profile_id && $profile_post && ! is_wp_error( $profile_post ) ) { ?>
    <script type="data/json" id="current_profile"> <?php echo json_encode( $profile ) ?></script>
<?php }
get_footer();