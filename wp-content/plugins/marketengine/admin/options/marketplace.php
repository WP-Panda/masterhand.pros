<?php
/**
 * ME Marketplace options array template
 * - general option
 * - listing-type
 * - sample-data
 * @since 1.0
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

return apply_filters('marketengine_marketplace_options',
    array(
        'general' => array(
            'title'        => __("General", "enginethemes"),
            'slug'         => 'general-section',
            'type'         => 'section',
            'template'     => array(
                'user-email-confirmation' => array(
                    'label'       => __("Email Confirmation", "enginethemes"),
                    'description' => __("Enabling this will require new users to confirm their email addresses after registration.", "enginethemes"),
                    'slug'        => 'user-email-confirmation',
                    'type'        => 'switch',
                    'name'        => 'user-email-confirmation',
                    'template'    => array(),
                ),
                'commission_fee'          => array(
                    'label'       => __("Commission Fee", "enginethemes"),
                    'description' => __("Set up the commission fee (%) charging seller for each transaction.", "enginethemes"),
                    'slug'        => 'paypal-commission-fee',
                    'type'        => 'number',
                    'class_name'  => 'positive',
                    'attributes'  => array(
                        'min' => 0,
                    ),
                    'name'        => 'paypal-commission-fee',
                    'default' => 0
                ),

                'currency'               => array(
                    'label'              => __("Currency Options", "enginethemes"),
                    'description'        => __("Set up your preferred currency for the listing price.", "enginethemes"),
                    'slug'               => 'user-account-endpoint',
                    'type'               => 'multi_field',
                    'name'               => 'user-account-endpoint',
                    'template'           => array(
                        'currency-code'         => array(
                            'label'       => __("Currency Code", "enginethemes"),
                            'description' => __("The International Standard for currency code supported by Paypal Adaptive.", "enginethemes"),
                            'slug'        => 'payment-currency-code',
                            'type'        => 'textbox',
                            'name'        => 'payment-currency-code',
                        ),
                        'currency-sign'         => array(
                            'label'       => __("Currency Sign", "enginethemes"),
                            'description' => __("The currency symbol displayed beside listing price.", "enginethemes"),
                            'slug'        => 'payment-currency-sign',
                            'type'        => 'textbox',
                            'name'        => 'payment-currency-sign',
                        ),
                        'currency-sign-postion' => array(
                            'label'       => __("Currency Align", "enginethemes"),
                            'description' => __("The position of the currency sign.", "enginethemes"),
                            'slug'        => 'currency-sign-postion',
                            'type'        => 'switch',
                            'name'        => 'currency-sign-postion',
                            'text'        => array(__('Left', 'enginethemes'), __('Right', 'enginethemes')),
                            'template'    => array(),
                        ),

                        // 'thousand-sep' =>  array(
                        // 	'label' => __("Thousand Separator", "enginethemes"),
                        // 	'description' => __("The thousand seperator of displayed price", "enginethemes"),
                        //     'slug'        => 'thousand-sep',
                        //     'type'        => 'textbox',
                        //     'name'        => 'thousand-sep',
                        // ),
                        // 'dec-sep' =>  array(
                        // 	'label' => __("Decimal Separator", "enginethemes"),
                        // 	'description' => __("The decimal seperator of displayed price", "enginethemes"),
                        //     'slug'        => 'dec-sep',
                        //     'type'        => 'textbox',
                        //     'name'        => 'dec-sep',
                        // ),
                        // 'number-of-dec' =>  array(
                        // 	'label' => __("Number of Decimals", "enginethemes"),
                        // 	'description' => __("The number of decimal point show in displayed price", "enginethemes"),
                        //     'slug'        => 'number-of-sep',
                        //     'type'        => 'textbox',
                        //     'name'        => 'number-of-sep',
                        // ),
                    ),
                ),
                'dispute-time-limit' => array(
                    'label'       => __("Auto close order", "enginethemes"),
                    'description' => __("Set up the time (days) that order must be closed. Default is set 3 days.", "enginethemes"),
                    'slug'        => 'dispute-time-limit',
                    'type'        => 'number',
                    'class_name'  => 'no-zero positive',
                    'attributes'  => array(
                        'min' => 1,
                    ),
                    'name'        => 'dispute-time-limit',
                ),
            ),
		),
        'listing-type' => array(
            'title'    => __("Listing Types", "enginethemes"),
            'slug'     => 'listing-type-section',
            'type'     => 'section',
            'template' => array(
                'purchase'               => array(
                    'label'              => __("Purchase", "enginethemes"),
                    'description'        => __("This type of listing allows sellers to submit their products for sale.", "enginethemes"),
                    'slug'               => 'purchasion-type',
                    'type'               => 'multi_field',
                    'name'               => 'purchase-type',
                    'template'           => array(
                        'purchase-title'         => array(
                            'label'       => __("Title", "enginethemes"),
                            'description' => __('The labels will be shown as listing type allowing user to filter. "Selling" is set by default.', "enginethemes"),
                            'slug'        => 'purchasion-title',
                            'type'        => 'textbox',
                            'name'        => 'purchasion-title',
                        ),
                        'purchase-action'         => array(
                            'label'       => __("Text Button", "enginethemes"),
                            'description' => __('"BUY NOW" is set by default. But you can enter the text button to demonstrate the behavior that user can do.', "enginethemes"),
                            'slug'        => 'purchasion-action',
                            'type'        => 'textbox',
                            'name'        => 'purchasion-action',
                        ),
                        'purchase-available' => array(
                            'label'       => __("Available Categories", "enginethemes"),
                            'description' => __("Select categories supporting for this listing type.", "enginethemes"),
                            'slug'        => 'purchasion-available',
                            'type'        => 'multiselect',
                            'name'        => 'purchasion-available',
                            'icon_note'   => '<i class="icon-me-info-circle"></i>',
                            'note' => __("Please select categories for the listing type. Otherwise, this field won't display in the post listing form.", "enginethemes"),
                            'data' => marketengine_get_listing_categories()
                        ),
                    ),
                ),
                'contact'               => array(
                    'label'              => __("Contact", "enginethemes"),
                    'description'        => __("This type of listing allows sellers to submit their products for contact.", "enginethemes"),
                    'slug'               => 'purchase-type',
                    'type'               => 'multi_field',
                    'name'               => 'purchase-type',
                    'template'           => array(
                        'purchase-title'         => array(
                            'label'       => __("Title", "enginethemes"),
                            'description' => __('The labels will be shown as listing type allowing user to filter. "Offering" is set by default.', "enginethemes"),
                            'slug'        => 'contact-title',
                            'type'        => 'textbox',
                            'name'        => 'contact-title',
                        ),
                        'purchase-action'         => array(
                            'label'       => __("Text Button", "enginethemes"),
                            'description' => __('"CONTACT" is set by default. But you can enter the text button to demonstrate the behavior that user can do.', "enginethemes"),
                            'slug'        => 'contact-action',
                            'type'        => 'textbox',
                            'name'        => 'contact-action',
                        ),
                        'purchase-available' => array(
                            'label'       => __("Available Categories", "enginethemes"),
                            'description' => __("Select categories supporting for this listing type.", "enginethemes"),
                            'slug'        => 'contact-available',
                            'type'        => 'multiselect',
                            'name'        => 'contact-available',
                            'icon_note'   => '<i class="icon-me-info-circle"></i>',
                            'note' => __("Please select categories for the listing type. Otherwise, this field won't display in the post listing form.", "enginethemes"),
                            'data' => marketengine_get_listing_categories()
                        ),
                    ),
                ),
            ),
        ),
        'sample-data'  => array(
            'title'    => __("Sample Data", "enginethemes"),
            'slug'     => 'sample-data-section',
            'type'     => 'section',
            'template' => array(
                'dispute-time-limit' => array(
                    'label'       => __("Sample Data", "enginethemes"),
                    'type'        => 'sampledata',
                    'class_name'  => 'me-sample-data',
                ),
            ),
        ),
    )
);