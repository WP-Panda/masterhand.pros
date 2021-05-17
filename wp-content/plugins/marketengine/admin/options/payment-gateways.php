<?php
/**
 * ME Payment Gateways setting page.
 * @since 1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

return apply_filters('marketengine_payment_options',
    array(
        'general' => array(
            'title'    => __("General", "enginethemes"),
            'slug'     => 'general-section',
            'type'     => 'section',
            'template' => array(
                'test-mode'           => array(
                    'label'       => __("Payment Test Mode", "enginethemes"),
                    'description' => __("Enabling this will allow you to test payment without charging your account.", "enginethemes"),
                    'slug'        => 'test-mode',
                    'type'        => 'switch',
                    'name'        => 'test-mode',
                    'template'    => array(),
                ),

                'paypal-adaptive-api' => array(
                    'label'       => __("PayPal Adaptive API", "enginethemes"),
                    'description' => __("Paypal Adaptive Payments handles all payments between buyer and seller.", "enginethemes"),
                    'slug'        => 'paypal-adaptive-api',
                    'type'        => 'multi_field',
                    'name'        => 'paypal-adaptive-api',
                    'template'    => array(
                        'receiver-email' => array(
                            'label'       => __("Receiver Email", "enginethemes"),
                            'description' => __("Enter your PayPal email to receive commission", "enginethemes"),
                            'slug'        => 'paypal-receiver-email',
                            'type'        => 'textbox',
                            'name'        => 'paypal-receiver-email',
                            'template'    => array(),
                        ),
                        'app-api'        => array(
                            'label'       => __("App ID", "enginethemes"),
                            'description' => __("Enter your PayPal Adaptive AppID", "enginethemes"),
                            'slug'        => 'paypal-app-api',
                            'type'        => 'textbox',
                            'name'        => 'paypal-app-api',
                            'template'    => array(),
                        ),
                        'username'       => array(
                            'label'       => __("PayPal API username", "enginethemes"),
                            'description' => __("", "enginethemes"),
                            'slug'        => 'paypal-api-username',
                            'type'        => 'textbox',
                            'name'        => 'paypal-api-username',
                            'template'    => array(),
                        ),
                        'password'       => array(
                            'label'       => __("PayPal API password", "enginethemes"),
                            'description' => __("", "enginethemes"),
                            'slug'        => 'paypal-api-password',
                            'type'        => 'password',
                            'name'        => 'paypal-api-password',
                            'template'    => array(),
                        ),
                        'signature'      => array(
                            'label'       => __("PayPal API signature", "enginethemes"),
                            'description' => __("", "enginethemes"),
                            'slug'        => 'paypal-api-signature',
                            'type'        => 'textbox',
                            'name'        => 'paypal-api-signature',
                            'template'    => array(),
                        ),
                    ),
                ),

            ),
        ),
    )
);
