<?php

function mailster_do_depcreated_mymail_action( $tag ) {
	$args = func_get_args();
	$tag  = array_shift( $args );
	do_action_ref_array( $tag, $args );
}

add_action(
	'mailster_campaign_pause',
	function( $id ) {
		mailster_do_depcreated_mymail_action( 'mymail_campaign_pause', $id );
	},
	10,
	1
);

add_action(
	'mailster_campaign_start',
	function( $id ) {
		mailster_do_depcreated_mymail_action( 'mymail_campaign_start', $id );
	},
	10,
	1
);

add_action(
	'mailster_finish_campaign',
	function( $id ) {
		mailster_do_depcreated_mymail_action( 'mymail_finish_campaign', $id );
	},
	10,
	1
);

add_action(
	'mailster_campaign_duplicate',
	function( $id, $new_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_campaign_duplicate', $id, $new_id );
	},
	10,
	2
);

add_action(
	'mailster_send',
	function( $subscriber_id, $campaign_id, $result ) {
		mailster_do_depcreated_mymail_action( 'mymail_send', $subscriber_id, $campaign_id, $result );
	},
	10,
	3
);

add_action(
	'mailster_subscriber_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		mailster_do_depcreated_mymail_action( 'mymail_subscriber_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'mailster_system_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		mailster_do_depcreated_mymail_action( 'mymail_system_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'mailster_campaign_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		mailster_do_depcreated_mymail_action( 'mymail_campaign_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'mailster_autoresponder_post_published',
	function( $campaign_id, $new_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_autoresponder_post_published', $campaign_id, $new_id );
	},
	10,
	2
);

add_action(
	'mailster_check_bounces',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_check_bounces' );
	}
);

add_action(
	'mailster_resend_confirmations',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_resend_confirmations' );
	}
);

add_action(
	'mailster_form_head_button',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_head_button' );
	}
);

add_action(
	'mailster_form_head_embeded',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_head_embeded' );
	}
);

add_action(
	'mailster_form_head_iframe',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_head_iframe' );
	}
);

add_action(
	'mailster_form_body_button',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_body_button' );
	}
);

add_action(
	'mailster_form_body_iframe',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_body_iframe' );
	}
);

add_action(
	'mailster_form_footer_button',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_footer_button' );
	}
);

add_action(
	'mailster_form_footer_embeded',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_footer_embeded' );
	}
);

add_action(
	'mailster_form_footer_iframe',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_footer_iframe' );
	}
);

add_action(
	'mailster_form_delete',
	function( $form_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_form_delete', $form_id );
	},
	10,
	1
);

add_action(
	'mailster_update_form',
	function( $form_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_update_form', $form_id );
	},
	10,
	1
);

add_action(
	'mailster_add_form',
	function( $form_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_add_form', $form_id );
	},
	10,
	1
);

add_action(
	'mailster_unassign_form_lists',
	function( $form_ids, $lists, $not_list ) {
		mailster_do_depcreated_mymail_action( 'mymail_unassign_form_lists', $form_ids, $lists, $not_list );
	},
	10,
	3
);

add_action(
	'mailster_click',
	function( $subscriber_id, $campaign_id, $target, $index ) {
		mailster_do_depcreated_mymail_action( 'mymail_click', $subscriber_id, $campaign_id, $target, $index );
	},
	10,
	4
);

add_action(
	'mailster_open',
	function( $subscriber_id, $campaign_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_open', $subscriber_id, $campaign_id );
	},
	10,
	2
);

add_action(
	'mailster_homepage_subscribe',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_homepage_subscribe' );
	}
);

add_action(
	'mailster_homepage_unsubscribe',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_homepage_unsubscribe' );
	}
);

add_action(
	'mailster_homepage_profile',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_homepage_profile' );
	}
);

add_action(
	'mailster_homepage_confirm',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_homepage_confirm' );
	}
);

add_action(
	'mailster_subscriber_subscribed',
	function( $subscriber_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_subscriber_subscribed', $subscriber_id );
	},
	10,
	1
);

add_action(
	'mailster_subscriber_insert',
	function( $subscriber_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_subscriber_insert', $subscriber_id );
	},
	10,
	1
);

add_action(
	'mailster_list_save',
	function( $list_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_list_save', $list_id );
	},
	10,
	1
);

add_action(
	'mailster_list_delete',
	function( $list_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_list_delete', $list_id );
	},
	10,
	1
);

add_action(
	'mailster_update_list',
	function( $list_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_update_list', $list_id );
	},
	10,
	1
);

add_action(
	'mailster_initsend',
	function( $obj ) {
		mailster_do_depcreated_mymail_action( 'mymail_initsend', $obj );
	},
	10,
	1
);

add_action(
	'mailster_presend',
	function( $obj ) {
		mailster_do_depcreated_mymail_action( 'mymail_presend', $obj );
	},
	10,
	1
);

add_action(
	'mailster_dosend',
	function( $obj ) {
		mailster_do_depcreated_mymail_action( 'mymail_dosend', $obj );
	},
	10,
	1
);

