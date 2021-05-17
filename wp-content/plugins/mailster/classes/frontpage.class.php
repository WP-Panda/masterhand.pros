<?php

class MailsterFrontpage {

	public function __construct() {

		add_action( 'init', array( &$this, 'init' ) );

		add_action( 'query_vars', array( &$this, 'set_query_vars' ) );
		add_action( 'template_redirect', array( &$this, 'template_redirect' ), 1 );
		add_action( 'pre_get_posts', array( &$this, 'filter_status_on_archive' ), 1 );

		add_action( 'mailster_wphead', array( &$this, 'styles' ) );
		add_action( 'mailster_wpfooter', array( &$this, 'scripts' ) );

		add_filter( 'rewrite_rules_array', array( &$this, 'rewrite_rules' ) );

		add_action( 'post_updated', array( &$this, 'update_homepage' ) );
		add_filter( 'oembed_request_post_id', array( &$this, 'add_filter_status_to_publish' ) );

		add_shortcode( 'newsletter', array( &$this, 'newsletter' ) );
		add_shortcode( 'newsletter_list', array( &$this, 'newsletter_list' ) );
		add_shortcode( 'newsletter_signup_form', array( &$this, 'newsletter_signup_form' ) );

		add_shortcode( 'newsletter_signup', array( &$this, 'do_shortcode' ) );
		add_shortcode( 'newsletter_unsubscribe', array( &$this, 'do_shortcode_unsubscribe' ) );
		add_shortcode( 'newsletter_profile', array( &$this, 'do_shortcode_profile' ) );

		add_shortcode( 'newsletter_confirm', array( &$this, 'do_shortcode_wrong_confirm' ) );

		add_shortcode( 'newsletter_subscribers', array( &$this, 'newsletter_subscribers' ) );
		add_shortcode( 'newsletter_button', array( &$this, 'newsletter_button' ) );

	}


	public function init() {

		add_filter( 'the_content', array( &$this, 'shortcode_empty_paragraph_fix' ) );

		if ( mailster_option( '_flush_rewrite_rules' ) ) {
			flush_rewrite_rules( true );
			mailster_update_option( '_flush_rewrite_rules', false );
		}

	}


	/**
	 *
	 *
	 * @param unknown $wp_rules
	 * @return unknown
	 */
	public function rewrite_rules( $wp_rules ) {

		$slugs = implode( '|', (array) mailster_option( 'slugs', array( 'confirm', 'subscribe', 'unsubscribe', 'profile' ) ) );

		$rules = array();

		if ( $homepage = mailster_option( 'homepage' ) ) {

			$pagename = get_page_uri( $homepage );

			$rules[ '(index\.php/)?(' . preg_quote( $pagename ) . ')/(' . $slugs . ')/?([a-f0-9]{32})?/?([a-z0-9/]*)?' ] = 'index.php?pagename=' . preg_replace( '#\.html$#', '', $pagename ) . '&_mailster_page=$matches[3]&_mailster_hash=$matches[4]&_mailster_extra=$matches[5]';

			$rules['^(index\.php/)?(mailster|mymail)/(subscribe)/?$']                             = 'index.php?_mailster=$matches[3]';
			$rules[ '(index\.php/)?(mailster)/(' . $slugs . ')/?([a-f0-9]{32})?/?([a-z0-9/]*)?' ] = 'index.php?pagename=' . preg_replace( '#\.html$#', '', $pagename ) . '&_mailster_page=$matches[3]&_mailster_hash=$matches[4]&_mailster_extra=$matches[5]';

			if ( get_option( 'page_on_front' ) == $homepage && get_option( 'show_on_front' ) == 'page' ) {
				$rules[ '^(' . $slugs . ')/?([a-f0-9]{32})?/?([a-z0-9/]*)?' ] = 'index.php?page_id=' . $homepage . '&_mailster_page=$matches[1]&_mailster_hash=$matches[2]&_mailster_extra=$matches[3]';
			}
		}

		$rules['^(index\.php/)?(mailster|mymail)/([0-9]+)/([a-f0-9]{32})/?([a-zA-Z0-9=_+]+)?/?([0-9]+)?/?'] = 'index.php?_mailster=$matches[3]&_mailster_hash=$matches[4]&_mailster_page=$matches[5]&_mailster_extra=$matches[6]';

		if ( $secret = mailster_option( 'cron_secret' ) ) {
			$rules[ '^(index\.php/)?mailster/(' . $secret . ')/?([0-9a-z]+)?/?$' ] = 'index.php?_mailster_cron=$matches[2]&_mailster_extra=$matches[3]';
		}

		$rules['^(index\.php/)?mailster/form$'] = 'index.php?_mailster_form=1';

		$rules = apply_filters( 'mailster_rewrite_rules', $rules );

		return $rules + $wp_rules;

	}


	/**
	 *
	 *
	 * @param unknown $post_id
	 */
	public function update_homepage( $post_id ) {

		$post = get_post( $post_id );
		if ( 'mailster' == $post->post_name ) {
			mailster_notice( sprintf( esc_html__( 'Please do not use %1$s in %2$s as page slug as it conflicts with Mailster form submission!', 'mailster' ), '&quot;<strong>mailster</strong>&quot;', '<a>' . str_replace( 'mailster', '<strong>mailster</strong>', get_permalink( $post_id ) . '</a>' ) ), 'error', true );
		}

		if ( $post_id == mailster_option( 'homepage' ) ) {
			flush_rewrite_rules();
			do_action( 'mailster_update_homepage', $post );
		}

	}


