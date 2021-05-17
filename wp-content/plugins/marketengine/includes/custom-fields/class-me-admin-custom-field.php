<?php
/**
 * MarketEngine Custom Field Handle
 *
 * @author  EngineThemes
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class MarketEngine Custom Field Handle
 * Handle MarketEngine Custom Field in post, edit listing form and listing details
 *
 * @package Includes/CustomFields
 * @category Class
 *
 * @since     1.0.1
 * @version 1.0.0
 */

class ME_Custom_Field_Handle {
    /**
     * Inserts or updates a custom field
     *
     * @param   array $field_data
     * @param   bool $is_update
     *
     * @return  int|WP_Error $field_id
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function insert($field_data, $is_update = false)
    {
        $term_ids            = isset($field_data['field_for_categories']) ? $field_data['field_for_categories'] : array();
        $field_data['count'] = count($term_ids);

        $attributes                     = self::filter_field_attribute($field_data);
        if(is_wp_error($attributes)) {
            return $attributes;
        }
        $field_data['field_constraint'] = $attributes;

        if(!$is_update) {
            $field_id = marketengine_cf_insert_field($field_data, true);
        }
        else {
            $current_cats      = marketengine_cf_get_field_categories($field_data['field_id']);
            self::remove_categories(array(
                'field_id'     => $field_data['field_id'],
                'current_cats' => $current_cats,
                'new_cats'     => $term_ids,
            ));
            $field_id = marketengine_cf_update_field($field_data, true);
        }

        $result = self::set_field_category($field_id, $term_ids);
        if (is_wp_error($result)) {
            return $result;
        }

        if(isset($field_data['field_options'])) {
            $result = self::add_field_taxonomy_options($field_data);
            if (is_wp_error($result)) {
                return $result;
            }
        }

        return $field_id;
    }

    /**
     * Deteles a custom field
     *
     * @param   int $field_id
     *
     * @return  bool
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function delete($field_id)
    {
        return marketengine_cf_delete_field($field_id);
    }

    /**
     * Removes a custom field from the category it affected,
     * or deletes it if no remain category
     *
     * @param   int $field_id
     * @param   int $category_id
     *
     * @return  bool
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function remove_from_category($field_id, $category_id)
    {
        $term_ids = marketengine_cf_get_field_categories($field_id);
        if (count($term_ids) == 1) {
            self::delete($field_id);
        } else {
            marketengine_cf_remove_field_category($field_id, $category_id);
        }
    }

    /**
     * Loads field input to set field attributes.
     * Fires when a field type is chosen.
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function load_field_input_ajax()
    {
        $options = marketengine_load_input_by_field_type($_POST);
        wp_send_json(array(
            'options' => $options,
        ));
    }

    /**
     * Renders field attributes for editing.
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function load_field_input()
    {
        $options = marketengine_load_input_by_field_type($_POST);
        echo $options;
    }

    /**
     * Set categories for a custom field.
     *
     * @param   int $field_id
     * @param   array $term_ids
     *
     * @return  WP_Error if set categories failed,
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function set_field_category($field_id, $term_ids)
    {
        $result = '';
        if (isset($term_ids) && !empty($term_ids)) {

            $field_cat = marketengine_cf_get_field_categories($field_id);

            foreach ($term_ids as $key => $term_id) {
                if (!in_array($term_id, $field_cat)) {
                    $result      = marketengine_cf_set_field_category($field_id, absint( $term_id ), 0);
                }
            }
        } else {
            $result = new WP_Error('invalid_taxonomy', __('Categories is required!', 'enginethemes'));
        }
        return $result;
    }

    /**
     * Removes unuse categories of a custom field.
     *
     * @param   array $args list of current categories, new categories, and custom field id.
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function remove_categories($args)
    {
        extract($args);
        $unuse_cats = array_diff($current_cats, $new_cats);

        foreach ($unuse_cats as $key => $cat) {
            $removed = marketengine_cf_remove_field_category($field_id, $cat);
            if (is_wp_error($removed)) {
                marketengine_wp_error_to_notices($removed);
                return;
            }
        }
    }

    /**
     * Prepares the string of field constraint.
     *
     * @param   array $field
     *
     * @return  string $constraint
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function filter_field_attribute($field)
    {
        $constraint = '';
        if (isset($field['field_constraint']) && !empty($field['field_constraint'])) {
            $constraint .= 'required';
        }

        if(isset($field['field_minimum_value']) && isset($field['field_maximum_value']) && !empty($field['field_minimum_value']) && !empty($field['field_maximum_value'])) {
            if($field['field_minimum_value'] >= $field['field_maximum_value']) {
                return new WP_Error('number_field_attributes_invalid', __('Maximum value must be greater than minimum value.', 'enginethemes'));
            }
        }

        if (isset($field['field_minimum_value']) && !empty($field['field_minimum_value'])) {
            $constraint .= '|min:' . $field['field_minimum_value'];
        }

        if (isset($field['field_maximum_value']) && !empty($field['field_maximum_value'])) {
            $constraint .= '|max:' . $field['field_maximum_value'];
        }

        if (isset($field['field_type']) && $field['field_type'] == 'date') {
            $constraint .= '|date';
        }

        if (isset($field['field_type']) && $field['field_type'] == 'number') {
            $constraint .= '|numeric';
        }

        return apply_filters('marketengine_add_field_contraints', $constraint);
    }

    /**
     * Check if the field name is exists.
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function is_field_name_exists()
    {
        if ($_POST['current_field_id'] != -1) {
            $field = marketengine_cf_get_field($_POST['current_field_id']);

            if ($field) {
                wp_send_json(array(
                    'unique'  => false,
                    'message' => __('Field name cannot be changed.', 'enginethemes'),
                ));
            }
        }

        $field = marketengine_cf_is_field_name_exists($_POST['field_name']);

        if ($field) {
            $unique  = false;
            $message = __('Sorry, this field name already exists!', 'enginethemes');
        } else {
            $unique  = true;
            $message = '';
        }

        wp_send_json(array(
            'unique'  => $unique,
            'message' => $message,
        ));
    }

    /**
     * Add field taxonomy options
     *
     * @param   array $field_data
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    public static function add_field_taxonomy_options($field_data)
    {
        if (isset($field_data['field_options']) && empty($field_data['field_options'])) {
            return new WP_Error('field_option_empty', __("Field option cannot be empty.", 'enginethemes'));
        }

        marketengine_cf_register_field_taxonomy($field_data);
        $field_options = trim($field_data['field_options']);
        $field_name    = $field_data['field_name'];

        $posted_options = self::field_options_to_array($field_options);
        self::remove_unused_field_options($field_name, $posted_options);

        $order = 0;
        foreach ($posted_options as $key => $option) {
        	$term = get_term_by( 'slug', $key, $field_name );
        	if($term) {
        		$term_id = wp_update_term( $term->term_id, $field_name, array('name' => $option, 'slug' => $key) );
        	}else {
        		$term_id = wp_insert_term($option, $field_name, array('slug' => sanitize_title(trim($key))));
        	}

            if(!is_wp_error( $term_id )) {
                update_term_meta( $term_id['term_id'], '_field_option_order', $order );
                $order++;
            }

        }
    }

    /**
     * Removes unuse field options.
     *
     * @param   string $field_name the field name
     * @param   array $new_options
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    private static function remove_unused_field_options($field_name, $new_options)
    {
        $existed_options = marketengine_cf_get_field_options($field_name);
        $options_remove = array_diff_key($existed_options, $new_options);

        foreach ($options_remove as $key => $option) {
            $term = get_term_by('slug', $key, $field_name);
            wp_delete_term($term->term_id, $field_name);
        }
    }

    /**
     * Converts the string of the field options to array
     *
     * @param   string $options
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    private static function field_options_to_array($options)
    {
        $options = explode(PHP_EOL, $options);
        $array   = array();
        foreach ($options as $key => $option) {
            $temp                                  = explode(':', $option);
            $temp                                  = self::sanitize_field_options_array($temp);
            $array[sanitize_title(trim($temp[0]))] = trim($temp[1]);
        }
        return $array;
    }

    /**
     * Generates and fill in the empty parts of field options array
     *
     * @param   array $options
     *
     * @return  array $options fully field options array
     *
     * @since   1.0.1
     * @version 1.0.0
     */
    private static function sanitize_field_options_array($options)
    {
        if (sizeof($options) == 1) {
            $options[1] = $options[0];
        }
        if (!empty($options[0]) || !empty($options[1])) {
            if (empty($options[0])) {
                $options[0] = sanitize_title($options[1]);
            }
            if (empty($options[1])) {
                $options[1] = $options[0];
            }
        }
        return $options;
    }
}
