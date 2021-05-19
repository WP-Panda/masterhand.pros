<?php
defined( 'ABSPATH' ) || exit;

class XLWUEV_XL_Support {

	public static $_instance = null;
	public $full_name = XLWUEV_FULL_NAME;
	public $is_license_needed = false;
	public $license_instance;
	public $expected_url;
	protected $slug = 'xlwuev';

	public function __construct() {
		$this->expected_url = admin_url( 'admin.php?page=xlplugins' );

		/**
		 * XL CORE HOOKS
		 */
		add_filter( 'xl_optin_notif_show', array( $this, 'xlwuev_xl_show_optin_pages' ), 10, 1 );

		add_action( 'admin_init', array( $this, 'xlwuev_xl_expected_slug' ), 9.1 );
		add_action( 'maybe_push_optin_notice_state_action', array(
			$this,
			'xlwuev_try_push_notification_for_optin'
		), 10 );

		add_action( 'admin_init', array( $this, 'modify_api_args_if_xlwuev_dashboard' ), 20 );
		add_filter( 'extra_plugin_headers', array( $this, 'extra_woocommerce_headers' ) );

		add_filter( 'add_menu_classes', array( $this, 'modify_menu_classes' ) );
		add_action( 'admin_init', array( $this, 'xlwuev_xl_parse_request_and_process' ), 15 );

		add_filter( 'xl_dashboard_tabs', array( $this, 'xlwuev_modify_tabs' ), 999, 1 );

		add_action( 'admin_menu', array( $this, 'add_menus' ), 80.1 );
		add_action( 'admin_menu', array( $this, 'add_xlwuev_menu' ), 85.2 );
		add_action( 'xlwuev_options_page_right_content', array( $this, 'xlwuev_options_page_right_content' ), 10 );

		add_filter( 'xl_uninstall_reasons', array( $this, 'modify_uninstall_reason' ) );

		add_filter( 'xl_uninstall_reason_threshold_' . XLWUEV_PLUGIN_BASENAME, function () {
			return 6;
		} );

		add_filter( 'xl_global_tracking_data', array( $this, 'xl_add_administration_emails' ) );

		add_filter( 'xl_in_update_message_support', array( $this, 'xlwuev_update_message_support' ), 10 );

	}

