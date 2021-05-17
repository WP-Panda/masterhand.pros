<?php

class MailsterNotification {

	private $message;
	private $template;
	private $file;
	private $to;
	private $subject;
	private $headline;
	private $preheader;
	private $attachments;
	private $replace;
	private $requeue = true;
	private $debug   = false;

	public $mail = null;

	private static $_instance = null;

	/**
	 *
	 *
	 * @param unknown $template
	 * @param unknown $file
	 * @return unknown
	 */
	public static function get_instance( $template, $file ) {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}
		self::$_instance->reset();
		self::$_instance->template( $template );
		self::$_instance->file( $file );

		return self::$_instance;
	}


	private function __construct() {

		add_filter( 'mailster_notification_to', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_subject', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_file', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_headline', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_preheader', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_replace', array( &$this, 'filter' ), 1, 4 );
		add_filter( 'mailster_notification_attachments', array( &$this, 'filter' ), 1, 4 );

	}


	public function init() {}


	public function reset() {
		$this->message     = null;
		$this->template    = null;
		$this->file        = null;
		$this->to          = null;
		$this->subject     = null;
		$this->headline    = null;
		$this->preheader   = null;
		$this->attachments = array();
		$this->replace     = array();
		$this->requeue     = true;
		$this->debug       = false;
	}


	/**
	 *
	 *
	 * @param unknown $timestamp (optional)
	 * @param unknown $args      (optional)
	 * @return unknown
	 */
	public function add( $timestamp = null, $args = array() ) {

		$now = time();

		$defaults = array(
			'subscriber_id' => null,
			'template'      => $this->template,
		);

		if ( is_null( $timestamp ) ) {
			$timestamp = $now;
		} elseif ( is_array( $timestamp ) ) {
			$args      = $timestamp;
			$timestamp = $now;
		}

		$args = $this->set_options( $args );

		$options = wp_parse_args( $args, $defaults );

		$subscriber_id = (int) $options['subscriber_id'];

		// send now
		if ( $timestamp <= $now ) {
			// sendnow
			$result = $this->send( (int) $subscriber_id, $options );

			// queue it if there was a problem
			if ( is_wp_error( $result ) ) {
				if ( $this->requeue ) {
					$this->add( $now + 360, $options );
				}

				return false;
			}

			return true;

		} else {

			unset( $options['subscriber_id'] );
			if ( ! $subscriber_id ) {
				$options['to'] = $this->to;
			}

			return mailster( 'queue' )->add(
				array(
					'campaign_id'   => 0,
					'subscriber_id' => $subscriber_id,
					'timestamp'     => $timestamp,
					'priority'      => 5,
					'ignore_status' => 1,
					'options'       => $options,
				)
			);
		}

	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @param unknown $template
	 * @param unknown $subscriber
	 * @param unknown $options
	 * @return unknown
	 */
	public function filter( $content, $template, $subscriber, $options ) {

		$filter = str_replace( 'mailster_notification_', '', current_filter() );

		switch ( $template . '_' . $filter ) {

			// new subscriber
			case 'new_subscriber_to':
			case 'new_subscriber_delayed_to':
				return explode( ',', mailster_option( 'subscriber_notification_receviers' ) );

			case 'new_subscriber_subject':
				return esc_html__( 'A new user has subscribed to your newsletter!', 'mailster' );
			case 'new_subscriber_delayed_subject':
				$delay    = mailster_option( 'subscriber_notification_delay' );
				$subjects = array(
					'day'   => esc_html__( 'Your daily summary', 'mailster' ),
					'week'  => esc_html__( 'Your weekly summary', 'mailster' ),
					'month' => esc_html__( 'Your monthly summary', 'mailster' ),
				);
				return isset( $subjects[ $delay ] ) ? $subjects[ $delay ] : esc_html__( 'New subscribers to your newsletter!', 'mailster' );

			case 'new_subscriber_file':
			case 'new_subscriber_delayed_file':
				return mailster_option( 'subscriber_notification_template' );

			case 'new_subscriber_replace':
			case 'new_subscriber_delayed_replace':
				return array(
					'preheader'    => $subscriber ? ( ( $subscriber->fullname ? $subscriber->fullname . ' - ' : '' ) . $subscriber->email ) : '',
					'notification' => sprintf( esc_html__( 'You are receiving this email because you have enabled notifications for new subscribers %s', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#subscribers' ) . '">' . esc_html__( 'on your settings page', 'mailster' ) . '</a>' ),
					'can-spam'     => '',
				);

			// unsubscription
			case 'unsubscribe_to':
			case 'unsubscribe_delayed_to':
				return explode( ',', mailster_option( 'unsubscribe_notification_receviers' ) );

			case 'unsubscribe_subject':
				return esc_html__( 'A user has canceled your newsletter!', 'mailster' );
			case 'unsubscribe_delayed_subject':
				$delay    = mailster_option( 'unsubscribe_notification_delay' );
				$subjects = array(
					'day'   => esc_html__( 'Your daily summary', 'mailster' ),
					'week'  => esc_html__( 'Your weekly summary', 'mailster' ),
					'month' => esc_html__( 'Your monthly summary', 'mailster' ),
				);
				return isset( $subjects[ $delay ] ) ? $subjects[ $delay ] : esc_html__( 'You have new cancellations!', 'mailster' );

			case 'unsubscribe_file':
			case 'unsubscribe_delayed_file':
				return mailster_option( 'unsubscribe_notification_template' );

			case 'unsubscribe_replace':
			case 'unsubscribe_delayed_replace':
				return array(
					'preheader'    => $subscriber ? ( ( $subscriber->fullname ? $subscriber->fullname . ' - ' : '' ) . $subscriber->email ) : '',
					'notification' => sprintf( esc_html__( 'You are receiving this email because you have enabled notifications for unsubscriptions %s', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#subscribers' ) . '">' . esc_html__( 'on your settings page', 'mailster' ) . '</a>' ),
					'can-spam'     => '',
				);

			// confirmation
			case 'confirmation_to':
				return $subscriber->email;

			case 'confirmation_subject':
				$form = $this->get_form_options( $options, $subscriber );
				return $form->subject;

			case 'confirmation_file':
				$form = $this->get_form_options( $options, $subscriber );
				return $form->template;

			case 'confirmation_headline':
				$form = $this->get_form_options( $options, $subscriber );
				return $form->headline;

			case 'confirmation_replace':
				if ( isset( $options['form'] ) ) {
					$form    = $this->get_form_options( $options, $subscriber, false, true );
					$form_id = $form->ID;
				} else {
					$form_id = null;
				}

				$subscriber_lists = mailster( 'subscribers' )->get_lists( $subscriber->ID );
				$list_names       = wp_list_pluck( $subscriber_lists, 'name' );

				$list_ids = isset( $options['list_ids'] ) ? array_filter( $options['list_ids'] ) : null;

				$link = mailster( 'subscribers' )->get_confirm_link( $subscriber->ID, $form_id, $list_ids );

				return wp_parse_args(
					array(
						'link'        => '<a href="' . htmlentities( $link ) . '">' . $form->link . '</a>',
						'linkaddress' => $link,
						'lists'       => implode( ', ', $list_names ),
					),
					$content
				);

			case 'confirmation_attachments':
				$form = $this->get_form_options( $options, $subscriber );
				if ( $form->vcard ) {

					global $wp_filesystem;

					mailster_require_filesystem();

					if ( $wp_filesystem->put_contents( MAILSTER_UPLOAD_DIR . '/vCard.vcf', $form->vcard_content, FS_CHMOD_FILE ) ) {
						$content[] = MAILSTER_UPLOAD_DIR . '/vCard.vcf';
					}
				}
				return $content;

			// test mail
			case 'test_subject':
				return esc_html__( 'Mailster Test Email', 'mailster' );

			case 'test_replace':
				return array(
					'notification' => sprintf( esc_html__( 'This is a test mail sent from %s', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_settings#delivery' ) . '">' . esc_html__( 'from your settings page', 'mailster' ) . '</a>' ),
					'can-spam'     => '',
				);

			default:
				return apply_filters( "mailster_notification_{$template}_{$filter}", $content, $subscriber, $options );
		}

	}


	/**
	 *
	 *
	 * @param unknown $template
	 */
	public function template( $template ) {
		$this->template = $template;
	}


	/**
	 *
	 *
	 * @param unknown $file
	 */
	public function file( $file ) {
		$this->file = $file;
	}


	/**
	 *
	 *
	 * @param unknown $to
	 */
	public function to( $to ) {
		$this->to = $to;
	}


	/**
	 *
	 *
	 * @param unknown $subject
	 */
	public function subject( $subject ) {
		$this->subject = $subject;
	}


	/**
	 *
	 *
	 * @param unknown $attachments
	 */
	public function attachments( $attachments ) {
		$this->attachments = is_array( $attachments ) ? $attachments : array( $attachments );
	}


	/**
	 *
	 *
	 * @param unknown $replace
	 */
	public function replace( $replace ) {
		$this->replace = is_array( $replace ) ? $replace : array( $replace );
	}


	/**
	 *
	 *
	 * @param unknown $requeue
	 */
	public function requeue( $requeue ) {
		$this->requeue = $requeue;
	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function debug( $bool = true ) {
		$this->debug = (bool) $bool;
	}


	/**
	 *
	 *
	 * @param unknown $subscriber_id
	 * @param unknown $options
	 * @return unknown
	 */
	public function send( $subscriber_id, $options ) {

		$template = $options['template'];

		$this->apply_options( $options );
		if ( $subscriber_id && $subscriber = mailster( 'subscribers' )->get( $subscriber_id, true ) ) {
			$userdata = mailster( 'subscribers' )->get_userdata( $subscriber );
			$this->to = $subscriber->email;
		} else {
			$subscriber = null;
		}

		ob_start();

		if ( method_exists( $this, 'template_' . $template ) ) {
			$return = call_user_func( array( $this, 'template_' . $template ), $subscriber, $options );
		}

		$output = ob_get_contents();

		ob_end_clean();

		if ( false === $return ) {
			return true;
		}

		// hook for custom templates
		ob_start();

		do_action( "mailster_notification_{$template}", $subscriber, $options, $output );

		$output2 = ob_get_contents();

		ob_end_clean();

		$this->message = ! empty( $output2 ) ? $output2 : $output;

		if ( empty( $this->message ) ) {
			return new WP_Error( 'notification_error', 'no content' );
		}

		$this->to        = apply_filters( 'mailster_notification_to', $this->to, $template, $subscriber, $options );
		$this->subject   = apply_filters( 'mailster_notification_subject', $this->subject, $template, $subscriber, $options );
		$this->file      = apply_filters( 'mailster_notification_file', $this->file, $template, $subscriber, $options );
		$this->headline  = apply_filters( 'mailster_notification_headline', $this->headline, $template, $subscriber, $options );
		$this->preheader = apply_filters( 'mailster_notification_preheader', $this->preheader, $template, $subscriber, $options );

		$this->replace = apply_filters( 'mailster_notification_replace', $this->replace, $template, $subscriber, $options );

		if ( ! isset( $this->file ) || empty( $this->file ) ) {
			$this->file = 'notification.html';
		}

		$this->mail = mailster( 'mail' );

		$this->to = (array) $this->to;

		$this->mail->to        = $this->to;
		$this->mail->from      = apply_filters( 'mailster_notification_from', $this->mail->from, $template, $subscriber, $options );
		$this->mail->from_name = apply_filters( 'mailster_notification_from_name', $this->mail->from_name, $template, $subscriber, $options );
		$this->mail->reply_to  = apply_filters( 'mailster_notification_reply_to', mailster_option( 'reply_to', false ), $template, $subscriber, $options );

		$this->mail->subject = $this->subject;

		$MID = mailster_option( 'ID' );
		$this->mail->add_header( 'X-Mailster-ID', $MID );

		$this->mail->bouncemail  = mailster_option( 'bounce' );
		$this->mail->attachments = apply_filters( 'mailster_notification_attachments', $this->attachments, $template, $subscriber, $options );

		$t   = mailster( 'template', null, $this->file );
		$raw = $t->get( true, true );

		$placeholder = mailster( 'placeholder', $raw );

		$placeholder->add_defaults();

		// only if the subscriber is in the list of receivers
		if ( $subscriber && in_array( $subscriber->email, $this->to ) ) {
			$this->mail->hash = $subscriber->hash;
			$this->mail->add_header( 'X-Mailster', $subscriber->hash );
			$placeholder->set_subscriber( $subscriber->ID );
			$placeholder->add( $userdata );
			$placeholder->add(
				array(
					'emailaddress' => $subscriber->email,
					'hash'         => $subscriber->hash,
				)
			);
		}

		$placeholder->add(
			array(
				'subject'   => $this->subject,
				'preheader' => $this->preheader,
				'headline'  => $this->headline,
				'content'   => $this->message,
			)
		);

		$placeholder->add( $this->replace );

		$content = $placeholder->get_content();
		$content = mailster( 'helper' )->prepare_content( $content );
		if ( apply_filters( 'mailster_inline_css', true ) ) {
			$content = mailster( 'helper' )->inline_css( $content );
		}

		$this->mail->content = $content;

		$placeholder->set_content( $this->mail->subject );
		$this->mail->subject = $placeholder->get_content();

		$this->mail->add_tracking_image = false;
		$this->mail->embed_images       = mailster_option( 'embed_images' );
		if ( $this->debug ) {
			$this->mail->debug();
		}

		foreach ( (array) $this->to as $receiver ) {

			$this->mail->to = $receiver;
			$result         = $this->mail->send();

			if ( $result && ! is_wp_error( $result ) ) {
				continue;
			}

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			if ( $this->mail->is_user_error() ) {
				return new WP_Error( 'user_error', $this->mail->last_error->getMessage() );
			}

			if ( $this->mail->last_error ) {
				return new WP_Error( 'notification_error', $this->mail->last_error->getMessage() );
			}

			return new WP_Error( 'notification_error', esc_html__( 'unknown', 'mailster' ) );
		}

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $options
	 * @return unknown
	 */
	private function set_options( $options ) {
		$params = array( 'to', 'subject' );
		foreach ( $params as $key ) {
			if ( ! is_null( $this->{$key} ) ) {
				$options[ $key ] = $this->{$key};
			}
		}

		return $options;
	}


	/**
	 *
	 *
	 * @param unknown $options
	 */
	private function apply_options( $options ) {
		if ( is_array( $options ) ) {
			foreach ( $options as $key => $value ) {
				if ( method_exists( $this, $key ) ) {
					$this->{$key}( $value );
				}
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $options
	 */
	private function get_form_options( $options, $subscriber, $fields = false, $lists = false ) {
		$form = mailster( 'forms' )->get( $options['form'], $fields, $lists );
		if ( $form_key = mailster( 'subscribers' )->meta( $subscriber->ID, 'formkey' ) ) {
			$form_args = (array) get_transient( '_mailster_form_' . $form_key );
			$form      = (object) wp_parse_args( $form_args, (array) $form );
		}
		return $form;
	}


	// Templates
	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_basic( $subscriber, $options ) {

	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_confirmation( $subscriber, $options ) {

		$form = $this->get_form_options( $options, $subscriber );

		if ( false === strpos( $form->content, '{link}' ) ) {
			$form->content .= "\n{link}";
		}

		echo nl2br( $form->content );

		?>
		<div itemscope itemtype="http://schema.org/EmailMessage">
			<div itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction">
			<meta itemprop="name" content="<?php echo $form->link; ?>"/>
			<div itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler">
			  <link itemprop="url" href="{linkaddress}"/>
			</div>
			</div>
			<meta itemprop="description" content="<?php esc_attr_e( 'Confirmation Message', 'mailster' ); ?>"/>
		</div>
		<?php

	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_test( $subscriber, $options ) {

		$options = mailster_options();
		?>

	   <style>
	   .mailster-settings td{border-top:1px solid #ccc;}
	   pre{line-height:16px;word-wrap:break-word;word-break:break-all;white-space:pre-wrap;font-size:11px;}
	   </style>
		<table width="100%" cellpadding="0" cellspacing="0" class="mailster-settings">

		<?php foreach ( $options as $key => $option ) : ?>

			<?php
			if ( $option == '' ) {
				continue;
			}

			if ( $key && preg_match( '#_pwd|_key|apikey|_secret#', $key ) ) {
				$option = '******';
			}

			if ( is_bool( $option ) ) {
				$option = $option ? 'true' : 'false';
			}

			?>
			<tr><td width="20%" valign="top"><b><pre><?php echo esc_html( $key ); ?></pre></b></td><td width="5%">&nbsp;</td><td width="75%" valign=""><pre><?php echo trim( print_r( $option, true ) ); ?></pre></td></tr>

		<?php endforeach; ?>


		</table>
		<?php

	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_new_subscriber( $subscriber, $options ) {

		$custom_fields = mailster()->get_custom_fields();

		?>

		<?php if ( get_option( 'show_avatars' ) ) : ?>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID ); ?>" >
					<img src="<?php echo mailster( 'subscribers' )->get_gravatar_uri( $subscriber->email, 240 ); ?>" width="120" style="border-radius:50%;display:block;width:120px;overflow:hidden;">
				</a>
			</td>
			</tr>
		</table>

	<?php endif; ?>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<h2><?php printf( esc_html__( '%s has joined', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID ) . '">' . ( ( $subscriber->fullname ) ? $subscriber->fullname . ' - ' : '' ) . $subscriber->email . '</a>' ); ?></h2>

				<table style="width:100%;table-layout:fixed">
					<tr><td><?php mailster( 'subscribers' )->output_referer( $subscriber->ID ); ?></td></tr>
				<?php foreach ( $custom_fields as $id => $field ) : ?>
					<?php
					if ( empty( $subscriber->{$id} ) && ! in_array( $field['type'], array( 'checkbox' ) ) ) {
						continue;
					}
					?>
					<tr><td height="20" style="border-top:1px solid #ccc;height:30px"><strong><?php echo strip_tags( $field['name'] ); ?>:</strong>
					<?php
					switch ( $field['type'] ) {
						case 'checkbox':
							echo $subscriber->{$id} ? esc_html__( 'yes', 'mailster' ) : esc_html__( 'no', 'mailster' );
							break;
						case 'textarea':
							echo wpautop( esc_html( $subscriber->{$id} ) );
							break;
						case 'date':
							echo $subscriber->{$id} && is_integer( strtotime( $subscriber->{$id} ) )
							? date( mailster( 'helper' )->dateformat(), strtotime( $subscriber->{$id} ) )
							: $subscriber->{$id};
							break;
						default:
							echo esc_html( $subscriber->{$id} );
					}
					?>
				</td></tr>
				<?php endforeach; ?>

					<?php if ( $lists = mailster( 'subscribers' )->get_lists( $subscriber->ID ) ) : ?>
				<tr><td height="30" style="border-top:1px solid #ccc;height:30px"><strong><?php esc_html_e( 'Lists', 'mailster' ); ?>:</strong>
						<?php foreach ( $lists as $i => $list ) { ?>
							<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID ); ?>"><?php echo $list->name; ?></a>
												<?php
												if ( $i + 1 < count( $lists ) ) {
													echo ', '; }
												?>
					<?php } ?>
				</td></tr>
					<?php endif; ?>

				</table>
			</td>
			</tr>
		</table>

		<?php if ( ( $loc = mailster_ip2City() ) != 'unknown' ) : ?>

			<?php
			$mapurl = add_query_arg(
				array(
					'markers'        => $loc->latitude . ',' . $loc->longitude,
					'zoom'           => 4,
					'size'           => '276x200',
					'visual_refresh' => true,
					'scale'          => 2,
					'language'       => get_locale(),
					'key'            => mailster_option( 'google_api_key' ),
				),
				'https://maps.googleapis.com/maps/api/staticmap'
			);
			?>

			<table style="width:100%;table-layout:fixed">
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>
						<img src="<?php echo esc_url( $mapurl ); ?>" width="276" heigth="200">
					</td>
					<td>
						<img src="<?php echo esc_url( add_query_arg( 'zoom', 8, $mapurl ) ); ?>" width="276" heigth="200">
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
			</table>

		<?php endif; ?>

		<?php

	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 * @return unknown
	 */
	private function template_new_subscriber_delayed( $subscriber, $options ) {

		global $wpdb;

		// should be odd
		$limit = apply_filters( 'mymail_subscriber_notification_subscriber_limit', apply_filters( 'mailster_subscriber_notification_subscriber_limit', 7 ) );

		$delay = mailster_option( 'subscriber_notification_delay' );
		if ( ! $delay ) {
			return false;
		}

		// get timestamp in UTC
		$timestamp = mailster( 'helper' )->get_timestamp_by_string( $delay, true );

		$sql = $wpdb->prepare( "SELECT a.ID, b.meta_value as coords, c.meta_value as geo FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.ID = b.subscriber_ID AND b.meta_key = 'coords' LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS c ON a.ID = c.subscriber_ID AND c.meta_key = 'geo' WHERE (a.signup >= %d OR a.confirm >= %d) AND a.status = 1 GROUP BY a.ID ORDER BY a.signup DESC, a.confirm DESC", $timestamp, $timestamp );

		$subscribers = $wpdb->get_results( $sql );

		$date_format = mailster( 'helper' )->dateformat();

		$count = count( $subscribers );
		if ( ! $count ) {
			return false;
		}

		if ( $count == 1 ) {
			$subscriber = mailster( 'subscribers' )->get( $subscribers[0]->ID, true );
			return $this->template_new_subscriber( $subscriber, $options );
		}

		$gmt_offset = mailster( 'helper' )->gmt_offset( true );

		$total = mailster( 'subscribers' )->get_count_by_status( 1 );
		?>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<h2><?php printf( esc_html__( 'You have %1$s new subscribers since %2$s.', 'mailster' ), '<strong>' . number_format_i18n( $count ) . '</strong>', date( $date_format, $timestamp + $gmt_offset ) ); ?></h2>
				<?php printf( esc_html__( 'You have now %s subscribers in total.', 'mailster' ), '<strong>' . number_format_i18n( $total ) . '</strong>' ); ?>
			</td>
			</tr>
		</table>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table cellpadding="0" cellspacing="0" class="o-fix">
		<tr>

			<td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">

		<?php foreach ( $subscribers as $i => $subscriber ) : ?>

			<?php
			if ( $i >= $limit ) {
				break;
			}

			$subscriber = mailster( 'subscribers' )->get( $subscriber->ID, true );
			$link       = admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID );

			?>
			<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
				<tr>
					<td width="264" valign="top" align="left" class="m-b">
					<table cellpadding="0" cellspacing="0">
						<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
						<tr>
						<?php if ( get_option( 'show_avatars' ) ) : ?>
						<td valign="top" align="center" width="80">
							<div style="border-radius:50%;width:60px;height:60px;background-color:#fafafa">
							<a href="<?php echo $link; ?>">
							<img src="<?php echo mailster( 'subscribers' )->get_gravatar_uri( $subscriber->email, 120 ); ?>" width="60" style="border-radius:50%;display:block;width:60px;overflow:hidden">
							</div>
							</a>
						</td>
						<?php endif; ?>
						<td valign="top" align="left">
							<h4 style="margin:0"><?php echo $subscriber->fullname ? '<a href="' . $link . '">' . esc_html( $subscriber->fullname ) . '</a>' : '&nbsp;'; ?></h4>
							<small><?php echo esc_html( $subscriber->email ); ?></small>
						</td>
						</tr>
						<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					</table>
					</td>
				</tr>
			</table>
			<?php
			if ( ! ! ( $i % 2 ) ) {
				echo '</td></tr></table><table cellpadding="0" cellspacing="0" class="o-fix"><tr><td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">'; }
			?>

	<?php endforeach; ?>

		<?php if ( $count > $limit ) : ?>

			<?php $link = admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&since=' . date( 'Y-m-d', $timestamp ) ); ?>

			  <table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
				<tr>
					<td width="264" valign="top" align="left" class="m-b">
					<table style="width:100%;table-layout:fixed">
					<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
					<td valign="center" align="center" width="80">
						<a href="<?php echo $link; ?>">
							<div style="border-radius:50%;width:60px;height:60px;background-color:#fafafa"></div>
						</a>
					</td>
					<td valign="center" align="left">
						<h4 style="margin:0;"><a href="<?php echo $link; ?>"><?php printf( esc_html__( _n( '%s other', '%s others', $count - $limit, 'mailster' ) ), number_format_i18n( $count - $limit ) ); ?></a></h5>
					</td>
					</tr>
					<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					</table>
					</td>
				</tr>
			   </table>

		<?php endif; ?>

			</td>

		</tr>
		</table>


		<?php
		$coords = wp_list_pluck( $subscribers, 'coords' );
		$geo    = wp_list_pluck( $subscribers, 'geo' );

		$coords        = array_values( array_slice( array_filter( $coords ), 0, 30 ) );
		$locationcount = count( $coords );
		$link          = 'https://maps.googleapis.com/maps/api/staticmap?' . ( $locationcount > 1 ? 'autoscale=true' : 'zoom=10' ) . '&size=600x300&scale=2&language=' . get_bloginfo( 'language' ) . '&maptype=roadmap&format=png&visual_refresh=true';

		foreach ( $coords as $i => $coord ) {
			$link .= '&markers=size:small%7Ccolor:0xdc3232%7Clabel:1%7C' . $coord;
		}

		// sanitation
		$geo = preg_replace( '/^([A-Z]+)|.*/', '$1', $geo );
		$geo = array_filter( $geo, 'strlen' );
		$geo = array_count_values( $geo );
		arsort( $geo );
		$other = array_sum( array_slice( $geo, 9, 9999 ) );
		$geo   = array_slice( $geo, 0, 9 );

		?>
		<?php if ( array_sum( $coords ) ) : ?>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table style="width:100%;table-layout:fixed">
			<tr>
				<td valign="top" align="center">
				<h2><?php printf( esc_html__( 'Subscribers are located in %s different countries', 'mailster' ), '<strong>' . $locationcount . '</strong>' ); ?></h2>
				</td>
				</tr>
				</table>

				<table style="width:100%;table-layout:fixed">
				<tr>
				<td valign="top" align="center">
				<img width="600" height="300" src="<?php echo $link; ?>" alt="<?php printf( esc_html__( _n( 'location of %d subscriber', 'location of %d subscribers', $locationcount, 'mailster' ) ), $locationcount ); ?>">
				</td>
			</tr>
		</table>


		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table cellpadding="0" cellspacing="0" class="o-fix">
			<tr>

			<td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">

			<?php $i = 0; foreach ( $geo as $code => $number ) : ?>
		<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
		<tr>
			<td width="264" valign="top" align="left" class="m-b">
			<table style="width:100%;table-layout:fixed">
			<tr>
			<td>&nbsp;</td>
			<td align="left" width="75%">
				<?php echo mailster( 'geo' )->code2Country( $code ); ?>
			</td>
			<td align="right" width="15%">
				<strong><?php echo number_format_i18n( $number ); ?></strong>
			</td>
			<td>&nbsp;</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>

				<?php
				if ( ! ! ( $i % 2 ) ) {
					echo '</td></tr></table><table cellpadding="0" cellspacing="0" class="o-fix"><tr><td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">'; }
				?>
				<?php
				$i++;
endforeach;
			?>

			<?php if ( ! empty( $other ) ) : ?>

			<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
			<tr>
				<td width="264" valign="top" align="left" class="m-b">
				<table style="width:100%;table-layout:fixed">
				<tr>
				<td>&nbsp;</td>
				<td align="left" width="75%">
					<?php esc_html_e( 'from other countries', 'mailster' ); ?>
				</td>
				<td align="right" width="15%">
					<strong><?php echo number_format_i18n( (int) $other ); ?></strong>
				</td>
				<td>&nbsp;</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>

	<?php endif; ?>

		</td>

	</tr>
	</table>

	<?php endif; ?>

		<?php

	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 */
	private function template_unsubscribe( $subscriber, $options ) {

		$custom_fields = mailster()->get_custom_fields();

		?>
		<?php if ( get_option( 'show_avatars' ) ) : ?>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<a href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID ); ?>" >
					<img src="<?php echo mailster( 'subscribers' )->get_gravatar_uri( $subscriber->email, 240 ); ?>" width="120" style="border-radius:50%;display:block;width:120px;overflow:hidden;">
				</a>
			</td>
			</tr>
		</table>

		<?php endif; ?>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<h2><?php printf( esc_html__( '%s has canceled', 'mailster' ), '<a href="' . admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID ) . '">' . ( ( $subscriber->fullname ) ? $subscriber->fullname . ' - ' : '' ) . $subscriber->email . '</a>' ); ?></h2>
			</td>
			</tr>
		</table>

		<?php

	}


	/**
	 *
	 *
	 * @param unknown $subscriber
	 * @param unknown $options
	 * @return unknown
	 */
	private function template_unsubscribe_delayed( $subscriber, $options ) {

		global $wpdb;

		// should be odd
		$limit = apply_filters( 'mymail_subscriber_unsubscribe_notification_subscriber_limit', apply_filters( 'mailster_subscriber_unsubscribe_notification_subscriber_limit', 7 ) );

		$delay = mailster_option( 'unsubscribe_notification_delay' );
		if ( ! $delay ) {
			return false;
		}

		// get timestamp in UTC
		$timestamp = mailster( 'helper' )->get_timestamp_by_string( $delay, true );

		$sql = $wpdb->prepare( "SELECT a.ID FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON a.ID = b.subscriber_ID AND b.type = 4 WHERE b.timestamp >= %d AND a.status = 2 GROUP BY a.ID ORDER BY b.timestamp DESC, a.signup DESC", $timestamp );

		$subscribers = $wpdb->get_results( $sql );

		$date_format = mailster( 'helper' )->dateformat();

		$count = count( $subscribers );
		if ( ! $count ) {
			return false;
		}

		if ( $count == 1 ) {
			$subscriber = mailster( 'subscribers' )->get( $subscribers[0]->ID, true );
			return $this->template_unsubscribe( $subscriber, $options );
		}

		$gmt_offset = mailster( 'helper' )->gmt_offset( true );

		$total = mailster( 'subscribers' )->get_count_by_status( 1 );

		?>
		<table style="width:100%;table-layout:fixed">
			<tr>
			<td valign="top" align="center">
				<h2><?php printf( esc_html__( 'You have %1$s cancellations since %2$s.', 'mailster' ), '<strong>' . number_format_i18n( $count ) . '</strong>', date( $date_format, $timestamp + $gmt_offset ) ); ?></h2>
				<?php printf( esc_html__( 'You have now %s subscribers in total.', 'mailster' ), '<strong>' . number_format_i18n( $total ) . '</strong>' ); ?>
			</td>
			</tr>
		</table>

		<table style="width:100%;table-layout:fixed"><tr><td valign="top" align="center">&nbsp;</td></tr></table>

		<table cellpadding="0" cellspacing="0" class="o-fix">
		<tr>

			<td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">
		<?php foreach ( $subscribers as $i => $subscriber ) : ?>
			<?php

			if ( $i >= $limit ) {
				break;
			}

			$subscriber = mailster( 'subscribers' )->get( $subscriber->ID, true );
			$link       = admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID );

			?>
		<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
			<tr>
				<td width="264" valign="top" align="left" class="m-b">
				<table cellpadding="0" cellspacing="0">
				<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
				<tr>
				<?php if ( get_option( 'show_avatars' ) ) : ?>
				<td valign="top" align="center" width="80">
					<div style="border-radius:50%;width:60px;height:60px;background-color:#fafafa">
					<a href="<?php echo $link; ?>">
					<img src="<?php echo mailster( 'subscribers' )->get_gravatar_uri( $subscriber->email, 120 ); ?>" width="60" style="border-radius:50%;display:block;width:60px;overflow:hidden">
					</div>
					</a>
				</td>
				<?php endif; ?>
				<td valign="top" align="left">
					<h4 style="margin:0"><?php echo esc_html( $subscriber->fullname ) ? '<a href="' . $link . '">' . esc_html( $subscriber->fullname ) . '</a>' : '&nbsp;'; ?></h4>
					<small><?php echo esc_html( $subscriber->email ); ?></small>
				</td>
				</tr>
				<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
				</table>
				</td>
			</tr>
		</table>

			<?php
			if ( ! ! ( $i % 2 ) ) {
				echo '</td></tr></table><table cellpadding="0" cellspacing="0" class="o-fix"><tr><td width="552" valign="top" align="center" style="border-top:1px solid #ccc;">'; }
			?>

		<?php endforeach; ?>

		<?php if ( $count > $limit ) : ?>

			<?php $link = admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&since=' . date( 'Y-m-d', $timestamp ) ); ?>

			<table cellpadding="0" cellspacing="0" align="<?php echo ! ( $i % 2 ) ? 'left' : 'right'; ?>">
				<tr>
					<td width="264" valign="top" align="left" class="m-b">
					<table style="width:100%;table-layout:fixed">
					<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
					<td valign="center" align="center" width="80">
						<a href="<?php echo $link; ?>">
							<div style="border-radius:50%;width:60px;height:60px;background-color:#fafafa"></div>
						</a>
					</td>
					<td valign="center" align="left">
						<h4 style="margin:0;"><a href="<?php echo $link; ?>"><?php printf( esc_html__( _n( '%s other', '%s others', $count - $limit, 'mailster' ) ), number_format_i18n( $count - $limit ) ); ?></a></h5>
					</td>
					</tr>
					<tr><td width="80">&nbsp;</td><td>&nbsp;</td></tr>
					</table>
					</td>
				</tr>
			</table>

		<?php endif; ?>

		</td>

	</tr>
	</table>
		<?php

	}


}
