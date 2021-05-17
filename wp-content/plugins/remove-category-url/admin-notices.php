<?php

/**
 * Admin Notices class
 *
 * @package    WordPress
 * @subpackage Admin Notices
 */
class Admin_Notices {

	// Configuration
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Plugin suggestions
	 */
	private $days_dismissing_suggestions = 180; // 6 months reappear
	private $suggestions_message;
	private $suggestions;

	// Properties
	// ---------------------------------------------------------------------------------------------------
	/**
	 * Store missing plugins
	 */
	private $missing;
	/**
	 * Default prefix
	 * Can be changed by the external initialization.
	 */
	private $prefix = 'lbladn';
	/**
	 * Caller plugin file
	 */
	private $plugin_file;

	/**
	 * Single class instance
	 */
	private static $instance;

	// Initialization
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Create or retrieve instance
	 */
	public static function instance( $plugin_file = null ) {

		// Avoid direct calls
		if ( ! function_exists( 'add_action' ) ) {
			die;
		}

		// Check instance
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( $plugin_file );
		}

		// Done
		return self::$instance;
	}


	/**
	 * Constructor
	 */
	private function __construct( $plugin_file = null ) {
		// Main plugin file
		$this->plugin_file = isset( $plugin_file ) ? $plugin_file : __FILE__;

		// Uninstall hook endpoint
		register_uninstall_hook( $this->plugin_file, array( __CLASS__, 'uninstall' ) );

		// Prefix from namespace constant
		$this->prefix = 'wbcr_rmcaturl_';

		$this->suggestions_message = '<b>%plugin% Suggestion:</b><br>Please install the ultimate version of the plugin to get more useful features to improve SEO.';
		$this->suggestions         = array(
			'clearfy' => array(
				'name' => 'Clearfy - base Wordpress optimization in one plugin',
				'desc' => 'This is a free plugin to optimize Wordpress. We recommend that you use it, because it performs complex optimization, improves SEO, improves securety of your site, speeds up your site without replacing the popular optimization plugins, but simply completing them. For example, you can extended YOAST SEO features at 30%, and speed optimization plug-ins by 15-20%! Clearfy will allow you to get rid of a large number of small plug-ins and reduce the load on your site. Try it, it\'s free!',
				'url'  => 'https://clearfy.pro/?utm_source=wordpress.org&utm_campaign=cyr-and-lat',
			)
		);

		// Check notices
		if ( is_admin() ) {
			$this->check_timestamps();
			$this->check_suggestions();
		}
	}

	// Timestamp checks
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Creates the activation timestamp only if it does not exist
	 */
	private function check_timestamps() {
		$timestamp = $this->get_activation_timestamp();
		if ( empty( $timestamp ) ) {
			$this->update_activation_timestamp();
		}
	}

	/**
	 * Check the suggestions dismissed timestamp
	 */
	private function check_suggestions() {

		// Compare timestamp
		$timestamp = $this->get_dismissed_timestamp( 'suggestions' );
		if ( empty( $timestamp ) || ( time() - $timestamp ) > ( $this->days_dismissing_suggestions * 86400 ) ) {

			// Check AJAX submit
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				add_action( 'wp_ajax_' . $this->prefix . '_dismiss_suggestions', array(
					&$this,
					'dismiss_suggestions'
				) );
				// Admin area (except install or activate plugins page)
			} elseif ( ! in_array( basename( $_SERVER['PHP_SELF'] ), array(
				'plugins.php',
				'plugin-install.php',
				'update.php'
			) ) ) {
				add_action( 'wp_loaded', array( &$this, 'load_notices_suggestions' ), PHP_INT_MAX );
			}
		}
	}

	// Loaders
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Check and load the sugestions notices
	 */
	public function load_notices_suggestions() {
		// Check the disable nag constant
		if ( $this->disable_nag_notices() ) {
			return;
		}

		// Collect missing plugins
		$this->missing = $this->get_missing_plugins();
		if ( ! empty( $this->missing ) && is_array( $this->missing ) ) {
			add_action( 'admin_footer', array( &$this, 'admin_footer_suggestions' ) );
			add_action( 'admin_notices', array( &$this, 'admin_notices_suggestions' ) );
		}
	}

	// Admin Notices display
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Suggestions display
	 */
	public function admin_notices_suggestions() {
		$plugin_data = get_plugin_data( $this->plugin_file );

		?>
        <div class="<?php echo esc_attr( $this->prefix ); ?>-dismiss-suggestions notice notice-success is-dismissible"
             data-nonce="<?php echo esc_attr( wp_create_nonce( $this->prefix . '-dismiss-suggestions' ) ); ?>">
            <p><?php echo str_replace( '%plugin%', $plugin_data['Name'], $this->suggestions_message ); ?></p>
            <ul><?php foreach ( $this->missing as $plugin ) : ?>

                    <li><strong><?php echo $this->suggestions[ $plugin ]['name']; ?></strong>
                        <a href="<?php echo esc_url( $this->get_install_url( $plugin ) ); ?>">
                            (Install for free)
                        </a><br/><?php echo $this->suggestions[ $plugin ]['desc']; ?></li>

				<?php endforeach; ?></ul>
        </div>
		<?php
	}

	// AJAX Handlers
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Dismiss suggestions
	 */
	public function dismiss_suggestions() {
		if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], $this->prefix . '-dismiss-suggestions' ) ) {
			$this->update_dismissed_timestamp( 'suggestions' );
		}
	}


	// Plugins information retrieval
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Retrieve uninstalled plugins
	 */
	private function get_missing_plugins() {

		// Initialize
		$inactive = array();

		// Check plugins directory
		$directories = array_merge( self::get_mu_plugins_directories(), self::get_plugins_directories() );
		if ( ! empty( $directories ) ) {
			$required = array_keys( $this->suggestions );
			foreach ( $required as $plugin ) {
				if ( ! in_array( $plugin, $directories ) ) {
					$inactive[] = $plugin;
				}
			}
		}

		// Check inactives
		if ( empty( $inactive ) ) {
			return false;
		}

		// Done
		return $inactive;
	}


	/**
	 * Collects all active plugins
	 */
	private function get_plugins_directories() {

		// Initialize
		$directories = array();

		// Plugins split directory
		$split = '/' . basename( WP_CONTENT_DIR ) . '/' . basename( WP_PLUGIN_DIR ) . '/';

		// Multisite plugins
		if ( is_multisite() ) {
			$ms_plugins = wp_get_active_network_plugins();
			if ( ! empty( $ms_plugins ) && is_array( $ms_plugins ) ) {
				foreach ( $ms_plugins as $file ) {
					$directory = explode( $split, $file );
					$directory = explode( '/', ltrim( $directory[1], '/' ) );
					$directory = $directory[0];
					if ( ! in_array( $directory, $directories ) ) {
						$directories[] = $directory;
					}
				}
			}
		}

		// Active plugins
		$plugins = wp_get_active_and_valid_plugins();
		if ( ! empty( $plugins ) && is_array( $plugins ) ) {
			foreach ( $plugins as $file ) {
				$directory = explode( $split, $file );
				$directory = explode( '/', ltrim( $directory[1], '/' ) );
				$directory = $directory[0];
				if ( ! in_array( $directory, $directories ) ) {
					$directories[] = $directory;
				}
			}
		}

		// Done
		return $directories;
	}


	/**
	 * Retrieve mu-plugins directories
	 */
	private function get_mu_plugins_directories() {

		// Initialize
		$directories = array();

		// Dependencies
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Retrieve mu-plugins
		$plugins = get_plugins( '/../mu-plugins' );
		if ( ! empty( $plugins ) && is_array( $plugins ) ) {
			foreach ( $plugins as $path => $info ) {
				$directory = dirname( $path );
				if ( ! in_array( $directory, array( '.', '..' ) ) ) {
					$directories[] = $directory;
				}
			}
		}

		// Done
		return $directories;
	}


	/**
	 * Plugin install/activate URL
	 */
	private function get_install_url( $plugin ) {
		if ( ! isset( $this->suggestions[ $plugin ]['filename'] ) && isset( $this->suggestions[ $plugin ]['url'] ) ) {
			return $this->suggestions[ $plugin ]['url'];
		}

		// Check existing plugin
		$exists = @file_exists( WP_PLUGIN_DIR . '/' . $plugin );

		// Activate
		if ( $exists ) {

			// Existing plugin
			$path = $plugin . '/' . $this->suggestions[ $plugin ]['filename'];

			return admin_url( 'plugins.php?action=activate&plugin=' . $path . '&_wpnonce=' . wp_create_nonce( 'activate-plugin_' . $path ) );
			// Install
		} else {

			// New plugin
			return admin_url( 'update.php?action=install-plugin&plugin=' . $plugin . '&_wpnonce=' . wp_create_nonce( 'install-plugin_' . $plugin ) );
		}
	}

	/**
	 * Determines the admin notices display
	 */
	private function disable_nag_notices() {
		return ( defined( 'DISABLE_NAG_NOTICES' ) && DISABLE_NAG_NOTICES );
	}

	// Plugin related
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Plugin uninstall hook
	 */
	public static function uninstall() {
		$admin_notices = self::instance();
		$admin_notices->delete_activation_timestamp();
		$admin_notices->delete_dismissed_timestamp( 'suggestions' );
	}



	// Activation timestamp management
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Retrieves the plugin activation timestamp
	 */
	private function get_activation_timestamp() {
		return (int) get_option( $this->prefix . '_activated_on' );
	}

	/**
	 * Updates activation timestamp
	 */
	private function update_activation_timestamp() {
		update_option( $this->prefix . '_activated_on', time(), true );
	}

	/**
	 * Removes activation timestamp
	 */
	private function delete_activation_timestamp() {
		delete_option( $this->prefix . '_activated_on' );
	}

	// Dismissed timestamp management
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Current timestamp by key
	 */
	private function get_dismissed_timestamp( $key ) {
		return (int) get_option( $this->prefix . '_dismissed_' . $key . '_on' );
	}

	/**
	 * Update with the current timestamp
	 */
	private function update_dismissed_timestamp( $key ) {
		update_option( $this->prefix . '_dismissed_' . $key . '_on', time(), true );
	}

	/**
	 * Removes dismissied option
	 */
	private function delete_dismissed_timestamp( $key ) {
		delete_option( $this->prefix . '_dismissed_' . $key . '_on' );
	}

	// Javascript code
	// ---------------------------------------------------------------------------------------------------

	/**
	 * Footer script for Suggestions
	 */
	public function admin_footer_suggestions() {
		?>
        <script type="text/javascript">
			jQuery(function($) {

				$(document).on('click', '.<?php echo $this->prefix; ?>-dismiss-suggestions .notice-dismiss', function() {
					$.post(ajaxurl, {
						'action': '<?php echo $this->prefix; ?>_dismiss_suggestions',
						'nonce': $(this).parent().attr('data-nonce')
					});
				});

			});
        </script>
		<?php
	}
}
