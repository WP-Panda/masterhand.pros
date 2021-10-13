<?php
	/*
	Plugin Name: ReviewsRating
	Description:
	Version: 1.1
	Lat Update: 31.07.2019
	Author:
	Author URI:
	*/

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	define( 'REVIEW_RATING_DIR', plugin_dir_path( __FILE__ ) );
	define( 'REVIEW_RATING_RELATIVE', '/wp-content/plugins/' . basename( __DIR__ ) );
	add_action( 'plugins_loaded', 'review_rating_load', 0 );
	add_action( 'admin_menu', 'add_menu_review_rating', 0 );
	add_action( 'init', 'rwRating_set_page' );

	function rwRating_set_page() {
		add_rewrite_endpoint( 'check-payment', EP_ROOT );

		add_filter( 'posts_clauses_request', function( $pieces, $wp_query ) {
			if ( isset( $wp_query->query[ 'check-payment' ] ) && $wp_query->is_main_query() ) {
				$pieces[ 'where' ] = ' AND ID = 0';
			}

			return $pieces;
		}, 10, 2 );

		add_filter( 'template_include', 'rwRating_template_include', 1 );
		add_filter( 'template_include', 'give_endorsements_template_include', 1 );

	}

	function rwRating_template_include( $template ) {
		if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/check-payment' ) !== false ) {
			add_filter( 'wp_title', function() {
				return __( 'Check payment' ) . ' | ';
			}, 1 );
			status_header( 200 );

			$new_template = locate_template( [ 'page-check-payment.php' ] );
			if ( ! empty( $new_template ) ) {
				return $new_template;

			}
		}

		return $template;
	}


	require_once get_template_directory() .  '/wpp/vendor/autoload.php';
	require_once __DIR__ . '/classes/AutoloadReviews.php';
	AutoloadReviews::init();

	function add_menu_review_rating() {
		add_menu_page( __( 'Ratings Reviews', 'review_rating' ), __( 'Ratings Reviews', 'review_rating' ), 'administrator', 'reviews_rating', 'reviews_rating_module', plugins_url( '/img/review_rating_25.png', __FILE__ ) );
		createTbReviewRating();
	}

	function createTbReviewRating() {
		if ( ! \ReviewsRating\Module::getInstance()->tbIsExists() ) {
			\ReviewsRating\Module::getInstance()->installTb();
		}
	}

	function reviews_rating_module() {
		require_once REVIEW_RATING_DIR . 'module/module.php';
	}

	function review_rating_load() {
		add_action( 'wp_ajax_rwRating', 'rwRatingAction' );
		add_action( 'wp_ajax_rwPaginate', 'ajaxReviewPaginate' );
		add_action( 'wp_ajax_previewPayRw', 'previewPayReview' );
		add_action( 'wp_ajax_payRw', 'createPayReview' );
		add_action( 'wp_enqueue_scripts', 'review_assets' );

	}

	function review_assets() {
		if ( ! is_admin() ) {
			wp_enqueue_style( '', '/wp-content/plugins/reviews_rating/css/review-rating.css' );
		}
	}


	function review_rating_init() {
		return \ReviewsRating\Reviews::getInstance();
	}

	function get_count_reviews_user( $userId = 0 ) {
		return review_rating_init()->getCountReviews( $userId );
	}

	function rwRatingAction() {

	    do_action( 'wpp_close_project', $_POST);

		if ( $_POST[ 'from_is' ] == 'employer' ) {
			if ( $_POST[ 'is_reply' ] == 'true' ) {
				employerReplyAction();
			} else {
				employerReviewAction();
			}
		} elseif ( $_POST[ 'from_is' ] == 'freelancer' ) {

			if ( $_POST[ 'is_reply' ] == 'true' ) {
				freelancerReplyAction();
			} else {
				freelancerReviewAction();
			}
		} else {
			\ReviewsRating\Base::outputJSON( [] );
		}



	}

	function employerReviewAction() {
		global $user_ID, $current_user;
		$postData = $_POST;

		if ( ! isset( $postData[ 'project_id' ] ) ) {
			\ReviewsRating\Base::outputJSON( __( 'Invalid project id.', ET_DOMAIN ) );
		}

		$project_id = $postData[ 'project_id' ];
		$old_status = get_post_field( 'post_status', $project_id );
		if ( $old_status == 'disputing' ) {
			\ReviewsRating\Base::outputJSON( __( 'Project is in dispute.', ET_DOMAIN ) );
		}
		$author_id = (int) get_post_field( 'post_author', $project_id );

		if ( ! $user_ID || $user_ID !== $author_id ) {
			\ReviewsRating\Base::outputJSON( __( 'You can\'t not access this action.', ET_DOMAIN ) );
		}

		$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
		if ( ! $bid_id_accepted ) {
			\ReviewsRating\Base::outputJSON( __( 'Please assign project before complete.', ET_DOMAIN ) );
		}

		if ( empty( $postData[ 'vote' ] ) ) {
			\ReviewsRating\Base::outputJSON( __( 'You have to rate for this profile.', ET_DOMAIN ) );
		}

		$postData[ 'user_id' ]  = ! empty( $user_ID ) ? (int) $user_ID : 0;
		$postData[ 'username' ] = 'employer';
		$postData[ 'email' ]    = $current_user->user_email;

		$freelancer_id = (int) get_post_field( 'post_author', $bid_id_accepted );

		$review = new \ReviewsRating\Reviews( $bid_id_accepted );
		$review->setUserIdForRating( $freelancer_id );
		if ( $review->isAccess( $postData ) ) {
			$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );

			if ( ! $order && $review->toInt( $postData[ 'vote' ] ) > 3 ) {
				$postData[ 'status' ] = $review::STATUS_HIDDEN;
			}

			$id = $review->create( $postData );
			if ( $id !== false ) {

				do_action( 'activityRating_forReview' );

				if ( $order ) {
					do_action( 'activityRating_projectSuccess', $user_ID, $freelancer_id );

					update_post_meta( $postData[ 'project_id' ], 'employer_id', $user_ID );
					update_post_meta( $postData[ 'project_id' ], 'professional_id', $freelancer_id );

					$safe_deals_employer = get_user_meta( $user_ID, 'safe_deals_count', true ) + 1;
					update_user_meta( $user_ID, 'safe_deals_count', $safe_deals_employer );
				}

				//transfer money to freelance after project owner complete his project
				do_action( 'fre_employer_review_freelancer', $project_id, $postData );

				//fire an action after project owner complete his project
				do_action( 'fre_complete_project', $project_id, $postData );

				// update project, bid, user rating scrore after review a project
				updateAfterEmployerReview( $project_id, $bid_id_accepted );

				$author_bid      = get_post_field( 'post_author', $bid_id_accepted );
				$freelancer_name = get_the_author_meta( 'display_name', $author_bid );

				$msg = sprintf( __( "You have completed the project, reviewed, and rated %s.", ET_DOMAIN ), $freelancer_name );
				\ReviewsRating\Base::outputJSON( $msg, 1 );
			}

			$log = \ReviewsRating\Log::getInstance()->getLog();
			$msg = empty( $log ) ? $review->getLang( 'something_went_wrong' ) : $review->getLang( 'error' ) . ' ' . implode( ', ', $log );

			\ReviewsRating\Base::outputJSON( $msg );
		} else {
			$log = \ReviewsRating\Log::getInstance()->getLog();
			$msg = implode( ', ', $log );

			\ReviewsRating\Base::outputJSON( $msg );
		}
	}

	function employerReplyAction() {
		global $user_ID, $current_user;
		$postData = $_POST;

		if ( ! isset( $postData[ 'project_id' ] ) ) {
			\ReviewsRating\Base::outputJSON( __( 'Invalid project id.', ET_DOMAIN ) );
		}

		$project_id   = $postData[ 'project_id' ];
		$reviewing_id = $postData[ 'reviewing_id' ];
		$old_status   = get_post_field( 'post_status', $project_id );
		if ( $old_status == 'disputing' ) {
			\ReviewsRating\Base::outputJSON( __( 'Project is in dispute.', ET_DOMAIN ) );
		}
		$author_id = (int) get_post_field( 'post_author', $project_id );

		if ( ! $user_ID || $user_ID !== $author_id ) {
			\ReviewsRating\Base::outputJSON( __( 'You can\'t not access this action.', ET_DOMAIN ) );
		}

		$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
		if ( ! $bid_id_accepted ) {
			\ReviewsRating\Base::outputJSON( __( 'Please assign project before complete2.', ET_DOMAIN ) );
		}

		/*
		if (empty($postData['vote'])) {
			\ReviewsRating\Base::outputJSON( __( 'You have to rate for this profile.', ET_DOMAIN ) );
		}
		*/

		$postData[ 'user_id' ]         = ! empty( $user_ID ) ? (int) $user_ID : 0;
		$postData[ 'username' ]        = 'employer';
		$postData[ 'email' ]           = $current_user->user_email;
		$postData[ 'vote' ]            = 'skip';
		$postData[ 'parent' ]          = $reviewing_id;
		$postData[ 'additional_data' ] = 'is_reply';

		$freelancer_id = (int) get_post_field( 'post_author', $bid_id_accepted );

		$review = new \ReviewsRating\Reviews( $bid_id_accepted );
		$review->setUserIdForRating( $freelancer_id );


		$order = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );

		if ( ! $order && $review->toInt( $postData[ 'vote' ] ) > 3 ) {
			$postData[ 'status' ] = $review::STATUS_HIDDEN;
		}

		$id = $review->create( $postData );
		if ( $id !== false ) {

			//do_action('activityRating_forReview');

			// notify freelancer about reply
			// through notify
			do_action( 'reply_added', [ 'project' => $project_id, 'freelancer' => $freelancer_id ] );

			// through E-mail
			$mail = new Fre_Mailing();
			$mail->reply_to_review_email( $project_id );

			/*
			if ($order){
				do_action('activityRating_projectSuccess', $user_ID, $freelancer_id);

				update_post_meta($postData['project_id'], 'employer_id', $user_ID);
				update_post_meta($postData['project_id'], 'professional_id', $freelancer_id);
			}
			*/

			//fire an action after project owner complete his project
			//do_action('fre_complete_project', $project_id, $postData);

			// update project, bid, user rating score after review a project
			//updateAfterEmployerReview($project_id, $bid_id_accepted);

			//$author_bid = get_post_field( 'post_author', $project_id );
			//$freelancer_name = get_the_author_meta('display_name', $author_bid);

			$msg = sprintf( __( "You left a reply", ET_DOMAIN ) );
			\ReviewsRating\Base::outputJSON( $msg, 1 );
		}

		$log = \ReviewsRating\Log::getInstance()->getLog();
		$msg = empty( $log ) ? $review->getLang( 'something_went_wrong' ) : $review->getLang( 'error' ) . ' ' . implode( ', ', $log );

		\ReviewsRating\Base::outputJSON( $msg );
	}

	function freelancerReplyAction() {
		global $user_ID, $current_user;

		$postData        = $_POST;
		$bid_id_accepted = $postData[ 'project_id' ];
		$reviewing_id    = $postData[ 'reviewing_id' ];
		$project_id      = (int) get_post_field( 'post_parent', $bid_id_accepted );
		$status          = get_post_status( $project_id );

		$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
		$order           = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
		$author_bid      = (int) get_post_field( 'post_author', $bid_id_accepted );

		if ( $user_ID !== $author_bid || ! $user_ID ) {
			\ReviewsRating\Base::outputJSON( __( 'You don\'t have permission to review.', ET_DOMAIN ) );
		}

		//if ( $status !== 'complete' ) {
		//	\ReviewsRating\Base::outputJSON( __( 'You can\'t not review on this project.', ET_DOMAIN ) );
		//}

		$postData[ 'user_id' ]         = ! empty( $user_ID ) ? (int) $user_ID : 0;
		$postData[ 'username' ]        = 'freelancer';
		$postData[ 'email' ]           = $current_user->user_email;
		$postData[ 'vote' ]            = 'skip';
		$postData[ 'parent' ]          = $reviewing_id;
		$postData[ 'additional_data' ] = 'is_reply';

		$employer_id = get_post_field( 'post_author', $project_id );

		$review = new \ReviewsRating\Reviews( $project_id );
		$review->setUserIdForRating( $employer_id );
		if ( $review->isAccess( $postData ) ) {
			$id = $review->create( $postData );
			if ( $id !== false ) {

				//do_action( 'activityRating_forReview' );

				if ( $order ) {
					//do_action( 'activityRating_projectSuccessFreelancer', $user_ID, $employer_id );

					$safe_deals_freelancer = get_user_meta( $user_ID, 'safe_deals_count', true ) + 1;
					update_user_meta( $user_ID, 'safe_deals_count', $safe_deals_freelancer );
				}

				//do_action( 'fre_freelancer_review_employer', $project_id, $postData );
				do_action( 'reply_added_emp', [ 'project' => $project_id, 'employer' => $employer_id ] );

				if ( review_rating_init()->getParamConfig( 'send_notice' ) ) {
					// send mail to employer.
					$mail = new Fre_Mailing();
					$mail->reply_to_review_email( $project_id );
				}
				$employer_name = get_the_author_meta( 'display_name', $project_id );
				$msg           = sprintf( __( "You left a reply", ET_DOMAIN ) );
				\ReviewsRating\Base::outputJSON( $msg, 1 );
			}

			wp_update_post( [ 'ID' => $bid_id_accepted, 'post_status' => 'publish' ] );

			$log = \ReviewsRating\Log::getInstance()->getLog();
			$msg = empty( $log ) ? $review->getLang( 'something_went_wrong' ) : $review->getLang( 'error' ) . ' ' . implode( ', ', $log );

			\ReviewsRating\Base::outputJSON( $msg );
		} else {
			$log = \ReviewsRating\Log::getInstance()->getLog();
			$msg = implode( ', ', $log );

			\ReviewsRating\Base::outputJSON( $msg );
		}
	}

	function freelancerReviewAction() {
		global $user_ID, $current_user;

		$postData   = $_POST;
		$project_id = $postData[ 'project_id' ];

		$status = get_post_status( $project_id );

		$bid_id_accepted = get_post_meta( $project_id, 'accepted', true );
		$order           = get_post_meta( $bid_id_accepted, 'fre_bid_order', true );
		$author_bid      = (int) get_post_field( 'post_author', $bid_id_accepted );
		wpp_d_log(222);
		wpp_d_log($user_ID);
		wpp_d_log($author_bid);
		if ( $user_ID !== $author_bid || ! $user_ID ) {
			\ReviewsRating\Base::outputJSON( __( 'You don\'t have permission to review.', ET_DOMAIN ) );
		}

		//if ( $status !== 'complete' ) {
		//	\ReviewsRating\Base::outputJSON( __( 'You can\'t not review on this project.', ET_DOMAIN ) );
		//}

		$postData[ 'user_id' ]  = ! empty( $user_ID ) ? (int) $user_ID : 0;
		$postData[ 'username' ] = 'freelancer';
		$postData[ 'email' ]    = $current_user->user_email;

		$employer_id = get_post_field( 'post_author', $project_id );

		$review = new \ReviewsRating\Reviews( $project_id );
		$review->setUserIdForRating( $employer_id );
		if ( $review->isAccess( $postData ) ) {
			$id = $review->create( $postData );
			if ( $id !== false ) {

				do_action( 'activityRating_forReview' );

				if ( $order ) {
					//do_action( 'activityRating_projectSuccessFreelancer', $user_ID, $employer_id );

					$safe_deals_freelancer = get_user_meta( $user_ID, 'safe_deals_count', true ) + 1;
					update_user_meta( $user_ID, 'safe_deals_count', $safe_deals_freelancer );
				}

				do_action( 'fre_freelancer_review_employer', $project_id, $postData );

				if ( review_rating_init()->getParamConfig( 'send_notice' ) ) {
					// send mail to employer.
					Fre_Mailing::get_instance()->review_employer_email( $project_id );
				}

				\ReviewsRating\Base::outputJSON( __( "Your review has been submitted. Thank you.", ET_DOMAIN ), 1 );
			}

			wp_update_post( [ 'ID' => $bid_id_accepted, 'post_status' => 'publish' ] );

			$log = \ReviewsRating\Log::getInstance()->getLog();
			$msg = empty( $log ) ? $review->getLang( 'something_went_wrong' ) : $review->getLang( 'error' ) . ' ' . implode( ', ', $log );

			\ReviewsRating\Base::outputJSON( $msg );
		} else {
			$log = \ReviewsRating\Log::getInstance()->getLog();
			$msg = implode( ', ', $log );

			\ReviewsRating\Base::outputJSON( $msg );
		}
	}

	function updateAfterEmployerReview( $project_id, $bid_id_accepted ) {
		$freelancer_id = get_post_field( 'post_author', $bid_id_accepted );

		$profile_id = get_user_meta( $freelancer_id, 'user_profile_id', true );

		//update status for project
		wp_update_post( [ 'ID' => $project_id, 'post_status' => 'complete' ] );

		//update bid post
		$bids_post = get_children( [
			'post_parent' => $project_id,
			'post_type'   => BID,
			'numberposts' => - 1,
			'post_status' => 'any'
		] );

		if ( ! empty( $bids_post ) ) {
			foreach ( $bids_post as $bid ) {
				if ( $bid->ID == $bid_id_accepted ) {
					wp_update_post( [ 'ID' => $bid_id_accepted, 'post_status' => 'complete' ] );
				} else {
					wp_update_post( [ 'ID' => $bid->ID, 'post_status' => 'hide' ] );
				}
			}
		}

		//update projects worked for profile
		$total_projects_worked_current = get_post_meta( $profile_id, 'total_projects_worked' );
		if ( $total_projects_worked_current ) {
			$total_project_worked_new = intval( $total_projects_worked_current[ 0 ] ) + 1;
			update_post_meta( $profile_id, 'total_projects_worked', $total_project_worked_new );
		} else {
			$works                = get_posts( [
				'post_status'    => [ 'complete' ],
				'post_type'      => BID,
				'author'         => $freelancer_id,
				'posts_per_page' => - 1,
			] );
			$total_project_worked = count( $works );
			add_post_meta( $profile_id, 'total_projects_worked', $total_project_worked );
		}

		if ( review_rating_init()->getParamConfig( 'send_notice' ) ) {
			// send mail to freelancer.
			Fre_Mailing::get_instance()->review_freelancer_email( $project_id );
		}
	}

	function review_rating_user( $userId = 0 ) {
		$result                 = review_rating_init()->getRating( $userId );
		$data[ 'percent_vote' ] = 0;
		$data[ 'title' ]        = '';
		if ( $result[ 'votes' ] > 2 ) {
			$data[ 'percent_vote' ] = number_format( ( floatval( $result[ 'rating' ] ) * 20 ), 2 );
			$data[ 'title' ]        = "{$result['rating']}/" . review_rating_init()->getMaxScore() . " ({$data['percent_vote']}%)";
		} else {
			if ( userHaveProStatus( $userId ) ) {
				$data[ 'percent_vote' ] = number_format( ( floatval( 5 ) * 20 ), 2 );
				$data[ 'title' ]        = "{$result['rating']}/" . review_rating_init()->getMaxScore() . " ({$data['percent_vote']}%)";
			}
		}

		return $data;
	}

	function HTML_review_rating_user( $userId = 0, $isReturn = false ) {
		if ( $isReturn ) {
			ob_start();
		}
		$data = review_rating_user( $userId );
		//	if($data['percent_vote']) {
		?>

        <div class="reviews-rating-summary" title="<?= $data[ 'title' ]; ?>">
            <div class="review-rating-result" style="width: <?= $data[ 'percent_vote' ]; ?>%"></div>
        </div>
		<?
		//	}
		if ( $isReturn ) {
			$output = ob_get_clean();

			//var_dump(json_encode($output));
			return $output;
		}
	}

	function prepare_percent_vote( $data = [] ) {

		$percent_vote = number_format( ( $data[ 'vote' ] * 20 ), 2, '.' );

		return $percent_vote;
	}

	if ( ! function_exists( 'string_is_nl2br' ) ) {
		function string_is_nl2br( $str = '' ) {
			echo is_string( $str ) ? str_replace( [ '\r', '\n' ], [ '', '<br>' ], nl2br( $str ) ) : '';
		}
	}

	function init_js_modal_review_rating() {
		?>
        <script type="text/javascript">
            (function ($, Views, Models, Collections) {
                $(document).ready(function () {
                    this.modal_review = new AE.Views.Modal_Review();
                    this.modal_review.openModal();
                });
            })(jQuery, AE.Views, AE.Models, AE.Collections);
        </script>
		<?
	}

	function ajaxReviewPaginate() {

		global $user_ID;

		if ( ! empty( $_POST[ 'user_id' ] ) ) {
			$reviewsUser = (int) $_POST[ 'user_id' ];
			$pageNum     = isset( $_POST[ 'rwn' ] ) ? (int) $_POST[ 'rwn' ] : 1;
			$objReviews  = review_rating_init();

			$onlyPublish = ( $reviewsUser == $user_ID ) ? 0 : 1;
			$objReviews->setLimitOffset( 1 );
			$objReviews->setSqlLimit( $pageNum );
			$total        = $objReviews->getCountReviews( $reviewsUser, $onlyPublish );
			$list_reviews = $objReviews->getReviewsUser( $reviewsUser, $onlyPublish );

			$vars[ 'list_reviews' ] = $list_reviews;
			$vars[ 'user_ID' ]      = $user_ID;

			$data[ 'list' ]       = ReviewsRating\TplRender::getInstance()->fetch( 'reviewsProfile.tpl', $vars );
			$data[ 'pagination' ] = $objReviews->getRwPagination( $total, $pageNum, 1 );

			\ReviewsRating\Base::outputJSON( $data, 1 );
		}
		\ReviewsRating\Base::outputJSON();
	}

	function previewPayReview() {
		global $user_ID;

		$rwId = intval( $_POST[ 'rwId' ] );
		if ( ! empty( $user_ID ) && $rwId ) {
			$objReviews = review_rating_init();
			$rw         = $objReviews->getReview( $rwId );

			if ( empty( $rw ) || $rw[ 'for_user_id' ] != $user_ID ) {
				\ReviewsRating\Base::outputJSON( _( "You don't have access", ET_DOMAIN ) );
			}

			$bidId = intval( $rw[ 'doc_id' ] );

			$ae_currency = ae_get_option( 'currency' );
			$currency    = $ae_currency[ 'code' ];

			$bid_budget = floatval( get_post_meta( $bidId, 'bid_budget', true ) );

			$percentPayReview = $objReviews->getPercentPayReview();
			$minPayReview     = $objReviews->getMinPayReview();
			$total            = ( $bid_budget / 100 ) * $percentPayReview;

			if ( $total < $minPayReview ) {
				$total = $minPayReview;
			}

			$data[ 'total' ]    = $total;
			$data[ 'currency' ] = $currency;
			\ReviewsRating\Base::outputJSON( $data, 1 );
		}
		\ReviewsRating\Base::outputJSON();
	}

	function createPayReview() {
		global $user_ID, $wpdb;

		$data = [];
		$rwId = intval( $_POST[ 'rwId' ] );
		if ( ! empty( $user_ID ) && $rwId ) {
			$objReviews = review_rating_init();
			$rw         = $objReviews->getReview( $rwId );
			if ( empty( $rw ) || $rw[ 'for_user_id' ] != $user_ID ) {
				\ReviewsRating\Base::outputJSON( _( "You don't have access", ET_DOMAIN ) );
			}

			$bidId = intval( $rw[ 'doc_id' ] );

			$ppAdaptive = AE_PPAdaptive::get_instance();
			$order      = getPendingPayGateSourceId( $rwId );
			if ( ! empty( $order ) ) {
				$time_create = strtotime( $order[ 'created' ] );
				$time_now    = review_rating_init()::getUTimeTimezone();
				$expiring    = $time_now - $time_create;

				if ( $expiring >= 10000 ) {
					$trzDetail = $ppAdaptive->PaymentDetails( $order[ 'trz_id' ] );

					//				ReviewsRating\Base::outputJSON([$trzDetail]);

					if ( strtoupper( $trzDetail->status ) == 'CREATED' ) {

						$data[ 'redirect_url' ] = $ppAdaptive->paypal_url . $order[ 'trz_id' ];
						\ReviewsRating\Base::outputJSON( $data, 1 );
					}
				}

				setClosePayGateId( $order[ 'id' ] );
			}

			$ppAdaptive_settings = ae_get_option( 'escrow_paypal' );

			// the admin's paypal business account
			$primary     = $ppAdaptive_settings[ 'business_mail' ];
			$ae_currency = ae_get_option( 'currency' );
			$currency    = $ae_currency[ 'code' ];

			$bid_budget = floatval( get_post_meta( $bidId, 'bid_budget', true ) );

			$percentPayReview = $objReviews->getPercentPayReview();
			$minPayReview     = $objReviews->getMinPayReview();
			$total            = ( $bid_budget / 100 ) * $percentPayReview;

			if ( $total < $minPayReview ) {
				$total = $minPayReview;
			}

			$project_id          = wp_get_post_parent_id( $bidId );
			$ins                 = [];
			$ins[ 'amount' ]     = $total;
			$ins[ 'currency' ]   = $currency;
			$ins[ 'type' ]       = 'pay';
			$ins[ 'gateway' ]    = 'paypal_adaptive';
			$ins[ 'type_order' ] = 'review';
			$ins[ 'source_id' ]  = $rwId;
			$ins[ 'parent_id' ]  = $project_id;
			$orderId             = createPayGateOrder( $ins );

			if ( $orderId ) {
				$homeUrl   = home_url( "check-payment/{$orderId}/" );
				$returnUrl = home_url( '/profile/#reviews' );
				/**
				 * paypal adaptive order data
				 */
				$order_data = [
					'actionType'   => 'PAY',
					'returnUrl'    => $homeUrl . '?' . http_build_query( [ 'returnUrl' => $returnUrl ] ),
					'cancelUrl'    => $homeUrl . '?' . http_build_query( [
							'payment'   => 'cancel',
							'returnUrl' => $returnUrl
						] ),
					'currencyCode' => $currency,

					'receiverList.receiver(0).amount' => $total,
					'receiverList.receiver(0).email'  => $primary,
					'requestEnvelope.errorLanguage'   => 'en_US'
				];

				$response = $ppAdaptive->Pay( $order_data );
				if ( strtoupper( $response->responseEnvelope->ack ) == 'SUCCESS' ) {
					$upd             = [];
					$upd[ 'trz_id' ] = $response->payKey;
					$upd[ 'status' ] = 'pending';

					if ( updDataPayGateId( $orderId, $upd ) ) {
						$data[ 'redirect_url' ] = $ppAdaptive->paypal_url . $response->payKey;

						\ReviewsRating\Base::outputJSON( $data, 1 );
					}

					\ReviewsRating\Base::outputJSON( $data );
				} else {
					$data[ 'msg' ] = $response->error[ 0 ]->message;
				}
			}

			\ReviewsRating\Base::outputJSON( $data, 0 );
		}

		\ReviewsRating\Base::outputJSON( $data );
	}

	function createPayGateOrder( $data = [] ) {
		global $wpdb;

		if ( ! empty( $data ) ) {
			$data[ 'created' ] = review_rating_init()::getTimestamp();
			if ( ! empty( $data[ 'additional_data' ] ) && is_array( $data[ 'additional_data' ] ) ) {
				$data[ 'additional_data' ] = json_encode( $data[ 'additional_data' ] );
			}

			if ( $wpdb->insert( "{$wpdb->prefix}payments_gate", $data ) ) {
				return $wpdb->insert_id;
			}
		}

		return false;
	}

	function getDataPayGateTRZ( $trz_id = '', $type_order = '' ) {
		global $wpdb;

		$andTypeOrder = ! empty( $type_order ) ? " AND type_order = '$wpdb->_escape($type_order)'" : '';

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}payments_gate WHERE trz_id = '{$wpdb->_escape($trz_id)}' $andTypeOrder", ARRAY_A );
	}

	function getDataPayGateId( $id = 0 ) {
		global $wpdb;
		$id = (int) $id;

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}payments_gate WHERE id = {$id}", ARRAY_A );
	}

	function getDataPayGateSourceId( $id = '' ) {
		global $wpdb;
		$id = (int) $id;

		return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}payments_gate WHERE source_id = {$id}", ARRAY_A );
	}

	function getPendingPayGateSourceId( $id = '' ) {
		global $wpdb;
		$id = (int) $id;

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}payments_gate WHERE source_id = {$id} AND status = 'pending' ORDER BY created DESC", ARRAY_A );
	}

	function updPayGateTRZ( $upd = [], $trz_id = '', $type_order = '' ) {
		global $wpdb;

		$where[ 'trz_id' ] = $trz_id;
		if ( ! empty( $type_order ) ) {
			$where[ 'type_order' ] = $type_order;
		}
		$upd[ 'updated' ] = review_rating_init()::getTimestamp();

		return $wpdb->update( "{$wpdb->prefix}payments_gate", $upd, $where );
	}

	function updPayGateSourceId( $id = 0, $upd = [], $where = [] ) {
		global $wpdb;

		$where            = array_merge( $where, [ 'source_id' => (int) $id ] );
		$upd[ 'updated' ] = review_rating_init()::getTimestamp();

		return $wpdb->update( "{$wpdb->prefix}payments_gate", $upd, $where );
	}

	function updDataPayGateId( $id = 0, $upd = [] ) {
		global $wpdb;

		if ( ! empty( $upd ) ) {
			$upd[ 'updated' ] = review_rating_init()::getTimestamp();
			$where[ 'id' ]    = (int) $id;

			return $wpdb->update( "{$wpdb->prefix}payments_gate", $upd, $where );
		}

		return false;
	}

	function updDataPayGate( $upd = [], $where = [] ) {
		global $wpdb;

		if ( ! empty( $upd ) && ! empty( $where ) ) {
			$upd[ 'updated' ] = review_rating_init()::getTimestamp();

			return $wpdb->update( "{$wpdb->prefix}payments_gate", $upd, $where );
		}

		return false;
	}

	function setClosePendPGateSourceId( $id = 0 ) {
		global $wpdb;

		$upd[ 'status' ]      = 'closed';
		$upd[ 'updated' ]     = review_rating_init()::getTimestamp();
		$where[ 'source_id' ] = (int) $id;
		$where[ 'status' ]    = 'pending';

		return $wpdb->update( "{$wpdb->prefix}payments_gate", $upd, $where );
	}

	function setClosePayGateId( $id = 0 ) {
		global $wpdb;

		$upd[ 'status' ]  = 'closed';
		$upd[ 'updated' ] = review_rating_init()::getTimestamp();
		$where[ 'id' ]    = (int) $id;

		return $wpdb->update( "{$wpdb->prefix}payments_gate", $upd, $where );
	}

	function setCompletePayGateId( $id = 0 ) {
		global $wpdb;

		$upd[ 'status' ]  = 'success';
		$upd[ 'updated' ] = review_rating_init()::getTimestamp();
		$where[ 'id' ]    = (int) $id;

		return $wpdb->update( "{$wpdb->prefix}payments_gate", $upd, $where );
	}

	function getQueryOrderId() {
		$result = explode( '/', trim( $_SERVER[ 'REQUEST_URI' ], '/' ) );

		return (int) $result[ 1 ];
	}

