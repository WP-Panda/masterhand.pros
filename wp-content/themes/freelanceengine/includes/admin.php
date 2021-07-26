<?php
define( 'ADMIN_PATH', TEMPLATEPATH . '/admin' );

if ( ! class_exists( 'AE_Base' ) ) {
	return;
}

/**
 * Handle admin features
 * Adding admin menus
 */
class ET_Admin extends AE_Base {
	function __construct() {

		/**
		 * admin setup
		 */
		$this->add_action( 'init', 'admin_setup' );

		/**
		 * update first options
		 */
		$this->add_action( 'after_switch_theme', 'update_first_time' );

		//declare ajax classes
		new AE_CategoryAjax( new AE_Category( [
			'taxonomy' => 'project_category'
		] ) );
		new AE_CategoryAjax( new AE_Category( [
			'taxonomy' => 'project_type'
		] ) );

		$this->add_ajax( 'ae-reset-option', 'reset_option' );

		/* User Actions */
		$this->add_action( 'ae_upload_image', 'ae_upload_image', 10, 2 );

		/**
		 * set default options
		 */
		$options = AE_Options::get_instance();
		if ( ! $options->init ) {
			$options->reset( $this->get_default_options() );
		}

		// kick subscriber user
		if ( ! current_user_can( 'manage_options' ) && basename( $_SERVER['SCRIPT_FILENAME'] ) != 'admin-ajax.php' ) {

			// wp_redirect( home_url(  ) );
			// exit;

		}
		$this->add_filter( 'ae_setup_wizard_template', 'fre_setup_wizard_template' );
		$this->add_filter( 'notice_after_installing_theme', 'fre_notice_after_installing_theme' );
		$this->add_action( 'ae_insert_sample_data_success', 'fre_after_insert_sample_data' );
	}

	/**
	 * update user avatar
	 */
	public function ae_upload_image( $attach_data, $data ) {

		if ( isset( $data["method"] ) && $data["method"] == "change_avatar" ) {
			if ( ! isset( $data['author'] ) ) {
				return;
			}

			$ae_users = AE_Users::get_instance();

			//update user avatar
			$user = $ae_users->update( [
				'ID'            => $data['author'],
				'et_avatar'     => $attach_data['attach_id'],
				'et_avatar_url' => $attach_data['thumbnail'][0]
			] );
		}
		switch ( $data ) {
			case 'site_logo_black':
			case 'site_logo_white':
				$options = AE_Options::get_instance();

				// save this setting to theme options
				$options->$data = $attach_data;
				if ( $data == 'site_logo_black' ) {
					$options->site_logo = $attach_data;
				}
				$options->save();

				break;

			default:
				// code...
				break;
		}
	}

	/**
	 * ajax function reset option
	 */
	function reset_option() {

		$option_name     = $_REQUEST['option_name'];
		$default_options = $this->get_default_options();

		if ( isset( $default_options[ $option_name ] ) ) {
			$options               = AE_Options::get_instance();
			$options->$option_name = $default_options[ $option_name ];
			$options->save();
			wp_send_json( [
				'msg' => $default_options[ $option_name ]
			] );
		}
	}

	function get_template_default_options( $name ) {
		$default_options = $this->get_default_options();
		if ( isset( $default_options[ $name ] ) ) {
			return $default_options[ $name ];
		}
	}

	function admin_custom_css() {
		?>
        <style type="text/css">
            .custom-icon {
                margin: 10px;
            }

            .custom-icon input {
                width: 80%;
            }
        </style>
		<?php
	}