	/**
	 * @return null|XLWUEV_XL_Support
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	public function xlwuev_xl_show_optin_pages( $is_show ) {
		return true;
	}

	public function xlwuev_xl_expected_slug() {
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'xlplugins' || $_GET['page'] == 'xlplugins-support' || $_GET['page'] == 'xlplugins-addons' ) ) {
			XL_dashboard::set_expected_slug( $this->slug );
		}
		XL_dashboard::set_expected_url( $this->expected_url );

	}

	public function xlwuev_metabox_always_open( $classes ) {
		if ( ( $key = array_search( 'closed', $classes ) ) !== false ) {
			unset( $classes[ $key ] );
		}

		return $classes;
	}

	public function modify_api_args_if_xlwuev_dashboard() {
		if ( XL_dashboard::get_expected_slug() == $this->slug ) {
			add_filter( 'xl_api_call_agrs', array( $this, 'modify_api_args_for_gravityxl' ) );
			XL_dashboard::register_dashboard( array(
				'parent' => array( 'woocommerce' => 'WooCommerce Add-ons' ),
				'name'   => $this->slug,
			) );
		}
	}

	public function xlplugins_page() {
		if ( ! isset( $_GET['tab'] ) ) {
			XL_dashboard::$selected = 'support';
		}
		XL_dashboard::load_page();
	}

	public function xlplugins_support_page() {
		if ( ! isset( $_GET['tab'] ) ) {
			XL_dashboard::$selected = 'support';
		}
		XL_dashboard::load_page();
	}

	public function xlplugins_plugins_page() {
		XL_dashboard::$selected = 'plugins';
		XL_dashboard::load_page();
	}

	public function modify_api_args_for_gravityxl( $args ) {
		if ( isset( $args['edd_action'] ) && $args['edd_action'] == 'get_xl_plugins' ) {
			$args['attrs']['tax_query'] = array(
				array(
					'taxonomy' => 'xl_edd_tax_parent',
					'field'    => 'slug',
					'terms'    => 'woocommerce',
					'operator' => 'IN',
				),
			);
		}
		$args['purchase'] = XLWUEV_PURCHASE;

		return $args;
	}

	public function xlwuev_try_push_notification_for_optin() {


		if ( ! XL_admin_notifications::has_notification( 'xl_optin_notice' ) ) {
			XL_admin_notifications::add_notification( array(
				'xl_optin_notice' => array(
					'type'    => 'info',
					'content' => sprintf( '
                        <p>We\'re always building new features into WooCommerce User Email Verification, Play a part in shaping the future of Woocommerce User Email Verification and in turn benefit from new conversion-boosting updates.</p>
                        <p>Simply by allowing us to learn about your plugin usage. No sensitive information will be passed on to us. It\'s all safe & secure to say YES.</p>
                        <p><a href=\'%s\' class=\'button button-primary\'>Yes, I want to help</a> <a href=\'%s\' class=\'button button-secondary\'>No, I don\'t want to help</a> <a style="float: right;" target="_blank" href=\'%s\'>Know More</a></p> ', esc_url( wp_nonce_url( add_query_arg( array(
						'xl-optin-choice' => 'yes',
						'ref'             => filter_input( INPUT_GET, 'page' ),
					) ), 'xl_optin_nonce', '_xl_optin_nonce' ) ), esc_url( wp_nonce_url( add_query_arg( 'xl-optin-choice', 'no' ), 'xl_optin_nonce', '_xl_optin_nonce' ) ), esc_url( 'https://xlplugins.com/data-collection-policy/?utm_source=wpplugin&utm_campaign=wooverify&utm_medium=text&utm_term=optin' ) ),
				),
			) );
		}
	}

	/**
	 * Adding XL Header to tell WordPress to read one extra params while reading plugin's header info. <br/>
	 * Hooked over `extra_plugin_headers`
	 *
	 * @param array $headers already registered arrays
	 *
	 * @return type
	 * @since 1.0.0
	 *
	 */
	public function extra_woocommerce_headers( $headers ) {
		array_push( $headers, 'XL' );

		return $headers;
	}

	public function modify_menu_classes( $menu ) {
		return $menu;
	}

	public function xlwuev_xl_parse_request_and_process() {
		$instance_support = XL_Support::get_instance();

		if ( $this->slug == XL_dashboard::get_expected_slug() && isset( $_POST['xl_submit_support'] ) ) {

			if ( filter_input( INPUT_POST, 'choose_addon' ) == '' || filter_input( INPUT_POST, 'comments' ) == '' ) {
				$instance_support->validation = false;
				XL_admin_notifications::add_notification( array(
					'support_request_failure' => array(
						'type'           => 'error',
						'is_dismissable' => true,
						'content'        => __( '<p> Unable to submit your request.All fields are required. Please ensure that all the fields are filled out.</p>', XLWUEV_TEXTDOMAIN ),
					),
				) );
			} else {
				$instance_support->xl_maybe_push_support_request( $_POST );
			}
		}
	}


	public function xlwuev_modify_tabs( $tabs ) {
		if ( $this->slug == XL_dashboard::get_expected_slug() ) {
			return array();
		}

		return $tabs;
	}


	/**
	 * Adding Woocommerce sub-menu for global options
	 */
	public function add_menus() {
		if ( ! XL_dashboard::$is_core_menu ) {
			add_menu_page( __( 'XLPlugins', XLWUEV_TEXTDOMAIN ), __( 'XLPlugins', XLWUEV_TEXTDOMAIN ), 'manage_woocommerce', 'xlplugins', array(
				$this,
				'xlplugins_page'
			), '', '59.5' );
			$licenses = apply_filters( 'xl_plugins_license_needed', array() );
			if ( ! empty( $licenses ) ) {
				add_submenu_page( 'xlplugins', __( 'Licenses', XLWUEV_TEXTDOMAIN ), __( 'License', XLWUEV_TEXTDOMAIN ), 'manage_woocommerce', 'xlplugins' );
			}
			XL_dashboard::$is_core_menu = true;
		}
	}