add_action(
	'mailster_thirdpartystuff',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_thirdpartystuff' );
	},
	10,
	1
);

add_action(
	'mailster_autoresponder_post_published',
	function( $campaign_id, $new_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_autoresponder_post_published', $campaign_id, $new_id );
	},
	10,
	2
);

add_action(
	'mailster_autoresponder_timebased',
	function( $campaign_id, $new_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_autoresponder_timebased', $campaign_id, $new_id );
	},
	10,
	2
);

add_action(
	'mailster_autoresponder_usertime',
	function( $campaign_id, $subscriber_ids ) {
		mailster_do_depcreated_mymail_action( 'mymail_autoresponder_usertime', $campaign_id, $subscriber_ids );
	},
	10,
	2
);

add_action(
	'mailster_subscriber_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		mailster_do_depcreated_mymail_action( 'mymail_subscriber_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'mailster_notification_error',
	function( $subscriber_id, $error_message ) {
		mailster_do_depcreated_mymail_action( 'mymail_notification_error', $subscriber_id, $error_message );
	},
	10,
	2
);

add_action(
	'mailster_campaign_error',
	function( $subscriber_id, $campaign_id, $error_message ) {
		mailster_do_depcreated_mymail_action( 'mymail_campaign_error', $subscriber_id, $campaign_id, $error_message );
	},
	10,
	3
);

add_action(
	'mailster_cron_finished',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_cron_finished' );
	}
);

add_action(
	'mailster_subscriber_save',
	function( $subscriber_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_subscriber_save', $subscriber_id );
	},
	10,
	1
);

add_action(
	'mailster_subscriber_save',
	function( $subscriber_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_subscriber_save', $subscriber_id );
	},
	10,
	1
);

add_action(
	'mailster_subscriber_delete',
	function( $subscriber_id, $email ) {
		mailster_do_depcreated_mymail_action( 'mymail_subscriber_delete', $subscriber_id, $email );
	},
	10,
	2
);

add_action(
	'mailster_update_subscriber',
	function( $subscriber_id ) {
		mailster_do_depcreated_mymail_action( 'mymail_update_subscriber', $subscriber_id );
	},
	10,
	1
);

add_action(
	'mailster_unassign_lists',
	function( $subscriber_ids, $lists, $not_list ) {
		mailster_do_depcreated_mymail_action( 'mymail_unassign_lists', $subscriber_ids, $lists, $not_list );
	},
	10,
	3
);

add_action(
	'mailster_unsubscribe',
	function( $subscriber_id, $campaign_id, $status ) {
		mailster_do_depcreated_mymail_action( 'mymail_unsubscribe', $subscriber_id, $campaign_id, $status );
	},
	10,
	3
);

add_action(
	'mailster_bounce',
	function( $subscriber_id, $campaign_id, $is_hard, $status ) {
		mailster_do_depcreated_mymail_action( 'mymail_bounce', $subscriber_id, $campaign_id, $is_hard, $status );
	},
	10,
	4
);

add_action(
	'mailster_subscriber_change_status',
	function( $new_status, $old_status, $subscriber ) {
		mailster_do_depcreated_mymail_action( 'mymail_subscriber_change_status', $new_status, $old_status, $subscriber );
	},
	10,
	3
);

add_action(
	'mailster_cron_worker',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_cron_worker' );
	}
);

add_action(
	'mailster_form_header',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_header' );
	}
);

add_action(
	'mailster_form_head',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_head' );
	}
);

add_action(
	'mailster_form_body',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_body' );
	}
);

add_action(
	'mailster_form_footer',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_form_footer' );
	}
);

add_action(
	'mailster_notice',
	function( $text, $type, $key ) {
		mailster_do_depcreated_mymail_action( 'mymail_notice', $text, $type, $key );
	},
	10,
	3
);

add_action(
	'mailster_remove_notice',
	function( $key ) {
		mailster_do_depcreated_mymail_action( 'mymail_remove_notice', $key );
	},
	10,
	1
);

add_action(
	'mailster_import_tab',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_import_tab' );
	}
);

add_action(
	'mailster_autoresponder_more',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_autoresponder_more' );
	}
);

add_action(
	'mailster_settings_tabs',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_settings_tabs' );
	}
);

add_action(
	'mailster_settings',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_settings' );
	}
);

add_action(
	'mailster_section_tab',
	function( $id ) {
		mailster_do_depcreated_mymail_action( 'mymail_section_tab', $id );
	},
	10,
	1
);

add_action(
	'mailster_deliverymethod_tab',
	function( $id ) {
		mailster_do_depcreated_mymail_action( 'mymail_deliverymethod_tab', $id );
	},
	10,
	1
);

add_action(
	'mailster_wphead',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_wphead' );
	}
);

add_action(
	'mailster_wpfooter',
	function() {
		mailster_do_depcreated_mymail_action( 'mymail_wpfooter' );
	}
);