	/**
	 * retrieve site default options
	 */
	function get_default_options() {

		return apply_filters( 'fre_default_setting_option', [
			'blogname'        => get_option( 'blogname' ),
			'blogdescription' => get_option( 'blogdescription' ),
			'copyright'       => '<span class="enginethemes"> <a href=http://www.enginethemes.com/themes/freelanceengine/ >FreelanceEngine</a> - Powered by WordPress </span>',

			'project_demonstration'         => [
				'home_page'    => 'The best way to <br/>  find a professional',
				'list_project' => 'A Million of Project.<br/> Find it out!'
			],
			'profile_demonstration'         => [
				'home_page'    => 'Need a job? <br/> Tell us your story',
				'list_profile' => 'Need a job? <br/> Tell us your story'
			],

			// default forgot passmail
			'forgotpass_mail_template'      => '<p>Hello [display_name],</p><p>You have just sent a request to recover the password associated with your account in [blogname]. If you did not make this request, please ignore this email; otherwise, click the link below to create your new password:</p><p>[activate_url]</p><p>Regards,<br />[blogname]</p>',

			// default register mail template
			//new start
			'register_mail_template'        => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Please click the link below to confirm your email address.</p><p>[confirm_link]</p><p>Thank you and welcome to [blogname].</p>',
			//new end

			// default register social mail template
			'register_social_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li><li>Change password: [active_link]</li></ol><p>Thank you and welcome to [blogname].</p>',


			// default confirm mail template
			'password_mail_template'        => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li><li>Password: [password]</li></ol><p>Thank you and welcome to [blogname].</p>',

			//  default reset pass mail template
			'resetpass_mail_template'       => "<p>Hello [display_name],</p><p>You have successfully changed your password. Click this link &nbsp;[site_url] to login to your [blogname]'s account.</p><p>Sincerely,<br />[blogname]</p>",

			// default confirm mail template
			'confirm_mail_template'         => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Please click the link below to confirm your email address.</p><p>[confirm_link]</p><p>Thank you and welcome to [blogname].</p>',

			'confirm_new_mail_template' => '<p>Hi [display_name],</p>
                <p>Your email [user_email] has just been changed on [user_new_email].</p>
                <p>Please click the link below to confirm your email address:</p>
                <p>[confirm_link]</p>
                <p>Otherwise, you are free to ignore this email.&nbsp;
                Regards,&nbsp;
                All at [blogname]&nbsp;
                [site_url]</p>',

			// default confirmed mail template
			'confirmed_mail_template'   => "<p>Hi [display_name],</p><p>Your email address has been successfully confirmed.</p><p>Thank you and welcome to [blogname].</p>",

			//  default inbox mail template
			'inbox_mail_template'       => "<p>Hello [display_name],</p><p>You have just received the following message from user: <a href=\"[sender_link]\">[sender]</a></p>
                                        <p>|--------------------------------------------------------------------------------------------------|</p>
                                        [message]
                                        <p>|--------------------------------------------------------------------------------------------------|</p>
                                        <p>You can answer the user by replying this email.</p><p>Sincerely,<br />[blogname]</p>",

			//  default inbox mail template
			'publish_mail_template'     => "<p>Hello [display_name],</p>
                                        <p>Your listing: [title] in [blogname] is publish.</p>
                                        <p>You can follow this link: [link] to view your listing offer.</p>
                                        <p>Sincerely,<br />[blogname]</p>",

			'archive_mail_template' => "<p>Hello [display_name],</p>
                                        <p>Your listing: [title] in [blogname] has been archived due to expiration or manual administrative action.</p>
                                        <p>If you want to continue displaying this listing in our website, please go to your dashboard at [dashboard] to renew your listing offer.</p>
                                        <p>Sincerely,<br />[blogname]</p>",

			'reject_mail_template'                      => "<p>Hello [display_name],</p>
                                        <p>Your listing: [link] in [blogname] has been rejected due to expiration or manual administrative action.</p>
                                        <p>Reasons reject: [reject_message].</p>
                                        <p>Please contact the administrators via [admin_email] for more information, or go to your dashboard at [dashboard] to edit your listing offer and post it again.</p>
                                        <p>Sincerely,<br />[blogname]</p>",
			'invite_mail_template'                      => "<p>Hello [display_name],</p>
                                        <p>You have a invitation  from  [blogname] to joint a project.</p>
                                        <p>You can view these project at link : [link]</p>
                                        <p>Sincerely,<br />[blogname]</p>",
			'bid_mail_template'                         => "<p>Hello [display_name],</p>
                                    <p>You have a new bid on the project : [title].</p>
                                    <p>Here is the freelancer's message : [message].</p>
                                    <p>You can have more details in : [link]</p>
                                    <p>Sincerely,</p>
                                    <p>[blogname]</p>",
			'complete_mail_template'                    => '<p>Hi,</p>
                                        <p>Your working project <b>[title]</b> has been marked as "Completed" by employer [employer].</p>
                                        <p>Now, you can review the project and rate for her/him in:</p>
                                        <p>[link_review]</p>
                                        <p>Regards,<br />[blogname]</p>',
			'reply_to_review_template'                  => '<p>Hi,</p>
                                        <p>Employer [employer] replied to your review on project <b>[title]</b>.</p>
                                        <p>[link_review]</p>
                                        <p>Regards,<br />[blogname]</p>',
			'options_deadline_paid'                     => '<p>Hello [display_name],</p>
                                        <p>Additional options in your project [project_name] are going to be expired in 1 day.</p>
                                        <p>If you would like to extend the options to keep up your project more visible, please follow the link [pay_link]</p>',
			'bid_accepted_template'                     => "<p>Hello [display_name],</p>
                                        <p>Your bid on the project [link] has been accepted.</p>
                                        <p>Enter the Workspace [workspace] for further discussion with the employer.</p>
                                        <p>Sincerely,</p>
                                        <p>[blogname]</p>",
			'bid_accepted_alternative_template'         => "<p>Hi,</p>
                                            <p>Employer [employer] started working on his project [link] with an alternative bidder.</p>
                                            <p>Thank you for your bid on this project.</p>
                                            <p>Regards,</p>
                                            <p>[blogname]</p>",
			'new_message_mail_template'                 => "<p>Hello [display_name],</p>
                                            <p>You have a new message on project [title]. Here is the message details:</p>
                                            <p>[message]</p>
                                            <p>You can view all message in [workspace]</p>
                                            <p>Sincerely,<br>[blogname]</p>",
			'new_payment_mail_template'                 => "<p>Hi Admin,</p>
                                            <p>User [user_name] has bought the [package_name] package on your site. Please review and confirm (if required) this payment.</p>
                                            <p>Regards,<br>[blogname]</p>",
			'cash_notification_mail'                    => "<p>Dear [display_name],</p>
                                        <p>[cash_message]</p>
                                        <p>Sincerely, <br/>[blogname].</p>",
			'ae_receipt_mail'                           => '<p>Dear [display_name],</p>
                                    <p>
                                        Thank you for your payment.<br />
                                        Here are the details of your transaction:<br />
                                        <strong>Detail</strong>: Purchase the [package_name] package. This package contains [number_of_bids] project bids.
                                    </p>
                                    <p>
                                        <strong> Customer info</strong>:<br />
                                        [display_name] <br />
                                        Email: [user_email]. <br />
                                    </p>
                                    <p>
                                        <strong> Invoice</strong> <br />
                                        Invoice No: [invoice_id]  <br />
                                        Date: [date] <br />
                                        Payment: [payment] <br />
                                        Total: [total] [currency]<br />
                                        [notify_cash]
                                    </p>
                                    <p>Sincerely,<br />[blogname]</p>',
			'ban_mail_template'                         => '<p>Hello [display_name],</p><p>You have been banned from [blogname] for reason:</p><p>[reason]</p><p>Your ban will be expired on [ban_expired]</p><p>Please contact our staff for more information</p><p>Sincerely,<br />[blogname]</p>',
			'employer_report_mail_template'             => '<p>Hello [display_name],</p><p>Project [title] you’ve worked on has a new report.&nbsp;</p><p>You can review the project in : [link]</p><p>Sincerely,</p>',
			'employer_close_mail_template'              => "<p>Hi [display_name], </p>
                                                <p>User [employer] has closed the [title] project. Please review and provide admin with detailed information and materials involved in this project.</p>
                                                <p>[link]</p>
                                                <p>If you have any questions, don't hesitate to contact me.</p>
                                                <p>Regards, <br>[blogname]</p>",
			'freelancer_report_mail_template'           => '<p>Hello [display_name],</p>
                                                <p>Your project has a report from [reporter].</p>
                                                <p>You can review the project in : [link]</p>
                                                <p>Sincerely, </br>
                                                [blogname]</p>',
			'freelancer_quit_mail_template'             => "<p>Hi [display_name], </p>
                                                <p>Freelancer [freelancer] has discontinued the [title] project. Please review and provide admin with detailed information and materials involved in this project.</p>
                                                <p>[link]</p>
                                                <p>If you have any questions, don't hesitate to contact me.</p>
                                                <p>Regards, <br>[blogname],</p>",
			'admin_report_mail_template'                => '<p>Hi [display_name], </p>
                                            <p>User [employer] has closed the [title] project on your site. Please review and arbitrate the dispute:</p>
                                            <p>[link]</p>
                                            <p>Regards, <br>[blogname]</p>',
			'admin_report_freelancer_mail_template'     => "<p>Hi [display_name], </p>
                                                        <p>Freelancer [freelancer] has discontinued the [title] project. Please review and arbitrate the dispute:</p>
                                                        <p>[link]</p>
                                                        <p>Regards, <br>[blogname]</p>",
			'fre_refund_mail_template'                  => '<p>Hello [display_name],</p>
                                            <p>The disputing project [title] has been proceed by admin. </p>
                                            <p>The payment will be transferred back to project’s owner</p>
                                            <p>You can review the project in : [link]</p>
                                            <p>Sincerely, </br>
                                            [blogname]</p>',
			'fre_execute_mail_template'                 => '<p>Hello [display_name],
                                            <p>The disputing project [title] has been proceed by admin. </p>
                                            <p>The payment will be transfer to the freelancer</p>
                                            <p>You can review the project in : [link]</p>
                                            <p>Sincerely, </br>
                                            [blogname]</p>',
			'fre_execute_to_employer_mail_template'     => '<p>Hi,</p>
                                                        <p>Your project [title] is completed and the payment has been transferred to freelancer [freelancer].</p>
                                                        <p>You can review the project in:</p>
                                                        <p>[link]</p>
                                                        <p>Regards, <br>[blogname]</p>',
			'fre_execute_to_freelancer_mail_template'   => '<p>Hi,</p>
                                                        <p>Your working project [title] is completed and the payment has been successfully transferred to you. Kindly check your account or your available balance.</p>
                                                        <p>Also, you should review the project and rate for employer [employer] in:</p>
                                                        <p>[link]</p>
                                                        <p>Regards, <br>[blogname]</p>',
			'fre_notify_employer_mail_template'         => '<p>Hi, </p>
                                                        <p>Your project [link] is completed. The payment for this project has been successfully transferred to the freelancer [freelancer].</p>
                                                        <p>Regards, <br>[blogname]</p>',
			'fre_notify_freelancer_mail_template'       => '<p>Hi, </p>
                                                            <p>Your working project [title] is completed and the payment has been successfully transferred to you. Kindly check your account or your available balance.</p>
                                                            <p>Also, you should review the project and rate for Employer [employer] in:</p>
                                                            <p>[link]</p>
                                                            <p>Regards, <br>[blogname]</p>',
			'review_for_employer_mail_template'         => '<p>Hi, </p>
                                                    <p>Freelancer [freelance] has reviewed and rated for you on the project [link].</p>
                                                    <p>Besides, you can visit the project detail tab in your profile page for detailed information.</p>
                                                    <p>[link_profile]</p>
                                                    <p>Regards, <br>[blogname]</p>',
			'bid_cancel_mail_template'                  => "<p>Hello [display_name],</p>
                                    <p>The Freelancer is canceled a bid on the project : [link].</p>
                                    <p>Sincerely,</p>
                                    <p>[blogname]</p>",
			'new_project_mail_template'                 => "<p>Hi there,</p>
                                            <p>There is a new job for you today. Hurry apply for this project [project_link] and get everything started.</p>
                                            <p>Hope you have a highly effective Day</p>",
			'approved_payment_mail_template'            => "<p>Hi [display_name],</p>
                                                <p>Congratulations! Your payment has been approved by the admin. Your [package_name] package is available to be used.<br>
                                                    Here are the details of your transaction:</p>
                                                <p>
                                                    <strong>Customer info</strong>:<br>
                                                    Name: [display_name] <br>
                                                    Email: [user_email]<br>
                                                </p>
                                                <p>
                                                    <strong>Invoice</strong> <br>
                                                    Invoice No: [invoice_id] <br>
                                                    Date: [date]. <br>
                                                    Payment: [payment] <br>
                                                    Total: [total] [currency]<br>
                                                </p>
                                                <p>Regards, <br>
                                                [blogname]</p>",
			'ae_receipt_project_mail'                   => '<p>Dear [display_name],</p>
                                        <p>
                                            Thank you for your payment.<br />
                                            Here are the details of your transaction:<br />
                                            <strong>Detail</strong>:<br />
                                                - [package_detail].<br />
                                                - Submit a project [link].
                                        </p>
                                        <p>
                                            <strong>Project options</strong>:<br />
                                                [project_options]
                                        </p>
                                        <p>
                                            <strong> Customer info</strong>:<br />
                                            Name: [display_name] <br />
                                            Email: [user_email]. <br />
                                        </p>
                                        <p>
                                            <strong> Invoice</strong> <br />
                                            Invoice No: [invoice_id]  <br />
                                            Date: [date] <br />
                                            Payment: [payment] <br />
                                            Total: [total] [currency]<br />
                                            [notify_cash]
                                        </p>
                                        <p>Sincerely,<br />[blogname]</p>',
			'ae_receipt_bid_mail'                       => '<p>Dear [display_name],</p>
                                    <p>
                                        Thank you for your payment.<br />
                                        Here are the details of your transaction:<br />
                                        <strong>Detail</strong>: Purchase the [package_name] package. This package contains [number_of_bids] project bids.
                                    </p>
                                    <p>
                                        <strong> Customer info</strong>:<br />
                                        Name: [display_name] <br />
                                        Email: [user_email]. <br />
                                    </p>
                                    <p>
                                        <strong> Invoice</strong> <br />
                                        Invoice No: [invoice_id]  <br />
                                        Date: [date] <br />
                                        Payment: [payment] <br />
                                        Total: [total] [currency]<br />
                                        [notify_cash]
                                    </p>
                                    <p>Sincerely,<br />[blogname]</p>',
			'ae_resubmitted_project_mail'               => '<p>Hi,</p>
                                            <p>Employer [display_name] has been re-submitted his previous rejected project [link]. Please review and approve this project.</p>
                                            <p>Regards,<br>[blogname]</p>',
			'fre_notify_employer_when_employer_win'     => '<p>Hi [display_name], </p>
                                                        <p>After reviewing all reasons and information provided by both parties, I decided you are the winner in the dispute occurring on the [link] project.</p>
                                                        <p>Your payment will be refunded, kindly check your either account or available balance.</p>
                                                        <p>Thanks for your continuous support and trust in us.</p>
                                                        <p>Regards, <br>[blogname]</p>',
			'fre_notify_freelancer_when_employer_win'   => "<p>Hi [display_name],</p>
                                                        <p>After reviewing all reasons and and information provided by both parties, I'm sorry to announce that employer [employer] is the winner in the dispute occurring on the [link] project.</p>
                                                        <p>The payment is refunded to the employer.</p>
                                                        <p>If you have any questions, don't hesitate to contact me.</p>
                                                        <p>Thanks for your continuous support and trust in us.</p>
                                                        <p>Regards, <br>[blogname]</p>",
			'fre_notify_employer_when_freelancer_win'   => "<p>Hi [display_name], </p>
                                                        <p>After reviewing all reasons and and information provided by both parties, I'm sorry to announce that freelancer [freelancer] is the winner in the dispute occurring on the [link] project.</p>
                                                        <p>The payment is transferred to the freelancer.</p>
                                                        <p>If you have any questions, don't hesitate to contact me.</p>
                                                        <p>Thanks for your continuous support and trust in us.</p>
                                                        <p>Regards, <br>[blogname]</p>",
			'fre_notify_freelancer_when_freelancer_win' => "<p><p>Hi [display_name], </p>
                                                        <p>After reviewing all reasons and and information provided by both parties, I decided you are the winner in the dispute occurring on [link] project.</p>
                                                        <p>Your payment is successfully transferred to you, kindly check your either account or available balance.</p>
                                                        <p>Thanks for your continuous support and trust in us.</p>
                                                        <p>Regards, <br>[blogname]</p>",
			'new_referral_template'                     => '<p>Hi,</p>
                                        <p>You have a new referral: <a href="[referral]">[referralname]</a>.</p>',

			'bay_pro_status_template'       => '<p>Hi, [display_name].</p>
                                    <p>You buy status - [status_data]</p>
                                    <p>Status delete by [expired_date]</p>
                                    <p>Regards, <br>[blogname]</p>',
			'expired_pro_status_template'   => '<p>Hi, [display_name].</p>
                                    <p>You status <b>[status_data]</b> has expired.</p>
                                    <p>Regards, <br>[blogname]</p>',
			'bay_review_template'           => '<p>Hi, [display_name].</p>
                                    <p>You buy [price_review] review in project [link]:<br>
                                    [review_detail] - [review_stars]</p>
                                    <p>Regards, <br>[blogname]</p>',
			'bay_pay_bid'                   => '<p>Hi, [display_name].</p>
                                    <p>You buy:<br>
                                    [price_bid] <br>
                                    for bid: [bid_detail]<br>
                                    in project [link]</p>
                                    <p>Regards, <br>[blogname]</p>',
			'get_quote_company_subject'     => 'Get Quote Company',
			'get_quote_company'             => '<p>[message]</p>
                                    <br>
                                    <p>Regards, [display_name]</p>',
			'request_quote_company_subject' => 'Request Quote Company',
			'request_quote_company'         => '<p>[display_name] writes to you with a proposal to evaluate the cost of your work</p>
                                    <p> and reply to email [user_email] or apply for a project on the site</p>
                                    <p>Link to the project <a href="[project_link]" target="_blank">[project_name]</a></p>
                                    <br><br>
                                    <p>Regards, [display_name]</p>',
			'notify_payment_code'           => '<p>For your project <b>[project_name]</b> was created payment code.<br>Payment code: [payment_code]</p>',
			'init'                          => 1
		] );
	}

	function update_first_time() {
		update_option( 'de_first_time_install', 1 );
		update_option( 'revslider-valid-notice', 'false' );
	}

	/**
	 * FrE setup wizard html template
	 *
	 * @param string $html
	 *
	 * @return string $html
	 * @since    1.6.2
	 * @package  void
	 * @category void
	 * @author   Tambh
	 */
	public function fre_setup_wizard_template( $html ) {
		ob_start();
		?>
        <div class="et-main-content" id="overview_settings">
            <div class="et-main-right">
                <div class="et-main-main clearfix inner-content" id="wizard-sample">

                    <div class="title font-quicksand" style="padding-top:0;">
                        <h3><?php _e( 'SAMPLE DATA', ET_DOMAIN ) ?></h3>

                        <div class="desc small"><?php _e( 'The sample data include some items from the list below: profile, project, etc.', ET_DOMAIN ) ?></div>

                        <div class="btn-language padding-top10 f-left-all"
                             style="padding-bottom:15px;height:65px;margin:0;">
							<?php
							$sample_data_op = get_option( 'option_sample_data' );
							if ( ! $sample_data_op ) {
								echo '<button class="primary-button" id="install_sample_data">' . __( "Install sample data", ET_DOMAIN ) . '</button>';
							} else {
								echo '<button class="primary-button" id="delete_sample_data">' . __( "Delete sample data", ET_DOMAIN ) . '</button>';
							}
							?>
                        </div>
                    </div>

                    <div class="desc" style="padding-top:0px;">
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" rel="noopener noreferrer"
                               href="<?php echo admin_url( 'edit.php?post_type=project' ); ?>"><?php _e( 'Project', ET_DOMAIN ) ?></a>
                            <span class="description"><?php _e( 'Post new projects or modify the sample projects to match your business scope.', ET_DOMAIN ) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" rel="noopener noreferrer"
                               href="<?php echo admin_url( 'edit-tags.php?taxonomy=project_category&post_type=project' ); ?>"><?php _e( 'Project category', ET_DOMAIN ) ?></a>
                            <span class="description"><?php _e( 'Add new categories or modify the sample categories to match your freelance business.', ET_DOMAIN ) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" rel="noopener noreferrer"
                               href="<?php echo admin_url( 'edit-tags.php?taxonomy=country&post_type=project' ); ?>"><?php _e( 'Location', ET_DOMAIN ) ?></a>
                            <span class="description"><?php _e( 'Add new locations or modify the sample locations to match your business scope.', ET_DOMAIN ) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" rel="noopener noreferrer"
                               href="<?php echo admin_url( 'edit-tags.php?taxonomy=skill&post_type=project' ); ?>"><?php _e( 'Skills', ET_DOMAIN ) ?></a>
                            <span class="description"><?php _e( 'Add new skills or modify the sample skills to match your freelance business.', ET_DOMAIN ) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" rel="noopener noreferrer"
                               href="<?php echo admin_url( 'edit.php?post_type=fre_profile' ); ?>"><?php _e( 'Profile', ET_DOMAIN ) ?></a>
                            <span class="description"><?php _e( 'Add new freelancer profiles or modify sample ones to match your business scope.', ET_DOMAIN ) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" rel="noopener noreferrer"
                               href="<?php echo admin_url( 'edit.php?post_type=page' ); ?>"><?php _e( 'Page', ET_DOMAIN ) ?></a>
                            <span class="description"><?php _e( 'Add your extra pages you need or modify pages included in the sample data such as "About Us", "Term of Services", and "Blog".', ET_DOMAIN ) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" rel="noopener noreferrer"
                               href="<?php echo admin_url( 'edit.php' ); ?>"><?php _e( 'Post', ET_DOMAIN ) ?></a>
                            <span
                                    class="description"><?php _e( 'Sample posts are added in the sample data. You can delete theme or add new your own post here.', ET_DOMAIN ) ?></span>
                        </div>
                    </div>
                </div>

                <div class="et-main-main clearfix inner-content <?php if ( ! $sample_data_op ) {
					echo 'hide';
				} ?>" id="overview-listplaces">

