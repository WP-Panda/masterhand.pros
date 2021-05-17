<?php

class MailsterDashboard {

	private $metaboxes = array();

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'init' ) );
		add_action( 'admin_menu', array( &$this, 'menu' ), -1 );

	}


	public function init() {

		add_filter( 'dashboard_glance_items', array( &$this, 'dashboard_glance_items' ), 99 );
		add_action( 'wp_dashboard_setup', array( &$this, 'add_widgets' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'remove_menu_entry' ), 10, 4 );
		add_filter( 'postbox_classes_newsletter_page_mailster_dashboard_mailster-mb-mailster', array( &$this, 'post_box_classes_for_mailster' ) );

	}


	public function init_page() {

		if ( isset( $_GET['mailster_setup_complete'] ) && wp_verify_nonce( $_GET['mailster_setup_complete'], 'mailster_setup_complete' ) ) {

			if ( ! get_option( 'mailster_setup' ) ) {
				update_option( 'mailster_setup', time() );
			}
			wp_redirect( admin_url( 'admin.php?page=mailster_dashboard' ) );
			exit;

		}

		if ( isset( $_GET['reset_license'] ) && wp_verify_nonce( $_GET['reset_license'], 'mailster_reset_license' ) && current_user_can( 'mailster_manage_licenses' ) ) {

			$result = mailster()->reset_license();

			if ( is_wp_error( $result ) ) {
				mailster_notice( esc_html__( 'There was an Error while processing your request!', 'mailster' ) . '<br>' . $result->get_error_message(), 'error', true );
			} else {
				update_option( 'mailster_license', '' );
				mailster_notice( esc_html__( 'Your License has been reset!', 'mailster' ), '', true );
			}

			wp_redirect( admin_url( 'admin.php?page=mailster_dashboard' ) );
			exit;
		}

		if ( ! get_option( 'mailster_setup' ) ) {
			wp_redirect( admin_url( 'admin.php?page=mailster_setup' ) );
			exit;
		}

	}


	public function remove_menu_entry() {

		if ( current_user_can( 'mailster_dashboard' ) ) {
			wp_add_inline_style( 'mailster-admin', '@media only screen and (min-width: 783px){#menu-posts-newsletter .wp-first-item{display: none;}}' );
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function menu() {

		global $submenu;

		if ( ! current_user_can( 'mailster_dashboard' ) ) {
			return;
		}

		$slug = 'edit.php?post_type=newsletter';

		$page = add_submenu_page( $slug, esc_html__( 'Mailster Dashboard', 'mailster' ), esc_html__( 'Dashboard', 'mailster' ), 'mailster_dashboard', 'mailster_dashboard', array( &$this, 'dashboard' ) );
		add_action( 'load-' . $page, array( &$this, 'init_page' ) );
		add_action( 'load-' . $page, array( &$this, 'scripts_styles' ) );
		add_action( 'load-' . $page, array( &$this, 'register_meta_boxes' ) );

		if ( isset( $submenu[ $slug ][11] ) ) {
			$submenu[ $slug ][0] = $submenu[ $slug ][11];
			unset( $submenu[ $slug ][11] );
			ksort( $submenu[ $slug ] );
		}

	}


	public function dashboard() {

		$this->update       = mailster()->has_update();
		$this->verified     = mailster()->is_verified();
		$this->plugin_info  = mailster()->plugin_info();
		$this->is_dashboard = false;

		$this->screen = get_current_screen();

		include MAILSTER_DIR . 'views/dashboard.php';
	}


	public function widget() {
		$this->is_dashboard = true;
		include MAILSTER_DIR . 'views/dashboard/widget.php';
	}


	public function quick_links() {
		include MAILSTER_DIR . 'views/dashboard/mb-quicklinks.php';
	}


	public function campaigns() {
		include MAILSTER_DIR . 'views/dashboard/mb-campaigns.php';
	}


	public function mailster() {
		include MAILSTER_DIR . 'views/dashboard/mb-mailster.php';
	}


	public function subscribers() {
		include MAILSTER_DIR . 'views/dashboard/mb-subscribers.php';
	}


	public function lists() {
		include MAILSTER_DIR . 'views/dashboard/mb-lists.php';
	}


	public function register_meta_boxes() {

		$this->register_meta_box( 'quick-links', esc_html__( 'Quick Links', 'mailster' ), array( &$this, 'quick_links' ) );
		$this->register_meta_box( 'campaigns', esc_html__( 'My Campaigns', 'mailster' ), array( &$this, 'campaigns' ) );
		if ( current_user_can( 'mailster_manage_licenses' ) ) {
			$this->register_meta_box( 'mailster', esc_html__( 'My Mailster', 'mailster' ), array( &$this, 'mailster' ), 'side', 'high' );
		}
		$this->register_meta_box( 'subscribers', esc_html__( 'My Subscribers', 'mailster' ), array( &$this, 'subscribers' ), 'side' );
		$this->register_meta_box( 'lists', esc_html__( 'My Lists', 'mailster' ), array( &$this, 'lists' ), 'side' );

	}


	/**
	 *
	 *
	 * @param unknown $classes
	 * @return unknown
	 */
	public function post_box_classes_for_mailster( $classes ) {

		if ( $this->verified ) {
			$classes[] = 'verified';
		}
		if ( mailster()->has_update() ) {
			$classes[] = 'has-update';
		} elseif ( mailster( 'translations' )->translation_available() ) {
			$classes[] = 'has-translation-update';
		}

		return $classes;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $title
	 * @param unknown $callback
	 * @param unknown $context       (optional)
	 * @param unknown $priority      (optional)
	 * @param unknown $callback_args (optional)
	 */
	public function register_meta_box( $id, $title, $callback, $context = 'normal', $priority = 'default', $callback_args = null ) {

		$id     = 'mailster-mb-' . sanitize_key( $id );
		$screen = get_current_screen();

		add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $context (optional)
	 */
	public function unregister_meta_box( $id, $context = 'normal' ) {

		$id     = 'mailster-mb-' . sanitize_key( $id );
		$screen = get_current_screen();

		remove_meta_box( $id, $screen, $context );

	}


	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-touch-punch' );

		wp_enqueue_script( 'easy-pie-chart', MAILSTER_URI . 'assets/js/libs/easy-pie-chart' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );
		wp_enqueue_style( 'easy-pie-chart', MAILSTER_URI . 'assets/css/libs/easy-pie-chart' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-chartjs', MAILSTER_URI . 'assets/js/libs/chart' . $suffix . '.js', array( 'easy-pie-chart' ), MAILSTER_VERSION, true );

		wp_enqueue_script( 'mailster-dashboard-script', MAILSTER_URI . 'assets/js/dashboard-script' . $suffix . '.js', array( 'mailster-script', 'postbox' ), MAILSTER_VERSION, true );
		wp_enqueue_style( 'mailster-dashboard-style', MAILSTER_URI . 'assets/css/dashboard-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		mailster_localize_script(
			'dashboard',
			array(
				'subscribers'   => esc_html__( '%s Subscribers', 'mailster' ),
				'reset_license' => esc_html__( 'You can reset your license up to three times!', 'mailster' ) . "\n" . esc_html__( 'Do you really like to reset your license for this site?', 'mailster' ),
				'check_again'   => esc_html__( 'Check Again', 'mailster' ),
				'checking'      => esc_html__( 'Checking...', 'mailster' ),
				'downloading'   => esc_html__( 'Downloading...', 'mailster' ),
				'reload_page'   => esc_html__( 'Complete. Reload page!', 'mailster' ),
			)
		);
	}




	public function add_widgets() {

		if ( ! current_user_can( 'mailster_dashboard_widget' ) ) {
			return;
		}

		add_meta_box( 'dashboard_mailster', esc_html__( 'Newsletter', 'mailster' ), array( &$this, 'widget' ), 'dashboard', 'side', 'high' );

		add_action( 'admin_enqueue_scripts', array( &$this, 'scripts_styles' ), 10, 1 );

	}


	/**
	 *
	 *
	 * @param unknown $elements
	 * @return unknown
	 */
	public function dashboard_glance_items( $elements ) {

		$autoresponder = count( mailster_get_autoresponder_campaigns() );
		$elements[]    = '</ul><br><ul>';

		if ( $campaigns = count( mailster_get_campaigns() ) ) {
			$elements[] = '<a class="mailster-campaigns" href="edit.php?post_type=newsletter">' . number_format_i18n( $campaigns - $autoresponder ) . ' ' . esc_html__( _nx( 'Campaign', 'Campaigns', $campaigns - $autoresponder, 'number of', 'mailster' ) ) . '</a>';
		}

		if ( $autoresponder ) {
			$elements[] = '<a class="mailster-campaigns" href="edit.php?post_status=autoresponder&post_type=newsletter">' . number_format_i18n( $autoresponder ) . ' ' . esc_html__( _nx( 'Autoresponder', 'Autoresponders', $autoresponder, 'number of', 'mailster' ) ) . '</a>';
		}

		if ( $subscribers = mailster( 'subscribers' )->get_totals( 1 ) ) {
			$elements[] = '<a class="mailster-subscribers" href="edit.php?post_type=newsletter&page=mailster_subscribers">' . number_format_i18n( $subscribers ) . ' ' . esc_html__( _nx( 'Subscriber', 'Subscribers', $subscribers, 'number of', 'mailster' ) ) . '</a>';
		}

		return $elements;
	}


}
