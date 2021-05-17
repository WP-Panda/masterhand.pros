<?php

/**
 *
 *
 * @param unknown $subclass (optional)
 * @return unknown
 */
function mailster( $subclass = null ) {
	global $mailster;

	$args     = func_get_args();
	$subclass = array_shift( $args );

	if ( is_null( $subclass ) ) {
		return $mailster;
	}

	return call_user_func_array( array( $mailster, $subclass ), $args );

}


/**
 *
 *
 * @param unknown $option
 * @param unknown $fallback (optional)
 * @return unknown
 */
function mailster_option( $option, $fallback = null ) {

	$mailster_options = mailster_options();

	$value = isset( $mailster_options[ $option ] ) ? $mailster_options[ $option ] : $fallback;
	$value = apply_filters( 'mailster_option', $value, $option, $fallback );
	$value = apply_filters( 'mailster_option_' . $option, $value, $fallback );

	return $value;

}


/**
 *
 *
 * @param unknown $option   (optional)
 * @param unknown $fallback (optional)
 * @return unknown
 */
function mailster_options( $option = null, $fallback = null ) {

	if ( ! is_null( $option ) ) {
		return mailster_option( $option, $fallback );
	}

	if ( ! ( $options = get_option( 'mailster_options', array() ) ) ) {
		if ( mailster() ) {
			$options = mailster( 'settings' )->maybe_repair_options( $options );
		}
	}

	return $options;
}


/**
 *
 *
 * @return unknown
 */
function mailster_version() {
	return defined( 'MAILSTER_VERSION' ) ? MAILSTER_VERSION : null;
}


if ( function_exists( 'wp_cache_add_non_persistent_groups' ) ) {
	wp_cache_add_non_persistent_groups( array( 'mailster' ) );
}


/**
 *
 *
 * @param unknown $key
 * @param unknown $data
 * @param unknown $expire (optional)
 * @return unknown
 */
function mailster_cache_add( $key, $data, $expire = 0 ) {
	if ( mailster_option( 'disable_cache' ) ) {
		return true;
	}

	return wp_cache_add( $key, $data, 'mailster', $expire );
}


/**
 *
 *
 * @param unknown $key
 * @param unknown $data
 * @param unknown $expire (optional)
 * @return unknown
 */
function mailster_cache_set( $key, $data, $expire = 0 ) {
	if ( mailster_option( 'disable_cache' ) ) {
		return true;
	}

	return wp_cache_set( $key, $data, 'mailster', $expire );
}


/**
 *
 *
 * @param unknown $key
 * @param unknown $force (optional)
 * @param unknown $found (optional, reference)
 * @return unknown
 */
function mailster_cache_get( $key, $force = false, &$found = null ) {
	if ( mailster_option( 'disable_cache' ) ) {
		return false;
	}

	return wp_cache_get( $key, 'mailster', $force, $found );
}


/**
 *
 *
 * @param unknown $key
 * @return unknown
 */
function mailster_cache_delete( $key ) {
	return wp_cache_delete( $key, 'mailster' );
}


/**
 *
 *
 * @param unknown $option
 * @param unknown $fallback (optional)
 * @return unknown
 */
function mailster_text( $option, $fallback = '' ) {

	$mailster_texts = mailster_texts();

	$string = isset( $mailster_texts[ $option ] ) ? $mailster_texts[ $option ] : $fallback;

	return apply_filters( 'mymail_text', apply_filters( 'mailster_text', $string, $option, $fallback ), $option, $fallback );
}


/**
 *
 *
 * @return unknown
 */
function mailster_texts() {
	return get_option( 'mailster_texts', array() );
}


/**
 *
 *
 * @param unknown $option
 * @param unknown $value  (optional)
 * @param unknown $temp   (optional)
 * @return unknown
 */
function mailster_update_option( $option, $value = null, $temp = false ) {

	$mailster_options = mailster_options();

	if ( is_array( $option ) ) {
		$temp             = (bool) $value;
		$mailster_options = wp_parse_args( $option, $mailster_options );
	} else {
		$mailster_options[ $option ] = apply_filters( 'mailster_update_option_' . $option, $value, $temp );
	}

	if ( $temp ) {
		$temp_options = mailster( 'settings' )->verify( $mailster_options );

		add_filter(
			'pre_option_mailster_options',
			function() use ( $temp_options ) {
				return $temp_options;
			}
		);

		return true;
	}

	return update_option( 'mailster_options', $mailster_options );
}


