<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class ME Handle CF
 * Handle MarketEngine Custom Field in post, edit listing form and listing details
 *
 * @package Includes/CustomFields
 * @category Class
 *
 * @version 1.0
 * @since 1.0.1
 */
class ME_Handle_CF
{
    /**
     * The single instance of the class.
     *
     * @var ME_Handle_CF
     * @since 1.0
     */
    protected static $_instance = null;

    /**
     * Main ME_Handle_CF Instance.
     *
     * Ensures only one instance of MarketEngine is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @return ME_Handle_CF - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        // render field form in post, edit listing
        add_action('marketengine_edit_listing_information_form_fields', array($this, 'edit_form_fields'));
        add_action('marketengine_post_listing_information_form_fields', array($this, 'post_form_fields'));

        // add ajax load custom field when user select category
        add_action('wp_ajax_me-load-category-fields', array($this, 'load_category_fields'));

        // validate field
        add_filter('marketengine_post_listing_error_messages', array($this, 'validate_fields'), 10, 2);

        add_action('marketengine_after_update_listing', array($this, 'update_fields'), 10, 2);
        add_action('marketengine_after_insert_listing', array($this, 'update_fields'), 10, 2);

        // custom field in listing details
        add_action('marketengine_after_single_listing_description', array($this, 'field_details'));
    }

    /**
     * Render edit listing custom fields form
     * @since 1.0
     */
    public function edit_form_fields($listing)
    {
        $category = wp_get_object_terms($listing->ID, 'listing_category', array('parent' => 0, 'fields' => 'ids'));
        if (empty($category)) {
            return;
        }

        $fields = marketengine_cf_get_fields($category[0]);
        marketengine_get_template('custom-fields/edit-field-form', array('fields' => $fields, 'listing' => $listing));
    }

    /**
     * Render post listing custom fields form
     * @since 1.0
     */
    public function post_form_fields()
    {
        marketengine_get_template('custom-fields/post-field-form');
    }

    /**
     * Ajax callback load custom fields by category
     * @since 1.0
     */
    public function load_category_fields()
    {
        if (empty($_GET['cat'])) {
            exit;
        }

        $cat    = absint($_GET['cat']);
        $fields = marketengine_cf_get_fields($cat);
        if (empty($fields)) {
            exit;
        }

        foreach ($fields as $field):
            $value = '';
            marketengine_get_template('custom-fields/listing-form/field-' . $field['field_type'], array('field' => $field, 'value' => $value));
        endforeach;

        exit;
    }

    /**
     * Hook to filter marketengine_post_listing_error_messages
     * Check category's custom field constraint and return the error messages
     *
     * @param array $errors The errors will be filtered
     * @param array $listing_data The listing data user submit
     *
     * @return array $errors
     * @since 1.0
     */
    public function validate_fields($errors, $listing_data)
    {
        $cat = absint( $listing_data['parent_cat'] );

        if (!$cat) {
            return $errors;
        }

        $fields = marketengine_cf_get_fields($cat);

        $rules             = array();
        $custom_attributes = array();

        foreach ($fields as $field) {
            $field_name         = $field['field_name'];
            $rules[$field_name] = $field['field_constraint'];
            if ($field['field_type'] == 'date') {
                $rules[$field_name] .= '|date';
            }

            if ($field['field_type'] == 'number') {
                $rules[$field_name] .= '|numeric';
            }

            $custom_attributes[$field_name] = $field['field_title'];
        }

        $is_valid = marketengine_validate($listing_data, $rules, $custom_attributes);
        if (!$is_valid) {
            $errors = array_merge($errors, marketengine_get_invalid_message($listing_data, $rules, $custom_attributes));
        }

        return $errors;
    }

    /**
     * Hook to update listing custom fields data
     *
     * @param int $post The listing id
     * @param array $data The data user submit
     *
     * @return void
     * @since 1.0
     */
    public function update_fields($post, $data)
    {
        $category = wp_get_object_terms($post, 'listing_category', array('parent' => 0, 'fields' => 'ids'));
        if (empty($category)) {
            return false;
        }

        $fields = marketengine_cf_get_fields($category[0]);
        if (empty($fields)) {
            return false;
        }

        foreach ($fields as $field) {
            $field_name = $field['field_name'];
            if (taxonomy_exists($field_name)) {
                $this->update_field_taxonomy($post, $field, $data);
            } else {
                $this->update_field_meta($post, $field, $data);
            }
        }

    }

    /**
     * Update listing custom field value as term taxonomy
     *
     * @param int $post The post id
     * @param array $field The field info
     * @param array $data The listing data
     *
     * @since 1.0
     * @return void
     */
    public function update_field_taxonomy($post, $field, $data)
    {

        $field_name  = $field['field_name'];
        $field_value = $data[$field_name];
        $term_arr    = array();
        if (is_array($field_value)) {
            foreach ($field_value as $term) {
                $term = absint($term);
                if (term_exists($term, $field_name)) {
                    $term_arr[] = $term;
                }
            }
        } else {
            $term = absint($field_value);
            if (term_exists($term, $field_name)) {
                $term_arr[] = $term;
            }
        }

        wp_set_object_terms($post, $term_arr, $field_name);

    }

    /**
     * Update listing custom field value as meta data
     *
     * @param int $post The post id
     * @param array $field The field info
     * @param array $data The listing data
     *
     * @since 1.0
     * @return void
     */
    public function update_field_meta($post, $field, $data)
    {
        $field_name  = $field['field_name'];
        $field_value = $data[$field_name];
        if ('date' === $field['field_type']) {
            $field_value = date('Y-m-d', strtotime($field_value));
        }else {
            $field_value = sanitize_text_field( $field_value );
        }

        update_post_meta($post, $field_name, $field_value);
    }

    /**
     * Render custom fields info in listing details template
     *
     * @return void
     * @since 1.0
     */
    public function field_details()
    {
        $post     = get_post();
        $category = wp_get_object_terms($post->ID, 'listing_category', array('parent' => 0, 'fields' => 'ids'));
        if (empty($category)) {
            return;
        }

        $fields = marketengine_cf_get_fields($category[0]);

        if (empty($fields)) {
            return;
        }

        ob_start();
        marketengine_get_template('custom-fields/field-details', array('fields' => $fields));
        $content = ob_get_clean();

        if ($content != '') {
            echo '<div class="me-custom-field me-desc-box">' . $content . '</div>';
        }
    }
}