                    <div class="title font-quicksand" style="padding-bottom:60px;">
                        <h3><?php _e( 'MORE SETTINGS', ET_DOMAIN ) ?></h3>
                        <div class="desc small"><?php _e( 'Enhance your site by customizing these other features', ET_DOMAIN ) ?></div>
                    </div>

                    <div style="clear:both;"></div>

                    <div class="title font-quicksand  sample-title">
                        <a target="_blank" rel="noopener noreferrer"
                           href="admin.php?page=et-settings"><?php _e( 'General Settings', ET_DOMAIN ) ?></a> <span
                                class="description"><?php _e( 'Modify your site information, social links, analytics script, or add a language, etc.', ET_DOMAIN ) ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a target="_blank" rel="noopener noreferrer"
                           href="edit.php?post_type=page"><?php _e( 'Front Page', ET_DOMAIN ) ?></a> <span
                                class="description"><?php _e( 'Rearrange content elements or add more information in your front page to suit your needs.', ET_DOMAIN ) ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a target="_blank" rel="noopener noreferrer"
                           href="nav-menus.php"><?php _e( 'Menus', ET_DOMAIN ) ?></a> <span
                                class="description"><?php _e( 'Edit all available menus in your site here.', ET_DOMAIN ) ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a href="widgets.php" target="_blank"
                           rel="noopener noreferrer"><?php _e( 'Sidebars & Widgets', ET_DOMAIN ) ?></a> <span
                                class="description"><?php _e( 'Add or remove widgets in sidebars throughout the site to best suit your need.', ET_DOMAIN ) ?></span>
                    </div>

                </div>
            </div>
        </div>
        <style type="text/css">
            .hide {
                display: none;
            }

            .et-main-left .title, .et-main-main .title {
                text-transform: none;
            }

            .et-main-main {
                margin-left: 0;
            }

            .title.font-quicksand h3 {
                margin-bottom: 0;
                margin-top: 0;
            }

            .desc.small, span.description {
                font-family: Arial, sans-serif !important;
                font-weight: 400;
                font-size: 16px !important;
                color: #9d9d9d;
                font-style: normal;
                margin-top: 10px;
            }

            span.description {
                margin-left: 30px;
            }

            .sample-title {
                color: #427bab !important;
                padding-left: 20px !important;
                font-size: 18px !important;
            }

            .title.font-quicksand {
                padding-top: 15px;
            }

            a.primary-button {
                right: 50px;
                position: absolute;
                text-decoration: none;
                color: #ff9b78;
            }

            .et-main-main .title {
                padding-left: 20px;
            }

            .sample-title a {
                text-decoration: none;
            }
        </style>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * FrE notification after setup theme
	 *
	 * @param string $noti
	 *
	 * @return string $noti
	 * @since    1.6.2
	 * @package  void
	 * @category void
	 * @author   Tambh
	 */
	public function fre_notice_after_installing_theme( $noti ) {
		$noti = sprintf( __( "You have just installed Freelanceengine theme, we recommend you follow through our <a href='%s'>setup wizard</a> to set up the basic configuration for your website! <a href='%s'>Close this message</a>", ET_DOMAIN ), admin_url( 'admin.php?page=et-wizard' ), add_query_arg( 'close_notices', '1' ) );

		return $noti;
	}

	/**
	 * Set static page after insert sample data
	 *
	 * @param void
	 *
	 * @return void
	 * @since    void
	 * @package  void
	 * @category void
	 * @author   Tambh
	 */
	public function fre_after_insert_sample_data() {
		if ( $homeid = url_to_postid( et_get_page_link( 'home-new' ) ) ) {
			update_option( 'page_on_front', $homeid );
		}
		if ( $blogid = url_to_postid( et_get_page_link( 'blog' ) ) ) {
			update_option( 'page_for_posts ', $blogid );
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'avatar_default', 'gravatar_default' );
		$ae_option = AE_Options::get_instance();
		$ae_option->update_option( 'social_user_role', [ 'freelancer', 'employer' ] );
		et_change_user_role();
	}