/**
 *
 *
 * @param unknown $custom_fields (optional)
 * @return unknown
 */
function mailster_get_current_user( $custom_fields = true ) {

	return mailster( 'subscribers' )->get_current_user( $custom_fields );

}


/**
 *
 *
 * @return unknown
 */
function mailster_get_current_user_id() {

	return mailster( 'subscribers' )->get_current_user_id();

}
/**
 *
 *
 * @return unknown
 */
function mailster_localize_script( $hook, $strings = array() ) {

	if ( is_array( $hook ) ) {
		$strings = $hook;
		$hook    = 'common';
	}

	add_filter(
		'mailster_localize_script',
		function( $array ) use ( $hook, $strings ) {
			if ( isset( $array[ $hook ] ) ) {
				$array[ $hook ] += $strings;
			} else {
				$array[ $hook ] = $strings;
			}
			return $array;
		}
	);

}


/**
 *
 *
 * @param unknown $post_type
 * @param unknown $args      (optional)
 * @param unknown $callback  (optional)
 * @param unknown $priority  (optional)
 * @return unknown
 */
function mailster_register_dynamic_post_type( $post_type, $args = array(), $callback = null, $priority = 10 ) {
	if ( ! class_exists( 'WP_Post_Type' ) ) {
		return false;
	}

	$args = wp_parse_args(
		$args,
		array(
			'labels' => array( 'name' => is_string( $args ) ? $args : ucwords( str_replace( '_', ' ', $post_type ) ) ),
		)
	);

	// add additional post type
	add_filter(
		'mailster_dynamic_post_types',
		function( $post_types, $output ) use ( $post_type, $args ) {

			if ( 'names' == $output ) {
				$post_types[ $post_type ] = $post_type;
			} else {
				$post_types[ $post_type ] = new WP_Post_Type( $post_type, $args );
			}

			return $post_types;

		},
		10,
		2
	);

	// filter in placeholder
	add_filter(
		'mailster_get_last_post_' . $post_type,
		function( $return, $args, $offset, $term_ids, $campaign_id, $subscriber_id ) use ( $callback ) {

			if ( is_callable( $callback ) ) {
				$random = false;
				if ( isset( $args['mailster_identifier'] ) ) {
					$random = (int) $args['mailster_identifier'];
					unset( $args['mailster_identifier'] );
					unset( $args['mailster_identifier_key'] );
				}
				if ( $return = call_user_func_array( $callback, array( $offset, $campaign_id, $subscriber_id, $term_ids, $args, $random ) ) ) {
					$return = new WP_Post( (object) $return );
				}
			}

			return $return;

		},
		(int) $priority,
		6
	);

	return true;
}

/**
 *
 *
 * @param unknown $id          (optional)
 * @param unknown $echo        (optional)
 * @param unknown $classes     (optional)
 * @param unknown $depreciated (optional)
 * @return unknown
 */
