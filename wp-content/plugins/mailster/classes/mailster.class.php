<?php

class Mailster {

	private $template;
	private $post_data;
	private $campaign_data;
	private $mail   = array();
	private $tables = array( 'actions', 'forms', 'forms_lists', 'form_fields', 'links', 'lists', 'lists_subscribers', 'queue', 'subscribers', 'subscriber_fields', 'subscriber_meta' );

	public $wp_mail = null;

	private $_classes = array();

	static $form_active;

	public function __construct() {

		register_activation_hook( MAILSTER_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook( MAILSTER_FILE, array( &$this, 'deactivate' ) );

		require_once MAILSTER_DIR . 'classes/settings.class.php';
		require_once MAILSTER_DIR . 'classes/translations.class.php';
		require_once MAILSTER_DIR . 'classes/campaigns.class.php';
		require_once MAILSTER_DIR . 'classes/subscribers.class.php';
		require_once MAILSTER_DIR . 'classes/lists.class.php';
		require_once MAILSTER_DIR . 'classes/forms.class.php';
		require_once MAILSTER_DIR . 'classes/manage.class.php';
		require_once MAILSTER_DIR . 'classes/templates.class.php';
		require_once MAILSTER_DIR . 'classes/widget.class.php';
		require_once MAILSTER_DIR . 'classes/frontpage.class.php';
		require_once MAILSTER_DIR . 'classes/statistics.class.php';
		require_once MAILSTER_DIR . 'classes/ajax.class.php';
		require_once MAILSTER_DIR . 'classes/tinymce.class.php';
		require_once MAILSTER_DIR . 'classes/cron.class.php';
		require_once MAILSTER_DIR . 'classes/queue.class.php';
		require_once MAILSTER_DIR . 'classes/actions.class.php';
		require_once MAILSTER_DIR . 'classes/bounce.class.php';
		require_once MAILSTER_DIR . 'classes/dashboard.class.php';
		require_once MAILSTER_DIR . 'classes/update.class.php';
		require_once MAILSTER_DIR . 'classes/upgrade.class.php';
		require_once MAILSTER_DIR . 'classes/helpmenu.class.php';
		require_once MAILSTER_DIR . 'classes/register.class.php';
		require_once MAILSTER_DIR . 'classes/geo.class.php';
		require_once MAILSTER_DIR . 'classes/privacy.class.php';
		require_once MAILSTER_DIR . 'classes/export.class.php';
		require_once MAILSTER_DIR . 'classes/empty.class.php';

		$this->_classes = array(
			'settings'     => new MailsterSettings(),
			'translations' => new MailsterTranslations(),
			'campaigns'    => new MailsterCampaigns(),
			'subscribers'  => new MailsterSubscribers(),
			'lists'        => new MailsterLists(),
			'forms'        => new MailsterForms(),
			'manage'       => new MailsterManage(),
			'templates'    => new MailsterTemplates(),
			'frontpage'    => new MailsterFrontpage(),
			'statistics'   => new MailsterStatistics(),
			'ajax'         => new MailsterAjax(),
			'tinymce'      => new MailsterTinymce(),
			'cron'         => new MailsterCron(),
			'queue'        => new MailsterQueue(),
			'actions'      => new MailsterActions(),
			'bounce'       => new MailsterBounce(),
			'dashboard'    => new MailsterDashboard(),
			'update'       => new MailsterUpdate(),
			'upgrade'      => new MailsterUpgrade(),
			'helpmenu'     => new MailsterHelpmenu(),
			'register'     => new MailsterRegister(),
			'geo'          => new MailsterGeo(),
			'privacy'      => new MailsterPrivacy(),
			'export'       => new MailsterExport(),
			'empty'        => new MailsterEmpty(),
		);

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );
		add_action( 'widgets_init', array( &$this, 'register_widgets' ), 1 );

		$this->wp_mail = function_exists( 'wp_mail' );

	}


