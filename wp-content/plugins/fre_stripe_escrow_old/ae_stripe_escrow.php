<?php
/*
Plugin Name: FrE Stripe Escrow
Plugin URI: http://enginethemes.com/
Description: Integrates the Stripe escrow system to your Freelance site
Version: 1.3.2
Author: enginethemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginetheme
*/
 /**
  * Require all file need for stripe escrow system
  * @since 1.0
  * @author Tambh
  */
// if( !class_exists('AE_Base') ){
//   require_once dirname(__FILE__) . '/aecore/class-ae-base.php';
// }
/**
* Run this plugin after setup theme
* @param void
* @return void
* @since 1.1
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function require_plugin_escrow()
{
    if( !class_exists('AE_Base') ){
        return 0;
    }
    require_once dirname(__FILE__) . '/settings.php';
    require_once dirname(__FILE__) . '/template.php';
    require_once dirname(__FILE__) . '/stripe.php';
    require_once dirname(__FILE__) . '/update.php';
    $ae_escrow_stripe = AE_Escrow_Stripe::getInstance();
    if( $ae_escrow_stripe->is_use_stripe_escrow() ){
        $ae_escrow_stripe->init();
    }
}
add_action('after_setup_theme', 'require_plugin_escrow');
/**
* Enqueue script for escrow stripe
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function ae_stripe_escrow_scripts(){
    if( !class_exists('AE_Base') ){
        return 0;
    }
    $ae_escrow_stripe = AE_Escrow_Stripe::getInstance();
    if( $ae_escrow_stripe->is_use_stripe_escrow() ){
        if( is_page_template('page-profile.php') || et_load_mobile() ) {
            wp_enqueue_style('ae_stripe_escrow_css', plugin_dir_url(__FILE__) . 'assets/stripe.css', array(), '1.0');
            wp_enqueue_script('stripe_escrow_api', 'https://js.stripe.com/v1/');
            wp_enqueue_script('ae_stripe_escrow_js', plugin_dir_url(__FILE__) . 'assets/stripe.js', array(
                'underscore',
                'backbone',
                'appengine'
            ), '1.0', true);
            $public_key = $ae_escrow_stripe->ae_get_stripe_public_key();
            wp_localize_script('ae_stripe_escrow_js', 'ae_stripe_escrow', array(
                'stripe_public_key' => trim($public_key),
                'currency' => ae_get_option('currency'),
                'card_number_msg' => __('The Credit card number is invalid.', ET_DOMAIN),
                'name_card_msg' => __('The name on card is invalid.', ET_DOMAIN),
                'transaction_success' => __('The transaction completed successful!.', ET_DOMAIN),
                'transaction_false' => __('The transaction was not completed successful!.', ET_DOMAIN)
            ));
            wp_localize_script('ae_stripe_escrow_js', 'ae_stripe_escrow_globals', array(
                'confirm_disconnect' => __('Are you sure you want to disconnect your Stripe account?', ET_DOMAIN)
            ));
        }
    }

}
add_action( 'wp_enqueue_scripts', 'ae_stripe_escrow_scripts' );
/**
 * hook to add translate string to plugins
 * @param Array $entries Array of translate entries
 * @since 1.0
 * @author Dakachi
 */
function ae_stripe_escrow_add_translate_string ($entries) {
    $lang_path = dirname(__FILE__).'/lang/default.po';
    if(file_exists($lang_path)) {
        $pot        =   new PO();
        $pot->import_from_file($lang_path, true );

        return  array_merge($entries, $pot->entries);
    }
    return $entries;
}
add_filter( 'et_get_translate_string', 'ae_stripe_escrow_add_translate_string' );
/**
* check user infor before bid
* @param void
* @return void
* @since 1.0
* @package AE_ESCROW
* @category STRIPE
* @author Tambh
*/
function ae_stripe_escrow_before_insert_bid( $args ){
    if( ae_get_option('use_escrow')){
        global $user_ID;
        $ae_escrow_stripe = AE_Escrow_Stripe::getInstance();
        $ae_escrow_stripe->init();
        if( $ae_escrow_stripe->is_use_stripe_escrow() && $ae_escrow_stripe->ae_get_stripe_user_id( $user_ID ) == '' ){
            return new WP_Error('update_stripe', __('Please connect with Stripe to bid a project.', ET_DOMAIN));
        }
    }
    return $args;
}
add_filter('ae_pre_insert_bid', 'ae_stripe_escrow_before_insert_bid', 12, 1);
