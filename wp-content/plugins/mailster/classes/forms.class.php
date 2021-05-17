<?php

class MailsterForms {

	private $request = null;

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}


	public function init() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 20 );
		add_action( 'wp', array( &$this, 'form' ) );

		if ( is_admin() ) {

			add_action( 'mailster_use_it_form_tab_intro', array( &$this, 'use_it_form_tab_intro' ) );
			add_action( 'mailster_use_it_form_tab_code', array( &$this, 'use_it_form_tab_code' ) );
			add_action( 'mailster_use_it_form_tab_subscriber-button', array( &$this, 'use_it_form_tab_subscriber_button' ) );
			add_action( 'mailster_use_it_form_tab_form-html', array( &$this, 'use_it_form_tab_form_html' ) );

			add_action( 'wp_loaded', array( &$this, 'edit_hook' ) );

		} else {

			add_action( 'mailster_form_header', array( &$this, 'set_form_request' ) );
			add_action( 'mailster_form_head', array( &$this, 'form_head' ) );
			add_action( 'mailster_form_body', array( &$this, 'form_body' ) );
			add_action( 'mailster_form_footer', array( &$this, 'form_footer' ) );

		}

	}


	public function set_form_request() {

		global $pagenow;

		$formpage = $pagenow == 'form.php' || get_query_var( '_mailster_form' );

		$this->request = array(
			'is_editable' => isset( $_GET['edit'] ) && wp_verify_nonce( $_GET['edit'], 'mailsteriframeform' ),
			'is_embeded'  => $formpage && ! isset( $_GET['iframe'] ),
			'is_button'   => isset( $_GET['button'] ),
			'is_iframe'   => $formpage && ( isset( $_GET['iframe'] ) && $_GET['iframe'] == 1 && ! isset( $_GET['button'] ) ),
			'use_style'   => ( ( isset( $_GET['style'] ) && $_GET['style'] == 1 ) || ( isset( $_GET['s'] ) && $_GET['s'] == 1 ) ),
			'form_id'     => ( isset( $_GET['id'] ) ? (int) $_GET['id'] : 1 ),
			'showcount'   => ( isset( $_GET['showcount'] ) ? (int) $_GET['showcount'] : 0 ),
			'width'       => ( isset( $_GET['width'] ) ? $_GET['width'] : 480 ),
			'buttonstyle' => ( isset( $_GET['design'] ) ? $_GET['design'] : 'default' ),
			'button_id'   => ( isset( $_GET['button'] ) ? (int) $_GET['button'] : '' ),
			'origin'      => ( isset( $_GET['origin'] ) ? $_GET['origin'] : '' ),
			'buttonlabel' => ( isset( $_GET['label'] ) ? esc_attr( strip_tags( urldecode( $_GET['label'] ) ) ) : 'Subscribe' ),

		);
	}


	public function form() {

		if ( get_query_var( '_mailster_form' ) ) {
			include_once MAILSTER_DIR . 'form.php';
			exit;
		}

	}


	public function form_head() {

		extract( $this->request );

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mailster-form-default-style', MAILSTER_URI . 'assets/css/form-default-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		if ( $use_style ) {
			wp_register_style( 'mailster-theme-style', get_template_directory_uri() . '/style.css', array(), MAILSTER_VERSION );
			wp_print_styles( 'mailster-theme-style' );
		}
		if ( $is_button ) {

			$buttonstyle = explode( ' ', $buttonstyle );
			wp_register_style( 'mailster-form-button-base-style', MAILSTER_URI . 'assets/css/button-style' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_register_style( 'mailster-form-button-style', MAILSTER_URI . 'assets/css/button-' . $buttonstyle[0] . '-style' . $suffix . '.css', array( 'mailster-form-button-base-style' ), MAILSTER_VERSION );

			do_action( 'mailster_form_head_button' );

			mailster( 'helper' )->wp_print_embedded_styles( 'mailster-form-button-style' );

		} elseif ( $is_editable ) {

			wp_print_styles( 'mailster-form-default-style' );

		} elseif ( $is_embeded ) {

			do_action( 'mailster_form_head_embeded' );
			wp_print_styles( 'mailster-form-default-style' );

		} elseif ( $is_iframe ) {

			wp_register_style( 'mailster-form-iframe-style', MAILSTER_URI . 'assets/css/form-iframe-style' . $suffix . '.css', array( 'mailster-form-default-style' ), MAILSTER_VERSION );
			do_action( 'mailster_form_head_iframe' );
			mailster( 'helper' )->wp_print_embedded_styles( 'mailster-form-iframe-style' );
			$width = preg_match( '#\d+%#', $width ) ? (int) $width . '%' : (int) $width . 'px';
			echo '<style type="text/css">.mailster-form-wrap{width:' . $width . '}</style>';

		}

	}


	public function form_body() {

		extract( $this->request );

		if ( $is_button ) {

			do_action( 'mailster_form_body_button' );
			include MAILSTER_DIR . 'views/forms/button.php';

		} elseif ( $is_iframe ) {

			do_action( 'mailster_form_body_iframe' );
			$form = mailster( 'form' )->id( $form_id );
			$form->add_class( 'in-iframe' );
			$form->render();

		} elseif ( $is_editable ) {

			$form = mailster( 'form' )->id( $form_id );
			$form->add_class( 'embeded' );
			$form->prefill( false );
			$form->set_success( esc_html__( 'This is a success message', 'mailster' ) );
			$form->set_error( esc_html__( 'This is an error message', 'mailster' ) );
			$form->is_preview();
			$form->render();

		} else {

			$form = mailster( 'form' )->id( $form_id );
			$form->add_class( 'embeded' );
			$form->render();

		}

	}


	public function form_footer() {

		extract( $this->request );

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'mailster-form', MAILSTER_URI . 'assets/js/form' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );

		if ( $is_button ) {

			do_action( 'mailster_form_footer_button' );
			wp_register_script( 'mailster-form-button-script', MAILSTER_URI . 'assets/js/form-button-script' . $suffix . '.js', array(), MAILSTER_VERSION );
			mailster( 'helper' )->wp_print_embedded_scripts( 'mailster-form-button-script' );
		} elseif ( $is_editable ) {

			wp_register_script( 'mailster-editable-form', MAILSTER_URI . 'assets/js/editable-form-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );
			wp_print_scripts( 'mailster-editable-form' );

		} elseif ( $is_embeded ) {

			do_action( 'mailster_form_footer_embeded' );
			wp_print_scripts( 'mailster-form' );

		} elseif ( $is_iframe ) {

			do_action( 'mailster_form_footer_iframe' );
			wp_register_script( 'mailster-form-iframe-script', MAILSTER_URI . 'assets/js/form-iframe-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );
			wp_print_scripts( 'mailster-form-iframe-script' );
			wp_print_scripts( 'mailster-form' );

		}

	}


	public function admin_menu() {

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Forms', 'mailster' ), esc_html__( 'Forms', 'mailster' ), 'mailster_edit_forms', 'mailster_forms', array( &$this, 'view_forms' ) );

		add_action( 'load-' . $page, array( &$this, 'script_styles' ) );

		if ( isset( $_GET['ID'] ) || isset( $_GET['new'] ) ) :

			add_action( 'load-' . $page, array( &$this, 'edit_entry' ), 99 );

		else :

			add_action( 'load-' . $page, array( &$this, 'bulk_actions' ), 99 );
			add_action( 'load-' . $page, array( &$this, 'screen_options' ) );
			add_filter( 'manage_' . $page . '_columns', array( &$this, 'get_columns' ) );

		endif;

	}


	public function script_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if ( isset( $_GET['ID'] ) || isset( $_GET['new'] ) ) :

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-touch-punch' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );

			wp_enqueue_style( 'form-button-style', MAILSTER_URI . 'assets/css/button-style' . $suffix . '.css', array(), MAILSTER_VERSION );

			wp_enqueue_style( 'form-button-default-style', MAILSTER_URI . 'assets/css/button-default-style' . $suffix . '.css', array( 'form-button-style' ), MAILSTER_VERSION );
			wp_enqueue_style( 'form-button-wp-style', MAILSTER_URI . 'assets/css/button-wp-style' . $suffix . '.css', array( 'form-button-style' ), MAILSTER_VERSION );
			wp_enqueue_style( 'form-button-twitter-style', MAILSTER_URI . 'assets/css/button-twitter-style' . $suffix . '.css', array( 'form-button-style' ), MAILSTER_VERSION );
			wp_enqueue_style( 'form-button-flat-style', MAILSTER_URI . 'assets/css/button-flat-style' . $suffix . '.css', array( 'form-button-style' ), MAILSTER_VERSION );
			wp_enqueue_style( 'form-button-minimal-style', MAILSTER_URI . 'assets/css/button-minimal-style' . $suffix . '.css', array( 'form-button-style' ), MAILSTER_VERSION );

			wp_enqueue_style( 'jquery-ui-style', MAILSTER_URI . 'assets/css/libs/jquery-ui' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_style( 'jquery-datepicker', MAILSTER_URI . 'assets/css/datepicker' . $suffix . '.css', array(), MAILSTER_VERSION );

			wp_enqueue_style( 'mailster-form-detail', MAILSTER_URI . 'assets/css/form-style' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_script( 'mailster-form-detail', MAILSTER_URI . 'assets/js/form-script' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable', 'mailster-clipboard-script', 'wp-color-picker' ), MAILSTER_VERSION );

			mailster_localize_script(
				'form',
				array(
					'require_save' => esc_html__( 'The changes you made will be lost if you navigate away from this page.', 'mailster' ),
					'not_saved'    => esc_html__( 'You haven\'t saved your recent changes on this form!', 'mailster' ),
					'prev'         => esc_html__( 'prev', 'mailster' ),
					'useit'        => esc_html__( 'Use your form as', 'mailster' ) . '&hellip;',
				)
			);
			wp_localize_script(
				'mailster-form-detail',
				'mailsterdata',
				array(
					'embedcode' => $this->get_empty_subscribe_button(),
				)
			);

		else :

			wp_enqueue_style( 'mailster-forms-table', MAILSTER_URI . 'assets/css/forms-table-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		endif;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_columns() {
		return $columns = array(
			'cb'         => '<input type="checkbox" />',
			'name'       => esc_html__( 'Name', 'mailster' ),
			'shortcode'  => esc_html__( 'Shortcode', 'mailster' ),
			'fields'     => esc_html__( 'Fields', 'mailster' ),
			'lists'      => esc_html__( 'Lists', 'mailster' ),
			'occurrence' => esc_html__( 'Occurrence', 'mailster' ),
			'preview'    => '',
		);

	}


	public function bulk_actions() {

		if ( empty( $_POST ) ) {
			return;
		}

		if ( empty( $_POST['forms'] ) ) {
			return;
		}

		if ( isset( $_POST['action'] ) && -1 != $_POST['action'] ) {
			$action = $_POST['action'];
		}

		if ( isset( $_POST['action2'] ) && -1 != $_POST['action2'] ) {
			$action = $_POST['action2'];
		}

		$redirect = add_query_arg( $_GET );

		switch ( $action ) {

			case 'delete':
				if ( current_user_can( 'mailster_delete_forms' ) ) {

					$success = $this->remove( $_POST['forms'] );
					if ( is_wp_error( $success ) ) {
						mailster_notice( sprintf( esc_html__( 'There was an error while deleting forms: %s', 'mailster' ), $success->get_error_message() ), 'error', true );

					} elseif ( $success ) {
						mailster_notice( sprintf( esc_html__( '%d forms have been removed', 'mailster' ), count( $_POST['forms'] ) ), 'error', true );
					}

					wp_redirect( $redirect );
					exit;

				}
				break;

			default:
				break;

		}

	}

	public function edit_hook() {

		if ( isset( $_GET['page'] ) && 'mailster_forms' == $_GET['page'] ) {

			// duplicate form
			if ( isset( $_GET['duplicate'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'mailster_duplicate_nonce' ) ) {
				$id = (int) $_GET['duplicate'];
				$id = $this->duplicate( $id );

			}

			if ( isset( $id ) && ! ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) {
				( isset( $_GET['ID'] ) )
					? wp_redirect( 'edit.php?post_type=newsletter&page=mailster_forms&ID=' . $id )
					: wp_redirect( 'edit.php?post_type=newsletter&page=mailster_forms' );
				exit;
			}
		}

	}

	public function edit_entry() {

		if ( isset( $_POST['mailster_data'] ) ) {

			$data     = (object) stripslashes_deep( $_POST['mailster_data'] );
			$redirect = $_POST['_wp_http_referer'];

			if ( isset( $_POST['save'] ) || isset( $_POST['structure'] ) || isset( $_POST['design'] ) || isset( $_POST['settings'] ) ) :

				parse_str( $_POST['_wp_http_referer'], $urlparams );

				$is_profile_form = isset( $_POST['profile_form'] ) && $_POST['profile_form'];

				if ( isset( $urlparams['new'] ) ) {

					$id = $this->add( $data );

					if ( is_wp_error( $id ) ) {
						mailster_notice( sprintf( esc_html__( 'There was an error while adding the form: %s', 'mailster' ), $id->get_error_message() ), 'error', true );

					}

					$redirect = remove_query_arg(
						'new',
						add_query_arg(
							array(
								'tab' => 'design',
								'ID'  => $id,
							),
							$redirect
						)
					);

				} else {

					$id = $this->update( $data );

					if ( is_wp_error( $id ) ) {
						mailster_notice( sprintf( esc_html__( 'There was an error while updating the form: %s', 'mailster' ), $id->get_error_message() ), 'error', true );

					}
				}

				if ( isset( $_POST['mailster_structure'] ) ) {
					$structure = stripslashes_deep( $_POST['mailster_structure'] );
					if ( ! isset( $structure['fields']['email'] ) ) {
						$structure['fields']['email'] = mailster_text( 'email' );
					}

					$required  = isset( $structure['required'] ) ? array_keys( $structure['required'] ) : array();
					$error_msg = isset( $structure['error_msg'] ) ? (array) $structure['error_msg'] : array();

					$this->update_fields( $id, $structure['fields'], $required, $error_msg );

				}

				if ( isset( $_POST['mailster_design'] ) ) {
					$design = stripslashes_deep( $_POST['mailster_design'] );

					$this->update_style( $id, urldecode( $design['style'] ), $design['custom'] );

				}

				if ( $is_profile_form ) {
					mailster_update_option( 'profile_form', $id );
				}

				if ( isset( $data->options ) ) {

					$this->update_options( $id, $data->options );

				}

				mailster_notice( isset( $urlparams['new'] ) ? esc_html__( 'Form added', 'mailster' ) : esc_html__( 'Form updated', 'mailster' ), 'success', true );

			endif;

			if ( isset( $_POST['design'] ) ) :

				wp_redirect( add_query_arg( array( 'tab' => 'design' ), $redirect ) );
				exit;

			elseif ( isset( $_POST['settings'] ) ) :

				wp_redirect( add_query_arg( array( 'tab' => 'settings' ), $redirect ) );
				exit;

			elseif ( isset( $_POST['structure'] ) ) :

				wp_redirect( add_query_arg( array( 'tab' => 'structure' ), $redirect ) );
				exit;

			elseif ( isset( $_POST['delete'] ) ) :

				if ( current_user_can( 'mailster_delete_forms' ) && $form = $this->get( (int) $_POST['mailster_data']['ID'] ) ) {
					$success = $this->remove( $form->ID );
					if ( is_wp_error( $success ) ) {
						mailster_notice( sprintf( esc_html__( 'There was an error while deleting forms: %s', 'mailster' ), $success->get_error_message() ), 'error', true );

					} elseif ( $success ) {
						mailster_notice( sprintf( esc_html__( 'Form %s has been removed', 'mailster' ), '<strong>&quot;' . $form->name . '&quot;</strong>' ), 'error', true );
						do_action( 'mailster_form_delete', $form->ID );
					}

					wp_redirect( 'edit.php?post_type=newsletter&page=mailster_forms' );
					exit;

				};

			endif;

			wp_redirect( $redirect );
			exit;

		}

	}


	public function view_forms() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if ( isset( $_GET['ID'] ) || isset( $_GET['new'] ) ) :

			include MAILSTER_DIR . 'views/forms/detail.php';

		else :

			include MAILSTER_DIR . 'views/forms/overview.php';

		endif;

	}


	/**
	 *
	 *
	 * @param unknown $form
	 */
	public function edit_form( $form ) {

		include MAILSTER_DIR . 'views/forms/edit.php';
	}


	public function screen_options() {

		require_once MAILSTER_DIR . 'classes/forms.table.class.php';

		$screen = get_current_screen();

		add_screen_option(
			'per_page',
			array(
				'label'   => esc_html__( 'Forms', 'mailster' ),
				'default' => 10,
				'option'  => 'mailster_forms_per_page',
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $status
	 * @param unknown $option
	 * @param unknown $value
	 * @return unknown
	 */
	public function save_screen_options( $status, $option, $value ) {

		if ( 'mailster_forms_per_page' == $option ) {
			return $value;
		}

		return $status;

	}


	/**
	 *
	 *
	 * @param unknown $args     (optional)
	 * @param unknown $endpoint (optional)
	 * @return unknown
	 */
	public function url( $args = array(), $endpoint = null ) {

		if ( is_null( $endpoint ) ) {
			$endpoint = get_home_url( null, 'mailster/form' );
		}

		return apply_filters( 'mailster_form_url', add_query_arg( $args, $endpoint ) );

	}


	/**
	 *
	 *
	 * @param unknown $entry
	 * @return unknown
	 */
	public function update( $entry ) {

		global $wpdb;

		$data = (array) $entry;

		if ( ! isset( $data['ID'] ) ) {
			return new WP_Error( 'id_required', esc_html__( 'updating form requires ID', 'mailster' ) );
		}

		$now = time();

		$lists = isset( $data['lists'] ) ? $data['lists'] : false;
		unset( $data['lists'] );

		if ( isset( $data['redirect'] ) ) {
			$data['redirect'] = trim( $data['redirect'] );
		}

		if ( isset( $data['confirmredirect'] ) ) {
			$data['confirmredirect'] = trim( $data['confirmredirect'] );
		}

		$wpdb->suppress_errors();

		if ( false !== $wpdb->update( "{$wpdb->prefix}mailster_forms", $data, array( 'ID' => $data['ID'] ) ) ) {

			$form_id = (int) $data['ID'];

			if ( $lists ) {
				$this->assign_lists( $form_id, $lists, true );
			}

			do_action( 'mailster_update_form', $form_id );

			mailster_clear_cache( 'form' );

			return $form_id;

		} else {

			return new WP_Error( 'form_exists', $wpdb->last_error );
		}

	}


	/**
	 *
	 *
	 * @param unknown $entry
	 * @return unknown
	 */
	public function add( $entry ) {

		global $wpdb;

		$now = time();

		$entry = is_string( $entry ) ? array( 'name' => $entry ) : (array) $entry;

		$entry = wp_parse_args(
			$entry,
			array(
				'name'            => esc_html__( 'Form', 'mailster' ),
				'submit'          => mailster_text( 'submitbutton' ),
				'asterisk'        => true,
				'userschoice'     => false,
				'dropdown'        => false,
				'inline'          => false,
				'overwrite'       => true,
				'addlists'        => false,
				'prefill'         => false,
				'style'           => '',
				'custom_style'    => '',
				'doubleoptin'     => true,
				'subject'         => esc_html__( 'Please confirm', 'mailster' ),
				'headline'        => esc_html__( 'Please confirm your Email Address', 'mailster' ),
				'content'         => sprintf( esc_html__( 'You have to confirm your email address. Please click the link below to confirm. %s', 'mailster' ), "\n{link}" ),
				'link'            => esc_html__( 'Click here to confirm', 'mailster' ),
				'resend'          => false,
				'resend_count'    => 2,
				'resend_time'     => 48,
				'template'        => 'notification.html',
				'vcard'           => false,
				'vcard_content'   => $this->get_vcard(),
				'confirmredirect' => '',
				'redirect'        => '',
				'added'           => $now,
				'updated'         => $now,
			)
		);

		if ( ! empty( $entry['style'] ) && ! is_string( $entry['style'] ) ) {
			$entry['style'] = json_encode( $entry['style'] );
		}

		if ( isset( $entry['ID'] ) && empty( $entry['ID'] ) ) {
			unset( $entry['ID'] );
		}

		$wpdb->suppress_errors();

		if ( $wpdb->insert( "{$wpdb->prefix}mailster_forms", $entry ) ) {

			$form_id = $wpdb->insert_id;

			do_action( 'mailster_add_form', $form_id );

			return $form_id;

		} else {

			return new WP_Error( 'form_exists', $wpdb->last_error );
		}

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function duplicate( $id ) {

		if ( ! current_user_can( 'mailster_add_forms' ) ) {
			wp_die( esc_html__( 'You are not allowed to add forms.', 'mailster' ) );
		}

		if ( $form = $this->get( $id ) ) {

			$fields    = wp_list_pluck( $form->fields, 'name' );
			$error_msg = wp_list_pluck( $form->fields, 'error_msg' );
			$required  = $form->required;
			$lists     = $form->lists;

			unset( $form->ID );
			unset( $form->fields );
			unset( $form->required );
			unset( $form->lists );
			unset( $form->added );
			unset( $form->updated );
			unset( $form->stylesheet );
			unset( $form->ajax );
			unset( $form->gdpr );
			if ( empty( $form->style ) ) {
				unset( $form->style );
			}

			if ( preg_match( '# \((\d+)\)$#', $form->name, $hits ) ) {
				$form->name = trim( preg_replace( '#(.*) \(\d+\)$#', '$1 (' . ( ++$hits[1] ) . ')', $form->name ) );
			} elseif ( $form->name ) {
				$form->name .= ' (2)';
			}

			$new_id = $this->add( $form );
			if ( ! is_wp_error( $new_id ) ) {
				$this->assign_lists( $new_id, $lists );
				$this->update_fields( $new_id, $fields, $required, $error_msg );

				do_action( 'mailster_form_duplicate', $id, $new_id );
			} else {
				mailster_notice( $new_id->get_error_message(), 'error', true );
			}

			return $new_id;

		}
		return false;
	}


	/**
	 *
	 *
	 * @param unknown $form_ids
	 * @param unknown $lists
	 * @param unknown $remove_old (optional)
	 * @return unknown
	 */
	public function assign_lists( $form_ids, $lists, $remove_old = false ) {

		global $wpdb;

		if ( ! is_array( $form_ids ) ) {
			$form_ids = array( $form_ids );
		}

		if ( ! is_array( $lists ) ) {
			$lists = array( $lists );
		}

		$now = time();

		$inserts = array();
		foreach ( $lists as $list_id ) {
			foreach ( $form_ids as $form_id ) {
				$inserts[] = "($list_id, $form_id, $now)";
			}
		}

		if ( empty( $inserts ) ) {
			return true;
		}

		$chunks = array_chunk( $inserts, 200 );

		$success = true;

		if ( $remove_old ) {
			$this->unassign_lists( $form_ids, null, $lists );
		}

		foreach ( $chunks as $insert ) {

			$sql  = "INSERT INTO {$wpdb->prefix}mailster_forms_lists (list_id, form_id, added) VALUES ";
			$sql .= ' ' . implode( ',', $insert );
			$sql .= ' ON DUPLICATE KEY UPDATE list_id = values(list_id), form_id = values(form_id)';

			$success = $success && ( false !== $wpdb->query( $sql ) );

		}
		return $success;

	}


	/**
	 *
	 *
	 * @param unknown $form_ids
	 * @param unknown $lists    (optional)
	 * @param unknown $not_list (optional)
	 * @return unknown
	 */
	public function unassign_lists( $form_ids, $lists = null, $not_list = null ) {

		global $wpdb;

		$form_ids = ! is_array( $form_ids ) ? array( (int) $form_ids ) : array_filter( $form_ids, 'is_numeric' );

		$sql = "DELETE FROM {$wpdb->prefix}mailster_forms_lists WHERE form_id IN (" . implode( ', ', $form_ids ) . ')';

		if ( ! is_null( $lists ) && ! empty( $lists ) ) {
			if ( ! is_array( $lists ) ) {
				$lists = array( $lists );
			}

			$sql .= ' AND list_id IN (' . implode( ', ', array_filter( $lists, 'is_numeric' ) ) . ')';
		}
		if ( ! is_null( $not_list ) && ! empty( $not_list ) ) {
			if ( ! is_array( $not_list ) ) {
				$not_list = array( $not_list );
			}

			$sql .= ' AND list_id NOT IN (' . implode( ', ', array_filter( $not_list, 'is_numeric' ) ) . ')';
		}

		if ( false !== $wpdb->query( $sql ) ) {

			do_action( 'mailster_unassign_form_lists', $form_ids, $lists, $not_list );

			return true;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @param unknown $field
	 * @param unknown $value
	 * @param unknown $required  (optional)
	 * @param unknown $error_msg (optional)
	 * @return unknown
	 */
	public function update_field( $form_id, $field, $value, $required = null, $error_msg = '' ) {

		return $this->update_fields( $form_id, array( $field => $value ), ( $required ? array( $field ) : array() ), array( $field => $error_msg ) );

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @param unknown $fields
	 * @param unknown $required  (optional)
	 * @param unknown $error_msg (optional)
	 * @return unknown
	 */
	public function update_fields( $form_id, $fields, $required = array(), $error_msg = array() ) {

		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}mailster_form_fields WHERE form_id = %d AND field_id NOT IN ('" . implode( "', '", array_keys( $fields ) ) . "')", $form_id ) );

		$sql = "INSERT INTO {$wpdb->prefix}mailster_form_fields (form_id, field_id, name, error_msg, required, position) VALUES ";

		$entries  = array();
		$position = 0;
		foreach ( $fields as $field_id => $name ) {
			$entries[] = $wpdb->prepare( '(%d, %s, %s, %s, %d, %d)', $form_id, $field_id, $name, ( isset( $error_msg[ $field_id ] ) ? $error_msg[ $field_id ] : '' ), ( in_array( $field_id, $required ) || $field_id == 'email' ), $position++ );
		}

		$sql .= implode( ', ', $entries );
		$sql .= ' ON DUPLICATE KEY UPDATE name = values(name), error_msg = values(error_msg), required = values(required), position = values(position)';

		return false !== $wpdb->query( $sql );

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @param unknown $field
	 * @param unknown $value   (optional)
	 * @return unknown
	 */
	public function update_options( $form_id, $field, $value = null ) {

		global $wpdb;

		$data = is_array( $field ) ? $field : array( $field => $value );

		$sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}mailster_forms SET ID = %d", $form_id );

		$entries = array();
		foreach ( $data as $col => $value ) {
			$sql .= $wpdb->prepare( ", `$col` = %s", $value );
		}

		$sql .= $wpdb->prepare( ' WHERE ID = %d', $form_id );

		return false !== $wpdb->query( $sql );

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @param unknown $style
	 * @param unknown $custom_style (optional)
	 * @return unknown
	 */
	public function update_style( $form_id, $style, $custom_style = '' ) {

		global $wpdb;

		if ( $style == '{}' ) {
			$style = '';
		}

		$sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}mailster_forms SET ID = %d, style = %s, custom_style = %s WHERE ID = %d", $form_id, $style, strip_tags( $custom_style ), $form_id );

		return false !== $wpdb->query( $sql );

	}


	/**
	 *
	 *
	 * @param unknown $form_ids
	 * @return unknown
	 */
	public function remove( $form_ids ) {

		global $wpdb;

		$form_ids = ! is_array( $form_ids ) ? array( (int) $form_ids ) : array_filter( $form_ids, 'is_numeric' );

		// delete from forms, form_fields
		$sql = "DELETE a,b FROM {$wpdb->prefix}mailster_forms AS a LEFT JOIN {$wpdb->prefix}mailster_form_fields AS b ON ( a.ID = b.form_id ) WHERE a.ID IN (" . implode( ',', $form_ids ) . ')';

		$success = false !== $wpdb->query( $sql );

		if ( $wpdb->last_error ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		return $success;

	}


	/**
	 *
	 *
	 * @param unknown $ID
	 * @param unknown $fields (optional)
	 * @param unknown $lists  (optional)
	 * @return unknown
	 */
	public function get( $ID = null, $fields = true, $lists = true ) {

		global $wpdb;

		if ( is_null( $ID ) ) {
			$sql = "SELECT a.* FROM {$wpdb->prefix}mailster_forms AS a WHERE 1 ORDER BY ID";

		} else {
			$sql = "SELECT a.* FROM {$wpdb->prefix}mailster_forms AS a WHERE a.ID = %d LIMIT 1";

			$sql = $wpdb->prepare( $sql, $ID );
		}

		if ( ! ( $forms = $wpdb->get_results( $sql ) ) ) {
			return array();
		}

		foreach ( $forms as $i => $form ) {

			if ( $fields ) {
				$forms[ $i ]->fields = $this->get_fields( $forms[ $i ]->ID );

				$forms[ $i ]->required = array();
				foreach ( $forms[ $i ]->fields as $field ) {
					if ( $field->required ) {
						$forms[ $i ]->required[] = $field->field_id;
					}
				}
			}

			if ( $lists ) {
				$forms[ $i ]->lists = $this->get_lists( $forms[ $i ]->ID, true );
			}

			$forms[ $i ]->style      = ( $forms[ $i ]->style ) ? json_decode( $forms[ $i ]->style ) : array();
			$forms[ $i ]->stylesheet = '';
			$forms[ $i ]->ajax       = true;
			foreach ( $forms[ $i ]->style as $selectors => $data ) {
				$forms[ $i ]->stylesheet .= '.mailster-form.mailster-form-' . $forms[ $i ]->ID . ' ' . $selectors . '{';
				foreach ( $data as $key => $value ) {
					$forms[ $i ]->stylesheet .= $key . ':' . $value . ';';
				}
				$forms[ $i ]->stylesheet .= '}';
			}
			$forms[ $i ]->stylesheet .= $forms[ $i ]->custom_style;
			if ( empty( $forms[ $i ]->submit ) ) {
				$forms[ $i ]->submit = mailster_text( 'submitbutton' );
			}

			$forms[ $i ]->gdpr = mailster_option( 'gdpr_forms' );
		}

		return is_null( $ID ) ? $forms : $forms[0];

	}


	/**
	 *
	 *
	 * @param unknown $fields (optional)
	 * @param unknown $lists  (optional)
	 * @return unknown
	 */
	public function get_all( $fields = false, $lists = false ) {

		return $this->get( null, $fields, $lists );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_list() {

		global $wpdb;

		$sql = "SELECT a.ID, a.name FROM {$wpdb->prefix}mailster_forms AS a WHERE 1 ORDER BY ID";

		if ( ! ( $forms = $wpdb->get_results( $sql ) ) ) {
			return array();
		}

		$return = array();

		foreach ( $forms as $i => $form ) {
			$return[ $form->ID ] = $form->name;
		}

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $ids_only (optional)
	 * @return unknown
	 */
	public function get_lists( $id, $ids_only = false ) {

		global $wpdb;

		$cache = mailster_cache_get( 'forms_lists' );
		if ( isset( $cache[ $id ] ) ) {
			return $cache[ $id ];
		}

		$sql = "SELECT lists.* FROM {$wpdb->prefix}mailster_lists AS lists LEFT JOIN {$wpdb->prefix}mailster_forms_lists AS forms_lists ON lists.ID = forms_lists.list_id WHERE forms_lists.form_id = %d";

		$lists = $wpdb->get_results( $wpdb->prepare( $sql, $id ) );

		return $ids_only ? wp_list_pluck( $lists, 'ID' ) : $lists;

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @return unknown
	 */
	public function get_fields( $form_id ) {

		global $wpdb;

		$sql = "SELECT forms_fields.field_id, forms_fields.name, forms_fields.error_msg, forms_fields.required FROM {$wpdb->prefix}mailster_form_fields AS forms_fields WHERE forms_fields.form_id = %d ORDER BY forms_fields.position ASC";

		$fields = $wpdb->get_results( $wpdb->prepare( $sql, $form_id ) );

		foreach ( $fields as $i => $field ) {
			if ( empty( $field->error_msg ) ) {
				$field->error_msg = ( $field->field_id == 'email' )
					? esc_html__( 'Email is missing or wrong', 'mailster' )
					: sprintf( esc_html__( '%s is missing', 'mailster' ), $field->name );
			}
			unset( $fields[ $i ] );
			$fields[ $field->field_id ] = $field;
		}

		return $fields;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_empty() {

		global $wpdb;

		$fields = wp_parse_args( array(), $wpdb->get_col( "DESCRIBE {$wpdb->prefix}mailster_forms" ) );

		$form = (object) array_fill_keys( array_values( $fields ), null );

		$form->fields = array();

		return $form;

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @return unknown
	 */
	public function get_occurrence( $form_id ) {

		global $wpdb;

		$return = array();
		$empty  = (object) array(
			'posts'   => array(),
			'widgets' => array(),
		);
		$empty  = array( 'posts' => array() );

		if ( false === ( $occurrence = mailster_cache_get( 'forms_occurrence' ) ) ) {

			$occurrence = array();

			$sql = "SELECT ID, post_title AS name, post_content FROM {$wpdb->posts} WHERE post_content LIKE '%[newsletter_signup_form%' AND post_status NOT IN ('inherit') AND post_type NOT IN ('newsletter', 'attachment')";

			$result = $wpdb->get_results( $sql );

			foreach ( $result as $row ) {
				preg_match_all( '#\[newsletter_signup_form((.*)id="?(\d+)"?)?#i', $row->post_content, $matches );
				foreach ( $matches[3] as $found_form_id ) {
					if ( ! $found_form_id ) {
						$found_form_id = 0;
					}

					$occurrence[ $found_form_id ]['posts'][ $row->ID ] = $row->name;
				}
			}

			$sql    = "SELECT option_id AS ID, option_value FROM {$wpdb->options} WHERE option_name = 'widget_mailster_signup'";
			$result = $wpdb->get_results( $sql );

			foreach ( $result as $row ) {
				$widgetdata = maybe_unserialize( $row->option_value );
				foreach ( $widgetdata as $data ) {
					if ( ! isset( $data['form'] ) ) {
						continue;
					}

					$occurrence[ $data['form'] ]['widgets'][] = $data['title'];
				}
			}

			mailster_cache_add( 'forms_occurrence', $occurrence );

		}

		return isset( $occurrence[ $form_id ] ) ? $occurrence[ $form_id ] : null;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_count() {

		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mailster_forms";

		return $wpdb->get_var( $sql );

	}



	/**
	 *
	 *
	 * @param unknown $style
	 * @param unknown $selector
	 * @param unknown $property
	 */
	private function _get_style( $style, $selector, $property ) {

		echo ( isset( $style->{$selector} ) && isset( $style->{$selector}->{$property} ) ) ? $style->{$selector}->{$property} : '';

	}

	/**
	 *
	 *
	 * @param unknown $form_id (optional)
	 * @param unknown $args    (optional)
	 */
	public function subscribe_button( $form_id = 1, $args = array() ) {

		echo $this->get_subscribe_button( $form_id, $args );
	}


	/**
	 *
	 *
	 * @param unknown $form_id (optional)
	 * @param unknown $args    (optional)
	 * @return unknown
	 */
	public function get_subscribe_button( $form_id = 1, $args = array() ) {

		$options = wp_parse_args(
			$args,
			array(
				'showcount' => false,
				'design'    => 'default',
				'label'     => mailster_text( 'submitbutton' ),
				'width'     => 480,
				'endpoint'  => null,
			)
		);

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		$button_src = MAILSTER_URI . 'assets/js/button' . $suffix . '.js';

		$button_src = apply_filters( 'mymail_subscribe_button_src', apply_filters( 'mailster_subscribe_button_src', $button_src, $options ), $options );

		$options['endpoint'] = $this->url(
			array(
				'id'     => $form_id,
				'iframe' => 1,
			),
			$options['endpoint']
		);

		$html = '<a href="' . $options['endpoint'] . '" class="mailster-subscribe-button" data-design="' . esc_attr( $options['design'] ) . '" data-showcount="' . ( $options['showcount'] ? 1 : 0 ) . '" data-width="' . esc_attr( $options['width'] ) . '">' . strip_tags( $options['label'] ) . '</a>';

		$script = "<script type=\"text/javascript\">!function(m,a,i,l,s,t,e,r){m[s]=m[s]||(function(){t=a.createElement(i);r=a.getElementsByTagName(i)[0];t.async=1;t.src=l;r.parentNode.insertBefore(t,r);return !0}())}(window,document,'script','$button_src','MailsterSubscribe');</script>";

		return $html . $script;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_empty_subscribe_button() {

		$button = $this->get_subscribe_button(
			1,
			array(
				'showcount' => true,
				'width'     => 999,
				'label'     => 'Subscribe',
			)
		);

		$button = strtr(
			$button,
			array(
				'id=1'                   => 'id=%ID%',
				' data-showcount="1"'    => '%SHOWCOUNT%',
				' data-width="999"'      => '%WIDTH%',
				' data-design="default"' => '%DESIGN%',
				'>Subscribe<'            => '>%LABEL%<',
			)
		);

		return $button;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_vcard() {

		$tags = mailster_option( 'tags' );

		$text  = 'BEGIN:VCARD' . "\n";
		$text .= 'N:Firstname;Lastname;;;' . "\n";
		$text .= 'ADR;INTL;PARCEL;WORK:;;StreetName;City;State;123456;Country' . "\n";
		$text .= 'EMAIL;INTERNET:' . mailster_option( 'from' ) . "\n";
		$text .= 'ORG:' . $tags['company'] . "\n";
		$text .= 'URL;WORK:' . $tags['homepage'] . "\n";
		$text .= 'END:VCARD' . "\n";
		return $text;
	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		if ( $new ) {
			$form_id = $this->add(
				array(
					'name'   => esc_html__( 'Default Form', 'mailster' ),
					'submit' => esc_html__( 'Subscribe', 'mailster' ),
				)
			);
			if ( ! is_wp_error( $form_id ) ) {
				$this->update_fields(
					$form_id,
					array(
						'email' => esc_html__( 'Email', 'mailster' ),
					)
				);
				$list_id = mailster( 'lists' )->get_by_name( esc_html__( 'Default List', 'mailster' ), 'ID' );
				$this->assign_lists( $form_id, $list_id );
			}
			$profile_form_id = $this->add(
				array(
					'name'        => esc_html__( 'Profile', 'mailster' ),
					'submit'      => esc_html__( 'Subscribe', 'mailster' ),
					'userschoice' => true,
				)
			);
			if ( ! is_wp_error( $profile_form_id ) ) {
				$this->update_fields(
					$profile_form_id,
					array(
						'email'     => esc_html__( 'Email', 'mailster' ),
						'firstname' => esc_html__( 'First Name', 'mailster' ),
						'lastname'  => esc_html__( 'Last Name', 'mailster' ),
					)
				);
				mailster_update_option( 'profile_form', $profile_form_id );
				$list_id = mailster( 'lists' )->get_by_name( esc_html__( 'Default List', 'mailster' ), 'ID' );
				$this->assign_lists( $profile_form_id, $list_id );
			}
		}

	}


	public function use_it_form_tab_intro( $form ) {
		?>
		<h4>&hellip; <?php esc_html_e( 'Shortcode', 'mailster' ); ?></h4>
		<p class="description"><?php esc_html_e( 'Use a shortcode on a blog post, page or wherever they are excepted.', 'mailster' ); ?> <?php printf( esc_html__( 'Read more about shortcodes at %s', 'mailster' ), '<a href="https://codex.wordpress.org/Shortcode">WordPress Codex</a>' ); ?></p>

		<h4>&hellip; <?php esc_html_e( 'Widget', 'mailster' ); ?></h4>
		<p class="description"><?php printf( esc_html__( 'Use this form as a %s in one of your sidebars', 'mailster' ), '<a href="widgets.php">' . esc_html__( 'widget', 'mailster' ) . '</a>' ); ?>.</p>

		<h4>&hellip; <?php esc_html_e( 'Subscriber Button', 'mailster' ); ?></h4>
		<p class="description"><?php esc_html_e( 'Embed your form on any site, no matter if it is your current or a third party one. It\'s similar to the Twitter button.', 'mailster' ); ?></p>

		<h4>&hellip; HTML</h4>
		<p class="description"><?php esc_html_e( 'Use your form via the HTML markup. This is often required by third party plugins. You can choose between an iframe or the raw HTML.', 'mailster' ); ?></p>
		<?php

	}

	public function use_it_form_tab_code( $form ) {
		?>
		<h4><?php esc_html_e( 'Shortcode', 'mailster' ); ?></h4>
		<p>
			<code id="form-shortcode" class="regular-text">[newsletter_signup_form id=<?php echo (int) $form->ID; ?>]</code> <a class="clipboard" data-clipboard-target="#form-shortcode"><?php esc_html_e( 'copy to clipboard', 'mailster' ); ?></a>
			<br><span class="description"><?php esc_html_e( 'Use this shortcode wherever they are excepted.', 'mailster' ); ?></span>
		</p>

		<h4><?php esc_html_e( 'PHP', 'mailster' ); ?></h4>
		<p>
			<code id="form-php-1" class="regular-text">&lt;?php echo mailster_form( <?php echo (int) $form->ID; ?> ); ?&gt;</code> <a class="clipboard" data-clipboard-target="#form-php-1"><?php esc_html_e( 'copy to clipboard', 'mailster' ); ?></a>
		</p>
		<p>
			<code id="form-php-2" class="regular-text">echo mailster_form( <?php echo (int) $form->ID; ?> );</code> <a class="clipboard" data-clipboard-target="#form-php-2"><?php esc_html_e( 'copy to clipboard', 'mailster' ); ?></a>
		</p>
		<p>
			<code id="form-php-3" class="regular-text">$form_html = mailster_form( <?php echo (int) $form->ID; ?> );</code> <a class="clipboard" data-clipboard-target="#form-php-3"><?php esc_html_e( 'copy to clipboard', 'mailster' ); ?></a>
		</p>
		<?php
	}

	public function use_it_form_tab_subscriber_button( $form ) {
		?>
		<p class="description"><?php esc_html_e( 'Embed a button where users can subscribe on any website', 'mailster' ); ?></p>

		<?php
		$subscribercount = mailster( 'subscribers' )->get_count( 'kilo' );
		$embeddedcode    = mailster( 'forms' )->get_subscribe_button();
		?>

		<div class="wrapper">

			<h4><?php esc_html_e( 'Button Style', 'mailster' ); ?></h4>
			<?php $styles = array( 'default', 'wp', 'twitter', 'flat', 'minimal' ); ?>
			<ul class="subscriber-button-style">
			<?php foreach ( $styles as $i => $style ) { ?>
				<li><label>
				<input type="radio" name="subscriber-button-style" value="<?php echo esc_attr( $style ); ?>" <?php checked( ! $i ); ?>>
				<div class="btn-widget design-<?php echo $style; ?> count">
					<div class="btn-count"><i></i><u></u><a><?php echo $subscribercount; ?></a></div>
					<a class="btn"><?php echo esc_html( $form->submit ); ?></a>
				</div>
				</label></li>
			<?php } ?>
			</ul>

		<div class="clear"></div>

		<div class="wrapper-left">

			<h4><?php esc_html_e( 'Button Options', 'mailster' ); ?></h4>

			<div class="button-options-wrap">

				<p><?php esc_html_e( 'Popup width', 'mailster' ); ?>:
					<input type="text" id="buttonwidth" placeholder="480" value="480" class="small-text"></p>

				<h4><?php esc_html_e( 'Label', 'mailster' ); ?></h4>
				<p><label><input type="radio" name="buttonlabel" value="default" checked>
				<?php esc_html_e( 'Use Form Default', 'mailster' ); ?></label></p>
				<p><input type="radio" name="buttonlabel" value="custom">
				<input type="text" id="buttonlabel" placeholder="<?php echo esc_attr( $form->submit ); ?>" value="<?php echo esc_attr( $form->submit ); ?>"></p>

				<h4><?php esc_html_e( 'Subscriber Count', 'mailster' ); ?></h4>
				<p><label><input type="checkbox" id="showcount" checked> <?php esc_html_e( 'Display subscriber count', 'mailster' ); ?></label></p>
				<p><label><input type="checkbox" id="ontop"> <?php esc_html_e( 'Count above Button', 'mailster' ); ?></label></p>

				</div>

			</div>

			<div class="wrapper-right">

				<h4><?php esc_html_e( 'Preview and Code', 'mailster' ); ?></h4>

				<p><?php esc_html_e( 'Test your button', 'mailster' ); ?> &hellip;</p>
					<div class="button-preview">
						<?php echo $embeddedcode; ?>
					</div>

				<p>&hellip; <?php esc_html_e( 'embed it somewhere', 'mailster' ); ?> &hellip;
					<div class="code-preview">
						<textarea id="form-embed-code" class="code" readonly></textarea>
						<a class="clipboard" data-clipboard-target="#form-embed-code"><?php esc_html_e( 'copy to clipboard', 'mailster' ); ?></a>
					</div>
				</p>
				<p>&hellip; <?php esc_html_e( 'or use this shortcode on your site', 'mailster' ); ?>
					<div class="shortcode-preview">
						<input id="form-shortcode-code" type="text" class="widefat code" readonly>
						<a class="clipboard" data-clipboard-target="#form-shortcode-code"><?php esc_html_e( 'copy to clipboard', 'mailster' ); ?></a>
					</div>
				</p>
			</div>
		</div>
		<?php
	}

	public function use_it_form_tab_form_html( $form ) {
		?>
		<h4><?php esc_html_e( 'iFrame Version', 'mailster' ); ?></h4>

		<?php $embedcode = '<iframe width="%s" height="%s" allowTransparency="true" frameborder="0" scrolling="no" style="border:none" src="' . $this->url( array( 'id' => $form->ID ) ) . '%s"></iframe>'; ?>

		<div>
			<label><?php esc_html_e( 'width', 'mailster' ); ?>: <input type="text" class="small-text embed-form-input" value="100%"></label>
			<label><?php esc_html_e( 'height', 'mailster' ); ?>: <input type="text" class="small-text embed-form-input" value="500"></label>
			<label title="<?php esc_attr_e( 'check this option to include the style.css of your theme into the form', 'mailster' ); ?>"><input type="checkbox" value="1" class="embed-form-input" checked> <?php esc_html_e( 'include themes style.css', 'mailster' ); ?></label>
			<textarea id="form-iframe" class="widefat code embed-form-output" data-embedcode="<?php echo esc_attr( $embedcode ); ?>"><?php echo esc_textarea( $embedcode ); ?></textarea>
			<a class="clipboard" data-clipboard-target="#form-iframe"><?php esc_html_e( 'copy to clipboard', 'mailster' ); ?></a>
		</div>

		<h4><?php esc_html_e( 'HTML Version', 'mailster' ); ?></h4>

		<div>
		<?php
			$form->add_class( 'extern' );
			$form->prefill( false );
			$form->ajax( false );
			$form->embed_style( false );
			$form->referer( 'extern' );
		?>
			<textarea id="form-html" class="widefat code form-output"><?php echo esc_textarea( $form->render( false ) ); ?></textarea>
			<a class="clipboard" data-clipboard-target="#form-html"><?php esc_html_e( 'copy to clipboard', 'mailster' ); ?></a>
		</div>
		<?php
	}


}
