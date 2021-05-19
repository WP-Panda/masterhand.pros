<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This class contains status logic that makes product grid actionable
 * @package XLCore
 * @since 1.0.0
 * @author XLPlugins
 */
class XL_addon extends PW_Plugin_states {

	/**
	 *
	 * Important properties here
	 */
	public $config;
	public $title;
	public $EDD_slug;
	public $base_terms;
	public $terms_list;
	public $current_version;
	public $button_config;
	public $status_config;
	public $plugin_state_config;
	public $download_url = '';
	public $buy_now_link = '';
	public $term = array();

	/**
	 * Construct that populate properties.
	 * <br/> Parse the terms came from remote request.
	 * <br/> Verify several configurations so that it wont create issues further.
	 * <br/> Prepare cases (imp) Whole logic to decide plugins state.
	 *
	 * @param type $plugin_remote_config .
	 */
	public function __construct( $plugin_remote_config ) {

		$this->config = $plugin_remote_config;

		$this->populate_properties();

		$this->parse_terms();

		$this->verify_configurations( $this->config );

		$this->prepare_cases();

		parent::__construct( $this );
	}

	/**
	 * Populate properties and sets up variables
	 */
	public function populate_properties() {

		if ( $this->config && is_object( $this->config ) ) {
			$config_as_array = (array) $this->config;
			$current_slug    = key( $config_as_array );

			$this->title             = $this->config->$current_slug->title;
			$this->pluginbasename    = $this->config->$current_slug->plugin_basename;
			$this->is_in_repo        = $this->config->$current_slug->is_in_repo;
			$this->more_details_link = $this->config->$current_slug->more_details_link;
			$this->title_link        = $this->config->$current_slug->title_link;
			$this->base_terms        = $this->config->$current_slug->terms;
			$this->current_version   = $this->config->$current_slug->version;
			$this->buy_now_link      = $this->config->$current_slug->buy_now_link;
			$this->status_link       = $this->config->$current_slug->status_link;
			$this->download_url      = $this->config->$current_slug->download_url;
			$this->Edd_slug          = '' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->title ) ) );
		}
	}

	/**
	 * Parse WP term object and make the array out of it so that it can be utilized in filtering
	 */
	public function parse_terms() {
		if ( $this->config && is_object( $this->config ) ) {
			$config_as_array = (array) $this->config;
			$current_slug    = key( $config_as_array );

			if ( $this->config->$current_slug->terms && count( $this->config->$current_slug->terms ) > 0 ) {
				foreach ( $this->config->$current_slug->terms as $term => $term_obj ) {
					$get_all_slugs = wp_list_pluck( $term_obj, 'slug' );
					if ( ! empty( $get_all_slugs ) ) {
						$get_terms = wp_list_pluck( $term_obj, 'slug' );
						if ( $get_terms && count( $get_terms ) > 0 ) {
							foreach ( $get_terms as $term_single ) {
								array_push( $this->term, $term_single );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Verify remote configs so that it will be shown and worked perfect with the current structure
	 */
	public function verify_configurations() {

		if ( $this->config && is_object( $this->config ) ) {
			$config_as_array = (array) $this->config;
			$current_slug    = key( $config_as_array );

			if ( ! $this->config->$current_slug->icon_full_url ) {

				$this->icon_full_url = plugin_dir_url( dirname( ( __FILE__ ) ) ) . 'img/noimage.jpg';
			}
		}
	}

	/**
	 * Prepare case
	 * As we are handling several scenarios to the dashboard screen so to make grid actions we need to have exact state of a product
	 * <br/> It means we need to parse info from the core wp about different plugin states.
	 * <br/> Taking decisions on the condition where the code lies.
	 * <br/> Different state can be: Update available, uninstalled, installed, inactive, active, license exp.
	 *  <br/>
	 *
	 * @since 1.0.0
	 */
	public function prepare_cases() {

		if ( $this->is_plugin_installed() ) {
			$this->button_config = array();

			$this->status_config = array( __( 'Plugin Is Installed.', 'xlplugins' ) );

		} else {
			if ( ! $this->is_plugin_in_wp() ) {
				$this->button_config = array(
					'state' => 'button-primary',
					'text'  => __( 'Get Add-On', 'xlplugins' ),
					'url'   => $this->buy_now_link,
				);

				$this->status_config = array( sprintf( __( 'Available For Install. <a target="_blank" href="%s"> Download Now</a>', 'xlplugins' ), $this->status_link ) );

			} else {
				$this->button_config = array(
					'state' => 'button-primary',
					'text'  => __( 'Get Add-On', 'xlplugins' ),
					'url'   => $this->buy_now_link,
				);

				$this->status_config = array( sprintf( __( 'License Not Available. <a target="_blank" href="%s"> Purchase Now</a>', 'xlplugins' ), $this->status_link ) );

			}
		}

		/** Preserving Previous logic to create dynamic status and button config */

		//        if ($this->is_plugin_installed()) {
		//
		//
		//            if (!$this->is_update_available()) {
		//
		//                $this->button_config = array();
		//                $this->status_config = array(__('Plugin is installed.', 'xlplugins'));
		//                array_push($this->term, 'purchased');
		//            } else {
		//
		//                $this->button_config = array(
		//                    'state' => 'update-now',
		//                    'text' => __('Update Now', 'xlplugins'),
		//                    'url' => wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . $this->pluginbasename), 'upgrade-plugin_' . $this->pluginbasename)
		//                );
		//
		//                $this->status_config = array(__('Update Available', 'xlplugins'));
		//                array_push($this->term, 'purchased');
		//            }
		//
		//            if (!$this->is_plugin_in_wp()) {
		//
		//                if (!$this->is_license_exists()) {
		//
		//                    $this->button_config = array(
		//                        'state' => 'button-primary',
		//                        'text' => __('Get Add-On', 'xlplugins'),
		//                        'url' => $this->buy_now_link
		//                    );
		//
		//                    $this->status_config = array(sprintf(__('License Not Available. <a target="_blank" href="%s"> Purchase Now</a>', 'xlplugins'),$this->status_link));
		//                } else {
		//
		//
		//                    if ($this->is_license_active()) {
		//
		//
		//
		//                        if (!$this->is_update_available()) {
		//
		//                            $this->button_config = array();
		//                            $this->status_config = array(__('Plugin is installed.', 'xlplugins'));
		//                        }
		//
		//                        else {
		//
		//                            $this->button_config = array(
		//                                'state' => 'update-now',
		//                                'text' => __('Update Now', 'xlplugins'),
		//                                'url' => wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . $this->pluginbasename), 'upgrade-plugin_' . $this->pluginbasename)
		//                            );
		//
		//                            $this->status_config = array(__('Update Available', 'xlplugins'));
		//                            array_push($this->term, 'purchased');
		//                        }
		//                    } else {
		//
		//                        if ($this->is_license_expired()) {
		//
		//                            $this->button_config = array(
		//                                'state' => 'button-primary',
		//                                'text' => __('Get Add-On', 'xlplugins'),
		//                                'url' => $this->buy_now_link
		//                            );
		//
		//                            $this->status_config = array(__('License Key Expired.Renew Now', 'xlplugins'));
		//                            array_push($this->term, 'purchased');
		//                        } else {
		//
		//                            if ($this->is_license_invalid()) {
		//
		//                                $this->button_config = array(
		//                                    'state' => 'button-primary',
		//                                    'text' => __('Get Add-On', 'xlplugins'),
		//                                    'url' => $this->buy_now_link
		//                                );
		//
		//                                $this->status_config = array(__('License Key invalid.Enter or Purchase License Key', 'xlplugins'));
		//                                array_push($this->term, 'purchased');
		//                            }
		//                        }
		//                    }
		//                }
		//            }
		//
		//
		//            if ($this->is_plugin_activated()) {
		//                $this->plugin_state_config = array('deactivate' => array(
		//                    'text' => __('Deactivate', 'xlplugins'),
		//                    'after_link' => sprintf('<i class=\'xl-slug\' data-slug=\'%s\'></i>', $this->pluginbasename),
		//                    'class' => 'xl_plugins_deactivate',
		//                    'wrapperClass' => 'deactivate',
		//                    'url' => wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $this->pluginbasename . '&amp;plugin_status=all&amp;paged=1&amp;', 'deactivate-plugin_' . $this->pluginbasename)
		//                ));
		//            } else {
		//                $this->plugin_state_config = array('activate' => array(
		//                    'text' => __('Activate', 'xlplugins'),
		//                    'after_link' => '',
		//                    'wrapperClass' => '',
		//                    'class' => '',
		//                    'url' => wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $this->pluginbasename . '&amp;plugin_status=all&amp;paged=1&amp;', 'activate-plugin_' . $this->pluginbasename)
		//                ), 'delete' => array(
		//                    'text' => __('Delete', 'xlplugins'),
		//                    'after_link' => '',
		//                    'wrapperClass' => '',
		//                    'class' => 'xl_plugins_deactivate',
		//                    'url' => wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=' . $this->pluginbasename . '&amp;plugin_status=all&amp;paged=1&amp;', 'bulk-plugins')
		//                ));
		//            }
		//
		//
		//
		//
		//        } else {
		//
		//
		//            if (!$this->is_plugin_in_wp()) {
		//
		//
		//                if (!$this->is_license_exists()) {
		//
		//                    $this->button_config = array(
		//                        'state' => 'button-primary',
		//                        'text' => __('Get Add-On', 'xlplugins'),
		//                        'url' => $this->buy_now_link
		//                    );
		//
		//                    $this->status_config = array(sprintf(__('License Not Available. <a target="_blank" href="%s"> Purchase Now</a>', 'xlplugins'),$this->status_link));
		//                } else {
		//
		//
		//                    if ($this->is_license_active()) {
		//                        $this->button_config = array(
		//                            'state' => 'button-primary',
		//                            'text' => __('Get Add-On', 'xlplugins'),
		//                            'url' => $this->buy_now_link,
		//                            //    'url' => wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $this->pluginbasename . "&xl=1"), 'install-plugin_' . $this->pluginbasename),
		//                        );
		//
		//                        $this->status_config = array(sprintf(__('Available For Install. <a target="_blank" href="%s"> Download Now</a>', 'xlplugins'),$this->status_link));
		//                        array_push($this->term, 'purchased');
		//                    } else {
		//                        if ($this->is_license_expired()) {
		//                            $this->button_config = array(
		//                                'state' => 'button-primary',
		//                                'text' => __('Get Add-On', 'xlplugins'),
		//                                'url' => $this->buy_now_link
		//                            );
		//
		//                            $this->status_config = array(__('License Key Expired.Renew Now'), 'xlplugins');
		//                            array_push($this->term, 'purchased');
		//                        } else {
		//                            if ($this->is_license_invalid()) {
		//                                $this->button_config = array(
		//                                    'state' => 'button-primary',
		//                                    'text' => __('Get Add-On', 'xlplugins'),
		//                                    'url' => $this->buy_now_link
		//                                );
		//
		//                                $this->status_config = array(__('License Key invalid.Enter or Purchase License Key', 'xlplugins'));
		//                                array_push($this->term, 'purchased');
		//                            }
		//                        }
		//                    }
		//                }
		//            } else {
		//                $this->button_config = array(
		//                    'state' => 'button-primary',
		//                    'text' => __('Get Add-On', 'xlplugins'),
		//                    'url' => $this->buy_now_link,
		//                    // 'url' => wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $this->pluginbasename . ""), 'install-plugin_' . $this->pluginbasename),
		//                );
		//
		//                $this->status_config = array(sprintf(__('Available For Install. <a target="_blank" href="%s"> Download Now</a>', 'xlplugins'),$this->status_link));
		//
		//                array_push($this->term, 'purchased');
		//            }
		//        }
		//    }
	}
}
