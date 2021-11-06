<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
new WPP_Notis();

class WPP_Notis extends WPP_Messages {
	public static $instance;

	function __construct() {

		parent::__construct();


		// catch action insert new bid to notify employer
		add_action( 'ae_insert_bid', [ __ClASS__, 'new_bid' ], 10, 2 );
		add_action( 'bid_edit', [ __ClASS__, 'bid_edited' ] );
		add_action( 'fre_accept_bid', [ __ClASS__, 'bid_accepted' ] );
		add_action( 'ask_final_bid', [ __ClASS__, 'ask_final_bid' ] );

		// catch action insert new bid to notify employer
		//add_action('wpp_insert_project',[ __ClASS__, 'newProject'], 10, 2);
		// catch action insert new bid to notify employer
		//add_action('wpp_update_project',[ __ClASS__, 'updateProject'], 10, 2);
		// catch action a bid accepted and notify freelancer

		// catch action to ask final bid from PRO-user

		add_action( 'reply_added', [ __ClASS__, 'replyAdded' ] );
		//add_action( 'reply_added_emp', [ __ClASS__, 'replyAddedEmp' ] );

		// add action when employer complete project
		add_action( 'wpp_complete_project', [ __ClASS__, 'completeProject' ], 10, 2 );
		// add action review project owner
		add_action( 'wpp_freelancer_review_employer', [ __ClASS__, 'reviewProjectOwner' ], 10, 2 );
		// add a notification when have new message
		add_action( 'wpp_send_message', [ __ClASS__, 'newMessage' ], 10, 3 );
		//add_action( 'wpp_new_invite', [ __ClASS__, 'newInvite' ], 10, 3 );  - когда напрямую приглашен в проект
		add_action( 'wpp_new_referral', [ __ClASS__, 'newReferral' ], 10, 2 );

		add_action( 'wpp_update_user', [ __ClASS__, 'clearNotify' ], 10, 2 );

		add_action( 'ae_convert_notify', [ __ClASS__, 'convert_notify' ] );

		add_action( 'wp_footer', [ __ClASS__, 'render_template_js' ] );
		add_action( 'template_redirect', [ __ClASS__, 'mark_user_read_message' ] );
		add_action( 'transition_comment_status', [ __ClASS__, 'wpp_approve_comment_callback' ], 10, 3 );
		add_action( 'wp_insert_comment', [ __ClASS__, 'wpp_auto_approve_comment_callback' ], 11, 2 );
		add_action( 'wpp_report_close_project', [ __ClASS__, 'close_project' ], 10, 3 );
		add_action( 'wpp_report_quit_project', [ __ClASS__, 'quit_project' ], 10, 3 );
		add_action( 'wpp_reject_post', [ __ClASS__, 'reject_project' ], 10, 3 );
		add_action( 'wpp_lock_upload_file', [ __ClASS__, 'act_lock_upload_file' ], 10, 3 );
		add_action( 'wpp_unlock_upload_file', [ __ClASS__, 'act_unlock_upload_file' ], 10, 3 );
		add_action( 'wpp_publish_post', [ __ClASS__, 'publish_project' ], 10, 3 );
		add_action( 'wpp_archive_post', [ __ClASS__, 'archive_project' ], 10, 3 );
		add_action( 'wpp_delete_post', [ __ClASS__, 'delete_project' ], 10, 3 );
		add_action( 'wpp_after_update_order', [ __ClASS__, 'update_order' ], 10, 3 );

		add_action( 'wpp_report_dispute_project', [ __ClASS__, 'admin_report_dispute_project_freelancer' ], 10, 3 );
		add_action( 'wpp_report_dispute_project', [ __ClASS__, 'admin_report_dispute_project_employer' ], 10, 3 );

		add_action( 'wpp_resolve_project_notification', [ __ClASS__, 'resolve_project_employer' ], 10, 3 );
		add_action( 'wpp_resolve_project_notification', [ __ClASS__, 'resolve_project_freelancer' ], 10, 3 );

		//add_ajax( 'ae-notify-sync', [ __ClASS__, 'notify_sync' ] );
		//add_ajax( 'ae-fetch-notify', [ __ClASS__, 'fetch_post' ] );
	}


	/**
	 * getInstance method
	 *
	 */
	public static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * При оценке скилов
	 */
	function skills_endorsed() {
		/**
		 * @todo Запустить счетчик при первой оценке затем по окончанию счетчика проверить измения в ксилах и отправить уведомление без легенды
		 */
	}

	/**
	 * Количество нотифиуаций
	 *
	 * @param $user_id
	 * @param $target - если true то уменьшаем на 1
	 */
	public static function update_notify_count( $user_id, $target = false ) {
		$number = get_user_meta( $user_id, 'wpp_new_notify', true );

		if ( ! empty( $number ) ) {
			$form = ! empty( $target ) ? (int) $number + 1 : (int) $number - 1;
		}

		$number = isset( $number ) && $number > 0 ? $form : 0;
		update_user_meta( $user_id, 'wpp_new_notify', $number );
	}

