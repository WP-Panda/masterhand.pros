<?php

class AppEngine extends AE_Base {

	public function __construct() {
		/**
		 * add script appengine
		 */
		$this->add_action( 'wp_enqueue_scripts', 'print_scripts', 9 );
		$this->add_action( 'admin_enqueue_scripts', 'print_scripts' );

		$this->add_action( 'wp_footer', 'override_template_setting', 101 );

		if ( isset( $_REQUEST['page'] ) ) {
			$this->add_action( 'admin_print_footer_scripts', 'override_template_setting', 200 );
		}

		/**
		 * Create a nicely formatted and more specific title element text for output
		 * in head of document, based on current view.
		 */
		$this->add_filter( 'wp_title', 'ae_wp_title', 10, 2 );

		// When you are viewing on the Wordpress Administrator Panels will not use this filter

		/**
		 * filter user avatar, replace by user upload avatar image
		 */
		$this->add_filter( 'get_avatar', 'get_avatar', 10, 5 );

		/**
		 * add ajax when user request thumbnail form view carousels
		 */
		$this->add_ajax( 'ae_request_thumb', 'request_thumb' );

		/**
		 * add ajax when user request delete an image from gallery
		 */
		$this->add_ajax( 'ae_remove_carousel', 'remove_carousel' );

		/**
		 * hook to action reject post and send mail to post author
		 *
		 * @since  1.0
		 * @author Dakachi
		 */
		$this->add_action( 'ae_reject_post', 'reject_post' );

		/**
		 * hook to action ae insert post then send mail notify admin
		 *
		 * @since  1.0
		 * @author Dakachi
		 */
		$this->add_action( 'ae_process_payment_action', 'notify_admin', 10, 2 );


		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );

		global $ae_post_factory;
		$ae_post_factory->set( 'post', new AE_Posts( 'post', [
			'category'
		] ) );

		add_action( 'after_setup_theme', [
			'AE_Language',
			'load_text_domain'
		] );

		$this->add_filter( 'nav_menu_css_class', 'special_nav_class', 10, 2 );

		if ( isset( $_GET['close_notices'] ) ) {
			update_option( 'option_sample_data', 1 );
		}

		if ( isset( $_GET['notice_update_db_for_182'] ) ) {
			update_option( 'notice_update_db_for_182', 1 );
		}

