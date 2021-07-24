<?php
/*
Plugin Name: Mailster Email Verify
Plugin URI: https://mailster.co/?utm_campaign=wporg&utm_source=Email+Verify+for+Mailster&utm_medium=plugin
Description: Verifies your subscribers email addresses
Version: 1.5
Author: EverPress
Author URI: https://mailster.co
Text Domain: mailster-email-verify
License: GPLv2 or later
*/


define( 'MAILSTER_EMAILVERIFY_VERSION', '1.5' );
define( 'MAILSTER_EMAILVERIFY_REQUIRED_VERSION', '2.3' );
define( 'MAILSTER_EMAILVERIFY_FILE', __FILE__ );

require_once dirname( __FILE__ ) . '/classes/emailverify.class.php';
new MailsterEmailVerify();
