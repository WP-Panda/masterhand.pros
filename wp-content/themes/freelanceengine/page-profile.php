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

	$ae_users    = AE_Users::get_instance();
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

	include $_SERVER[ 'DOCUMENT_ROOT' ] . '/dbConfig.php';
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
			$style = 'style="background-image: url(' . $img_url[ 0 ] . '); background-repeat: no-repeat; background-size: 100% 100%;"';
		}
	}

	// Тут надо прямо разбираться!!!!!
	$visualFlag = getValueByProperty( $user_status, 'visual_flag' );

	if ( $visualFlag ) {
		$visualFlagNumber = get_user_meta( $user_ID, 'visual_flag', true );
	}

	$visualFlagNumber = $visualFlagNumber ?? 0;
	if ( ! empty( $_GET[ 'adminhash' ] ) ) {
		if ( is_array( $user_meta ) && $user_meta[ 'hash' ] == $_GET[ 'adminhash' ] && ! empty( $user_meta[ 'newemail' ] ) ) {
			wp_update_user( [
				'ID'         => $user_ID,
				'user_email' => $user_meta[ 'newemail' ]
			] );
			delete_user_meta( $user_ID, 'adminhash' );
		}
		echo "<script> window.location.href = '" . et_get_page_link( "profile" ) . "'</script>";
	} elseif ( ! empty( $_GET[ 'dismiss' ] ) && 'new_email' == $_GET[ 'dismiss' ] ) {
		delete_user_meta( $user_ID, 'adminhash' );
		echo "<script> window.location.href = '" . et_get_page_link( "profile" ) . "'</script>";
	}

	$is_company = get_user_meta( $user_ID, 'is_company', true );


	$referral_code   = get_referral_code_by_user( $user_ID );
	$count_referrals = get_count_referrals( $user_ID );
	$referrals       = get_list_referrals( 'all', $user_ID );
	$sponsor_name    = get_sponsor( $user_ID );


	$data_args = [
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
		'personal_cover'            => ''
	];

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

						$linkurl  = $user_data->author_url;

						$linkref  = $_SERVER[ "HTTP_HOST" ] . '/register/?code=' . $referral_code;

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

	/**
	 * Проост мета
	 */
	$post_meras = [
		'user_available'        => "on",
		"total_projects_worked" => "20",
		"email_skill"           => "1",
		"installmentPlan"       => "1",
		"pro_status"            => "3",
		"address"               => "",
		"avatar"                => "",
		"post_count"            => "",
		"comment_count"         => "",
		"et_featured"           => "",
		"et_professional_title" => "",
		"hour_rate"             => "14",
		"et_experience"         => "10",
		"et_receive_mail"       => "",
		"currency"              => "",
		"country"               => "12",
		"linkedin"              => "www.linkedin.com/abc",
		"telegram"              => "@abc",
		"whatsapp"              => "+354567892345",
		"viber"                 => "+354567892345",
		"facebook"              => "www.facebook.com/abc",
		"skype"                 => "abc.abc",
		"wechat"                => "+354567892345",
		"work_experience"       => [
			's:219:"a:8:{s:5:"title";s:8:"Mechanic";s:8:"subtitle";s:7:"PL Auto";s:6:"m_from";s:2:"01";s:6:"y_from";s:4:"2016";s:4:"m_to";s:2:"03";s:4:"y_to";s:4:"2017";s:17:"currently_working";s:0:"";s:7:"content";s:15:"Did auto repair";}";',
			's:248:"a:8:{s:5:"title";s:15:"Senior Mechanic";s:8:"subtitle";s:9:"DF Garage";s:6:"m_from";s:2:"06";s:6:"y_from";s:4:"2017";s:4:"m_to";s:0:"";s:4:"y_to";s:0:"";s:17:"currently_working";a:1:{i:0;s:1:"1";}s:7:"content";s:29:"Supervisor of repair division";}";',
		],
		"certification"         => [
			'a:7:{s:5:"title";s:25:"Engine Repair Certificat ";s:8:"subtitle";s:16:"Polytech College";s:6:"m_from";s:2:"06";s:6:"y_from";s:4:"2015";s:4:"m_to";s:2:"03";s:4:"y_to";s:4:"2016";s:7:"content";s:28:"Silver Medal of Achievements";}',
			's:228:"a:7:{s:5:"title";s:25:"Window Repair Certificate";s:8:"subtitle";s:9:"FT Center";s:6:"m_from";s:2:"04";s:6:"y_from";s:4:"2017";s:4:"m_to";s:2:"07";s:4:"y_to";s:4:"2017";s:7:"content";s:36:"Learned window repair best practices";}";',
		],
		"education"             => ':7:{s:5:"title";s:22:"Diploma in Engineering";s:8:"subtitle";s:25:"Harvard College of Trades";s:6:"m_from";s:2:"07";s:6:"y_from";s:4:"2014";s:4:"m_to";s:2:"06";s:4:"y_to";s:4:"2016";s:7:"content";s:29:"Graduated with flying colours";}',
		"state"                 => " 162",
		"city"                  => " 469",
		"document_list"         => [ "16553" ]
	];

	/**
	 * User мета
	 */
	$user_metas = [
		"nickname"                                      => "prodd",
		"first_name"                                    => "Pro",
		"last_name"                                     => "DD",
		"description"                                   => "",
		"rich_editing"                                  => "true",
		"syntax_highlighting"                           => "true",
		"comment_shortcuts"                             => "false",
		"admin_color"                                   => "fresh",
		"use_ssl"                                       => "0",
		"show_admin_bar_front"                          => "true",
		"locale"                                        => "",
		"wp_capabilities"                               => 'a:1:{s:10:"freelancer";b:1;}',
		"wp_user_level"                                 => "0",
		"country"                                       => "12",
		"state"                                         => " 162",
		"city"                                          => " 469",
		"register_status"                               => "confirm",
		"dismissed_wp_pointers"                         => "",
		"user_available"                                => "on",
		"wfls-last-login"                               => "1622474171",
		"user_profile_id"                               => "5605",
		"et_avatar"                                     => "5607",
		"et_avatar_url"                                 => "https://masterhand.pros/wp-content/uploads/2020/02/cropped-beardmanface-150x150.jpg",
		"paypal"                                        => "proa@masterhand.pro",
		"user_phone"                                    => "5878903719",
		"ihs-country-code"                              => "+1",
		"safe_deals_count"                              => "12",
		"fre_new_notify"                                => "0",
		"googleplus"                                    => "",
		"aioseop_notice_time_set_review_plugin_cta"     => "1590553184",
		"aioseop_notice_display_time_review_plugin_cta" => "1591762783",
		"currency"                                      => "AUD",
		"ae_member_current_order"                       => 'a:2:{s:8:"pro_plan";i:9345;s:14:"review_payment";i:13274;}',
		"ae_prm_latest_time_receive_email"              => "1621865737",
		"ae_private_message_reply_latest"               => "1623251681",
		"visual_flag"                                   => "3",
		"total_projects_worked"                         => "49",
		"_wpp_skills"                                   => 'a:3:{i:0;i:11;i:1;i:8;i:2;s:1:"9";}',
		"session_tokens"                                => 'a:1:{s:64:"27b0b40cda19e42fef4ac1e4e85fa57846c6a827af06a7d6ed339fa46b7c8df5";a:4:{s:10:"expiration";i:1625343182;s:2:"ip";s:9:"127.0.0.1";s:2:"ua";s:114:"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36";s:5:"login";i:1624133582;}}',
	];