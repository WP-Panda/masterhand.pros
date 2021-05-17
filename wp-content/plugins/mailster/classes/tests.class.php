<?php

class MailsterTests {

	private $message;
	private $tests;
	private $current;
	private $next;
	private $total;

	private $errors;

	public function __construct( $test = null ) {

		if ( ! is_null( $test ) ) {
			$this->tests = is_array( $test ) ? array_keys( $test ) : array( $test => 0 );
		} else {
			$this->tests = $this->get_tests();
		}
		$this->total  = count( $this->tests );
		$this->errors = array(
			'count'         => 0,
			'error_count'   => 0,
			'warning_count' => 0,
			'notice_count'  => 0,
			'success_count' => 0,
			'all'           => array(),
			'error'         => array(),
			'warning'       => array(),
			'notice'        => array(),
			'success'       => array(),
		);

	}

	public function __call( $method, $args ) {

		switch ( $method ) {
			case 'error':
			case 'warning':
			case 'notice':
			case 'success':
				call_user_func_array( array( &$this, $method ), $args );
				break;
		}

		if ( method_exists( $this, 'test_' . $method ) ) {
			$this->run( $method );
			return ! $this->last_is_error;
		}

	}

	public function run( $test_id = null, $args = array() ) {

		if ( $test_id == null ) {
			$test_id = key( $this->tests );
		}

		if ( isset( $this->tests[ $test_id ] ) ) {

			$this->last_is_error      = false;
			$this->last_error_test    = null;
			$this->last_error_message = null;
			$this->last_error_type    = 'success';
			$this->last_error_link    = null;

			$this->current_id = $test_id;
			$this->current    = $this->tests[ $test_id ];

			try {
				if ( is_callable( $this->current ) ) {
					call_user_func_array( $this->current, array( $this ) );
				} elseif ( method_exists( $this, 'test_' . $test_id ) ) {
					call_user_func_array( array( &$this, 'test_' . $test_id ), $args );
				} else {
					$this->warning( 'Test \'' . $test_id . '\' does not exist!' );
				}
			} catch ( Exception $e ) {
				$this->error( $e );
			}
			return ! ( $this->last_error_test == $test_id );

		}

		return null;
	}

	public function get_tests() {

		$tests = get_class_methods( $this );
		$tests = preg_grep( '/^test_/', $tests );
		$tests = array_values( $tests );
		$tests = preg_replace( '/^test_/', '', $tests );
		$tests = array_flip( $tests );

		return apply_filters( 'mailster_tests', $tests );

	}

	public function get_message() {

		$time   = date( 'Y-m-d H:i:s' );
		$html   = '';
		$text   = '';
		$maxlen = max( array_map( 'strlen', array_keys( $this->get_tests() ) ) );

		foreach ( array( 'error', 'warning', 'notice', 'success' ) as $type ) {
			if ( ! $this->errors[ $type . '_count' ] ) {
				continue;
			}
			foreach ( $this->errors[ $type ] as $test_id => $test_errors ) {

				foreach ( $test_errors as $i => $error ) {
					$name  = $this->nicename( $test_id );
					$html .= '<div class="mailster-test-result mailster-test-is-' . $type . '"><h4>' . $name . ( $error['data']['link'] ? ' (<a class="mailster-test-result-link external" href="' . esc_url( $error['data']['link'] ) . '">' . esc_html__( 'More Info', 'mailster' ) . '</a>)' : '' ) . ' <a class="retest mailster-icon" href="' . add_query_arg( array( 'test' => $test_id ), admin_url( 'edit.php?post_type=newsletter&page=mailster_tests&autostart' ) ) . '">' . esc_html__( 'Test again', 'mailster' ) . '</a></h4><div class="mailster-test-result-more">' . nl2br( $error['msg'] ) . '</div></div>';
					if ( $type != 'success' ) {
						$text .= '[' . $type . '] ' . $test_id . ': ' . strip_tags( $error['msg'] ) . "\n";
					}
				}
			}
		}

		return array(
			'test' => $this->current_id,
			'time' => $time,
			'html' => $html,
			'text' => $text,
		);
	}

