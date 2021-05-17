<?php

class MailsterTinymce {

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	public function init() {

		if ( is_admin() ) {
			add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_plugin' ), 10, 3 );
		}

	}


	/**
	 *
	 *
	 * @param unknown $plugin_array
	 * @return unknown
	 */
	public function add_tinymce_plugin( $plugin_array ) {

		global $post;

		if ( isset( $post ) ) {

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			if ( 'newsletter' == $post->post_type ) {

				$plugin_array['mailster_mce_button'] = MAILSTER_URI . 'assets/js/tinymce-editbar-button' . $suffix . '.js';

				add_action( 'before_wp_tiny_mce', array( &$this, 'editbar_translations' ) );
				add_filter( 'mce_buttons', array( &$this, 'register_mce_button' ) );

			} else {
				$plugin_array['mailster_mce_button'] = MAILSTER_URI . 'assets/js/tinymce-button' . $suffix . '.js';

				add_action( 'before_wp_tiny_mce', array( &$this, 'translations' ) );
				add_filter( 'mce_buttons', array( &$this, 'register_mce_button' ) );

			}
		}

		return $plugin_array;

	}


	/**
	 *
	 *
	 * @param unknown $buttons
	 * @return unknown
	 */
	public function register_mce_button( $buttons ) {
		array_push( $buttons, 'mailster_mce_button' );
		return $buttons;
	}


	/**
	 *
	 *
	 * @param unknown $settings
	 */
	public function editbar_translations( $settings = null ) {

		global $mailster_tags;

		if ( ! did_action( 'mailster_add_tag' ) ) {
			do_action( 'mailster_add_tag' );
		}

		$user = array(
			'firstname'    => esc_html__( 'First Name', 'mailster' ),
			'lastname'     => esc_html__( 'Last Name', 'mailster' ),
			'fullname'     => esc_html__( 'Full Name', 'mailster' ),
			'emailaddress' => esc_html__( 'Email address', 'mailster' ),
			'profile'      => esc_html__( 'Profile Link', 'mailster' ),
		);

		$customfields = mailster()->get_custom_fields();

		foreach ( $customfields as $key => $data ) {
			$user[ $key ] = strip_tags( $data['name'] );
		}

		$tags = array();

		$tags['user'] = array(
			'name' => esc_html__( 'User', 'mailster' ),
			'tags' => $user,
		);

		$tags['campaign'] = array(
			'name' => esc_html__( 'Campaign related', 'mailster' ),
			'tags' => array(
				'webversion' => esc_html__( 'Webversion', 'mailster' ),
				'unsub'      => esc_html__( 'Unsubscribe Link', 'mailster' ),
				'forward'    => esc_html__( 'Forward', 'mailster' ),
				'subject'    => esc_html__( 'Subject', 'mailster' ),
				'preheader'  => esc_html__( 'Preheader', 'mailster' ),
			),
		);

		$custom = mailster_option( 'custom_tags', array() );
		if ( ! empty( $mailster_tags ) ) {
			$custom += $mailster_tags;
		}
		if ( ! empty( $custom ) ) {
			$tags['custom'] = array(
				'name' => esc_html__( 'Custom Tags', 'mailster' ),
				'tags' => $this->transform_array( $custom ),
			);

		};

		if ( $permanent = mailster_option( 'tags' ) ) {
			$tags['permanent'] = array(
				'name' => esc_html__( 'Permanent Tags', 'mailster' ),
				'tags' => $this->transform_array( $permanent ),
			);

		};

		$tags['date'] = array(
			'name' => esc_html__( 'Date', 'mailster' ),
			'tags' => array(
				'year'  => esc_html__( 'Current Year', 'mailster' ),
				'month' => esc_html__( 'Current Month', 'mailster' ),
				'day'   => esc_html__( 'Current Day', 'mailster' ),
			),
		);

		echo '<script type="text/javascript">';
		echo 'mailster_mce_button = ' . json_encode(
			array(
				'l10n' => array(
					'tags'   => array(
						'title' => esc_html__( 'Mailster Tags', 'mailster' ),
						'tag'   => esc_html__( 'Tag', 'mailster' ),
						'tags'  => esc_html__( 'Tags', 'mailster' ),
					),
					'remove' => array(
						'title' => esc_html__( 'Remove Block', 'mailster' ),
					),
				),
				'tags' => $tags,
			)
		);
		echo '</script>';

	}


	/**
	 *
	 *
	 * @return unknown
	 * @param unknown $settings
	 */
	public function translations( $settings ) {

		$forms = mailster( 'forms' )->get_list();

		echo '<script type="text/javascript">';
		echo 'mailster_mce_button = ' . json_encode(
			array(
				'l10n'    => array(
					'title'    => 'Mailster',
					'homepage' => array(
						'menulabel'    => esc_html__( 'Newsletter Homepage', 'mailster' ),
						'title'        => esc_html__( 'Insert Newsletter Homepage Shortcodes', 'mailster' ),
						'prelabel'     => esc_html__( 'Text', 'mailster' ),
						'pre'          => esc_html__( 'Signup for the newsletter', 'mailster' ),
						'confirmlabel' => esc_html__( 'Confirm Text', 'mailster' ),
						'confirm'      => esc_html__( 'Thanks for your interest!', 'mailster' ),
						'unsublabel'   => esc_html__( 'Unsubscribe Text', 'mailster' ),
						'unsub'        => esc_html__( 'Do you really want to unsubscribe?', 'mailster' ),
					),
					'button'   => array(
						'menulabel'  => esc_html__( 'Subscriber Button', 'mailster' ),
						'title'      => esc_html__( 'Insert Subscriber Button Shortcode', 'mailster' ),
						'labellabel' => esc_html__( 'Label', 'mailster' ),
						'label'      => esc_html__( 'Subscribe', 'mailster' ),
						'count'      => esc_html__( 'Display subscriber count', 'mailster' ),
						'countabove' => esc_html__( 'Count above Button', 'mailster' ),
						'design'     => esc_html__( 'Design', 'mailster' ),
					),
					'form'     => esc_html__( 'Form', 'mailster' ),
					'forms'    => esc_html__( 'Forms', 'mailster' ),
				),
				'forms'   => $forms,
				'designs' => array(
					'default' => 'Default',
					'twitter' => 'Twitter',
					'wp'      => 'WordPress',
					'flat'    => 'Flat',
					'minimal' => 'Minimal',
				),
			)
		);
		echo '</script>';

	}


	/**
	 *
	 *
	 * @param unknown $array
	 * @return unknown
	 */
	private function transform_array( $array ) {

		$return = array();

		foreach ( $array as $tag => $data ) {
			$return[ $tag ] = ucwords( str_replace( array( '-', '_' ), ' ', strip_tags( $tag ) ) );
		}

		return $return;

	}


}
