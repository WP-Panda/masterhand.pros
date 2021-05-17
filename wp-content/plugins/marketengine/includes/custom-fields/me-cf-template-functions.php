<?php
/**
 * List custom field type
 *
 * @since 	1.0.1
 */
function marketengine_list_custom_field_type() {
	$field_types = array(
		array(
			'label'		=> __("Basic", "enginethemes"),
			'options'	=> array(
				'text'		=> 'Text',
				'textarea'	=> 'Textarea',
				'number'	=> 'Number',
				'date'		=> 'Date',
			),
		),
		array(
			'label'		=> __("Choose", "enginethemes"),
			'options'	=> array(
				'checkbox'				 => 'Checkbox',
				'single-select' => 'Dropdown Single Select',
				'multi-select'	 => 'Dropdown Multi Select',
			),
		),
	);

	$field_types = apply_filters('filter_custom_field', $field_types);

	return $field_types;
}

/**
 * Gets label of the field type
 *
 * @param string $field_type
 * @return string label of the field type
 *
 * @since   1.0.1
 * @version 1.0.0
 */
function marketengine_get_field_type_label($field_type) {
    $field_types = array(
        'text'          => 'Text',
        'textarea'      => 'Textarea',
        'number'        => 'Number',
        'date'          => 'Date',
        'checkbox'      => 'Checkbox',
        'single-select' => 'Dropdown Single Select',
        'multi-select'  => 'Dropdown Multi Select',
    );

    $field_types = apply_filters('filter_custom_field', $field_types);

    return $field_types[$field_type];
}

/**
 * Loads custom field templates
 *
 * @since 	1.0.1
 * @version 1.0.0
 */
function marketengine_custom_field_template() {
    if(isset($_REQUEST['view'])) {
        if($_REQUEST['view'] == 'add') {
            marketengine_get_template('custom-fields/admin-templates/admin-custom-field-form');
        } elseif($_REQUEST['view'] == 'edit') {
            $field_obj = marketengine_cf_get_field($_REQUEST['custom-field-id']);
            $field_obj['field_for_categories'] = marketengine_cf_get_field_categories($_REQUEST['custom-field-id']);
            marketengine_get_template('custom-fields/admin-templates/admin-custom-field-form', array('field_obj' => $field_obj));
        } elseif($_REQUEST['view'] == 'group-by-category') {
            marketengine_get_template('custom-fields/admin-templates/admin-custom-field');
        }
    } else {
        marketengine_get_template('custom-fields/admin-templates/admin-custom-field');
    }
}

function marketengine_load_input_by_field_type($args) {
	$placeholder = isset($args['field_placeholder']) ? $args['field_placeholder'] : '';
    $attribute = marketengine_field_attribute_array($args);
    $attribute = wp_parse_args($attribute, array('min'=>'','max'=>''));

	$field_options = isset($args['field_options']) ? $args['field_options'] : array();

    $default_value = isset($args['field_default_value']) ? $args['field_default_value'] : '';

    $options = '';

    switch($args['field_type']) {
        case 'text':
        case 'textarea':
            ob_start();
            marketengine_get_template('custom-fields/admin-templates/admin-field-placeholder', array('placeholder' => $placeholder));
            $options = ob_get_clean();
            break;

        case 'number':
            ob_start();
            marketengine_get_template('custom-fields/admin-templates/admin-field-placeholder', array('placeholder' => $placeholder));

            marketengine_get_template('custom-fields/admin-templates/admin-field-minimum-value', array('min' => $attribute['min']));

            marketengine_get_template('custom-fields/admin-templates/admin-field-maximum-value', array('max' => $attribute['max']));
            $options = ob_get_clean();
            break;
        case 'date':
            break;

        case 'checkbox':
        case 'radio':
            ob_start();
            marketengine_get_template('custom-fields/admin-templates/admin-field-option', $args);
            $options = ob_get_clean();
            break;

        case 'single-select':
        case 'multi-select':
            ob_start();
            marketengine_get_template('custom-fields/admin-templates/admin-field-option-none', array('options_none_text' => $placeholder));

            marketengine_get_template('custom-fields/admin-templates/admin-field-option', $args);
            $options = ob_get_clean();
            break;

        default:
            break;
    }
    return $options;
}

function marketengine_load_inputs_for_view( $field ) {
    extract($field);
    switch($field_type) {
        case 'text':
        case 'textarea':
            $field_placeholder = isset($field_placeholder) && !empty($field_placeholder) ? $field_placeholder : 'N/A';
            
            echo "<tr><td><span>".__('Placeholder:', 'enginethemes')."</span></td><td>".$field_placeholder."</td></tr>";

            break;

        case 'number':
            $field_placeholder = isset($field_placeholder) && !empty($field_placeholder) ? $field_placeholder : 'N/A';
            $field_attribute = marketengine_field_attribute_array($field);
            $field_attribute = wp_parse_args($field_attribute, array('min' => 'N/A', 'max' => 'N/A'));


            echo "<tr><td><span>".__('Placeholder:', 'enginethemes')."</span></td><td>".$field_placeholder."</td></tr>";
            echo "<tr><td><span>".__('Minimum value:', 'enginethemes')."</span></td><td>".$field_attribute['min']."</td></tr>";
            echo "<tr><td><span>".__('Maximum value:', 'enginethemes')."</span></td><td>".$field_attribute['max']."</td></tr>";

            break;

        case 'date':
            break;

        case 'checkbox':
            $field_options = marketengine_cf_get_field_options($field_name);
            $field_options = marketengine_render_field_option($field_options);

            echo "<tr><td><span>".__('Options:', 'enginethemes')."</span></td><td>".$field_options."</td></tr>";
            break;

        case 'single-select':
        case 'multi-select':
            $field_options = marketengine_cf_get_field_options($field_name);
            $field_options = marketengine_render_field_option($field_options);

            $field_option_none = isset($field_placeholder) && !empty($field_placeholder) ? $field_placeholder : 'N/A';
            echo "<tr><td><span>".__('Option None Text:', 'enginethemes')."</span></td><td>".$field_option_none."</td></tr>";
            echo "<tr><td><span>".__('Options:', 'enginethemes')."</span></td><td>".$field_options."</td></tr>";
            break;

        default:
            break;
    }
}
add_action('marketengine_load_inputs_for_view', 'marketengine_load_inputs_for_view');

/**
 * Convert array of field option to string for displaying in textarea value format.
 *
 * @param array $options
 *
 * @return string $str
 */
function marketengine_field_option_to_string($options) {
    $str = '';
    $count = 0;
    foreach($options as $key => $option) {
        if($count == (count($options) -1) ) {
            $str .= sprintf("%s : %s", $option['key'], $option['label']);
        } else {
            $str .= sprintf("%s : %s". PHP_EOL, $option['key'], $option['label']);
        }
        $count++;
    }
    return $str;
}

/**
 * Convert array of field option to a string of labels.
 *
 * @param array $options
 *
 * @return string $str
 */
function marketengine_render_field_option($options) {
    $str = array();
    foreach($options as $key => $option) {
        $str[] = $option['label'];
    }
    return implode(', ', $str);
}

function marketengine_cf_pagination($args) {
    $big = 999999999;
    $current_page = empty($_REQUEST['paged']) ? 1 : absint( $_REQUEST['paged'] );
    echo paginate_links( array(
        'base' => add_query_arg( 'paged', '%#%' ),
        'format' => '',
        'current' => max( 1, $current_page ),
        'total' => $args['max_numb_pages']
    ) );
}