<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * This class is a mail loader class for dashboard page , controls and sets up all the necessary actions
 *
 * @author XLPlugins
 * @package XLCore
 */
class XL_dashboard {

	public static $currentPage;
	public static $parent;
	public static $selected = '';
	public static $pagefullurl = '';
	public static $is_dashboard_page = false;
	public static $loader_url = '';
	protected static $expectedurl;
	protected static $expectedslug;
	public static $is_core_menu = false;

	/**
	 * Function Loads the html and required javascript to render on dashboard page
	 */
	public static function load_page() {

		//do_action
		do_action( 'xl_before_dashboard_page' );

		$model = apply_filters( 'xl_tabs_modal_' . self::$selected, array() );
		?>
        <div class="wrap">
            <div class="icon32" id="icon-themes"><br></div>
            <div class="xl_dashboard_tab_content" id="<?php echo self::$selected; ?>">
				<?php include_once self::$loader_url . 'views/xl-tabs-' . self::$selected . '.phtml'; ?>
            </div>
        </div>
		<?php
	}

	/**
	 * Init function hooked on `admin_init`
	 * Set the required variables and register some important hooks
	 */
	public static function init() {
		global $xl_ultimate_latest_core;

		self::$loader_url = $xl_ultimate_latest_core['plugin_path'] . '/xl/';
		$selected         = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'plugins' );

		self::$selected = $selected;

