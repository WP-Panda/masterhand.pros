<?php
/**
 * Orion SMS OTP verification Main File.
 *
 * @package Orion SMS OTP verification
 */

/*
Plugin Name:  Orion SMS OTP Verification
Plugin URI:   http://imransayed.com/otp-verifcation
Description:  This plugin allows you to verify mobile number by sending a one time OTP to the user entered mobile number. It gives you an option to choose MSG91 or TWILIO third party API to send OTP and SMS. You can also use this for Woo-commerce Order SMS and Mobile OTP verification on Woo-commerce checkout form.
Version:      5.0.1
Author:       Imran Sayed, Smit Patadiya
Author URI:   https://profiles.wordpress.org/gsayed786
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  orion-sms-orion-sms-otp-verification
Domain Path:  /languages
*/

/* Include the Custom functions file */
require 'inc/country-code-functions.php';
require 'inc/rate-us.php';
require 'inc/form-input-functions.php';
require 'custom-functions.php';
require 'inc/admin-settings.php';
require 'inc/woo-commerce-order-functions.php';
