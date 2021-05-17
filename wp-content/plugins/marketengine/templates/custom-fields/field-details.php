<?php
/**
 * The Template for render listing custom field value
 *
 * This template can be overridden by copying it to yourtheme/marketengine/custom-fields/field-details.php.
 *
 * @author         EngineThemes
 * @package     MarketEngine/Templates
 *
 * @since         1.0.1
 *
 * @version     1.0.0
 *
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

foreach ($fields as $field):

    switch ($field['field_type']) {
        case 'text':
        case 'textarea':
        case 'number':

            $value = marketengine_field($field['field_name']);
            if (!$value) {
                break;
            }

            marketengine_get_template('custom-fields/listing-details/text', array('field' => $field, 'value' => $value));

            break;
            
        case 'date':
            $value = marketengine_field($field['field_name']);
            if (!$value) {
                break;
            }

            $date = date_i18n(get_option('date_format'), strtotime($value));
            marketengine_get_template('custom-fields/listing-details/text', array('field' => $field, 'value' => $date));

            break;

        case 'single-select':
            $value = marketengine_field($field['field_name'], null, array('fields' => 'names'));
            if (empty($value)) {
                break;
            }

            $value = $value[0];
            marketengine_get_template('custom-fields/listing-details/text', array('field' => $field, 'value' => $value));

            break;

        case 'checkbox':
        case 'multi-select':
            $value = marketengine_field($field['field_name'], null, array('fields' => 'names'));
            if (empty($value)) {
                break;
            }

            marketengine_get_template('custom-fields/listing-details/list', array('field' => $field, 'value' => $value));

            break;

        default:
            break;
    }

endforeach;