	/**
	 * Новое предложение
	 *
	 * @param $bid
	 * @param $args
	 */
	public static function new_bid( $bid, $args ) {


		$project = get_post( $args['post_parent'] );

		$data = [
			'user_id' => $project->post_author,
			'post_id' => (int) $args['post_parent'],
			'text'    => "type=new_bid&project={$args['post_parent']}&bid={$bid}",
			'date'    => current_time( 'mysql' ),
			'title'   => sprintf( __( "New bid on %s", ET_DOMAIN ), get_the_title( $project->ID ) ),
			'group'   => '1' //
		];

		$notify_id = self::insert( $data );
		self::update_notify_count( $project->post_author );
		update_post_meta( $bid, 'notify_id', $notify_id );

		__return_false();
	}


	/**
	 * Редактирвание бида
	 *
	 * @param $data
	 *
	 * @return int|WP_Error
	 */
	public static function bid_edited( $data ) {

		$project = get_post( $data['post_parent'] );

		$bid_ID = $data['bid_id'];

		$data = [
			'user_id' => $project->post_author,
			'post_id' => (int) $data['post_parent'],
			'text'    => "type=bid_edited&project={$data['post_parent']}&bid_id={$bid_ID}",
			'date'    => current_time( 'mysql' ),
			'title'   => sprintf( __( "The freelancer changed his bid for your project %s", ET_DOMAIN ), get_the_title( $data['post_parent'] ) ),
			'group'   => '1'
		];


		$notify_id = self::insert( $data );
		self::update_notify_count( $project->post_author );
		update_post_meta( $bid_ID, 'notify_id', $notify_id );

		__return_false();
	}

	/**
	 * ПРием Бида
	 *
	 * @param $bid_id
	 *
	 * @return int|void|WP_Error
	 */
	public static function bid_accepted( $bid_id ) {

		$bid = get_post( $bid_id );
		if ( ! $bid || is_wp_error( $bid ) ) {
			return;
		}

		$project_id = $bid->post_parent;
		$project    = get_post( $project_id );
		if ( ! $project || is_wp_error( $project ) ) {
			return;
		}

		$data = [
			'user_id' => $bid->post_author,
			'post_id' => (int) $project_id,
			'text'    => "type=bid_accept&project={$project_id}",
			'date'    => current_time( 'mysql' ),
			'title'   => sprintf( __( "Bid on project %s was accepted", ET_DOMAIN ), get_the_title( $project->ID ) ),
			'group'   => '1'
		];

		$notify_id = self::insert( $data );

		if ( ! empty( $notify_id ) ) {
			self::update_notify_count( $project->post_author );

			update_post_meta( $bid_id, 'notify_id', $notify_id );

			return $notify_id;
		}

		__return_false();

	}


	/**
	 * Запрс финального
	 *
	 * @param $bid_id
	 *
	 * @return int|void|WP_Error
	 */
	public static function ask_final_bid( $bid_id ) {
		$bid = get_post( $bid_id );
		if ( ! $bid || is_wp_error( $bid ) ) {
			return;
		}

		$project_id = $bid->post_parent;
		$project    = get_post( $project_id );
		if ( ! $project || is_wp_error( $project ) ) {
			return;
		}

		$data = [
			'user_id' => $bid->post_author,
			'post_id' => (int) $project_id,
			'text'    => "type=final_bid&project={$project_id}",
			'date'    => current_time( 'mysql' ),
			'title'   => sprintf( __( "Client has requested the final bid for %s", ET_DOMAIN ), get_the_title( $project->ID ) ),
			'group'   => '1'
		];

		$notify_id = self::insert( $data );

		if ( ! empty( $notify_id ) ) {
			self::update_notify_count( $project->post_author );

			update_post_meta( $bid_id, 'notify_id', $notify_id );

			return $notify_id;
		}

		__return_false();

	}

	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/
	/******************************************************************************************************/