		/**
		 * Function to trigger error message at WordPress plugins page when we have update but license invalid
		 */
		self::add_notice_unlicensed_product();
	}

	/**
	 * Getting and parsing all our licensing products and checking if there update is available
	 */
	public static function add_notice_unlicensed_product() {

		/**
		 * Getting necessary data
		 */
		$licenses = XL_licenses::get_instance()->get_data();

		/**
		 * Looping over to check how many licenses are invalid and pushing notification and error accordingly
		 */
		if ( $licenses && count( $licenses ) > 0 ) {
			foreach ( $licenses as $key => $license ) {
				if ( $license['product_status'] == 'invalid' ) {
					add_action( 'in_plugin_update_message-' . $key, array( __CLASS__, 'need_license_message' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Message displayed if license not activated. <br/>
	 *
	 * @param  array $plugin_data
	 * @param  object $r
	 *
	 * @return void
	 */
	public static function need_license_message( $plugin_data, $r ) {
		if ( empty( $r->package ) ) {
			echo wp_kses_post( '<div class="xl-updater-plugin-upgrade-notice">' . __( 'To enable this update please activate your XL license by visiting the Dashboard Page.', 'xlplugins' ) . '</div>' );
		}
	}

	/**
	 * Register dashboard function just initializes the execution by firing some hooks that helps getting and rendering data
	 *
	 * @param type $attrs
	 */
	public static function register_dashboard( $attrs ) {

		self::$is_dashboard_page = true;
		self::$currentPage       = ( isset( $attrs['name'] ) ) ? $attrs['name'] : null;
		self::$parent            = ( isset( $attrs['parent'] ) ) ? $attrs['parent'] : null;

		//registering necessary hooks
		//making sure these hooks loads only when register for dashboard happens (specific page)
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'xl_dashboard_scripts' ) );
		add_action( 'xl_tabs_modal_licenses', array( __CLASS__, 'xl_licenses_data' ), 99 );
		add_action( 'xl_tabs_modal_support', array( __CLASS__, 'xl_support_data' ), 99 );
		add_action( 'xl_tabs_modal_tools', array( __CLASS__, 'xl_tools_data' ), 99 );

	}

	/**
	 * Setting expected url , some time i need to access url for dashboard even when register function doesn't hit
	 * <br/> To get over with this issue we set expected url before register executes
	 *
	 * @param type $url
	 */
	public static function set_expected_url( $urls ) {
		self::$expectedurl = $urls;
	}

	public static function get_expected_slug() {
		return self::$expectedslug;
	}

	public static function set_expected_slug( $slug ) {
		self::$expectedslug = $slug;
	}


	/**
	 * Model function to fire over licensing page. <br/>
	 * Hooked over 'xl_tabs_modal_licenses'. <br/>
	 * @return mixed false on failure and data on success
	 */
	public static function xl_licenses_data() {

		if ( false === XL_API::get_xl_status() ) {
			return;
		}
		$get_list = array();

		$License = XL_licenses::get_instance();

		return (object) array_merge( (array) $get_list, (array) array(
			'additional_tabs' => apply_filters( 'xl_additional_tabs', array(
					array(
						'slug'  => 'tools',
						'label' => __( 'Tools', 'xlplugins' ),
					),
				) ),
			'licenses'        => $License->get_data(),
			'current_tab'     => self::$selected,
		) );
	}


	/**
	 * Model function to fire over licensing page. <br/>
	 * Hooked over 'xl_tabs_modal_licenses'. <br/>
	 * @return mixed false on failure and data on success
	 */
	public static function xl_tools_data() {

		if ( false === XL_API::get_xl_status() ) {
			return;
		}
		$get_list = array();

		return (object) array_merge( (array) $get_list, (array) array(
			'additional_tabs' => apply_filters( 'xl_additional_tabs', array(
					array(
						'slug'  => 'tools',
						'label' => __( 'Tools', 'xlplugins' ),
					),
				) ),

			'current_tab' => self::$selected,
		) );
	}


	/**
	 * Model function to fire over support page. <br/>
	 * Hooked over 'xl_tabs_modal_support'. <br/>
	 * @return mixed false on failure and data on success
	 */
	public static function xl_support_data( $data ) {

		//getting plugins list and tabs data
		$get_list = array();

		/** creating instance of XL_licenses and getting all installed plugins */
		$License       = XL_licenses::get_instance();
		$get_installed = XL_addons::get_installed_plugins();

		//Creating Instance of support class for further operations (like system info)
		$object_support = XL_Support::get_instance();

		return (object) array_merge( (array) $get_list, (array) array(
			'additional_tabs' => apply_filters( 'xl_additional_tabs', array(
					array(
						'slug'  => 'tools',
						'label' => __( 'Tools', 'xlplugins' ),
					),
				) ),
			'installed'       => $get_installed,
			'system_info'     => $object_support->prepare_system_information_report(),
			'licenses'        => $License->get_data(),
			'email'           => get_bloginfo( 'admin_email' ),
			'current_tab'     => self::$selected,
		) );
	}


	/**
	 * Get current admin url for dashboard page. <br/>
	 * To be used in view files. <br/>
	 * @return type
	 */
	public static function get_current_url() {
		return admin_url( 'admin.php?page=' . self::$currentPage );
	}

	/**
	 * Get current admin url for dashboard page. <br/>
	 * To be used in view files. <br/>
	 * @return type
	 */
	public static function get_current_license_management() {
		return admin_url( 'admin.php?page=' . self::$currentPage . '&tab=license' );
	}

	/**
	 * Hooked over 'admin_enqueue_scripts' under the register function, cannot run on every admin page
	 * Enqueues `updates` handle script,  core script that is responsible for plugin updates
	 */
	public static function xl_dashboard_scripts() {
		?>
        <style type="text/css">

            /* product grid */
            .xl_plugins_wrap .filter-links.filter-primary {
                border-right: 2px solid #e5e5e5;
            }

            .xl_plugins_wrap .wp-filter {
                margin-bottom: 0;
            }

            .xl_plugins_wrap .filter-links li {
                border-bottom: 4px solid white;
            }

            .xl_plugins_wrap .filter-links li a.current {
                border-bottom: 4px solid #fff;
            }

            .xl_plugins_wrap .filter-links li.current {
                border-bottom-color: #666666;
            }

            .xl_plugins_wrap .xl_dashboard_tab_content {
                float: left;
                width: 54%;
            }

            .xl_plugins_wrap .xl_dashboard_license_content {
                float: left;
                width: 74%;
            }

            .xl_plugins_wrap .xl_dashboard_tab_content .xl_core_tools {
                width: 100% !important;
                background: #fff;
            }

            .xl_plugins_wrap .xl_dashboard_tab_content .xl_core_tools h2 {
                margin-top: 0;
            }

            .xl_plugins_wrap .xl_plugins_status {
                font-style: italic;
            }

            .xl_plugins_wrap .xl_plugins_features_div {
            }

            .xl_plugins_wrap div#col-container.about-wrap {
                max-width: 100%;
                margin: 30px 20px 0 0;
                width: auto;
                margin-right: 0;
                clear: both;

            }

            .xl_plugins_wrap div#col-container.about-wrap .col-wrap {
                float: left;
            }

            .xl_dashboard_tab_content .xl_plugins_wrap .xl-area-right {
                float: right;
                width: 44%;
                margin: 0;
            }

            .xl_dashboard_tab_content#licenses .xl_plugins_wrap .xl-area-right {
                width: 24%;
            }

            .xl_plugins_wrap .xl-area-right table {
                padding: 15px;
            }

            .xl_plugins_wrap .xl-area-right table th, .xl_plugins_wrap .xl-area-right table td {
                vertical-align: middle;
                padding: 15px 0;
            }

            .xl_plugins_wrap .xl-area-right table td {
                text-align: right;
            }

            .xl_plugins_wrap .xl_plugins_features {
                margin-left: -10px;
                margin-right: -10px;
            }

            .xl_plugins_wrap .xl_plugins_features .xl_plugins_half_col {
                width: 100%;
                margin: 4px 0;
                padding-left: 30px;
                padding-right: 10px;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }

            .xl_plugins_wrap .xl_plugins_features .xl_plugins_half_col:before {
                margin-left: -20px;
                content: "\f147";
                font: 400 20px/.5 dashicons;
                speak: none;
                display: inline-block;
                padding: 0;
                top: 4px;
                left: -2px;
                position: relative;
                vertical-align: top;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-decoration: none !important;
                color: #444;
            }

            @media screen and (min-width: 481px) {
                .xl_plugins_wrap .xl_plugins_features .xl_plugins_half_col {
                    width: 50%;
                    float: left;
                }

                .xl_plugins_wrap .xl_plugins_features .xl_plugins_half_col:nth-child(2n) {
                    text-align: right;
                }

                .xl_plugins_wrap .xl_plugins_features .xl_plugins_half_col:nth-child(2n+1) {
                    clear: both;
                }
            }

            .xl_plugins_wrap .xl_plugins_status_div {
                padding-top: 8px;
                padding-bottom: 8px;
                border-color: rgba(221, 221, 221, 0.4);
                background: #fff;
            }

            .xl_plugins_wrap .xl_plugins_status_div .xl_plugins_status {
                margin: 0;
            }

            .xl_plugins_wrap .button-primary.xl_plugins_renew_btn {
                min-width: 120px;
                text-align: center;
            }

            .xl_plugins_wrap .button-primary.xl_plugins_renew_btn:before {
                content: "\f321";
                font: 400 20px/.5 dashicons;
                speak: none;
                display: inline-block;
                padding: 0;
                top: 4px;
                left: -2px;
                position: relative;
                vertical-align: top;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-decoration: none !important;
                color: #fff;
            }

            .xl_plugins_wrap .button-primary.xl_plugins_buy_btn {
                min-width: 120px;
                text-align: center;
            }

            .xl_plugins_wrap .button-primary.xl_plugins_buy_btn:before {
                content: "\f174";
                font: 400 20px/.5 dashicons;
                speak: none;
                display: inline-block;
                padding: 0;
                top: 4px;
                left: -2px;
                position: relative;
                vertical-align: top;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-decoration: none !important;
                color: #fff;
            }

            .xl_plugins_wrap .plugin-card-bottom.xl_plugins_features_links_div {
                background: #fff;
            }

            .xl_plugins_wrap .plugin-card-bottom.xl_plugins_features_links_div .xl_plugins_features_links ul {
                margin: 0;
            }

            .xl_plugins_wrap .xl_plugins_deactivate_add.xl_plugins_features_links {
                padding-right: 125px;
                display: block;
                position: relative;
            }

            .xl_plugins_wrap .xl_plugins_deactivate_add.xl_plugins_features_links .xl_plugins_deactivate {
                color: #a00000;
                display: inline-block;
                line-height: 26px;
            }

            .clearfix:after, .clearfix:before {
                display: table;
                content: '';
            }

            .clearfix:after {
                clear: both;
            }

            .xl_plugins_wrap ul.xl_plugins_options {
                display: inline-block;
                line-height: 26px;
                float: right;
                position: absolute;
                z-index: 1;
                right: 0;
            }

            .xl_plugins_wrap ul.xl_plugins_options li {
                display: inline-block;
                margin: 0;
            }

            .xl_plugins_wrap .js_filters li a:focus {
                box-shadow: none;
                -webkit-box-shadow: none;
                color: #23282d;
            }

            #licenses .column-product_status, .index_page_woothemes-helper-network .column-product_status {
                width: 350px;
            }

            #licenses .below_input_message {
                color: #9E0B0F;
                padding-left: 1px;
            }

            #licenses .below_input_message a {
                text-decoration: underline;
            }

            .xl-updater-plugin-upgrade-notice {
                font-weight: 400;
                color: #fff;
                background: #d54d21;
                padding: 1em;
                margin: 9px 0;
            }

            .xl-updater-plugin-upgrade-notice:before {
                content: "\f348";
                display: inline-block;
                font: 400 18px/1 dashicons;
                speak: none;
                margin: 0 8px 0 -2px;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                vertical-align: top;
            }

            #support-request label:not(.radio) {
                display: block;
                font-weight: 600;
                font-size: 14px;
                line-height: 1.3;
            }

            #pdf-system-status {
                overflow: hidden;
            }

            #pdf-system-status p {
                clear: left;
            }

            #pdf-system-status span.details, #support-request span.details {
                font-size: 95%;
                color: #444;
                margin-top: 7px;
                display: inline-block;
                clear: left;
            }

            #pdf-system-status span.details.path, #support-request span.details.path {
                padding: 2px;
                background: #f2f2f2;
            }

            #support-request input:not([type="radio"]), #support-request select {
                width: 20em;
                max-width: 350px;
                width: 100%;
            }

            #support-request input[type="submit"] {
                width: auto;
            }

            #support-request textarea {
                width: 65%;
                height: 150px;
            }

            #support-request input, #support-request textarea {
                padding: 5px 4px;
            }

            #support-request #support-request-button {
                padding: 0 8px;
            }

            #support-request .gfspinner {
                vertical-align: middle;
                margin-left: 5px;
            }

            #support-request textarea {
                max-width: 350px;
                width: 100%;
                /*                border: 1px solid #999;
								color: #444;*/
            }

            #support-request :disabled, #support-request textarea:disabled {
                color: #CCC;
                border: 1px solid #CCC;
            }

            #support-request input.error, #support-request textarea.error, #support-request select.error {
                color: #d10b0b;
                border: 1px solid #d10b0b;
            }

            #support-request .form-table .radioBtns span {

                margin-right: 10px;
                display: inline-block;
            }

            #support-request .fa-times-circle {
                vertical-align: middle;
            }

            .icon-spinner {
                font-size: 18px;
                margin-left: 5px;
            }

            #support-request span.msg {
                margin-left: 5px;
                color: #008000;
            }

            #support-request span.error {
                margin-left: 5px;
                color: #d10b0b;
            }

            #lv_pointer_target {
                float: right;
                background: #0e3f7a;
                color: #fff;
                border: none;
                position: relative;
                top: -6px;
            }

            #lv_pointer_target:focus {
                border: none;
                -webkit-box-shadow: none;
                box-shadow: none;

            }

            .xl_plugins_wrap .filter-links li > a:focus {
                -webkit-box-shadow: none;
                box-shadow: none;
                -moz-box-shadow: none;
            }

            @media (max-width: 1023px) {

                #support-request .form-table .radioBtns span {
                    display: block;
                    margin-bottom: 4px;
                }
            }

            @media screen and (max-width: 782px) {
                .xl_plugins_wrap .xl_dashboard_tab_content {
                    float: none;
                }

                .xl_plugins_wrap div#col-container.about-wrap {
                    margin-right: 0;
                }

                .xl_plugins_wrap div#col-container.about-wrap .xl-area-right {
                    float: none;
                    margin-right: 0;
                    width: 280px;
                    margin-left: 0;
                    margin: auto;
                }

                .xl_plugins_wrap div#col-container.about-wrap .col-wrap {
                    float: none;
                }

                #support-request .form-table input[type="radio"] {
                    height: 16px;
                    width: 16px;
                }
            }

            .xl_core_tools .xl_download_files_label {
                display: inline-block;
                padding-left: 10px;
                vertical-align: -webkit-baseline-middle;
            }

            .xl_core_tools .xl_download_buttons {
                display: inline-block;
                padding-left: 5px;

            }
        </style>
		<?php
	}

}

//Initialize the instance so that some necessary hooks can run on each load
add_action( 'admin_init', array( 'XL_dashboard', 'init' ), 11 );