	/**
	 *
	 *
	 * @param unknown $vars
	 * @return unknown
	 */
	public function set_query_vars( $vars ) {

		$vars[] = '_mailster';
		$vars[] = '_mailster_page';
		$vars[] = '_mailster_hash';
		$vars[] = '_mailster_extra';
		$vars[] = '_mailster_cron';
		$vars[] = '_mailster_form';
		return $vars;

	}


	/**
	 *
	 *
	 * @param unknown $subpage (optional)
	 * @param unknown $hash    (optional)
	 * @param unknown $extra   (optional)
	 * @return unknown
	 */
	public function get_link( $subpage = null, $hash = '', $extra = '' ) {

		$is_permalink = mailster( 'helper' )->using_permalinks();

		$homepage = get_permalink( mailster_option( 'homepage' ) );

		$prefix = ! mailster_option( 'got_url_rewrite' ) ? '/index.php' : '/';

		if ( ! $is_permalink ) {
			$homepage = str_replace( trailingslashit( get_bloginfo( 'url' ) ), untrailingslashit( get_bloginfo( 'url' ) ) . $prefix, $homepage );
		}

		if ( is_null( $subpage ) ) {
			return $homepage;
		}

		$subpage = $this->get_page_by_slug( $subpage );

		wp_parse_str( (string) parse_url( $homepage, PHP_URL_QUERY ), $query_string );

		// remove all query strings
		if ( ! empty( $query_string ) ) {
			$homepage = remove_query_arg( array_keys( $query_string ), $homepage );
		}

		if ( $is_permalink ) {

			$url = trailingslashit( $homepage ) . trailingslashit( $subpage . '/' . ( $hash ? $hash . '/' : '' ) . $extra );

		} else {

			$query = array(
				'_mailster_page'  => $subpage,
				'_mailster_hash'  => $hash,
				'_mailster_extra' => $extra,
			);

			if ( get_option( 'page_on_front' ) == mailster_option( 'homepage' ) ) {
				$query = wp_parse_args( $query, array( 'page_id' => mailster_option( 'homepage' ) ) );
			}

			$url = add_query_arg( $query, $homepage );

		}

		return ! empty( $query_string ) ? add_query_arg( $query_string, $url ) : $url;

	}


	public function template_redirect() {

		if ( is_404() ) {
			global $wp;
			if ( preg_match( '#^(index\.php/)?mailster/#', $wp->request ) && ! isset( $_REQUEST['mailster_error'] ) ) {
				flush_rewrite_rules();
				$redirect_to = add_query_arg( array( 'mailster_error' => 1 ), home_url( $wp->request ) );
				wp_redirect( $redirect_to, 302 );
				exit;
			}
		}

		// Mailster < 2 method
		if ( isset( $_GET['mailster'] ) ) {

			$target      = isset( $_GET['t'] ) ? str_replace( '&amp;', '&', preg_replace( '/\s+/', '', $_GET['t'] ) ) : null;
			$hash        = isset( $_GET['k'] ) ? preg_replace( '/\s+/', '', $_GET['k'] ) : null;
			$count       = isset( $_GET['c'] ) ? (int) $_GET['c'] : 0;
			$campaign_id = (int) $_GET['mailster'];
			if ( isset( $_GET['s'] ) ) {
				$target = ( ! empty( $_GET['s'] ) ? 'https://' : 'http://' ) . $target;
			}

			if ( preg_match( '#[a-zA-Z\d\/+]+#', $target ) ) {
				$target = base64_decode( strtr( $target, '-_', '+/' ) );
			}

			if ( false !== strpos( $target, 'unsubscribe=' ) ) {
				$target = untrailingslashit( $this->get_link( 'unsubscribe' ) );
			}

			if ( false !== strpos( $target, 'profile=' ) ) {
				$target = untrailingslashit( $this->get_link( 'profile' ) );
			}

			set_query_var( '_mailster', $campaign_id );
			set_query_var( '_mailster_page', rtrim( strtr( base64_encode( $target ), '+/', '-_' ), '=' ) );
			set_query_var( '_mailster_hash', $hash );
			set_query_var( '_mailster_extra', $count );

		}

		if ( isset( $_GET['mailster_unsubscribe'] ) ) {
			if ( mailster( 'helper' )->using_permalinks() ) {
				wp_redirect( $this->get_link( 'unsubscribe', $_GET['mailster_unsubscribe'], $_GET['k'] ), 301 );
				exit;
			} else {
				set_query_var( '_mailster_page', 'unsubscribe' );
				set_query_var( '_mailster_hash', isset( $_GET['k'] ) ? preg_replace( '/\s+/', '', $_GET['k'] ) : null );

			}
		} elseif ( isset( $_GET['mailster_profile'] ) ) {
			if ( mailster( 'helper' )->using_permalinks() ) {
				wp_redirect( $this->get_link( 'profile', $_GET['mailster_profile'] ), 301 );
				exit;
			} else {
				set_query_var( '_mailster_page', 'profile' );
				set_query_var( '_mailster_hash', isset( $_GET['k'] ) ? preg_replace( '/\s+/', '', $_GET['k'] ) : null );

			}
		} elseif ( isset( $_GET['mailster_confirm'] ) ) {
			if ( mailster( 'helper' )->using_permalinks() ) {
				wp_redirect( $this->get_link( 'confirm', $_GET['mailster_confirm'] ), 301 );
				exit;
			} else {
				set_query_var( '_mailster_page', 'confirm' );
				set_query_var( '_mailster_hash', isset( $_GET['k'] ) ? preg_replace( '/\s+/', '', $_GET['k'] ) : null );

			}
		}

		// convert custom slugs
		if ( get_query_var( '_mailster_page' ) && mailster( 'helper' )->using_permalinks() ) {
			set_query_var( '_mailster_page', $this->get_page_by_slug( get_query_var( '_mailster_page' ) ) );
		}

		if ( get_query_var( '_mailster' ) ) {
			if ( in_array( get_query_var( '_mailster' ), array( 'subscribe', '___update', '___unsubscribe' ) ) ) {
				$this->do_post_actions();

			} else {
				$this->do_tracking_actions();
			}
		} elseif ( get_query_var( '_mailster_page' ) ) {

			$this->do_homepage();

		} else {

		}

		// front page & archive page
		if ( get_query_var( 'post_type' ) == 'newsletter' ) {

			if ( is_archive() ) {

				add_filter( 'get_the_excerpt', array( &$this, 'content_as_iframe' ), -1 );
				add_filter( 'get_the_content', array( &$this, 'content_as_iframe' ), -1 );
				add_filter( 'the_excerpt', array( &$this, 'content_as_iframe' ), -1 );
				add_filter( 'the_content', array( &$this, 'content_as_iframe' ), -1 );

			} elseif ( function_exists( 'is_embed' ) && is_embed() ) {

				// alter the embed content
				add_filter( 'the_excerpt_embed', array( &$this, 'the_excerpt_embed' ), -1 );

			} else {

				$this->do_frontpage();
			}
		}
	}