	/**
	 *
	 *
	 * @param unknown $class
	 * @param unknown $args
	 * @return unknown
	 */
	public function __call( $class, $args ) {

		if ( ! isset( $this->_classes[ $class ] ) ) {
			if ( WP_DEBUG ) {
				throw new Exception( "Class $class doesn't exists", 1 );
			} else {
				$class = 'empty';
			}
		}

		return $this->_classes[ $class ];
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function stats() {
		return $this->statistics();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function mail() {
		require_once MAILSTER_DIR . 'classes/mail.class.php';

		return MailsterMail::get_instance();
	}


	/**
	 *
	 *
	 * @param unknown $content (optional)
	 * @return unknown
	 */
	public function placeholder( $content = '' ) {
		require_once MAILSTER_DIR . 'classes/placeholder.class.php';

		return new MailsterPlaceholder( $content );
	}


	/**
	 *
	 *
	 * @param unknown $content (optional)
	 * @return unknown
	 */
	public function conditions( $conditions = array() ) {
		require_once MAILSTER_DIR . 'classes/conditions.class.php';

		return new MailsterConditions( $conditions );
	}


	/**
	 *
	 *
	 * @param unknown $file     (optional)
	 * @param unknown $template (optional)
	 * @return unknown
	 */
	public function notification( $file = null, $template = null ) {
		if ( is_null( $template ) ) {
			$template = 'basic';
		}
		if ( is_null( $file ) ) {
			$file = mailster_option( 'system_mail_template', 'notification.html' );
		}

		require_once MAILSTER_DIR . 'classes/notification.class.php';
		return MailsterNotification::get_instance( $template, $file );
	}


	/**
	 *
	 *
	 * @param unknown $test
	 * @return unknown
	 */
	public function test( $test = null ) {
		require_once MAILSTER_DIR . 'classes/tests.class.php';

		$testobj = new MailsterTests();
		if ( is_null( $test ) ) {
			return $testobj;
		}
		$testobj->run( $test );
		return $testobj->get();

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @param unknown $file (optional)
	 * @return unknown
	 */
	public function template( $slug = null, $file = null ) {

		if ( is_null( $slug ) ) {
			$slug = mailster_option( 'default_template', 'mailster' );
		}
		$file = is_null( $file ) ? 'index.html' : $file;
		require_once MAILSTER_DIR . 'classes/template.class.php';

		$template = new MailsterTemplate( $slug, $file );

		return $template;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function form() {

		require_once MAILSTER_DIR . 'classes/form.class.php';
		return new MailsterForm();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function helper() {

		require_once MAILSTER_DIR . 'classes/helper.class.php';
		return new MailsterHelper();
	}

	public function is( $page ) {

		$screen = get_current_screen();

		return $screen && 'admin_page_mailster_' . $page == $screen->id;

	}



	public function init() {

		// remove revisions if newsletter is finished
		add_action( 'mailster_reset_mail', array( &$this, 'reset_mail_delayed' ), 10, 3 );

		add_action( 'mailster_cron', array( &$this, 'check_homepage' ) );
		add_action( 'mailster_cron', array( &$this, 'check_compatibility' ) );

		add_action( 'mailster_update', array( &$this, 'remove_support_accounts' ) );

		$this->wp_mail_setup();

		if ( is_admin() ) {

			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts_styles' ), 10, 1 );
			add_action( 'admin_print_scripts', array( &$this, 'localize_scripts' ), 10, 1 );
			add_action( 'admin_menu', array( &$this, 'special_pages' ), 60 );
			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );

			add_filter( 'plugin_action_links', array( &$this, 'add_action_link' ), 10, 2 );
			add_filter( 'plugin_row_meta', array( &$this, 'add_plugin_links' ), 10, 2 );

			add_filter( 'install_plugin_complete_actions', array( &$this, 'add_install_plugin_complete_actions' ), 10, 3 );

			add_filter( 'add_meta_boxes_page', array( &$this, 'add_homepage_info' ), 10, 2 );

			add_filter( 'display_post_states', array( &$this, 'display_post_states' ), 10, 2 );

			add_filter( 'admin_page_access_denied', array( &$this, 'maybe_redirect_special_pages' ) );

			add_action( 'load-plugins.php', array( &$this, 'deactivation_survey' ) );

		} else {

		}

		do_action( 'mailster', $this );

	}


	public function register_widgets() {

		register_widget( 'Mailster_Signup_Widget' );
		register_widget( 'Mailster_Newsletter_List_Widget' );
		register_widget( 'Mailster_Newsletter_Subscriber_Button_Widget' );
		register_widget( 'Mailster_Newsletter_Subscribers_Count_Widget' );

	}


	public function save_admin_notices() {

		global $mailster_notices;

		update_option( 'mailster_notices', empty( $mailster_notices ) ? null : (array) $mailster_notices );

	}


	public function admin_notices() {

		global $mailster_notices;

		if ( ! ( $mailster_notices = get_option( 'mailster_notices' ) ) ) {
			return;
		}

		$successes = array();
		$errors    = array();
		$infos     = array();
		$warnings  = array();
		$dismiss   = isset( $_GET['mailster_remove_notice_all'] ) ? esc_attr( $_GET['mailster_remove_notice_all'] ) : false;

		if ( ! is_array( $mailster_notices ) ) {
			$mailster_notices = array();
		}

		if ( isset( $_GET['mailster_remove_notice'] ) && isset( $mailster_notices[ $_GET['mailster_remove_notice'] ] ) ) {
			unset( $mailster_notices[ $_GET['mailster_remove_notice'] ] );
		}

		$notices = array_reverse( $mailster_notices, true );

		foreach ( $notices as $id => $notice ) {

			if ( isset( $notice['cap'] ) && ! empty( $notice['cap'] ) ) {

				// specific users or admin
				if ( is_numeric( $notice['cap'] ) ) {
					if ( get_current_user_id() != $notice['cap'] && ! current_user_can( 'manage_options' ) ) {
						continue;
					}

					// certain capability
				} else {
					if ( ! current_user_can( $notice['cap'] ) ) {
						continue;
					}
				}
			}
			if ( isset( $notice['screen'] ) && ! empty( $notice['screen'] ) ) {
				$screen = get_current_screen();
				if ( ! in_array( $screen->id, (array) $notice['screen'] ) ) {
					continue;
				}
			}

			$type        = esc_attr( $notice['type'] );
			$dismissable = ! $notice['once'] || is_numeric( $notice['once'] );

			$classes = array( 'hidden', 'notice', 'mailster-notice', 'notice-' . $type );
			if ( 'success' == $type ) {
				$classes[] = 'updated';
			}
			if ( 'error' == $type ) {
				$classes[] = 'error';
			}
			if ( $dismissable ) {
				$classes[] = 'mailster-notice-dismissable';
			}

			$msg = '<div data-id="' . esc_attr( $id ) . '" id="mailster-notice-' . esc_attr( $id ) . '" class="' . implode( ' ', $classes ) . '">';

			$text = ( isset( $notice['text'] ) ? $notice['text'] : '' );
			$text = isset( $notice['cb'] ) && function_exists( $notice['cb'] )
				? call_user_func( $notice['cb'], $text )
				: $text;

			if ( $text === false ) {
				continue;
			}
			if ( ! is_string( $text ) ) {
				$text = print_r( $text, true );
			}

			if ( 'error' == $type ) {
				$text = '<strong>' . $text . '</strong>';
			}

			$msg .= ( $text ? $text : '&nbsp;' );
			if ( $dismissable ) {
				$msg .= '<a class="notice-dismiss" title="' . esc_attr__( 'Dismiss this notice (Alt-click to dismiss all notices)', 'mailster' ) . '" href="' . add_query_arg( array( 'mailster_remove_notice' => $id ) ) . '">' . esc_attr__( 'Dismiss', 'mailster' ) . '<span class="screen-reader-text">' . esc_attr__( 'Dismiss this notice (Alt-click to dismiss all notices)', 'mailster' ) . '</span></a>';

				$mailster_notices[ $id ]['seen'] = true;
				if ( is_numeric( $notice['once'] ) && (int) $notice['once'] - time() < 0 ) {
					unset( $mailster_notices[ $id ] );
					if ( isset( $notice['seen'] ) ) {
						continue;
					}
				}
			} else {
				unset( $mailster_notices[ $id ] );
			}

			$msg .= '</div>';

			if ( $notice['type'] == 'success' && $dismiss != 'success' ) {
				$successes[] = $msg;
			}

			if ( $notice['type'] == 'error' && $dismiss != 'error' ) {
				$errors[] = $msg;
			}

			if ( $notice['type'] == 'info' && $dismiss != 'info' ) {
				$infos[] = $msg;
			}

			if ( $notice['type'] == 'warning' && $dismiss != 'warning' ) {
				$warnings[] = $msg;
			}

			if ( 'success' == $dismiss && isset( $mailster_notices[ $id ] ) ) {
				unset( $mailster_notices[ $id ] );
			}

			if ( 'error' == $dismiss && isset( $mailster_notices[ $id ] ) ) {
				unset( $mailster_notices[ $id ] );
			}

			if ( 'info' == $dismiss && isset( $mailster_notices[ $id ] ) ) {
				unset( $mailster_notices[ $id ] );
			}

			if ( 'warning' == $dismiss && isset( $mailster_notices[ $id ] ) ) {
				unset( $mailster_notices[ $id ] );
			}
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-notice', MAILSTER_URI . 'assets/css/notice-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-notice', MAILSTER_URI . 'assets/js/notice-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		echo implode( '', $successes );
		echo implode( '', $errors );
		echo implode( '', $infos );
		echo implode( '', $warnings );

		add_action( 'shutdown', array( &$this, 'save_admin_notices' ) );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function maybe_redirect_special_pages() {

		global $pagenow;

		if ( 'edit.php' != $pagenow ) {
			return;
		}
		if ( is_network_admin() ) {
			return;
		}
		if ( ! isset( $_GET['page'] ) ) {
			return;
		}
		$page = $_GET['page'];
		if ( ! in_array( $page, array( 'mailster_update', 'mailster_welcome', 'mailster_setup', 'mailster_tests' ) ) ) {
			return;
		}

		wp_redirect( 'admin.php?page=' . $page, 302 );
		exit;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function get_base_link( $campaign_id = '' ) {

		$is_permalink = mailster( 'helper' )->using_permalinks();

		$prefix = ! mailster_option( 'got_url_rewrite' ) ? '/index.php' : '/';

		return $is_permalink
			? home_url( $prefix . '/mailster/' . $campaign_id )
			: add_query_arg( 'mailster', $campaign_id, home_url( $prefix ) );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @param unknown $hash        (optional)
	 * @return unknown
	 */
	public function get_unsubscribe_link( $campaign_id = '', $hash = '' ) {

		$is_permalink = mailster( 'helper' )->using_permalinks();

		if ( empty( $hash ) ) {

			$prefix = ! mailster_option( 'got_url_rewrite' ) ? '/index.php' : '/';

			$unsubscribe_homepage = get_page( mailster_option( 'homepage' ) );

			if ( $unsubscribe_homepage ) {
				$unsubscribe_homepage = get_permalink( $unsubscribe_homepage );
			} else {
				$unsubscribe_homepage = get_bloginfo( 'url' );
			}

			$slugs = mailster_option( 'slugs' );
			$slug  = trailingslashit( isset( $slugs['unsubscribe'] ) ? $slugs['unsubscribe'] : 'unsubscribe' );

			if ( ! $is_permalink ) {
				$unsubscribe_homepage = str_replace( trailingslashit( get_bloginfo( 'url' ) ), untrailingslashit( get_bloginfo( 'url' ) ) . $prefix, $unsubscribe_homepage );
			}

			$unsubscribe_homepage = apply_filters( 'mymail_unsubscribe_link', apply_filters( 'mailster_unsubscribe_link', $unsubscribe_homepage, $campaign_id ) );

			wp_parse_str( (string) parse_url( $unsubscribe_homepage, PHP_URL_QUERY ), $query_string );

			// remove all query strings
			if ( ! empty( $query_string ) ) {
				$unsubscribe_homepage = remove_query_arg( array_keys( $query_string ), $unsubscribe_homepage );
			}

			$url = $is_permalink
				? trailingslashit( $unsubscribe_homepage ) . $slug
				: add_query_arg( 'mailster_unsubscribe', md5( $campaign_id . '_unsubscribe' ), $unsubscribe_homepage );

			return ! empty( $query_string ) ? add_query_arg( $query_string, $url ) : $url;
		}

		$baselink = get_home_url( null, '/' );

		wp_parse_str( (string) parse_url( $baselink, PHP_URL_QUERY ), $query_string );

		// remove all query strings
		if ( ! empty( $query_string ) ) {
			$baselink = remove_query_arg( array_keys( $query_string ), $baselink );
		}

		$slugs = mailster_option( 'slugs' );
		$slug  = isset( $slugs['unsubscribe'] ) ? $slugs['unsubscribe'] : 'unsubscribe';
		$path  = $slug;
		if ( ! empty( $hash ) ) {
			$path .= '/' . $hash;
		}
		if ( ! empty( $campaign_id ) ) {
			$path .= '/' . $campaign_id;
		}

		$url = $is_permalink
			? trailingslashit( $baselink ) . trailingslashit( 'mailster/' . $path )
			: add_query_arg(
				array(
					'mailster_unsubscribe' => md5( $campaign_id . '_unsubscribe' ),
				),
				$baselink
			);

		return ! empty( $query_string ) ? add_query_arg( $query_string, $url ) : $url;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $email       (optional)
	 * @return unknown
	 */
	public function get_forward_link( $campaign_id, $email = '' ) {

		$page = get_permalink( $campaign_id );

		return add_query_arg( array( 'mailster_forward' => urlencode( $email ) ), $page );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $hash        (optional)
	 * @return unknown
	 */
	public function get_profile_link( $campaign_id, $hash = '' ) {

		$is_permalink = mailster( 'helper' )->using_permalinks();

		if ( empty( $hash ) ) {

			$prefix = ! mailster_option( 'got_url_rewrite' ) ? '/index.php' : '/';

			$homepage = get_page( mailster_option( 'homepage' ) )
				? get_permalink( mailster_option( 'homepage' ) )
				: get_bloginfo( 'url' );

			$slugs = mailster_option( 'slugs' );
			$slug  = trailingslashit( isset( $slugs['profile'] ) ? $slugs['profile'] : 'profile' );

			if ( ! $is_permalink ) {
				$homepage = str_replace( trailingslashit( get_bloginfo( 'url' ) ), untrailingslashit( get_bloginfo( 'url' ) ) . $prefix, $homepage );
			}

			return $is_permalink
				? trailingslashit( $homepage ) . $slug
				: add_query_arg( 'mailster_profile', $hash, $homepage );
		}

		$baselink = get_home_url( null, '/' );
		$slugs    = mailster_option( 'slugs' );
		$slug     = isset( $slugs['profile'] ) ? $slugs['profile'] : 'profile';
		$path     = $slug;
		if ( ! empty( $hash ) ) {
			$path .= '/' . $hash;
		}
		if ( ! empty( $campaign_id ) ) {
			$path .= '/' . $campaign_id;
		}

		$link = ( $is_permalink )
			? trailingslashit( $baselink ) . trailingslashit( 'mailster/' . $path )
			: add_query_arg(
				array(
					'mailster_profile' => $hash,
				),
				$baselink
			);

		return $link;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function check_link_structure() {

		$args = array( 'sslverify' => false );

		// only if permalink structure is used
		if ( mailster( 'helper' )->using_permalinks() ) {

			$hash = str_repeat( '0', 32 );

			$urls = array(
				trailingslashit( $this->get_unsubscribe_link( 0 ) ) . $hash,
				trailingslashit( $this->get_profile_link( 0 ) ) . $hash,
				trailingslashit( $this->get_base_link( 0 ) ) . $hash,
			);

			foreach ( $urls as $url ) {

				$response = wp_remote_get( $url, $args );

				$code = wp_remote_retrieve_response_code( $response );
				if ( $code && $code != 200 ) {
					return false;
				}
			}
		}

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $content     (optional)
	 * @param unknown $hash        (optional)
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function replace_links( $content = '', $hash = '', $campaign_id = '' ) {

		// get all links from the basecontent
		preg_match_all( '# href=(\'|")?(https?[^\'"]+)(\'|")?#', $content, $links );
		$links = $links[2];

		if ( empty( $links ) ) {
			return $content;
		}

		$used = array();

		$new_structure = mailster( 'helper' )->using_permalinks();
		$base          = $this->get_base_link( $campaign_id );

		foreach ( $links as $link ) {

			if ( $new_structure ) {
				$new_link = trailingslashit( $base ) . $hash . '/' . $this->encode_link( $link );

				! isset( $used[ $link ] )
					? $used[ $link ] = 1
					: $new_link     .= '/' . ( $used[ $link ]++ );

			} else {

				$link                = str_replace( array( '%7B', '%7D' ), array( '{', '}' ), $link );
				$target              = $this->encode_link( $link );
				$new_link            = $base . '&k=' . $hash . '&t=' . $target;
				! isset( $used[ $link ] )
					? $used[ $link ] = 1
					: $new_link     .= '&c=' . ( $used[ $link ]++ );

			}

			$link     = ' href="' . $link . '"';
			$new_link = ' href="' . apply_filters( 'mailster_replace_link', $new_link, $base, $hash, $campaign_id ) . '"';

			if ( ( $pos = strpos( $content, $link ) ) !== false ) {
				// do not use substr_replace as it has problems with multibyte
				// $content = substr_replace( $content, $new_link, $pos, strlen( $link ) );
				$content = preg_replace( '/' . preg_quote( $link, '/' ) . '/', $new_link, $content, 1 );

			}
		}

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $link (optional)
	 * @return unknown
	 */
	public function encode_link( $link ) {
		return apply_filters( 'mailster_encode_link', rtrim( strtr( base64_encode( $link ), '+/', '-_' ), '=' ), $link );
	}

	/**
	 *
	 *
	 * @param unknown $decode_link (optional)
	 * @return unknown
	 */
	public function decode_link( $encoded_link ) {
		return apply_filters( 'mailster_decode_link', base64_decode( strtr( $encoded_link, '-_', '+/' ) ), $encoded_link );
	}


	/**
	 *
	 *
	 * @param unknown $identifier    (optional)
	 * @param unknown $post_type     (optional)
	 * @param unknown $term_ids      (optional)
	 * @param unknown $args          (optional)
	 * @param unknown $campaign_id   (optional)
	 * @param unknown $subscriber_id (optional)
	 * @param unknown $try           (optional)
	 * @return unknown
	 */
	public function get_random_post( $identifier = 0, $post_type = 'post', $term_ids = array(), $args = array(), $campaign_id = 0, $subscriber_id = null, $try = 1 ) {

		// filters only on first run.
		if ( 1 === $try ) {
			$args = apply_filters( 'mailster_get_random_post_args', $args, $identifier, $post_type, $term_ids, $campaign_id, $subscriber_id );
			// try max 10 times to prevent infinity loop
		} elseif ( $try >= 10 ) {
			return false;
		}

		// get a seed to bring some randomness.
		$seed = apply_filters( 'mailster_get_random_post_seed', 0 );

		$args['orderby'] = 'RAND(' . ( (int) $seed . (int) $campaign_id . (int) $identifier ) . ')';

		// add an identifier to prevent results from being cached.
		$key                             = md5( serialize( array( $identifier, $post_type, $term_ids, $args, $campaign_id ) ) );
		$args['mailster_identifier']     = $identifier;
		$args['mailster_identifier_key'] = $key;
		// $args['date_query'] = array();

		// check if there's a cached version.
		$posts = mailster_cache_get( 'get_random_post' );

		if ( $posts && isset( $posts[ $campaign_id ] ) && isset( $posts[ $campaign_id ][ $key ] ) ) {
			return $posts[ $campaign_id ][ $key ];
		}

		// get the actual post.
		$post = $this->get_last_post( 0, $post_type, $term_ids, $args, $campaign_id, $subscriber_id );

		if ( ! $post ) {
			return false;
		}

		if ( ! isset( $posts[ $campaign_id ] ) ) {
			$posts[ $campaign_id ] = $stored = array();
		} else {
			$stored = wp_list_pluck( $posts[ $campaign_id ], 'ID' );
		}

		$allow_duplciates = apply_filters( 'mailster_allow_random_post_duplicates', false, $post_type, $term_ids, $args, $campaign_id, $subscriber_id );

		// get new if already used
		if ( ! $allow_duplciates && ( $pos = array_search( $post->ID, $stored ) ) !== false ) {
			unset( $args['mailster_identifier'] );
			unset( $args['mailster_identifier_key'] );
			return $this->get_random_post( ++$identifier, $post_type, $term_ids, $args, $campaign_id, $subscriber_id, ++$try );
		} else {
			$posts[ $campaign_id ][ $key ] = $post;
		}

		mailster_cache_set( 'get_random_post', $posts );

		return $post;

	}


	/**
	 *
	 *
	 * @param unknown $offset        (optional)
	 * @param unknown $post_type     (optional)
	 * @param unknown $term_ids      (optional)
	 * @param unknown $args          (optional)
	 * @param unknown $campaign_id   (optional)
	 * @param unknown $subscriber_id (optional)
	 * @return unknown
	 */
	public function get_last_post( $offset = 0, $post_type = 'post', $term_ids = array(), $args = array(), $campaign_id = null, $subscriber_id = null ) {

		global $wpdb;

		$args = apply_filters( 'mailster_pre_get_last_post_args', $args, $offset, $post_type, $term_ids, $campaign_id, $subscriber_id );
		$key  = md5( serialize( array( $offset, $post_type, $term_ids, $args, $campaign_id, $subscriber_id ) ) );

		$posts = mailster_cache_get( 'get_last_post' );

		if ( $posts && isset( $posts[ $key ] ) ) {
			return $posts[ $key ];
		}

		if ( ! did_action( 'mailster_register_dynamic_post_type' ) ) {
			do_action( 'mailster_register_dynamic_post_type' );
		}

		$post = apply_filters( 'mailster_get_last_post_' . $post_type, null, $args, $offset, $term_ids, $campaign_id, $subscriber_id );

		if ( is_null( $post ) ) {

			if ( 'rss' == $post_type && isset( $args['mailster_rss_url'] ) ) {

				$posts = false;

				$post = mailster( 'helper' )->feed( $args['mailster_rss_url'], absint( $offset ) );

				if ( ! is_wp_error( $post ) && $post ) {
					$posts = array( $post );
				}
			} else {
				$defaults = array(
					'posts_per_page'         => 1,
					'numberposts'            => 1,
					'post_type'              => $post_type,
					'offset'                 => $offset,
					'update_post_meta_cache' => false,
					'no_found_rows'          => true,
					// 'cache_results' => false,
				);

				if ( ! isset( $args['post__not_in'] ) ) {
					$exclude = mailster_cache_get( 'get_last_post_ignore' );

					if ( ! $exclude ) {
						$exclude = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'mailster_ignore' AND meta_value != '0'" );
					}

					if ( ! empty( $exclude ) ) {
						$args['post__not_in'] = (array) $exclude;
					}
				}
				$args = wp_parse_args( $args, $defaults );

				mailster_cache_set( 'get_last_post_ignore', $exclude );

				if ( ! empty( $term_ids ) ) {

					$tax_query = array();

					$taxonomies = get_object_taxonomies( $post_type, 'names' );

					for ( $i = 0; $i < count( $term_ids ); $i++ ) {
						if ( empty( $term_ids[ $i ] ) ) {
							continue;
						}

						$terms = explode( ',', $term_ids[ $i ] );

						$include = array_filter(
							$terms,
							function ( $v ) {
								return $v >= 0;
							}
						);
						$exclude = array_filter(
							$terms,
							function ( $v ) {
								return $v < 0;
							}
						);

						if ( ! empty( $include ) ) {
							$tax_query[] = array(
								'taxonomy' => $taxonomies[ $i ],
								'field'    => 'id',
								'terms'    => $include,
							);
						}
						if ( ! empty( $exclude ) ) {
							$tax_query[] = array(
								'taxonomy' => $taxonomies[ $i ],
								'field'    => 'id',
								'terms'    => $exclude,
								'operator' => 'NOT IN',
							);
						}
					}

					if ( ! empty( $tax_query ) ) {
						$tax_query['relation'] = 'AND';
						$args                  = wp_parse_args( $args, array( 'tax_query' => $tax_query ) );
					}
				}

				$args = apply_filters( 'mailster_get_last_post_args', $args, $offset, $post_type, $term_ids, $campaign_id, $subscriber_id );

				$posts = get_posts( $args );
				if ( is_wp_error( $posts ) ) {
					$post = $posts;
				} elseif ( ! empty( $posts ) ) {
					$post = $posts[0];
				}
			}
		}

		if ( is_wp_error( $post ) ) {

		} elseif ( $post ) {

			if ( ! $post->post_excerpt ) {
				if ( preg_match( '/<!--more(.*?)?-->/', $post->post_content, $matches ) ) {
					$content            = explode( $matches[0], $post->post_content, 2 );
					$post->post_excerpt = trim( $content[0] );
				}
			}

			$post->post_excerpt = mailster( 'helper' )->get_excerpt( ( ! empty( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content ), apply_filters( 'mailster_excerpt_length', null ) );

			$post->post_content = mailster( 'helper' )->handle_shortcodes( $post->post_content );

			$post->post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );

			$post->post_content = apply_filters( 'the_content', $post->post_content );

		} else {
			$post = false;
		}

		if ( ! isset( $args['cache_results'] ) || $args['cache_results'] !== false ) {
			$posts[ $key ] = $post;

			mailster_cache_set( 'get_last_post', $posts );
		}

		return $post;
	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @param unknown $customhead (optional)
	 * @param unknown $preserve_comments (optional)
	 * @return unknown
	 */
	public function sanitize_content( $content, $customhead = null, $preserve_comments = false ) {
		if ( empty( $content ) ) {
			return '';
		}

		if ( function_exists( 'mb_convert_encoding' ) ) {
			$encoding = mb_detect_encoding( $content, 'auto' );
			if ( $encoding != 'UTF-8' ) {
				$content = mb_convert_encoding( $content, $encoding, 'UTF-8' );
			}
		}

		$bodyattributes = '';
		$pre_stuff      = '';
		$protocol       = ( is_ssl() ? 'https' : 'http' );

		preg_match( '#^(.*?)<head([^>]*)>(.*?)<\/head>#is', ( is_null( $customhead ) ? $content : $customhead ), $matches );
		if ( ! empty( $matches ) ) {
			$pre_stuff = $matches[1];
			// remove multiple heads
			if ( substr_count( $pre_stuff, '<!DOCTYPE' ) > 1 ) {
				$pos       = strrpos( $pre_stuff, '<!DOCTYPE' );
				$pre_stuff = substr( $pre_stuff, strrpos( $pre_stuff, '<!DOCTYPE' ) );
			}
			$head = '<head' . $matches[2] . '>' . $matches[3] . '</head>';
		} else {
			$pre_stuff = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n" . '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
			$head      = '<head>' . "\n\t" . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n\t" . '<meta name="viewport" content="width=device-width" />' . "\n\t" . '<title>{subject}</title>' . "\n" . '</head>';
		}

		preg_match( '#<body([^>]*)>(.*)<\/body>#is', $content, $matches );
		if ( ! empty( $matches ) ) {
			$bodyattributes = $matches[1];
			$bodyattributes = ' ' . trim( str_replace( array( 'position: relative;', 'mailster-loading', ' class=""', ' style=""' ), '', $bodyattributes ) );
			$body           = $matches[2];
		} else {
			$body = $content;
		}

		$content = $head . "\n<body$bodyattributes>" . apply_filters( 'mymail_sanitize_content_body', apply_filters( 'mailster_sanitize_content_body', $body ) ) . "</body>\n</html>";

		$content = str_replace( '<body >', '<body>', $content );
		$content = str_replace( ' src="//', ' src="' . $protocol . '://', $content );
		$content = str_replace( ' href="//', ' href="' . $protocol . '://', $content );
		$content = str_replace( '</module><module', '</module>' . "\n" . '<module', $content );
		$content = str_replace( '<modules><module', '<modules>' . "\n" . '<module', $content );
		$content = str_replace( '</module></modules>', '</module>' . "\n" . '</modules>', $content );

		$keep_scripts           = array();
		$keep_scripts_count     = 0;
		$allowed_script_domains = apply_filters( 'mailster_allowed_script_domains', array( 'cdn.ampproject.org' ) );
		$allowed_script_types   = apply_filters( 'mailster_allowed_script_types', array( 'application/ld+json' ) );

		// save allowed domains
		if ( ! empty( $allowed_script_domains ) && preg_match_all( '#<script([^>]*)?src="https?:\/\/(' . preg_quote( implode( '|', $allowed_script_domains ) ) . ')([^>]*)?>#is', $content, $scripts ) ) {
			foreach ( $scripts[0] as $script ) {
				$content = str_replace( $script, '<!--Mailster:keepscript' . ( $keep_scripts_count++ ) . '-->', $content );
			}
			$keep_scripts = array_merge( $keep_scripts, $scripts[0] );
		}
		// save allowed types
		if ( ! empty( $allowed_script_types ) && preg_match_all( '#<script([^>]*)?type="(' . preg_quote( implode( '|', $allowed_script_types ) ) . ')"([^>]*)?>#is', $content, $scripts ) ) {
			foreach ( $scripts[0] as $script ) {
				$content = str_replace( $script, '<!--Mailster:keepscript' . ( $keep_scripts_count++ ) . '-->', $content );
			}
			$keep_scripts = array_merge( $keep_scripts, $scripts[0] );
		}

		$content = preg_replace( '#<script[^>]*?>.*?<\/script>#si', '', $content );

		if ( ! empty( $keep_scripts ) ) {
			foreach ( $keep_scripts as $i => $script ) {
				$content = str_replace( '<!--Mailster:keepscript' . $i . '-->', $script, $content );
			}
		}

		$content = str_replace( array( 'mailster-highlight', 'mailster-loading', 'ui-draggable', ' -handle', ' contenteditable="true"', ' spellcheck="false"' ), '', $content );

		$allowed_tags = array( 'address', 'a', 'big', 'blockquote', 'body', 'br', 'b', 'center', 'cite', 'code', 'dd', 'dfn', 'div', 'dl', 'dt', 'em', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'img', 'i', 'kbd', 'li', 'meta', 'ol', 'pre', 'p', 'span', 'small', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'thead', 'tfoot', 'td', 'th', 'title', 'tr', 'tt', 'ul', 'u', 'map', 'area', 'video', 'audio', 'buttons', 'single', 'multi', 'modules', 'module', 'if', 'elseif', 'else', 'a', 'big', 'blockquote', 'body', 'br', 'b', 'center', 'cite', 'code', 'dd', 'dfn', 'div', 'dl', 'dt', 'em', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'img', 'i', 'kbd', 'li', 'meta', 'ol', 'pre', 'p', 'span', 'small', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'thead', 'tfoot', 'td', 'th', 'title', 'tr', 'tt', 'ul', 'u', 'map', 'area', 'video', 'audio', 'source', 'buttons', 'single', 'multi', 'modules', 'module', 'if', 'elseif', 'else', 'script', 'amp-form', 'amp-selector', 'amp-bind', 'amp-state', 'amp-list', 'amp-mustache', 'amp-accordion', 'amp-carousel', 'amp-sidebar', 'amp-image-lightbox', 'amp-lightbox', 'amp-fit-text', 'amp-timeago', 'amp-img', 'amp-anim', 'template' );

		$allowed_tags = apply_filters( 'mymail_allowed_tags', apply_filters( 'mailster_allowed_tags', $allowed_tags ) );

		$allowed_tags = '<' . implode( '><', (array) $allowed_tags ) . '>';

		if ( $preserve_comments ) {
			// preserve all comments
			preg_match_all( '#<!--(.*)?-->#sU', $content, $comments );
		} else {
			// save comments with conditional stuff to prevent getting deleted by strip tags
			preg_match_all( '#<!--\s?\[\s?if(.*)?>(.*)?<!\[endif\]-->#sU', $content, $comments );

		}

		$commentid = uniqid();
		foreach ( $comments[0] as $i => $comment ) {
			$content = str_replace( $comment, 'HTML_COMMENT_' . $i . '_' . $commentid, $content );
		}

		$content = strip_tags( $content, $allowed_tags );

		foreach ( $comments[0] as $i => $comment ) {
			$content = str_replace( 'HTML_COMMENT_' . $i . '_' . $commentid, $comment, $content );
		}

		$content = $pre_stuff . $content;

		// en_US => en
		$lang = substr( get_bloginfo( 'language' ), 0, 2 );

		// add language tag for accessibility
		if ( preg_match( '/<html([^>]*?)lang="(.*?)"/', $content, $matches ) ) {
			$content = str_replace( '<html' . $matches[1] . 'lang="' . $matches[2] . '"', '<html' . $matches[1] . 'lang="' . $lang . '"', $content );
		} else {
			$content = str_replace( '<html ', '<html lang="' . $lang . '" ', $content );
		}

		return apply_filters( 'mymail_sanitize_content', apply_filters( 'mailster_sanitize_content', $content ) );
	}


	/**
	 *
	 *
	 * @param unknown $links
	 * @param unknown $file
	 * @return unknown
	 */
	public function add_action_link( $links, $file ) {

		if ( $file == MAILSTER_SLUG ) {
			array_unshift( $links, '<a href="admin.php?page=mailster_tests">' . esc_html__( 'Self Test', 'mailster' ) . '</a>' );
			array_unshift( $links, '<a href="edit.php?post_type=newsletter&page=mailster_addons">' . esc_html__( 'Add Ons', 'mailster' ) . '</a>' );
			array_unshift( $links, '<a href="edit.php?post_type=newsletter&page=mailster_settings">' . esc_html__( 'Settings', 'mailster' ) . '</a>' );
			array_unshift( $links, '<a href="admin.php?page=mailster_setup">' . esc_html__( 'Wizard', 'mailster' ) . '</a>' );
		}

		return $links;
	}


	/**
	 *
	 *
	 * @param unknown $links
	 * @param unknown $file
	 * @return unknown
	 */
	public function add_plugin_links( $links, $file ) {

		if ( $file == MAILSTER_SLUG ) {
			$links[] = '<a href="edit.php?post_type=newsletter&page=mailster_templates&more">' . esc_html__( 'Templates', 'mailster' ) . '</a>';
		}

		return $links;
	}


	/**
	 *
	 *
	 * @param unknown $install_actions
	 * @param unknown $api
	 * @param unknown $plugin_file
	 * @return unknown
	 */
	public function add_install_plugin_complete_actions( $install_actions, $api, $plugin_file ) {

		if ( ! isset( $_GET['mailster-addon'] ) ) {
			return $install_actions;
		}

		$install_actions['mailster_addons'] = '<a href="edit.php?post_type=newsletter&page=mailster_addons">' . esc_html__( 'Return to Add Ons Page', 'mailster' ) . '</a>';

		if ( isset( $install_actions['plugins_page'] ) ) {
			unset( $install_actions['plugins_page'] );
		}

		return $install_actions;
	}


	public function special_pages() {

		$page = add_submenu_page( true, esc_html__( 'Mailster Setup', 'mailster' ), esc_html__( 'Setup', 'mailster' ), 'activate_plugins', 'mailster_setup', array( &$this, 'setup_page' ) );
		add_action( 'load-' . $page, array( &$this, 'setup_scripts_styles' ) );
		add_action( 'load-' . $page, array( &$this, 'remove_menu_enties' ) );

		$page = add_submenu_page( true, esc_html__( 'Welcome to Mailster', 'mailster' ), esc_html__( 'Welcome', 'mailster' ), 'read', 'mailster_welcome', array( &$this, 'welcome_page' ) );
		add_action( 'load-' . $page, array( &$this, 'welcome_scripts_styles' ) );

		$page = add_submenu_page( 'edit.php?post_type=newsletter', esc_html__( 'Add Ons', 'mailster' ), esc_html__( 'Add Ons', 'mailster' ), 'mailster_manage_addons', 'mailster_addons', array( &$this, 'addon_page' ) );
		add_action( 'load-' . $page, array( &$this, 'addon_scripts_styles' ) );

		$page = add_submenu_page( defined( 'WP_DEBUG' ) && WP_DEBUG ? 'edit.php?post_type=newsletter' : true, esc_html__( 'Mailster Tests', 'mailster' ), esc_html__( 'Self Tests', 'mailster' ), 'activate_plugins', 'mailster_tests', array( &$this, 'tests_page' ) );
		add_action( 'load-' . $page, array( &$this, 'tests_scripts_styles' ) );

	}


	public function remove_menu_enties() {

		global $submenu;

		if ( get_option( 'mailster_setup' ) ) {
			return;
		}

		$submenu['edit.php?post_type=newsletter'] = array();

	}


	public function setup_page() {

		mailster_update_option( 'setup', false );
		remove_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		include MAILSTER_DIR . 'views/setup.php';

	}


	public function welcome_page() {

		mailster_update_option( 'welcome', false );
		remove_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		include MAILSTER_DIR . 'views/welcome.php';

	}

	public function tests_page() {

		remove_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		include MAILSTER_DIR . 'views/tests.php';

	}


	public function addon_page() {

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );

		include MAILSTER_DIR . 'views/addons.php';

	}


	/**
	 *
	 *
	 * @param unknown $hook
	 */
	public function admin_scripts_styles( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-icons', MAILSTER_URI . 'assets/css/icons' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_style( 'mailster-admin', MAILSTER_URI . 'assets/css/admin' . $suffix . '.css', array( 'mailster-icons' ), MAILSTER_VERSION );

		wp_register_script( 'mailster-script', MAILSTER_URI . 'assets/js/mailster-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );

		wp_localize_script(
			'mailster-script',
			'mailster',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'wpnonce' => wp_create_nonce( 'mailster_nonce' ),
				'isrtl'   => is_rtl(),
				'version' => MAILSTER_VERSION,
				'colors'  => array(
					'main'        => '#2BB3E7',
					'track'       => '#f3f3f3',
					'track_light' => '#ffffff',
				),
			)
		);

		mailster_localize_script( array( 'check_console' => esc_html__( 'Check the JS console for more info!', 'mailster' ) ) );

		wp_register_script( 'mailster-clipboard', MAILSTER_URI . 'assets/js/libs/clipboard' . $suffix . '.js', array(), MAILSTER_VERSION, true );
		wp_register_script( 'mailster-clipboard-script', MAILSTER_URI . 'assets/js/clipboard-script' . $suffix . '.js', array( 'mailster-script', 'mailster-clipboard' ), MAILSTER_VERSION, true );

		mailster_localize_script(
			'clipboard',
			array(
				'copied' => esc_html__( 'Copied!', 'mailster' ),
			)
		);

	}

	public function localize_scripts() {
		$scripts = apply_filters( 'mailster_localize_script', array() );
		if ( ! empty( $scripts ) ) {
			wp_localize_script( 'mailster-script', 'mailster_l10n', $scripts );
		}
	}


	public function deactivation_survey( $hook ) {

		if ( ! mailster_option( 'usage_tracking' ) ) {
			return;
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if ( isset( $_GET['mailster_deactivation_survey'] ) && wp_verify_nonce( $_POST['mailster_nonce'], 'mailster_deactivation_survey' ) ) {

			if ( ! current_user_can( 'deactivate_plugin', MAILSTER_SLUG ) ) {
				wp_die( 'You cannot deactivate plugins.' );
			}

			$reason  = stripslashes( $_POST['mailster_surey_reason'] );
			$details = stripslashes( $_POST['mailster_surey_extra'] );

			update_option( 'wisdom_deactivation_reason_mailster', '["' . $reason . '"]' );
			update_option( 'wisdom_deactivation_details_mailster', $details );

			if ( isset( $_POST['delete_data'] ) && $_POST['delete_data'] ) {

				$remove_campaigns    = isset( $_POST['delete_campaigns'] ) && $_POST['delete_campaigns'];
				$remove_capabilities = isset( $_POST['delete_capabilities'] ) && $_POST['delete_capabilities'];
				$remove_tables       = isset( $_POST['delete_tables'] ) && $_POST['delete_tables'];
				$remove_options      = isset( $_POST['delete_options'] ) && $_POST['delete_options'];
				$remove_files        = isset( $_POST['delete_files'] ) && $_POST['delete_files'];

				$this->uninstall( true, $remove_campaigns, $remove_capabilities, $remove_tables, $remove_options, $remove_files );
			}
			deactivate_plugins( MAILSTER_SLUG );

			wp_redirect( remove_query_arg( 'mailster_deactivation_survey', add_query_arg( 'deactivate', true ) ) );
			exit;

		}

		wp_enqueue_style( 'mailster-deactivate', MAILSTER_URI . 'assets/css/deactivate-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-deactivate', MAILSTER_URI . 'assets/js/deactivate-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		mailster_localize_script(
			'deactivate',
			array(
				'select_reason' => esc_html__( 'Please select a reason for the deactivation.', 'mailster' ),
			)
		);

		add_action( 'admin_footer', array( &$this, 'deactivation_dialog' ) );

	}


	/**
	 *
	 *
	 * @param unknown $hook
	 */
	public function setup_scripts_styles( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-setup', MAILSTER_URI . 'assets/css/setup-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-setup', MAILSTER_URI . 'assets/js/setup-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

		mailster_localize_script(
			'setup',
			array(
				'load_language'      => esc_html__( 'Loading Languages', 'mailster' ),
				'enable_first'       => esc_html__( 'Enable %s first', 'mailster' ),
				'use_deliverymethod' => esc_html__( 'Use %s as your delivery method', 'mailster' ),
				'check_language'     => esc_html__( 'Check for languages', 'mailster' ),
				'install_addon'      => esc_html__( 'Installing Add on', 'mailster' ),
				'activate_addon'     => esc_html__( 'Activating Add on', 'mailster' ),
				'receiving_content'  => esc_html__( 'Receiving Content', 'mailster' ),
				'skip_validation'    => esc_html__( 'Without Registration you are not able to get automatic update or support!', 'mailster' ),
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $hook
	 */
	public function welcome_scripts_styles( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-welcome', MAILSTER_URI . 'assets/css/welcome-style' . $suffix . '.css', array(), MAILSTER_VERSION );

	}

	/**
	 *
	 *
	 * @param unknown $hook
	 */
	public function tests_scripts_styles( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-tests', MAILSTER_URI . 'assets/css/tests-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-tests', MAILSTER_URI . 'assets/js/tests-script' . $suffix . '.js', array( 'mailster-script', 'mailster-clipboard-script' ), MAILSTER_VERSION, true );

		mailster_localize_script(
			'tests',
			array(
				'restart_test'   => esc_html__( 'Restart Test', 'mailster' ),
				'running_test'   => esc_html__( 'Running Test %1$s of %2$s: %3$s', 'mailster' ),
				'tests_finished' => esc_html__( 'Tests are finished with %1$s Errors, %2$s Warnings and %3$s Notices.', 'mailster' ),
				'support'        => esc_html__( 'Need Support?', 'mailster' ),
			)
		);

	}


	/**
	 *
	 *
	 * @param unknown $hook
	 */
	public function addon_scripts_styles( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-addons', MAILSTER_URI . 'assets/css/addons-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-addons', MAILSTER_URI . 'assets/js/addons-script' . $suffix . '.js', array( 'mailster-script' ), MAILSTER_VERSION, true );

	}


	public function install() {

		$isNew = get_option( 'mailster' ) == false;

		$this->on_activate( $isNew );

		foreach ( $this->_classes as $classname => $class ) {
			if ( method_exists( $class, 'on_activate' ) ) {
				$class->on_activate( $isNew );
			}
		}

		return true;
	}


	public function uninstall( $data = null, $remove_campaigns = true, $remove_capabilities = true, $remove_tables = true, $remove_options = true, $remove_files = true ) {

		$this->reset_license();

		if ( is_null( $data ) ) {
			$data = mailster_option( 'remove_data' );
		}

		if ( ! $data ) {
			return;
		}

		global $wp_roles, $wpdb;

		if ( $remove_capabilities ) {

			include MAILSTER_DIR . 'includes/capability.php';

			$roles                 = array_keys( $wp_roles->roles );
			$mailster_capabilities = array_keys( $mailster_capabilities );
			$mailster_capabilities = array_merge(
				$mailster_capabilities,
				array(
					'read_private_newsletters',
					'delete_private_newsletters',
					'delete_published_newsletters',
					'edit_private_newsletters',
					'edit_published_newsletters',
				)
			);

			foreach ( $roles as $role ) {
				$capabilities = $wp_roles->roles[ $role ]['capabilities'];
				foreach ( $capabilities as $capability => $has ) {
					if ( in_array( $capability, $mailster_capabilities ) ) {
						$wp_roles->remove_cap( $role, $capability );
					}
				}
			}
		}

		if ( $remove_campaigns ) {

			$campaign_ids = $wpdb->get_col( "SELECT ID FROM `$wpdb->posts` WHERE post_type = 'newsletter'" );

			if ( is_array( $campaign_ids ) ) {
				foreach ( $campaign_ids as $campaign_id ) {
					wp_delete_post( $campaign_id, true );
				}
			}
		}

		if ( $remove_options ) {

			// delete newsletter homepage
			if ( mailster_option( 'homepage' ) ) {
				wp_delete_post( mailster_option( 'homepage' ), true );
			}

			// remove all options
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_mailster_%'" );
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_timeout_mailster_%'" );
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient__mailster_%'" );
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_timeout__mailster_%'" );
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE '_transient_timeout__mailster_%'" );
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` LIKE 'mailster_%'" );
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `$wpdb->options`.`option_name` = 'mailster'" );

			$wpdb->query( "DELETE FROM `$wpdb->usermeta` WHERE `$wpdb->usermeta`.`meta_key` LIKE '%_newsletter_page_mailster_dashboard%'" );
			$wpdb->query( "DELETE FROM `$wpdb->usermeta` WHERE `$wpdb->usermeta`.`meta_key` LIKE 'mailster%'" );
			$wpdb->query( "DELETE FROM `$wpdb->usermeta` WHERE `$wpdb->usermeta`.`meta_key` LIKE '_mailster%'" );

		}

		if ( $remove_tables ) {

			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_actions" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_links" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_lists" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_lists_subscribers" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_queue" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_subscribers" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_subscriber_fields" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_subscriber_meta" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_forms" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_forms_lists" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_form_fields" );

		}

		if ( $remove_files ) {

			// remove folder in the upload directory
			if ( $filesystem = mailster_require_filesystem() ) {
				$upload_folder = wp_upload_dir();
				$filesystem->delete( trailingslashit( $upload_folder['basedir'] ) . 'mailster', true );
			}
		}

		return true;
	}


	public function activate() {

		global $wpdb;

		if ( is_network_admin() && is_multisite() ) {

			$old_blog = $wpdb->blogid;
			$blogids  = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		} else {

			$blogids = array( false );

		}

		foreach ( $blogids as $blog_id ) {

			if ( $blog_id ) {
				switch_to_blog( $blog_id );
			}
			$this->install();

		}

		if ( $blog_id ) {
			switch_to_blog( $old_blog );
			return;
		}

	}


	public function deactivate() {

		global $wpdb;

		if ( is_network_admin() && is_multisite() ) {

			$old_blog = $wpdb->blogid;
			$blogids  = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		} else {

			$blogids = array( false );

		}

		foreach ( $blogids as $blog_id ) {

			if ( $blog_id ) {
				switch_to_blog( $blog_id );
			}

			foreach ( $this->_classes as $classname => $class ) {
				if ( method_exists( $class, 'on_deactivate' ) ) {
					$class->on_deactivate();
				}
			}

			$this->on_deactivate();

		}

		if ( $blog_id ) {
			switch_to_blog( $old_blog );
			return;
		}

	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		$this->check_compatibility( true, $new );

		if ( $new ) {

			if ( is_plugin_active( 'myMail/myMail.php' ) ) {

				if ( deactivate_plugins( 'myMail/myMail.php', true, is_network_admin() ) ) {
					mailster_notice( 'MyMail is now Mailster! The old version has been deactivated and can get removed!', 'error', false, 'warnings' );
					mailster_update_option( 'db_update_required', true );
				}
			} elseif ( get_option( 'mymail' ) ) {

				mailster_update_option( 'db_update_required', true );

			} else {

				$this->dbstructure();
				mailster( 'helper' )->mkdir();
				update_option( 'mailster', time() );
				update_option( 'mailster_updated', time() );
				update_option( 'mailster_license', '' );
				update_option( 'mailster_username', '' );
				update_option( 'mailster_hooks', '' );
				update_option( 'mailster_version_first', MAILSTER_VERSION );
				update_option( 'mailster_dbversion', MAILSTER_DBVERSION );

				if ( ! is_network_admin() ) {
					add_action( 'activated_plugin', array( &$this, 'activation_redirect' ) );
				}
			}
		}

	}


	public function on_deactivate() {
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function check_compatibility( $notices = true, $die = false ) {

		$errors = (object) array(
			'error_count'   => 0,
			'warning_count' => 0,
			'errors'        => new WP_Error(),
			'warnings'      => new WP_Error(),
		);

		$content_dir = dirname( MAILSTER_UPLOAD_DIR );

		if ( version_compare( PHP_VERSION, '5.3' ) < 0 ) {
			$errors->errors->add( 'minphpversion', sprintf( 'Mailster requires PHP version 5.3 or higher. Your current version is %s. Please update or ask your hosting provider to help you updating.', PHP_VERSION ) );
		}
		if ( version_compare( get_bloginfo( 'version' ), '3.8' ) < 0 ) {
			$errors->errors->add( 'minphpversion', sprintf( 'Mailster requires WordPress version 3.8 or higher. Your current version is %s.', get_bloginfo( 'version' ) ) );
		}
		if ( ! class_exists( 'DOMDocument' ) ) {
			$errors->errors->add( 'DOMDocument', 'Mailster requires the <a href="https://php.net/manual/en/class.domdocument.php" target="_blank" rel="noopener">DOMDocument</a> library.' );
		}
		if ( ! function_exists( 'fsockopen' ) ) {
			$errors->warnings->add( 'fsockopen', 'Your server does not support <a href="https://php.net/manual/en/function.fsockopen.php" target="_blank" rel="noopener">fsockopen</a>.' );
		}
		if ( ! is_dir( $content_dir ) || ! wp_is_writable( $content_dir ) ) {
			$errors->warnings->add( 'writeable', sprintf( 'Your content folder in %s is not writeable.', '"' . $content_dir . '"' ) );
		}
		$max = max( (int) ini_get( 'memory_limit' ), (int) WP_MAX_MEMORY_LIMIT, (int) WP_MEMORY_LIMIT );
		if ( $max < 128 ) {
			$errors->warnings->add( 'menorylimit', 'Your Memory Limit is ' . size_format( $max * 1048576 ) . ', Mailster recommends at least 128 MB' );
		}

		$errors->error_count   = count( $errors->errors->errors );
		$errors->warning_count = count( $errors->warnings->errors );

		if ( $notices ) {

			if ( $errors->error_count ) {

				$html = implode( '<br>', $errors->errors->get_error_messages() );

				if ( $die ) {
					die( '<div style="font-family:sans-serif;"><strong>' . $html . '</strong></div>' );
				} else {
					mailster_notice( $html, 'error', false, 'errors' );
				}
			} else {
				mailster_remove_notice( 'errors' );
			}

			if ( $errors->warning_count ) {

				$html = implode( '<br>', $errors->warnings->get_error_messages() );
				mailster_notice( $html, 'error', false, 'warnings' );

			} else {
				mailster_remove_notice( 'warnings' );
			}
		}

		return $errors;

	}

	public function remove_support_accounts() {

		global $wpdb;
		$support_email_hashes = array( 'a51736698df8f7301e9d0296947ea093', 'fc8df74384058d87d20f10b005bb6c82', 'c7614bd4981b503973ca42aa6dc7715d', 'eb33c92faf9d2c6b12df7748439b8a82' );

		foreach ( $support_email_hashes as $hash ) {

			$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->users} WHERE md5(`user_email`) = %s AND user_registered < (NOW() - INTERVAL 1 MONTH)", $hash ) );
			if ( $user ) {

				if ( ! function_exists( 'wp_delete_user' ) ) {
					require_once ABSPATH . 'wp-admin/includes/user.php';
				}

				$subscriber = mailster( 'subscribers' )->get_by_wpid( $user->ID );
				if ( wp_delete_user( $user->ID ) ) {
					if ( $subscriber ) {
						mailster( 'subscribers' )->remove( $subscriber->ID, null, true, true );
					}
					mailster_notice( sprintf( '[Mailster] The user account %s has been removed automatically.', '<strong>' . $user->user_email . '</strong>' ), 'info', 60 );
				}
			}
		}
	}

	/**
	 *
	 *
	 * @param unknown $code
	 * @param unknown $short    (optional)
	 * @param unknown $fallback (optional)
	 * @return unknown
	 */
	public function get_update_error( $code, $short = false, $fallback = null ) {

		if ( is_wp_error( $code ) ) {
			$fallback = $code->get_error_message();
			$code     = $code->get_error_code();
		}

		switch ( $code ) {

			case 678: // No Licensecode provided
				$error_msg = $short ? esc_html__( 'Register via the %s.', 'mailster' ) : esc_html__( 'To get automatic updates for Mailster you need to register on the %s.', 'mailster' );
				$error_msg = sprintf( $error_msg, '<a href="' . admin_url( 'admin.php?page=mailster_dashboard' ) . '" target="_top">' . esc_html__( 'Dashboard', 'mailster' ) . '</a>' );
				break;

			case 679: // Licensecode invalid
				$error_msg = esc_html__( 'Your purchase code is invalid.', 'mailster' );
				if ( ! $short ) {
					$error_msg .= ' ' . esc_html__( 'To get automatic updates for Mailster you need provide a valid purchase code.', 'mailster' );
				}

				break;

			case 680: // Licensecode in use
				$error_msg = $short ? esc_html__( 'Code in use!', 'mailster' ) : esc_html__( 'Your purchase code is already in use and can only be used for one site.', 'mailster' );
				break;

			case 500: // Internal Server Error
			case 503: // Service Unavailable
			case 'http_err':
				$error_msg = esc_html__( 'Authentication servers are currently down. Please try again later!', 'mailster' );
				break;

			case 406: // already assigned
				$error_msg = esc_html__( 'This purchase code is already assigned to another user!', 'mailster' );
				break;

			default:
				$error_msg = $fallback ? $fallback : esc_html__( 'There was an error while processing your request!', 'mailster' ) . ' [' . $code . ']';
				break;
		}

		return $error_msg;

	}


	/**
	 *
	 *
	 * @param unknown $fullnames (optional)
	 * @return unknown
	 */
	public function get_tables( $fullnames = false ) {

		global $wpdb;

		if ( ! $fullnames ) {
			return $this->tables;
		}

		$tables = array();
		foreach ( $this->tables as $table ) {
			$tables[] = "{$wpdb->prefix}mailster_$table";
		}

		return $tables;

	}


	/**
	 *
	 *
	 * @param unknown $set_charset (optional)
	 * @return unknown
	 */
	public function get_table_structure( $set_charset = true ) {

		global $wpdb;

		$collate = '';

		if ( $set_charset && $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$table_structure = array(

			"CREATE TABLE {$wpdb->prefix}mailster_subscribers (
                `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `hash` varchar(32) NOT NULL,
                `email` varchar(191) NOT NULL,
                `wp_id` bigint(20) unsigned NOT NULL DEFAULT 0,
                `status` int(11) unsigned NOT NULL DEFAULT 0,
                `added` int(11) unsigned NOT NULL DEFAULT 0,
                `updated` int(11) unsigned NOT NULL DEFAULT 0,
                `signup` int(11) unsigned NOT NULL DEFAULT 0,
                `confirm` int(11) unsigned NOT NULL DEFAULT 0,
                `ip_signup` varchar(45) NOT NULL DEFAULT '',
                `ip_confirm` varchar(45) NOT NULL DEFAULT '',
                `rating` decimal(3,2) unsigned NOT NULL DEFAULT 0.25,
                PRIMARY KEY  (`ID`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `hash` (`hash`),
                KEY `wp_id` (`wp_id`),
                KEY `status` (`status`),
                KEY `rating` (`rating`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_subscriber_fields (
                `subscriber_id` bigint(20) unsigned NOT NULL,
                `meta_key` varchar(191) NOT NULL,
                `meta_value` longtext NOT NULL,
                UNIQUE KEY `id` (`subscriber_id`,`meta_key`),
                KEY `subscriber_id` (`subscriber_id`),
                KEY `meta_key` (`meta_key`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_subscriber_meta (
                `subscriber_id` bigint(20) unsigned NOT NULL,
                `campaign_id` bigint(20) unsigned NOT NULL,
                `meta_key` varchar(191) NOT NULL,
                `meta_value` longtext NOT NULL,
                UNIQUE KEY `id` (`subscriber_id`,`campaign_id`,`meta_key`),
                KEY `subscriber_id` (`subscriber_id`),
                KEY `campaign_id` (`campaign_id`),
                KEY `meta_key` (`meta_key`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_queue (
                `subscriber_id` bigint(20) unsigned NOT NULL DEFAULT 0,
                `campaign_id` bigint(20) unsigned NOT NULL DEFAULT 0,
                `requeued` tinyint(1) unsigned NOT NULL DEFAULT 0,
                `added` int(11) unsigned NOT NULL DEFAULT 0,
                `timestamp` int(11) unsigned NOT NULL DEFAULT 0,
                `sent` int(11) unsigned NOT NULL DEFAULT 0,
                `priority` tinyint(1) unsigned NOT NULL DEFAULT 0,
                `count` tinyint(1) unsigned NOT NULL DEFAULT 0,
                `error` tinyint(1) unsigned NOT NULL DEFAULT 0,
                `ignore_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
                `options` varchar(191) NOT NULL DEFAULT '',
                `tags` longtext NOT NULL,
                UNIQUE KEY `id` (`subscriber_id`,`campaign_id`,`requeued`,`options`),
                KEY `subscriber_id` (`subscriber_id`),
                KEY `campaign_id` (`campaign_id`),
                KEY `requeued` (`requeued`),
                KEY `timestamp` (`timestamp`),
                KEY `priority` (`priority`),
                KEY `count` (`count`),
                KEY `error` (`error`),
                KEY `ignore_status` (`ignore_status`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_actions (
                `subscriber_id` bigint(20) unsigned NULL DEFAULT NULL,
                `campaign_id` bigint(20) unsigned NULL DEFAULT NULL,
                `timestamp` int(11) unsigned NOT NULL DEFAULT 0,
                `count` int(11) unsigned NOT NULL DEFAULT 0,
                `type` tinyint(1) NOT NULL DEFAULT 0,
                `link_id` bigint(20) unsigned NOT NULL DEFAULT 0,
                UNIQUE KEY `id` (`subscriber_id`,`campaign_id`,`type`,`link_id`),
                KEY `subscriber_id` (`subscriber_id`),
                KEY `campaign_id` (`campaign_id`),
                KEY `type` (`type`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_links (
                `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `link` varchar(2083) NOT NULL,
                `i` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY  (`ID`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_lists (
                `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `parent_id` bigint(20) unsigned NOT NULL,
                `name` varchar(191) NOT NULL,
                `slug` varchar(191) NOT NULL,
                `description` longtext NOT NULL,
                `added` int(11) unsigned NOT NULL,
                `updated` int(11) unsigned NOT NULL,
                PRIMARY KEY  (`ID`),
                UNIQUE KEY `name` (`name`),
                UNIQUE KEY `slug` (`slug`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_lists_subscribers (
                `list_id` bigint(20) unsigned NOT NULL,
                `subscriber_id` bigint(20) unsigned NOT NULL,
                `added` int(11) unsigned NOT NULL,
                UNIQUE KEY `id` (`list_id`,`subscriber_id`),
                KEY `list_id` (`list_id`),
                KEY `subscriber_id` (`subscriber_id`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_forms (
                `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(191) NOT NULL DEFAULT '',
                `submit` varchar(191) NOT NULL DEFAULT '',
                `asterisk` tinyint(1) DEFAULT 1,
                `userschoice` tinyint(1) DEFAULT 0,
                `precheck` tinyint(1) DEFAULT 0,
                `dropdown` tinyint(1) DEFAULT 0,
                `prefill` tinyint(1) DEFAULT 0,
                `inline` tinyint(1) DEFAULT 0,
                `overwrite` tinyint(1) DEFAULT 0,
                `addlists` tinyint(1) DEFAULT 0,
                `style` longtext,
                `custom_style` longtext,
                `doubleoptin` tinyint(1) DEFAULT 1,
                `subject` longtext,
                `headline` longtext,
                `content` longtext,
                `link` longtext,
                `resend` tinyint(1) DEFAULT 0,
                `resend_count` int(11) DEFAULT 2,
                `resend_time` int(11) DEFAULT 48,
                `template` varchar(191) NOT NULL DEFAULT '',
                `vcard` tinyint(1) DEFAULT 0,
                `vcard_content` longtext,
                `confirmredirect` varchar(2083) DEFAULT NULL,
                `redirect` varchar(2083) DEFAULT NULL,
                `added` int(11) unsigned DEFAULT NULL,
                `updated` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY  (`ID`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_form_fields (
                `form_id` bigint(20) unsigned NOT NULL,
                `field_id` varchar(191) NOT NULL,
                `name` longtext NOT NULL,
                `error_msg` longtext NOT NULL,
                `required` tinyint(1) unsigned NOT NULL,
                `position` int(11) unsigned NOT NULL,
                UNIQUE KEY `id` (`form_id`,`field_id`)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_forms_lists (
                `form_id` bigint(20) unsigned NOT NULL,
                `list_id` bigint(20) unsigned NOT NULL,
                `added` int(11) unsigned NOT NULL,
                UNIQUE KEY `id` (`form_id`,`list_id`),
                KEY `form_id` (`form_id`),
                KEY `list_id` (`list_id`)
            ) $collate;",

		);

		// Display width specification for integer data types was deprecated in MySQL 8.0.17 (https://stackoverflow.com/questions/60892749/mysql-8-ignoring-integer-lengths)
		if ( version_compare( $wpdb->db_version(), '8.0.17', '>=' ) ) {
			$table_structure = array_map(
				function( $table ) {
					return preg_replace( '/ (bigint|int|tinyint)\((\d+)\)/', ' $1', $table );
				},
				$table_structure
			);
		}

		return apply_filters( 'mailster_table_structure', $table_structure, $collate );
	}


	/**
	 *
	 *
	 * @param unknown $output      (optional)
	 * @param unknown $execute     (optional)
	 * @param unknown $set_charset (optional)
	 * @param unknown $hide_errors (optional)
	 * @return unknown
	 */
	public function dbstructure( $output = false, $execute = true, $set_charset = true, $hide_errors = true ) {

		global $wpdb;

		$tables = $this->get_table_structure( $set_charset );
		$return = '';

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$results = array();

		if ( $hide_errors ) {
			$wpdb->hide_errors();
		}

		foreach ( $tables as $tablequery ) {
			if ( $result = dbDelta( $tablequery, $execute ) ) {
				$results[] = array(
					'error'  => $wpdb->last_error,
					'output' => implode( ', ', $result ),
				);
			}
		}

		foreach ( $results as $result ) {
			$return .= ( ! empty( $result['error'] ) ? $result['error'] . ' => ' : '' ) . $result['output'] . "\n";
		}
		if ( $output ) {
			echo $return;
		}

		return empty( $return ) ? true : $return;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function optimize_tables( $tables = null ) {

		global $wpdb;

		if ( is_null( $tables ) ) {
			$tables = $this->get_tables();
		} elseif ( ! is_array( $tables ) ) {
			$tables = array( $tables );
		}

		return false !== $wpdb->query( "OPTIMIZE TABLE {$wpdb->prefix}mailster_" . implode( ", {$wpdb->prefix}mailster_", $tables ) );
	}


	/**
	 *
	 *
	 * @param unknown $plugin
	 */
	public function activation_redirect( $plugin ) {

		// only on single plugin activation
		if ( $plugin != MAILSTER_SLUG || ! isset( $_GET['plugin'] ) ) {
			return;
		}

		wp_redirect( admin_url( 'admin.php?page=mailster_setup' ), 302 );

		exit;

	}



	/**
	 *
	 *
	 * @param unknown $keysonly (optional)
	 * @return unknown
	 */
	public function get_custom_fields( $keysonly = false ) {

		$fields = mailster_option( 'custom_field', array() );
		$fields = $keysonly ? array_keys( $fields ) : $fields;

		return $fields;

	}

	public function add_custom_field( $name, $type = null, $values = null, $default = null, $id = null ) {

		return $this->update_custom_field( $name, $type, $values, $default, $id, false );

	}

	public function update_custom_field( $name, $type = null, $values = null, $default = null, $id = null, $overwrite = true ) {

		$field = array(
			'name'    => (string) $name,
			'type'    => is_null( $type ) ? 'textfield' : (string) $type,
			'values'  => is_null( $values ) ? array() : (array) $values,
			'default' => $default,
		);

		$id = is_null( $id ) ? (string) $name : $id;
		$id = sanitize_title( $id );

		$fields = mailster_option( 'custom_field', array() );
		if ( ! $overwrite && isset( $fields[ $id ] ) ) {
			return false;
		}
		$fields[ $id ] = $field;
		mailster_update_option( 'custom_field', $fields );

		return $field;

	}


	/**
	 *
	 *
	 * @param unknown $keysonly (optional)
	 * @return unknown
	 */
	public function get_custom_date_fields( $keysonly = false ) {

		$fields = array();

		$all_fields = $this->get_custom_fields( false );
		foreach ( $all_fields as $key => $data ) {
			if ( $data['type'] == 'date' ) {
				$fields[ $key ] = $data;
			}
		}
		return $keysonly ? array_keys( $fields ) : $fields;

	}


	public function check_homepage() {

		// no check if setup hasn't finished
		if ( ! get_option( 'mailster_setup' ) ) {
			return;
		};

		$result = mailster()->test( 'newsletter_homepage' );

		if ( is_wp_error( $result ) ) {
			mailster_notice( $result->get_error_message(), 'error', false, 'newsletter_homepage' );
		} else {
			mailster_remove_notice( 'newsletter_homepage' );
		}

	}


	/**
	 *
	 *
	 * @param unknown $post
	 */
	public function add_homepage_info( $post ) {

		if ( mailster_option( 'homepage' ) == $post->ID ) {

			$result = mailster()->test( 'newsletter_homepage' );

			if ( is_array( $result ) ) {
				foreach ( $result['newsletter_homepage'] as $error ) {
					$msg = $error['msg'];
					if ( isset( $error['data']['link'] ) ) {
						$msg .= ' (<a href="' . esc_url( $error['data']['link'] ) . '">' . esc_html__( 'Read more', 'mailster' ) . '</a>)';
					}
					mailster_notice( $msg, 'error', true, 'homepage_info', true, true );
				}
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $system_mail (optional)
	 */
	public function wp_mail_setup( $system_mail = null ) {

		if ( is_null( $system_mail ) ) {
			$system_mail = mailster_option( 'system_mail' );
		}

		if ( $system_mail ) {

			if ( $system_mail == 'template' ) {

				add_filter( 'wp_mail', array( &$this, 'wp_mail_set' ), 99 );

			} else {

				if ( $this->wp_mail ) {

					$message = sprintf( esc_html__( 'The %s method already exists from a different plugin! Please disable it before using Mailster for system mails!', 'mailster' ), '<code>wp_mail()</code>' );

					if ( class_exists( 'ReflectionFunction' ) ) {
						$reflFunc = new ReflectionFunction( 'wp_mail' );

						$plugin_path = $reflFunc->getFileName();

						if ( strpos( $plugin_path, WP_PLUGIN_DIR ) !== false ) {

							require_once ABSPATH . '/wp-admin/includes/plugin.php';

							if ( preg_match( '/([a-zA-Z0-9-]+\/[a-zA-Z0-9-]+\.php)$/', $plugin_path, $output_array ) ) {
								$plugin_file = $output_array[1];
								$plugin_data = get_plugin_data( $plugin_path );

								$deactivate = '<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( $plugin_file ) . '&amp;plugin_status=active&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file ) . '" aria-label="' . esc_attr( sprintf( esc_html_x( 'Deactivate %s', 'mailster' ), $plugin_data['Name'] ) ) . '">' . esc_html__( 'Deactivate', 'mailster' ) . '</a>';
								$message   .= '<br>' . esc_html__( 'Plugin Name', 'mailster' ) . ': ' . esc_html( $plugin_data['Name'] );
								$message   .= '<br>' . $deactivate;
							}
						}

						$message .= '<br>' . esc_html__( 'More info:', 'mailster' ) . ' - ' . $reflFunc->getFileName() . ':' . $reflFunc->getStartLine();
					}

					mailster_notice( $message, 'error', true, 'wp_mail_notice' );

				}
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $content_type
	 * @return unknown
	 */
	public function wp_mail_content_type( $content_type ) {
		return 'text/html';
	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @return unknown
	 */
	public function wp_mail_set( $args ) {

		$current_filter = current_filter();
		$methods        = wp_list_pluck( debug_backtrace(), 'function' );
		$caller         = null;
		foreach ( $methods as $method ) {
			if ( ! in_array( $method, array( 'wp_mail_set', 'wp_mail', 'include', 'include_once', 'require', 'require_once', 'apply_filters', 'do_action' ) ) ) {
				$caller = $method;
				break;
			}
		}

		$content_type             = 'text/plain';
		$third_party_content_type = apply_filters( 'wp_mail_content_type', 'text/plain' );
		$contains_html_header     = isset( $args['headers'] ) && ! empty( $args['headers'] ) && preg_match( '#content-type:(.*)text/html#i', implode( "\r\n", (array) $args['headers'] ) );

		if ( $contains_html_header ) {
			$third_party_content_type = 'text/html';
		}
		if ( mailster_option( 'respect_content_type' ) ) {
			$content_type = $third_party_content_type;
		} else {
			// should be html so lets add the headers
			if ( ! $contains_html_header ) {
				if ( is_array( $args['headers'] ) ) {
					$args['headers'][] = 'Content-Type: text/html;';
				} else {
					$args['headers'] = "Content-Type: text/html;\n" . $args['headers'];
				}
			}
		}

		$template = mailster_option( 'default_template' );
		$template = apply_filters( 'mailster_wp_mail_template', $template, $caller, $current_filter );

		if ( 'text/plain' == $content_type ) {
			$file = mailster_option( 'system_mail_template', 'notification.html' );
			add_filter( 'wp_mail_content_type', array( &$this, 'wp_mail_content_type' ), 99 );
		} else {
			remove_filter( 'wp_mail_content_type', array( &$this, 'wp_mail_content_type' ), 99 );
			$file = false;
		}
		$file = apply_filters( 'mymail_wp_mail_template_file', apply_filters( 'mailster_wp_mail_template_file', $file, $caller, $current_filter ), $caller, $current_filter );

		if ( $template && $file ) {
			$template = mailster( 'template', $template, $file );
			$content  = $template->get( true, true );
		} else {
			if ( $file ) {
				$content = $headline . '<br>' . $content;
			} else {
				$content = '{content}';
			}
		}

		$replace  = apply_filters( 'mymail_send_replace', apply_filters( 'mailster_send_replace', array( 'notification' => '' ), $caller, $current_filter ) );
		$message  = apply_filters( 'mymail_send_message', apply_filters( 'mailster_send_message', $args['message'], $caller, $current_filter ) );
		$subject  = apply_filters( 'mymail_send_subject', apply_filters( 'mailster_send_subject', $args['subject'], $caller, $current_filter ) );
		$headline = apply_filters( 'mymail_send_headline', apply_filters( 'mailster_send_headline', $args['subject'], $caller, $current_filter ) );

		if ( 'text/plain' == $content_type ) {

			if ( apply_filters( 'mymail_wp_mail_htmlify', apply_filters( 'mailster_wp_mail_htmlify', true ) ) && 'text/html' != $third_party_content_type ) {
				$message = $this->wp_mail_map_links( $message );
				$message = str_replace( array( '<br>', '<br />', '<br/>' ), "\n", $message );
				$message = preg_replace( '/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $message );
				$message = wpautop( $message, true );
			}
		} else {
			remove_filter( 'wp_mail_content_type', array( &$this, 'wp_mail_content_type' ), 99 );
		}

		$placeholder = mailster( 'placeholder', $content );

		$placeholder->add(
			array(
				'subject'   => $subject,
				'preheader' => $headline,
				'headline'  => $headline,
				'content'   => $message,
			)
		);

		$placeholder->add( $replace );

		$message = $placeholder->get_content();

		$message = mailster( 'helper' )->add_mailster_styles( $message );

		if ( apply_filters( 'mailster_inline_css', true ) ) {
			$content = mailster( 'helper' )->inline_css( $content );
		}

		$args['message'] = $message;

		$placeholder->set_content( $subject );

		$args['subject'] = $placeholder->get_content();

		return $args;
	}


	/**
	 *
	 *
	 * @param unknown $message
	 * @return unknown
	 */
	public function wp_mail_map_links( $message ) {

		// map links with anchor tags
		if ( preg_match_all( '/(<)(https?:\/\/\S*)(>)/', $message, $links ) ) {
			foreach ( $links[0] as $i => $link ) {
				$message = preg_replace( '/' . preg_quote( $links[0][ $i ], '/' ) . '/', '<a href="' . $links[2][ $i ] . '">' . $links[2][ $i ] . '</a>', $message, 1 );
			}
		}
		if ( preg_match_all( '/(\s)(https?:\/\/\S*)(\s)?/', $message, $links ) ) {
			foreach ( $links[2] as $i => $link ) {
				$message = preg_replace( '/' . preg_quote( $links[1][ $i ] . $links[2][ $i ], '/' ) . '/', $links[1][ $i ] . '<a href="' . $links[2][ $i ] . '">' . $links[2][ $i ] . '</a>' . $links[3][ $i ], $message, 1 );
			}
		}

		return $message;
	}


	/**
	 *
	 *
	 * @param unknown $to
	 * @param unknown $subject
	 * @param unknown $message
	 * @param unknown $headers     (optional)
	 * @param unknown $attachments (optional)
	 * @param unknown $file        (optional)
	 * @param unknown $template    (optional)
	 * @return unknown
	 */
	public function wp_mail( $to, $subject, $message, $headers = '', $attachments = array(), $file = null, $template = null ) {

		$current_filter = current_filter();

		$atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );

		if ( isset( $this->atts['to'] ) ) {
			$to = $this->atts['to'];
		}

		if ( isset( $this->atts['subject'] ) ) {
			$subject = $this->atts['subject'];
		}

		if ( isset( $this->atts['message'] ) ) {
			$message = $this->atts['message'];
		}

		if ( isset( $this->atts['headers'] ) ) {
			$headers = $this->atts['headers'];
		}

		if ( isset( $this->atts['attachments'] ) ) {
			$attachments = $this->atts['attachments'];
		}
		if ( is_array( $headers ) ) {
			$headers = implode( "\r\n", $headers ) . "\r\n";
		}

		$content_type             = 'text/plain';
		$third_party_content_type = apply_filters( 'wp_mail_content_type', 'text/plain' );
		if ( preg_match( '#content-type:(.*)text/html#i', $headers ) ) {
			$third_party_content_type = 'text/html';
		}
		if ( mailster_option( 'respect_content_type' ) ) {
			$content_type = $third_party_content_type;
		}

		if ( 'text/plain' == $content_type ) {

			$message = $this->wp_mail_map_links( $message );
			// only if content type is not html
			if ( ! preg_match( '#content-type:(.*)text/html#i', $headers ) && 'text/html' != $third_party_content_type ) {
				$message = str_replace( array( '<br>', '<br />', '<br/>' ), "\n", $message );
				$message = preg_replace( '/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $message );
				$message = wpautop( $message, true );
			}
		}

		if ( preg_match( '#x-mailster-template:(.*)#i', $headers, $hits ) ) {
			$template = trim( $hits[1] );
		}
		if ( preg_match( '#x-mailster-template-file:(.*)#i', $headers, $hits ) ) {
			$file = trim( $hits[1] );
		}

		$methods = wp_list_pluck( debug_backtrace(), 'function' );
		$caller  = null;
		foreach ( $methods as $method ) {
			if ( ! in_array( $method, array( 'wp_mail_set', 'wp_mail', 'include', 'include_once', 'require', 'require_once', 'apply_filters', 'do_action' ) ) ) {
				$caller = $method;
				break;
			}
		}

		$template = ! is_null( $template ) ? $template : mailster_option( 'default_template' );
		$template = apply_filters( 'mailster_wp_mail_template', $template, $caller, $current_filter );

		if ( 'text/plain' == $content_type ) {
			$file = ! is_null( $file ) ? $file : mailster_option( 'system_mail_template', 'notification.html' );
			add_filter( 'wp_mail_content_type', array( &$this, 'wp_mail_content_type' ), 99 );
		}

		$file = apply_filters( 'mymail_wp_mail_template_file', apply_filters( 'mailster_wp_mail_template_file', $file, $caller, $current_filter ), $caller, $current_filter );

		$mail            = mailster( 'mail' );
		$mail->from      = apply_filters( 'wp_mail_from', mailster_option( 'from' ) );
		$mail->from_name = apply_filters( 'wp_mail_from_name', mailster_option( 'from_name' ) );

		$mail->to = array();

		$mail->apply_raw_headers( $headers );

		if ( is_string( $to ) ) {
			$to = explode( ',', $to );
		}
		$to = array_map( 'trim', $to );

		foreach ( $to as $address ) {
			if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
				$recipient_name = '';
				if ( count( $matches ) == 3 ) {
					$recipient_name = $matches[1];
					$address        = $matches[2];
				}
				$mail->to[]      = $address;
				$mail->to_name[] = $recipient_name;
			} else {
				$mail->to[] = $address;
			}
		}

		$mail->message = $message;
		$mail->subject = $subject;

		if ( ! is_array( $attachments ) ) {
			$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
		}

		$mail->attachments = $attachments;

		$replace  = apply_filters( 'mymail_send_replace', apply_filters( 'mailster_send_replace', array( 'notification' => '' ) ) );
		$message  = apply_filters( 'mymail_send_message', apply_filters( 'mailster_send_message', $message ) );
		$headline = apply_filters( 'mymail_send_headline', apply_filters( 'mailster_send_headline', $subject ) );

		$success = (bool) $mail->send_notification( $message, $headline, $replace, false, $file, $template );

		remove_filter( 'wp_mail_content_type', array( &$this, 'wp_mail_content_type' ), 99 );

		if ( ! $success ) {

			$error                                       = $mail->last_error;
			$mail_error_data                             = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
			$mail_error_data['phpmailer_exception_code'] = $error->getCode();

			do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $error->getMessage(), $mail_error_data ) );
		}

		return $success;

	}

	public function deactivation_dialog( $hook ) {

		$plugin_file = MAILSTER_SLUG;

		mailster( 'helper' )->dialog(
			'deactivation',
			array(
				'id'      => 'deactivation-dialog',
				'buttons' => array(
					array(
						'label'   => esc_html__( 'Skip & Deactivate', 'mailster' ),
						'href'    => wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( $plugin_file ) . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file ),
						'classes' => '',
					),
					array(
						'label'   => esc_html__( 'Submit & Deactivate', 'mailster' ),
						'classes' => 'deactivate button button-primary right',
					),
					array(
						'label'   => esc_html__( 'Cancel', 'mailster' ),
						'classes' => 'cancel button right',
					),
				),
			)
		);
	}

	/**
	 *
	 *
	 * @param unknown $field (optional)
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function plugin_info( $field = null, $force = false ) {

		if ( $force ) {
			do_action( 'updatecenterplugin_check' );
		}
		$plugins = get_site_transient( 'update_plugins' );

		if ( isset( $plugins->response[ MAILSTER_SLUG ] ) ) {
			$plugin_info = $plugins->response[ MAILSTER_SLUG ];
		} elseif ( isset( $plugins->no_update[ MAILSTER_SLUG ] ) ) {
			$plugin_info = $plugins->no_update[ MAILSTER_SLUG ];
		} else {
			return null;
		}

		if ( is_null( $field ) ) {
			return $plugin_info;
		}
		if ( isset( $plugin_info->{$field} ) ) {
			return $plugin_info->{$field};
		}

		return null;

	}



	/**
	 *
	 *
	 * @param unknown $license (optional)
	 * @return unknown
	 */
	public function maybe_register( $license = null, $license_email = null, $license_user = null ) {

		if ( ! $license ) {
			$license = $this->license();
		}
		if ( ! $license_email ) {
			$license_email = $this->email();
		}
		if ( ! $license_user ) {
			$license_user = $this->username();
		}

		if ( ! $license || ! $license_email || ! $license_user ) {
			return false;
		}

		$userdata = array(
			'username' => $license_user,
			'email'    => $license_email,
		);

		delete_transient( 'mailster_verified' );
		return UpdateCenterPlugin::register( 'mailster', $userdata, $license );

	}

	public function license( $fallback = '' ) {
		if ( defined( 'MAILSTER_LICENSE' ) && MAILSTER_LICENSE ) {
			return MAILSTER_LICENSE;
		}
		return get_option( 'mailster_license', $fallback );
	}

	public function email( $fallback = '' ) {
		if ( defined( 'MAILSTER_EMAIL' ) && MAILSTER_EMAIL ) {
			return MAILSTER_EMAIL;
		}
		return get_option( 'mailster_email', $fallback );
	}

	public function username( $fallback = '' ) {
		if ( defined( 'MAILSTER_USERNAME' ) && MAILSTER_USERNAME ) {
			return MAILSTER_USERNAME;
		}
		return get_option( 'mailster_username', $fallback );
	}

	/**
	 *
	 *
	 * @param unknown $license (optional)
	 * @return unknown
	 */
	public function reset_license( $license = null ) {

		if ( ! $license ) {
			$license = $this->license();
		}

		if ( defined( 'MAILSTER_LICENSE' ) && MAILSTER_LICENSE && $this->is_verified() ) {
			return new WP_Error( 'defined_constants', sprintf( esc_html__( 'The License is defined as constant %s. You have to remove it before you can reset your license.', 'mailster' ), '<code>MAILSTER_LICENSE</code>' ) );
		}

		delete_transient( 'mailster_verified' );
		return UpdateCenterPlugin::reset( 'mailster', $license );

	}


	public function is_verified( $force = false ) {

		$license       = $this->license();
		$license_email = $this->email();
		$license_user  = $this->username();

		if ( ! $license || ! $license_email || ! $license_user ) {
			return false;
		}

		$verified = $this->get_verfied_object( $force );

		return is_array( $verified );

	}


	public function is_email_verified( $force = false ) {

		$verified = $this->get_verfied_object( $force );

		return is_array( $verified ) && isset( $verified['email_verfied'] ) && $verified['email_verfied'];
	}

	private function get_verfied_object( $force = false ) {

		$old = get_option( '_transient_mailster_verified', array() );

		if ( false === ( $verified = get_transient( 'mailster_verified' ) ) || $force ) {

			$verified = null;
			$recheck  = DAY_IN_SECONDS;

			$result = UpdateCenterPlugin::verify( 'mailster' );
			if ( ! is_wp_error( $result ) ) {
				$verified = $result;
			} else {
				switch ( $result->get_error_code() ) {
					case 500: // Internal Server Error
					case 503: // Service Unavailable
					case 'http_err':
						$recheck  = 900;
						$verified = $old;
						break;
					case 681: // no user assigned
						break;
				}
			}

			if ( null !== $verified ) {
				mailster_remove_notice( 'verify' );
			}

			set_transient( 'mailster_verified', $verified, $recheck );

		}

		return $verified;
	}

	public function has_update( $force = false ) {

		$new_version = $this->plugin_info( 'new_version', $force );

		return version_compare( $new_version, MAILSTER_VERSION, '>' );
	}

	public function is_outdated() {

		// make sure Mailster has been updated within a year
		return defined( 'MAILSTER_BUILT' ) && MAILSTER_BUILT && MAILSTER_BUILT + YEAR_IN_SECONDS < time();

	}

	public function has_support( $force = false ) {

		return $this->support( $force ) - time() > 0;

	}

	public function support( $force = false ) {

		$support = (int) $this->plugin_info( 'support', $force );

		if ( $support ) {
			$support += DAY_IN_SECONDS;
		}

		return $support;

	}


	public function get_plugin_hash( $force = false ) {

		if ( ! ( $hash = get_transient( 'mailster_hash' ) ) || $force ) {

			$files = list_files( MAILSTER_DIR, 100 );

			sort( $files );

			$hashes = array();

			foreach ( $files as $file ) {
				$file_parts = pathinfo( $file );
				if ( isset( $file_parts['extension'] ) && 'php' == $file_parts['extension'] ) {
					$hashes[] = md5_file( $file );
				}
			}

			$hash = md5( implode( '', $hashes ) );
			set_transient( 'mailster_hash', $hash, DAY_IN_SECONDS );

		}

		return $hash;

	}


	/**
	 *
	 *
	 * @param unknown $post_id
	 * @param unknown $part     (optional)
	 * @param unknown $meta_key (optional)
	 * @return unknown
	 */
	public function meta( $post_id, $part = null, $meta_key = null ) {

		$meta = get_post_meta( $post_id, $meta_key, true );

		if ( is_null( $part ) ) {
			return $meta;
		}

		if ( isset( $meta[ $part ] ) ) {
			return $meta[ $part ];
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $key
	 * @param unknown $value    (optional)
	 * @param unknown $meta_key (optional)
	 * @return unknown
	 */
	public function update_meta( $id, $key, $value = null, $meta_key = null ) {
		if ( is_array( $key ) ) {
			$meta = $key;
			return update_post_meta( $id, $meta_key, $meta );
		}
		$meta         = $this->meta( $id, null, $meta_key );
		$old          = isset( $meta[ $key ] ) ? $meta[ $key ] : '';
		$meta[ $key ] = $value;
		return update_post_meta( $id, $meta_key, $meta, $old );
	}


	/**
	 *
	 *
	 * @param unknown $post_states
	 * @param unknown $post
	 * @return unknown
	 */
	public function display_post_states( $post_states, $post ) {

		if ( is_mailster_newsletter_homepage() ) {
			$post_states['mailster_is_homepage'] = esc_html__( 'Newsletter Homepage', 'mailster' );
		}

		return $post_states;

	}


	/**
	 *
	 *
	 * @param unknown $postdata
	 * @param unknown $post
	 * @return unknown
	 */
	public function import_post_data( $postdata, $post ) {

		if ( ! isset( $postdata['post_type'] ) || $postdata['post_type'] != 'newsletter' ) {
			return $postdata;
		}

		kses_remove_filters();

		preg_match_all( '/(src|background|href)=["\'](.*)["\']/Ui', $postdata['post_content'], $links );
		$links = $links[2];

		$old_home_url = '';
		foreach ( $links as $link ) {
			if ( preg_match( '/(.*)wp-content(.*)\/mailster/U', $link, $match ) ) {
				$new_link                 = str_replace( $match[0], MAILSTER_UPLOAD_URI, $link );
				$old_home_url             = $match[1];
				$postdata['post_content'] = str_replace( $link, $new_link, $postdata['post_content'] );
			}
		}

		if ( $old_home_url ) {
			$postdata['post_content'] = str_replace( $old_home_url, trailingslashit( home_url() ), $postdata['post_content'] );
		}

		mailster_notice( esc_html__( 'Please make sure all your campaigns are imported correctly!', 'mailster' ), 'error', false, 'import_campaigns' );

		return $postdata;

	}

	public function convert_old_campaign_ids( $post_id, $original_post_ID, $postdata, $post ) {

		global $wpdb;

		if ( $postdata['post_type'] != 'newsletter' ) {
			return;
		}
		if ( $post_id == $original_post_ID ) {
			return;
		}

		$tables = array( 'actions', 'queue', 'subscriber_meta' );

		echo '<h4>';
		printf( esc_html__( 'Updating Mailster tables for Campaign %s:', 'mailster' ), '"<a href="' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . '">' . $postdata['post_title'] . '</a>"' );
		echo '</h4>';

		foreach ( $tables as $table ) {
			printf( '<code>%s</code>', 'mailster_' . $table );

			$sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}mailster_{$table} SET campaign_id = %d WHERE campaign_id = %d", $post_id, $original_post_ID );
			if ( false !== ( $rows = $wpdb->query( $sql ) ) ) {
				printf( '..' . esc_html__( 'completed for %d rows.', 'mailster' ), $rows );
			}
			echo '<br>';
		}

	}


	private function thirdpartystuff() {

		do_action( 'mailster_thirdpartystuff' );

		if ( function_exists( 'w3tc_objectcache_flush' ) ) {
			add_action( 'shutdown', 'w3tc_objectcache_flush' );
		}

		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			add_action( 'shutdown', 'wp_cache_clear_cache' );
		}

	}


}
