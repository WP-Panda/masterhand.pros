<?php

$mailster_autoresponder_info = array(

	'units'   => array(
		'minute' => esc_html__( 'minute(s)', 'mailster' ),
		'hour'   => esc_html__( 'hour(s)', 'mailster' ),
		'day'    => esc_html__( 'day(s)', 'mailster' ),
		'week'   => esc_html__( 'week(s)', 'mailster' ),
		'month'  => esc_html__( 'month(s)', 'mailster' ),
		'year'   => esc_html__( 'year(s)', 'mailster' ),
	),

	'actions' => array(
		'mailster_subscriber_insert'       => array(
			'label' => esc_html__( 'user signed up', 'mailster' ),
			'hook'  => 'mailster_subscriber_insert',
		),
		'mailster_subscriber_unsubscribed' => array(
			'label' => esc_html__( 'user unsubscribed', 'mailster' ),
			'hook'  => 'mailster_subscriber_unsubscribed',
		),
		'mailster_post_published'          => array(
			'label' => esc_html__( 'something has been published', 'mailster' ),
			'hook'  => 'transition_post_status',
		),
		'mailster_autoresponder_timebased' => array(
			'label' => esc_html__( 'at a specific time', 'mailster' ),
			'hook'  => 'mailster_autoresponder_timebased',
		),
		'mailster_autoresponder_usertime'  => array(
			'label' => esc_html__( 'a specific user time', 'mailster' ),
			'hook'  => 'mailster_autoresponder_usertime',
		),
		'mailster_autoresponder_followup'  => array(
			'label' => esc_html__( 'a specific campaign', 'mailster' ),
			'hook'  => 'mailster_autoresponder_followup',
		),
		'mailster_autoresponder_hook'      => array(
			'label' => esc_html__( 'a specific action hook', 'mailster' ),
			'hook'  => 'mailster_autoresponder_hook',
		),
	),

);

$mailster_autoresponder_info['units']   = apply_filters( 'mymail_autoresponder_units', apply_filters( 'mailster_autoresponder_units', $mailster_autoresponder_info['units'] ) );
$mailster_autoresponder_info['actions'] = apply_filters( 'mymail_autoresponder_actions', apply_filters( 'mailster_autoresponder_actions', $mailster_autoresponder_info['actions'] ) );