	private function do_post_actions() {

		switch ( get_query_var( '_mailster' ) ) {
			case 'subscribe':
				mailster( 'form' )->submit();
				break;
			case 'update':
				mailster( 'form' )->submit();
				break;
			case 'unsubscribe':
				mailster( 'form' )->unsubscribe();
				break;
		}
		exit;
	}


	private function do_tracking_actions() {

		$campaign_id = (int) get_query_var( '_mailster', 0 );
		$target      = mailster()->decode_link( get_query_var( '_mailster_page' ) );
		$hash        = get_query_var( '_mailster_hash' );
		$index       = get_query_var( '_mailster_extra' );
		$redirect_to = null;

		if ( ! ( $campaign = mailster( 'campaigns' )->get( $campaign_id, false ) ) ) {
			$this->do_404();
		}
		if ( ! ( $subscriber = mailster( 'subscribers' )->get_by_hash( $hash, false ) ) ) {
			$subscriber = (object) array(
				'ID'   => null,
				'hash' => $hash,
			);
		}
		$campaign_id = $campaign->ID;
		$meta        = mailster( 'campaigns' )->meta( $campaign_id );

		if ( $target ) {

			if ( ! preg_match( '#^https?:#', $target ) ) {
				wp_die( sprintf( esc_html__( '%s is not a valid URL!', 'mailster' ), '<code>&quot;' . urldecode( $target ) . '&quot;</code>' ) );
			}

			$target = apply_filters( 'mymail_click_target', apply_filters( 'mailster_click_target', $target, $campaign_id ), $campaign_id );

			// check if external URLS are actually in the campaign to prevent URL hijacking
			$target_host = wp_parse_url( $target, PHP_URL_HOST );
			$home_host   = wp_parse_url( home_url(), PHP_URL_HOST );

			// either the target url is in the home url or vice versa - to allow subdomains (improvable)
			$home_in_target_host = ( false !== strpos( $home_host, $target_host ) );
			$target_in_home_host = ( false !== strpos( $target_host, $home_host ) );

			if ( ! $home_in_target_host && ! $target_in_home_host ) {

				// link is not in campaign => further checks
				if ( false === strpos( $campaign->post_content, $target ) ) {

					$placeholder = mailster( 'placeholder' );
					$placeholder->set_campaign( $campaign_id );
					$placeholder->set_hash( $subscriber->hash );

					$proccessed_content = mailster()->sanitize_content( $campaign->post_content, $meta['head'] );

					$placeholder->add_defaults( $campaign_id );
					$placeholder->set_content( $proccessed_content );

					$placeholder->set_subscriber( $subscriber->ID );

					// add subscriber info
					$placeholder->add( (array) $subscriber );

					// add subscriber specific tags
					if ( $subscriber_tags = mailster( 'subscribers' )->meta( $subscriber->ID, 'tags', $campaign_id ) ) {
						$placeholder->add( (array) $subscriber_tags );
					}

					$proccessed_content = $placeholder->get_content();

					// check if in all links is at least one from the target host => should be save
					if ( preg_match_all( '# href=(\'|")?(https?[^\'"]+)(\'|")?#', $proccessed_content, $all_links ) && preg_grep( '/https?:\/\/' . preg_quote( $target_host ) . '/', array_unique( $all_links[2] ) ) ) {
					} else {
						wp_die( sprintf( esc_html__( '%s is not a valid URL!', 'mailster' ), '<code>&quot;' . urldecode( $target ) . '&quot;</code>' ) );
					}
				}
			}

			$redirect_to = $target;
			$this->setcookie( $subscriber->hash );

			// append hash and campaign_id if unsubscribe link
			if ( mailster()->get_unsubscribe_link( $campaign_id, $hash ) == $redirect_to ) :
				$redirect_to = $this->get_link( 'unsubscribe', $subscriber->hash, get_query_var( '_mailster' ) );
				$target      = $this->get_link( 'unsubscribe' );

			elseif ( mailster()->get_profile_link( $campaign_id, $hash ) == $redirect_to ) :
				$redirect_to = $this->get_link( 'profile', md5( wp_create_nonce( 'mailster_nonce' ) . $subscriber->hash ), get_query_var( '_mailster' ) );
				$target      = $this->get_link( 'profile' );

			endif;

			if ( $subscriber->ID && $meta['track_clicks'] ) {
				do_action( 'mailster_click', $subscriber->ID, $campaign_id, $target, $index );
			}
		} else {

			if ( $subscriber->ID && $meta['track_opens'] ) {
				do_action( 'mailster_open', $subscriber->ID, $campaign_id );
			}
		}

		if ( ! $redirect_to ) {
			$redirect_to = $target ? apply_filters( 'mymail_click_target', apply_filters( 'mailster_click_target', $target, $campaign_id, $subscriber->ID ), $campaign_id, $subscriber->ID ) : false;
		}

		// no target => tracking image
		if ( ! $redirect_to ) {

			nocache_headers();
			header( 'Content-type: image/gif' );
			// The transparent, beacon image
			echo chr( 71 ) . chr( 73 ) . chr( 70 ) . chr( 56 ) . chr( 57 ) . chr( 97 ) . chr( 1 ) . chr( 0 ) . chr( 1 ) . chr( 0 ) . chr( 128 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 33 ) . chr( 249 ) . chr( 4 ) . chr( 1 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 44 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 1 ) . chr( 0 ) . chr( 1 ) . chr( 0 ) . chr( 0 ) . chr( 2 ) . chr( 2 ) . chr( 68 ) . chr( 1 ) . chr( 0 ) . chr( 59 );

		} else {
			// redirect in any case with 307 (temporary moved) to force tracking
			$to = apply_filters( 'mymail_redirect_to', apply_filters( 'mailster_redirect_to', $redirect_to, $campaign_id, $subscriber->ID ), $campaign_id, $subscriber->ID );
			$to = str_replace( '&amp;', '&', $to );
			header( 'Location: ' . $to, true, 307 );
		}

		exit;
	}