	public function nicename( $test ) {
		$test = ucwords( str_replace( array( 'test_', '_' ), array( '', ' ' ), $test ) );
		$test = str_replace( array( 'Php', 'Wordpress', 'Wp ', 'Db', 'Mymail' ), array( 'PHP', 'WordPress', 'WP ', 'DB ', 'MyMail' ), $test );
		return $test;
	}

	public function get_next() {

		foreach ( $this->tests as $key => $value ) {
			unset( $this->tests[ $key ] );

			if ( $key == $this->current_id ) {
				break;
			}
		}
		$next = key( $this->tests );
		return $next;
	}

	public function get_current() {
		return $this->nicename( $this->current_id );
	}
	public function get_current_type() {
		return $this->last_error_type;
	}

	public function get_total() {

		return $this->total;

	}
	public function get_error_counts() {

		return array(
			'error'   => $this->errors['error_count'],
			'warning' => $this->errors['warning_count'],
			'notice'  => $this->errors['notice_count'],
			'success' => $this->errors['success_count'],
		);

	}



	private function error( $msg, $link = null ) {

		$this->failure( 'error', $msg, $link );

	}


	private function warning( $msg, $link = null ) {

		$this->failure( 'warning', $msg, $link );

	}


	private function notice( $msg, $link = null ) {

		$this->failure( 'notice', $msg, $link );

	}

	private function success( $msg, $link = null ) {

		$this->failure( 'success', $msg, $link );

	}


	private function failure( $type, $msg, $link = null ) {

		$test_id = $this->current_id;

		if ( is_null( $test_id ) ) {
			$test_id = uniqid();
		}

		$data = array( 'link' => $link );
		if ( ! isset( $this->errors['all'][ $test_id ] ) ) {
			$this->errors['all'][ $test_id ] = array();
		}
		$this->errors['all'][ $test_id ][] = array(
			'msg'  => $msg,
			'data' => $data,
		);
		if ( ! isset( $this->errors[ $type ][ $test_id ] ) ) {
			$this->errors[ $type ][ $test_id ] = array();
		}
		$this->errors[ $type ][ $test_id ][] = array(
			'msg'  => $msg,
			'data' => $data,
		);
		$this->errors['count']++;
		$this->errors[ $type . '_count' ]++;

		$this->last_is_error      = 'success' != $type;
		$this->last_error_type    = $type;
		$this->last_error_test    = $test_id;
		$this->last_error_message = $msg;
		$this->last_error_link    = $link;

	}

	public function get() {
		return $this->errors['count'] ? $this->errors['all'] : true;
	}

	public function has( $type = null ) {
		if ( is_null( $type ) ) {
			return $this->errors['count'];
		} elseif ( isset( $this->errors[ $type ] ) ) {
			return $this->errors[ $type . '_count' ];
		}

		return false;
	}





	private function _test_error() {
		$this->error( 'This is an error error' );
	}
	private function _test_notice() {
		$this->notice( 'This is a notice error' );
	}
	private function _test_warning() {
		$this->warning( 'This is a warning error' );
	}
	private function _test_success() {
		$this->success( 'This is a success error' );
	}
	private function _test_multiple() {
		$this->error( 'This is a error error' );
		$this->notice( 'This is a notice error' );
		$this->warning( 'This is a warning error' );
		$this->success( 'This is a success error' );
	}

