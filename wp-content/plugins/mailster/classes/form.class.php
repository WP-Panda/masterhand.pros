<?php

class MailsterForm {

	private $values  = array();
	private $scheme  = 'http';
	private $object  = array(
		'userdata' => array(),
		'lists'    => array(),
		'errors'   => array(),
	);
	private $lists   = array();
	private $message = '';

	private $form          = null;
	private $formkey       = null;
	private $campaignID    = null;
	private $honeypot      = true;
	private $hash          = null;
	private $profile       = false;
	private $unsubscribe   = false;
	private $preview       = false;
	private $ajax          = true;
	private $embed_style   = true;
	private $form_endpoint = 'subscribe';
	private $classes       = array( 'mailster-form', 'mailster-form-submit' );
	private $redirect      = false;
	private $referer       = true;
	private $extern        = false;
	private $action        = 'subscribe';

	static $add_script = false;
	static $add_style  = false;

	public function __construct() {
		$this->scheme   = is_ssl() ? 'https' : 'http';
		$this->honeypot = true; // check https://bugs.chromium.org/p/chromium/issues/detail?id=132135
		$this->form     = new StdClass();
	}


	/**
	 *
	 *
	 * @param unknown $message
	 * @param unknown $field   (optional)
	 */
	public function set_error( $message, $field = '_' ) {
		$this->object['errors'][ $field ] = (string) $message;
	}


