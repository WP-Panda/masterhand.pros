<?php
/**
 * MarketEngine Custom Field Form
 *
 * @author  EngineThemes
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class MarketEngine Custom Field Form
 *
 * @package Includes/CustomFields
 * @category Class
 *
 * @since     1.0.1
 * @version 1.0.0
 */

class ME_Custom_Field_Form
{

    /**
     * Initializes actions for form
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function init()
    {
        add_filter('marketengine_section', array(__CLASS__, 'marketengine_add_custom_field_section'));

        add_action('admin_init', array(__CLASS__, 'marketengine_create_custom_field'));
        add_action('admin_init', array(__CLASS__, 'marketengine_update_custom_field'));
        add_action('admin_init', array(__CLASS__, 'marketengine_delete_custom_field'));
        add_action('admin_init', array(__CLASS__, 'marketengine_remove_custom_field_from_category'));

        add_action('admin_init', array(__CLASS__, 'marketengine_add_actions'));
        add_action('wp_ajax_me_cf_sort', array(__CLASS__, 'marketengine_cf_sort'));

        add_action('wp_ajax_check_field_name', array('ME_Custom_Field_Handle', 'is_field_name_exists'));
        add_action('marketengine_load_cf_input', array('ME_Custom_Field_Handle', 'load_field_input'));
        add_action('wp_ajax_me_cf_load_input_type', array('ME_Custom_Field_Handle', 'load_field_input_ajax'));

    }

    /**
     * Adds custom field section to list of admin settings
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function marketengine_add_custom_field_section($sections)
    {
        if (!isset($_REQUEST['tab']) || $_REQUEST['tab'] == 'marketplace-settings') {
            $sample_data              = $sections['sample-data'];
            $sections['custom-field'] = array(
                'title' => __('Custom Fields', 'enginethemes'),
                'slug'  => 'custom-field',
                'type'  => 'section',
            );
            unset($sections['sample-data']);
            $sections['sample-data'] = $sample_data;
        }
        return $sections;
    }

    /**
     * Prepares content of custom field section
     *
     * @since     1.0.1
     * @version 1.0.0
     */
    public static function marketengine_add_actions()
    {
        if (is_admin() && isset($_REQUEST['section']) && $_REQUEST['section'] == 'custom-field') {
            add_action('wp_print_scripts', array(__CLASS__, 'marketengine_print_script'), 100);
            add_action('get_custom_field_template', 'marketengine_custom_field_template');
        }
    }

    /**
     * Prepares scripts for custom field form
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function marketengine_print_script()
    {
        wp_dequeue_script('option-view');
        if (is_admin() && isset($_REQUEST['view']) && $_REQUEST['view'] == 'group-by-category') {
            wp_enqueue_script('cf_sort', MARKETENGINE_URL . "assets/admin/custom-field-sort.js", array('jquery-ui'));
        }
    }

    /**
     * Creates a custom field
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function marketengine_create_custom_field()
    {
        if (is_admin() && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-insert_custom_field')) {

            $field_for_categories = $_POST['field_for_categories'];
            $field_data = array_map( 'sanitize_text_field', $_POST );
            $field_data['field_for_categories'] = $field_for_categories;
            $field_id = ME_Custom_Field_Handle::insert($field_data);

            if (is_wp_error($field_id)) {
                marketengine_wp_error_to_notices($field_id);
                return;
            }

            $redirect = apply_filters('marketengine_after_create_custom_field_redirect', $_POST['redirect']);

            if ($redirect) {
                wp_redirect($redirect);
                exit;
            }
        }
    }

    /**
     * Updates a custom field
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function marketengine_update_custom_field()
    {
        if (is_admin() && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'me-update_custom_field')) {

            $_POST['field_id'] = absint( $_REQUEST['custom-field-id'] );            

            $field_for_categories = $_POST['field_for_categories'];
            $field_data = array_map( 'sanitize_text_field', $_POST );
            $field_data['field_for_categories'] = $field_for_categories;

            $field_id          = ME_Custom_Field_Handle::insert($_POST, true);

            if (is_wp_error($field_id)) {
                marketengine_wp_error_to_notices($field_id);
                return;
            }

            $redirect = apply_filters('marketengine_after_create_custom_field_redirect', $_POST['redirect']);

            if ($redirect) {
                wp_redirect($redirect);
                exit;
            }
        }
    }

    /**
     * Deletes a custom field
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function marketengine_delete_custom_field()
    {
        if (is_admin() && isset($_REQUEST['_wp_nonce']) && wp_verify_nonce($_REQUEST['_wp_nonce'], 'delete-custom-field') && isset($_REQUEST['custom-field-id'])) {

            ME_Custom_Field_Handle::delete(absint( $_REQUEST['custom-field-id'] ));

            if (is_wp_error($result)) {
                marketengine_wp_error_to_notices($result);
                return;
            }

            $redirect = remove_query_arg(array('action', '_wp_nonce', 'custom-field-id'));
            wp_redirect($redirect);
            exit;
        }
    }

    /**
     * Removes a custom field from the category it's affected
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function marketengine_remove_custom_field_from_category()
    {
        if (is_admin() && isset($_REQUEST['_wp_nonce']) && wp_verify_nonce($_REQUEST['_wp_nonce'], 'remove-from-category') && isset($_REQUEST['custom-field-id'])) {

            ME_Custom_Field_Handle::remove_from_category(absint( $_REQUEST['custom-field-id'] ), absint( $_REQUEST['category-id'] ));

            $redirect = remove_query_arg(array('action', '_wp_nonce', 'custom-field-id'));
            wp_redirect($redirect);
            exit;
        }
    }

    /**
     * Sort custom fields in a category
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function marketengine_cf_sort()
    {
        if (is_admin()) {
            parse_str($_POST['order'], $fields);
            $fields = $fields['me-cf-item'];
            foreach ($fields as $order => $field_id) {
                $result = marketengine_cf_set_field_category($field_id, absint($_POST['category_id']), absint($order));
                if (is_wp_error($result)) {
                    wp_send_json(array(
                        'status'  => false,
                        'message' => $result,
                    ));
                }
            }

            wp_send_json(array(
                'status'   => true,
                'message'  => __('Sort custom fields successfully', 'enginethemes'),
                'asd'      => $result,
                'field_id' => $field_id,
                'category' => absint($_POST['category_id']),
                'order'    => absint($order),
            ));
        }
    }
}
