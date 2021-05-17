<?php

namespace Pmue\Common\Bootstrap;


class DefaultImportOptions
{

    private $defaultImportOptions = array(
				'pmue' => array(
					'import_users' => 0,
					'first_name' => '',
					'last_name' => '',
					'role' => 'subscriber',
					'role_xpath' => '',
					'nickname' => '',
					'description' => '',
					'login' => '',
					'pass' => '',
					'nicename' => '',
					'email' => '',
					'registered' => '',
					'display_name' => '',
					'url' => ''
				),
				'is_update_first_name' => 1,
				'is_update_last_name' => 1,
				'is_update_role' => 1,
				'is_update_nickname' => 1,
				'is_update_description' => 1,
				'is_update_login' => 1,
				'is_update_password' => 1,
				'is_update_nicename' => 1,
				'is_update_email' => 1,
				'is_update_registered' => 1,
				'is_update_display_name' => 1,
				'is_update_url' => 1,
				'do_not_send_password_notification' => 1,
				'is_hashed_wordpress_password' => 0,
				'pmsci_customer' => array(
					'import_customers' => 0,
					'first_name' => '',
					'last_name' => '',
					'role' => 'customer',
					'nickname' => '',
					'description' => '',
					'login' => '',
					'pass' => '',
					'nicename' => '',
					'email' => '',
					'registered' => '',
					'display_name' => '',
					'url' => '',
					'billing_first_name' => '',
					'billing_last_name' => '',
					'billing_company' => '',
					'billing_address_1' => '',
					'billing_address_2' => '',
					'billing_city' => '',
					'billing_postcode' => '',
					'billing_country' => '',
					'billing_state' => '',
					'billing_email' => '',
					'billing_phone' => '',
					'shipping_source' => 'copy',
					'shipping_first_name' => '',
					'shipping_last_name' => '',
					'shipping_company' => '',
					'shipping_address_1' => '',
					'shipping_address_2' => '',
					'shipping_city' => '',
					'shipping_postcode' => '',
					'shipping_country' => '',
					'shipping_state' => '',
				),
				'pmsci_is_update_billing_fields' => 1,
				'pmsci_is_update_shipping_fields' => 1,
				'pmsci_update_billing_fields_logic' => 'full_update',
				'pmsci_billing_fields_list' => array(),
				'pmsci_billing_fields_only_list' => array(),
				'pmsci_billing_fields_except_list' => array(),
				'pmsci_update_shipping_fields_logic' => 'full_update',
				'pmsci_shipping_fields_list' => array(),
				'pmsci_shipping_fields_only_list' => array(),
				'pmsci_shipping_fields_except_list' => array()
			);

    public function getDefaultImportOptions()
    {
        return $this->getDefaultImportOptions();
    }
}