	/**
	 *
	 *
	 * @param unknown $message
	 * @param unknown $field   (optional)
	 */
	public function set_success( $message, $field = '_' ) {
		$this->object['success'][ $field ] = (string) $message;
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $args   (optional)
	 * @return unknown
	 */
	public function id( $id, $args = array() ) {

		$this->ID   = $id;
		$this->form = mailster( 'forms' )->get( $this->ID, true, true );
		if ( ! $this->form ) {
			$this->form = mailster( 'forms' )->get( mailster( 'helper' )->get_first_form_id(), true, true );
		}
		if ( isset( $args['id'] ) ) {
			unset( $args['id'] );
		}
		if ( ! empty( $args ) ) {

			// only allow certain elements
			$args = array_intersect_key( $args, array_flip( array( 'name', 'submit', 'asterisk', 'userschoice', 'precheck', 'dropdown', 'prefill', 'inline', 'overwrite', 'style', 'doubleoptin', 'subject', 'headline', 'content', 'link', 'template', 'redirect', 'gdpr' ) ) );

			// special case form stylesheet
			if ( isset( $args['style'] ) ) {
				$args['stylesheet'] = $args['style'];
				unset( $args['style'] );
			}

			$this->formkey = md5( AUTH_SALT . serialize( $args ) );
			set_transient( '_mailster_form_' . $this->formkey, $args );
			$this->form = (object) shortcode_atts( (array) $this->form, $args );

		}

		$this->ajax( $this->form->ajax );
		return $this;
	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function is_preview( $bool = true ) {

		$this->preview = ! ! $bool;
	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function ajax( $bool = true ) {

		$this->ajax = ! ! $bool;
		( $bool ) ? $this->add_class( 'mailster-ajax-form' ) : $this->remove_class( 'mailster-ajax-form' );
	}


	/**
	 *
	 *
	 * @param unknown $class
	 */
	public function add_class( $class ) {

		$this->classes[] = esc_attr( $class );
		$this->classes   = array_unique( $this->classes );
	}


	/**
	 *
	 *
	 * @param unknown $class
	 */
	public function remove_class( $class ) {

		if ( ( $key = array_search( $class, $this->classes ) ) !== false ) {
			unset( $this->classes[ $key ] );
		}

	}


	/**
	 *
	 *
	 * @param unknown $value
	 */
	public function redirect( $value ) {

		$this->redirect = $value;
	}


	/**
	 *
	 *
	 * @param unknown $key
	 * @param unknown $args
	 */
	public function __call( $key, $args ) {

		$value = empty( $args ) ? true : $args[0];

		if ( isset( $this->form->{$key} ) ) {
			$this->form->{$key} = $value;
		} else {
			$this->{$key} = $value;
		}

	}


	/**
	 *
	 *
	 * @param unknown $key
	 * @return unknown
	 */
	public function __get( $key ) {

		if ( isset( $this->form->{$key} ) ) {
			return $this->form->{$key};
		}

		return null;
	}


	/**
	 *
	 *
	 * @param unknown $echo (optional)
	 * @return unknown
	 */
	public function render( $echo = true ) {

		if ( ! $this->form ) {
			return;
		}

		add_action( 'wp_footer', array( &$this, 'print_script' ) );
		add_action( 'admin_footer', array( &$this, 'print_script' ) );

		if ( ! defined( 'DONOTCACHEPAGE' ) && mailster_option( 'disable_cache_frontpage' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		if ( $this->prefill ) {

			$current_user = wp_get_current_user();
			if ( $current_user->ID != 0 ) {
				$this->object['userdata']['email']     = $current_user->user_email;
				$this->object['userdata']['firstname'] = get_user_meta( $current_user->ID, 'first_name', true );
				$this->object['userdata']['lastname']  = get_user_meta( $current_user->ID, 'last_name', true );
				if ( ! $this->object['userdata']['firstname'] ) {
					$this->object['userdata']['firstname'] = $current_user->display_name;
				}
			}
		}
		if ( $this->profile || $this->unsubscribe ) {

			if ( $subscriber = mailster( 'subscribers' )->get_by_hash( $this->hash, true ) ) {
				$this->object['userdata'] = (array) $subscriber;
			} elseif ( $subscriber = mailster( 'subscribers' )->get_by_wpid( null, true ) ) {
				$this->object['userdata'] = (array) $subscriber;
			}
		} else {
		}

		if ( isset( $_GET['userdata'] ) ) {
			$this->object['userdata'] = wp_parse_args( $this->object['userdata'], $_GET['userdata'] );
		}

		if ( isset( $_GET['mailster_error'] ) ) {
			// for non ajax request
			$transient = 'mailster_error_' . esc_attr( $_GET['mailster_error'] );
			$data      = get_transient( $transient );
			if ( $data ) {
				$this->object['userdata'] = $data['userdata'];
				$this->object['errors']   = $data['errors'];
				$this->object['lists']    = $data['lists'];
				$this->has_errors( ! ! count( $this->object['errors'] ) );
				delete_transient( $transient );
			}
		}
		if ( isset( $_GET['success'] ) ) {
			$this->object['success'] = array( mailster_text( $_GET['success'] ) );
		}

		$this->add_class( 'mailster-form-' . $this->ID );

		$html  = '';
		$html .= '<!--Mailster:styles-->';

		$html .= '<form action="<!--Mailster:formaction-->" method="post" class="<!--Mailster:classes-->" novalidate>';
		$html .= '<!--Mailster:infos-->';
		$html .= '<!--Mailster:hiddenfields-->';

		$customfields = mailster()->get_custom_fields();
		$inline       = $this->form->inline;
		$asterisk     = $this->form->asterisk;

		$fields = array();

		if ( $this->unsubscribe ) {

			$single_opt_out = mailster_option( 'single_opt_out' );
			$buttonlabel    = mailster_text( 'unsubscribebutton', esc_html__( 'Unsubscribe', 'mailster' ) );

			// instant unsubscribe
			if ( $subscriber && $single_opt_out && isset( $_COOKIE['mailster'] ) ) {

				if ( mailster( 'subscribers' )->unsubscribe( $subscriber->ID, $this->campaignID, 'link_unsubscribe' ) ) {
					$buttonlabel        = $this->form->submit;
					$this->form->fields = array();
					$this->set_success( mailster_text( 'unsubscribe' ) );
				} else {
					$this->set_error( mailster_text( 'unsubscribeerror' ) );
				}
			}
			if ( get_query_var( '_mailster_hash' ) ) {
				$this->form->fields = array();
			} else {
				$this->form->fields = array_intersect_key( $this->form->fields, array_flip( array( 'email' ) ) );
			}
			$this->form->userschoice = false;
		} else {
			$buttonlabel = strip_tags( $this->form->submit );
		}

		if ( $this->profile ) {
			$this->form->fields['_status'] = (object) array(
				'field_id' => '_status',
				'name'     => esc_html__( 'Status', 'mailster' ),
			);
		}

		if ( empty( $this->form->fields ) ) {
			$this->form->fields = array();
		}

		foreach ( $this->form->fields as $field_id => $field ) {

			$required = isset( $field->required ) && $field->required;

			$label     = ! empty( $field->name ) ? $field->name : mailster_text( $field->field_id );
			$label     = apply_filters( 'mailster_form_field_label_' . $field_id, $label, $field );
			$esc_label = esc_attr( strip_tags( $label ) );

			$value = ( isset( $this->object['userdata'][ $field->field_id ] )
				? esc_attr( $this->object['userdata'][ $field->field_id ] )
				: '' );

			$class = ( isset( $this->object['errors'][ $field->field_id ] ) ? ' error' : '' );

			switch ( $field->field_id ) {

				case 'email':
					$fields['email'] = '<div class="mailster-wrapper mailster-email-wrapper' . $class . '">';
					if ( ! $inline ) {
						$fields['email'] .= '<label for="mailster-email-' . $this->ID . '">' . $label . ' ' . ( $asterisk ? '<span class="mailster-required">*</span>' : '' ) . '</label>';
					}

					$fields['email'] .= '<input id="mailster-email-' . $this->ID . '" name="email" type="email" value="' . $value . '"' . ( $inline ? ' placeholder="' . $esc_label . ( $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input mailster-email mailster-required" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '" spellcheck="false">';
					$fields['email'] .= '</div>';

					break;

				case 'firstname':
				case 'lastname':
					$fields[ $field->field_id ] = '<div class="mailster-wrapper mailster-' . $field->field_id . '-wrapper' . $class . '">';
					if ( ! $inline ) {
						$fields[ $field->field_id ] .= '<label for="mailster-' . $field->field_id . '-' . $this->ID . '">' . $label . ( $required && $asterisk ? ' <span class="mailster-required">*</span>' : '' ) . '</label>';
					}

					$fields[ $field->field_id ] .= '<input id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" type="text" value="' . $value . '"' . ( $inline ? ' placeholder="' . $esc_label . ( $required && $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '">';
					$fields[ $field->field_id ] .= '</div>';

					break;

				case '_status':
					$subscriber_status = isset( $this->object['userdata']['status'] ) ? (int) $this->object['userdata']['status'] : 1;

					$fields[ $field->field_id ] = '<div class="mailster-wrapper mailster-' . $field->field_id . '-wrapper' . $class . '">';

					if ( ! $inline ) {
						$fields[ $field->field_id ] .= '<label for="mailster-' . $field->field_id . '-' . $this->ID . '">' . $label;
						if ( $required && $asterisk ) {
							$fields[ $field->field_id ] .= ' <span class="mailster-required">*</span>';
						}

						$fields[ $field->field_id ] .= '</label>';
					}
					$fields[ $field->field_id ] .= '<select id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" class="input mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '">';

					$statuses = mailster( 'subscribers' )->get_status( null, true );
					foreach ( $statuses as $status => $name ) {
						if ( in_array( $status, array( 1, 2 ) ) || $status == $subscriber_status ) {
							$fields[ $field->field_id ] .= '<option value="' . $status . '" ' . selected( $subscriber_status, $status, false ) . '>' . $name . '</option>';
						}
					}
					$fields[ $field->field_id ] .= '</select></div>';

					break;
				// custom fields
				default:
					if ( ! isset( $customfields[ $field->field_id ] ) ) {
						break;
					}

					$data = $customfields[ $field->field_id ];

					$fields[ $field->field_id ] = '<div class="mailster-wrapper mailster-' . $field->field_id . '-wrapper' . $class . '">';

					$showlabel = ! $inline;

					switch ( $data['type'] ) {
						case 'dropdown':
						case 'radio':
							$showlabel = true;
							break;
						case 'checkbox':
							$showlabel = false;
							break;
					}

					if ( $showlabel ) {
						$fields[ $field->field_id ] .= '<label for="mailster-' . $field->field_id . '-' . $this->ID . '">' . $label;
						if ( $required && $asterisk ) {
							$fields[ $field->field_id ] .= ' <span class="mailster-required">*</span>';
						}

						$fields[ $field->field_id ] .= '</label>';
					}

					switch ( $data['type'] ) {

						case 'dropdown':
							$fields[ $field->field_id ] .= '<select id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" class="input mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '">';
							foreach ( $data['values'] as $v ) {
								if ( ! isset( $data['default'] ) || ! $data['default'] ) {
									$data['default'] = $value;
								}

								$fields[ $field->field_id ] .= '<option value="' . $v . '" ' . selected( $data['default'], $v, false ) . '>' . $v . '</option>';
							}
							$fields[ $field->field_id ] .= '</select>';
							break;

						case 'radio':
							$fields[ $field->field_id ] .= '<ul class="mailster-list">';
							$i                           = 0;
							foreach ( $data['values'] as $v ) {
								if ( ! isset( $data['default'] ) || ! $data['default'] ) {
									$data['default'] = $value;
								}

								$fields[ $field->field_id ] .= '<li><label><input id="mailster-' . $field->field_id . '-' . $this->ID . '-' . ( $i++ ) . '" name="' . $field->field_id . '" type="radio" value="' . $v . '" class="radio mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" ' . checked( $data['default'], $v, false ) . ' aria-label="' . $v . '"> ' . $v . '</label></li>';

							}
							$fields[ $field->field_id ] .= '</ul>';
							break;

						case 'checkbox':
							$fields[ $field->field_id ] .= '<label for="mailster-' . $field->field_id . '-' . $this->ID . '">';
							$fields[ $field->field_id ] .= '<input type="hidden" name="' . $field->field_id . '" value="0"><input id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" type="checkbox" value="1" ' . checked( $value || ( ! $value && isset( $data['default'] ) && $data['default'] ), true, false ) . ' class="mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '"> ';
							$fields[ $field->field_id ] .= ' ' . $label;
							if ( $required && $asterisk ) {
								$fields[ $field->field_id ] .= ' <span class="mailster-required">*</span>';
							}

							$fields[ $field->field_id ] .= '</label>';

							break;

						case 'date':
							$fields[ $field->field_id ] .= '<input id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" type="text" value="' . $value . '"' . ( $inline ? ' placeholder="' . $esc_label . ( $required && $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input input-date datepicker mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '">';

							break;

						case 'textarea':
							$fields[ $field->field_id ] .= '<textarea id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '"' . ( $inline ? ' placeholder="' . $label . ( $required && $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $label . '">' . esc_textarea( $value ) . '</textarea>';

							break;

						default:
							$fields[ $field->field_id ] .= '<input id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" type="text" value="' . $value . '"' . ( $inline ? ' placeholder="' . $esc_label . ( $required && $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '">';

					}

					$fields[ $field->field_id ] .= '</div>';

			}
		}

		if ( $this->form->userschoice ) {
			$lists = mailster( 'forms' )->get_lists( $this->ID );

			if ( ! empty( $lists ) ) {

				if ( $this->profile && isset( $this->object['userdata']['ID'] ) ) {
					$userlists = mailster( 'subscribers' )->get_lists( $this->object['userdata']['ID'], true );
				}

				$fields['lists'] = '<div class="mailster-wrapper mailster-lists-wrapper' . $class . '"><label>' . mailster_text( 'lists', esc_html__( 'Lists', 'mailster' ) ) . '</label>';

				if ( $this->form->dropdown ) {
					$fields['lists'] .= '<select name="lists[]" class="input mailster-lists-dropdown">';
					foreach ( $lists as $list ) {
						$selected = ! empty( $this->object['errors'] ) && in_array( $list->ID, $this->object['lists'] );

						$fields['lists'] .= '<option value="' . $list->ID . '"' . selected( $selected, true, false ) . '> ' . $list->name . '</option>';
					}
					$fields['lists'] .= '</select>';
				} else {
					$fields['lists'] .= '<ul class="mailster-list">';
					foreach ( $lists as $i => $list ) {
						$checked = ( empty( $this->object['errors'] ) && $this->form->precheck )
							|| ( ! empty( $this->object['errors'] ) && in_array( $list->ID, $this->object['lists'] ) )
							|| ( $this->form->precheck && $this->preview );

						if ( $this->profile && isset( $userlists ) ) {
							$checked = in_array( $list->ID, $userlists );
						}

						$fields['lists'] .= '<li><label title="' . esc_attr( $list->description ) . '"><input type="hidden" name="lists[' . $i . ']" value=""><input class="mailster-list mailster-list-' . $list->slug . '" type="checkbox" name="lists[' . $i . ']" value="' . $list->ID . '" ' . checked( $checked, true, false ) . ' aria-label="' . esc_attr( $list->name ) . '"> ' . $list->name;
						if ( $list->description ) {
							$fields['lists'] .= ' <span class="mailster-list-description mailster-list-description-' . $list->ID . '">' . $list->description . '</span>';
						}

						$fields['lists'] .= '</label></li>';

					}
					$fields['lists'] .= '</ul>';
				}

				$fields['lists'] .= '</div>';
			}
		}

		if ( ! $this->profile && ! $this->unsubscribe && $this->form->gdpr ) {
			if ( ! is_numeric( $this->form->gdpr ) ) {
				$label = $this->form->gdpr;
			} else {
				$label = mailster_text( 'gdpr_text' );
			}
			$fields['_gdpr']    = '<div class="mailster-wrapper mailster-_gdpr-wrapper">';
			$fields['_gdpr']   .= '<label for="mailster-_gdpr-' . $this->ID . '">';
			$fields['_gdpr']   .= '<input type="hidden" name="_gdpr" value="0"><input id="mailster-_gdpr-' . $this->ID . '" name="_gdpr" type="checkbox" value="1" class="mailster-_gdpr mailster-required" aria-required="true" aria-label="' . esc_attr( $label ) . '"> ';
			$gdpr_label_content = $label;
			if ( mailster_option( 'gdpr_link' ) ) {
				$gdpr_label_content .= ' (<a href="' . mailster_option( 'gdpr_link' ) . '" target="_top">' . esc_html__( 'Link', 'mailster' ) . '</a>)';
			}

			$fields['_gdpr'] .= apply_filters( 'mailster_gdpr_label', $gdpr_label_content );
			$fields['_gdpr'] .= '</label>';
			$fields['_gdpr'] .= '</div>';
		}

		$fields['_submit'] = '<div class="mailster-wrapper mailster-submit-wrapper form-submit">';

		if ( apply_filters( 'mailster_form_button_as_input', true, $this->ID, $this->form ) ) {
			$fields['_submit'] .= '<input name="submit" type="submit" value="' . esc_attr( $buttonlabel ) . '" class="submit-button button" aria-label="' . esc_attr( $buttonlabel ) . '">';
		} else {
			$fields['_submit'] .= '<button name="submit" class="submit-button button" aria-label="' . esc_attr( $buttonlabel ) . '">' . esc_attr( $buttonlabel ) . '</button>';
		}
		$fields['_submit'] .= '</div>';

		// remove submit button on single opt out
		if ( $this->unsubscribe && $subscriber && $single_opt_out && isset( $_COOKIE['mailster'] ) ) {
			unset( $fields['_submit'] );
		}

		$fields = apply_filters( 'mymail_form_fields', apply_filters( 'mailster_form_fields', $fields, $this->ID, $this->form ), $this->ID, $this->form );

		if ( ! is_admin() && apply_filters( 'mailster_honeypot', $this->honeypot ) ) {
			// place honeypot after email field
			$position = array_search( 'email', array_keys( $fields ) ) + 1;
			$fields   = array_slice( $fields, 0, $position, true ) +
				array( '_honeypot' => '<label style="position:absolute;top:-99999px;' . ( is_rtl() ? 'right' : 'left' ) . ':-99999px;z-index:-99;"><input name="n_' . wp_create_nonce( 'mailster_honeypot' ) . '_email" type="email" tabindex="-1" autocomplete="noton" autofill="off"></label>' ) +
				array_slice( $fields, $position, null, true );
		}

		$html .= '<div class="mailster-form-fields">';
		$html .= "\n" . implode( "\n", $fields ) . "\n";
		$html .= '</div>' . "\n";

		$html .= '</form>' . "\n";

		$html = str_replace( '<!--Mailster:formaction-->', $this->get_form_action( $this->profile ? 'mailster_profile_submit' : 'mailster_form_submit' ), $html );
		$html = str_replace( '<!--Mailster:classes-->', esc_attr( implode( ' ', $this->classes ) ), $html );
		$html = str_replace( '<!--Mailster:styles-->', $this->get_styles(), $html );
		$html = str_replace( '<!--Mailster:hiddenfields-->', $this->get_hidden_fields(), $html );
		$html = str_replace( '<!--Mailster:infos-->', $this->get_info(), $html );

		if ( $this->profile ) {
			$html = apply_filters( 'mailster_profile_form', $html, $this->ID, $this->form );
		}
		if ( $this->unsubscribe ) {
			$html = apply_filters( 'mailster_unsubscribe_form', $html, $this->ID, $this->form );
		}

		$html = apply_filters( 'mymail_form', apply_filters( 'mailster_form', $html, $this->ID, $this->form ), $this->ID, $this->form );

		if ( ! $echo ) {
			return $html;
		}

		echo $html;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_styles() {

		$html = '';
		if ( ! self::$add_style ) {
			ob_start();
			$this->print_style( $this->embed_style );
			$html .= ob_get_contents();
			ob_end_clean();

		}
		if ( isset( $this->form->stylesheet ) && ! empty( $this->form->stylesheet ) ) {
			$html .= '<style type="text/css" media="screen" class="mailster-custom-form-css">' . "\n" . $this->strip_css( $this->form->stylesheet ) . '</style>' . "\n";
		}

		return $html;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_hidden_fields() {

		global $pagenow;

		$html = '';

		$redirect = esc_url( home_url( remove_query_arg( array( 'mailster_error', 'mailster_success' ), $_SERVER['REQUEST_URI'] ) ) );
		$referer  = $pagenow == 'form.php' || get_query_var( '_mailster_form' ) ? ( isset( $_GET['referer'] ) ? $_GET['referer'] : 'extern' ) : $redirect;

		if ( $this->action ) {
			$html .= '<input name="_action" type="hidden" value="' . esc_attr( $this->action ) . '">' . "\n";
		}

		if ( $this->redirect ) {
			$html .= '<input name="_redirect" type="hidden" value="' . esc_attr( is_string( $this->redirect ) ? $this->redirect : $redirect ) . '">' . "\n";
		}

		if ( $this->referer ) {
			$html .= '<input name="_referer" type="hidden" value="' . esc_attr( is_string( $this->referer ) ? $this->referer : $referer ) . '">' . "\n";
		}

		if ( $this->formkey ) {
			$html .= '<input name="_formkey" type="hidden" value="' . esc_attr( $this->formkey ) . '">' . "\n";
		}

		if ( $this->hash ) {
			$html .= '<input name="_hash" type="hidden" value="' . esc_attr( $this->hash ) . '">' . "\n";
		}

		if ( $this->campaignID ) {
			$html .= '<input name="_campaign_id" type="hidden" value="' . esc_attr( $this->campaignID ) . '">' . "\n";
		}

		if ( $nonce = $this->get_nonce() ) {
			$html .= '<input name="_nonce" type="hidden" value="' . esc_attr( $nonce ) . '">' . "\n";
		}

		$html .= '<input name="formid" type="hidden" value="' . $this->ID . '">' . "\n";

		return $html;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_nonce() {
		if ( is_admin() || mailster_option( 'use_post_nonce' ) ) {
			return mailster_option( 'post_nonce' );
		}
		return wp_create_nonce( 'mailster-form-nonce' );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_info() {

		$html = '';

		if ( ! empty( $this->object['success'] ) ) :
			$html .= '<div class="mailster-form-info success">';
			$html .= $this->get_message( 'success' );
			$html .= $this->message;
			$html .= '</div>' . "\n";
		endif;
		if ( ! empty( $this->object['errors'] ) ) :
			$html .= '<div class="mailster-form-info error">';
			$html .= $this->get_message();
			$html .= '</div>' . "\n";
		endif;

		return $html;

	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function is_profile( $bool = true ) {

		if ( $bool ) {

			$this->profile      = true;
			$this->form->submit = mailster_text( 'profilebutton', esc_html__( 'Update Profile', 'mailster' ) );
			$this->add_class( 'is-profile' );
			$this->set_hash();
			$this->action = 'update';

		} else {

			$this->profile = false;
			$this->remove_class( 'is-profile' );
			$this->hash = null;

		}

	}


	/**
	 *
	 *
	 * @param unknown $ID
	 */
	public function campaign_id( $ID ) {
		$this->campaignID = (int) $ID;
	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function is_unsubscribe( $bool = true ) {

		if ( $bool ) {

			$this->unsubscribe = true;
			$this->add_class( 'is-unsubscribe' );
			$this->set_hash();
			$this->action = 'unsubscribe';

		} else {

			$this->remove_class( 'is-unsubscribe' );
			$this->unsubscribe = false;
			$this->hash        = null;

		}

	}


	/**
	 *
	 *
	 * @param unknown $hash (optional)
	 */
	private function set_hash( $hash = null ) {
		$this->hash = is_null( $hash ) ? $this->get_hash() : $hash;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_hash() {

		if ( isset( $_COOKIE['mailster'] ) ) {
			return $_COOKIE['mailster'];
		}

		if ( $hash = get_query_var( '_mailster_hash' ) ) {
			return $hash;
		}

		if ( ! $this->unsubscribe && is_user_logged_in() && ( $subscriber = mailster( 'subscribers' )->get_by_wpid( get_current_user_id() ) ) ) {
			return $subscriber->hash;
		}

		return null;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function unsubscribe_form() {

		$campaign = $this->campaignID ? mailster( 'campaigns' )->get( $this->campaignID ) : null;

		$subscriber = $this->hash ? mailster( 'subscribers' )->get_by_hash( $this->hash ) : null;

		$single_opt_out = mailster_option( 'single_opt_out' );

		$infoclass = '';

		// instant unsubscribe
		if ( $subscriber && $single_opt_out ) {

			if ( mailster( 'subscribers' )->unsubscribe( $subscriber->ID, $this->campaignID ) ) {
				$infoclass           = ' success';
				$this->message       = '<p>' . mailster_text( 'unsubscribe' ) . '</p>';
				$this->form_endpoint = 'subscribe';
			} else {
				$infoclass     = ' error';
				$this->message = '<p>' . mailster_text( 'unsubscribeerror' ) . '</p>';
			}
		}

		global $post;
		$form_id = '';
		if ( preg_match( '#\[newsletter_signup_form id="?(\d+)"?#i', $post->post_content, $matches ) ) {
			$form_id = (int) $matches[1];
			$this->id( $form_id );
		}

		$html = '';

		$html .= $this->get_styles();

		$action = 'mailster_form_unsubscribe';

		$html .= '<form action="' . $this->get_form_action( $action ) . '" method="post" class="mailster-form mailster-form-' . $form_id . ' mailster-form-submit mailster-ajax-form" id="mailster-form-unsubscribe" novalidate>' . "\n";
		$html .= '<div class="mailster-form-info ' . $infoclass . '">';
		$html .= $this->message;
		$html .= '</div>';
		$html .= '<input name="_action" type="hidden" value="unsubscribe">';
		$html .= '<input name="hash" type="hidden" value="' . $this->hash . '">';
		$html .= '<input name="campaign" type="hidden" value="' . $this->campaignID . '">';
		$html .= '<div class="mailster-form-fields">';
		if ( ! $this->hash ) {

			$html .= '<div class="mailster-wrapper mailster-email-wrapper"><label for="mailster-email">' . mailster_text( 'email', esc_html__( 'Email', 'mailster' ) ) . ' <span class="mailster-required">*</span></label>';
			$html .= '<input id="mailster-email" class="input mailster-email mailster-required" name="email" type="email" value=""></div>';

		}
		if ( $subscriber && $single_opt_out ) {
		} else {
			$buttontext = mailster_text( 'unsubscribebutton', esc_html__( 'Unsubscribe', 'mailster' ) );
			$html      .= '<div class="mailster-wrapper mailster-submit-wrapper form-submit"><input name="submit" type="submit" value="' . $buttontext . '" class="submit-button button"></div>';
			$html      .= '</div>';
		}
		$html .= '</form>';

		return apply_filters( 'mymail_unsubscribe_form', apply_filters( 'mailster_unsubscribe_form', $html, $this->campaignID ), $this->campaignID );
	}


	public function submit() {

		$_BASE = $_POST;

		if ( empty( $_BASE ) ) {
			wp_die( 'no data' );
		};

		$submissiontype = isset( $_BASE['_action'] ) ? $_BASE['_action'] : 'subscribe';

		if ( ! $submissiontype ) {
			wp_die( 'wrong submissiontype' );
		};

		$_nonce     = isset( $_BASE['_nonce'] ) ? $_BASE['_nonce'] : null;
		$_formkey   = isset( $_BASE['_formkey'] ) ? $_BASE['_formkey'] : null;
		$post_nonce = mailster_option( 'post_nonce' );

		if ( $_nonce || $post_nonce ) {
			if ( wp_verify_nonce( $_nonce, 'mailster-form-nonce' ) || $post_nonce == $_nonce ) {
			} else {
				$this->object['errors']['_nonce'] = esc_html__( 'Security Nonce is invalid!', 'mailster' );
			}
		}

		$baselink = get_permalink( mailster_option( 'homepage' ) );
		if ( ! $baselink ) {
			$baselink = site_url();
		}

		$referer = isset( $_BASE['_referer'] ) ? $_BASE['_referer'] : $baselink;
		if ( $referer == 'extern' || isset( $_GET['_extern'] ) ) {
			$referer = esc_url( mailster_get_referer() );
		}

		$now = time();

		$form_args = array();
		if ( $_formkey ) {
			$form_args = (array) get_transient( '_mailster_form_' . $_formkey );
		}

		$this->id( isset( $_BASE['formid'] ) ? (int) $_BASE['formid'] : 1, $form_args );

		$double_opt_in = $this->form->doubleoptin;
		$overwrite     = $this->form->overwrite;

		$customfields = mailster()->get_custom_fields();

		$formdata = stripslashes_deep( isset( $_BASE['userdata'] ) ? $_BASE['userdata'] : $_BASE );
		$formdata = apply_filters( 'mymail_pre_submit', apply_filters( 'mailster_pre_submit', $formdata, $this->form ), $this->form );

		foreach ( $this->form->fields as $field_id => $field ) {

			$type = isset( $customfields[ $field_id ] ) ? $customfields[ $field_id ]['type'] : 'textfield';

			$value = isset( $formdata[ $field_id ] ) ? $formdata[ $field_id ] : '';

			switch ( $type ) {
				case 'textarea':
					$value = stripslashes( $value );
					break;
				case 'date':
					$value = mailster( 'helper' )->do_timestamp( $value, 'Y-m-d' );
				default:
					$value = sanitize_text_field( $value );
					break;
			}

			$this->object['userdata'][ $field_id ] = $value;

			if ( $submissiontype != 'unsubscribe' ) {
				if ( ( $field_id == 'email' && ! mailster_is_email( trim( $this->object['userdata'][ $field_id ] ) ) )
					|| ( ! $this->object['userdata'][ $field_id ] && in_array( $field_id, $this->form->required ) ) ) {
					$this->object['errors'][ $field_id ] = $field->error_msg;
				}
			}
		}

		$this->object['userdata']['email'] = trim( $this->object['userdata']['email'] );

		if ( $this->form->userschoice ) {
			$this->object['lists'] = isset( $_BASE['lists'] ) ? array_filter( (array) $_BASE['lists'] ) : array();
		} else {
			$this->object['lists'] = $this->form->lists;
		}

		if ( isset( $_BASE['_gdpr'] ) ) {
			if ( empty( $_BASE['_gdpr'] ) ) {
				$this->object['errors']['_gdpr'] = mailster_text( 'gdpr_error' );
			} else {
				$this->object['userdata']['gdpr'] = $now;
			}
		}

		if ( isset( $_BASE['_formkey'] ) ) {
			$this->object['userdata']['formkey'] = $_BASE['_formkey'];
		}

		if ( ! is_admin() && apply_filters( 'mailster_honeypot', $this->honeypot ) && empty( $this->object['errors'] ) ) {
			$honeypotnonce = wp_create_nonce( 'mailster_honeypot' );
			$honeypot      = isset( $_BASE[ 'n_' . $honeypotnonce . '_email' ] ) ? $_BASE[ 'n_' . $honeypotnonce . '_email' ] : null;

			if ( ! empty( $honeypot ) ) {
				$this->object['errors']['_honeypot'] = esc_html__( 'Honeypot is for bears only!', 'mailster' );
			}
		}

		// to hook into the system
		$this->object = apply_filters( 'mymail_submit', apply_filters( 'mailster_submit', $this->object ) );
		$this->object = apply_filters( 'mymail_submit_' . $this->ID, apply_filters( 'mailster_submit_' . $this->ID, $this->object ) );

		if ( $this->valid() ) {

			$email = $this->object['userdata']['email'];

			$entry = wp_parse_args(
				array(
					'lang' => mailster_get_lang(),
				),
				$this->object['userdata']
			);

			$remove_old_lists = false;

			switch ( $submissiontype ) {

				case 'subscribe':
					$entry = wp_parse_args(
						array(
							'signup'  => $now,
							'confirm' => $double_opt_in ? 0 : $now,
							'status'  => $double_opt_in ? 0 : 1,
							'lang'    => mailster_get_lang(),
							'referer' => $referer,
							'form'    => $this->ID,
						),
						$this->object['userdata']
					);

					if ( $overwrite && $subscriber = mailster( 'subscribers' )->get_by_mail( $entry['email'] ) ) {
						$entry = wp_parse_args(
							array(
								// set status to the form default if it's not "subscribed"
								'status' => $subscriber->status != 1 ? $entry['status'] : $subscriber->status,
								'ID'     => $subscriber->ID,
							),
							$entry
						);

						if ( isset( $entry['form'] ) ) {
							unset( $entry['form'] );
						}

						$subscriber_id = mailster( 'subscribers' )->update( $entry, true, true );
						$message       = $entry['status'] == 0 ? 'confirmation' : 'success';
						$message       = $double_opt_in ? 'confirmation' : 'success';

						$submissiontype = 'update';

					} else {

						$subscriber_id = mailster( 'subscribers' )->add( $entry );
						$message       = $double_opt_in ? 'confirmation' : 'success';

					}

					$assign_lists = $this->object['lists'];

					break;

				case 'unsubscribe':
					$campaign_id   = ! empty( $_BASE['_campaign_id'] ) ? (int) $_BASE['_campaign_id'] : null;
					$subscriber_id = $subscriber = null;

					if ( isset( $_BASE['email'] ) ) {
						if ( ! empty( $_BASE['email'] ) ) {
							$subscriber = mailster( 'subscribers' )->get_by_mail( $_BASE['email'] );
							if ( ! $subscriber ) {
								$this->object['errors']['email'] = mailster_text( 'unsubscribeerror' );
							}
						} else {
							$this->object['errors']['email'] = mailster_text( 'enter_email' );
						}
						$type = 'email_unsubscribe';
					} elseif ( isset( $_BASE['_hash'] ) ) {
						$subscriber = mailster( 'subscribers' )->get_by_hash( $_BASE['_hash'] );
						$type       = 'link_unsubscribe';
					}

					if ( $subscriber ) {
						$subscriber_id = $subscriber->ID;
						if ( ! ( $return['success'] = mailster( 'subscribers' )->unsubscribe( $subscriber_id, $campaign_id, $type ) ) ) {
							$this->object['errors']['email'] = mailster_text( 'unsubscribeerror' );
						} else {
							$message = 'unsubscribe';
						}
					} else {

					}

					break;

				case 'update':
					$this->set_hash( $_BASE['_hash'] );

					if ( ! ( $subscriber = mailster( 'subscribers' )->get_by_hash( $this->hash, true ) ) ) {
						$subscriber = mailster( 'subscribers' )->get_by_wpid( null, true );
					}

					if ( $subscriber ) {

						$unassign_lists          = null;
						$assign_lists            = null;
						$subscriber_notification = false;

						if ( $this->form->userschoice ) {
							$assigned_lists = mailster( 'subscribers' )->get_lists( $subscriber->ID, true );

							$unassign_lists = array_diff( $assigned_lists, $this->object['lists'] );
							$fix_lists      = array_diff( $assigned_lists, $this->form->lists );
							$unassign_lists = array_diff( $unassign_lists, $fix_lists );

							$assign_lists = array_diff( $this->object['lists'], $assigned_lists );
						}

						// change status if other than pending, subscribed or unsubscribed
						$status = $subscriber->status >= 3 ? 1 : $subscriber->status;
						if ( isset( $_BASE['_status'] ) ) {
							if ( $status == 0 && (int) $_BASE['_status'] == 1 ) {

								if ( mailster_option( 'track_users' ) ) {
									$ip                  = mailster_get_ip();
									$entry['ip']         = $ip;
									$entry['ip_confirm'] = $ip;
								}
								$entry['confirm'] = time();

							}
							$status = (int) $_BASE['_status'];
						}

						if ( isset( $entry['email'] ) && $entry['email'] != $subscriber->email && $double_opt_in ) {
							$status                  = 0;
							$subscriber_notification = true;
						}

						$entry = wp_parse_args(
							array(
								'status' => $status,
								'ID'     => $subscriber->ID,
							),
							$entry
						);

						if ( isset( $entry['form'] ) ) {
							unset( $entry['form'] );
						}

						$subscriber_id = mailster( 'subscribers' )->update( $entry, true, true, $subscriber_notification );
						if ( is_wp_error( $subscriber_id ) ) {
							$subscriber_id = $subscriber->ID;
						}

						$message = $entry['status'] == 0 ? 'confirmation' : 'profile_update';

					} else {
						$subscriber_id = new WP_Error( 'error', esc_html__( 'There was an error updating the user', 'mailster' ) );
					}

					break;
			}

			if ( is_wp_error( $subscriber_id ) ) {

				$error_code = $subscriber_id->get_error_code();

				switch ( $error_code ) {

					case 'email_exists':
						if ( $exists = mailster( 'subscribers' )->get_by_mail( $this->object['userdata']['email'] ) ) {

							$this->object['errors']['email'] = mailster_text( 'already_registered' );

							if ( $exists->status == 0 ) {
								$this->object['errors']['confirmation'] = mailster_text( 'new_confirmation_sent' );
								mailster( 'subscribers' )->send_confirmations( $exists->ID, true, true );

							} elseif ( $exists->status == 1 ) {

								// change status to "pending" if user is other than subscribed
							} elseif ( $exists->status != 1 ) {
								if ( $double_opt_in ) {
									$this->object['errors']['confirmation'] = mailster_text( 'new_confirmation_sent' );
									mailster( 'subscribers' )->change_status( $exists->ID, 0, true );
									mailster( 'subscribers' )->send_confirmations( $exists->ID, true, true );
								} else {
									mailster( 'subscribers' )->change_status( $exists->ID, 1, true );
								}
							}
						}

						break;

					default:
						$field                            = isset( $entry[ $error_code ] ) ? $error_code : '_';
						$this->object['errors'][ $field ] = $subscriber_id->get_error_message();

						break;

				}
			} else {

				if ( ! empty( $assign_lists ) ) {
					mailster( 'subscribers' )->assign_lists( $subscriber_id, $assign_lists, $remove_old_lists, ! $double_opt_in );
					if ( 'update' == $submissiontype ) {
						mailster( 'subscribers' )->send_confirmations( $subscriber_id, true, true, $this->form->ID );
					}
				}
				if ( ! empty( $unassign_lists ) ) {
					mailster( 'subscribers' )->unassign_lists( $subscriber_id, $unassign_lists );
				}

				$target = add_query_arg(
					array(
						'subscribe' => '',
					),
					$baselink
				);

			}

			$this->object = apply_filters( 'mymail_post_submit', apply_filters( 'mailster_post_submit', $this->object ) );
			$this->object = apply_filters( 'mymail_post_submit_' . $this->ID, apply_filters( 'mailster_post_submit_' . $this->ID, $this->object ) );

			if ( $this->valid() ) {
				$return = array(
					'action'  => $submissiontype,
					'success' => true,
					'html'    => '<p>' . mailster_text( $message ) . '</p>',
				);
			} else {
				$return = array(
					'action'  => $submissiontype,
					'success' => false,
					'fields'  => $this->object['errors'],
					'html'    => '<p>' . $this->get_message( 'errors', true ) . '</p>',
				);
			}

			if ( $this->form->redirect && 'unsubscribe' != $submissiontype ) {
				$return = wp_parse_args( array( 'redirect' => $this->form->redirect ), $return );
			}

			// an error occurred
		} else {

			$return = array(
				'action'  => $submissiontype,
				'success' => false,
				'fields'  => $this->object['errors'],
				'html'    => $this->get_message(),
			);

		}

		// ajax request
		if ( ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) :

			header( 'Content-type: application/json' );
			echo json_encode( $return );
			exit;

		endif;

		if ( $this->is_extern() ) {

			if ( ! $return['success'] ) {
				wp_die( $return['html'] . '<a href="javascript:history.back()">' . esc_html__( 'Go back', 'mailster' ) . '</a>' );
				exit;
			}

			$target = isset( $return['redirect'] ) ? $return['redirect'] : esc_url( add_query_arg( 'success', $message, mailster_get_referer() ) );

		} else {

			if ( ! $return['success'] ) {
				wp_die( $return['html'] . '<a href="javascript:history.back()">' . esc_html__( 'Go back', 'mailster' ) . '</a>' );
				exit;
			}

			$target = isset( $return['redirect'] ) ? $return['redirect'] : esc_url( add_query_arg( 'success', $message, mailster_get_referer() ) );

		}

		wp_redirect( $target );
		exit;

	}


	public function unsubscribe() {

		$return['action']  = 'unsubscribe';
		$return['success'] = false;

		$_BASE = $_POST;

		if ( empty( $_BASE ) ) {
			wp_die( 'no data' );
		};

		$campaign_id = ! empty( $_BASE['_campaign_id'] ) ? (int) $_BASE['_campaign_id'] : null;

		if ( isset( $_BASE['email'] ) ) {
			$return['success'] = mailster( 'subscribers' )->unsubscribe_by_mail( $_BASE['email'], $campaign_id, 'email_unsubscribe' );
		} elseif ( isset( $_BASE['hash'] ) ) {
			$return['success'] = mailster( 'subscribers' )->unsubscribe_by_hash( $_BASE['hash'], $campaign_id, 'link_unsubscribe' );
		}

		// redirect if no ajax request
		if ( ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) {

			$return['html'] = $return['success']
				? mailster_text( 'unsubscribe' )
				: ( empty( $_POST['email'] )
				? mailster_text( 'enter_email' )
				: mailster_text( 'unsubscribeerror' ) );

			header( 'Content-type: application/json' );
			echo json_encode( $return );
			exit;

		} else {

			if ( $return['success'] ) {
				wp_die( $return['html'] . '<a href="javascript:history.back()">' . mailster_text( 'unsubscribe' ) . '</a>' );
			} else {
				wp_die( $return['html'] . '<a href="javascript:history.back()">' . ( empty( $_POST['email'] ) ? mailster_text( 'enter_email' ) : mailster_text( 'unsubscribeerror' ) ) . '</a>' );
			}
			exit;

		}

	}


	/**
	 *
	 *
	 * @param unknown $css
	 * @return unknown
	 */
	public function strip_css( $css ) {
		$css = strip_tags( $css );
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css = trim( str_replace( array( "\r\n", "\r", "\n", "\t", '   ', '  ' ), '', $css ) );
		$css = str_replace( ' {', '{', $css );
		$css = str_replace( '{ ', '{', $css );
		$css = str_replace( ' }', '}', $css );
		$css = str_replace( '}', '}' . "\n", $css );
		return $css;
	}


	/**
	 *
	 *
	 * @param unknown $action (optional)
	 * @return unknown
	 */
	private function get_form_action( $action = '' ) {

		$is_permalink = mailster( 'helper' )->using_permalinks();

		$prefix = ! mailster_option( 'got_url_rewrite' ) ? '/index.php' : '/';

		return $is_permalink
			? home_url( $prefix . '/mailster/' . $this->form_endpoint )
			: add_query_arg( array( 'action' => $action ), admin_url( 'admin-ajax.php', $this->scheme ) );

	}


	/**
	 *
	 *
	 * @param unknown $type   (optional)
	 * @param unknown $simple (optional)
	 * @return unknown
	 */
	private function get_message( $type = 'errors', $simple = false ) {

		$html = '';
		if ( ! empty( $this->object[ $type ] ) ) {
			if ( ! $simple && $type == 'errors' ) {
				$html .= '<p>' . mailster_text( 'error' ) . '</p>';
			}

			$html .= '<ul>';
			foreach ( $this->object[ $type ] as $field => $name ) {
				$html .= '<li>' . apply_filters( 'mymail_error_output_' . $field, apply_filters( 'mailster_error_output_' . $field, $name, $this->object ), $this->object ) . '</li>';
			}
			$html .= '</ul>';
		}

		return $html;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function is_extern() {
		return parse_url( mailster_get_referer(), PHP_URL_HOST ) != parse_url( home_url(), PHP_URL_HOST );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function valid() {
		return empty( $this->object['errors'] );
	}


	public static function print_script() {

		if ( self::$add_script ) {
			return;
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'mailster-form', MAILSTER_URI . 'assets/js/form' . $suffix . '.js', apply_filters( 'mailster_no_jquery', array( 'jquery' ) ), MAILSTER_VERSION, true );
		wp_register_script( 'mailster-form-placeholder', MAILSTER_URI . 'assets/js/placeholder-fix' . $suffix . '.js', apply_filters( 'mailster_no_jquery', array( 'jquery' ) ), MAILSTER_VERSION, true );

		global $is_IE;

		if ( $is_IE ) {
			wp_print_scripts( 'jquery' );
			echo '<!--[if lte IE 9]>';
			wp_print_scripts( 'mailster-form-placeholder' );
			echo '<![endif]-->';
		}

		wp_print_scripts( 'mailster-form' );

		self::$add_script = true;

	}


	/**
	 *
	 *
	 * @param unknown $embed (optional)
	 */
	public static function print_style( $embed = true ) {

		if ( self::$add_style ) {
			return;
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mailster-form-default', MAILSTER_URI . 'assets/css/form-default-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		( $embed )
			? mailster( 'helper' )->wp_print_embedded_styles( 'mailster-form-default' )
			: wp_print_styles( 'mailster-form-default' );

		self::$add_style = true;

	}


	/**
	 * deprecated methods
	 *
	 * @param unknown $form_id (optional)
	 * @return unknown
	 */
	public function get( $form_id = 0 ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->get()" );

		$return = mailster( 'helper' )->object_to_array( mailster( 'forms' )->get( $form_id ) );

		$return['id'] = $return['ID'];

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $option (optional)
	 * @return unknown
	 */
	public function get_all( $option = null ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->get_all()" );

		$forms = mailster( 'helper' )->object_to_array( mailster( 'forms' )->get_all() );
		foreach ( $forms as $i => $form ) {
			$forms[ $i ]['id'] = $form['ID'];
		}

		return $forms;

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @param unknown $key
	 * @param unknown $value
	 * @return unknown
	 */
	public function set( $form_id, $key, $value ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->update_field()" );

		$return = mailster( 'forms' )->update_field( $form_id, $key, $value );

		$return['id'] = $return['ID'];

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @param unknown $list_id
	 * @return unknown
	 */
	public function assign_list( $form_id, $list_id ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->assign_lists()" );

		$return = mailster( 'forms' )->assign_lists( $form_id, $list_id );

		$return['id'] = $return['ID'];

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @param unknown $list_id
	 * @return unknown
	 */
	public function unassign_list( $form_id, $list_id ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->unassign_lists()" );

		$return = mailster( 'forms' )->unassign_lists( $form_id, $list_id );

		$return['id'] = $return['ID'];

		return $return;

	}


}