function mailster_form( $id = 1, $echo = true, $classes = '', $depreciated = '' ) {

	// tabindex is depreciated but for backward compatibility.
	if ( is_int( $echo ) ) {
		$classes = $depreciated;
		$echo    = $classes;
	}

	$form = mailster( 'form' )->id( $id );
	$form->add_class( $classes );
	$form = $form->render( false );

	if ( $echo ) {
		echo $form;
	} else {
		return $form;
	}
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mailster_get_active_campaigns( $args = '' ) {

	return mailster( 'campaigns' )->get_active( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mailster_get_paused_campaigns( $args = '' ) {

	return mailster( 'campaigns' )->get_paused( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mailster_get_queued_campaigns( $args = '' ) {

	return mailster( 'campaigns' )->get_queued( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mailster_get_draft_campaigns( $args = '' ) {

	return mailster( 'campaigns' )->get_drafted( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mailster_get_finished_campaigns( $args = '' ) {

	return mailster( 'campaigns' )->get_finished( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mailster_get_pending_campaigns( $args = '' ) {

	return mailster( 'campaigns' )->get_pending( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mailster_get_autoresponder_campaigns( $args = '' ) {

	return mailster( 'campaigns' )->get_autoresponder( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mailster_get_campaigns( $args = '' ) {

	return mailster( 'campaigns' )->get_campaigns( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mailster_list_newsletter( $args = '' ) {
	$defaults = array(
		'title_li'    => esc_html__( 'Newsletters', 'mailster' ),
		'post_type'   => 'newsletter',
		'post_status' => array( 'finished', 'active' ),
		'echo'        => 1,
	);

	$r = wp_parse_args( $args, $defaults );

	extract( $r, EXTR_SKIP );

	$output = '';

	// sanitize, mostly to keep spaces out.
	$r['exclude'] = preg_replace( '/[^0-9,]/', '', $r['exclude'] );

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array).
	$exclude_array = ( $r['exclude'] ) ? explode( ',', $r['exclude'] ) : array();
	$r['exclude']  = implode( ',', apply_filters( 'mymail_list_newsletter_excludes', apply_filters( 'mailster_list_newsletter_excludes', $exclude_array ) ) );

	$newsletters = get_posts( $r );

	if ( ! empty( $newsletters ) ) {
		if ( $r['title_li'] ) {
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';
		}

		foreach ( $newsletters as $newsletter ) {
			$output .= '<li class="newsletter_item newsletter-item-' . $newsletter->ID . '"><a href="' . get_permalink( $newsletter->ID ) . '">' . $newsletter->post_title . '</a></li>';
		}

		if ( $r['title_li'] ) {
			$output .= '</ul></li>';
		}
	}

	$output = apply_filters( 'mymail_list_newsletter', apply_filters( 'mailster_list_newsletter', $output, $r ), $r );

	if ( $r['echo'] ) {
		echo $output;
	} else {
		return $output;
	}

}


/**
 *
 *
 * @param unknown $ip  (optional)
 * @param unknown $get (optional)
 * @return unknown
 */
function mailster_ip2Country( $ip = '', $get = 'code' ) {

	if ( ! mailster_option( 'track_location' ) ) {
		return 'unknown';
	}

	try {

		if ( empty( $ip ) ) {
			$ip = mailster_get_ip();
		}

		$ip2Country = mailster( 'geo' )->Ip2Country();

		$code = $ip2Country->get( $ip, $get );

		if ( ! $code ) {
			$code = mailster_ip2City( $ip, $get ? 'country_' . $get : null );
		}
		return ( $code ) ? $code : 'unknown';

	} catch ( Exception $e ) {
		return 'error';
	}
}


/**
 *
 *
 * @param unknown $ip  (optional)
 * @param unknown $get (optional)
 * @return unknown
 */
function mailster_ip2City( $ip = '', $get = null ) {

	if ( ! mailster_option( 'track_location' ) ) {
		return 'unknown';
	}

	$geo = mailster( 'geo' );

	$code = $geo->get_city_by_ip( $ip, $get );

	return ( $code ) ? $code : 'unknown';

}


/**
 *
 *
 * @return unknown
 */
function mailster_get_ip() {

	$ip = apply_filters( 'mymail_get_ip', apply_filters( 'mailster_get_ip', null ) );

	if ( ! is_null( $ip ) ) {
		return $ip;
	}

	$ip = '';
	foreach ( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key ) {
		if ( isset( $_SERVER[ $key ] ) ) {
			foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
				$ip = trim( $ip );
				if ( mailster_validate_ip( $ip ) ) {
					return $ip;
				}
			}
		}
	}
	return $ip;
}


/**
 *
 *
 * @return unknown
 */
function mailster_is_local() {

	return ! filter_var( mailster_get_ip(), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );

}


/**
 *
 *
 * @param unknown $ip
 * @return unknown
 */
function mailster_validate_ip( $ip ) {

	if ( strtolower( $ip ) === 'unknown' ) {
		return false;
	}

	return filter_var( $ip, FILTER_VALIDATE_IP );

}


/**
 *
 *
 * @param unknown $fallback (optional)
 * @return unknown
 */
function mailster_get_lang( $fallback = false ) {

	return isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? strtolower( substr( trim( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ), 0, 2 ) ) : $fallback;

}


/**
 *
 *
 * @param unknown $string (optional)
 * @return unknown
 */
function mailster_get_user_client( $string = null ) {

	$client = apply_filters( 'mymail_get_user_client', apply_filters( 'mailster_get_user_client', null ) );

	if ( ! is_null( $client ) ) {
		return $client;
	}

	require_once MAILSTER_DIR . 'classes/libs/MailsterUserAgent.php';
	$agent  = new MailsterUserAgent( $string );
	$client = $agent->get();
	return $client;

}


/**
 *
 *
 * @param unknown $email
 * @param unknown $userdata      (optional)
 * @param unknown $lists         (optional)
 * @param unknown $double_opt_in (optional)
 * @param unknown $overwrite     (optional)
 * @param unknown $mergelists    (optional)
 * @param unknown $template      (optional)
 * @return unknown
 */
function mailster_subscribe( $email, $userdata = array(), $lists = array(), $double_opt_in = null, $overwrite = true, $mergelists = null, $template = 'notification.html' ) {

	$entry = wp_parse_args( array( 'email' => $email ), $userdata );

	$added = null;
	if ( ! is_null( $double_opt_in ) ) {
		$entry['status'] = $double_opt_in ? 0 : 1;
		$added           = mailster_option( 'list_based_opt_in' ) ? ( $double_opt_in ? false : true ) : time();
	}

	$subscriber_id = mailster( 'subscribers' )->add( $entry, $overwrite );

	if ( is_wp_error( $subscriber_id ) ) {
		return false;
	}

	if ( ! is_array( $lists ) ) {
		$lists = array( $lists );
	}

	$new_lists = array();

	foreach ( $lists as $list ) {
		if ( is_numeric( $list ) ) {
			$new_lists[] = (int) $list;
		} else {
			if ( $list_id = mailster( 'lists' )->get_by_name( $list, 'ID' ) ) {
				$new_lists[] = $list_id;
			}
		}
	}
	mailster( 'subscribers' )->assign_lists( $subscriber_id, $new_lists, $mergelists, $added );

	return true;

}


/**
 * depreciated
 *
 * @param unknown $email_hash_id
 * @param unknown $campaign_id   (optional)
 * @param unknown $status        (optional)
 * @return unknown
 */
function mailster_unsubscribe( $email_hash_id, $campaign_id = null, $status = null ) {

	if ( is_numeric( $email_hash_id ) ) {

		return mailster( 'subscribers' )->unsubscribe( $email_hash_id, $campaign_id, $status );

	} elseif ( preg_match( '#^[0-9a-f]{32}$#', $email_hash_id ) ) {

		return mailster( 'subscribers' )->unsubscribe_by_hash( $email_hash_id, $campaign_id, $status );

	} else {

		return mailster( 'subscribers' )->unsubscribe_by_mail( $email_hash_id, $campaign_id, $status );
	}

	return false;

}


/**
 *
 *
 * @return unknown
 */
function mailster_get_subscribed_subscribers() {
	return mailster_get_subscribers();
}


/**
 *
 *
 * @return unknown
 */
function mailster_get_unsubscribed_subscribers() {
	return mailster_get_subscribers( 2 );
}


/**
 *
 *
 * @return unknown
 */
function mailster_get_hardbounced_subscribers() {
	return mailster_get_subscribers( 5 );
}


/**
 *
 *
 * @param unknown $status (optional)
 * @return unknown
 */
function mailster_get_subscribers( $status = null ) {
	return mailster( 'subscribers' )->get_totals( $status );
}


/**
 *
 *
 * @param unknown $part       (optional)
 * @param unknown $deprecated (optional)
 * @return unknown
 */
function mailster_clear_cache( $part = '', $deprecated = false ) {

	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_mailster_" . esc_sql( $part ) . "%'" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mailster_" . esc_sql( $part ) . "%'" );

	return true;

}


/**
 *
 *
 * @param unknown $args
 * @param unknown $type       (optional)
 * @param unknown $once       (optional)
 * @param unknown $key        (optional)
 * @param unknown $capability (optional)
 * @param unknown $screen     (optional)
 * @param unknown $append     (optional)
 * @return unknown
 */
function mailster_notice( $args, $type = '', $once = false, $key = null, $capability = true, $screen = null, $append = false ) {

	global $mailster_notices;

	if ( true === $key ) {
		$capability = true;
		$key        = null;
	}

	if ( ! is_array( $args ) ) {
		$args = array(
			'text' => $args,
			'type' => in_array( $type, array( 'success', 'error', 'info', 'warning' ) ) ? $type : 'success',
			'once' => $once,
			'key'  => $key ? $key : uniqid(),
		);
	}

	if ( true === $capability ) {
		$capability = get_current_user_id();
		// no logged in user => only for admins
		if ( ! $capability ) {
			$capability = 'manage_options';
		}
	}

	$args = wp_parse_args(
		$args,
		array(
			'text'   => '',
			'type'   => 'success',
			'once'   => false,
			'key'    => uniqid(),
			'cb'     => null,
			'cap'    => $capability,
			'screen' => $screen,
		)
	);

	if ( empty( $args['key'] ) ) {
		$args['key'] = uniqid();
	}

	if ( is_numeric( $args['once'] ) && $args['once'] < 1500000000 ) {
		$args['once'] = time() + $args['once'];
	}

	$mailster_notices = get_option( 'mailster_notices' );
	if ( ! is_array( $mailster_notices ) ) {
		$mailster_notices = array();
	}

	if ( $append && isset( $mailster_notices[ $args['key'] ] ) ) {
		$args['text'] = $mailster_notices[ $args['key'] ]['text'] . '<br>' . $args['text'];
	}

	$mailster_notices[ $args['key'] ] = array(
		'text'   => $args['text'],
		'type'   => $args['type'],
		'once'   => $args['once'],
		'cb'     => $args['cb'],
		'cap'    => $args['cap'],
		'screen' => $args['screen'],
	);

	do_action( 'mailster_notice', $args['text'], $args['type'], $args['key'] );

	update_option( 'mailster_notices', $mailster_notices );

	return $args['key'];

}



/**
 *
 *
 * @param unknown $key
 * @return unknown
 */
function mailster_remove_notice( $key ) {

	global $mailster_notices;

	$mailster_notices = get_option( 'mailster_notices', array() );

	if ( isset( $mailster_notices[ $key ] ) ) {

		unset( $mailster_notices[ $key ] );

		do_action( 'mailster_remove_notice', $key );
		do_action( 'mailster_remove_notice_' . $key );

		return update_option( 'mailster_notices', $mailster_notices );
	}

	return false;

}


/**
 *
 *
 * @param unknown $email
 * @return unknown
 */
function mailster_is_email( $email ) {

	$pre_check = apply_filters( 'mailster_is_email', null, $email );
	if ( ! is_null( $pre_check ) ) {
		return (bool) $pre_check;
	}

	// First, we check that there's one @ symbol, and that the lengths are right
	if ( ! preg_match( '/^[^@]{1,64}@[^@]{1,255}$/', $email ) ) {
		// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		return false;
	}
	// Split it into sections to make life easier
	$email_array = explode( '@', $email );
	$local_array = explode( '.', $email_array[0] );
	for ( $i = 0; $i < sizeof( $local_array ); $i++ ) {
		if ( ! preg_match( "/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[ $i ] ) ) {
			return false;
		}
	}
	if ( ! preg_match( '/^\[?[0-9\.]+\]?$/', $email_array[1] ) ) {
		// Check if domain is IP. If not, it should be valid domain name
		$domain_array = explode( '.', $email_array[1] );
		if ( sizeof( $domain_array ) < 2 ) {
			return false; // Not enough parts to domain
		}
		for ( $i = 0; $i < sizeof( $domain_array ); $i++ ) {
			if ( ! preg_match( '/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/', $domain_array[ $i ] ) ) {
				return false;
			}
		}
	}

	return true;

}


/**
 *
 *
 * @param unknown $id_email_or_hash
 * @param unknown $type             (optional)
 * @return unknown
 */
function mailster_get_subscriber( $id_email_or_hash, $type = null ) {

	$id_email_or_hash = trim( $id_email_or_hash );

	if ( ! is_null( $type ) ) {
		if ( $type == 'id' ) {
			return mailster( 'subscribers' )->get( $id_email_or_hash );
		} elseif ( $type == 'email' ) {
			return mailster( 'subscribers' )->get_by_mail( $id_email_or_hash );
		} elseif ( $type == 'hash' ) {
			return mailster( 'subscribers' )->get_by_hash( $id_email_or_hash );
		}
	}

	if ( is_numeric( $id_email_or_hash ) ) {
		return mailster( 'subscribers' )->get( $id_email_or_hash );
	} elseif ( preg_match( '#[0-9a-f]{32}#', $id_email_or_hash ) ) {
		return mailster( 'subscribers' )->get_by_hash( $id_email_or_hash );
	} elseif ( mailster_is_email( $id_email_or_hash ) ) {
		return mailster( 'subscribers' )->get_by_mail( $id_email_or_hash );
	}
	return false;
}


/**
 *
 *
 * @param unknown $tag
 * @param unknown $callback
 * @return unknown
 */
function mailster_add_tag( $tag, $callback ) {

	if ( false && ! did_action( 'mailster_add_tag' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				__( 'Custom Mailster tags should be added by using the %s action. Please check the documentation.', 'mailster' ),
				'<code>mailster_add_tag</code>'
			),
			'2.5'
		);
	}

	if ( is_callable( $callback ) ) {

	} elseif ( is_array( $callback ) ) {
		if ( ! method_exists( $callback[0], $callback[1] ) ) {
			return false;
		}
	} else {

		if ( ! function_exists( $callback ) ) {
			return false;
		}
	}

	global $mailster_tags;

	if ( ! isset( $mailster_tags ) ) {
		$mailster_tags = array();
	}

	$mailster_tags[ $tag ] = $callback;

	return true;

}


/**
 *
 *
 * @param unknown $tag
 * @return unknown
 */
function mailster_remove_tag( $tag ) {

	if ( false && ! did_action( 'mailster_add_tag' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				__( 'Custom Mailster tags should be removed by using the %s action. Please check the documentation.', 'mailster' ),
				'<code>mailster_add_tag</code>'
			),
			'2.5'
		);
	}

	global $mailster_tags;

	if ( isset( $mailster_tags[ $tag ] ) ) {
		unset( $mailster_tags[ $tag ] );
	}

	return true;

}


/**
 *
 *
 * @param unknown $callback
 * @return unknown
 */
function mailster_add_style( $callback ) {

	if ( false && ! did_action( 'mailster_add_style' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				__( 'Custom Mailster styles should be added by using the %s action. Please check the documentation.', 'mailster' ),
				'<code>mailster_add_style</code>'
			),
			'2.5'
		);
	}

	$args = func_get_args();
	$args = array_slice( $args, 1 );
	return mailster( 'helper' )->add_style( $callback, $args );
}

/**
 *
 *
 * @param unknown $callback
 * @return unknown
 */
function mailster_add_embeded_style( $callback ) {

	if ( false && ! did_action( 'mailster_add_style' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				__( 'Custom Mailster styles should be added by using the %s action. Please check the documentation.', 'mailster' ),
				'<code>mailster_add_style</code>'
			),
			'2.5'
		);
	}

	$args = func_get_args();
	$args = array_slice( $args, 1 );
	return mailster( 'helper' )->add_style( $callback, $args, true );

}

function mailster_add_embedded_style( $callback ) {

	return mailster_add_embeded_style( $callback );

}

/**
 *
 *
 * @return unknown
 */
function mailster_get_referer() {
	if ( $referer = wp_get_referer() ) {
		return $referer;
	}
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		return wp_unslash( $_REQUEST['_wp_http_referer'] );
	} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		return wp_unslash( $_SERVER['HTTP_REFERER'] );
	}
	return false;
}


/**
 *
 *
 * @param unknown $text
 * @return unknown
 */
function mailster_update_notice( $text ) {

	wp_enqueue_style( 'thickbox' );
	wp_enqueue_script( 'thickbox' );

	return sprintf( esc_html__( 'Mailster has been updated to %s.', 'mailster' ), '<strong>' . MAILSTER_VERSION . '</strong>' ) . ' <a class="thickbox" href="' . network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=mailster&amp;section=changelog&amp;TB_iframe=true&amp;width=772&amp;height=745' ) . '">' . esc_html__( 'Changelog', 'mailster' ) . '</a>';

}


/**
 *
 *
 * @param unknown $post_id (optional)
 * @return unknown
 */
function is_mailster_newsletter_homepage( $post_id = null ) {

	global $post;
	if ( is_null( $post_id ) ) {
		$the_post = $post;
		$post_id  = isset( $post ) ? $post->ID : null;
	} else {
		$the_post = get_post( $post_id );
	}

	return apply_filters( 'is_mailster_newsletter_homepage', $post_id == mailster_option( 'homepage' ), $the_post );

}


function mailster_remove_block_comments( $content ) {

	if ( false !== strpos( $content, '<!-- wp' ) ) {
		$content = preg_replace( '/<!-- \/?wp:(.+) -->(\n?)/', '', $content );
		$content = str_replace( array( '<p></p>' ), '', $content );
		$content = trim( $content );
	}

	return $content;
}

/**
 *
 *
 * @param unknown $redirect (optional)
 * @param unknown $method   (optional)
 * @param unknown $showform (optional)
 * @return unknown
 */
function mailster_require_filesystem( $redirect = '', $method = '', $showform = true ) {

	global $wp_filesystem;

	// force direct method
	add_filter(
		'filesystem_method',
		function() {
			return 'direct';
		}
	);

	if ( ! function_exists( 'request_filesystem_credentials' ) ) {

		require_once ABSPATH . 'wp-admin/includes/file.php';

	}

	ob_start();

	if ( false === ( $credentials = request_filesystem_credentials( $redirect, $method ) ) ) {
		$data = ob_get_contents();
		ob_end_clean();
		if ( ! empty( $data ) ) {
			include_once ABSPATH . 'wp-admin/admin-header.php';
			echo $data;
			include ABSPATH . 'wp-admin/admin-footer.php';
			exit;
		}
		return false;
	}

	if ( ! $showform ) {
		return false;
	}

	if ( ! WP_Filesystem( $credentials ) ) {
		request_filesystem_credentials( $redirect, $method, true ); // Failed to connect, Error and request again
		$data = ob_get_contents();
		ob_end_clean();
		if ( ! empty( $data ) ) {
			include_once ABSPATH . 'wp-admin/admin-header.php';
			echo $data;
			include ABSPATH . 'wp-admin/admin-footer.php';
			exit;
		}
		return false;
	}

	return $wp_filesystem;

}


if ( ! function_exists( 'http_negotiate_language' ) ) :


	/**
	 *
	 *
	 * @param unknown $supported
	 * @param unknown $http_accept_language (optional)
	 * @return unknown
	 */
	function http_negotiate_language( $supported, $http_accept_language = 'auto' ) {

		if ( $http_accept_language == 'auto' ) {
			$http_accept_language = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
		}

		preg_match_all(
			'/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?' .
			'(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i',
			$http_accept_language,
			$hits,
			PREG_SET_ORDER
		);

		// default language (in case of no hits) is the first in the array
		$bestlang = $supported[0];
		$bestqval = 0;

		foreach ( $hits as $arr ) {
			// read data from the array of this hit
			$langprefix = strtolower( $arr[1] );
			if ( ! empty( $arr[3] ) ) {
				$langrange = strtolower( $arr[3] );
				$language  = $langprefix . '-' . $langrange;
			} else {
				$language = $langprefix;
			}

			$qvalue = 1.0;
			if ( ! empty( $arr[5] ) ) {
				$qvalue = (float) $arr[5];
			}

			// find q-maximal language
			if ( in_array( $language, $supported ) && ( $qvalue > $bestqval ) ) {
				$bestlang = $language;
				$bestqval = $qvalue;
			} // if no direct hit, try the prefix only but decrease q-value by 10% (as http_negotiate_language does)
			elseif ( in_array( $langprefix, $supported ) && ( ( $qvalue * 0.9 ) > $bestqval ) ) {
				$bestlang = $langprefix;
				$bestqval = $qvalue * 0.9;
			}
		}

		return $bestlang;

	}


endif;

if ( ! function_exists( 'inet_pton' ) ) :


	/**
	 *
	 *
	 * @param unknown $ip
	 * @return unknown
	 */
	function inet_pton( $ip ) {
		// ipv4
		if ( strpos( $ip, '.' ) !== false ) {
			if ( strpos( $ip, ':' ) === false ) {
				$ip = pack( 'N', ip2long( $ip ) );
			} else {
				$ip = explode( ':', $ip );
				$ip = pack( 'N', ip2long( $ip[ count( $ip ) - 1 ] ) );
			}
		} // ipv6
		elseif ( strpos( $ip, ':' ) !== false ) {
			$ip       = explode( ':', $ip );
			$parts    = 8 - count( $ip );
			$res      = '';
			$replaced = 0;
			foreach ( $ip as $seg ) {
				if ( $seg != '' ) {
					$res .= str_pad( $seg, 4, '0', STR_PAD_LEFT );
				} elseif ( $replaced == 0 ) {
					for ( $i = 0; $i <= $parts; $i++ ) {
						$res .= '0000';
					}

					$replaced = 1;
				} elseif ( $replaced == 1 ) {
					$res .= '0000';
				}
			}
			$ip = pack( 'H' . strlen( $res ), $res );
		}
		return $ip;
	}


endif;