	public function add_xlwuev_menu() {
		add_submenu_page( 'xlplugins', XLWUEV_SHORTNAME_NAME, XLWUEV_SHORTNAME_NAME, 'manage_woocommerce', XLWUEV_SLUG, array(
			'XLWUEV_Woocommerce_Confirmation_Email_Admin',
			'add_admin_page'
		) );
	}


	public function modify_uninstall_reason( $reasons ) {
		$reasons_our = $reasons;

		$reason_other = array(
			'id'                => 7,
			'text'              => __( 'Other', XLWUEV_TEXTDOMAIN ),
			'input_type'        => 'textfield',
			'input_placeholder' => __( 'Other', XLWUEV_TEXTDOMAIN ),
		);

		$reasons_our[ XLWUEV_PLUGIN_BASENAME ] = array(
			array(
				'id'                => 27,
				'text'              => __( 'Customers are not receiving the Verification Emails', 'woo-confirmation-email' ),
				'input_type'        => '',
				'input_placeholder' => __( 'Customers are not receiving the Verification Emails', 'woo-confirmation-email' ),
			),
			array(
				'id'                => 36,
				'text'              => __( 'Verification Email is delayed for few minutes', 'woo-confirmation-email' ),
				'input_type'        => '',
				'input_placeholder' => __( 'Verification Email is delayed for few minutes', 'woo-confirmation-email' ),
			),
			array(
				'id'                => 37,
				'text'              => __( 'User doesn\'t get verified after clicking on verification link', 'woo-confirmation-email' ),
				'input_type'        => '',
				'input_placeholder' => __( 'User doesn\'t get verified after clicking on verification link', 'woo-confirmation-email' ),
			),
			array(
				'id'                => 38,
				'text'              => __( 'User is not able to login after verification', 'woo-confirmation-email' ),
				'input_type'        => '',
				'input_placeholder' => __( 'User is not able to login after verification', 'woo-confirmation-email' ),
			),
			array(
				'id'                => 3,
				'text'              => __( 'I only needed the plugin for a short period', 'woo-confirmation-email' ),
				'input_type'        => '',
				'input_placeholder' => __( 'I only needed the plugin for a short period', 'woo-confirmation-email' ),
			),
			array(
				'id'                => 39,
				'text'              => __( 'The plugin broke my site, or results in White blank screen', 'woo-confirmation-email' ),
				'input_type'        => '',
				'input_placeholder' => __( 'The plugin broke my site, or results in White blank screen', 'woo-confirmation-email' ),
			),
			array(
				'id'                => 40,
				'text'              => __( 'The plugin suddenly stopped working after Update', 'woo-confirmation-email' ),
				'input_type'        => '',
				'input_placeholder' => __( 'The plugin suddenly stopped working after Update', 'woo-confirmation-email' ),
			),
			array(
				'id'                => 35,
				'text'              => __( 'Doing Testing', 'woo-confirmation-email' ),
				'input_type'        => '',
				'input_placeholder' => __( 'Doing Testing', 'woo-confirmation-email' ),
			),
		);

		array_push( $reasons_our[ XLWUEV_PLUGIN_BASENAME ], $reason_other );

		return $reasons_our;
	}

	public function xl_add_administration_emails( $data ) {

		if ( isset( $data['admins'] ) ) {
			return $data;
		}
		$users = get_users( array(
			'role'   => 'administrator',
			'fields' => array( 'user_email', 'user_nicename' ),
		) );

		$data['admins'] = $users;

		return $data;
	}

	public function xlwuev_update_message_support( $config ) {
		$config[ XLWUEV_PLUGIN_BASENAME ] = 'https://plugins.svn.wordpress.org/woo-confirmation-email/trunk/readme.txt';

		return $config;
	}

