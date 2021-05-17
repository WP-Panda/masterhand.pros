<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ME_Listing_Contact extends ME_Listing {
	public $contact_info;
	public function get_contact_info() {
		return get_post_meta($this->ID, 'contact_email', true);
	}

	public function get_inquiry_users() {
		global $wpdb;
		$message_table = $wpdb->prefix . 'marketengine_message_item';

		$sql = "SELECT sender 
					FROM $message_table as msg
						JOIN $wpdb->posts as listing
						ON msg.post_parent = listing.ID 
					GROUP BY sender";
	}
}