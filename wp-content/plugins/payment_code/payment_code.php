<?php
	/*
	Plugin Name: PaymentCode for Project
	Description:
	Version: 1.0
	Lat Update: 19.12.2019
	Author:
	Author URI:
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	add_action( 'plugins_loaded', 'paymentCode_load', 0 );
	add_action( 'admin_menu', 'payCode_add_menu', 0 );

	function payCode_add_menu() {
		add_menu_page( __( 'Payment Code', 'review_rating' ), __( 'Payment Code', 'review_rating' ), 'administrator', 'paycode', 'payCode_admin_page' );
	}

	function payCode_admin_page() {
		require_once __DIR__ . '/admin_page.php';
	}

	function paymentCode_load() {
		require_once ABSPATH . 'vendor/autoload.php';
		AutoloadVendor::init( __DIR__ . '/class' );

		if ( ! \PaymentCode\Activate::getInstance()->tbIsExists() ) {
			\PaymentCode\Activate::getInstance()->installTb();
		}

		add_action( 'fre_process_escrow_complete', function( $data ) {
			\PaymentCode\PaymentCode::getInstance()->createPayCode( $data );
		} );

		add_action( 'wp_ajax_payCode', 'payCodeAction' );
		add_action( 'wp_ajax_payCodeReview', 'payCodeReviewAction' );
		add_action( 'wp_ajax_payCodeNotice', 'payCodeNotice' );
		add_action( 'wp_enqueue_scripts', 'assets' );

	}

	function assets() {
		if ( ! is_admin() ) {
			wp_register_script( 'paymentCode', '/wp-content/plugins/payment_code/js/payment_code.js', [], '1.0', true );
		}
	}

	function payCodeInit() {
		return \PaymentCode\PaymentCode::getInstance();
	}

	function viewPaymentCode( $projectId = null ) {
		global $user_ID, $review_for_employer, $review_for_freelancer;

		//    print_filters_for( 'fre_employer_review_freelancer' );
		//    print_filters_for( 'fre_freelancer_review_employer' );

		if ( $user_ID && (int) $projectId > 0 ) {
			$bid_id_accepted = get_post_meta( $projectId, 'accepted', true );
			$dataCode        = \PaymentCode\PaymentCode::getInstance()->getData( $bid_id_accepted );

			if ( ! empty( $dataCode[ 'code' ] ) ) {
				if ( $dataCode[ 'granted' ] ) {
					if ( userRole( $user_ID ) == FREELANCER ) {
						$review_for_employer = review_rating_init()->getReviewDoc( $projectId );
						if ( empty( $review_for_employer ) ) {
							require 'template/freelancerProject.php';
						}
					} else {
						$review_for_freelancer = review_rating_init()->getReviewDoc( $bid_id_accepted );
						if ( empty( $review_for_freelancer ) ) {
							$code = $dataCode[ 'code' ];
							require 'template/employerProject.php';
						}
					}
				}
			}
		}
	}

	function payCodeAction() {
		global $user_ID;

		$code      = $_POST[ 'code' ];
		$projectId = (int) $_POST[ 'project_id' ];
		$bid_id    = get_post_meta( $projectId, 'accepted', true );
		$payCode   = \PaymentCode\PaymentCode::getInstance();

		if ( $data = $payCode->getData( $bid_id ) ) {
			if ( $user_ID && $payCode->checkAccess( $user_ID, $bid_id ) ) {
				if ( $data[ 'used' ] == 1 ) {
					\PaymentCode\PaymentCode::outputJSON( __( 'Payment code was used' ) );
				}
				if ( $payCode->isValid( $code, $data[ 'code' ] ) ) {
					//                $order = get_post_meta($bid_id, 'fre_bid_order', true);
					//                $status = get_post_field($order, 'post_status', true);
					$status = $payCode->getStatusOrder( $bid_id );
					if ( $status == 'finish' ) { //finish ||  bid not complete
						$payCode->setUsed( $bid_id );
						\PaymentCode\PaymentCode::outputJSON( 'Money was transferred', 1 );
					}

					if ( bid_finish_escrow( $projectId ) ) {
						$payCode->setUsed( $bid_id );
						wp_update_post( [ 'ID' => $projectId, 'post_status' => 'complete' ] );
						\PaymentCode\PaymentCode::outputJSON( 'Payment Code is accepted. Payment has been transferred to your PayPal account.', 1 );
					} else {
						\PaymentCode\PaymentCode::outputJSON( __( 'Transfer money process finished with error' ) );
					}
				} else {
					\PaymentCode\PaymentCode::outputJSON( __( 'Payment Code is invalid' ) );
				}
			} else {
				\PaymentCode\PaymentCode::outputJSON( __( 'Error!!! Access closed' ) );
			}
		} else {
			\PaymentCode\PaymentCode::outputJSON( __( 'Your Bid not found' ) );
		}
	}

	function payCodeReviewAction() {
		if ( $_POST[ 'from_is' ] == 'employer' ) {
			remove_all_actions( 'fre_employer_review_freelancer' );
			employerReviewAction();
		} elseif ( $_POST[ 'from_is' ] == 'freelancer' ) {
			//    remove_all_actions('fre_freelancer_review_employer');
			freelancerReviewAction();
		} else {
			\PaymentCode\PaymentCode::outputJSON();
		}
	}

	function payCodeNotice() {
		global $user_ID;

		$projectId = (int) $_POST[ 'project_id' ];
		$bid_id    = get_post_meta( $projectId, 'accepted', true );
		$payCode   = \PaymentCode\PaymentCode::getInstance();
		$data      = $payCode->getData( $bid_id );
		$project   = get_post( $projectId );

		if ( $project->post_author == $user_ID && ! empty( $data ) ) {
			$projectName = $project->post_title;

			if ( $payCode->noticeEmail( $data[ 'code' ], $projectName ) ) {
				\PaymentCode\PaymentCode::outputJSON( __( 'Payment Code has been emailed' ), 1 );
			} else {
				\PaymentCode\PaymentCode::outputJSON( __( 'sending failed' ) );
			}
		}

		\PaymentCode\PaymentCode::outputJSON( __( 'Error!!! Access closed' ) );
	}