	private function test_php_version() {
		if ( version_compare( PHP_VERSION, '5.3' ) < 0 ) {
			$this->error( sprintf( 'Mailster requires PHP version 5.3 or higher. Your current version is %s. Please update or ask your hosting provider to help you updating.', PHP_VERSION ) );
		} elseif ( version_compare( PHP_VERSION, '7.0' ) < 0 ) {
			$this->notice( sprintf( 'Mailster recommends PHP version 7.0 or higher. Your current version is %s. Please update or ask your hosting provider to help you updating.', PHP_VERSION ) );
		} else {
			$this->success( 'You have version ' . PHP_VERSION );
		}
	}
	private function test_wordpress_version() {
		$update  = get_preferred_from_update_core();
		$current = get_bloginfo( 'version' );
		if ( version_compare( $current, '3.8' ) < 0 ) {
			$this->error( sprintf( 'Mailster requires WordPress version 3.8 or higher. Your current version is %s.', $current ) );
		} elseif ( $update && $update->response == 'upgrade' && version_compare( $update->current, $current ) ) {
			$this->warning( sprintf( 'Your WordPress site is not up-to-date! Version %1$s is available. Your current version is %2$s.', $update->current, $current ) );
		} else {
			$this->success( 'You have version ' . $current );

		}
	}
	private function test_verfied_installation() {

		if ( ! mailster()->is_verified() ) {
			$this->error( 'Your Mailster installation is not verified! Please register via your <a href="' . admin_url( 'admin.php?page=mailster_dashboard' ) . '">dashboard</a>.' );
		} else {
			$this->success( 'Thank you!' );
		}

	}
	private function test_update_available() {

		$plugin_info = mailster()->plugin_info();

		if ( $plugin_info->update ) {
			$this->warning( sprintf( 'A new version of Mailster is available! Please %1$s to version %2$s', '<a href="update.php?action=upgrade-plugin&plugin=' . urlencode( MAILSTER_SLUG ) . '&_wpnonce=' . wp_create_nonce( 'upgrade-plugin_' . MAILSTER_SLUG ) . '">update now</a>', $plugin_info->new_version ) );
		} else {
			$this->success( 'You are running the latest version of Mailster!' );
		}

	}
	private function test_content_directory() {
		$content_dir = MAILSTER_UPLOAD_DIR;
		if ( ! is_dir( dirname( $content_dir ) ) ) {
			wp_mkdir_p( dirname( $content_dir ) );
		}
		if ( ! is_dir( dirname( $content_dir ) ) || ! wp_is_writable( dirname( $content_dir ) ) ) {
			$this->warning( sprintf( 'Your content folder in %s is not writable.', '"' . dirname( $content_dir ) . '"' ) );
		} else {
			$this->success( sprintf( 'Your content folder in %s is writable.', '"' . dirname( $content_dir ) . '"' ) );
		}

		if ( ! is_dir( $content_dir ) ) {
			wp_mkdir_p( $content_dir );
		}
		if ( ! is_dir( $content_dir ) || ! wp_is_writable( $content_dir ) ) {
			$this->warning( sprintf( 'Your Mailster content folder in %s is not writable.', '"' . $content_dir . '"' ) );
		} else {
			$this->success( sprintf( 'Your Mailster content folder in %s is writable.', '"' . $content_dir . '"' ) );
		}

	}
	private function test_default_template() {

		$default      = 'mymail';
		$template     = mailster_option( 'default_template' );
		$template_dir = trailingslashit( MAILSTER_UPLOAD_DIR ) . 'templates/' . $template;

		if ( ! is_dir( dirname( $template_dir ) ) ) {
			wp_mkdir_p( dirname( $template_dir ) );
		}
		if ( ! is_dir( dirname( $template_dir ) ) || ! wp_is_writable( dirname( $template_dir ) ) ) {
			$this->warning( sprintf( 'Your Template folder %s doesn\'t exists or is not writeable.', '"' . dirname( $template_dir ) . '"' ) );

		} else {
			$this->success( sprintf( 'Your Template folder %s exists.', '"' . dirname( $template_dir ) . '"' ) );
		}
		if ( ! is_dir( $template_dir ) || ! wp_is_writable( $template_dir ) ) {
			$default_template_dir = trailingslashit( MAILSTER_UPLOAD_DIR ) . 'templates/' . $default;
			if ( ! is_dir( $default_template_dir ) || ! wp_is_writable( $default_template_dir ) ) {
				$result = mailster( 'templates' )->renew_default_template( $default );
				if ( is_wp_error( $result ) ) {
					$this->warning( sprintf( 'Your Template folder %s doesn\'t exists or is not writeable.', '"' . $template_dir . '"' ) );
					$this->error( 'Not able to download default template.' );
				} else {
					$this->notice( sprintf( 'Default template loaded to %s.', '"' . $default_template_dir . '"' ) );
					if ( mailster_update_option( 'default_template', $default ) ) {
						$this->notice( sprintf( 'Default template changed to %s.', '"' . $default . '"' ) );
					}
				}
			}
		} else {
			$this->success( sprintf( 'Your Template folder %s exists.', '"' . $template_dir . '"' ) );
		}

	}
	private function test_deprecated_hooks() {

		global $wp_filter;
		$hooks = array_values( preg_grep( '/^mymail_/', array_keys( $wp_filter ) ) );

		if ( ! empty( $hooks ) ) {
			$msg = 'Following deprecated MyMail hooks were found and should get replaced:<ul>';
			foreach ( $hooks as $hook ) {

				$msg .= '<li><code>' . $hook . '</code> => <code>' . str_replace( 'mymail', 'mailster', $hook ) . '</code>';
				foreach ( array_values( $wp_filter[ $hook ]->callbacks ) as $data ) {
					foreach ( $data as $id => $entries ) {
						if ( is_string( $entries['function'] ) ) {
							continue;
						} elseif ( $entries['function'] instanceof Closure ) {
							$reflFunc = new ReflectionFunction( $entries['function'] );
						} else {
							$reflFunc = new ReflectionMethod( $entries['function'][0], $entries['function'][1] );
						}
						$plugin_path = $reflFunc->getFileName();
						$msg        .= '<br>found in ' . $plugin_path;
					}
				}
				$msg .= '</li>';

			}
			$msg .= '</ul>';

			$this->warning( $msg );

		}

	}
	private function test_support_account_found() {

		global $wpdb;
		$support_email_hashes = array( 'a51736698df8f7301e9d0296947ea093', 'fc8df74384058d87d20f10b005bb6c82', 'c7614bd4981b503973ca42aa6dc7715d', 'eb33c92faf9d2c6b12df7748439b8a82' );

		foreach ( $support_email_hashes as $hash ) {

			$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->users} WHERE md5(`user_email`) = %s AND user_registered < (NOW() - INTERVAL 60 MINUTE)", $hash ) );
			if ( $user ) {
				$this->warning( sprintf( 'Please remove any unused support account: %s', '<a href="' . admin_url( 'users.php?s=' . urlencode( $user->user_email ) ) . '">' . $user->user_email . '</a>' ) );
			}
		}
	}
	private function _test_tinymce_access() {

		$file     = includes_url( 'js/tinymce/' ) . 'wp-tinymce.php';
		$response = wp_remote_post( $file );
		$code     = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) ) {
			$this->warning( sprintf( 'The Mailster Editor requires TinyMCE and access to the file %1$s which seems to be blocked by your host. [%2$s]', '"' . $file . '"', $response->get_error_message() ) );
		} elseif ( $code != 200 ) {
			$this->warning( sprintf( 'The Mailster Editor requires TinyMCE and access to the file %1$s which seems to be blocked by your host. [Error Code %2$s]', '"' . $file . '"', $code ) );
		}

	}
	private function test_custom_language() {
		if ( file_exists( $custom = MAILSTER_UPLOAD_DIR . '/languages/mailster-' . get_locale() . '.mo' ) ) {
			$this->notice( sprintf( 'Custom Language file found in %s', $custom ) );
		}
	}
	private function test_geo_database() {

		if ( mailster_option( 'track_location' ) ) {

			$geo = mailster( 'geo' );

			$warnings = array();

			if ( ! file_exists( $path = $geo->get_file_path( 'country' ) ) ) {
				$warnings[] = sprintf( 'The Country Database was not found in %s', $path );
			}
			if ( ! file_exists( $path = $geo->get_file_path( 'city' ) ) ) {
				$warnings[] = sprintf( 'The City Database was not found in %s', $path );
			}

			if ( ! empty( $warnings ) ) {
				$this->warning( implode( '<br>', $warnings ), admin_url( 'edit.php?post_type=newsletter&page=mailster_settings&mailster_remove_notice=newsletter_homepage#privacy' ) );
			}
		}
	}
	private function test_wp_debug() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			if ( function_exists( 'wp_get_environment_type' ) && 'production' == wp_get_environment_type() ) {
				$this->failure( ( mailster_is_local() ? 'notice' : 'warning' ), 'WP_DEBUG is enabled and should be disabled on a production site.', 'https://codex.wordpress.org/WP_DEBUG' );
			}
		}
	}
	private function test_dom_document_extension() {
		if ( ! class_exists( 'DOMDocument' ) ) {
			$this->error( 'Mailster requires the <a href="https://php.net/manual/en/class.domdocument.php" target="_blank" rel="noopener">DOMDocument</a> library.' );
		}
	}
	private function test_fsockopen_extension() {
		if ( ! function_exists( 'fsockopen' ) ) {
			$this->warning( 'Your server does not support <a href="https://php.net/manual/en/function.fsockopen.php" target="_blank" rel="noopener">fsockopen</a>.' );
		}
	}
	private function test_database_structure() {

		global $wpdb;

		$set_charset = true;
		$result      = mailster()->dbstructure( false, true, $set_charset, false );

		if ( false !== strpos( $result, 'Unknown character set:' ) ) {
			$set_charset = false;
			$result      = mailster()->dbstructure( false, true, $set_charset, false );
		}

		if ( true !== $result ) {
			$second_result = mailster()->dbstructure( false, true, $set_charset, false );
			if ( $result === $second_result ) {
				$this->error( $result );
			} else {
				$this->notice( $result );
			}
		}

		if ( false === mailster( 'subscribers' )->wp_id() ) {
			$status = $wpdb->get_row( $wpdb->prepare( 'SHOW TABLE STATUS LIKE %s', $wpdb->users ) );
			if ( isset( $status->Collation ) ) {
				$tables = mailster()->get_tables( true );

				foreach ( $tables as $table ) {
					$sql = $wpdb->prepare( 'ALTER TABLE %s CONVERT TO CHARACTER SET utf8mb4 COLLATE %s', $table, $status->Collation );
					if ( false !== $wpdb->query( $sql ) ) {
						$this->notice( "'$table' converted to {$status->Collation}" );
					}
				}
			}
		}

		// reset auto increments
		$wpdb->query( $wpdb->prepare( "ALTER TABLE {$wpdb->prefix}mailster_subscribers AUTO_INCREMENT = %d", 1 ) );
		$wpdb->query( $wpdb->prepare( "ALTER TABLE {$wpdb->prefix}mailster_forms AUTO_INCREMENT = %d", 1 ) );
		$wpdb->query( $wpdb->prepare( "ALTER TABLE {$wpdb->prefix}mailster_lists AUTO_INCREMENT = %d", 1 ) );
		$wpdb->query( $wpdb->prepare( "ALTER TABLE {$wpdb->prefix}mailster_links AUTO_INCREMENT = %d", 1 ) );

		// remove temp table
		delete_option( 'mailster_bulk_import' );
		delete_option( 'mailster_bulk_import_errors' );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mailster_temp_import" );

	}
	private function test_memory_limit() {
		$max = max( (int) ini_get( 'memory_limit' ), (int) WP_MAX_MEMORY_LIMIT, (int) WP_MEMORY_LIMIT );
		if ( $max < 128 ) {
			$this->warning( 'Your Memory Limit is ' . size_format( $max * 1048576 ) . ', Mailster recommends at least 128 MB' );
		} else {
			$this->success( 'Your Memory Limit is ' . size_format( $max * 1048576 ) );
		}
	}
	private function test_plugin_location() {
		if ( MAILSTER_SLUG != 'mailster/mailster.php' ) {
			$this->warning( 'You have changed the plugin location of Mailster. This can cause problems while updating the plugin.' );
		}
	}
	private function test_mailster_folder_in_root() {
		if ( file_exists( ABSPATH . 'mailster' ) || is_dir( ABSPATH . 'mailster' ) ) {
			$this->error( 'There\'s a file or folder called \'mailster\' in ' . ABSPATH . ' which causes a conflict with campaign links! Please remove or rename this folder.' );
		} else {
			$this->success( 'There\'s no file or folder called \'mailster\' in ' . ABSPATH . '.' );
		}
	}
	private function test_working_cron() {

		$cron_status = mailster( 'cron' )->check( true );

		if ( is_wp_error( $cron_status ) ) {
			switch ( $cron_status->get_error_code() ) {
				case 'cron_error':
					$this->error( $cron_status->get_error_message(), 'https://kb.mailster.co/how-do-i-know-if-my-cron-is-working-correctly/' );
					break;
				default:
					$this->warning( $cron_status->get_error_message(), 'https://kb.mailster.co/how-do-i-know-if-my-cron-is-working-correctly/' );
					break;
			}
		} else {
			if ( $last_hit = get_option( 'mailster_cron_lasthit' ) ) {
				$this->success( sprintf( esc_html__( 'Last hit was %s ago', 'mailster' ), human_time_diff( $last_hit['timestamp'] ) ) );
			}
		}
		return;
	}
	private function test_cron_lock() {

		mailster( 'cron' )->lock();

		if ( ! mailster( 'cron' )->is_locked() ) {
			$this->warning( 'Cron Lock mechanism is not working with the current method.', 'https://kb.mailster.co/what-is-a-cron-lock/' );
		} else {
			$this->success( 'No Cron Lock in place!' );
		}
		mailster( 'cron' )->unlock();

	}
	private function test_mail_throughput() {
		if ( $last_hit = get_option( 'mailster_cron_lasthit' ) ) {

			if ( ! isset( $last_hit['mail'] ) || ! $last_hit['mail'] ) {
				return;
			}
			$mails_per_sec = round( 1 / $last_hit['mail'], 2 );
			$mails_per_sec = sprintf( esc_html__( _n( '%s mail per second', '%s mails per second', $mails_per_sec, 'mailster' ) ), $mails_per_sec );

			if ( $last_hit['mail'] > 1 ) {
				$this->warning( 'Your mail throughput is low. (' . $mails_per_sec . ')', 'https://kb.mailster.co/how-can-i-increase-the-sending-speed/' );
			} else {
				$this->success( 'Your mail throughput is ok. (' . $mails_per_sec . ')', 'https://kb.mailster.co/how-can-i-increase-the-sending-speed/' );
			}
		}
	}
	private function test_newsletter_homepage() {

		$hp = get_post( mailster_option( 'homepage' ) );

		if ( ! $hp || $hp->post_status == 'trash' ) {

			$this->error( sprintf( esc_html__( 'You haven\'t defined a homepage for the newsletter. This is required to make the subscription form work correctly. Please check the %1$s or %2$s.', 'mailster' ), '<a href="edit.php?post_type=newsletter&page=mailster_settings&mailster_remove_notice=newsletter_homepage#frontend">' . esc_html__( 'frontend settings page', 'mailster' ) . '</a>', '<a href="' . add_query_arg( 'mailster_create_homepage', wp_create_nonce( 'mailster_create_homepage' ), admin_url() ) . '">' . esc_html__( 'create it right now', 'mailster' ) . '</a>' ), 'https://kb.mailster.co/how-can-i-setup-the-newsletter-homepage/' );
			return;

		} elseif ( $hp->post_status != 'publish' ) {

			$this->error( sprintf( esc_html__( 'Your newsletter homepage is not visible. Please update %s.', 'mailster' ), '<a href="post.php?post=' . $hp->ID . '&action=edit&mailster_remove_notice=newsletter_homepage">' . esc_html__( 'this page', 'mailster' ) . '</a>' ), 'https://kb.mailster.co/how-can-i-setup-the-newsletter-homepage/' );

		}

		if ( ! preg_match( '#\[newsletter_signup\]#', $hp->post_content )
			|| ! preg_match( '#\[newsletter_signup_form#', $hp->post_content )
			|| ! preg_match( '#\[newsletter_confirm\]#', $hp->post_content )
			|| ! preg_match( '#\[newsletter_unsubscribe\]#', $hp->post_content ) ) {

			$this->error( sprintf( esc_html__( 'Your newsletter homepage is not setup correctly. Please update %s.', 'mailster' ), '<a href="post.php?post=' . $hp->ID . '&action=edit">' . esc_html__( 'this page', 'mailster' ) . '</a>' ), 'https://kb.mailster.co/how-can-i-setup-the-newsletter-homepage/' );

		}

		if ( preg_match( '#\[newsletter_signup_form id="?(\d+)"?#i', $hp->post_content, $matches ) ) {
			$form_id = (int) $matches[1];
			if ( ! mailster( 'forms' )->get( $form_id ) ) {
				$this->error( sprintf( esc_html__( 'The form with id %1$s doesn\'t exist. Please update %2$s.', 'mailster' ), $form_id . ' (<code>' . $matches[0] . ']</code>)', '<a href="post.php?post=' . $hp->ID . '&action=edit">' . esc_html__( 'this page', 'mailster' ) . '</a>' ), 'https://kb.mailster.co/how-can-i-setup-the-newsletter-homepage/' );
			}
		}

	}
	private function test_form_exist() {

		$forms = mailster( 'forms' )->get_all();

		if ( ! count( $forms ) ) {
			$this->error( sprintf( esc_html__( 'You have no form! Mailster requires at least one form for the newsletter homepage. %s.', 'mailster' ), '<a href="edit.php?post_type=newsletter&page=mailster_forms&new">' . esc_html__( 'Create a new form now', 'mailster' ) . '</a>' ) );
		}

	}
	private function test_update_server_connection() {

		if ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL ) {
			$this->error( 'Constant WP_HTTP_BLOCK_EXTERNAL defined' );
		}

		$response = wp_remote_post( apply_filters( 'mailster_updatecenter_endpoint', 'https://update.mailster.co/' ) );
		$code     = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) ) {
			$this->error( $response->get_error_message() . ' - Please allow connection to update.mailster.co!' );
		} elseif ( $code >= 200 && $code < 300 ) {
		} else {
			$this->error( 'does not work: Error code ' . $code );
		}
	}
	private function _test_TLS() {
		$response = wp_remote_post(
			'https://www.howsmyssl.com/a/check',
			array(
				'sslverify' => true,
				'timeout'   => 5,
			)
		);

		$code = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) ) {
			$this->error( 'does not work: ' . $response->get_error_message() );
		} elseif ( $code >= 200 && $code < 300 ) {
			$body = json_decode( wp_remote_retrieve_body( $response ) );
			switch ( $body->rating ) {
				case 'Probably Okay':
					$this->success( 'Version: ' . $body->tls_version . '; ' . $body->rating );
					break;
				case 'Improvable':
					$this->warning( 'Version: ' . $body->tls_version . '; ' . $body->rating );
					break;
				case 'Bad':
					$this->error( 'Version: ' . $body->tls_version . '; ' . $body->rating );
					break;
			}
		} else {
			$this->error( 'does not work: ' . $code );
		}

	}
	private function test_mailfunction() {

		$to      = 'deadend@newsletter-plugin.com';
		$subject = 'This is a test mail from the Mailster Test page';
		$message = 'This test message can sent from ' . admin_url( 'edit.php?post_type=newsletter&page=mailster_tests' ) . ' and can get deleted.';

		$mail          = mailster( 'mail' );
		$mail->to      = $to;
		$mail->subject = $subject;
		$mail->debug();

		if ( ! $mail->send_notification( 'Sendtest', $message, array( 'notification' => '' ), false ) ) {
			$error_message = strip_tags( $mail->get_errors() );
			$msg           = 'You are not able to send mails with the current delivery settings!';

			if ( false !== stripos( $error_message, 'smtp connect()' ) ) {
				$this->error( $msg . '<br>' . $error_message, 'https://kb.mailster.co/smtp-error-could-not-connect-to-smtp-host/' );
			} elseif ( false !== stripos( $error_message, 'data not accepted' ) ) {
				$this->error( $msg . '<br>' . $error_message, 'https://kb.mailster.co/smtp-error-data-not-accepted/' );
			} else {
				$this->error( $msg . '<br>' . $error_message );
			}
		} else {
			$this->success( 'Email was successfully delivery to ' . $to );
		}

		if ( mailster_option( 'system_mail' ) ) {

			add_action( 'wp_mail_failed', array( $this, 'wp_mail_failed' ) );
			if ( $response = wp_mail( $to, '[wp_mail] ' . $subject, $message ) ) {
				$this->success( '[wp_mail] Email was successfully delivery to ' . $to );
			}
			remove_action( 'wp_mail_failed', array( $this, 'wp_mail_failed' ) );

		}

	}
	public function wp_mail_failed( $error ) {
		$error_message = strip_tags( $error->get_error_message() );
		$msg           = 'You are not able to use <code>wp_mail()</code> with Mailster';

		if ( false !== stripos( $error_message, 'smtp connect()' ) ) {
			$this->error( $msg . '<br>' . $error_message, 'https://kb.mailster.co/smtp-error-could-not-connect-to-smtp-host/' );
		} elseif ( false !== stripos( $error_message, 'data not accepted' ) ) {
			$this->error( $msg . '<br>' . $error_message, 'https://kb.mailster.co/smtp-error-data-not-accepted/' );
		} else {
			$this->error( $msg . '<br>' . $error_message );
		}
	}
	private function test_db_version() {
		if ( get_option( 'mailster_dbversion' ) != MAILSTER_DBVERSION ) {
			$this->error( 'Your current DB version is ' . get_option( 'mailster_dbversion' ) . ' and should be ' . MAILSTER_DBVERSION . '. Please visit the <a href="' . admin_url( 'admin.php?page=mailster_update' ) . '">update page</a> to run necessary updates.' );

		}
	}
	private function test_delivery_port() {
		if ( 'smtp' == mailster_option( 'deliverymethod' ) ) {
			$this->port_test( mailster_option( 'smtp_port' ), mailster_option( 'smtp_host' ), true );
		}
	}
	private function test_bounce_port() {

		if ( mailster_option( 'bounce_active' ) ) {
			$this->port_test( mailster_option( 'bounce_port' ), mailster_option( 'bounce_server' ), true );
		}
	}
	private function _test_port_110() {

		$this->port_test( 110, 'pop.gmx.net' );

	}
	private function _test_port_995() {

		$this->port_test( 995, 'pop.gmail.com' );

	}
	private function _test_port_993() {

		$this->port_test( 993, 'smtp.gmail.com' );

	}
	private function _test_port_25() {

		$this->port_test( 25, 'smtp.gmail.com' );

	}
	private function _test_port_2525() {

		$this->port_test( 2525, 'smtp.sparkpostmail.com' );

	}
	private function _test_port_465() {

		$this->port_test( 465, 'smtp.gmail.com' );

	}
	private function _test_port_587() {

		$this->port_test( 587, 'smtp.gmail.com' );

	}
	private function test_permalink_structure() {

		if ( ! mailster( 'helper' )->using_permalinks() ) {
			$this->notice( 'You are not using a permalink structure. Define one <a href="' . admin_url( 'options-permalink.php' ) . '">here</a>.' );
		} elseif ( ! mailster()->check_link_structure() ) {
			$this->error( 'A problem with you permalink structure. Please check the slugs on the <a href="' . admin_url( 'admin.php?page=mailster_settings#frontend' ) . '">frontend tab</a>.' );
		}

	}





	private function port_test( $port, $domain, $strict = false ) {

		$result = mailster( 'settings' )->check_port( $domain, $port );
		if ( strpos( $result, 'open' ) !== false ) {
			$this->success( sprintf( 'Port %s is open an can be used!', '<strong>' . $port . '</strong>' ) . ' <code>' . $result . '</code>' );
		} else {
			$message = sprintf( 'Port %s is NOT open an cannot be used!', '<strong>' . $port . '</strong>' ) . ' <code>' . $result . '</code>';
			if ( $strict ) {
				$this->error( $message );
			} else {
				$this->notice( $message );
			}
		}

	}

}