	public static function resolve_project_freelancer( $project_id ) {
		global $user_ID;
		$project      = get_post( $project_id );
		$bid_id       = get_post_meta( $project_id, 'accepted', true );
		$bid_author   = get_post_field( 'post_author', $bid_id );
		$content      = 'type=resolve_project&project=' . $project_id . '&admin=' . $user_ID;
		$title        = __( 'Resolve the disputed project' );
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $bid_author,
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	public static function admin_report_dispute_project_freelancer( $project_id, $report ) {
		global $user_ID;
		$project = get_post( $project_id );
		// Freelancer
		$bid_id     = get_post_meta( $project_id, 'accepted', true );
		$bid_author = get_post_field( 'post_author', $bid_id );
		if ( $bid_author == $user_ID ) {
			return;
		}
		$content      = 'type=report_dispute_project&project=' . $project_id . '&admin=' . $user_ID;
		$title        = __( 'Admin send Professional a report' );
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $bid_author,
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	public static function admin_report_dispute_project_employer( $project_id, $report ) {
		global $user_ID;
		$project = get_post( $project_id );
		if ( $project->post_author == $user_ID ) {
			return;
		}
		// Employer
		$content      = 'type=report_dispute_project&project=' . $project_id . '&admin=' . $user_ID;
		$title        = __( 'Admin send Client a report' );
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $project->post_author,
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	public static function update_order( $order_id ) {
		global $user_ID;
		$order = get_post( $order_id );
		// check status
		if ( ! in_array( $order->post_status, [ 'publish', [ __ClASS__, 'draft' ] ] ) ) {
			return;
		}

		switch ( $order->post_status ) {
			case 'publish':
				$content = 'type=approve_order&order=' . $order_id . '&admin=' . $user_ID;
				$title   = sprintf( __( 'Approve payment', ET_DOMAIN ), $order->post_title );
				break;
			case 'draft':
				$content = 'type=cancel_order&order=' . $order_id . '&admin=' . $user_ID;
				$title   = sprintf( __( 'Cancel payment', ET_DOMAIN ), $order->post_title );
				break;
			default:
				$content = '';
				$title   = '';
				break;
		}
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $order->post_author, // notify to employer
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_parent'  => $order_id
		];

		return $this->insert( $notification );
	}

	/**
	 * Уборка проекта в архив
	 *
	 * @param $args
	 *
	 * @return int|void|WP_Error
	 */
	public static function archive_project( $args ) {
		global $user_ID;
		$project_id = $args['ID'];
		$post       = get_post( $project_id );
		if ( $user_ID == $post->post_author ) {
			return;
		}
		$content      = 'type=archive_project&project=' . $project_id . '&admin=' . $user_ID;
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $args['post_author'], // notify to Freelance
			'post_title'   => __( "Archive the draft project", ET_DOMAIN ),
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	/**
	 * Удаление Проекта
	 *
	 * @param $args
	 *
	 * @return int|void|WP_Error
	 */
	public static function delete_project( $args ) {
		global $user_ID;
		$project_id = $args['ID'];
		$post       = get_post( $project_id );
		if ( $user_ID == $post->post_author ) {
			return;
		}

		$content      = 'type=delete_project&project=' . $project_id . '&admin=' . $user_ID;
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $args['post_author'], // notify to Freelance
			'post_title'   => __( "Archive the draft project", ET_DOMAIN ),
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	/**
	 * Отмена Пректа
	 *
	 * @param $args
	 *
	 * @return int|void|WP_Error
	 */
	public static function reject_project( $args ) {
		global $user_ID;
		$project_id = $args['ID'];
		$post       = get_post( $project_id );
		if ( $user_ID == $post->post_author ) {
			return;
		}
		$content      = 'type=reject_project&project=' . $project_id . '&admin=' . $user_ID;
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $args['post_author'], // notify to Freelance
			'post_title'   => sprintf( __( "Reject the pending project %s", ET_DOMAIN ), get_the_title( $project_id ) ),
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	/**
	 * Блокировка файла
	 *
	 * @param $project_id
	 *
	 * @return int|void|WP_Error
	 */
	public static function act_lock_upload_file( $project_id ) {
		global $user_ID, $wpp_post_factory;
		$post          = get_post( $project_id );
		$projects_data = $wpp_post_factory->get( PROJECT );
		$projects      = $projects_data->convert( $post );
		$bids          = get_post( $projects->accepted );
		if ( $user_ID != $post->post_author ) {
			return;
		}
		$content      = 'type=lock_file_project&project=' . $project_id . '&sender=' . $user_ID;
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $bids->post_author, // notify to Freelance
			'post_title'   => sprintf( __( "%s locked the file section in the project %s. Click here for details.", ET_DOMAIN ), get_the_author_meta( 'display_name', $post->post_author ), get_the_title( $project_id ) ),
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	/**
	 * Разблокировка файлв
	 *
	 * @param $project_id
	 *
	 * @return int|void|WP_Error
	 */
	public static function act_unlock_upload_file( $project_id ) {
		global $user_ID, $wpp_post_factory;
		$post          = get_post( $project_id );
		$projects_data = $wpp_post_factory->get( PROJECT );
		$projects      = $projects_data->convert( $post );
		$bids          = get_post( $projects->accepted );
		if ( $user_ID != $post->post_author ) {
			return;
		}
		$content      = 'type=unlock_file_project&project=' . $project_id . '&sender=' . $user_ID;
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $bids->post_author, // notify to Freelance
			'post_title'   => sprintf( __( "%s unlocked the file section in the project %s. Click here for details.", ET_DOMAIN ), get_the_author_meta( 'display_name', $post->post_author ), get_the_title( $project_id ) ),
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	/**
	 * Публикация проекта
	 *
	 * @param $args
	 *
	 * @return int|void|WP_Error
	 */
	public static function publish_project( $args ) {
		global $user_ID;
		$project_id = $args['ID'];
		$post       = get_post( $project_id );
		if ( $user_ID == $post->post_author ) {
			return;
		}
		$content      = 'type=publish_project&project=' . $project_id . '&admin=' . $user_ID;
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $args['post_author'], // notify to employer
			'post_title'   => sprintf( __( "Approve the pending project %s", ET_DOMAIN ), get_the_title( $project_id ) ),
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	/**
	 * Закрытие проекта
	 *
	 * @param $project_id
	 *
	 * @return int|WP_Error
	 */
	public static function close_project( $project_id ) {
		$bid_id       = get_post_meta( $project_id, 'accepted', true );
		$bid_author   = get_post_field( 'post_author', $bid_id );
		$content      = 'type=close_project&project=' . $project_id;
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $bid_author, // notify to Freelance
			'post_title'   => sprintf( __( "Close project %s", ET_DOMAIN ), get_the_title( $project_id ) ),
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	/**
	 * Отказаться от пректа
	 *
	 * @param $project_id
	 *
	 * @return int|WP_Error
	 */
	public static function quit_project( $project_id ) {
		$bid_id        = get_post_meta( $project_id, 'accepted', true );
		$bid_author    = get_post_field( 'post_author', $bid_id );
		$project_owner = get_post_field( 'post_author', $project_id );
		$content       = 'type=quit_project&project=' . $project_id;
		$notification  = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $project_owner, // notify to Freelance
			'post_title'   => sprintf( __( "Quit project %s", ET_DOMAIN ), get_the_title( $project_id ) ),
			'post_status'  => 'publish',
			'post_parent'  => $project_id
		];

		return $this->insert( $notification );
	}

	/**
	 * Новый проект
	 *
	 * @param $project
	 * @param $args
	 *
	 * @return int|WP_Error
	 */
	function newProject( $project, $args ) {
		$project   = get_post( $project );
		$content   = 'type=new_project&project=' . $project->ID;
		$blogusers = get_users( 'role=administrator' );
		$bloguser  = $blogusers['0'];
		// insert notification
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $bloguser->ID,
			'post_title'   => sprintf( __( "New project %s is created", ET_DOMAIN ), get_the_title( $project->ID ) ),
			'post_status'  => 'publish',
			'post_parent'  => $project->ID
		];

		return $this->insert( $notification );
	}

	/**
	 * Обновление Пректа
	 *
	 * @param $project
	 * @param $args
	 *
	 * @return int|WP_Error
	 */
	public static function updateProject( $project, $args ) {
		if ( isset( $args['renewe'] ) ) {
			$project   = get_post( $project );
			$content   = 'type=renew_project&project=' . $project->ID;
			$blogusers = get_users( 'role=administrator' );
			$bloguser  = $blogusers['0'];
			// insert notification
			$notification = [
				'post_type'    => $this->post_type,
				'post_content' => $content,
				'post_excerpt' => $content,
				'post_author'  => $bloguser->ID,
				'post_title'   => sprintf( __( "project %s is renewed", ET_DOMAIN ), get_the_title( $project->ID ) ),
				'post_status'  => 'publish',
				'post_parent'  => $project->ID
			];

			return $this->insert( $notification );
		}
	}


	/**
	 * Ответ
	 *
	 * @param $data
	 *
	 * @return int|WP_Error
	 */
	public static function replyAdded( $data ) {
		$project_id    = $data['project'];
		$freelancer_id = $data['freelancer'];

		$content = 'type=reply_added&project=' . $project_id;

		// insert notification
		$notification = [
			'post_type'    => $this->post_type,
			'post_parent'  => $project_id,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_status'  => 'publish',
			'post_author'  => $freelancer_id,
			'post_title'   => sprintf( __( "Client replied to your review for project %s", ET_DOMAIN ), get_the_title( $project_id ) )
		];

		return $this->insert( $notification );
	}

	public static function replyAddedEmp( $data ) {
		$project_id  = $data['project'];
		$employer_id = $data['employer'];

		$content = 'type=reply_added_emp&project=' . $project_id;

		// insert notification
		$notification = [
			'post_type'    => $this->post_type,
			'post_parent'  => $project_id,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_status'  => 'publish',
			'post_author'  => $employer_id,
			'post_title'   => sprintf( __( "Client replied to your review for project %s", ET_DOMAIN ), get_the_title( $project_id ) )
		];

		return $this->insert( $notification );
	}


	/**
	 * Выполненый проект
	 *
	 * @param $project_id
	 * @param $args
	 *
	 * @return int|WP_Error
	 */
	public static function completeProject( $project_id, $args ) {

		$content    = 'score=' . $args['score'] . '&type=complete_project&project=' . $project_id;
		$project    = get_post( $project_id );
		$bid_id     = get_post_meta( $project_id, 'accepted', true );
		$bid_author = get_post_field( 'post_author', $bid_id );

		// insert notification
		$notification = [
			'post_type'    => $this->post_type,
			'post_parent'  => $project_id,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $bid_author,
			'post_status'  => 'publish',
			'post_title'   => sprintf( __( "Project %s was completed", ET_DOMAIN ), get_the_title( $project->ID ) )
		];

		return $this->insert( $notification );
	}


	public static function reviewProjectOwner( $project_id, $args ) {
		global $user_ID;
		$content = 'score=' . $args['score'] . '&type=review_project&project=' . $project_id;
		$project = get_post( $project_id );

		$project_title = get_the_title( $project->ID );
		$bidder_name   = get_the_author_meta( 'display_name', $user_ID );

		// insert notification
		$notification = [
			'post_type'    => $this->post_type,
			'post_parent'  => $project_id,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $project->post_author,
			'post_status'  => 'publish',
			'post_title'   => sprintf( __( "%s reviewed project %s", ET_DOMAIN ), $bidder_name, $project_title )
		];

		return $this->insert( $notification );
	}

	/**
	 * Новое Сообщение
	 *
	 * @param $message
	 * @param $project
	 * @param $bid
	 *
	 * @return int|void|WP_Error
	 */
	public static function newMessage( $message, $project, $bid ) {
		global $user_ID;

		$content      = 'type=new_message&project=' . $project->ID . '&sender=' . $user_ID;
		$notification = [
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_status'  => 'publish',
			'post_type'    => $this->post_type,
			'post_title'   => sprintf( __( "New message on project %s", ET_DOMAIN ), get_the_title( $project->ID ) ),
			'post_parent'  => $project->ID
		];

		// update notify for freelancer if current user is project owner
		if ( $user_ID == $project->post_author ) {
			$notification['post_author'] = $bid->post_author;
		}

		// update notify to employer if freelancer send message
		if ( $user_ID == $bid->post_author ) {
			$notification['post_author'] = $project->post_author;
		}

		$have_new = get_post_meta( $project->ID, $user_ID . '_new_message', true );

		if ( $have_new ) {
			return;
		}
		update_post_meta( $project->ID, $user_ID . '_new_message', true );

		$mail = wpp_Mailing::get_instance();
		$mail->new_message( $notification['post_author'], $project, $message );

		return $this->insert( $notification );
	}


	public static function newInvite( $invited, $send_invite, $list_project ) {
		global $user_ID;
		$content = 'type=new_invite&send_invite=' . $send_invite;

		$author      = get_the_author_meta( 'display_name', $invited );
		$send_author = get_the_author_meta( 'display_name', $send_invite );
		// insert notification
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $invited,
			'post_status'  => 'publish',
			'post_title'   => sprintf( __( "%s have a new invite from %s", ET_DOMAIN ), $author, $send_author )
		];
		$notify       = $this->insert( $notification );
		update_post_meta( $notify, 'project_list', $list_project );

		return $notify;
	}

	/**
	 * Новый рнферал
	 *
	 * @param $invited
	 * @param $send_invite
	 *
	 * @return int|WP_Error
	 */
	public static function newReferral( $invited, $send_invite ) {
		global $user_ID;
		$content = 'type=new_referral&send_invite=' . $send_invite;

		$author      = get_the_author_meta( 'display_name', $invited );
		$send_author = get_the_author_meta( 'display_name', $send_invite );
		// insert notification
		$notification = [
			'post_type'    => $this->post_type,
			'post_content' => $content,
			'post_excerpt' => $content,
			'post_author'  => $invited,
			'post_status'  => 'publish',
			'post_title'   => sprintf( __( "%s have a new referral %s", ET_DOMAIN ), $author, $send_author )
		];
		$notify       = $this->insert( $notification );

		//update_user_meta( $notify, 'project_list', $list_project );

		return $notify;
	}


	public static function clearNotify( $user_id, $data ) {
		global $user_ID;
		if ( $user_ID != $user_id ) {
			return $user_id = $user_ID;
		}
		if ( isset( $data['read_notify'] ) && $data['read_notify'] ) {
			delete_user_meta( $user_id, 'wpp_new_notify' );
		}

		return $user_id;
	}


	public static public static function convert_notify( $notify ) {
		$notify->content = self::wpp_notify_item( $notify );

		return $notify;
	}


	public static function wpp_notify_item( $notify ) {
		// parse post excerpt to get data
		$excerpt      = str_replace( '&amp;', '&', $notify->text );
		$project_link = $content = '';

		if ( ! empty( $excerpt ) ) {
			parse_str( $excerpt, $data );
			extract( $data );
		}

		//type=bid_edited&project=16384&bid_id=16396

		if ( ! isset( $type ) || ! $type ) {
			return;
		}

		if ( ! in_array( $type, [
			'new_invite',
			'approve_order',
			'cancel_order',
			'approve_withdraw',
			'cancel_withdraw',
			'new_referral'
		] ) ) {

			if ( empty( $project ) ) {
				__return_false();
			}
			$project_post = get_post( $project );

			// check project exists or deleted
			if ( empty( $project_post ) || is_wp_error( $project_post ) ) {
				__return_false();
			}

			$project_link = '<a class="project-link" href="' . get_permalink( $project ) . '" >' . get_the_title( $project ) . '</a>';

		}

		$content = apply_filters( 'wpp_notify_item', $content, $notify );


		$template_without_avatar = <<<TEMPLATE
            <div class="fre-notify-wrap">
                <span class="notify-info">%s</span>
                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">%s</div>
                <div class="col-sm-6 col-xs-6 notify-time text-right">%s</div></div>
            </div>
TEMPLATE;


		switch ( $type ) {
			// notify employer when his project have new bid
			case 'new_bid':
				$bid_author = get_post_field( 'post_author', $bid );
				$message    = sprintf( '<strong class="notify-name">%s</strong> %s %s', get_the_author_meta( 'display_name', $bid_author ), __( 'bid on your project', ET_DOMAIN ), $project_link );
				$content    .= sprintf( $template_without_avatar, $message, get_the_date( '', $bid ), get_the_time( '', $bid ) );
				break;

			// notify client when freelancer change his bid for the project
			case 'bid_edited':
				$bid_author = get_post_field( 'post_author', $bid_id );
				$message    = sprintf( __( '<strong class="notify-name">%s</strong> changed his bid for your project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $bid_author ), '<a href="' . get_permalink( $project ) . '?workspace=1">' . get_the_title( $project ) . '</a>' );
				$content    .= sprintf( $template_without_avatar, $message, get_the_date( '', $bid_id ), get_the_time( '', $bid_id ) );
				/*$content .= '<div class="fre-notify-wrap">
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';*/
				break;

			case 'resolve_project':
				// text : [Admin] Resolved the disputed project [dispute_project_name]
				$message = sprintf( __( '<strong class="notify-name">%s</strong> resolved the disputed project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $admin ), '<a href="' . get_permalink( $project ) . '?dispute=1">' . get_the_title( $project ) . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $admin, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'report_dispute_project':
				// text : [Admin] Sent you a report on [dispute_detail_page]
				$classAdmin = '';
				if ( wpp_user_role( $admin ) == 'administrator' ) {
					$classAdmin = 'author-admin';
				}
				$message = sprintf( __( '<strong class="notify-name">%s</strong> left you a message on %s dispute page', ET_DOMAIN ), get_the_author_meta( 'display_name', $admin ), '<a href="' . get_permalink( $project ) . '?dispute=1">' . get_the_title( $project ) . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $admin, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'approve_order':
				$order_type = get_post_meta( $order, 'et_order_product_type', true );
				//$href       = et_get_page_link( 'profile' );
				$href = '';
				if ( $order_type == 'wpp_credit_plan' ) {
					$href = ' href="' . et_get_page_link( 'my-credit' ) . '" ';
				}
				$package = current( get_post_meta( $order, 'et_order_products', true ) );

				// text : [Admin] Approved your payment on [package_name] package
				$message = sprintf( __( '<strong class="notify-name">%s</strong> approved your payment on %s package', ET_DOMAIN ), get_the_author_meta( 'display_name', $admin ), '<a href="' . $href . '">' . $package['NAME'] . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $admin, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'cancel_order':
				$order_type = get_post_meta( $order, 'et_order_product_type', true );
				$href       = et_get_page_link( 'profile' );
				if ( $order_type == 'wpp_credit_plan' ) {
					$href = et_get_page_link( 'my-credit' );
				}
				$package = current( get_post_meta( $order, 'et_order_products', true ) );

				// text : [Admin] Declined your payment on [package_name] package
				$message = sprintf( __( '<strong class="notify-name">%s</strong> declined your payment on %s package', ET_DOMAIN ), get_the_author_meta( 'display_name', $admin ), '<a href="' . $href . '">' . $package['NAME'] . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $admin, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'reject_project':
				// text: [Admin] Rejected your project [project_name]
				$message = sprintf( __( '<strong class="notify-name">%s</strong> rejected your project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $admin ), '<a href="' . get_permalink( $project ) . '">' . get_the_title( $project ) . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $admin, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'archive_project':
				// text: [Admin] Archived your draft project [project_name]
				$message = sprintf( __( '<strong class="notify-name">%s</strong> archived your draft project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $admin ), '<a href="' . get_permalink( $project ) . '">' . get_the_title( $project ) . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $admin, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'delete_project':
				// text: [Admin] Deleted your archived project [project_name]
				$message = sprintf( __( '<strong class="notify-name">%s</strong> deleted your archived project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $admin ), '<a href="' . get_permalink( $project ) . '">' . get_the_title( $project ) . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $admin, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'publish_project':
				// text: [Admin] Approved your project [project_name]
				$message = sprintf( __( '<strong class="notify-name">%s</strong> approved your project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $admin ), '<a href="' . get_permalink( $project ) . '">' . get_the_title( $project ) . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $admin, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'close_project':
				// Text: <employer> closed your working project <project_title>
				$project_owner = get_post_field( 'post_author', $project );
				$message       = sprintf( __( '<strong class="notify-name">%s</strong> closed the project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $project_owner ), '<a href="' . get_permalink( $project ) . '?dispute=1">' . get_the_title( $project ) . '</a>' );
				$content       .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $project_owner, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'quit_project':
				// Text: <freelancer> discontinued on your project <project_title>
				$project_owner = get_post_field( 'post_author', $project );
				$bid_id        = get_post_meta( $project, 'accepted', true );
				$bid_author    = get_post_field( 'post_author', $bid_id );
				$message       = sprintf( __( '<strong class="notify-name">%s</strong> discontinued your project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $bid_author ), '<a href="' . get_permalink( $project ) . '?dispute=1">' . get_the_title( $project ) . '</a>' );
				$content       .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $bid_author, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'delete_bid':
				// Text: <freelancer> cancelled his bid on your project <project_title>
				$message = sprintf( __( '<strong class="notify-name">%s</strong> cancelled his bid on your project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $freelancer ), '<a href="' . get_permalink( $project ) . '">' . get_the_title( $project ) . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $freelancer, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			// notify freelancer when he get reply oh his review
			case 'reply_added':
				// Text: Client replied to your review for project <project_title>
				$accepted_bid = get_post_meta( $project, 'accepted', true );
				$bid_author   = get_post_field( 'post_author', $accepted_bid );
				$message      = sprintf( __( '<strong class="notify-name">%s</strong> left a reply on your review', ET_DOMAIN ), get_the_author_meta( 'display_name', $bid_author ) );

				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			// notify employer when he get reply oh his review
			case 'reply_added_emp':
				// Text: Client replied to your review for project <project_title>
				$accepted_bid = get_post_meta( $project, 'accepted', true );
				$bid_author   = get_post_field( 'post_author', $accepted_bid );
				//$professional_reply = get_post_field( 'post_author', $project );
				$message = sprintf( __( '<strong class="notify-name">%s</strong> left a reply on your review', ET_DOMAIN ), get_the_author_meta( 'display_name', $bid_author ) );

				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;


			case 'bid_accept':
				// Text: <employer> accepted your bid on the project <project_title>
				$project_owner = get_post_field( 'post_author', $project );
				$message       = sprintf( __( '<strong class="notify-name">%s</strong> accepted your bid on the project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $project_owner ), '<a href="' . get_permalink( $project ) . '">' . get_the_title( $project ) . '</a>' );
				$content       .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $project_owner, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;

			case 'final_bid':
				// Text: <employer> is asking you about final bid <project_title>
				$project_owner = get_post_field( 'post_author', $project );
				$message       = sprintf( __( '<strong class="notify-name">%s</strong> has requested the final bid for %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $project_owner ), '<a href="' . get_permalink( $project ) . '">' . get_the_title( $project ) . '</a>' );
				$content       .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $project_owner, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;

			case 'complete_project':
				// Text: <employer> finished your working project <project_title>
				$project_owner = get_post_field( 'post_author', $project );
				$message       = sprintf( __( '<strong class="notify-name">%s</strong> has finished your project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $project_owner ), '<a href="' . get_permalink( $project ) . '?workspace=1">' . get_the_title( $project ) . '</a>' );
				$content       .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $project_owner, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;

			case 'review_project':
				// Text: <freelancer> reviewed on your project <project_title>
				$accepted_bid = get_post_meta( $project, 'accepted', true );
				$bid_author   = get_post_field( 'post_author', $accepted_bid );
				$message      = sprintf( __( '<strong class="notify-name">%s</strong> left a review on your project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $bid_author ), '<a href="' . get_permalink( $project ) . '?workspace=1">' . get_the_title( $project ) . '</a>' );
				$content      .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $bid_author, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;

			case 'new_message':
				// Text: <employer> sent you a message on the <project_title> workspace
				$user_profile_id = get_user_meta( $sender, 'user_profile_id', true );
				if ( ! isset( $sender ) ) {
					$sender = 1;
				}
				$workspace_link = add_query_arg( [ 'workspace' => 1 ], get_permalink( $project ) );
				$message        = sprintf( __( '<strong class="notify-name">%s</strong> sent you a message in %s workspace ', ET_DOMAIN ), get_the_author_meta( 'display_name', $sender ), '<a href="' . $workspace_link . '">' . get_the_title( $project ) . '</a>' );
				$content        .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $sender, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'new_invite':
				$project = get_post_meta( $notify->id, 'project_list', true );
				$message = sprintf( __( '<strong class="notify-name">%s</strong> invited you to join the project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $send_invite ), '<a href="' . get_permalink( $project ) . '">' . get_the_title( $project ) . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $send_invite, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;

			case 'new_referral':
				$project = get_user_meta( $notify->id, 'project_list', true );
				$message = sprintf( __( '<strong class="notify-name">%s</strong> You have a new referral %s', ET_DOMAIN ), get_user_meta( 'nickname', $send_invite ),

					'<a href="' . site_url( '/author/' . get_the_author_meta( 'nickname', $send_invite ) ) . '">' . get_the_author_meta( 'first_name', $send_invite ) . ' ' . get_the_author_meta( 'last_name', $send_invite ) . '</a>' );
				$content .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $send_invite, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;

			case 'new_comment':
				// Text: <user> commented on your project <project_title>
				$workspace_link = add_query_arg( [ 'workspace' => 1 ], get_permalink( $project ) );
				$message        = sprintf( __( '<strong class="notify-name">%s</strong> commented on your project %s', ET_DOMAIN ), get_the_author_meta( 'display_name', $comment_author_id ), '<a href="' . $workspace_link . '">' . get_the_title( $project ) . '</a>' );
				$content        .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $comment_author_id, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'lock_file_project':
				// Text: <employer> sent you a message on the <project_title> workspace
				$user_profile_id = get_user_meta( $sender, 'user_profile_id', true );
				if ( ! isset( $sender ) ) {
					$sender = 1;
				}
				$workspace_link = add_query_arg( [ 'workspace' => 1 ], get_permalink( $project ) );
				$message        = sprintf( __( '<strong class="notify-name">%s</strong> locked the file section in the project %s. Click here for details', ET_DOMAIN ), get_the_author_meta( 'display_name', $sender ), '<a href="' . $workspace_link . '">' . get_the_title( $project ) . '</a>' );
				$content        .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $sender, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			case 'unlock_file_project':
				// Text: <employer> sent you a message on the <project_title> workspace
				$user_profile_id = get_user_meta( $sender, 'user_profile_id', true );
				if ( ! isset( $sender ) ) {
					$sender = 1;
				}
				$workspace_link = add_query_arg( [ 'workspace' => 1 ], get_permalink( $project ) );
				$message        = sprintf( __( '<strong class="notify-name">%s</strong> unlocked the file section in the project %s. Click here for details', ET_DOMAIN ), '<strong class="notify-name">' . get_the_author_meta( 'display_name', $sender ) . '</strong>', [
					__ClASS__,
					'<a href="' . $workspace_link . '">' . get_the_title( $project ) . '</a>'
				] );
				$content        .= '<div class="fre-notify-wrap">
                                <span class="notify-avatar">' . get_avatar( $sender, 65 ) . '</span>
                                <span class="notify-info">' . $message . '</span>
                                <div class="row"><div class="col-sm-6 col-xs-6 notify-time">' . sprintf( __( "%s", ET_DOMAIN ), get_the_date( '', $notify->id ) ) . '</div>
                                <div class="col-sm-6 col-xs-6 notify-time text-right">' . sprintf( __( "%s", ET_DOMAIN ), get_the_time( '', $notify->id ) ) . '</div></div>
                            </div>';
				break;
			default:
				break;
		}

		$content .= '<a class="notify-remove" data-id="' . $notify->id . '"><span></span></a>';

		// return notification content
		return $content;
	}

	/**
	 * render js template for notification
	 *
	 * @since  1.2
	 * @author Dakachi
	 */
	public static function render_template_js() {
		?>
        <script type="text/template" id="ae-notify-loop">
            {{= content }}
        </script>
		<?php
	}

	/**
	 * clear a flag when user read new message
	 *
	 * @param snippet
	 *
	 * @since  snippet.
	 * @author Dakachi
	 */
	public static function mark_user_read_message() {
		if ( isset( $_REQUEST['workspace'] ) && $_REQUEST['workspace'] ) {
			if ( is_singular( PROJECT ) ) {
				global $post, $user_ID;
				delete_post_meta( $post->ID, $user_ID . '_new_message' );
			}
		}
	}

	/**
	 * user can sync notification ( delete )
	 *
	 * @param snippet
	 *
	 * @since  snippet.
	 * @author Dakachi
	 */
	public static function notify_sync() {
		global $wpp_post_factory, $user_ID;
		$request = $_REQUEST;
		// unset($request['post_content']);
		unset( $request['post_excerpt'] );
		if ( isset( $request['delete'] ) ) {
			$request['post_status'] = 'trash';
		}

		$place = $wpp_post_factory->get( $this->post_type );
		// sync notify
		$result = $place->sync( $request );

		wp_send_json_success( [
			'data' => $result,
			'msg'  => __( "Update project successful!", ET_DOMAIN )
		] );

	}

	/**
	 * Notify employer when have new comment approve
	 *
	 * @param $new_status
	 * @param $old_status
	 * @param $comment
	 *
	 * @since  1.7.5
	 * @author Tuandq
	 */
	public static function wpp_approve_comment_callback( $new_status, $old_status, $comment ) {
		$post_status = get_post_field( 'post_status', $comment->comment_post_ID );
		if ( ! in_array( $post_status, [ 'pending', [ __ClASS__, 'publish' ] ] ) ) {
			return;
		}
		if ( $new_status != $old_status ) {
			if ( $new_status == 'approved' ) {
				$post_excerpt = 'type=new_comment&project=' . $comment->comment_post_ID . "&comment_author=" . $comment->comment_author . "&comment_author_id=" . $comment->user_id . "&comment_author_email=" . $comment->comment_author_email;
				$post_author  = get_post_field( 'post_author', $comment->comment_post_ID );
				// insert notification
				$notification = [
					'post_type'    => 'notify',
					'post_content' => $comment->comment_content,
					'post_excerpt' => $post_excerpt,
					'post_author'  => $post_author,
					'post_title'   => sprintf( __( "New comment on %s", ET_DOMAIN ), get_the_title( $comment->comment_post_ID ) ),
					'post_status'  => 'publish',
					'post_parent'  => $comment->comment_post_ID,
					'post_date'    => $comment->comment_date
				];
				// $noti = new wpp_Notification();
				$insert = $this->insert( $notification );
			}
		}
	}

	/**
	 * Notify employer when have new comment auto approve
	 *
	 * @param $comment_id
	 * @param $comment
	 *
	 * @since  1.7.5
	 * @author Tuandq
	 */
	public static function wpp_auto_approve_comment_callback( $comment_id, $comment ) {
		$post_author = get_post_field( 'post_author', $comment->comment_post_ID );
		$post_status = get_post_field( 'post_status', $comment->comment_post_ID );
		if ( ! in_array( $post_status, [ 'pending', __ClASS__, 'publish' ] ) ) {
			return;
		}
		if ( ( ! get_option( 'comment_moderation' ) && $post_author != $comment->user_id ) || ( get_option( 'comment_moderation' ) && current_user_can( 'administrator' ) ) ) {
			$post_excerpt = 'type=new_comment&project=' . $comment->comment_post_ID . "&comment_author=" . $comment->comment_author . "&comment_author_id=" . $comment->user_id . "&comment_author_email=" . $comment->comment_author_email;
			// insert notification
			$notification = [
				'post_type'    => 'notify',
				'post_content' => $comment->comment_content,
				'post_excerpt' => $post_excerpt,
				'post_author'  => $post_author,
				'post_title'   => sprintf( __( "New comment on %s", ET_DOMAIN ), get_the_title( $comment->comment_post_ID ) ),
				'post_status'  => 'publish',
				'post_parent'  => $comment->comment_post_ID,
				'post_date'    => $comment->comment_date
			];
			// $noti = new wpp_Notification();
			$insert = $this->insert( $notification );
		}
	}

}