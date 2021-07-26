<?php

/**
 * class Mailing control mail options
 */
Class Fre_Mailing extends AE_Mailing {

	public static $instance;

	static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new Fre_Mailing();
		}

		return self::$instance;
	}

	/**
	 * Send to the admin when employer re-submits his rejected project
	 *
	 * @param int $post_id
	 */
	function review_resubmmit_mail( $post_id ) {
		$mail      = ae_get_option( 'new_post_alert', '' ) ? ae_get_option( 'new_post_alert', '' ) : get_option( 'admin_email' );
		$post      = get_post( $post_id );
		$user_data = get_userdata( $post->post_author );

		$subject = __( "A new re-submitted project.", ET_DOMAIN );
		$message = ae_get_option( 'ae_resubmitted_project_mail' );
		$message = str_replace( '[display_name]', $user_data->display_name, $message );
		$message = str_replace( '[link]', "<a href='" . get_permalink( $post_id ) . "' target='_Blank' rel='noopener noreferrer'>" . get_the_title( $post_id ) . "</a>", $message );

		$this->wp_mail( $mail, $subject, $message, [
			'post' => $post_id
		], '' );
	}

	function approved_payment_notification( $order_id, $pack ) {
		$order          = get_post( $order_id );
		$user           = get_userdata( $order->post_author );
		$payment_method = get_post_meta( $order_id, 'et_order_gateway', true );

		$subject = __( 'Your payment has been successfully processed!', ET_DOMAIN );
		$message = ae_get_option( 'approved_payment_mail_template' );

		$message = str_replace( '[package_name]', $pack->post_title, $message );
		$message = str_replace( '[display_name]', $user->display_name, $message );
		$message = str_replace( '[user_email]', $user->user_email, $message );
		$message = str_replace( '[invoice_id]', $order_id, $message );
		$message = str_replace( '[date]', date( get_option( 'date_format' ), time() ), $message );
		$message = str_replace( '[payment]', $payment_method, $message );
		$message = str_replace( '[total]', number_format( $pack->et_price, 2 ), $message );
		$message = str_replace( '[currency]', ae_currency_code( false ), $message );
		$message = str_replace( '[blogname]', get_bloginfo( 'name' ), $message );
		$this->wp_mail( $user->user_email, $subject, $message, [
			'user_id' => $order->post_author,
		], '' );

	}

	/**
	 * bid_mail Mail to author's project know have a freelance has bided on their project.
	 *
	 * @param  [type] $new_status [description]
	 * @param  [type] $old_status [description]
	 */
	function new_payment_notification( $order_id ) {
		$mail    = ae_get_option( 'new_post_alert', '' ) ? ae_get_option( 'new_post_alert', '' ) : get_option( 'admin_email' );
		$subject = __( "A new payment notification.", ET_DOMAIN );
		$message = ae_get_option( 'new_payment_mail_template' );

		$order  = get_post( $order_id );
		$author = get_user_by( 'id', $order->post_author );

		$product = current( get_post_meta( $order_id, 'et_order_products', true ) );

		$message = apply_filters( 'mail_notify_admin_has_new_payment', $message, $product, $order ); // from 1.8.6

		$message = str_replace( '[package_name]', $product['NAME'], $message );
		$message = str_replace( '[user_name]', $author->display_name, $message );
		$message = str_replace( '[display_name]', $author->display_name, $message );
		$message = str_replace( '[blogname]', get_bloginfo( 'blogname' ), $message );

		$this->wp_mail( $mail, $subject, $message, '', '' );
	}

	/**
	 * Email to author's project
	 */
	function bid_mail( $bid_id ) {

		$project_id  = get_post_field( 'post_parent', $bid_id );
		$post_author = get_post_field( 'post_author', $project_id );
		$author      = get_userdata( $post_author );
		if ( $author ) {
			$message = ae_get_option( 'bid_mail_template' );
			$bid_msg = get_post_field( 'post_content', $bid_id );
			$message = str_replace( '[message]', $bid_msg, $message );
			$subject = sprintf( __( "Your project posted on %s has a new bid.", ET_DOMAIN ), get_option( 'blogname' ) );

			return $this->wp_mail( $author->user_email, $subject, $message, [
				'post'    => $project_id,
				'user_id' => $post_author
			], '' );
		}

		return false;
	}

	/**
	 * bid_cancel mail Mail to author's project know have a freelance has bided on their project.
	 *
	 * @param  [type] $new_status [description]
	 * @param  [type] $old_status [description]
	 */
	function bid_cancel_mail( $project_id ) {

		$post_author = get_post_field( 'post_author', $project_id );
		$author      = get_userdata( $post_author );
		if ( $author ) {
			$message = ae_get_option( 'bid_cancel_mail_template' );
			$subject = sprintf( __( "There is a Freelancer canceled a bid on Your project %s.", ET_DOMAIN ), get_option( 'blogname' ) );

			return $this->wp_mail( $author->user_email, $subject, $message, [
				'post'    => $project_id,
				'user_id' => $post_author
			], '' );
		}

		return false;
	}

	/**
	 * employer complete a job and send mail to freelancer joined project
	 *
	 * @param integer $project_id The project id
	 *
	 * @since  1.0
	 * @author Dan
	 */
	function review_freelancer_email( $project_id ) {
		$post = get_post( $project_id );

		$employer  = get_the_author_meta( 'display_name', $post->post_author );
		$link      = esc_url( add_query_arg( 'workspace', '1', get_permalink( $project_id ) ) );
		$post_link = '<a href="' . $link . '" >' . $post->post_title . '</a>';

		$message = ae_get_option( 'complete_mail_template' );
		$message = str_replace( '[link_review]', $post_link, $message );
		$message = str_replace( '[employer]', $employer, $message );

		$subject = __( "Your project has been finished.", ET_DOMAIN );

		$bid_id        = get_post_meta( $project_id, 'accepted', true );
		$freelancer_id = get_post_field( 'post_author', $bid_id );
		$author        = get_userdata( $freelancer_id );

		$this->wp_mail( $author->user_email, $subject, $message, [
			'post'    => $project_id,
			'user_id' => $freelancer_id
		], '' );


		return $author;
	}

	/**
	 * employer replied to freelancers review
	 *
	 * @param integer $project_id
	 *
	 * @since  1.0
	 * @author Artem
	 */
	function reply_to_review_email( $project_id ) {
		$post = get_post( $project_id );

		$link          = esc_url( add_query_arg( 'workspace', '1', get_permalink( $project_id ) ) );
		$post_link     = '<a href="' . $link . '" >' . $post->post_title . '</a>';
		$bid_id        = get_post_meta( $project_id, 'accepted', true );
		$freelancer_id = get_post_field( 'post_author', $bid_id );
		$employer      = get_the_author_meta( 'display_name', $post->post_author );
		$freelancer    = get_the_author_meta( 'display_name', $freelancer_id );
		$author        = get_userdata( $freelancer_id );

		$message = ae_get_option( 'reply_to_review_template' );
		$message = str_replace( '[link_review]', $post_link, $message );
		$message = str_replace( '[employer]', $employer, $message );
		$message = str_replace( '[display_name]', $freelancer, $message );
		$message = str_replace( '[blogname]', get_bloginfo( 'name' ), $message );

		$subject = __( $freelancer . " replied to your review", ET_DOMAIN );

		$this->wp_mail( $author->user_email, $subject, $message, [
			'post'    => $project_id,
			'user_id' => $freelancer_id
		], '' );

		return $author;
	}

	/**
	 * employer complete a job and send mail to freelancer joined project
	 *
	 * @param integer $project_id The project id
	 *
	 * @since  1.0
	 * @author Dan
	 */
	function review_employer_email( $project_id ) {
		$subject = __( "New review from a Professional", ET_DOMAIN );

		$message = ae_get_option( 'review_for_employer_mail_template' );

		$bid_id        = get_post_meta( $project_id, 'accepted', true );
		$freelancer_id = get_post_field( 'post_author', $bid_id );
		$employer      = get_the_author_meta( 'display_name', $freelancer_id );
		// desktop
		$link_profile = '<a href="' . et_get_page_link( 'profile' ) . '">' . et_get_page_link( 'profile' ) . '</a>';

		$link          = esc_url( add_query_arg( 'workspace', '1', get_permalink( $project_id ) ) );
		$project_title = get_post_field( 'post_title', $project_id );
		$project_link  = '<a href="' . $link . '" >' . $project_title . '</a>';

		$message = str_replace( '[link_review]', $project_link, $message );
		$message = str_replace( '[freelance]', $employer, $message );
		$message = str_replace( '[link_profile]', $link_profile, $message );

		$employer_id = get_post_field( 'post_author', $project_id );
		$author      = get_userdata( $employer_id );
		$this->wp_mail( $author->user_email, $subject, $message, [
			'post'    => $project_id,
			'user_id' => $employer_id
		], '' );

		return $author;
	}

	/**
	 * invite a freelancer to work on current user project
	 *
	 * @param int $user_id The user will be invite
	 * @param int $project_id The project will be send
	 *
	 * @since  1.0
	 * @author Dakachi
	 */
	function invite_mail( $user_id, $project_id ) {
		global $current_user, $user_ID;
		if ( $user_id && $project_id ) {

			// $user = new WP_User($user_id);
			// get user email
			$user_email = get_the_author_meta( 'user_email', $user_id );

			// mail subject
			$subject = sprintf( __( "You have a new invitation to join project from %s.", ET_DOMAIN ), get_option( 'blogname' ) );

			// build list of project send to freelancer
			$project_info = '';
			foreach ( $project_id as $key => $value ) {
				// check invite this project or not
				if ( fre_check_invited( $user_id, $value ) ) {
					continue;
				}
				$project_link = get_permalink( $value );
				$project_tile = get_the_title( $value );
				// create a invite message
				fre_create_invite( $user_id, $value );

				$project_info .= '<li><p>' . $project_tile . '</p><p>' . $project_link . '</p></li>';
			}

			if ( $project_info == '' ) {
				return false;
			}
			$project_info = '<ul>' . $project_info . '</ul>';

			// get mail template
			$message = '';
			if ( ae_get_option( 'invite_mail_template' ) ) {
				$message = ae_get_option( 'invite_mail_template' );
			}

			// replace project list by placeholder
			$message = str_replace( '[project_list]', $project_info, $message );

			// send mail
			return $this->wp_mail( $user_email, $subject, $message, [
				'user_id' => $user_id,
				'post'    => $value
			], '' );
		}
	}

	/**
	 * send email to freelancer if his/her bid is accepted by employer
	 * use mail template bid_accepted_template
	 *
	 * @param int $freelancer_id
	 * @param int $project_id
	 *
	 * @since  1.1
	 * @author Dakachi
	 */
	function bid_accepted( $freelancer_id, $project_id ) {
		$user_email = get_the_author_meta( 'user_email', $freelancer_id );

		// mail subject
		$subject = sprintf( __( "Your bid on project %s has been accepted.", ET_DOMAIN ), get_the_title( $project_id ) );

		// get mail template
		$message = '';
		if ( ae_get_option( 'bid_accepted_template' ) ) {
			$message = ae_get_option( 'bid_accepted_template' );
		}

		$workspace_link = add_query_arg( [
			'workspace' => 1
		], get_permalink( $project_id ) );

		$workspace_link = '<a href="' . $workspace_link . '">' . $workspace_link . '</a>';
		$message        = str_replace( '[workspace]', $workspace_link, $message );

		return $this->wp_mail( $user_email, $subject, $message, [
			'user_id' => $freelancer_id,
			'post'    => $project_id
		], '' );
	}

	/**
	 * Send to the freelancers after employer accepted a bid from an alternative freelancer
	 * use mail template bid_accepted_alternative_template
	 *
	 * @param int $freelancer_id
	 * @param int $project_id
	 *
	 * @since  1.1
	 * @author ThanhTu
	 */
	function bid_accepted_alternative( $freelancer_id, $project_id ) {
		// get mail template
		if ( ae_get_option( 'bid_accepted_alternative_template' ) ) {
			$message = ae_get_option( 'bid_accepted_alternative_template' );
		} else {
			$et      = new ET_Admin();
			$message = $et->get_template_default_options( 'bid_accepted_alternative_template' );
		}

		$project  = get_post( $project_id );
		$employer = get_the_author_meta( 'display_name', $project->post_author );
		$message  = str_replace( '[employer]', $employer, $message );

		// Get list bid project
		$q_bid = new WP_Query( [
			'post_type'   => BID,
			'post_parent' => $project_id,
			'post_status' => [ 'publish', 'complete', 'accept', 'unaccept' ]
		] );

		// mail subject
		$subject = __( "Thank you for submitting your bid.", ET_DOMAIN );

		// send mail only for declined freelancers
		if ( $q_bid->have_posts() ) {
			foreach ( $q_bid->posts as $post ) {
				if ( $post->post_author == $freelancer_id ) {
					continue;
				}

				$user_email = get_the_author_meta( 'user_email', $post->post_author );

				$sent = $this->wp_mail( $user_email, $subject, $message, [
					'user_id' => $post->post_author,
					'post'    => $project_id
				], '' );
			}

			return $sent;
		}
	}

	/**
	 * send email to employer when have new message
	 *
	 * @param int $receiver the user will receive email
	 * @param int $project the project id message send base on
	 * @param string $message the message content
	 *
	 * @since  1.2
	 * @author Dakachi
	 */
	function new_message( $receiver, $project, $message ) {
		$user_email = get_the_author_meta( 'user_email', $receiver );

		// mail subject
		$subject        = sprintf( __( "You have a new message on %s workspace.", ET_DOMAIN ), get_the_title( $project ) );
		$workspace_link = add_query_arg( [
			'workspace' => 1
		], get_permalink( $project ) );

		$mail_template = ae_get_option( 'new_message_mail_template' );

		// replace message content place holder
		$mail_template = str_replace( '[message]', $message->comment_content, $mail_template );

		// replace workspace place holder
		$workspace_link = '<a href="' . $workspace_link . '">' . __( "Workspace", ET_DOMAIN ) . '</a>';
		$mail_template  = str_replace( '[workspace]', $workspace_link, $mail_template );

		// send mail
		return $this->wp_mail( $user_email, $subject, $mail_template, [
			'user_id' => $receiver,
			'post'    => $project
		], '' );
	}

	/**
	 * send email to 3 user admin, employer, or freelancer when have a new report ignore current user
	 *
	 * @param Int $project_id The project id was reported
	 * @param Object $report The object contain report content
	 *
	 * @since  1.3
	 * @author Dakachi
	 */
	function new_report( $project_id, $report ) {
		global $user_ID;
		$project = get_post( $project_id );

		// email subject
		$subject = sprintf( __( "Have a new report on project %s.", ET_DOMAIN ), get_the_title( $project_id ) );

		if ( $project->post_author == $user_ID ) {

			// mail to freelancer when project owner send a report
			$mail_template = ae_get_option( 'employer_report_mail_template' );
			$mail_template = str_replace( '[reporter]', get_the_author_meta( 'display_name', $user_ID ), $mail_template );

			$bid_id     = get_post_meta( $project_id, 'accepted', true );
			$bid_author = get_post_field( 'post_author', $bid_id );
			$user_email = get_the_author_meta( 'user_email', $bid_author );
			$receiver   = $bid_author;
		} else {

			// mail to employer when freelancer working on project send a new report
			$mail_template = ae_get_option( 'freelancer_report_mail_template' );
			$mail_template = str_replace( '[reporter]', get_the_author_meta( 'display_name', $user_ID ), $mail_template );

			$user_email = get_the_author_meta( 'user_email', $project->post_author );
			$receiver   = $project->post_author;
		}

		$workspace_link = add_query_arg( [
			'workspace' => 1
		], get_permalink( $project_id ) );

		$workspace_link = '<a href="' . $workspace_link . '">' . __( "Workspace", ET_DOMAIN ) . '</a>';
		// replace workspace place holder
		$mail_template = str_replace( '[workspace]', $workspace_link, $mail_template );

		// mail to admin
		$admin_template = ae_get_option( 'admin_report_mail_template' );
		$admin_template = str_replace( '[reporter]', get_the_author_meta( 'display_name', $user_ID ), $admin_template );

		// send mail to freelancer / employer
		$this->wp_mail( $user_email, $subject, $mail_template, [
			'user_id' => $receiver,
			'post'    => $project_id
		], '' );

		// send mail to admin
		$this->wp_mail( get_option( 'admin_email' ), $subject, $admin_template, [
			'user_id' => 1,
			'post'    => $project_id
		], '' );
	}

	/**
	 * send email to freelancer, admin when employer request close project
	 *
	 * @param snippet
	 *
	 * @since  1.3
	 * @author Dakachi
	 */
	function close_project( $project_id, $message ) {
		global $user_ID;
		$project    = get_post( $project_id );
		$employer   = get_the_author_meta( 'display_name', $project->post_author );
		$bid_id     = get_post_meta( $project_id, 'accepted', true );
		$bid_author = get_post_field( 'post_author', $bid_id );

		// mail to freelancer when project owner send a report
		$mail_template_freelancer = ae_get_option( 'employer_close_mail_template' );
		$mail_template_freelancer = str_replace( '[employer]', $employer, $mail_template_freelancer );
		$subject_freelancer       = __( "A new dispute on your working project", ET_DOMAIN );
		$user_email_freelancer    = get_the_author_meta( 'user_email', $bid_author );
		$this->wp_mail( $user_email_freelancer, $subject_freelancer, $mail_template_freelancer, [
			'user_id' => $bid_author,
			'post'    => $project_id
		], '' );
		// send mail to freelancer / employer

		// mail to admin
		$mail_template_admin = ae_get_option( 'admin_report_mail_template' );
		$mail_template_admin = str_replace( '[employer]', $employer, $mail_template_admin );
		$subject_admin       = __( 'A new dispute project on your site', ET_DOMAIN );

		$this->wp_mail( get_option( 'admin_email' ), $subject_admin, $mail_template_admin, [
			'user_id' => 1,
			'post'    => $project_id
		], '' );
		// send mail to admin
	}

	/**
	 * send email to employer, admin when freelancer request close project
	 *
	 * @param snippet
	 *
	 * @since  1.3
	 * @author Dakachi
	 */
	function quit_project( $project_id, $message ) {
		global $user_ID;
		$project    = get_post( $project_id );
		$bid_id     = get_post_meta( $project_id, 'accepted', true );
		$bid_author = get_post_field( 'post_author', $bid_id );
		$employer   = get_the_author_meta( 'display_name', $project->post_author );
		$freelancer = get_the_author_meta( 'display_name', $bid_author );

		// mail to employer when freelancer working on project send a new report
		$subject_freelancer       = __( "A new dispute on your project", ET_DOMAIN );
		$mail_template_freelancer = ae_get_option( 'freelancer_quit_mail_template' );
		$mail_template_freelancer = str_replace( '[freelancer]', $freelancer, $mail_template_freelancer );
		$user_email               = get_the_author_meta( 'user_email', $project->post_author );
		$this->wp_mail( $user_email, $subject_freelancer, $mail_template_freelancer, [
			'user_id' => $project->post_author,
			'post'    => $project_id
		], '' );
		// send mail to employer

		// mail to admin
		$subject_admin       = __( 'A new dispute project on your site', ET_DOMAIN );
		$mail_template_admin = ae_get_option( 'admin_report_freelancer_mail_template' );
		$mail_template_admin = str_replace( '[freelancer]', $freelancer, $mail_template_admin );
		$this->wp_mail( get_option( 'admin_email' ), $subject_admin, $mail_template_admin, [
			'user_id' => 1,
			'post'    => $project_id
		], '' );
		// send mail to admin
	}

	/**
	 * send mail to employer, freelancer when admin decide dispute process
	 *
	 * @param
	 *
	 * @since  1.3
	 * @author ThanhTu
	 */
	function execute_payment( $project_id, $bid_accepted ) {
		$project_owner            = get_post_field( 'post_author', $project_id );
		$bid_owner                = get_post_field( 'post_author', $bid_accepted );
		$mail_template_employer   = ae_get_option( 'fre_notify_employer_when_freelancer_win' );
		$mail_template_freelancer = ae_get_option( 'fre_notify_freelancer_when_freelancer_win' );
		if ( ! $mail_template_employer || ! $mail_template_freelancer ) {
			return;
		}
		$employer   = get_the_author_meta( 'display_name', $project_owner );
		$freelancer = get_the_author_meta( 'display_name', $bid_owner );

		// Mail to project owner
		$subject_employer       = __( 'Final result of the dispute on your project', ET_DOMAIN );
		$mail_template_employer = str_replace( '[freelancer]', $freelancer, $mail_template_employer );
		$employer_email         = get_the_author_meta( 'user_email', $project_owner );
		$this->wp_mail( $employer_email, $subject_employer, $mail_template_employer, [
			'user_id' => $project_owner,
			'post'    => $project_id
		], '' );
		// Mail to project owner

		// Mail to freelancer
		$subject_freelancer = __( 'Final result of the dispute on your working project', ET_DOMAIN );
		$freelancer_email   = get_the_author_meta( 'user_email', $bid_owner );
		$this->wp_mail( $freelancer_email, $subject_freelancer, $mail_template_freelancer, [
			'user_id' => $bid_owner,
			'post'    => $project_id
		], '' );
		// Mail to freelancer
	}

	/**
	 * send mail to employer, freelancer when admin decide dispute process
	 *
	 * @param
	 *
	 * @since  1.3
	 * @author ThanhTu
	 */
	function refund( $project_id, $bid_accepted ) {
		$project_owner = get_post_field( 'post_author', $project_id );
		$bid_owner     = get_post_field( 'post_author', $bid_accepted );

		$mail_template_employer   = ae_get_option( 'fre_notify_employer_when_employer_win' );
		$mail_template_freelancer = ae_get_option( 'fre_notify_freelancer_when_employer_win' );
		if ( ! $mail_template_employer || ! $mail_template_freelancer ) {
			return;
		}

		$employer   = get_the_author_meta( 'display_name', $project_owner );
		$freelancer = get_the_author_meta( 'display_name', $bid_owner );

		// Mail to project owner
		$subject_employer = __( 'Final result of the dispute on your project', ET_DOMAIN );
		$employer_email   = get_the_author_meta( 'user_email', $project_owner );
		$this->wp_mail( $employer_email, $subject_employer, $mail_template_employer, [
			'user_id' => $project_owner,
			'post'    => $project_id
		], '' );
		// Mail to project owner

		// Mail to freelancer
		$subject_freelancer       = __( 'Final result of the dispute on your working project', ET_DOMAIN );
		$mail_template_freelancer = str_replace( '[employer]', $employer, $mail_template_freelancer );
		$freelancer_email         = get_the_author_meta( 'user_email', $bid_owner );
		$this->wp_mail( $freelancer_email, $subject_freelancer, $mail_template_freelancer, [
			'user_id' => $bid_owner,
			'post'    => $project_id
		], '' );
		// Mail to freelancer
	}

	/**
	 * Notify freelancer, employer when the payment is sent - Disable manual transfer
	 *
	 * @param
	 *
	 * @since  1.3
	 * @author ThanhTu
	 */
	function notify_execute( $project_id, $bid_accepted ) {
		$project_owner = get_post_field( 'post_author', $project_id );
		$bid_owner     = get_post_field( 'post_author', $bid_accepted );

		$employer     = get_the_author_meta( 'display_name', $project_owner );
		$freelancer   = get_the_author_meta( 'display_name', $bid_owner );
		$link_project = '<a rel="nofollow" href="' . get_permalink( $project_id ) . '">' . get_permalink( $project_id ) . '</a>';

		// Mail to Employer
		$subject_employer       = __( 'Your project is completed', ET_DOMAIN );
		$mail_template_employer = ae_get_option( 'fre_notify_employer_mail_template' );
		$mail_template_employer = str_replace( '[freelancer]', $freelancer, $mail_template_employer );
		$employer_email         = get_the_author_meta( 'user_email', $project_owner );
		$this->wp_mail( $employer_email, $subject_employer, $mail_template_employer, [
			'user_id' => $project_owner,
			'post'    => $project_id
		], '' );
		// Mail to Employer
		// Mail to Freelancer
		$subject_freelancer       = __( 'The payment has been successfully transferred', ET_DOMAIN );
		$mail_template_freelancer = ae_get_option( 'fre_notify_freelancer_mail_template' );
		$mail_template_freelancer = str_replace( '[employer]', $employer, $mail_template_freelancer );
		$mail_template_freelancer = str_replace( '[link]', $link_project, $mail_template_freelancer );
		$freelancer_email         = get_the_author_meta( 'user_email', $bid_owner );
		$this->wp_mail( $freelancer_email, $subject_freelancer, $mail_template_freelancer, [
			'user_id' => $bid_owner,
			'post'    => $project_id
		], '' );
		// Mail to Freelancer
	}

	/**
	 * send mail to employer, freelancer when admin execute payment
	 *
	 * @param
	 *
	 * @since  1.3
	 * @author ThanhTu
	 */
	function execute( $project_id, $bid_accepted ) {
		$project_owner = get_post_field( 'post_author', $project_id );
		$bid_owner     = get_post_field( 'post_author', $bid_accepted );

		$employer     = get_the_author_meta( 'display_name', $project_owner );
		$freelancer   = get_the_author_meta( 'display_name', $bid_owner );
		$link_project = '<a rel="nofollow" href="' . get_permalink( $project_id ) . '">' . get_permalink( $project_id ) . '</a>';

		// Mail to Employer
		$subject_employer       = __( 'The payment has been successfully transferred', ET_DOMAIN );
		$mail_template_employer = ae_get_option( 'fre_execute_to_employer_mail_template' );
		$mail_template_employer = str_replace( '[freelancer]', $freelancer, $mail_template_employer );
		$mail_template_employer = str_replace( '[link]', $link_project, $mail_template_employer );
		$employer_email         = get_the_author_meta( 'user_email', $project_owner );
		$this->wp_mail( $employer_email, $subject_employer, $mail_template_employer, [
			'user_id' => $project_owner,
			'post'    => $project_id
		], '' );
		// Mail to Employer
		// Mail to Freelancer
		$subject_freelancer       = __( 'The payment has been successfully transferred', ET_DOMAIN );
		$mail_template_freelancer = ae_get_option( 'fre_execute_to_freelancer_mail_template' );
		$mail_template_freelancer = str_replace( '[employer]', $employer, $mail_template_freelancer );
		$mail_template_freelancer = str_replace( '[link]', $link_project, $mail_template_freelancer );
		$freelancer_email         = get_the_author_meta( 'user_email', $bid_owner );
		$this->wp_mail( $freelancer_email, $subject_freelancer, $mail_template_freelancer, [
			'user_id' => $project_owner,
			'post'    => $project_id
		], '' );
		// Mail to Freelancer
	}

	/**
	 * mail alert admin when project complete and transfer money to freelancer
	 *
	 * @param int $project_id The project was completed
	 * @param int $bid_accepted The bid accepted on project
	 *
	 * @since  1.3
	 * @author ThanhTu
	 */
	function alert_transfer_money( $project_id, $bid_accepted ) {
		$project      = get_post( $project_id );
		$bid          = get_post( $bid_accepted );
		$employer     = get_the_author_meta( 'display_name', $project->post_author );
		$freelancer   = get_the_author_meta( 'display_name', $bid->post_author );
		$link_project = '<a rel="nofollow" href="' . get_permalink( $project_id ) . '">' . get_permalink( $project_id ) . '</a>';

		if ( ! ae_get_option( 'manual_transfer' ) ) {
			// Disable Manual Transfer - Send to admin when employer finishes his project, the payment is successful sent.
			$subject        = "The payment has been successfully transferred";
			$admin_template = sprintf( '<p>Hi,</p>
                                        <p>The project %s is completed and the payment has been successfully transferred to freelancer %s.</p>
                                        <p>You can review the project in:</p>
                                        <p>%s</p>
                                        <p>Regards,<br>[blogname]</p>', get_the_title( $project_id ), // title project
				$freelancer,                // name of freelancer
				$link_project               // link project
			);
		} else {
			// Enable Manual Transfer - Send to the admin when the project is completed.
			$subject        = "Manual transfer money to freelancer";
			$admin_template = sprintf( "<p>Hi,</p>
                                        <p>The project %s has been marked as 'Completed' by employer %s.</p>
                                        <p>Please review the project and transfer the payment to the freelancer %s: </p>
                                        <p>%s</p>
                                        <p>Regards, <br>[blogname]</p>", get_the_title( $project_id ), // title project
				$employer,                  // name of employer
				$freelancer,                // name of freelancer
				$link_project               // link project
			);
		}

		// send mail to admin
		$this->wp_mail( get_option( 'admin_email' ), $subject, $admin_template, [
			'user_id' => 1,
			'post'    => $project_id
		], '' );
	}

	/**
	 * Notifications of new projects for Freelancers in selected skills !--categories
	 *
	 * @param object $project
	 *
	 * @since  1.0
	 * @author ThanhTu
	 *
	 * @since  2.0
	 * Update function change category to skill
	 * @author Vosydao
	 */
	function new_project_of_category( $project ) {
		global $ae_post_factory, $post, $wpdb;

		requestQuoteCompany( $project );

		return;
		$skills = [];
		if ( ! empty( $project->tax_input['skill'] ) ) {
			foreach ( $project->tax_input['skill'] as $key => $value ) {
				$skills[] = $value->term_id;
			}

			$postdata = [];
			//			$currentDate = (new DateTime('now', new DateTimeZone('UTC')))->getTimestamp();

			if ( ! empty( $project->country ) ) {
				$andCountry   = "LEFT JOIN {$wpdb->prefix}postmeta pmc ON pmc.post_id = p.ID AND pmc.meta_key = 'country'";
				$whereCountry = 'AND pmc.meta_value = ' . (int) $project->country;
			}
			if ( ! empty( $project->state ) ) {
				$andState   = "LEFT JOIN {$wpdb->prefix}postmeta pms ON pms.post_id = p.ID AND pms.meta_key = 'state'";
				$whereState = 'AND pms.meta_value = ' . (int) $project->state;
			}
			if ( ! empty( $project->city ) ) {
				$andCity   = "LEFT JOIN {$wpdb->prefix}postmeta pmct ON pmct.post_id = p.ID AND pmct.meta_key = 'city'";
				$whereCity = 'AND pmct.meta_value = ' . (int) $project->city;
			}

			$currentDate = current_time( 'timestamp' );
			$sql         = "SELECT DISTINCT u.display_name, u.user_email FROM {$wpdb->prefix}posts p
			LEFT JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = p.ID AND pm.meta_key = 'email_skill'
			LEFT JOIN {$wpdb->prefix}term_relationships tr ON tr.object_id = p.ID
			LEFT JOIN {$wpdb->prefix}users u ON u.ID = p.post_author
			LEFT JOIN {$wpdb->prefix}pro_paid_users pp ON pp.user_id = u.ID
			{$andCountry}
			{$andState}
			{$andCity}
			WHERE p.post_status = 'publish' AND p.post_type = '" . PROFILE . "' AND pm.meta_value = 1
			AND tr.term_taxonomy_id IN (" . implode( ',', $skills ) . ")
			{$whereCountry} {$whereState} {$whereCity}
			AND pp.expired_date > {$currentDate}
			";

			$data_emails = $wpdb->get_results( $sql, ARRAY_A );

			if ( ! empty( $data_emails ) ) {
				foreach ( $data_emails as $data_email ) {
					if ( ! empty( $data_email['display_name'] ) && ! empty( $data_email['user_email'] ) ) {
						$postdata[] = "{$data_email['display_name']} <{$data_email['user_email']}>";
					}
				}
			}

			$subject   = __( 'New Project For You Today!', ET_DOMAIN );
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$headers[] = 'From: ' . get_option( 'blogname' ) . ' <' . get_option( 'admin_email' ) . '>' . "\r\n";
			//			$headers[] = 'Bcc: ' . implode( ',', $postdata ) . '\r\n';

			$template_default = "<p>Hi there,</p><p>There is a new job for you today. Hurry apply for this project [project_link] and get everything started.</p><p>Hope you have a highly effective Day</p>";
			$mail_template    = ae_get_option( 'new_project_mail_template', $template_default );
			$link             = "<a rel='nofollow noopener noreferrer' target='_Blank' href='" . $project->permalink . "'>" . $project->post_title . "</a>";
			$mail_template    = str_replace( '[project_link]', $link, $mail_template );

			// send mail
			//			$mail = $this->wp_mail( get_option( 'admin_email' ), $subject, $mail_template, array(), $headers );
			$mail = $this->wp_mail( $postdata, $subject, $mail_template, [], $headers );
		}
	}


	/**
	 * registration by referral code of the user
	 *
	 * @since  1.0
	 * @author Azrail
	 */
	function notification_registration_referral_code( $refer_id, $user_id ) {
		$author   = get_userdata( $refer_id );
		$referral = get_userdata( $user_id );

		$message = '<p>Hi,</p> <p>You have a new referral: <a href="' . site_url( '/author/' . $referral->user_nicename ) . '">' . $referral->display_name . '</a>.</p>';
		$subject = __( "You have a new referral.", ET_DOMAIN );
		$this->wp_mail( $author->user_email, $subject, $message, '', '' );

		return $user_id;
	}
}