	/**
	 * update admin setup
	 */
	function admin_setup() {
		// disable admin bar for all users except admin
		if ( ! current_user_can( 'administrator' ) && ! is_admin() ) {
			show_admin_bar( false );
		}

		$sections = [];

		/**
		 * general settings section
		 */
		$sections['general-settings'] = [
			'args'   => [
				'title' => __( "General", ET_DOMAIN ),
				'id'    => 'general-settings',
				'icon'  => 'y',
				'class' => ''
			],
			'groups' => [
				[
					'args' => [
						'title' => __( "Website Title", ET_DOMAIN ),
						'id'    => 'site-name',
						'class' => '',
						'desc'  => __( "Enter your website title.", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'    => 'blogname',
							'type'  => 'text',
							'title' => __( "Website Title", ET_DOMAIN ),
							'name'  => 'blogname',
							'class' => 'option-item bg-grey-input'
						]
					]
				],
				[
					'args' => [
						'title' => __( "Website Description", ET_DOMAIN ),
						'id'    => 'site-description',
						'class' => '',
						'desc'  => __( "Enter your website description", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'    => 'blogdescription',
							'type'  => 'text',
							'title' => __( "Website Title", ET_DOMAIN ),
							'name'  => 'blogdescription',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args' => [
						'title' => __( "Copyright", ET_DOMAIN ),
						'id'    => 'site-copyright',
						'class' => '',
						'desc'  => __( "This copyright information will appear in the footer.", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'    => 'copyright',
							'type'  => 'text',
							'title' => __( "Copyright", ET_DOMAIN ),
							'name'  => 'copyright',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args' => [
						'title' => __( "Google Analytics Script", ET_DOMAIN ),
						'id'    => 'site-analytics',
						'class' => '',
						'desc'  => __( "Google analytics is a service offered by Google that generates detailed statistics about the visits to a website.", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'    => 'opt-ace-editor-js',
							'type'  => 'textarea',
							'title' => __( "Google Analytics Script", ET_DOMAIN ),
							'name'  => 'google_analytics',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args' => [
						'title' => __( "Email Confirmation ", ET_DOMAIN ),
						'id'    => 'user-confirm',
						'class' => '',
						'desc'  => __( "Enabling this will require users to confirm their email addresses after registration.", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'    => 'user_confirm',
							'type'  => 'switch',
							'title' => __( "Email Confirmation", ET_DOMAIN ),
							'name'  => 'user_confirm',
							'class' => ''
						]
					]
				],
				[
					'args' => [
						'title' => __( "Admin Notification of New User Registration", ET_DOMAIN ),
						'id'    => 'sendmail-admin',
						'class' => '',
						'desc'  => __( "Notify the admin via email whenever the site gets new user registration.", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'    => 'sendmail_admin',
							'type'  => 'switch',
							'title' => __( "Admin Notification of New User Registration", ET_DOMAIN ),
							'name'  => 'sendmail_admin',
							'class' => ''
						]
					]
				],
				// @version: 1.8.3
				// @des: disable from this version.
				// array(
				// 	'args' => array(
				// 		'title' => __( "Log into admin panel", ET_DOMAIN ),
				// 		'id'    => 'login_init',
				// 		'class' => '',
				// 		'desc'  => __( "Prevent direct login to admin page.", ET_DOMAIN )
				// 	),

				// 	'fields' => array(
				// 		array(
				// 			'id'    => 'login-init',
				// 			'type'  => 'switch',
				// 			'label' => __( "Enable this option will prevent directly login to admin page.", ET_DOMAIN ),
				// 			'name'  => 'login_init',
				// 			'class' => 'option-item bg-grey-input '
				// 		)
				// 	)
				// ),
				[
					'args'   => [
						'title' => __( "Social Links", ET_DOMAIN ),
						'id'    => 'Social-Links',
						'class' => 'Social-Links',
						'desc'  => __( "Social links are displayed in the footer and in your blog sidebar.", ET_DOMAIN ),

						// 'name' => 'currency'

					],
					'fields' => []
				],

				[
					'args' => [
						'title' => __( "Twitter URL", ET_DOMAIN ),
						'id'    => 'site-twitter',
						'class' => 'payment-gateway',

						//'desc' => __("Your twitter link .", ET_DOMAIN)

					],

					'fields' => [
						[
							'id'    => 'site-twitter',
							'type'  => 'text',
							'title' => __( "Twitter URL", ET_DOMAIN ),
							'name'  => 'site_twitter',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args' => [
						'title' => __( "Facebook URL", ET_DOMAIN ),
						'id'    => 'site-facebook',
						'class' => 'payment-gateway',

						//'desc' => __(".", ET_DOMAIN)

					],

					'fields' => [
						[
							'id'    => 'site-facebook',
							'type'  => 'text',
							'title' => __( "Copyright", ET_DOMAIN ),
							'name'  => 'site_facebook',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args' => [
						'title' => __( "Google Plus URL", ET_DOMAIN ),
						'id'    => 'site-google',
						'class' => 'payment-gateway',

						// 'desc' => __("This copyright information will appear in the footer.", ET_DOMAIN)

					],

					'fields' => [
						[
							'id'    => 'site-google',
							'type'  => 'text',
							'title' => __( "Google Plus URL", ET_DOMAIN ),
							'name'  => 'site_google',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args' => [
						'title' => __( "Linkedin URL", ET_DOMAIN ),
						'id'    => 'site-linkedin',
						'class' => 'payment-gateway',

						// 'desc' => __("This copyright information will appear in the footer.", ET_DOMAIN)

					],

					'fields' => [
						[
							'id'    => 'site-linkedin',
							'type'  => 'text',
							'title' => __( "Linkedin URL", ET_DOMAIN ),
							'name'  => 'site_linkedin',
							'class' => 'option-item bg-grey-input '
						]
					]

				],


				[
					'args' => [
						'title' => __( "Disable automatic page creation.", ET_DOMAIN ),
						'id'    => 'auto_create_page',
						'class' => '',
						'desc'  => __( "Disable automatic page creation", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'    => 'login-init',
							'type'  => 'switch',
							'label' => __( "Enable this option will disable automatic page creation.", ET_DOMAIN ),
							'name'  => 'auto_create_page',
							'class' => 'option-item bg-grey-input '
						]
					]
				]
			]
		];

		/**
		 * branding section
		 */
		/*
	$sections['branding'] = array(

		'args' => array(
			'title' => __("Branding", ET_DOMAIN) ,
			'id' => 'branding-settings',
			'icon' => 'b',
			'class' => ''
		) ,

		'groups' => array(
			array(
				'args' => array(
					'title' => __("Site logo ", ET_DOMAIN) ,
					'id' => 'site-logo-black',
					'class' => '',
					'name' => '',
					'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
				) ,

				'fields' => array(
					array(
						'id' => 'opt-ace-editor-js',
						'type' => 'image',
						'title' => __("Site Logo", ET_DOMAIN) ,
						'name' => 'site_logo',
						'class' => '',
						'size' => array(
							'143',
							'29'
						)
					)
				)
			) ,
			array(
				'args' => array(
					'title' => __("Site logo in front page", ET_DOMAIN) ,
					'id' => 'site-logo-while',
					'class' => '',
					'name' => '',
					'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
				) ,

				'fields' => array(
					array(
						'id' => 'opt-ace-editor-js',
						'type' => 'image',
						'title' => __("Site Logo while", ET_DOMAIN) ,
						'name' => 'site_logo_white',
						'class' => '',
						'size' => array(
							'143',
							'29'
						)
					)
				)
			) ,
			array(
				'args' => array(
					'title' => __("Mobile logo", ET_DOMAIN) ,
					'id' => 'mobile-logo',
					'class' => '',
					'name' => '',
					'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
				) ,

				'fields' => array(
					array(
						'id' => 'opt-ace-editor-js',
						'type' => 'image',
						'title' => __("Mobile Logo", ET_DOMAIN) ,
						'name' => 'mobile_logo',
						'class' => '',
						'size' => array(
							'150',
							'50'
						)
					)
				)
			) ,

			array(
				'args' => array(
					'title' => __("Mobile Icon", ET_DOMAIN) ,
					'id' => 'mobile-icon',
					'class' => '',
					'name' => '',
					'desc' => __("This icon will be used as a launcher icon for iPhone and Android smartphones and also as the website favicon. The image dimensions should be 57x57px.", ET_DOMAIN)
				) ,

				'fields' => array(
					array(
						'id' => 'opt-ace-editor-js',
						'type' => 'image',
						'title' => __("Mobile Icon", ET_DOMAIN) ,
						'name' => 'mobile_icon',
						'class' => '',
						'size' => array(
							'57',
							'57'
						)
					)
				)
			) ,
			array(
				'args' => array(
					'title' => __("User default logo & avtar", ET_DOMAIN) ,
					'id' => 'default-logo',
					'class' => '',
					'name' => '',
					'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
				) ,

				'fields' => array(
					array(
						'id' => 'opt-ace-editor-js',
						'type' => 'image',
						'title' => __("User default logo & avtar", ET_DOMAIN) ,
						'name' => 'default_avatar',
						'class' => '',
						'size' => array(
							'150',
							'150'
						)
					)
				)
			)
		)
	);
	*/
		$sections['content'] = [
			'args'   => [
				'title' => __( "Content", ET_DOMAIN ),
				'id'    => 'content-settings',
				'icon'  => 'l',
				'class' => ''
			],
			//fre_share_role
			'groups' => [
				// array(
				//     'args' => array(
				//         'title' => __("Sharing Role Capabilities", ET_DOMAIN) ,
				//         'id' => 'fre-share-role',
				//         'class' => 'fre-share-role',
				//         'desc' => __("Enabling this will make employer and freelancer have the same capabilities.", ET_DOMAIN) ,
				//     ) ,
				//     'fields' => array(
				//         array(
				//             'id' => 'fre_share_role',
				//             'type' => 'switch',
				//             'title' => __("Shared Roles", ET_DOMAIN) ,
				//             'name' => 'fre_share_role',
				//             'class' => 'option-item bg-grey-input '
				//         )
				//     )
				// ) ,
				// array(
				//     'args' => array(
				//         'title' => __("Currency", ET_DOMAIN) ,
				//         'id' => 'content-payment-currency',
				//         'class' => 'content-list-package',
				//         'desc' => __("Enter currency code and sign ....", ET_DOMAIN) ,
				//         'name' => 'content_currency'
				//     ) ,
				//     'fields' => array(
				//         array(
				//             'id' => 'content-currency-code',
				//             'type' => 'text',
				//             'title' => __("Code", ET_DOMAIN) ,
				//             'name' => 'code',
				//             'placeholder' => __("Code", ET_DOMAIN) ,
				//             'class' => 'option-item bg-grey-input '
				//         ) ,
				//         array(
				//             'id' => 'content-currency-code',
				//             'type' => 'text',
				//             'title' => __("Sign", ET_DOMAIN) ,
				//             'name' => 'icon',
				//             'placeholder' => __("Sign", ET_DOMAIN) ,
				//             'class' => 'option-item bg-grey-input '
				//         ) ,
				//         array(
				//             'id' => 'currency-align',
				//             'type' => 'switch',
				//             'title' => __("Align", ET_DOMAIN) ,
				//             'name' => 'align',
				//             'class' => 'option-item bg-grey-input ',
				//             'label_1' => __("Left", ET_DOMAIN) ,
				//             'label_2' => __("Right", ET_DOMAIN) ,
				//         )
				//     )
				// ) ,

				[
					'args'   => [
						'title' => __( "Budget limitation", ET_DOMAIN ),
						'id'    => 'pending-post',
						'class' => 'pending-post',
						'desc'  => __( "Set up the limitation for the 'Budget' filter in 'Projects' page.", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'fre-slide-max-budget',
							'type'        => 'text',
							'title'       => __( "Slide max budget", ET_DOMAIN ),
							'name'        => 'fre_slide_max_budget',
							'placeholder' => __( "Slide max budget", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Freelancer budget limitation", ET_DOMAIN ),
						'id'    => 'pending-post-free',
						'class' => 'pending-post',
						'desc'  => __( "Set up the limitation for the 'Freelancer budget' filter in 'Profile' page.", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'fre-slide-max-budget',
							'type'        => 'text',
							'title'       => __( "Slide max budget", ET_DOMAIN ),
							'name'        => 'fre_slide_max_budget_freelancer',
							'placeholder' => __( "Slide max budget", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Pending Post", ET_DOMAIN ),
						'id'    => 'pending-post',
						'class' => 'pending-post',
						'desc'  => __( "Enabling this will make every new project posted pending until you review and approve it manually.", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'    => 'use_pending',
							'type'  => 'switch',
							'title' => __( "Align", ET_DOMAIN ),
							'name'  => 'use_pending',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Hide bid info", ET_DOMAIN ),
						'id'    => 'hide_bid_info',
						'class' => 'hide_bid_info',
						'desc'  => __( "Enabling this will make only admin and employer can see the detail of list bid in a project detail page.", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'      => 'hide_bid_info',
							'type'    => 'switch',
							'title'   => __( "Align", ET_DOMAIN ),
							'name'    => 'hide_bid_info',
							'class'   => 'option-item bg-grey-input ',
							'class'   => 'option-item bg-grey-input ',
							'label_1' => __( "Yes", ET_DOMAIN ),
							'label_2' => __( "No", ET_DOMAIN ),
						]
					]
				],

				[
					'args'   => [
						'title' => __( "Maximum Number of Categories", ET_DOMAIN ),
						'id'    => 'max-categories',
						'class' => 'max-categories',
						'desc'  => __( "Set a maximum number of categories freelancers can input in profile and employers can input when posting project.", ET_DOMAIN )
					],
					'fields' => [
						[
							'id'    => 'max_cat',
							'type'  => 'text',
							'title' => __( "Max Number Of Project Categories", ET_DOMAIN ),
							'name'  => 'max_cat',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Maximum Number of Skills", ET_DOMAIN ),
						'id'    => 'max-skill',
						'class' => 'max-skill',
						'desc'  => __( "Set a maximum number of skills freelancers can input their profile and employers can input when posting project.", ET_DOMAIN )
					],
					'fields' => [
						[
							'id'    => 'fre-max-skill',
							'type'  => 'text',
							'name'  => 'fre_max_skill',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				[
					'args' => [
						'title' => __( "Project Category Order", ET_DOMAIN ),
						'id'    => 'unit_measurement',
						'class' => '',
						'desc'  => __( "Order list project categories by.", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'          => 'order-project-category',
							'type'        => 'select',
							'data'        => [
								'name'  => __( "Name", ET_DOMAIN ),
								'slug'  => __( "Slug", ET_DOMAIN ),
								'id'    => __( "ID", ET_DOMAIN ),
								'count' => __( "Count", ET_DOMAIN )
							],
							'title'       => __( "Project Category Order", ET_DOMAIN ),
							'name'        => 'project_category_order',
							'class'       => 'option-item bg-grey-input ',
							'placeholder' => __( "Project Category Order", ET_DOMAIN )
						]
					]
				],
				[
					'args' => [
						'title' => __( "Project Type Order", ET_DOMAIN ),
						'id'    => 'unit_measurement',
						'class' => '',
						'desc'  => __( "Order list project types by.", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'          => 'order-project-type',
							'type'        => 'select',
							'data'        => [
								'name'  => __( "Name", ET_DOMAIN ),
								'slug'  => __( "Slug", ET_DOMAIN ),
								'id'    => __( "ID", ET_DOMAIN ),
								'count' => __( "Count", ET_DOMAIN )
							],
							'title'       => __( "Project Type Order", ET_DOMAIN ),
							'name'        => 'project_type_order',
							'class'       => 'option-item bg-grey-input ',
							'placeholder' => __( "Project Type Order", ET_DOMAIN )
						]
					]
				],
				/**
				 * hidden "Disable ocmment" option
				 *
				 * @since  : 1.8.4
				 * @author : danng
				 */
				// array(
				// 	'args' => array(
				// 		'title' => __( "Disable Comment", ET_DOMAIN ),
				// 		'id'    => 'disable-project-comment',
				// 		'class' => '',
				// 		'desc'  => __( "Disable comment on project page.", ET_DOMAIN )
				// 	),

				// 	'fields' => array(
				// 		array(
				// 			'id'      => 'disable_project_comment',
				// 			'type'    => 'switch',
				// 			'title'   => __( "Align", ET_DOMAIN ),
				// 			'name'    => 'disable_project_comment',

				// 			// 'label' => __("Code", ET_DOMAIN),
				// 			'class'   => 'option-item bg-grey-input ',
				// 			'label_1' => __( "Yes", ET_DOMAIN ),
				// 			'label_2' => __( "No", ET_DOMAIN ),
				// 		)
				// 	)
				// ),
				[
					'args' => [
						'title' => __( "Invited To Bid", ET_DOMAIN ),
						'id'    => 'invited-to-bid',
						'class' => '',
						'desc'  => __( "If you enable this option, freelancers have to be invited first before bidding a project.", ET_DOMAIN )
					],

					'fields' => [
						[
							'id'      => 'invited_to_bid',
							'type'    => 'switch',
							'title'   => __( "Invited To Bid", ET_DOMAIN ),
							'name'    => 'invited_to_bid',

							// 'label' => __("Code", ET_DOMAIN),
							'class'   => 'option-item bg-grey-input ',
							'label_1' => __( "Yes", ET_DOMAIN ),
							'label_2' => __( "No", ET_DOMAIN ),
						]
					]
				]
			]
		];

		$sections['freelancer'] = [
			'args'   => [
				'title' => __( "Freelancer", ET_DOMAIN ),
				'id'    => 'freelancer-settings',
				'icon'  => 'U',
				'class' => ''
			],
			//fre_share_role
			'groups' => [
				[
					'args'   => [
						'title' => __( "Pay to Bid", ET_DOMAIN ),
						'id'    => 'pay-to-bid',
						'class' => 'pay-to-bid',
						'desc'  => __( "Enabling this will require freelancer pay to bid.", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'    => 'pay_to_bid',
							'type'  => 'switch',
							'title' => __( "Pay to Bid", ET_DOMAIN ),
							'name'  => 'pay_to_bid',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				/**
				 * package plan list
				 */
				[
					'type' => 'list',
					'args' => [
						'title'        => __( "Bid Plans", ET_DOMAIN ),
						'id'           => 'list-package',
						'class'        => 'list-package',
						'desc'         => '',
						'name'         => 'bid_plan',
						'custom_field' => 'bid_plan'
					],

					'fields' => [
						'form'        => '/admin-template/bid-plan-form.php',
						'form_js'     => '/admin-template/bid-plan-form-js.php',
						'js_template' => '/admin-template/bid-plan-js-item.php',
						'template'    => '/admin-template/bid-plan-item.php'
					]
				],
			]
		];

		/**
		 * slug settings
		 */
		$sections['url_slug'] = [
			'args'   => [
				'title' => __( "Url slug", ET_DOMAIN ),
				'id'    => 'Url-Slug',
				'icon'  => 'i',
				'class' => ''
			],
			'groups' => [
				[
					'args'   => [
						'title' => __( "Project", ET_DOMAIN ),
						'id'    => 'project-slug',
						'class' => 'list-package',
						'desc'  => __( "Enter slug for your Single Project page", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'fre_project_slug',
							'type'        => 'text',
							'title'       => __( "Single Project page Slug", ET_DOMAIN ),
							'name'        => 'fre_project_slug',
							'placeholder' => __( "Single Project page Slug", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input ',
							'default'     => 'project'
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Project Listing", ET_DOMAIN ),
						'id'    => 'project-archive_slug',
						'class' => 'list-package',
						'desc'  => __( "Enter slug for your Projects listing page", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'fre_project_archive',
							'type'        => 'text',
							'title'       => __( "Projects listing page Slug", ET_DOMAIN ),
							'name'        => 'fre_project_archive',
							'placeholder' => __( "Projects listing page Slug", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input ',
							'default'     => 'projects'
						]
					]
				],

				[
					'args'   => [
						'title' => __( "Project Category", ET_DOMAIN ),
						'id'    => 'Project-Category',
						'class' => 'list-package',
						'desc'  => __( "Enter slug for your Project Category page", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'project_category_slug',
							'type'        => 'text',
							'title'       => __( "Project Category page Slug", ET_DOMAIN ),
							'name'        => 'project_category_slug',
							'placeholder' => __( "Project Category page Slug", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input ',
							'default'     => 'project_category',
						]
					]
				],

				[
					'args'   => [
						'title' => __( "Project Type", ET_DOMAIN ),
						'id'    => 'Project-Type',
						'class' => 'list-package',
						'desc'  => __( "Enter slug for your Project Type page", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'project_type_slug',
							'type'        => 'text',
							'title'       => __( "Project Type page Slug", ET_DOMAIN ),
							'name'        => 'project_type_slug',
							'placeholder' => __( "Project Type page Slug", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input ',
							'default'     => 'project_type'
						]
					]
				],

				[
					'args'   => [
						'title' => __( "Profile", ET_DOMAIN ),
						'id'    => 'Profile-slug',
						'class' => 'list-package',
						'desc'  => __( "Enter slug for your User Profile page", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'fre_profile_slug',
							'type'        => 'text',
							'title'       => __( "User Profile page Slug", ET_DOMAIN ),
							'name'        => 'author_base',
							'placeholder' => __( "User Profile page Slug", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input ',
							'default'     => 'author'
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Profiles Listing", ET_DOMAIN ),
						'id'    => 'profiles-archive_slug',
						'class' => 'list-package',
						'desc'  => __( "Enter slug for your Profiles listing page", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'fre_profile_archive',
							'type'        => 'text',
							'title'       => __( " Profiles listing page Slug", ET_DOMAIN ),
							'name'        => 'fre_profile_archive',
							'placeholder' => __( "Profiles listing page Slug", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input ',
							'default'     => 'profiles'
						]
					]
				],

				[
					'args'   => [
						'title' => __( "Country", ET_DOMAIN ),
						'id'    => 'profile-Country',
						'class' => 'list-package',
						'desc'  => __( "Enter slug for your Country tag page", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'country_slug',
							'type'        => 'text',
							'title'       => __( "Country tag page Slug", ET_DOMAIN ),
							'name'        => 'country_slug',
							'placeholder' => __( "Country tag page Slug", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input ',
							'default'     => 'country'
						]
					]
				],

				[
					'args'   => [
						'title' => __( "Skill", ET_DOMAIN ),
						'id'    => 'profile-Skill',
						'class' => 'list-package',
						'desc'  => __( "Enter slug for your Skill tag page", ET_DOMAIN ),
					],
					'fields' => [
						[
							'id'          => 'skill_slug',
							'type'        => 'text',
							'title'       => __( "Skill tag page Slug", ET_DOMAIN ),
							'name'        => 'skill_slug',
							'placeholder' => __( "Skill tag page Slug", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input ',
							'default'     => 'skill'
						]
					]
				]
			]
		];

		/**
		 * video background settings section
		 */
		/*
	 * disable from version 1.8.3 by danng.

	$sections['header_video'] = array(

		'args' => array(
			'title' => __( "Header Video", ET_DOMAIN ),
			'id'    => 'header-settings',
			'icon'  => 'V',
			'class' => ''
		),

		'groups' => array(

			array(
				'args' => array(
					'title' => __( "Video Background Url", ET_DOMAIN ),
					'id'    => 'header-slider-settings',
					'class' => '',
					'desc'  => __( "Enter your video background url in page-home.php template (.mp4)", ET_DOMAIN )
				),

				'fields' => array(
					array(
						'id'          => 'header-video',
						'type'        => 'text',
						'title'       => __( "header video url", ET_DOMAIN ),
						'name'        => 'header_video',
						'class'       => 'option-item bg-grey-input ',
						'placeholder' => __( 'Enter your header video url', ET_DOMAIN )
					)
				)
			),
			array(
				'args' => array(
					'title' => __( "Video Background Via Youtube ID", ET_DOMAIN ),
					'id'    => 'header-youtube_id',
					'class' => '',
					'desc'  => __( "Enter youtube ID for background video instead of video url", ET_DOMAIN )
				),

				'fields' => array(
					array(
						'id'          => 'youtube_id-video',
						'type'        => 'text',
						'title'       => __( "header video url", ET_DOMAIN ),
						'name'        => 'header_youtube_id',
						'class'       => 'option-item bg-grey-input ',
						'placeholder' => __( 'Enter youtube video ID', ET_DOMAIN )
					)
				)
			),

			array(
				'args' => array(
					'title' => __( "Video Background Fallback", ET_DOMAIN ),
					'id'    => 'header-slider-settings',
					'class' => '',
					'desc'  => __( "Fallback image for video background when browser not support", ET_DOMAIN )
				),

				'fields' => array(
					array(
						'id'          => 'header-video',
						'type'        => 'text',
						'title'       => __( "header video url", ET_DOMAIN ),
						'name'        => 'header_video_fallback',
						'class'       => 'option-item bg-grey-input ',
						'placeholder' => __( 'Enter your header video fallback image url', ET_DOMAIN )
					)
				)
			),

			array(
				'args' => array(
					'title' => __( "Project Demonstration", ET_DOMAIN ),
					'id'    => 'header-slider-settings',
					'class' => '',
					'name'  => 'project_demonstration'

					// 'desc' => __("Enter your header slider setting", ET_DOMAIN)

				),

				'fields' => array(
					array(
						'id'    => 'header-left-text',
						'type'  => 'text',
						'title' => __( "header left text", ET_DOMAIN ),
						'name'  => 'home_page',
						'class' => 'option-item bg-grey-input ',
						'label' => __( 'Project demonstration on header video background viewed by employer.', ET_DOMAIN )
					),
					array(
						'id'    => 'header-right-text',
						'type'  => 'text',
						'title' => __( "header right text", ET_DOMAIN ),
						'name'  => 'list_project',
						'class' => 'option-item bg-grey-input ',
						'label' => __( 'Project demonstration on header video background viewed by freelancer.', ET_DOMAIN )
					)
				)
			),

			array(
				'args' => array(
					'title' => __( "Profile Demonstration", ET_DOMAIN ),
					'id'    => 'header-slider-settings',
					'class' => '',
					'name'  => 'profile_demonstration'

					// 'desc' => __("Enter your header slider setting", ET_DOMAIN)

				),

				'fields' => array(
					array(
						'id'    => 'header-left-text',
						'type'  => 'text',
						'title' => __( "header left text", ET_DOMAIN ),
						'name'  => 'home_page',
						'class' => 'option-item bg-grey-input ',
						'label' => __( 'Profile demonstration on header video background viewed by freelancer.', ET_DOMAIN )
					),
					array(
						'id'    => 'header-right-text',
						'type'  => 'text',
						'title' => __( "header right text", ET_DOMAIN ),
						'name'  => 'list_profile',
						'class' => 'option-item bg-grey-input ',
						'label' => __( 'Profiles demonstration on list profiles page viewed by employer.', ET_DOMAIN )
					)
				)
			),
			array(
				'args'   => array(
					'title' => __( "Loop Header Video", ET_DOMAIN ),
					'id'    => 'header-video-loop-option',
					'class' => '',
					'desc'  => __( " Enabling this will make the video on the header repeated automatically.", ET_DOMAIN )
				),
				'fields' => array(
					array(
						'id'    => 'header-video-loop',
						'type'  => 'switch',
						'title' => __( "Select video loop", ET_DOMAIN ),
						'name'  => 'header_video_loop',
						'class' => 'option-item bg-grey-input '
					)
				)
			)
		)
	);
	*/

		/**
		 * google captcha settings section
		 * Re-add from 1.8.4
		 *
		 * @author : danng
		 */
		$sections['gg_captcha'] = [
			'args'   => [
				'title' => __( "Captcha", ET_DOMAIN ),
				'id'    => 'gg-captcha',
				'icon'  => '3',
				'class' => ''
			],
			'groups' => [
				[
					'args'   => [
						'title' => __( "Google reCaptcha", ET_DOMAIN ),
						'id'    => 'google-recaptcha',
						'class' => '',
						'desc'  => __( "Enabling this will prevent spammers from registering.<a href='https://www.google.com/recaptcha/admin#list' target='_blank' rel='nofollow'>get key</a>", ET_DOMAIN )
					],
					'fields' => [
						[
							'id'    => 'gg_captcha',
							'type'  => 'switch',
							'title' => __( "Google reCaptcha", ET_DOMAIN ),
							'name'  => 'gg_captcha',
							'class' => ''
						],
						[
							'id'          => 'gg_site_key',
							'type'        => 'text',
							'title'       => __( "Site key", ET_DOMAIN ),
							'name'        => 'gg_site_key',
							'placeholder' => __( "reCaptcha Site Key", ET_DOMAIN ),
							'class'       => ''
						],
						[
							'id'          => 'gg_secret_key',
							'type'        => 'text',
							'title'       => __( "Secret key", ET_DOMAIN ),
							'name'        => 'gg_secret_key',
							'placeholder' => __( "reCaptcha Secret Key", ET_DOMAIN ),
							'class'       => ''
						]
					]
				]
			]
		];


		/**
		 * Payment settings
		 */
		$sections['payment_settings'] = [
			'args' => [
				'title' => __( "Payment", ET_DOMAIN ),
				'id'    => 'payment-settings',
				'icon'  => '%',
				'class' => ''
			],

			'groups' => [
				[
					'args'   => [
						'title' => '<span class="dashicons dashicons-warning" style="color:#ff6c00;"></span> ' . __( "Please enter the currency supported by payment gateways that are activated on the site.", ET_DOMAIN ),
						'id'    => 'warning-description-group',
						'class' => 'no-desc',
						'name'  => ''
					],
					'fields' => [
						[
							'id'    => 'warning-description',
							'type'  => 'desc',
							'title' => "",
							'text'  => "",
							'class' => '',
							'name'  => 'warning_description'
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Payment Currency", ET_DOMAIN ),
						'id'    => 'payment-currency',
						'class' => 'list-package',
						'desc'  => __( "Enter currency code and sign.", ET_DOMAIN ),
						'name'  => 'currency'
					],
					'fields' => [
						[
							'id'          => 'currency-code',
							'type'        => 'text',
							'title'       => __( "Code", ET_DOMAIN ),
							'name'        => 'code',
							'placeholder' => __( "Code", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input '
						],
						[
							'id'          => 'currency-sign',
							'type'        => 'text',
							'title'       => __( "Sign", ET_DOMAIN ),
							'name'        => 'icon',
							'placeholder' => __( "Sign", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input '
						],
						[
							'id'      => 'currency-align',
							'type'    => 'switch',
							'title'   => __( "Align", ET_DOMAIN ),
							'name'    => 'align',

							// 'label' => __("Code", ET_DOMAIN),
							'class'   => 'option-item bg-grey-input ',
							'label_1' => __( "Left", ET_DOMAIN ),
							'label_2' => __( "Right", ET_DOMAIN ),
						],
					]
				],

				[
					'args'   => [
						'title' => __( "Number Format", ET_DOMAIN ),
						'id'    => 'number-format',
						'class' => 'list-package',
						'desc'  => __( "Format a number with grouped thousands", ET_DOMAIN ),
						'name'  => 'number_format'
					],
					'fields' => [
						[
							'id'          => 'decimal-point',
							'type'        => 'text',
							'title'       => __( "Decimal point", ET_DOMAIN ),
							'label'       => __( "Decimal point", ET_DOMAIN ),
							'name'        => 'dec_point',
							'placeholder' => __( "Decimal point", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input '
						],
						[
							'id'          => 'thousand_sep',
							'type'        => 'text',
							'label'       => __( "Thousand separator", ET_DOMAIN ),
							'title'       => __( "Thousand separator", ET_DOMAIN ),
							'name'        => 'thousand_sep',
							'placeholder' => __( "Thousand separator", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input '
						],
						[
							'id'          => 'et_decimal',
							'type'        => 'text',
							'label'       => __( "Number of decimal points", ET_DOMAIN ),
							'title'       => __( "Number of decimal points", ET_DOMAIN ),
							'name'        => 'et_decimal',
							'placeholder' => __( "Sets the number of decimal points.", ET_DOMAIN ),
							'class'       => 'option-item bg-grey-input positive_int',
							'default'     => 2
						],
					]
				],

				[
					'args'   => [
						'title' => __( "Free to submit listing", ET_DOMAIN ),
						'id'    => 'free-to-submit-listing',
						'class' => 'free-to-submit-listing',
						'desc'  => __( "Enabling this will allow users to submit listing free.", ET_DOMAIN ),

						// 'name' => 'currency'


					],
					'fields' => [
						[
							'id'    => 'disable-plan',
							'type'  => 'switch',
							'title' => __( "Align", ET_DOMAIN ),
							'name'  => 'disable_plan',
							'class' => 'option-item bg-grey-input '
						]
					]
				],

				/* payment test mode settings */
				[
					'args'   => [
						'title' => __( "Payment Test Mode", ET_DOMAIN ),
						'id'    => 'payment-test-mode',
						'class' => 'payment-test-mode',
						'desc'  => __( "Enabling this will allow you to test payment without charging your account.", ET_DOMAIN ),

						// 'name' => 'currency'


					],
					'fields' => [
						[
							'id'    => 'test-mode',
							'type'  => 'switch',
							'title' => __( "Align", ET_DOMAIN ),
							'name'  => 'test_mode',
							'class' => 'option-item bg-grey-input '
						]
					]
				],
				// payment test mode

				/* payment gateways settings */
				[
					'args'   => [
						'title' => __( "Payment Gateways", ET_DOMAIN ),
						'id'    => 'payment-gateways',
						'class' => 'payment-gateways',
						'desc'  => __( "Set up the payment gateway which your users can select for the payment.", ET_DOMAIN ),

						// 'name' => 'currency'

					],
					'fields' => []
				],

				[
					'args'   => [
						'title' => __( "Paypal", ET_DOMAIN ),
						'id'    => 'Paypal',
						'class' => 'payment-gateway',
						'desc'  => __( "Enabling this will allow your users to pay via PayPal.", ET_DOMAIN ),

						'name' => 'paypal'
					],
					'fields' => [
						[
							'id'    => 'paypal',
							'type'  => 'switch',
							'title' => __( "Align", ET_DOMAIN ),
							'name'  => 'enable',
							'class' => 'option-item bg-grey-input '
						],
						[
							'id'          => 'paypal_mode',
							'type'        => 'text',
							'title'       => __( "Align", ET_DOMAIN ),
							'name'        => 'api_username',
							'class'       => 'option-item bg-grey-input ',
							'placeholder' => __( 'Enter your PayPal email address', ET_DOMAIN )
						]
					]
				],

				[
					'args'   => [
						'title' => __( "2Checkout", ET_DOMAIN ),
						'id'    => '2Checkout',
						'class' => 'payment-gateway',
						'desc'  => __( "Enabling this will allow your users to pay via 2Checkout.", ET_DOMAIN ),

						'name' => '2checkout'
					],
					'fields' => [
						[
							'id'    => '2Checkout_mode',
							'type'  => 'switch',
							'title' => __( "2Checkout mode", ET_DOMAIN ),
							'name'  => 'enable',
							'class' => 'option-item bg-grey-input '
						],
						[
							'id'          => 'sid',
							'type'        => 'text',
							'title'       => __( "Sid", ET_DOMAIN ),
							'name'        => 'sid',
							'class'       => 'option-item bg-grey-input ',
							'placeholder' => __( 'Your 2Checkout Seller ID', ET_DOMAIN )
						],
						[
							'id'          => 'secret_key',
							'type'        => 'text',
							'title'       => __( "Secret Key", ET_DOMAIN ),
							'name'        => 'secret_key',
							'class'       => 'option-item bg-grey-input ',
							'placeholder' => __( 'Your 2Checkout Secret Key', ET_DOMAIN )
						],
						[
							'id'          => 'publishable_key',
							'type'        => 'text',
							'title'       => __( "Publishable Key", ET_DOMAIN ),
							'name'        => 'publishable_key',
							'class'       => 'option-item bg-grey-input ',
							'placeholder' => __( 'Your 2Checkout Publishable Key', ET_DOMAIN )
						],
						[
							'id'          => 'private_key',
							'type'        => 'text',
							'title'       => __( "Private Key", ET_DOMAIN ),
							'name'        => 'private_key',
							'class'       => 'option-item bg-grey-input ',
							'placeholder' => __( 'Your 2Checkout Private Key', ET_DOMAIN )
						],
					]
				],
				[
					'args'   => [
						'title' => __( "Cash", ET_DOMAIN ),
						'id'    => 'Cash',
						'class' => 'payment-gateway',
						'desc'  => __( "Please enter your bank account information so that users can transfer money to you. Ex: Doris Clarke, 13078885679XXX,  International ACH Bank", ET_DOMAIN ),

						'name' => 'cash'
					],
					'fields' => [
						[
							'id'    => 'cash_message_enable',
							'type'  => 'switch',
							'title' => __( "Align", ET_DOMAIN ),
							'name'  => 'enable',
							'class' => 'option-item bg-grey-input '
						],
						[
							'id'    => 'cash_message',
							'type'  => 'editor',
							'title' => __( "Align", ET_DOMAIN ),
							'name'  => 'cash_message',
							'class' => 'option-item bg-grey-input ',

							// 'placeholder' => __('Enter your PayPal email address', ET_DOMAIN)

						]
					]
				],
				/**
				 * package plan list
				 */
				[
					'type' => 'list',
					'args' => [
						'title' => __( "Payment Plans", ET_DOMAIN ),
						'id'    => 'list-package',
						'class' => 'list-package',
						'desc'  => '',
						'name'  => 'pack',
					],

					'fields' => [
						'form'        => '/admin-template/package-form.php',
						'form_js'     => '/admin-template/package-form-js.php',
						'js_template' => '/admin-template/package-js-item.php',
						'template'    => '/admin-template/package-item.php'
					]
				],

				// limit_free_plan
				// array(
				//     'args' => array(
				//         'title' => __("Limit Free Plan Use", ET_DOMAIN) ,
				//         'id' => 'limit_free_plan',
				//         'class' => 'limit_free_plan',
				//         'desc' => __("Enter the maximum number allowing employers to use your Free plan.", ET_DOMAIN)
				//     ) ,
				//     'fields' => array(
				//         array(
				//             'id' => 'cash_message_enable',
				//             'type' => 'text',
				//             'title' => __("Align", ET_DOMAIN) ,
				//             'name' => 'limit_free_plan',
				//             'class' => 'option-item bg-grey-input '
				//         )
				//     )
				// ) ,
			]
		];

		/**
		 * mail template settings section
		 */
		$sections['mailing'] = [
			'args' => [
				'title' => __( "Mailing", ET_DOMAIN ),
				'id'    => 'mail-settings',
				'icon'  => 'M',
				'class' => ''
			],

			'groups' => [
				[
					'args'   => [
						'title' => __( "Get Quote | Request Quote Company Mail Template", ET_DOMAIN ),
						'id'    => 'get-quote-group',
						'class' => '',
						'name'  => ''
					],
					'fields' => [
						[
							'id'    => 'get-quote',
							'type'  => 'desc',
							'title' => __( "Info Qet Quote", ET_DOMAIN ),
							'text'  => __( "Email templates for Get/Request Quote process. You can use placeholders to include some specific content.", ET_DOMAIN ) . '<a class="icon btn-template-help payment" data-icon="?" href="#" title="View more details"></a>' . '<div class="cont-template-help get-quote-setting">' . '[message],[display_name],[user_email] : ' . __( "shortcodes mail \"Qet Quote\" that you can send mail", ET_DOMAIN ) . '</div>' . '<div class="cont-template-help get-quote-setting">' . '[project_link],[project_name],[display_name],[user_email] : ' . __( "shortcodes mail \"Request Quote Company\" that you can send mail", ET_DOMAIN ) . '</div>',

							'class' => '',
							'name'  => 'mail_description'
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Get a Quote Mail Template", ET_DOMAIN ),
						'id'     => 'get-quote-company',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to a company when user select companies in the catalog", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'          => 'get_quote_company_subject',
							'type'        => 'text',
							'title'       => __( "Subject Mail", ET_DOMAIN ),
							'name'        => 'get_quote_company_subject',
							'placeholder' => 'Subject Mail',
							'class'       => '',
							'default'     => 'Get Quote Company'
						],
						[
							'id'    => 'get_quote_company',
							'type'  => 'editor',
							'title' => __( "Get Quote Mail", ET_DOMAIN ),
							'name'  => 'get_quote_company',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Request a Quote Mail Template", ET_DOMAIN ),
						'id'     => 'request-quote-company',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to companies when user requests it in a project", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'          => 'request_quote_company_subject',
							'type'        => 'text',
							'title'       => __( "Subject Mail", ET_DOMAIN ),
							'name'        => 'request_quote_company_subject',
							'placeholder' => 'Subject Mail',
							'class'       => '',
							'default'     => 'Request Quote Company'
						],
						[
							'id'    => 'request_quote_company',
							'type'  => 'editor',
							'title' => __( "Request Quote Company Mail", ET_DOMAIN ),
							'name'  => 'request_quote_company',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title' => __( "Authentication Mail Template", ET_DOMAIN ),
						'id'    => 'mail-description-group',
						'class' => '',
						'name'  => ''
					],
					'fields' => [
						[
							'id'    => 'mail-description',
							'type'  => 'desc',
							'title' => __( "Mail description here", ET_DOMAIN ),
							'text'  => __( "Email templates for authentication process. You can use placeholders to include some specific content.", ET_DOMAIN ) . '<a class="icon btn-template-help payment" data-icon="?" href="#" title="View more details"></a>' . '<div class="cont-template-help payment-setting">
                                                    [user_login],[display_name],[user_email] : ' . __( "user's details you want to send mail", ET_DOMAIN ) . '<br />
                                                    [dashboard] : ' . __( "member dashboard url ", ET_DOMAIN ) . '<br />
                                                    [title], [link], [excerpt],[desc], [author] : ' . __( "project title, link, details, author", ET_DOMAIN ) . ' <br />
                                                    [activate_url] : ' . __( "activate link is require for user to renew their pass", ET_DOMAIN ) . ' <br />
                                                    [site_url],[blogname],[admin_email] : ' . __( " site info, admin email", ET_DOMAIN ) . '
                                                    [project_list] : ' . __( "list projects employer send to freelancer when invite him to join", ET_DOMAIN ) . '

                                                </div>',

							'class' => '',
							'name'  => 'mail_description'
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Register Mail Template", ET_DOMAIN ),
						'id'     => 'register-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user when he has successfully registered.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'register_mail_template',
							'type'  => 'editor',
							'title' => __( "Register Mail", ET_DOMAIN ),
							'name'  => 'register_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Register Social Network Mail Template", ET_DOMAIN ),
						'id'     => 'register-mail-social',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user when he has successfully registered by Social Network.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'register_social_mail_template',
							'type'  => 'editor',
							'title' => __( "Register Mail", ET_DOMAIN ),
							'name'  => 'register_social_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Confirm Mail Template", ET_DOMAIN ),
						'id'     => 'confirm-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user after he successfully registered when the option of confirming email is on.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'confirm_mail_template',
							'type'  => 'editor',
							'title' => __( "Confirme Mail", ET_DOMAIN ),
							'name'  => 'confirm_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Confirm New Mail Template", ET_DOMAIN ),
						'id'     => 'confirm-new-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user after he change email.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'confirm_new_mail_template',
							'type'  => 'editor',
							'title' => __( "Confirme New Mail", ET_DOMAIN ),
							'name'  => 'confirm_new_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Confirmed Mail Template", ET_DOMAIN ),
						'id'     => 'confirmed-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user to notify that he successfully confirmed his email.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'confirmed_mail_template',
							'type'  => 'editor',
							'title' => __( "Confirmed Mail", ET_DOMAIN ),
							'name'  => 'confirmed_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Forgotpass Mail Template", ET_DOMAIN ),
						'id'     => 'forgotpass-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user when he requests resetpass.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'forgotpass_mail_template',
							'type'  => 'editor',
							'title' => __( "Register Mail", ET_DOMAIN ),
							'name'  => 'forgotpass_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Resetpass Mail Template", ET_DOMAIN ),
						'id'     => 'resetpass-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user to notify his password has been successfully reset.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'resetpass_mail_template',
							'type'  => 'editor',
							'title' => __( "Resetpassword Mail", ET_DOMAIN ),
							'name'  => 'resetpass_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Ban Mail Template", ET_DOMAIN ),
						'id'     => 'ban-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						// 'desc' => __("Send to user to notify him has resetpass successfully.", ET_DOMAIN),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'ban_mail_template',
							'type'  => 'editor',
							'title' => __( "Ban Mail", ET_DOMAIN ),
							'name'  => 'ban_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Project options expired", ET_DOMAIN ),
						'id'     => 'proj-opt-expired',
						'class'  => 'payment-gateway',
						'name'   => '',
						// 'desc' => __("Send to user to notify him has resetpass successfully.", ET_DOMAIN),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'options_deadline_paid',
							'type'  => 'editor',
							'title' => __( "Options Expired", ET_DOMAIN ),
							'name'  => 'options_deadline_paid',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title' => __( "Project Related Mail Template", ET_DOMAIN ),
						'id'    => 'mail-description-group',
						'class' => '',
						'name'  => ''
					],
					'fields' => [
						[
							'id'    => 'mail-description',
							'type'  => 'desc',
							'title' => __( "Mail description here", ET_DOMAIN ),
							'text'  => __( "Email templates used for project-related event. You can use placeholders to include some specific content", ET_DOMAIN ),
							'class' => '',
							'name'  => 'mail_description'
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Review Payment Notification Mail Template", ET_DOMAIN ),
						'id'     => 'new-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to admin when the site has a new payment.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'new_payment_mail_template',
							'type'  => 'editor',
							'title' => __( "Review payment notification", ET_DOMAIN ),
							'name'  => 'new_payment_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "New Message Mail Template", ET_DOMAIN ),
						'id'     => 'new-message-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user when he has a new message on workspace.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'new_message_mail_template',
							'type'  => 'editor',
							'title' => __( "Inbox Mail", ET_DOMAIN ),
							'name'  => 'new_message_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Inbox Mail Template", ET_DOMAIN ),
						'id'     => 'inbox-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user when someone contacts him.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'inbox_mail_template',
							'type'  => 'editor',
							'title' => __( "Inbox Mail", ET_DOMAIN ),
							'name'  => 'inbox_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Invite Mail Template", ET_DOMAIN ),
						'id'     => 'invite-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user when someone invites him join a project.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'invite_mail_template',
							'type'  => 'editor',
							'title' => __( "Invite Mail", ET_DOMAIN ),
							'name'  => 'invite_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				// array(
				//     'args' => array(
				//         'title' => __("Cash Notification Mail Template", ET_DOMAIN) ,
				//         'id' => 'cash-mail',
				//         'class' => 'payment-gateway',
				//         'name' => '',
				//         'desc' => __("Send to user cash message when he pays by cash.", ET_DOMAIN),
				//         'toggle' => true
				//     ) ,
				//     'fields' => array(
				//         array(
				//             'id' => 'cash_notification_mail',
				//             'type' => 'editor',
				//             'title' => __("Cash Notification Mail", ET_DOMAIN) ,
				//             'name' => 'cash_notification_mail',
				//             'class' => '',
				//             'reset' => 1
				//         )
				//     )
				// ),

				[
					'args'   => [
						'title'  => __( "Receipt Mail Template", ET_DOMAIN ),
						'id'     => 'ae-receipt_mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'toggle' => true,
						'desc'   => __( "Send to users after they finish a payment", ET_DOMAIN )
					],
					'fields' => [
						[
							'id'    => 'ae_receipt_mail',
							'type'  => 'editor',
							'title' => __( "Receipt Mail Template", ET_DOMAIN ),
							'name'  => 'ae_receipt_mail',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Payment receipt for the post package notification", ET_DOMAIN ),
						'id'     => 'ae-receipt_project_mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'toggle' => true,
						'desc'   => __( "Send to employer when he buys a post project package", ET_DOMAIN )
					],
					'fields' => [
						[
							'id'    => 'ae_receipt_project_mail',
							'type'  => 'editor',
							'title' => __( "Payment receipt for the post package notification", ET_DOMAIN ),
							'name'  => 'ae_receipt_project_mail',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Payment receipt for the bid package notification", ET_DOMAIN ),
						'id'     => 'ae-receipt_bid_mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'toggle' => true,
						'desc'   => __( "Send to user when he buys a bid project package", ET_DOMAIN )
					],
					'fields' => [
						[
							'id'    => 'ae_receipt_bid_mail',
							'type'  => 'editor',
							'title' => __( "Payment receipt for the bid package notification", ET_DOMAIN ),
							'name'  => 'ae_receipt_bid_mail',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Publish Mail Template", ET_DOMAIN ),
						'id'     => 'publish-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Sent to users to notify that one of their listing has been published.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'publish_mail_template',
							'type'  => 'editor',
							'title' => __( "publish Mail", ET_DOMAIN ),
							'name'  => 'publish_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Review the re-submitted project notification", ET_DOMAIN ),
						'id'     => 'resubmmit-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to the admin when employer re-submits his rejected project.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'ae_resubmitted_project_mail',
							'type'  => 'editor',
							'title' => __( "Review the re-submitted project notification", ET_DOMAIN ),
							'name'  => 'ae_resubmitted_project_mail',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Archive Mail Template", ET_DOMAIN ),
						'id'     => 'archive-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Sent to users to notify that one of their listing has been archived due to expiration or manual administrative action.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'archive_mail_template',
							'type'  => 'editor',
							'title' => __( "archive Mail", ET_DOMAIN ),
							'name'  => 'archive_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Reject Mail Template", ET_DOMAIN ),
						'id'     => 'reject-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Sent to users to notify that one of their listing has been rejected.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'reject_mail_template',
							'type'  => 'editor',
							'title' => __( "reject Mail", ET_DOMAIN ),
							'name'  => 'reject_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "New Bid Mail Template", ET_DOMAIN ),
						'id'     => 'bid-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Sent to users when a candidate bid their projects.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'bid_mail_template',
							'type'  => 'editor',
							'title' => __( "Bid Mail", ET_DOMAIN ),
							'name'  => 'bid_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Bid Accepted Mail Template", ET_DOMAIN ),
						'id'     => 'bid_accepted_-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to freelancer when his bid was accepted.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'bid_accepted_template',
							'type'  => 'editor',
							'title' => __( "Bid Accepted Mail", ET_DOMAIN ),
							'name'  => 'bid_accepted_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Reply To Review Mail Template", ET_DOMAIN ),
						'id'     => 'reply_to_review_-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to freelancer employer reply to his review.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'reply_to_review_template',
							'type'  => 'editor',
							'title' => __( "Reply To Review Mail", ET_DOMAIN ),
							'name'  => 'reply_to_review_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Project was accepted by Employer", ET_DOMAIN ),
						'id'     => 'bid_accepted_alternative-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to the freelancers after employer accepted a bid from an alternative freelancer.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'bid_accepted_alternative_template',
							'type'  => 'editor',
							'title' => __( "Project was accepted by Employer", ET_DOMAIN ),
							'name'  => 'bid_accepted_alternative_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Project was finished by employer", ET_DOMAIN ),
						'id'     => 'complete-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to the freelancer when employer finished a project", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'complete_mail_template',
							'type'  => 'editor',
							'title' => __( "Complete Mail", ET_DOMAIN ),
							'name'  => 'complete_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "New referral template", ET_DOMAIN ),
						'id'     => 'new-referral',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to user when he receives a new referral", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'new_referral_template',
							'type'  => 'editor',
							'title' => __( "New referral", ET_DOMAIN ),
							'name'  => 'new_referral_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify employer when freelancer review for employer.", ET_DOMAIN ),
						'id'     => 'review-for-employer',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to employer when freelancer reviews and rate for him", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'review_for_employer_mail_template',
							'type'  => 'editor',
							'title' => __( "Notify employer when freelancer review for employer.", ET_DOMAIN ),
							'name'  => 'review_for_employer_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "New Project Mail Template", ET_DOMAIN ),
						'id'     => 'new-project-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to freelancers when a new project which related to his profile category is posted.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'new_project_mail_template',
							'type'  => 'editor',
							'title' => __( "New Project Mail", ET_DOMAIN ),
							'name'  => 'new_project_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Approved Payment Notification Mail Template", ET_DOMAIN ),
						'id'     => 'approved-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to the freelancer after admin reviews & approved the payment.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'approved_payment_mail_template',
							'type'  => 'editor',
							'title' => __( "Approved Payment Notification Mail", ET_DOMAIN ),
							'name'  => 'approved_payment_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Project Report Mail Template", ET_DOMAIN ),
						'id'    => 'mail-description-group',
						'class' => '',
						'name'  => ''
					],
					'fields' => [
						[
							'id'    => 'mail-description',
							'type'  => 'desc',
							'title' => __( "Mail description here", ET_DOMAIN ),
							'text'  => __( "Email templates used for project-report event. You can use placeholders to include some specific content", ET_DOMAIN ),
							'class' => '',
							'name'  => 'mail_description'
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Project was Reported by Employer", ET_DOMAIN ),
						'id'     => 'employer-report-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to freelancer when employer sends a report on the project.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'employer_report_mail_template',
							'type'  => 'editor',
							'title' => __( "Employer Report  Mail", ET_DOMAIN ),
							'name'  => 'employer_report_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				//admin_report_mail_template
				[
					'args'   => [
						'title'  => __( "Notify admin when employer closed the project", ET_DOMAIN ),
						'id'     => 'admin-new-report-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to admin when employer closed his project.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'admin_report_mail_template',
							'type'  => 'editor',
							'title' => __( "Notify admin when employer closed the project", ET_DOMAIN ),
							'name'  => 'admin_report_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify freelancer when employer closed the project", ET_DOMAIN ),
						'id'     => 'employer-close-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to admin when employer closed the project.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'employer_close_mail_template',
							'type'  => 'editor',
							'title' => __( "Notify freelancer when employer closed the project", ET_DOMAIN ),
							'name'  => 'employer_close_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				//admin_report_mail_template
				[
					'args'   => [
						'title'  => __( "Notify admin when freelancer discontinues the project", ET_DOMAIN ),
						'id'     => 'admin-report-freelancer-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to admin when freelancer discontinues his working project.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'admin_report_freelancer_mail_template',
							'type'  => 'editor',
							'title' => __( "Notify admin when freelancer discontinues the project", ET_DOMAIN ),
							'name'  => 'admin_report_freelancer_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify employer when freelancer discontinues the project", ET_DOMAIN ),
						'id'     => 'freelancer-quit-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to employer when freelancer discontinues the project", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'freelancer_quit_mail_template',
							'type'  => 'editor',
							'title' => __( "Notify employer when freelancer discontinues the project", ET_DOMAIN ),
							'name'  => 'freelancer_quit_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Project Reported by Freelancer", ET_DOMAIN ),
						'id'     => 'freelancer-report-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to employer when freelancer sends a report on project.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'freelancer_report_mail_template',
							'type'  => 'editor',
							'title' => __( "Freelancer Report  Mail", ET_DOMAIN ),
							'name'  => 'freelancer_report_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Admin Refunded The Payment", ET_DOMAIN ),
						'id'     => 'admin-refund-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to users when admin refunds the escrow payment to the project's owner.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'fre_refund_mail_template',
							'type'  => 'editor',
							'title' => __( "Admin Refund Payment", ET_DOMAIN ),
							'name'  => 'fre_refund_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Bid Cancel Mail Template", ET_DOMAIN ),
						'id'     => 'bid-cancel-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to employer when freelancer cancels a bid.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'bid_cancel_mail_template',
							'type'  => 'editor',
							'title' => __( "Bid Cancel Mail", ET_DOMAIN ),
							'name'  => 'bid_cancel_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Payment Related Mail Template", ET_DOMAIN ),
						'id'    => 'mail-description-group',
						'class' => '',
						'name'  => ''
					],
					'fields' => [
						[
							'id'    => 'mail-description',
							'type'  => 'desc',
							'title' => __( "Payment Related Mail Template", ET_DOMAIN ),
							'text'  => __( "Email templates used for payment process. You can use placeholders to include some specific content", ET_DOMAIN ),
							'class' => '',
							'name'  => 'mail_description'
						]
					]
				],
				// array(
				//     'args' => array(
				//         'title' => __("Admin Executed The Payment", ET_DOMAIN) ,
				//         'id' => 'admin-execute-payment-mail',
				//         'class' => 'payment-gateway',
				//         'name' => '',
				//         'desc' => __("Send to user when admin executes the escrow payment and send to the freelancer.", ET_DOMAIN),
				//         'toggle' => true
				//     ) ,
				//     'fields' => array(
				//         array(
				//             'id' => 'fre_execute_mail_template',
				//             'type' => 'editor',
				//             'title' => __("Admin Execute Payment Mail", ET_DOMAIN) ,
				//             'name' => 'fre_execute_mail_template',
				//             'class' => '',
				//             'reset' => 1
				//         )
				//     )
				// ),
				[
					'args'   => [
						'title'  => __( "Notify employer when the payment is sent", ET_DOMAIN ),
						'id'     => 'admin-execute-to-employer-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to the employer when admin sends money to freelancer.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'fre_execute_to_employer_mail_template',
							'type'  => 'editor',
							'title' => __( "Notify employer when the payment is sent", ET_DOMAIN ),
							'name'  => 'fre_execute_to_employer_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify freelancer when the payment is sent.", ET_DOMAIN ),
						'id'     => 'admin-execute-to-freelancer-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to the freelancer when admin sends money to him.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'fre_execute_to_freelancer_mail_template',
							'type'  => 'editor',
							'title' => __( "Notify freelancer when the payment is sent.", ET_DOMAIN ),
							'name'  => 'fre_execute_to_freelancer_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify employer when the payment is sent - Disable manual transfer.", ET_DOMAIN ),
						'id'     => 'notify-employer-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to employer when he finishes his project, the payment is successful sent.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'fre_notify_employer_mail_template',
							'type'  => 'editor',
							'title' => __( "Notify employer when the payment is sent - Disable manual transfer.", ET_DOMAIN ),
							'name'  => 'fre_notify_employer_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify freelancer when the payment is sent - Disable manual transfer.", ET_DOMAIN ),
						'id'     => 'notify-freelancer-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to freelancer when employer finishes his project, the payment is successful sent.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'fre_notify_freelancer_mail_template',
							'type'  => 'editor',
							'title' => __( "Notify freelancer when the payment is sent - Disable manual transfer.", ET_DOMAIN ),
							'name'  => 'fre_notify_freelancer_mail_template',
							'class' => '',
							'reset' => 1
						]
					]
				],

				[
					'args'   => [
						'title'  => __( "Notify employer about dispute resolution.", ET_DOMAIN ),
						'id'     => 'notify-freelancer-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to employer when admin arbitrates employer as a winner.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'fre_notify_employer_when_employer_win',
							'type'  => 'editor',
							'title' => __( "Notify employer about dispute resolution.", ET_DOMAIN ),
							'name'  => 'fre_notify_employer_when_employer_win',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify freelancer about dispute resolution.", ET_DOMAIN ),
						'id'     => 'notify-freelancer-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send freelancer when admin arbitrates employer as a winner.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'fre_notify_freelancer_when_employer_win',
							'type'  => 'editor',
							'title' => __( "Notify freelancer about dispute resolution.", ET_DOMAIN ),
							'name'  => 'fre_notify_freelancer_when_employer_win',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify employer about dispute resolution.", ET_DOMAIN ),
						'id'     => 'notify-freelancer-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to employer when admin arbitrates freelancer as a winner.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'fre_notify_employer_when_freelancer_win',
							'type'  => 'editor',
							'title' => __( "Notify employer about dispute resolution.", ET_DOMAIN ),
							'name'  => 'fre_notify_employer_when_freelancer_win',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify freelancer when admin arbitrates him as a winner.", ET_DOMAIN ),
						'id'     => 'notify-freelancer-payment-mail',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to freelancer when admin arbitrates freelancer as a winner.", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'fre_notify_freelancer_when_freelancer_win',
							'type'  => 'editor',
							'title' => __( "Notify freelancer when admin arbitrates him as a winner.", ET_DOMAIN ),
							'name'  => 'fre_notify_freelancer_when_freelancer_win',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify when PRO status is activated", ET_DOMAIN ),
						'id'     => 'bay_pro_status_template',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to notify when PRO is purchased", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'bay_pro_status_template',
							'type'  => 'editor',
							'title' => __( "Notify when PRO status is activated", ET_DOMAIN ),
							'name'  => 'bay_pro_status_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify when PRO status is expired", ET_DOMAIN ),
						'id'     => 'expired_pro_status_template',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to notify when PRO is expired", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'expired_pro_status_template',
							'type'  => 'editor',
							'title' => __( "Notify when PRO status is expired", ET_DOMAIN ),
							'name'  => 'expired_pro_status_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify when review is activated", ET_DOMAIN ),
						'id'     => 'bay_review_template',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to notify when peview is paid", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'bay_review_template',
							'type'  => 'editor',
							'title' => __( "Notify when review is activated", ET_DOMAIN ),
							'name'  => 'bay_review_template',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify when SafePay deal deposit is received", ET_DOMAIN ),
						'id'     => 'bay_pay_bid',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to notify when SafePay deal deposit is received", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'bay_pay_bid',
							'type'  => 'editor',
							'title' => __( "Notify when SafePay deal deposit is received", ET_DOMAIN ),
							'name'  => 'bay_pay_bid',
							'class' => '',
							'reset' => 1
						]
					]
				],
				[
					'args'   => [
						'title'  => __( "Notify when payment code is issued", ET_DOMAIN ),
						'id'     => 'notify_payment_code',
						'class'  => 'payment-gateway',
						'name'   => '',
						'desc'   => __( "Send to notify when payment code is issued", ET_DOMAIN ),
						'toggle' => true
					],
					'fields' => [
						[
							'id'    => 'notify_payment_code',
							'type'  => 'editor',
							'title' => __( "Notify when payment code is issued", ET_DOMAIN ),
							'name'  => 'notify_payment_code',
							'class' => '',
							'reset' => 1
						]
					]
				],
			]
		];

		/**
		 * language settings
		 */
		$sections['language'] = [
			'args' => [
				'title' => __( "Language", ET_DOMAIN ),
				'id'    => 'language-settings',
				'icon'  => 'G',
				'class' => ''
			],

			'groups' => [
				[
					'args'   => [
						'title' => __( "Website Language", ET_DOMAIN ),
						'id'    => 'website-language',
						'class' => '',
						'name'  => '',
						'desc'  => __( "Select the language you want to use for your website.", ET_DOMAIN )
					],
					'fields' => [
						[
							'id'    => 'forgotpass_mail_template',
							'type'  => 'language_list',
							'title' => __( "Register Mail", ET_DOMAIN ),
							'name'  => 'website_language',
							'class' => ''
						]
					]
				],
				[
					'args'   => [
						'title' => __( "Translator", ET_DOMAIN ),
						'id'    => 'translator',
						'class' => '',
						'name'  => 'translator',
						'desc'  => __( "Translate a language", ET_DOMAIN )
					],
					'fields' => [
						[
							'id'    => 'translator-field',
							'type'  => 'translator',
							'title' => __( "Register Mail", ET_DOMAIN ),
							'name'  => 'translate',
							'class' => ''
						]
					]
				]
			]
		];

		/**
		 * license key settings
		 */
		$sections['update'] = [
			'args' => [
				'title' => __( "Update", ET_DOMAIN ),
				'id'    => 'update-settings',
				'icon'  => '~',
				'class' => ''
			],

			'groups' => [
				[
					'args'   => [
						'title' => __( "License Key", ET_DOMAIN ),
						'id'    => 'license-key',
						'class' => '',
						'desc'  => ''
					],
					'fields' => [
						[
							'id'    => 'et_license_key',
							'type'  => 'text',
							'title' => __( "License Key", ET_DOMAIN ),
							'name'  => 'et_license_key',
							'class' => ''
						]
					]
				]
			]
		];

		$temp    = [];
		$options = AE_Options::get_instance();

		foreach ( $sections as $key => $section ) {
			$temp[] = new AE_section( $section['args'], $section['groups'], $options );
		}

		$pages = [];

		/**
		 * overview container
		 */
		$container = new AE_Overview( [
			PROFILE,
			PROJECT
		], true );

		//$statics      =   array();
		// $header      =   new AE_Head( array( 'page_title'    => __('Overview', ET_DOMAIN),
		//                                  'menu_title'    => __('OVERVIEW', ET_DOMAIN),
		//                                  'desc'          => __("Overview", ET_DOMAIN) ) );
		$pages['overview'] = [
			'args'      => [
				'parent_slug' => 'et-overview',
				'page_title'  => __( 'Overview', ET_DOMAIN ),
				'menu_title'  => __( 'OVERVIEW', ET_DOMAIN ),
				'cap'         => 'administrator',
				'slug'        => 'et-overview',
				'icon'        => 'L',
				'desc'        => sprintf( __( "%s overview", ET_DOMAIN ), $options->blogname )
			],
			'container' => $container,

			// 'header' => $header


		];

		/**
		 * setting view
		 */
		$container         = new AE_Container( [
			'class' => '',
			'id'    => 'settings'
		], $temp, '' );
		$pages['settings'] = [
			'args'      => [
				'parent_slug' => 'et-overview',
				'page_title'  => __( 'Settings', ET_DOMAIN ),
				'menu_title'  => __( 'SETTINGS', ET_DOMAIN ),
				'cap'         => 'administrator',
				'slug'        => 'et-settings',
				'icon'        => 'y',
				'desc'        => __( "Manage how your FreelanceEngine looks and feels", ET_DOMAIN )
			],
			'container' => $container
		];

		/**
		 * user list view
		 */

		$container        = new AE_UsersContainer( [
			'filter' => [
				'moderate'
			]
		] );
		$pages['members'] = [
			'args'      => [
				'parent_slug' => 'et-overview',
				'page_title'  => __( 'Members', ET_DOMAIN ),
				'menu_title'  => __( 'MEMBERS', ET_DOMAIN ),
				'cap'         => 'administrator',
				'slug'        => 'et-users',
				'icon'        => 'g',
				'desc'        => __( "Overview of registered members", ET_DOMAIN )
			],
			'container' => $container
		];

		/**
		 * order list view
		 */
		$orderlist         = new AE_OrderList( [] );
		$pages['payments'] = [
			'args'      => [
				'parent_slug' => 'et-overview',
				'page_title'  => __( 'Payments', ET_DOMAIN ),
				'menu_title'  => __( 'PAYMENTS', ET_DOMAIN ),
				'cap'         => 'administrator',
				'slug'        => 'et-payments',
				'icon'        => '%',
				'desc'        => __( "Overview of all payments", ET_DOMAIN )
			],
			'container' => $orderlist
		];

		/**
		 * setup wizard view
		 */

		$container = new AE_Wizard();
		$pages[]   = [
			'args'      => [
				'parent_slug' => 'et-overview',
				'page_title'  => __( 'Setup Wizard', ET_DOMAIN ),
				'menu_title'  => __( 'SETUP WIZARD', ET_DOMAIN ),
				'cap'         => 'administrator',
				'slug'        => 'et-wizard',
				'icon'        => 'S',
				'desc'        => __( "Set up and manage every content of your site", ET_DOMAIN )
			],
			'container' => $container
		];


		/**
		 *  filter pages config params so user can hook to here
		 */
		$pages = apply_filters( 'ae_admin_menu_pages', $pages );

		/**
		 * add menu page
		 */
		$this->admin_menu = new AE_Menu( $pages );

		/**
		 * add sub menu page
		 */
		foreach ( $pages as $key => $page ) {
			new AE_Submenu( $page, $pages );
		}
	}
}