	public function xlwuev_options_page_right_content() {
		$support_banner_link = add_query_arg( array(
			'pro'          => 'wc-email-verification',
			'utm_source'   => 'email-verification',
			'utm_medium'   => 'banner',
			'utm_campaign' => 'plugin-resource',
			'utm_term'     => 'support',
		), 'https://xlplugins.com/support/' );
		$documentation_link  = add_query_arg( array(
			'utm_source'   => 'email-verification',
			'utm_medium'   => 'sidebar',
			'utm_campaign' => 'plugin-resource',
			'utm_term'     => 'documentation',
		), 'https://xlplugins.com/documentation/woo-confirmation-email/' );

		$other_products = array();
		if ( ! class_exists( 'WCCT_Core' ) ) {
			$finale_link              = add_query_arg( array(
				'utm_source'   => 'email-verification',
				'utm_medium'   => 'sidebar',
				'utm_campaign' => 'other-products',
				'utm_term'     => 'finale',
			), 'https://xlplugins.com/finale-woocommerce-sales-countdown-timer-discount-plugin/' );
			$other_products['finale'] = array(
				'image' => 'finale.png',
				'link'  => $finale_link,
				'head'  => 'Finale Woocommerce Sales Countdown Timer',
				'desc'  => 'Run Urgency Marketing Campaigns On Your Store And Move Buyers to Make A Purchase',
			);
		}
		if ( ! class_exists( 'WCST_Core' ) ) {
			$sales_trigger_link              = add_query_arg( array(
				'utm_source'   => 'email-verification',
				'utm_medium'   => 'sidebar',
				'utm_campaign' => 'other-products',
				'utm_term'     => 'sales-trigger',
			), 'https://xlplugins.com/woocommerce-sales-triggers/' );
			$other_products['sales_trigger'] = array(
				'image' => 'sales-trigger.png',
				'link'  => $sales_trigger_link,
				'head'  => 'XL Woocommerce Sales Triggers',
				'desc'  => 'Use 7 Built-in Sales Triggers to Optimise Single Product Pages For More Conversions',
			);
		}
		if ( ! class_exists( 'XLWCTY_Core' ) ) {
			$nextmove_link              = add_query_arg( array(
				'utm_source'   => 'email-verification',
				'utm_medium'   => 'sidebar',
				'utm_campaign' => 'other-products',
				'utm_term'     => 'nextmove',
			), 'https://xlplugins.com/woocommerce-thank-you-page-nextmove/' );
			$other_products['nextmove'] = array(
				'image' => 'nextmove.png',
				'link'  => $nextmove_link,
				'head'  => 'NextMove Woocommerce Thank You Pages',
				'desc'  => 'Get More Repeat Orders With 17 Plug n Play Components',
			);
		}
		if ( is_array( $other_products ) && count( $other_products ) > 0 ) {
			?>
            <h3>Checkout Our Other Plugins:</h3>
			<?php
			foreach ( $other_products as $product_short_name => $product_data ) {
				?>
                <div class="postbox wuev_side_content wuev_xlplugins wuev_xlplugins_<?php echo $product_short_name ?>">
                    <a href="<?php echo $product_data['link'] ?>" target="_blank"></a>
                    <img src="<?php echo plugin_dir_url( XLWUEV_PLUGIN_FILE ) . 'admin/assets/img/' . $product_data['image']; ?>"/>
                    <div class="wuev_plugin_head"><?php echo $product_data['head'] ?></div>
                    <div class="wuev_plugin_desc"><?php echo $product_data['desc'] ?></div>
                </div>
				<?php
			}
		}
		?>
        <!--        <div class="postbox wcct_side_content">-->
        <!--            <div class="inside">-->
        <!--                <h3>Resources</h3>-->
        <!--                <p><a href="--><?php //echo $support_banner_link ?><!--" target="_blank"><img src="https://xlplugins.com/assets/wcct/support.jpg?v=--><?php //echo XLWUEV_VERSION ?><!--" width="100%"/></a></p>-->
        <!--                <ul>-->
        <!--                    <li><a href="--><?php //echo $demo_link ?><!--" target="_blank">Demo</a></li>-->
        <!--                    <li><a href="--><?php //echo $support_link ?><!--" target="_blank">Support</a></li>-->
        <!--                    <li><a href="--><?php //echo $documentation_link ?><!--" target="_blank">Documentation</a></li>-->
        <!--                </ul>-->
        <!--            </div>-->
        <!--        </div>-->
		<?php
	}
}

if ( class_exists( 'XLWUEV_XL_Support' ) ) {
	XLWUEV_Core::register( 'xl_support', 'XLWUEV_XL_Support' );
}