		/**
		 * hook to action ae insert wp_head
		 *
		 * @author Tuandq
		 */
		$this->add_action( 'wp_head', 'ae_wp_head', 9 );
	}

	/**
	 * register base script
	 */
	public function print_scripts() {

		$this->add_existed_script( 'jquery' );

		$this->register_script( 'bootstrap', ae_get_url() . '/assets/js/bootstrap.min.js', [
			'jquery'
		], ET_VERSION, true );

		/**
		 * bootstrap slider for search form
		 */
		$this->register_script( 'slider-bt', ae_get_url() . '/assets/js/slider-bt.js', [], true );

		$mapUrl = '//maps.googleapis.com/maps/api/js?signed_in=false';
		if ( ae_get_option( 'gg_map_apikey' ) ) {
			$mapUrl = '//maps.googleapis.com/maps/api/js?signed_in=false&key=' . ae_get_option( 'gg_map_apikey' );
		}
		$this->register_script( 'et-googlemap-api', $mapUrl, '3.0', true );

		$this->register_script( 'ae-colorpicker', ae_get_url() . '/assets/js/colorpicker.js', [
			'jquery'
		] );


		//enqueue google recaptcha scripts
		if ( ae_get_option( 'gg_captcha', false ) && ae_get_option( 'gg_site_key' ) ) {
			$this->add_script( 'recaptcha', '//www.google.com/recaptcha/api.js' );
		}

		// comment from 1.8.4
		// $this->register_script( 'gmap', ae_get_url() . '/assets/js/gmap.js', array(
		// 	'jquery',
		// 	'et-googlemap-api'
		// ) );
		// $this->register_script( 'marker', ae_get_url() . '/assets/js/marker.js', array(
		// 	'gmap'
		// ), true );

		// tam thoi add de xai
		$this->register_script( 'jquery-validator', ae_get_url() . '/assets/js/jquery.validate.min.js', 'jquery' );

		$this->register_script( 'chosen', ae_get_url() . '/assets/js/chosen.js', 'jquery' );

		$this->register_script( 'marionette', ae_get_url() . '/assets/js/marionette.js', [
			'jquery',
			'backbone',
			'underscore',
		], true );

		// ae core js appengine
		$this->register_script( 'appengine', ae_get_url() . '/assets/js/appengine.js', [
			'jquery',
			'underscore',
			'backbone',
			'marionette',
			'plupload',
		], true );

		wp_localize_script( 'chosen', 'raty', [
			'hint' => [
				__( 'bad', ET_DOMAIN ),
				__( 'poor', ET_DOMAIN ),
				__( 'nice', ET_DOMAIN ),
				__( 'good', ET_DOMAIN ),
				__( 'gorgeous', ET_DOMAIN )
			]
		] );
		$adminurl = admin_url( 'admin-ajax.php' );
		if ( function_exists( 'icl_object_id' ) ) {
			$current  = ICL_LANGUAGE_CODE;
			$adminurl = admin_url( 'admin-ajax.php?lang=' . $current );
		}
		$max_upload_size = wp_max_upload_size();

		$number_format = ae_get_option( 'number_format' );
		$decimal       = ( isset( $number_format['et_decimal'] ) ) ? $number_format['et_decimal'] : get_theme_mod( 'et_decimal', 2 );

		$variable                     = [
			'ajaxURL'                => $adminurl,
			'imgURL'                 => ae_get_url() . '/assets/img/',
			'jsURL'                  => ae_get_url() . '/assets/js/',
			'loadingImg'             => '<img class="loading loading-wheel" src="' . ae_get_url() . '/assets/img/loading.gif" alt="' . __( 'Loading...', ET_DOMAIN ) . '">',
			'loading'                => __( 'Loading', ET_DOMAIN ),
			'ae_is_mobile'           => et_load_mobile() ? 1 : 0,
			'plupload_config'        => [
				'max_file_size'       => ( $max_upload_size / ( 1024 * 1024 ) ) . 'mb',
				'url'                 => admin_url( 'admin-ajax.php' ),
				'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
				'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
				'filters'             => [
					[
						'title'      => __( 'Image Files', ET_DOMAIN ),
						'extensions' => 'jpg,jpeg,gif,png'
					]
				]
			],
			'homeURL'                => home_url(),
			'purchase_bid'           => et_get_page_link( 'upgrade-account' ),
			'is_submit_post'         => is_page_template( 'page-post-place.php' ) ? true : false,
			'is_submit_project'      => is_page_template( 'page-submit-project.php' ) ? true : false,
			'is_single'              => ( ! is_singular( 'page' ) && is_singular() ) ? true : false,
			'max_images'             => ae_get_option( 'max_carousel', 5 ),
			'user_confirm'           => ae_get_option( 'user_confirm' ) ? 1 : 0,
			'max_cat'                => ae_get_option( 'max_cat', 3 ),
			'confirm_message'        => __( "Are you sure to archive this?", ET_DOMAIN ),
			'map_zoom'               => ae_get_option( 'map_zoom_default', 8 ),
			'map_center'             => ae_get_option( 'map_center_default', [
				'latitude'  => 10,
				'longitude' => 106
			] ),
			'file_extension_error'   => __( 'File extension error', ET_DOMAIN ),
			'default_single_text'    => __( 'Select an Option', ET_DOMAIN ),
			'default_multiple_text'  => __( 'Select Some Options', ET_DOMAIN ),
			'default_no_result_text' => __( 'No results match', ET_DOMAIN ),
			'fitbounds'              => ae_get_option( 'fitbounds', '' ),
			'limit_free_msg'         => __( "You have reached the maximum number of Free posts. Please select another plan.", ET_DOMAIN ),
			'error'                  => __( "Please fill all require fields.", ET_DOMAIN ),
			'geolocation'            => ae_get_option( 'geolocation', 0 ),
			'et_decimal'             => $decimal,
			'date_format'            => get_option( 'date_format' ),
			'time_format'            => get_option( 'time_format' ),
			'dates'                  => [
				'days'        => [
					__( "Sunday", ET_DOMAIN ),
					__( "Monday", ET_DOMAIN ),
					__( "Tuesday", ET_DOMAIN ),
					__( "Wednesday", ET_DOMAIN ),
					__( "Thursday", ET_DOMAIN ),
					__( "Friday", ET_DOMAIN ),
					__( "Saturday", ET_DOMAIN ),
					__( "Sunday", ET_DOMAIN )
				],
				'daysShort'   => [
					__( "Sun", ET_DOMAIN ),
					__( "Mon", ET_DOMAIN ),
					__( "Tue", ET_DOMAIN ),
					__( "Wed", ET_DOMAIN ),
					__( "Thu", ET_DOMAIN ),
					__( "Fri", ET_DOMAIN ),
					__( "Sat", ET_DOMAIN ),
					__( "Sun", ET_DOMAIN )
				],
				'daysMin'     => [
					__( "Su", ET_DOMAIN ),
					__( "Mo", ET_DOMAIN ),
					__( "Tu", ET_DOMAIN ),
					__( "We", ET_DOMAIN ),
					__( "Th", ET_DOMAIN ),
					__( "Fr", ET_DOMAIN ),
					__( "Sa", ET_DOMAIN ),
					__( "Su", ET_DOMAIN )
				],
				'months'      => [
					__( "January", ET_DOMAIN ),
					__( "February", ET_DOMAIN ),
					__( "March", ET_DOMAIN ),
					__( "April", ET_DOMAIN ),
					__( "May", ET_DOMAIN ),
					__( "June", ET_DOMAIN ),
					__( "July", ET_DOMAIN ),
					__( "August", ET_DOMAIN ),
					__( "September", ET_DOMAIN ),
					__( "October", ET_DOMAIN ),
					__( "November", ET_DOMAIN ),
					__( "December", ET_DOMAIN )
				],
				'monthsShort' => [
					__( "Jan", ET_DOMAIN ),
					__( "Feb", ET_DOMAIN ),
					__( "Mar", ET_DOMAIN ),
					__( "Apr", ET_DOMAIN ),
					__( "May", ET_DOMAIN ),
					__( "Jun", ET_DOMAIN ),
					__( "Jul", ET_DOMAIN ),
					__( "Aug", ET_DOMAIN ),
					__( "Sep", ET_DOMAIN ),
					__( "Oct", ET_DOMAIN ),
					__( "Nov", ET_DOMAIN ),
					__( "Dec", ET_DOMAIN )
				]
			]
		];
		$variable['global_map_style'] = AE_Mapstyle::get_instance()->get_current_style();
		$variable                     = apply_filters( 'ae_globals', $variable ); // this use for front-end
		wp_localize_script( 'appengine', 'ae_globals', $variable );
		// Loads the Internet Explorer specific stylesheet.
		if ( ! is_admin() ) {
			$this->register_style( 'bootstrap', ae_get_url() . '/assets/css/bootstrap.min.css', [], '3.0' );
		}
	}

	/**
	 * add script to footer override underscore templateSettings, localize validator message
	 */
	function override_template_setting() {
		?>
        <!-- localize validator -->
        <script type="text/javascript">
            (function ($) {
                if (typeof $.validator !== 'undefined') {
                    $.extend($.validator.messages, {
                        required: "<?php _e( "This field is required.", ET_DOMAIN ) ?>",
                        email: "<?php _e( "Please enter a valid email address.", ET_DOMAIN ) ?>",
                        url: "<?php _e( "Please enter a valid URL.", ET_DOMAIN ) ?>",
                        number: "<?php _e( "Please enter a valid number.", ET_DOMAIN ) ?>",
                        digits: "<?php _e( "Please enter only digits.", ET_DOMAIN ) ?>",
                        equalTo: "<?php _e( "Please enter the same value again.", ET_DOMAIN ) ?>",
                        date: "<?php _e( "Please enter a valid date.", ET_DOMAIN ); ?>",
                        creditcard: "<?php _e( "Please enter a valid credit card number.", ET_DOMAIN ); ?>",
                        accept: "<?php _e( "Please enter a value with a valid extension.", ET_DOMAIN ); ?>",
                        integer: "<?php _e( "You must enter an integer value.", ET_DOMAIN ); ?>",
                        maxlength: $.validator.format("<?php _e( "Please enter no more than {0} characters.", ET_DOMAIN ); ?>"),
                        minlength: $.validator.format("<?php _e( "Please enter at least {0} characters.", ET_DOMAIN ); ?>"),
                        rangelength: $.validator.format("<?php _e( "Please enter a value between {0} and {1} characters long.", ET_DOMAIN ); ?>"),
                        range: jQuery.validator.format("<?php _e( "Please enter a value between {0} and {1}.", ET_DOMAIN ); ?>"),
                        min: $.validator.format("<?php _e( "Please enter a value greater than or equal to {0}.", ET_DOMAIN ); ?>"),
                        max: $.validator.format("<?php _e( "Please enter a value less than or equal to {0}.", ET_DOMAIN ); ?>")
                    });
                }


            })(jQuery);
        </script>

		<?php
		// print google analytics code
		if ( ! is_admin() ) {
			echo stripslashes( ae_get_option( 'google_analytics' ) );
			// user confirm scripts
			if ( isset( $_GET['act'] ) && $_GET['act'] == "confirm" && isset( $_GET['act'] ) ) {
				$user_id = AE_Users::confirm( $_GET['key'] );
				if ( $user_id ) {
					$mail = AE_Mailing::get_instance();
					$mail->confirmed_mail( $user_id );

					?>
                    <script type="text/javascript" id="user-confirm">
                        (function ($, Views, Models, AE) {
                            $(document).ready(function () {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: "<?php _e( "Your account has been confirmed successfully", ET_DOMAIN )  ?>",
                                    notice_type: 'success',
                                });
                                window.location.href = "<?php echo home_url(); ?>"
                            });
                        })(jQuery, AE.Views, AE.Models, window.AE);
                    </script>
				<?php }
			}
		}
	}

	/**
	 * Create a nicely formatted and more specific title element text for output
	 * in head of document, based on current view.
	 *
	 * @since AE 1.0
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 *
	 * @return string The filtered title.
	 */
	function ae_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() ) {
			return $title;
		}

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title = "$title $sep $site_description";
		}

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 ) {
			$title = "$title $sep " . sprintf( __( 'Page %s', ET_DOMAIN ), max( $paged, $page ) );
		}

		return $title;
	}

	/**
	 * filter wp avatar use AE_Users return a image tag with user setting avatar url
	 *
	 * @param $avatar
	 * @param $id_or_email
	 * @param $size
	 *
	 * @author  Dakachi
	 * @version 1.0
	 */
	function get_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
		global $pagenow;
		// return avatar in discussion option page
		if ( $pagenow == "options-discussion.php" ) {
			return $avatar;
		}

		$user = false;
		if ( is_numeric( $id_or_email ) ) {
			$id   = (int) $id_or_email;
			$user = get_userdata( $id );
		} elseif ( is_object( $id_or_email ) ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$id   = (int) $id_or_email->user_id;
				$user = get_userdata( $id );
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );
			if ( ! $user ) {
				$user = false;
			}
			// $user = false;
		}

		if ( ! $user ) {
			return $avatar;
		}

		$seller          = AE_Users::get_instance();
		$profile_picture = $seller->get_avatar( $user->ID, $size, $default );
		/**
		 * overide $default by profile picture
		 */
		if ( $profile_picture != '' ) {
			$default = $profile_picture;
			if ( false === $alt ) {
				$safe_alt = '';
			} else {
				$safe_alt = esc_attr( $alt );
			}

			$avatar = "<img alt='{$safe_alt}' src='{$default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
		}

		return $avatar;
	}

	/**
	 * request carousel thumbnail image for edit ad form
	 * send json back carousel js view
	 *
	 * @author  Dakachi
	 * @version 1.0
	 */
	function request_thumb() {
		$items  = isset( $_REQUEST['item'] ) ? $_REQUEST['item'] : [];
		$return = [];
		if ( ! empty( $items ) ) {
			foreach ( $items as $key => $value ) {
				$return[] = et_get_attachment_data( $value, [
					'thumbnail'
				] );
			}
			wp_send_json( [
				'success' => true,
				'data'    => $return
			] );
		} else {
			wp_send_json( [
				'success' => false
			] );
		}
	}

	/**
	 * request remove image for edit ad form
	 * send json back carousel js view
	 *
	 * @author  Dakachi
	 * @version 1.0
	 */
	function remove_carousel() {
		if ( ! current_user_can( 'manage_options' ) ) {
			global $user_ID;
			$post = get_post( $_REQUEST['id'] );
			if ( $user_ID != $post->post_author ) {
				wp_send_json( [
					'success' => false,
					'msg'     => __( "Not owned this image!", ET_DOMAIN )
				] );
			}
		}
		wp_delete_post( $_REQUEST['id'], true );
		$meta = get_post_meta( $_REQUEST['id'] );
		if ( ! empty( $meta ) ) {
			delete_post_meta( $_REQUEST['id'] );
		}
		wp_send_json( [
			'success' => true
		] );
	}

	/**
	 * reject post
	 *
	 * @param $data
	 */
	function reject_post( $data ) {
		$this->mail = AE_Mailing::get_instance();
		$this->mail->reject_post( $data );
	}

	/**
	 * send notify to admin
	 *
	 * @param Object $post Post data
	 *
	 * @since  1.1
	 * @author Dakachi
	 */
	function notify_admin( $payment_return, $data ) {
		do_action( 'ae_notify_admin', $data );
		if ( ! isset( $data['ad_id'] ) || empty( $data['ad_id'] ) ) {
			return false;
		}
		if ( ! $payment_return['ACK'] ) {
			return;
		}
		$this->mail = AE_Mailing::get_instance();
		$this->mail->new_post_alert( $data['ad_id'] );
	}

	function special_nav_class( $classes, $item ) {
		if ( in_array( 'current-menu-item', $classes ) ) {
			$classes[] = 'active ';
		}

		return $classes;
	}

	/**
	 * hook to action ae insert wp_head
	 *
	 * @author Tuandq
	 */
	function ae_wp_head() {
		/**
		 * html5
		 */
		echo '<!--[if lt IE 9]>
                <script src="' . ae_get_url() . '/assets/js/html5.js"></script>
            <![endif]-->';
	}
}

global $et_appengine;
$et_appengine = new AppEngine();