if ( ! function_exists( 'send_receipt_mail' ) ) {
	/**
	 * employer complete a job and send mail to freelancer joined project
	 *
	 * @param $content
	 * @param $user_id
	 * @param $order
	 *
	 * @author ThanhTu
	 */
	function send_receipt_mail( $content, $order ) {
		// Get info Order
		//		$product = current( $order['products'] );
		$options = $order['products'];

		$product = array_shift( $options );

		if ( ! empty( get_property_name_by_nickname( $product['ID'] ) ) ) {
			$options[] = $product;
		}

		$type  = $product['TYPE'];
		$packs = AE_Package::get_instance();
		$sku   = $order['payment_package'];
		$pack  = $packs->get_pack( $sku, $type );

		if ( $type == 'bid_plan' ) {
			$content = ae_get_option( 'ae_receipt_bid_mail' );
		} else {
			$content = ae_get_option( 'ae_receipt_project_mail' );
		}

		$content = apply_filters( 'ae_send_receipt_credit_mail', $content, $pack, $type ); // add from 1.8.6

		$post_parent = get_post_field( 'post_parent', $order['ID'] );


		$ad_url  = '<a href="' . get_permalink( $post_parent ) . '">' . get_the_title( $post_parent ) . '</a>';
		$content = str_ireplace( '[link]', $ad_url, $content );

		if ( $order['payment'] == 'cash' ) {
			if ( $type == 'bid_plan' ) {
				$content = str_ireplace( '[notify_cash]', __( 'Please send the payment to admin to complete your payment.', ET_DOMAIN ), $content );
			} else {
				$content = str_ireplace( '[notify_cash]', __( 'Please send the payment to admin to complete your payment.<br>Your project post is under admin review. It will be active right after admin approval.', ET_DOMAIN ), $content );
			}
		} else {
			$content = str_ireplace( '[notify_cash]', '', $content );
		}

		//		if( isset( $product['NAME'] ) )
		//			$content = str_ireplace( '[package_name]', $product['NAME'], $content );
		//		if( isset( $pack->et_number_posts ) )
		//			$content = str_ireplace( array( '[number_of_bids]', '[number_of_posts]' ), $pack->et_number_posts, $content );

		if ( isset( $pack->et_number_posts ) && empty( get_property_name_by_nickname( $product['ID'] ) ) ) {
			$str_package_detail = 'Purchase the ' . $product['NAME'] . ' package. This package contains ' . $pack->et_number_posts . ' project posts';
		} else {
			$package_id         = get_post_meta( $order['product_id'], 'et_payment_package', true );
			$package_           = $packs->get( $package_id );
			$str_package_detail = 'Package - ' . $package_->post_title . ': ' . strip_tags( $package_->post_content );
		}
		$content = str_ireplace( '[package_detail]', $str_package_detail, $content );

		$str_options = '- no selected options';
		if ( ! empty( $options ) ) {
			$str_options = '<ul>';
			foreach ( $options as $item ) {
				$ex_date     = get_post_meta( $order['product_id'], 'et_' . $item['ID'], true );
				$str_options .= '<li>' . get_property_name_by_nickname( $item['ID'] ) . ': $' . $item['AMT'] . '. active by ' . $ex_date . '</li>';
			}
			$str_options .= '</ul>';
		}
		$content = str_ireplace( '[project_options]', $str_options, $content );

		return $content;
	}

	add_filter( 'ae_send_receipt_mail', 'send_receipt_mail', 10, 3 );
}

