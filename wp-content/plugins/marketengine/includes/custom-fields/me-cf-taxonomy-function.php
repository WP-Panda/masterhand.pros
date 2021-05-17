<?php
/**
 * Fields are predefined and user only selected available values. Using wordpress taxonomy to control the field value
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register taxonomy to control custom field value for checkbox, multi-select, single-select
 *
 * @package Includes/CustomField
 * @category Function
 *
 * @since 1.0.1
 */
function marketengine_cf_register_field_taxonomies()
{
    // get fields by type select, checkbox, multiselect, radio
    $fields_query = marketengine_cf_fields_query(array('showposts' => -1, 'field_type' => array('single-select', 'multi-select', 'radio', 'checkbox')));
    $fields       = $fields_query['fields'];
    if (empty($fields)) {
        return;
    }

    foreach ($fields as $field) {
        // register taxonomy
        marketengine_cf_register_field_taxonomy($field);
    }
}
add_action('init', 'marketengine_cf_register_field_taxonomies');

/**
 * Register taxonomy to control a field value
 *
 * @param array $field The field data
 * @see register_taxonomy()
 *
 * @package Includes/CustomField
 * @category Function
 *
 * @since 1.0.1
 */
function marketengine_cf_register_field_taxonomy($field)
{
    $labels = array(
        'name'          => $field['field_title'],
        'singular_name' => $field['field_title'],
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => false,
        'show_admin_column' => false,
        'query_var'         => true,
        'rewrite'           => false,
        'show_in_nav_menus' => false
    );
    $args = apply_filters('marketengine_register_field_taxonomy_args', $args, $field);
    register_taxonomy($field['field_name'], 'listing', $args);
}

/**
 * Get field taxonomy option value list
 *
 * @param string $field_name The field name
 * @param array $args Get terms args @see get_terms()
 *
 * @since 1.0.1
 * @return array Array of option value
 */
function marketengine_cf_get_field_options($field_name, $args = array())
{
    $results   = array();
    $defaults = array('hide_empty' => 0, 'meta_key' => '_field_option_order', 'orderby' => 'meta_value_num');
    $args     = wp_parse_args($args, $defaults);

    $args = apply_filters('marketengine_get_field_option_args', $args);
    $termlist = get_terms($field_name, $args );
    if(empty($termlist)) {
        $termlist = get_terms($field_name, array('hide_empty' => 0) );
    }
    foreach ($termlist as $term) {
        $results[$term->slug] = array(
            'value' => $term->term_id,
            'label' => $term->name,
            'key'   => $term->slug,
        );
    }

    return $results;
}

/**
 * Hook to filter marketengine_field taxonomy args
 * @param array $args
 * @return array
 */
function marketengine_field_taxonomy_args($args) {
    $defaults = array('meta_key' => '_field_option_order', 'orderby' => 'meta_value_num');
    return wp_parse_args( $args, $defaults );
}
add_filter( 'marketengine_me_field_taxonomy_args', 'marketengine_field_taxonomy_args' );