	private function do_homepage() {

		global $wp;

		// remove this filter as it's cause redirection to homepage in WP 4.5
		if ( is_front_page() ) {
			remove_action( 'template_redirect', 'redirect_canonical' );
		}

		switch ( get_query_var( '_mailster_page' ) ) {

			case 'subscribe':
				do_action( 'mailster_homepage_subscribe' );

				break;

			case 'unsubscribe':
				// handle one click unsubscribe for RFC8058 (https://tools.ietf.org/html/rfc8058)
				if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

					$hash        = get_query_var( '_mailster_hash' );
					$campaign_id = get_query_var( '_mailster_extra' );
					$status      = 'list_unsubscribe';

					if ( mailster( 'subscribers' )->unsubscribe_by_hash( $hash, $campaign_id, $status ) ) {
						status_header( 200 );
					} else {
						status_header( 404 );
					}
					nocache_headers();
					exit;

				}

				$unsubscribe_url = $this->get_link( 'unsubscribe', get_query_var( '_mailster_hash' ), get_query_var( '_mailster_extra' ) );

				// if tracking is disabled
				if ( strpos( $unsubscribe_url, $wp->request ) === false ) {
					$this->setcookie( get_query_var( '_mailster_hash' ) );
					$redirect_to = $this->get_link( 'unsubscribe', get_query_var( '_mailster_hash' ), get_query_var( '_mailster_extra' ) );
					wp_redirect( $redirect_to, 301 );
					exit;
				}

				do_action( 'mailster_homepage_unsubscribe' );

				break;

			case 'profile':
				$profile_url = $this->get_link( 'profile', get_query_var( '_mailster_hash' ), get_query_var( '_mailster_extra' ) );

				// if tracking is disabled
				if ( strpos( $profile_url, $wp->request ) === false ) {
					$this->setcookie( get_query_var( '_mailster_hash' ) );
					$redirect_to = $this->get_link( 'profile', md5( wp_create_nonce( 'mailster_nonce' ) . get_query_var( '_mailster_hash' ) ), get_query_var( '_mailster_extra' ) );
					wp_redirect( $redirect_to, 301 );
					exit;
				}

				do_action( 'mailster_homepage_profile' );
				$hash = get_query_var( '_mailster_hash' );

				// redirect if no hash is set
				if ( empty( $hash ) ) {

					if ( is_user_logged_in() ) {
						if ( $subscriber = mailster( 'subscribers' )->get_by_wpid( get_current_user_id() ) ) {
							$hash = $subscriber->hash;
							set_query_var( '_mailster_hash', $subscriber->hash );
						}
					}

					if ( empty( $hash ) ) {

						wp_redirect( $this->get_link(), 301 );
						exit;
					}
				}

				break;

			case 'confirm':
				do_action( 'mailster_homepage_confirm' );

				$subscriber = mailster( 'subscribers' )->get_by_hash( get_query_var( '_mailster_hash' ) );
				// redirect if no such subscriber
				if ( ! $subscriber ) {

					wp_redirect( $this->get_link(), 301 );
					exit;
				}

				$extra = explode( '/', get_query_var( '_mailster_extra' ) );
				if ( isset( $extra[0] ) ) {
					$form_id = array_shift( $extra );
				} else {
					$form_id = mailster( 'subscribers' )->meta( $subscriber->ID, 'form' );
				}

				if ( ! $form_id ) {
					$form = mailster( 'forms' )->get( null, false, true );
					$form = $form[0];
				} else {
					$form = mailster( 'forms' )->get( $form_id, false, true );
				}

				if ( isset( $extra[0] ) ) {
					$list_ids = $extra;
				} else {
					// confirm all lists
					$list_ids = null;
				}

				$target = ! empty( $form->confirmredirect ) ? $form->confirmredirect : $this->get_link( 'subscribe', $subscriber->hash, true );

				// subscriber no "pending" anymore
				if ( 0 == $subscriber->status ) {

					$ip        = mailster_option( 'track_users' ) ? mailster_get_ip() : null;
					$user_meta = array(
						'ID'         => $subscriber->ID,
						'confirm'    => time(),
						'status'     => 1,
						'ip_confirm' => $ip,
						'ip'         => $ip,
						'lang'       => mailster_get_lang(),
					);

					if ( 'unknown' !== ( $geo = mailster_ip2City() ) ) {

						$user_meta['geo'] = $geo->country_code . '|' . $geo->city;
						if ( $geo->city ) {
							$user_meta['coords'] = (float) $geo->latitude . ',' . (float) $geo->longitude;
						}
					}

					if ( $subscriber_id = mailster( 'subscribers' )->update( $user_meta, true, false, true ) ) {

						if ( ! is_wp_error( $subscriber_id ) ) {
							do_action( 'mailster_subscriber_subscribed', $subscriber->ID );
							// old hook for backward compatibility
						}
					} else {

						wp_redirect( $this->get_link(), 301 );
						exit;
					}
				}

				mailster( 'lists' )->confirm_subscribers( $list_ids, $subscriber->ID );

				$redirect_to = apply_filters( 'mymail_confirm_target', apply_filters( 'mailster_confirm_target', $target, $subscriber->ID ), $subscriber->ID );

				wp_redirect( $redirect_to, 301 );
				exit;
			break;

		}

	}


	/**
	 *
	 *
	 * @param unknown $post_id
	 * @return unknown
	 */
	public function add_filter_status_to_publish( $post_id ) {

		add_filter( 'get_post_status', array( &$this, 'status_to_publish' ), 10, 2 );

		return $post_id;

	}


	/**
	 *
	 *
	 * @param unknown $post_status
	 * @param unknown $post
	 * @return unknown
	 */
	public function status_to_publish( $post_status, $post ) {

		if ( 'newsletter' == $post->post_type && in_array( $post_status, array( 'finished', 'paused', 'queued' ) ) ) {
			return 'publish';
		}

		return $post_status;

	}


	/**
	 *
	 *
	 * @param unknown $output
	 * @return unknown
	 */
	public function the_excerpt_embed( $output ) {

		global $post;

		if ( ! $post ) {
			return $output;
		}

		return mailster( 'campaigns' )->get_excerpt( $post->ID );
	}


	private function do_frontpage() {

		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				$meta = mailster( 'campaigns' )->meta( get_the_ID() );

				if ( ! $meta['webversion'] && get_current_user_id() != get_the_author_meta( 'ID' ) ) {
					$this->do_404();
				}

				if ( ! mailster_option( 'webversion_bar' ) || ( isset( $_GET['frame'] ) && $_GET['frame'] == '0' ) ) {

					do_action( 'mailster_frontpage' );

					// remove oembed
					if ( isset( $GLOBALS['wp_embed'] ) ) {
						remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
						remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
					}

					if ( post_password_required() ) {

						global $post;

						// unlock post if pwd hash is provided
						if ( isset( $_GET['pwd'] ) && $_GET['pwd'] == md5( $post->post_password . AUTH_KEY ) ) {
							require_once ABSPATH . WPINC . '/class-phpass.php';
							$hasher                                 = new PasswordHash( 8, true );
							$pwd                                    = $hasher->HashPassword( wp_unslash( $post->post_password ) );
							$_COOKIE[ 'wp-postpass_' . COOKIEHASH ] = $pwd;
						}
					}

					$content = get_the_content();

					if ( post_password_required() ) {
						wp_die( $content );
					}

					if ( ! $content ) {
						wp_die( esc_html__( 'There is no content for this newsletter.', 'mailster' ) . ( current_user_can( 'edit_newsletters' ) ? ' <a href="' . admin_url( 'post.php?post=' . get_the_ID() . '&action=edit' ) . '">' . esc_html__( 'Add content', 'mailster' ) . '</a>' : '' ) );
					}

					$content = mailster()->sanitize_content( $content, $meta['head'] );

					$placeholder = mailster( 'placeholder', $content );
					$placeholder->excerpt_filters( false );
					$placeholder->set_campaign( get_the_ID() );

					if ( mailster_option( 'tags_webversion' ) && $subscriber = mailster( 'subscribers' )->get_current_user() ) {
						$userdata = mailster( 'subscribers' )->get_custom_fields( $subscriber->ID );

						$placeholder->set_subscriber( $subscriber->ID );
						$placeholder->add( $userdata );

						$placeholder->add(
							array(
								'firstname' => $subscriber->firstname,
								'lastname'  => $subscriber->lastname,
								'fullname'  => $subscriber->fullname,
							)
						);
					}

					$placeholder->add_defaults( get_the_ID() );
					$placeholder->add_custom( get_the_ID() );

					$content = $placeholder->get_content();
					$content = mailster( 'helper' )->strip_structure_html( $content );
					$search  = array( '<a ', '@media only screen and (max-device-width:' );
					$replace = array( '<a target="_top" ', '@media only screen and (max-width:' );
					$content = str_replace( $search, $replace, $content );

					if ( mailster_option( 'frontpage_public' ) || ! get_option( 'blog_public' ) ) {
						$content = str_replace( '</head>', "<meta name='robots' content='noindex,nofollow' />\n</head>", $content );
					}
					$content = mailster( 'helper' )->add_mailster_styles( $content );

					echo $content;

					exit;

				} else {

					add_filter( 'get_previous_post_where', array( &$this, 'get_post_where' ) );
					add_filter( 'get_previous_post_join', array( &$this, 'get_post_join' ) );
					add_filter( 'get_next_post_where', array( &$this, 'get_post_where' ) );
					add_filter( 'get_next_post_join', array( &$this, 'get_post_join' ) );

					do_action( 'mailster_frontpage_frame' );

					$url = add_query_arg( 'frame', 0, get_permalink() );

					if ( $preview = get_query_var( 'preview' ) ) {
						$url = add_query_arg( 'preview', 1, $url );
					}

					$social_services = mailster( 'helper' )->social_services();

					if ( ! $custom = locate_template( 'single-newsletter.php' ) ) {

						include MAILSTER_DIR . 'views/single-newsletter.php';

					} else {

						include $custom;

					}

					exit;
				}

		endwhile;

			else :

				wp_old_slug_redirect();

				// NOT FOUND
				$this->do_404();

		endif;

			// Reset Post Data
			wp_reset_postdata();

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $width       (optional)
	 * @param unknown $height      (optional)
	 * @return unknown
	 */
	public function content_as_iframe( $campaign_id = null, $width = 610, $height = null ) {

		global $post;

		if ( is_integer( $campaign_id ) ) {
			$campaign = mailster( 'campaigns' )->get( $campaign_id );
		} else {
			$campaign = $post;
		}
		if ( ! isset( $campaign ) || ( isset( $campaign ) && $campaign->post_type != 'newsletter' ) ) {
			return '';
		}

		switch ( current_filter() ) {
			case 'the_excerpt':
			case 'get_the_excerpt':
				remove_filter( 'get_the_content', array( &$this, 'content_as_iframe' ), -1 );
				add_filter( 'get_the_content', '__return_empty_string', -1 );
				remove_filter( 'the_content', array( &$this, 'content_as_iframe' ), -1 );
				add_filter( 'the_content', '__return_empty_string', -1 );
				break;
			case 'the_content':
			case 'get_the_content':
				remove_filter( 'get_the_excerpt', array( &$this, 'content_as_iframe' ), -1 );
				add_filter( 'get_the_excerpt', '__return_empty_string', -1 );
				remove_filter( 'the_excerpt', array( &$this, 'content_as_iframe' ), -1 );
				add_filter( 'the_excerpt', '__return_empty_string', -1 );
				break;
		}

		return '<iframe class="mailster-frame mailster-frame-' . $campaign->ID . '" src="' . add_query_arg( 'frame', 0, get_permalink( $campaign->ID ) ) . '" style="width:' . $width . 'px;' . ( $height ? 'height=' . (int) $height . 'px;' : '' ) . '" width="' . apply_filters( 'mymail_iframe_width', apply_filters( 'mailster_iframe_width', '100%' ) ) . '" scrolling="auto" frameborder="0" onload="this.height=this.contentWindow.document.body.scrollHeight+20;" data-no-lazy=""></iframe>';

	}


	/**
	 *
	 *
	 * @param unknown $query
	 */
	public function filter_status_on_archive( $query ) {
		if ( is_admin() ) {
			return;
		}

		if ( $query->is_main_query() && $query->is_post_type_archive( 'newsletter' ) ) {
			$query->set( 'post_status', mailster_option( 'archive_types', array( 'finished', 'active' ) ) );
			$query->set( 'meta_key', '_mailster_webversion' );
			$query->set( 'meta_compare', 'NOT EXISTS' );
		}

	}


	public function styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mailster-frontpage-style', MAILSTER_URI . 'assets/css/frontpage' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_print_styles( 'mailster-frontpage-style' );

	}


	public function scripts() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'mailster-frontpage-script', MAILSTER_URI . 'assets/js/frontpage' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );
		wp_localize_script( 'mailster-frontpage-script', 'ajaxurl', admin_url( 'admin-ajax.php' ) );

		wp_print_scripts( 'mailster-frontpage-script' );

	}


	/**
	 *
	 *
	 * @param unknown $sql
	 * @return unknown
	 */
	public function get_post_where( $sql ) {
		return str_replace( "post_status = 'publish'", "post_status IN ('finished', 'active' ,'queued') AND post_password = '' AND (pmeta.meta_value = 1 OR pmeta.meta_key IS NULL OR p.post_author = " . get_current_user_id() . ')', $sql );
	}


	/**
	 *
	 *
	 * @param unknown $sql
	 * @return unknown
	 */
	public function get_post_join( $sql ) {
		global $wpdb;
		return $sql .= " LEFT JOIN $wpdb->postmeta as pmeta ON pmeta.post_id = p.ID AND pmeta.meta_key = '_mailster_webversion'";
	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @return unknown
	 */
	public function get_page_by_slug( $slug ) {

		$slugs = mailster_option( 'slugs' );

		$return = is_array( $slugs ) ? array_search( $slug, $slugs ) : $slug;

		if ( empty( $return ) ) {
			$return = isset( $slugs[ $slug ] ) ? $slugs[ $slug ] : $slug;
		}

		return $return;
	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	public function do_shortcode( $atts, $content ) {

		$content = get_the_content();

		// signup form
		if ( ! get_query_var( '_mailster_page' ) ) {

			$pattern = '\[(\[?)(newsletter_signup)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';

			if ( preg_match( '/' . $pattern . '/s', $content, $matches ) ) {
				return do_shortcode( wpautop( $matches[5] ) );
			}

			return '';

		}

		switch ( get_query_var( '_mailster_page' ) ) {

			case 'confirm':
				break;

			case 'subscribe':
				if ( $hash = get_query_var( '_mailster_hash' ) ) {
					$subscriber = mailster( 'subscribers' )->get_by_hash( $hash );

					if ( $subscriber->status != 1 ) {

						return mailster_text( 'unsubscribeerror' );

					}
				}

				$pattern = '\[(\[?)(newsletter_confirm)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';

				preg_match( '/' . $pattern . '/s', $content, $matches );

				return ! empty( $matches[5] ) ? do_shortcode( $matches[5] ) : mailster_text( 'success' );

			break;

			case 'profile':
				$form = mailster( 'form' )->id( mailster_option( 'profile_form', 1 ) );
				$form->is_profile();

				return $form->render( false );

			break;

			case 'unsubscribe':
				$pattern = '\[(\[?)(newsletter_unsubscribe)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
				$return  = '';
				if ( preg_match( '/' . $pattern . '/s', $content, $matches ) ) {
					$return .= do_shortcode( wpautop( $matches[5] ) );
				}

				if ( preg_match( '/\[newsletter_signup_form id=("|\')?(\d+)("|\')?\]/i', $content, $form_id ) ) {
					$form_id = (int) $form_id[2];
				} else {
					global $wpdb;
					$form_id = mailster( 'helper' )->get_first_form_id();
				}

				$form = mailster( 'form' )->id( $form_id );
				$form->is_unsubscribe();
				$form->campaign_id( get_query_var( '_mailster', get_query_var( '_mailster_extra' ) ) );

				$return .= $form->render( false );

				return $return;

			break;

			default:
				return do_shortcode( $content );

		}

	}


	/**
	 *
	 *
	 * @param unknown $hash
	 * @return unknown
	 */
	private function setcookie( $hash, $timeout = 3600 ) {

		$cookietime = apply_filters( 'mailster_cookie_time', $timeout );

		if ( $cookietime ) {
			return setcookie( 'mailster', $hash, time() + $cookietime, COOKIEPATH, COOKIE_DOMAIN );
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	public function newsletter( $atts, $content ) {

		if ( ! isset( $atts['id'] ) || ( ! is_single() && ! is_page() ) ) {
			return false;
		}

		$link = get_permalink( $atts['id'] );

		if ( ! $link ) {
			return '';
		}

		extract(
			shortcode_atts(
				array(
					'scrolling' => true,
				),
				$atts
			)
		);

		return '<iframe class="mailster_frame" src="' . add_query_arg( 'frame', 0, $link ) . '" style="min-width:610px;" width="' . apply_filters( 'mymail_iframe_width', apply_filters( 'mailster_iframe_width', '100%' ) . '" scrolling="' . ( $scrolling ? 'auto' : 'no' ) ) . '" frameborder="0" onload="this.height=this.contentWindow.document.body.scrollHeight+20;" data-no-lazy=""></iframe>';

	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	public function newsletter_list( $atts, $content ) {
		extract(
			shortcode_atts(
				array(
					'date'    => false,
					'count'   => 10,
					'status'  => array( 'finished', 'active' ),
					'order'   => 'desc',
					'orderby' => 'date',
				),
				$atts
			)
		);

		$r = new WP_Query(
			array(
				'post_type'           => 'newsletter',
				'posts_per_page'      => $count,
				'no_found_rows'       => true,
				'post_status'         => $status,
				'ignore_sticky_posts' => true,
				'order'               => $order,
				'orderby'             => $orderby,
			)
		);

		$return = '';

		if ( $r->have_posts() ) :

			$return .= '<ul class="mailster-newsletter-list">';
			while ( $r->have_posts() ) :
				$r->the_post();
				$title   = get_the_title();
				$return .= '<li><a href="' . get_permalink() . '" title="' . esc_attr( $title ) . '">' . $title . '</a>';
				if ( $date ) {
					$return .= ' <span class="mailster-newsletter-date">' . get_the_date() . '</span>';
				}

				$return .= '</li>';
		endwhile;
			$return .= '</ul>';

			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();

		endif;

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @return unknown
	 */
	public function newsletter_subscribers( $atts ) {
		extract(
			shortcode_atts(
				array(
					'formatted' => true,
					'round'     => 1,
					'lists'     => null,
				),
				$atts
			)
		);

		$round = max( 1, $round );

		if ( ! is_null( $lists ) ) {
			$lists       = explode( ',', (string) $lists );
			$subscribers = mailster( 'lists' )->count( $lists, 1 );
		} else {
			$subscribers = mailster( 'subscribers' )->get_count_by_status( 1 );
		}

		$subscribers = ceil( $subscribers / $round ) * $round;
		if ( $formatted ) {
			$subscribers = number_format_i18n( $subscribers );
		}

		return $subscribers;
	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	public function newsletter_signup( $atts, $content ) {
		return do_shortcode( $content );
	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	public function newsletter_signup_form( $atts, $content ) {

		if ( ! isset( $atts['id'] ) ) {
			$atts['id'] = mailster( 'helper' )->get_first_form_id();
		}

		$form = mailster( 'form' )->id( (int) $atts['id'], $atts );
		if ( isset( $atts['profile'] ) && $atts['profile'] ) {
			$form->is_profile();
		}
		return $form->render( false );
	}

	/**
	 *
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	public function do_shortcode_profile( $atts, $content ) {

		// not on the newsletter homepage
		if ( is_mailster_newsletter_homepage() ) {
			return;
		}

		$atts = wp_parse_args( $atts, array( 'id' => mailster_option( 'profile_form', 1 ) ) );

		$form = mailster( 'form' )->id( (int) $atts['id'], $atts );
		$form->is_profile();

		return $form->render( false );

	}
	/**
	 *
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	public function do_shortcode_unsubscribe( $atts, $content ) {

		// not on the newsletter homepage
		if ( is_mailster_newsletter_homepage() ) {
			return;
		}

		$atts = wp_parse_args(
			$atts,
			array(
				'id' => mailster( 'helper' )->get_first_form_id(),
			)
		);

		$form = mailster( 'form' )->id( (int) $atts['id'], $atts );
		$form->is_unsubscribe();
		$form->campaign_id( get_query_var( '_mailster', get_query_var( '_mailster_extra' ) ) );

		return $form->render( false );
	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	public function do_shortcode_wrong_confirm( $atts, $content ) {

		return $this->do_shortcode_wrong( 'newsletter_confirm', $atts, $content );

	}


	/**
	 *
	 *
	 * @param unknown $shorttcode
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	private function do_shortcode_wrong( $shorttcode, $atts, $content ) {

		if ( ! is_mailster_newsletter_homepage() && is_user_logged_in() ) {
			$msg = sprintf( esc_html__( 'You should use the shortcode %s only on the newsletter homepage!', 'mailster' ), "[$shorttcode]" );
			_doing_it_wrong( "[$shorttcode]", $msg, '2.1.5' );
			return '<p>' . $msg . '</p>';
		}
		return;
	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 * @return unknown
	 */
	public function newsletter_button( $atts, $content ) {

		$args = shortcode_atts(
			array(
				'id'        => 1,
				'showcount' => false,
				'label'     => mailster_text( 'submitbutton' ),
				'design'    => 'default',
				'width'     => 480,
			),
			$atts
		);

		return mailster( 'forms' )->get_subscribe_button( $args['id'], $args );

	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @return unknown
	 */
	public function shortcode_empty_paragraph_fix( $content ) {

		// array of custom shortcodes requiring the fix
		$block = join( '|', array( 'newsletter', 'newsletter_signup', 'newsletter_signup_form', 'newsletter_confirm', 'newsletter_unsubscribe', 'newsletter_subscribers', 'newsletter_subscribe' ) );

		// opening tag
		$rep = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content );

		// closing tag
		$rep = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", '[/$2]', $rep );

		return $rep;

	}

	private function do_404() {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
		get_template_part( 404 );
		exit;
	}


}
