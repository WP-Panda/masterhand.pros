<?php // User Submitted Posts - Plugin Settings

if (!defined('ABSPATH')) die();



function usp_add_options_page() {
	
	// add_options_page($page_title, $menu_title, $capability, $menu_slug, $function)
	add_options_page(USP_PLUGIN, USP_PLUGIN, 'manage_options', USP_PATH, 'usp_render_form');
	
}
add_action('admin_menu', 'usp_add_options_page');



function usp_init() {
	
	// register_setting($option_group, $option_name, $sanitize_callback)
	register_setting('usp_plugin_options', 'usp_options', 'usp_validate_options');
	
}
add_action('admin_init', 'usp_init');



function usp_plugin_action_links($links, $file) {
	
	if ($file == USP_PATH && current_user_can('manage_options')) {
		
		$settings = '<a href="'. admin_url('options-general.php?page='. USP_PATH) .'">'. esc_html__('Settings', 'usp') .'</a>';
		
		array_unshift($links, $settings);
		
	}
	
	if ($file == USP_PATH) {
		
		$pro_href  = 'https://plugin-planet.com/usp-pro/?plugin';
		$pro_title = esc_attr__('Get USP Pro for unlimited forms!', 'usp');
		$pro_text  = esc_html__('Go Pro', 'usp');
		$pro_style = 'font-weight:bold;';
		
		$pro = '<a target="_blank" rel="noopener noreferrer" href="'. $pro_href .'" title="'. $pro_title .'" style="'. $pro_style .'">'. $pro_text .'</a>';
		
		array_unshift($links, $pro);
		
	}
	
	return $links;
	
}
add_filter('plugin_action_links', 'usp_plugin_action_links', 10, 2);



function add_usp_links($links, $file) {
	
	if ($file == USP_PATH) {
		
		$home_href  = 'https://perishablepress.com/user-submitted-posts/';
		$home_title = esc_attr__('Plugin Homepage', 'usp');
		$home_text  = esc_html__('Homepage', 'usp');
		
		$links[] = '<a target="_blank" rel="noopener noreferrer" href="'. $home_href .'" title="'. $home_title .'">'. $home_text .'</a>';
		
		$rate_href  = 'https://wordpress.org/support/plugin/user-submitted-posts/reviews/?rate=5#new-post';
		$rate_title = esc_attr__('Give USP a 5-star rating at WordPress.org', 'usp');
		$rate_text  = esc_html__('Rate this plugin&nbsp;&raquo;', 'usp');
		
		$links[] = '<a target="_blank" rel="noopener noreferrer" href="'. $rate_href .'" title="'. $rate_title .'">'. $rate_text .'</a>';
		
	}
	
	return $links;
	
}
add_filter('plugin_row_meta', 'add_usp_links', 10, 2);



// https://bit.ly/1MJWrau
function usp_filter_safe_styles($styles) {
	
	 $styles[] = 'display'; 
	 
	 return $styles;
	 
}
add_filter('safe_style_css', 'usp_filter_safe_styles');



function usp_compare_version() {
	
	global $usp_options;
	
	$usp_options = get_option('usp_options');
	
	$version_current = intval(USP_VERSION);
	$version_previous = isset($usp_options['usp_version']) ? intval($usp_options['usp_version']) : $version_current;
	
	if ($version_current > $version_previous) {
		
		$usp_options['version_alert'] = 0;
		$usp_options['usp_version'] = $version_current;
		
	} else {
		
		$usp_options['usp_version'] = $version_previous;
		
	}
	
	update_option('usp_options', $usp_options);
	
}
add_action('admin_init', 'usp_compare_version');



function usp_post_type() {
	
	$post_type = array(
		
		'post' => array(
			'value' => 'post',
			'label' => esc_html__('WP Post (recommended)', 'usp'),
		),
		'page' => array(
			'value' => 'page',
			'label' => esc_html__('WP Page', 'usp'),
		),
	);
	
	return apply_filters('usp_post_type_options', $post_type);
	
}



function usp_form_version() {
	
	$form_version = array(
		
		'current' => array(
			'value' => 'current',
			'label' => esc_html__('HTML5 Form + Default CSS', 'usp') .' <span class="mm-item-caption">'. esc_html__('(Recommended)', 'usp') .'</span>',
		),
		'disable' => array(
			'value' => 'disable',
			'label' => esc_html__('HTML5 Form + Disable CSS', 'usp') .' <span class="mm-item-caption">'. esc_html__('(Provide your own styles)', 'usp') .'</span>',
		),
		'custom' => array(
			'value' => 'custom',
			'label' => esc_html__('Custom Form + Custom CSS', 'usp') .' <span class="mm-item-caption">'. esc_html__('(Provide your own form template &amp; styles)', 'usp') .'</span>',
		),
	);
	
	return $form_version;
	
}



function usp_image_display() {
	
	$image_display = array(
		
		'before' => array(
			'value' => 'before',
			'label' => esc_html__('Auto-display before post content', 'usp')
		),
		'after' => array(
			'value' => 'after',
			'label' => esc_html__('Auto-display after post content', 'usp')
		),
		'disable' => array(
			'value' => 'disable',
			'label' => esc_html__('Do not auto-display submitted images', 'usp')
		),
	);
	
	return $image_display;
	
}



function usp_email_display() {
	
	$email_display = array(
		
		'before' => array(
			'value' => 'before',
			'label' => esc_html__('Auto-display before post content', 'usp')
		),
		'after' => array(
			'value' => 'after',
			'label' => esc_html__('Auto-display after post content', 'usp')
		),
		'disable' => array(
			'value' => 'disable',
			'label' => esc_html__('Do not auto-display submitted email', 'usp')
		),
	);
	
	return $email_display;
	
}



function usp_name_display() {
	
	$name_display = array(
		
		'before' => array(
			'value' => 'before',
			'label' => esc_html__('Auto-display before post content', 'usp')
		),
		'after' => array(
			'value' => 'after',
			'label' => esc_html__('Auto-display after post content', 'usp')
		),
		'disable' => array(
			'value' => 'disable',
			'label' => esc_html__('Do not auto-display submitted name', 'usp')
		),
	);
	
	return $name_display;
	
}



function usp_url_display() {
	
	$url_display = array(
		
		'before' => array(
			'value' => 'before',
			'label' => esc_html__('Auto-display before post content', 'usp')
		),
		'after' => array(
			'value' => 'after',
			'label' => esc_html__('Auto-display after post content', 'usp')
		),
		'disable' => array(
			'value' => 'disable',
			'label' => esc_html__('Do not auto-display submitted URL', 'usp')
		),
	);
	
	return $url_display;
	
}



function usp_custom_display() {
	
	$custom_display = array(
		
		'before' => array(
			'value' => 'before',
			'label' => esc_html__('Auto-display before post content', 'usp')
		),
		'after' => array(
			'value' => 'after',
			'label' => esc_html__('Auto-display after post content', 'usp')
		),
		'disable' => array(
			'value' => 'disable',
			'label' => esc_html__('Do not auto-display the Custom Field', 'usp')
		),
	);
	
	return $custom_display;
	
}



function usp_recaptcha_version() {
	
	$recaptcha_version = array(
		
		2 => array(
			'value' => 2,
			'label' => esc_html__('v2 (I&rsquo;m not a robot)', 'usp')
		),
		3 => array(
			'value' => 3,
			'label' => esc_html__('v3 (Hidden reCaptcha)', 'usp')
		),
	);
	
	return $recaptcha_version;
	
}



function usp_form_field_options($args) {
	
	global $usp_options;
	
	$name  = isset($args[0]) ? $args[0] : '';
	$label = isset($args[1]) ? $args[1] : '';
	
	$option = isset($usp_options[$name]) ? $usp_options[$name] : '';
	
	$selected_show = ($option === 'show') ? 'selected="selected"' : '';
	$selected_optn = ($option === 'optn') ? 'selected="selected"' : '';
	$selected_hide = ($option === 'hide') ? 'selected="selected"' : '';
	
	$output  = '<tr>';
	$output .= '<th scope="row"><label class="description" for="usp_options['. esc_attr($name) .']">'. esc_html($label) .'</label></th>';
	$output .= '<td>';
	$output .= '<select name="usp_options['. esc_attr($name) .']" id="usp_options['. esc_attr($name) .']">';
	
	$output .= '<option '. $selected_show .' value="show">'. esc_html__('Enable and require', 'usp')        .'</option>';
	$output .= '<option '. $selected_optn .' value="optn">'. esc_html__('Enable but do not require', 'usp') .'</option>';
	$output .= '<option '. $selected_hide .' value="hide">'. esc_html__('Disable this field', 'usp')        .'</option>';
	
	$output .= '</select>';
	$output .= '</td>';
	$output .= '</tr>';
	
	return $output;
	
}



function usp_form_field_options_custom() {
	
	global $usp_options;
	
	$name  = 'custom_field';
	$label = esc_html__('Custom Field', 'usp');
	
	$option = isset($usp_options[$name]) ? $usp_options[$name] : '';
	
	$selected_show = ($option === 'show') ? 'selected="selected"' : '';
	$selected_optn = ($option === 'optn') ? 'selected="selected"' : '';
	$selected_hide = ($option === 'hide') ? 'selected="selected"' : '';
	
	$output  = '<tr>';
	$output .= '<th scope="row"><label class="description" for="usp_options['. esc_attr($name) .']">'. esc_html($label) .'</label></th>';
	$output .= '<td>';
	$output .= '<select name="usp_options['. esc_attr($name) .']" id="usp_options['. esc_attr($name) .']">';
	
	$output .= '<option '. $selected_show .' value="show">'. esc_html__('Enable and require', 'usp')        .'</option>';
	$output .= '<option '. $selected_optn .' value="optn">'. esc_html__('Enable but do not require', 'usp') .'</option>';
	$output .= '<option '. $selected_hide .' value="hide">'. esc_html__('Disable this field', 'usp')        .'</option>';
	
	$output .= '</select> ';
	$output .= '<span class="mm-item-caption">'. esc_html__('(Visit', 'usp') .' <a href="#usp-custom-field">';
	$output .= esc_html__('Custom Field', 'usp') .'</a> '. esc_html__('to configure options)', 'usp') .'</span>';
	$output .= '</td>';
	$output .= '</tr>';
	
	return $output;
	
}



function usp_form_field_options_captcha() {
	
	global $usp_options;
	
	$name  = 'usp_captcha';
	$label = esc_html__('Challenge Question', 'usp');
	
	$option = isset($usp_options[$name]) ? $usp_options[$name] : '';
	
	$selected_show = ($option === 'show') ? 'selected="selected"' : '';
	$selected_hide = ($option === 'hide') ? 'selected="selected"' : '';
	
	$output  = '<tr>';
	$output .= '<th scope="row"><label class="description" for="usp_options['. esc_attr($name) .']">'. esc_html($label) .'</label></th>';
	$output .= '<td>';
	$output .= '<select name="usp_options['. esc_attr($name) .']" id="usp_options['. esc_attr($name) .']">';
	
	$output .= '<option '. $selected_show .' value="show">'. esc_html__('Enable and require', 'usp') .'</option>';
	$output .= '<option '. $selected_hide .' value="hide">'. esc_html__('Disable this field', 'usp')  .'</option>';
	
	$output .= '</select> ';
	$output .= '<span class="mm-item-caption">'. esc_html__('(Visit', 'usp') .' <a href="#usp-challenge-question">';
	$output .= esc_html__('Challenge Question', 'usp') .'</a> '. esc_html__('to configure options)', 'usp') .'</span>';
	$output .= '</td>';
	$output .= '</tr>';
	
	return $output;
	
}



function usp_form_field_options_recaptcha() {
	
	global $usp_options;
	
	$name  = 'usp_recaptcha';
	$label = esc_html__('Google reCAPTCHA', 'usp');
	
	$option = isset($usp_options[$name]) ? $usp_options[$name] : '';
	
	$selected_show = ($option === 'show') ? 'selected="selected"' : '';
	$selected_hide = ($option === 'hide') ? 'selected="selected"' : '';
	
	$output  = '<tr>';
	$output .= '<th scope="row"><label class="description" for="usp_options['. esc_attr($name) .']">'. esc_html($label) .'</label></th>';
	$output .= '<td>';
	$output .= '<select name="usp_options['. esc_attr($name) .']" id="usp_options['. esc_attr($name) .']">';
	
	$output .= '<option '. $selected_show .' value="show">'. esc_html__('Enable and require', 'usp') .'</option>';
	$output .= '<option '. $selected_hide .' value="hide">'. esc_html__('Disable this field', 'usp')  .'</option>';
	
	$output .= '</select> ';
	$output .= '<span class="mm-item-caption">'. esc_html__('(Visit', 'usp') .' <a href="#usp-recaptcha">';
	$output .= esc_html__('Google reCAPTCHA', 'usp') .'</a> '. esc_html__('to configure options)', 'usp') .'</span>';
	$output .= '</td>';
	$output .= '</tr>';
	
	return $output;
	
}



function usp_form_field_options_images() {
	
	global $usp_options;
	
	$name  = 'usp_images';
	$label = esc_html__('Post Images', 'usp');
	
	$option = isset($usp_options[$name]) ? $usp_options[$name] : '';
	
	$selected_show = ($option === 'show') ? 'selected="selected"' : '';
	$selected_hide = ($option === 'hide') ? 'selected="selected"' : '';
	
	$output  = '<tr>';
	$output .= '<th scope="row"><label class="description" for="usp_options['. esc_attr($name) .']">'. esc_html($label) .'</label></th>';
	$output .= '<td>';
	$output .= '<select name="usp_options['. esc_attr($name) .']" id="usp_options['. esc_attr($name) .']">';
	
	$output .= '<option '. $selected_show .' value="show">'. esc_html__('Enable', 'usp') .'</option>';
	$output .= '<option '. $selected_hide .' value="hide">'. esc_html__('Disable', 'usp') .'</option>';
	
	$output .= '</select> ';
	$output .= '<span class="mm-item-caption">'. esc_html__('(Visit', 'usp') .' <a href="#usp-image-uploads">';
	$output .= esc_html__('Image Uploads', 'usp') .'</a> '. esc_html__('to configure options)', 'usp') .'</span>';
	$output .= '</td>';
	$output .= '</tr>';
	
	return $output;
	
}



function usp_form_display_options() {
	
	global $usp_options;
	
	$radio_setting = isset($usp_options['usp_form_version']) ? $usp_options['usp_form_version'] : '';
	
	$form_styles = usp_form_version();
	
	$output = '';
	
	foreach ($form_styles as $form_style) {
		
		$label = isset($form_style['label']) ? $form_style['label'] : '';
		$value = isset($form_style['value']) ? $form_style['value'] : '';
		
		$checked = (!empty($radio_setting) && $radio_setting === $value) ? 'checked="checked"' : '';
		
		$class = ($value === 'custom') ? 'usp-custom-form' : 'usp-form';
		
		$output .= '<div class="mm-radio-inputs">';
		$output .= '<input type="radio" name="usp_options[usp_form_version]" class="'. $class .'" value="'. esc_attr($value) .'" '. $checked .' /> '. $label;
		$output .= '</div>';
		
	}
	
	return $output;
	
}



function usp_post_type_options() {
	
	global $usp_options;
	
	$usp_post_type = usp_post_type();
	
	$selected_option = isset($usp_options['usp_post_type']) ? $usp_options['usp_post_type'] : 'post';
	
	$select_options = '<select name="usp_options[usp_post_type]">';
	
	foreach($usp_post_type as $k => $v) {
		
		$selected = selected($selected_option === $k, true, false);
		
		$value = isset($v['value']) ? $v['value'] : null;
		$label = isset($v['label']) ? $v['label'] : null;
		
		$select_options .= '<option value="'. $value .'"'. $selected .'>'. $label .'</option>';
		
	}
	
	$select_options .= '</select>';
	
	return $select_options;
	
}



function usp_post_status_options() {
	
	global $usp_options;
	
	$approved = isset($usp_options['number-approved']) ? intval($usp_options['number-approved']) : -1;
	
	$output = '<select id="usp_options[number-approved]" name="usp_options[number-approved]">';
	
	foreach (range(-2, 20) as $v) {
		
		if     ($v === -2) $k = esc_html__('Draft', 'usp');	
		elseif ($v === -1) $k = esc_html__('Pending (default)', 'usp');
		elseif ($v === 0)  $k = esc_html__('Publish immediately', 'usp');
		elseif ($v === 1)  $k = esc_html__('Publish after 1 approved post', 'usp');
		else               $k = esc_html__('Publish after ', 'usp') . $v . esc_html__(' approved posts', 'usp');
		
		$selected = selected($v, $approved, false);
		
		$output .= '<option '. $selected .' value="'. $v .'">'. $k .'</option>';
		
	}
	
	$output .= '</select>';
	
	return $output;
	
}



function usp_post_category_options() {
	
	global $usp_options;
	
	$options = isset($usp_options['categories']) ? $usp_options['categories'] : array();
	
	$cats = get_categories(array('parent' => 0, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0));
	
	if (empty($cats)) return;
	
	$usp_cats = array();
	
	$output  = '<p class="usp-category-options-desc">'. esc_html__('Select categories to display in the Category field.', 'usp') .'</p>';
	$output .= '<ul class="usp-category-options">';
	
	foreach ($cats as $c) {
		
		// parents
		
		$output .= '<li><input type="checkbox" name="usp_options[categories][]" id="usp_options[categories][]" value="'. esc_attr($c->term_id) .'" '. checked(true, in_array($c->term_id, $options), false) .'> ';
		$output .= '<label for="usp_options[categories][]"><a href="'. esc_url(get_category_link($c->term_id)) .'" title="Cat ID: '. esc_attr($c->term_id) .'" target="_blank" rel="noopener noreferrer">'. esc_html($c->name) .'</a></label></li>';
		
		$usp_cats['c'][] = array('id' => esc_attr($c->term_id), 'c1' => array());
		$children = get_terms('category', array('parent' => esc_attr($c->term_id), 'hide_empty' => 0));
		
		if (!empty($children)) {
			
			$output .= '<li><ul>';
			
			foreach ($children as $c1) {

				// children
				
				$usp_cats['c'][]['c1'][] = array('id' => esc_attr($c1->term_id), 'c2' => array());
				$grandchildren = get_terms('category', array('parent' => esc_attr($c1->term_id), 'hide_empty' => 0));
				
				if (!empty($grandchildren)) {
					
					$output .= '<li><input type="checkbox" name="usp_options[categories][]" id="usp_options[categories][]" value="'. esc_attr($c1->term_id) .'" '. checked(true, in_array($c1->term_id, $options), false) .'> ';
					$output .= '<label for="usp_options[categories][]"><a href="'. esc_url(get_category_link($c1->term_id)) .'" title="Cat ID: '. esc_attr($c1->term_id) .'" target="_blank" rel="noopener noreferrer">'. esc_html($c1->name) .'</a></label>';
					$output .= '<ul>';
					
					foreach ($grandchildren as $c2) {

						// grandchildren
						
						$usp_cats['c'][]['c1'][]['c2'][] = array('id' => esc_attr($c2->term_id), 'c3' => array());
						$great_grandchildren = get_terms('category', array('parent' => esc_attr($c2->term_id), 'hide_empty' => 0));
						
						if (!empty($great_grandchildren)) {
							
							$output .= '<li><input type="checkbox" name="usp_options[categories][]" id="usp_options[categories][]" value="'. esc_attr($c2->term_id) .'" '. checked(true, in_array($c2->term_id, $options), false) .'> ';
							$output .= '<label for="usp_options[categories][]"><a href="'. esc_url(get_category_link($c2->term_id)) .'" title="Cat ID: '. esc_attr($c2->term_id) .'" target="_blank" rel="noopener noreferrer">'. esc_html($c2->name) .'</a></label>';
							$output .= '<ul>';
							
							foreach ($great_grandchildren as $c3) {
								
								// great enkelkinder
								
								$usp_cats['c'][]['c1'][]['c2'][]['c3'][] = array('id' => esc_attr($c3->term_id), 'c4' => array());
								$great_great_grandchildren = get_terms('category', array('parent' => esc_attr($c3->term_id), 'hide_empty' => 0));
								
								if (!empty($great_great_grandchildren)) {
									
									$output .= '<li><input type="checkbox" name="usp_options[categories][]" id="usp_options[categories][]" value="'. esc_attr($c3->term_id) .'" '. checked(true, in_array($c3->term_id, $options), false) .'> ';
									$output .= '<label for="usp_options[categories][]"><a href="'. esc_url(get_category_link($c3->term_id)) .'" title="Cat ID: '. esc_attr($c3->term_id) .'" target="_blank" rel="noopener noreferrer">'. esc_html($c3->name) .'</a></label>';
									$output .= '<ul>';
									
									foreach ($great_great_grandchildren as $c4) {
										
										// great great grandchildren
										
										$usp_cats['c'][]['c1'][]['c2'][]['c3'][]['c4'][] = array('id' => esc_attr($c4->term_id));
										$output .= '<li><input type="checkbox" name="usp_options[categories][]" id="usp_options[categories][]" value="'. esc_attr($c4->term_id) .'" '. checked(true, in_array($c4->term_id, $options), false) .'> ';
										$output .= '<label for="usp_options[categories][]"><a href="'. esc_url(get_category_link($c4->term_id)) .'" title="Cat ID: '. esc_attr($c4->term_id) .'" target="_blank" rel="noopener noreferrer">'. esc_html($c4->name) .'</a></label></li>';
										
									}
									$output .= '</ul></li>'; // great great grandchildren
									
								} else {
									$output .= '<li><input type="checkbox" name="usp_options[categories][]" id="usp_options[categories][]" value="'. esc_attr($c3->term_id) .'" '. checked(true, in_array($c3->term_id, $options), false) .'> ';
									$output .= '<label for="usp_options[categories][]"><a href="'. esc_url(get_category_link($c3->term_id)) .'" title="Cat ID: '. esc_attr($c3->term_id) .'" target="_blank" rel="noopener noreferrer">'. esc_html($c3->name) .'</a></label></li>';
								}
							}
							$output .= '</ul></li>'; // great grandchildren
						} else {
							$output .= '<li><input type="checkbox" name="usp_options[categories][]" id="usp_options[categories][]" value="'. esc_attr($c2->term_id) .'" '. checked(true, in_array($c2->term_id, $options), false) .'> ';
							$output .= '<label for="usp_options[categories][]"><a href="'. esc_url(get_category_link($c2->term_id)) .'" title="Cat ID: '. esc_attr($c2->term_id) .'" target="_blank" rel="noopener noreferrer">'. esc_html($c2->name) .'</a></label></li>';
						}
					}
					$output .= '</ul></li>'; // grandchildren
				} else {
					$output .= '<li><input type="checkbox" name="usp_options[categories][]" id="usp_options[categories][]" value="'. esc_attr($c1->term_id) .'" '. checked(true, in_array($c1->term_id, $options), false) .'> ';
					$output .= '<label for="usp_options[categories][]"><a href="'. esc_url(get_category_link($c1->term_id)) .'" title="Cat ID: '. esc_attr($c1->term_id) .'" target="_blank" rel="noopener noreferrer">'. esc_html($c1->name) .'</a></label></li>';
				}
			}
			$output .= '</ul></li>'; // children
		}
	}
	
	$output .= '</ul>'; // parents
	
	return $output;
	
}



function usp_post_author_options() {
	
	global $usp_options, $wpdb;
	
	$user_count = count_users();
	
	$user_total = isset($user_count['total_users']) ? intval($user_count['total_users']) : 1;
	
	$user_max = apply_filters('usp_max_users', 200);
	
	$limit = ($user_total > $user_max) ? $user_max : $user_total;
	
	if (is_multisite()) {
		
		$args = array('blog_id' => get_current_blog_id(), 'number'  => $limit);
		
		$user_query = new WP_User_Query($args);
		
		$users = $user_query->get_results();
		
	} else {
		
		$query = "SELECT ID, display_name FROM {$wpdb->users} LIMIT %d";
		
		$users = $wpdb->get_results($wpdb->prepare($query, $limit));
		
	}
	
	$output = '<select id="usp_options[author]" name="usp_options[author]">';
	
	foreach($users as $user) {
		
		$selected = isset($usp_options['author']) ? selected($usp_options['author'], $user->ID, false) : '';
		
		$output .= '<option '. $selected .'value="'. esc_attr($user->ID) .'">'. esc_html($user->display_name) .'</option>';
		
	}
	
	$output .= '</select>';
	
	return $output;
	
}



function usp_auto_display_options($item) {
	
	global $usp_options;
	
	$usp_image_display  = usp_image_display();
	$usp_email_display  = usp_email_display();
	$usp_name_display   = usp_name_display();
	$usp_url_display    = usp_url_display();
	$usp_custom_display = usp_custom_display();
	
	if ($item === 'images') {
		
		$array = $usp_image_display;
		$key = 'auto_display_images';
		
	} elseif ($item === 'email') {
		
		$array = $usp_email_display;
		$key = 'auto_display_email';
		
	} elseif ($item === 'name') {
		
		$array = $usp_name_display;
		$key = 'auto_display_name';
		
	} elseif ($item === 'url') {
		
		$array = $usp_url_display;
		$key = 'auto_display_url';
		
	} elseif ($item === 'custom') {
		
		$array = $usp_custom_display;
		$key = 'auto_display_custom';
		
	}
	
	$radio_setting = isset($usp_options[$key]) ? $usp_options[$key] : '';
	
	$output = '';
	
	foreach ($array as $arr) {
		
		$label = isset($arr['label']) ? $arr['label'] : '';
		$value = isset($arr['value']) ? $arr['value'] : '';
		
		$checked = (!empty($radio_setting) && $radio_setting === $value) ? 'checked="checked"' : '';
		
		$output .= '<div class="mm-radio-inputs">';
		$output .= '<input type="radio" name="usp_options['. esc_attr($key) .']" value="'. esc_attr($value) .'" '. $checked .' /> '. esc_html($label);
		$output .= '</div>';
		
	}
	
	return $output;
	
}



function usp_form_field_recaptcha() {
	
	global $usp_options;
	
	$version = isset($usp_options['recaptcha_version']) ? $usp_options['recaptcha_version'] : 2;
	
	$output = '<select id="usp_options[recaptcha_version]" name="usp_options[recaptcha_version]">';
	
	foreach(usp_recaptcha_version() as $option) {
		
		$option_value = isset($option['value']) ? $option['value'] : '';
		$option_label = isset($option['label']) ? $option['label'] : '';
		
		$output .= '<option '. selected($option_value, $version, false) .' value="'. esc_attr($option_value) .'">'. esc_attr($option_label) .'</option>';
		
	}
	
	$output .= '</select>';
	
	return $output;
	
}



function usp_add_defaults() {
	
	$currentUser = wp_get_current_user();
	
	$admin_mail = get_bloginfo('admin_email');
	
	$tmp = get_option('usp_options');
	
	if ((isset($tmp['default_options']) && $tmp['default_options'] == '1') || (!is_array($tmp))) {
		
		$arr = array(
			'usp_version'          => USP_VERSION,
			'version_alert'        => 0,
			'default_options'      => 0,
			'author'               => $currentUser->ID,
			'categories'           => array(get_option('default_category')),
			'multiple-cats'        => false,
			'number-approved'      => -1,
			'redirect-url'         => '',
			'error-message'        => esc_html__('There was an error. Please ensure that you have added a title, some content, and that you have uploaded only images.', 'usp'),
			'min-images'           => 0,
			'max-images'           => 1,
			'min-image-height'     => 0,
			'min-image-width'      => 0,
			'max-image-height'     => 1500,
			'max-image-width'      => 1500,
			'usp_name'             => 'show',
			'usp_url'              => 'show',
			'usp_email'            => 'hide',
			'usp_title'            => 'show',
			'usp_tags'             => 'show',
			'usp_category'         => 'show',
			'usp_images'           => 'hide',
			'upload-message'       => esc_html__('Please select your image(s) to upload.', 'usp'),
			'usp_question'         => '1 + 1 =',
			'usp_response'         => '2',
			'usp_casing'           => 0,
			'usp_captcha'          => 'show',
			'usp_content'          => 'show',
			'success-message'      => esc_html__('Success! Thank you for your submission.', 'usp'),
			'usp_form_version'     => 'current',
			'usp_email_alerts'     => 1,
			'usp_email_html'       => 0,
			'usp_email_address'    => $admin_mail,
			'usp_email_from'       => $admin_mail,
			'usp_use_author'       => 0,
			'usp_use_url'          => 0,
			'usp_use_email'        => 0,
			'usp_use_cat'          => 0,
			'usp_use_cat_id'       => '',
			'usp_include_js'       => 1,
			'usp_display_url'      => '',
			'usp_form_content'     => '',
			'usp_existing_tags'    => 0,
			'usp_richtext_editor'  => 0,
			'usp_featured_images'  => 0,
			'usp_add_another'      => '',
			'disable_required'     => 0,
			'titles_unique'        => 0,
			'enable_shortcodes'    => 0,
			'disable_ip_tracking'  => 0,
			'email_alert_subject'  => '',
			'email_alert_message'  => '',
			'auto_display_images'  => 'disable',
			'auto_display_email'   => 'disable', 
			'auto_display_name'    => 'disable', 
			'auto_display_url'     => 'disable', 
			'auto_image_markup'    => '<a href="%%full%%"><img src="%%thumb%%" width="%%width%%" height="%%height%%" alt="%%title%%" style="display:inline-block;" /></a> ',
			'auto_email_markup'    => '<p><a href="mailto:%%email%%">'. esc_html__('Email', 'usp') .'</a></p>',
			'auto_name_markup'     => '<p>%%author%%</p>',
			'auto_url_markup'      => '<p><a href="%%url%%">'. esc_html__('URL', 'usp') .'</a></p>',
			'logged_in_users'      => 0,
			'disable_author'       => 0,
			'recaptcha_public'     => '',
			'recaptcha_private'    => '',
			'recaptcha_version'    => 2,
			'usp_recaptcha'        => 'hide',
			'usp_post_type'        => 'post',
			'custom_field'         => 'hide',
			'custom_name'          => 'usp_custom_field',
			'custom_label'         => esc_html__('Custom Field', 'usp'),
			'auto_display_custom'  => 'disable',
			'auto_custom_markup'   => '<p>%%custom_label%% : %%custom_name%% : %%custom_value%%</p>',
			'custom_checkbox'      => false,
			'custom_checkbox_name' => 'usp_custom_checkbox',
			'custom_checkbox_text' => 'I agree the to the terms.',
			'custom_checkbox_err'  => 'Custom checkbox required',
		);
		
		update_option('usp_options', $arr);
		
	}
	
}
register_activation_hook(dirname(dirname(__FILE__)).'/user-submitted-posts.php', 'usp_add_defaults');



function usp_delete_plugin_options() {
	
	delete_option('usp_options');
	
}
if (isset($usp_options['default_options']) && $usp_options['default_options'] == 1) {
	
	register_deactivation_hook(dirname(dirname(__FILE__)).'/user-submitted-posts.php', 'usp_delete_plugin_options');
	
}



function usp_update_category_option($option_name, $old_value, $value) { 
	
	usp_clear_cookies();
	
}
add_action('updated_option', 'usp_update_category_option', 10, 3);



function usp_validate_options($input) {
	
	global $usp_options;
	
	if (!isset($input['version_alert'])) $input['version_alert'] = null;
	$input['version_alert'] = ($input['version_alert'] == 1 ? 1 : 0);
	
	if (!isset($input['default_options'])) $input['default_options'] = null;
	$input['default_options'] = ($input['default_options'] == 1 ? 1 : 0);
	
	if (isset($input['categories'])) $input['categories'] = is_array($input['categories']) && !empty($input['categories']) ? array_unique($input['categories']) : array(get_option('default_category'));
	
	$input['number-approved']  = is_numeric($input['number-approved']) ? intval($input['number-approved']) : -1;
	
	$input['min-images']       = is_numeric($input['min-images']) ? intval($input['min-images']) : $input['max-images'];
	$input['max-images']       = (is_numeric($input['max-images']) && ($usp_options['min-images'] <= abs($input['max-images']))) ? intval($input['max-images']) : $usp_options['max-images'];
	
	$input['min-image-height'] = is_numeric($input['min-image-height']) ? intval($input['min-image-height']) : $usp_options['min-image-height'];
	$input['min-image-width']  = is_numeric($input['min-image-width'])  ? intval($input['min-image-width'])  : $usp_options['min-image-width'];
	
	$input['max-image-height'] = (is_numeric($input['max-image-height']) && ($usp_options['min-image-height'] <= $input['max-image-height'])) ? intval($input['max-image-height']) : $usp_options['max-image-height'];
	$input['max-image-width']  = (is_numeric($input['max-image-width'])  && ($usp_options['min-image-width']  <= $input['max-image-width']))  ? intval($input['max-image-width'])  : $usp_options['max-image-width'];
	
	$usp_form_version = usp_form_version();
	if (!isset($input['usp_form_version'])) $input['usp_form_version'] = null;
	if (!array_key_exists($input['usp_form_version'], $usp_form_version)) $input['usp_form_version'] = null;
	
	$usp_image_display = usp_image_display();
	if (!isset($input['auto_display_images'])) $input['auto_display_images'] = null;
	if (!array_key_exists($input['auto_display_images'], $usp_image_display)) $input['auto_display_images'] = null;
	
	$usp_email_display = usp_email_display();
	if (!isset($input['auto_display_email'])) $input['auto_display_email'] = null;
	if (!array_key_exists($input['auto_display_email'], $usp_email_display)) $input['auto_display_email'] = null;
	
	$usp_url_display = usp_url_display();
	if (!isset($input['auto_display_url'])) $input['auto_display_url'] = null;
	if (!array_key_exists($input['auto_display_url'], $usp_url_display)) $input['auto_display_url'] = null;
	
	$usp_custom_display = usp_custom_display();
	if (!isset($input['auto_display_custom'])) $input['auto_display_custom'] = null;
	if (!array_key_exists($input['auto_display_custom'], $usp_custom_display)) $input['auto_display_custom'] = null;
	
	$usp_post_type = usp_post_type();
	if (!isset($input['usp_post_type'])) $input['usp_post_type'] = null;
	if (!array_key_exists($input['usp_post_type'], $usp_post_type)) $input['usp_post_type'] = null;
	
	$usp_recaptcha_version = usp_recaptcha_version();
	if (!isset($input['recaptcha_version'])) $input['recaptcha_version'] = null;
	if (!array_key_exists($input['recaptcha_version'], $usp_recaptcha_version)) $input['recaptcha_version'] = null;
	
	if (isset($input['author']))               $input['author']               = wp_filter_nohtml_kses($input['author']);               else $input['author']               = null;
	if (isset($input['usp_name']))             $input['usp_name']             = wp_filter_nohtml_kses($input['usp_name']);             else $input['usp_name']             = null;
	if (isset($input['usp_url']))              $input['usp_url']              = wp_filter_nohtml_kses($input['usp_url']);              else $input['usp_url']              = null; 
	if (isset($input['usp_email']))            $input['usp_email']            = wp_filter_nohtml_kses($input['usp_email']);            else $input['usp_email']            = null;
	if (isset($input['usp_title']))            $input['usp_title']            = wp_filter_nohtml_kses($input['usp_title']);            else $input['usp_title']            = null;
	if (isset($input['usp_tags']))             $input['usp_tags']             = wp_filter_nohtml_kses($input['usp_tags']);             else $input['usp_tags']             = null;
	if (isset($input['usp_category']))         $input['usp_category']         = wp_filter_nohtml_kses($input['usp_category']);         else $input['usp_category']         = null;
	if (isset($input['usp_images']))           $input['usp_images']           = wp_filter_nohtml_kses($input['usp_images']);           else $input['usp_images']           = null;
	if (isset($input['usp_question']))         $input['usp_question']         = wp_filter_nohtml_kses($input['usp_question']);         else $input['usp_question']         = null;
	if (isset($input['usp_captcha']))          $input['usp_captcha']          = wp_filter_nohtml_kses($input['usp_captcha']);          else $input['usp_captcha']          = null;
	if (isset($input['usp_content']))          $input['usp_content']          = wp_filter_nohtml_kses($input['usp_content']);          else $input['usp_content']          = null;
	if (isset($input['usp_email_address']))    $input['usp_email_address']    = wp_filter_nohtml_kses($input['usp_email_address']);    else $input['usp_email_address']    = null;
	if (isset($input['usp_email_from']))       $input['usp_email_from']       = wp_filter_nohtml_kses($input['usp_email_from']);       else $input['usp_email_from']       = null;
	if (isset($input['usp_use_cat_id']))       $input['usp_use_cat_id']       = wp_filter_nohtml_kses($input['usp_use_cat_id']);       else $input['usp_use_cat_id']       = null;
	if (isset($input['usp_display_url']))      $input['usp_display_url']      = wp_filter_nohtml_kses($input['usp_display_url']);      else $input['usp_display_url']      = null;
	if (isset($input['redirect-url']))         $input['redirect-url']         = wp_filter_nohtml_kses($input['redirect-url']);         else $input['redirect-url']         = null;
	if (isset($input['email_alert_subject']))  $input['email_alert_subject']  = wp_filter_nohtml_kses($input['email_alert_subject']);  else $input['email_alert_subject']  = null;
	if (isset($input['recaptcha_public']))     $input['recaptcha_public']     = wp_filter_nohtml_kses($input['recaptcha_public']);     else $input['recaptcha_public']     = null;
	if (isset($input['recaptcha_private']))    $input['recaptcha_private']    = wp_filter_nohtml_kses($input['recaptcha_private']);    else $input['recaptcha_private']    = null;
	if (isset($input['usp_recaptcha']))        $input['usp_recaptcha']        = wp_filter_nohtml_kses($input['usp_recaptcha']);        else $input['usp_recaptcha']        = null;
	if (isset($input['custom_field']))         $input['custom_field']         = wp_filter_nohtml_kses($input['custom_field']);         else $input['custom_field']         = null;
	if (isset($input['custom_name']))          $input['custom_name']          = wp_filter_nohtml_kses($input['custom_name']);          else $input['custom_name']          = null;
	if (isset($input['custom_label']))         $input['custom_label']         = wp_filter_nohtml_kses($input['custom_label']);         else $input['custom_label']         = null;
	if (isset($input['custom_checkbox_name'])) $input['custom_checkbox_name'] = wp_filter_nohtml_kses($input['custom_checkbox_name']); else $input['custom_checkbox_name'] = null;
	if (isset($input['custom_checkbox_err']))  $input['custom_checkbox_err']  = wp_filter_nohtml_kses($input['custom_checkbox_err']);  else $input['custom_checkbox_err']  = null;
	
	// dealing with kses
	global $allowedposttags;
	$allowed_atts = array(
		'align'      => array(),
		'class'      => array(),
		'type'       => array(),
		'id'         => array(),
		'dir'        => array(),
		'lang'       => array(),
		'style'      => array(),
		'xml:lang'   => array(),
		'src'        => array(),
		'alt'        => array(),
		'href'       => array(),
		'rel'        => array(),
		'rev'        => array(),
		'target'     => array(),
		'novalidate' => array(),
		'type'       => array(),
		'value'      => array(),
		'name'       => array(),
		'tabindex'   => array(),
		'action'     => array(),
		'method'     => array(),
		'for'        => array(),
		'width'      => array(),
		'height'     => array(),
		'data'       => array(),
		'data-rel'   => array(),
		'title'      => array(),
	);
	$allowedposttags['form']     = $allowed_atts;
	$allowedposttags['label']    = $allowed_atts;
	$allowedposttags['input']    = $allowed_atts;
	$allowedposttags['textarea'] = $allowed_atts;
	$allowedposttags['iframe']   = $allowed_atts;
	$allowedposttags['script']   = $allowed_atts;
	$allowedposttags['style']    = $allowed_atts;
	$allowedposttags['strong']   = $allowed_atts;
	$allowedposttags['small']    = $allowed_atts;
	$allowedposttags['table']    = $allowed_atts;
	$allowedposttags['span']     = $allowed_atts;
	$allowedposttags['abbr']     = $allowed_atts;
	$allowedposttags['code']     = $allowed_atts;
	$allowedposttags['pre']      = $allowed_atts;
	$allowedposttags['div']      = $allowed_atts;
	$allowedposttags['img']      = $allowed_atts;
	$allowedposttags['h1']       = $allowed_atts;
	$allowedposttags['h2']       = $allowed_atts;
	$allowedposttags['h3']       = $allowed_atts;
	$allowedposttags['h4']       = $allowed_atts;
	$allowedposttags['h5']       = $allowed_atts;
	$allowedposttags['h6']       = $allowed_atts;
	$allowedposttags['ol']       = $allowed_atts;
	$allowedposttags['ul']       = $allowed_atts;
	$allowedposttags['li']       = $allowed_atts;
	$allowedposttags['em']       = $allowed_atts;
	$allowedposttags['hr']       = $allowed_atts;
	$allowedposttags['br']       = $allowed_atts;
	$allowedposttags['tr']       = $allowed_atts;
	$allowedposttags['td']       = $allowed_atts;
	$allowedposttags['p']        = $allowed_atts;
	$allowedposttags['a']        = $allowed_atts;
	$allowedposttags['b']        = $allowed_atts;
	$allowedposttags['i']        = $allowed_atts;
	
	if (isset($input['usp_form_content']))     $input['usp_form_content']     = wp_kses_post($input['usp_form_content'],     $allowedposttags); else $input['usp_form_content']     = null;
	if (isset($input['error-message']))        $input['error-message']        = wp_kses_post($input['error-message'],        $allowedposttags); else $input['error-message']        = null;
	if (isset($input['upload-message']))       $input['upload-message']       = wp_kses_post($input['upload-message'],       $allowedposttags); else $input['upload-message']       = null;
	if (isset($input['success-message']))      $input['success-message']      = wp_kses_post($input['success-message'],      $allowedposttags); else $input['success-message']      = null;
	if (isset($input['usp_add_another']))      $input['usp_add_another']      = wp_kses_post($input['usp_add_another'],      $allowedposttags); else $input['usp_add_another']      = null;
	if (isset($input['email_alert_message']))  $input['email_alert_message']  = wp_kses_post($input['email_alert_message'],  $allowedposttags); else $input['email_alert_message']  = null;
	if (isset($input['auto_image_markup']))    $input['auto_image_markup']    = wp_kses_post($input['auto_image_markup'],    $allowedposttags); else $input['auto_image_markup']    = null;
	if (isset($input['auto_email_markup']))    $input['auto_email_markup']    = wp_kses_post($input['auto_email_markup'],    $allowedposttags); else $input['auto_email_markup']    = null;
	if (isset($input['auto_url_markup']))      $input['auto_url_markup']      = wp_kses_post($input['auto_url_markup'],      $allowedposttags); else $input['auto_url_markup']      = null;
	if (isset($input['auto_custom_markup']))   $input['auto_custom_markup']   = wp_kses_post($input['auto_custom_markup'],   $allowedposttags); else $input['auto_custom_markup']   = null;
	if (isset($input['custom_checkbox_text'])) $input['custom_checkbox_text'] = wp_kses_post($input['custom_checkbox_text'], $allowedposttags); else $input['custom_checkbox_text'] = null;
	
	if (!isset($input['usp_casing'])) $input['usp_casing'] = null;
	$input['usp_casing'] = ($input['usp_casing'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_email_alerts'])) $input['usp_email_alerts'] = null;
	$input['usp_email_alerts'] = ($input['usp_email_alerts'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_email_html'])) $input['usp_email_html'] = null;
	$input['usp_email_html'] = ($input['usp_email_html'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_use_author'])) $input['usp_use_author'] = null;
	$input['usp_use_author'] = ($input['usp_use_author'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_use_url'])) $input['usp_use_url'] = null;
	$input['usp_use_url'] = ($input['usp_use_url'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_use_email'])) $input['usp_use_email'] = null;
	$input['usp_use_email'] = ($input['usp_use_email'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_use_cat'])) $input['usp_use_cat'] = null;
	$input['usp_use_cat'] = ($input['usp_use_cat'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_include_js'])) $input['usp_include_js'] = null;
	$input['usp_include_js'] = ($input['usp_include_js'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_existing_tags'])) $input['usp_existing_tags'] = null;
	$input['usp_existing_tags'] = ($input['usp_existing_tags'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_richtext_editor'])) $input['usp_richtext_editor'] = null;
	$input['usp_richtext_editor'] = ($input['usp_richtext_editor'] == 1 ? 1 : 0);
	
	if (!isset($input['usp_featured_images'])) $input['usp_featured_images'] = null;
	$input['usp_featured_images'] = ($input['usp_featured_images'] == 1 ? 1 : 0);
	
	if (!isset($input['disable_required'])) $input['disable_required'] = null;
	$input['disable_required'] = ($input['disable_required'] == 1 ? 1 : 0);
	
	if (!isset($input['titles_unique'])) $input['titles_unique'] = null;
	$input['titles_unique'] = ($input['titles_unique'] == 1 ? 1 : 0);
	
	if (!isset($input['enable_shortcodes'])) $input['enable_shortcodes'] = null;
	$input['enable_shortcodes'] = ($input['enable_shortcodes'] == 1 ? 1 : 0);
	
	if (!isset($input['disable_ip_tracking'])) $input['disable_ip_tracking'] = null;
	$input['disable_ip_tracking'] = ($input['disable_ip_tracking'] == 1 ? 1 : 0);
	
	if (!isset($input['logged_in_users'])) $input['logged_in_users'] = null;
	$input['logged_in_users'] = ($input['logged_in_users'] == 1 ? 1 : 0);
	
	if (!isset($input['disable_author'])) $input['disable_author'] = null;
	$input['disable_author'] = ($input['disable_author'] == 1 ? 1 : 0);
	
	if (!isset($input['custom_checkbox'])) $input['custom_checkbox'] = null;
	$input['custom_checkbox'] = ($input['custom_checkbox'] == 1 ? 1 : 0);
	
	if (!isset($input['multiple-cats'])) $input['multiple-cats'] = null;
	$input['multiple-cats'] = ($input['multiple-cats'] == 1 ? 1 : 0);
	
	return apply_filters('usp_input_validate', $input);
	
}



function usp_render_form() {
	
	global $usp_options, $wpdb; 
	
	$version_previous = isset($usp_options['usp_version']) ? esc_attr($usp_options['usp_version']) : USP_VERSION;
	
	$display_alert = (isset($usp_options['version_alert']) && $usp_options['version_alert']) ? ' style="display:none;"' : ' style="display:block;"'; 
	
	$custom_styles = (isset($usp_options['usp_form_version']) && $usp_options['usp_form_version'] !== 'custom') ? 'display: none;' : 'display: block;';
	
	?>
	
	<style type="text/css">#mm-plugin-options .usp-custom-form-info { <?php echo $custom_styles; ?> }</style>
	
	<div id="mm-plugin-options" class="wrap">
		
		<h1><?php echo USP_PLUGIN; ?> <small><?php echo 'v'. USP_VERSION; ?></small></h1>
		<div id="mm-panel-toggle"><a href="<?php get_admin_url('options-general.php?page='. USP_PATH); ?>"><?php esc_html_e('Toggle all panels', 'usp'); ?></a></div>
		
		<form method="post" action="options.php">
			<?php settings_fields('usp_plugin_options'); ?>
			
			<div class="metabox-holder">
				<div class="meta-box-sortables ui-sortable">
					
					<div id="mm-panel-alert"<?php echo $display_alert; ?> class="postbox">
						<h2><?php esc_html_e('We need your support!', 'usp'); ?></h2>
						<div class="toggle">
							<div class="mm-panel-alert">
								<p>
									<?php esc_html_e('Please', 'usp'); ?> <a target="_blank" rel="noopener noreferrer" href="https://monzillamedia.com/donate.html" title="<?php esc_attr_e('Make a donation via PayPal', 'usp'); ?>"><?php esc_html_e('make a donation', 'usp'); ?></a> <?php esc_html_e('and/or', 'usp'); ?> 
									<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/user-submitted-posts/reviews/?rate=5#new-post" title="<?php esc_attr_e('Rate and review at the Plugin Directory', 'usp'); ?>">
										<?php esc_html_e('give this plugin a 5-star rating', 'usp'); ?>&nbsp;&raquo;
									</a>
								</p>
								<p>
									<?php esc_html_e('Your generous support enables continued development of this free plugin. Thank you!', 'usp'); ?>
								</p>
								<div class="dismiss-alert">
									<div class="dismiss-alert-wrap">
										<input class="input-alert" name="usp_options[version_alert]" type="checkbox" value="1" <?php if (isset($usp_options['version_alert'])) checked('1', $usp_options['version_alert']); ?> />
										<label class="description" for="usp_options[version_alert]"><?php esc_html_e('Check this box if you have shown support', 'usp') ?></label>
										<input type="hidden" name="usp_options[usp_version]" value="<?php echo $version_previous; ?>" />
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div id="mm-panel-overview" class="postbox">
						<h2><?php esc_html_e('Overview', 'usp'); ?></h2>
						<div class="toggle<?php if (isset($_GET['settings-updated'])) echo ' default-hidden'; ?>">
							<div class="mm-panel-overview clear">
								<p class="mm-overview-intro">
									<strong><abbr title="<?php echo USP_PLUGIN; ?>"><?php esc_html_e('USP', 'usp'); ?></abbr></strong> <?php esc_html_e('enables your visitors to submit posts and upload images from the front-end of your site. ', 'usp'); ?> 
									<?php esc_html_e('For advanced functionality and unlimited forms, check out', 'usp'); ?> <strong><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro/"><?php esc_html_e('USP Pro', 'usp'); ?></a></strong> 
									<?php esc_html_e('&mdash; the ultimate solution for user-generated content.', 'usp'); ?>
								</p>
								<div class="mm-left-div">
									<ul>
										<li><a id="mm-panel-primary-link" href="#mm-panel-primary"><?php esc_html_e('Plugin Settings', 'usp'); ?></a></li>
										<li><a id="mm-panel-secondary-link" href="#mm-panel-secondary"><?php esc_html_e('Display the form', 'usp'); ?></a></li>
										<li><a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/user-submitted-posts/"><?php esc_html_e('Plugin Homepage', 'usp'); ?>&nbsp;&raquo;</a></li>
									</ul>
									<p>
										<?php esc_html_e('If you like this plugin, please', 'usp'); ?> 
										<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/user-submitted-posts/reviews/?rate=5#new-post" title="<?php esc_attr_e('THANK YOU for your support!', 'usp'); ?>"><?php esc_html_e('give it a 5-star rating', 'usp'); ?>&nbsp;&raquo;</a>
									</p>
								</div>
								<div class="mm-right-div">
									<a target="_blank" rel="noopener noreferrer" class="mm-pro-blurb" href="https://plugin-planet.com/usp-pro/" title="<?php esc_attr_e('Unlimited front-end forms', 'usp'); ?>"><?php esc_html_e('Get USP Pro', 'usp'); ?></a>
								</div>
							</div>
						</div>
					</div>
					
					<div id="mm-panel-primary" class="postbox">
						<h2><?php esc_html_e('Plugin Settings', 'usp'); ?></h2>
						<div class="toggle<?php if (!isset($_GET['settings-updated'])) echo ' default-hidden'; ?>">
							
							<p><?php esc_html_e('Configure your settings for User Submitted Posts.', 'usp'); ?></p>
							
							<h3><?php esc_html_e('Form Fields', 'usp'); ?></h3>
							
							<div class="mm-table-wrap mm-table-less-padding">
								<table class="widefat mm-table">
									<?php 
										
										echo usp_form_field_options(array('usp_name',     esc_html__('User Name',     'usp')));
										echo usp_form_field_options(array('usp_email',    esc_html__('User Email',    'usp')));
										echo usp_form_field_options(array('usp_url',      esc_html__('User URL',      'usp')));
										echo usp_form_field_options(array('usp_title',    esc_html__('Post Title',    'usp')));
										echo usp_form_field_options(array('usp_tags',     esc_html__('Post Tags',     'usp')));
										echo usp_form_field_options(array('usp_category', esc_html__('Post Category', 'usp')));
										echo usp_form_field_options(array('usp_content',  esc_html__('Post Content',  'usp')));
										
										echo usp_form_field_options_custom();
										echo usp_form_field_options_captcha();
										echo usp_form_field_options_recaptcha();
										echo usp_form_field_options_images();
										
									?>
								</table>
							</div>
							
							<h3><?php esc_html_e('General Settings', 'usp'); ?></h3>
							
							<p><?php esc_html_e('Note that the default settings work fine for most cases.', 'usp'); ?></p>
							
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_form_version]"><?php esc_html_e('Form Style', 'usp'); ?></label></th>
										<td>
											<?php echo usp_form_display_options(); ?>
											
											<div class="usp-custom-form-info">
												<p><?php esc_html_e('With this option, you can copy the plugin&rsquo;s default templates:', 'usp'); ?></p>
												<ul>
													<li><code>/user-submitted-posts/resources/usp.css</code></li>
													<li><code>/user-submitted-posts/views/submission-form.php</code></li>
												</ul>
												<p><?php esc_html_e('..and upload them into a directory named', 'usp'); ?> <code>/usp/</code> <?php esc_html_e('in your theme:', 'usp'); ?></p>
												<ul>
													<li><code>/wp-content/themes/your-theme/usp/usp.css</code></li>
													<li><code>/wp-content/themes/your-theme/usp/submission-form.php</code></li>
												</ul>
												<p>
													<?php esc_html_e('That will enable you to customize the form and styles as desired. For more info, check out the "Custom Submission Form" section in the', 'usp'); ?> 
													<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/user-submitted-posts/#installation"><?php esc_html_e('Installation Docs', 'usp'); ?></a>. 
													<?php esc_html_e('FYI: here is a', 'usp'); ?> <a target="_blank" rel="noopener noreferrer" href="https://m0n.co/e"><?php esc_html_e('list of USP CSS selectors', 'usp'); ?>&nbsp;&raquo;</a> 
												</p>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_include_js]"><?php esc_html_e('Include JavaScript', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_include_js]" <?php if (isset($usp_options['usp_include_js'])) checked('1', $usp_options['usp_include_js']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Check this box if you want to include the external JavaScript files (recommended)', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_display_url]"><?php esc_html_e('Targeted Loading', 'usp'); ?></label></th>
										<td><input type="text" size="45" maxlength="200" name="usp_options[usp_display_url]" value="<?php if (isset($usp_options['usp_display_url'])) echo esc_attr($usp_options['usp_display_url']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('By default, CSS &amp; JavaScript assets are loaded on every page. Here you may specify the URL(s) of the USP form to load assets only on that page. Note: leave blank to load on all pages. Use commas to separate multiple URLs.', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_post_type]"><?php esc_html_e('Post Type', 'usp'); ?></label></th>
										<td>
											<?php echo usp_post_type_options(); ?>
											<span class="mm-item-caption"><?php esc_html_e('Submit posts as WP Posts or Pages', 'usp'); ?></span>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[number-approved]"><?php esc_html_e('Post Status', 'usp'); ?></label></th>
										<td>
											<?php echo usp_post_status_options(); ?>
											<span class="mm-item-caption"><?php esc_html_e('Post Status for submitted posts', 'usp'); ?></span>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[redirect-url]"><?php esc_html_e('Redirect URL', 'usp'); ?></label></th>
										<td><input type="text" size="45" maxlength="200" name="usp_options[redirect-url]" value="<?php if (isset($usp_options['redirect-url'])) echo esc_attr($usp_options['redirect-url']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('Specify a URL to redirect the user after post submission (leave blank to redirect back to current page)', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[success-message]"><?php esc_html_e('Success Message', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[success-message]"><?php if (isset($usp_options['success-message'])) echo esc_textarea($usp_options['success-message']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('Success message that is displayed if post submission is successful (basic markup is allowed)', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[error-message]"><?php esc_html_e('Error Message', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[error-message]"><?php if (isset($usp_options['error-message'])) echo esc_textarea($usp_options['error-message']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('General error message that is displayed if post submission fails (basic markup is allowed)', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_form_content]"><?php esc_html_e('Custom Content', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[usp_form_content]"><?php if (isset($usp_options['usp_form_content'])) echo esc_textarea($usp_options['usp_form_content']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('Custom text/markup to be included before the submission form (leave blank to disable)', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_richtext_editor]"><?php esc_html_e('Rich Text Editor', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_richtext_editor]" <?php if (isset($usp_options['usp_richtext_editor'])) checked('1', $usp_options['usp_richtext_editor']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Check this box if you want to enable RTE/Visual Editor for the Post Content field', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[titles_unique]"><?php esc_html_e('Unique Titles', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[titles_unique]" <?php if (isset($usp_options['titles_unique'])) checked('1', $usp_options['titles_unique']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Require submitted post titles to be unique (useful for preventing multiple/duplicate submitted posts)', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[disable_required]"><?php esc_html_e('Disable Required', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[disable_required]" <?php if (isset($usp_options['disable_required'])) checked('1', $usp_options['disable_required']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Disable all required attributes on default form fields (useful for troubleshooting error messages)', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[enable_shortcodes]"><?php esc_html_e('Enable Shortcodes', 'usp'); ?></label></th>
										<td><input name="usp_options[enable_shortcodes]" type="checkbox" value="1" <?php if (isset($usp_options['enable_shortcodes'])) checked('1', $usp_options['enable_shortcodes']); ?> /> 
										<span class="mm-item-caption"><?php esc_html_e('Enable shortcodes in widgets. By default, WordPress does not enable shortcodes in widgets. ', 'usp'); ?>
										<?php esc_html_e('This setting enables any/all shortcodes in widgets (even shortcodes from other plugins).', 'usp'); ?></span></td>
									</tr>
								</table>
							</div>
							
							<h3><?php esc_html_e('Privacy', 'usp'); ?></h3>
							
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="usp_options[disable_ip_tracking]"><?php esc_html_e('Disable IP Tracking', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[disable_ip_tracking]" <?php if (isset($usp_options['disable_ip_tracking'])) checked('1', $usp_options['disable_ip_tracking']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('By default USP records the IP address with each submitted post. Check this box to disable all IP tracking.', 'usp'); ?></span></td>
									</tr>
									
									<tr>
										<th scope="row"><label class="description" for="usp_options[custom_checkbox]"><?php esc_html_e('Display Checkbox', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[custom_checkbox]" <?php if (isset($usp_options['custom_checkbox'])) checked('1', $usp_options['custom_checkbox']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Display custom checkbox on your form (useful for GDPR, agree to terms, etc.)', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[custom_checkbox_name]"><?php esc_html_e('Checkbox Name', 'usp'); ?></label></th>
										<td><input type="text" size="45" maxlength="200" name="usp_options[custom_checkbox_name]" value="<?php if (isset($usp_options['custom_checkbox_name'])) echo esc_attr($usp_options['custom_checkbox_name']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('Use only alphanumeric, underscores, and dashes. If unsure, use the default name:', 'usp'); ?> <code>usp_custom_checkbox</code></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[custom_checkbox_err]"><?php esc_html_e('Checkbox Error', 'usp'); ?></label></th>
										<td><input type="text" size="45" maxlength="200" name="usp_options[custom_checkbox_err]" value="<?php if (isset($usp_options['custom_checkbox_err'])) echo esc_attr($usp_options['custom_checkbox_err']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('Error message displayed if user does not check the box', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[custom_checkbox_text]"><?php esc_html_e('Checkbox Text', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[custom_checkbox_text]"><?php if (isset($usp_options['custom_checkbox_text'])) echo esc_textarea($usp_options['custom_checkbox_text']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('Text displayed next to checkbox. Tip: use curly brackets to output angle brackets, for example:', 'usp'); ?> <code>{img}</code> = <code>&lt;img&gt;</code></div></td>
									</tr>
								</table>
							</div>
							
							<h3><?php esc_html_e('Categories &amp; Tags', 'usp'); ?></h3>
							
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description"><?php esc_html_e('Categories', 'usp'); ?></label></th>
										<td>
											<div class="mm-item-desc">
												<strong><a href="#" class="usp-cat-toggle-link"><?php esc_html_e('Click to view/select categories', 'usp'); ?></a></strong>
											</div>
											<div class="usp-cat-toggle-div default-hidden">
												<?php echo usp_post_category_options(); ?>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[multiple-cats]"><?php esc_html_e('Multiple Categories', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[multiple-cats]" <?php if (isset($usp_options['multiple-cats'])) checked('1', $usp_options['multiple-cats']); ?> /> 
										<span class="mm-item-caption"><?php esc_html_e('Enable users to select multiple categories.', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_use_cat]"><?php esc_html_e('Hidden/Default Category', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_use_cat]" <?php if (isset($usp_options['usp_use_cat'])) checked('1', $usp_options['usp_use_cat']); ?> /> 
										<span class="mm-item-caption"><?php esc_html_e('Use a hidden field for the post category. This hides the category field and sets its value via the next option.', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_use_cat_id]"><?php esc_html_e('Category ID for Hidden Field', 'usp'); ?></label></th>
										<td><input class="input-short" type="text" size="45" maxlength="100" name="usp_options[usp_use_cat_id]" value="<?php if (isset($usp_options['usp_use_cat_id'])) echo esc_attr($usp_options['usp_use_cat_id']); ?>" /> 
										<span class="mm-item-caption"><?php esc_html_e('Specify category ID(s) to use for &ldquo;Hidden/Default Category&rdquo; (separate multiple IDs with commas)', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_existing_tags]"><?php esc_html_e('Use Existing Tags', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_existing_tags]" <?php if (isset($usp_options['usp_existing_tags'])) checked('1', $usp_options['usp_existing_tags']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Check this box to display a select/dropdown menu of existing tags (valid when Tag field is displayed)', 'usp'); ?></span></td>
									</tr>
								</table>
							</div>
							
							<h3><?php esc_html_e('Users', 'usp'); ?></h3>
							
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="usp_options[author]"><?php esc_html_e('Assigned Author', 'usp'); ?></label></th>
										<td>
											<?php echo usp_post_author_options(); ?>
											<span class="mm-item-caption"><?php esc_html_e('Specify the user that should be assigned as author for submitted posts', 'usp'); ?></span>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_use_author]"><?php esc_html_e('Registered Username', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_use_author]" <?php if (isset($usp_options['usp_use_author'])) checked('1', $usp_options['usp_use_author']); ?> /> 
										<span class="mm-item-caption"><?php esc_html_e('Use the user&rsquo;s registered username for the Name field (valid when the user submitting the form is logged in to WordPress)', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_use_email]"><?php esc_html_e('Registered Email', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_use_email]" <?php if (isset($usp_options['usp_use_email'])) checked('1', $usp_options['usp_use_email']); ?> /> 
										<span class="mm-item-caption"><?php esc_html_e('Use the user&rsquo;s registered email as the value of the Email field (valid when the user submitting the form is logged in to WordPress)', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_use_url]"><?php esc_html_e('User Profile URL', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_use_url]" <?php if (isset($usp_options['usp_use_url'])) checked('1', $usp_options['usp_use_url']); ?> /> 
										<span class="mm-item-caption"><?php esc_html_e('Use the user&rsquo;s Profile URL as the value of the URL field (valid when the user submitting the form is logged in to WordPress)', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[logged_in_users]"><?php esc_html_e('Require User Login', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[logged_in_users]" <?php if (isset($usp_options['logged_in_users'])) checked('1', $usp_options['logged_in_users']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Require users to be logged in to WordPress to view/submit the form', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[disable_author]"><?php esc_html_e('Disable Replace Author', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[disable_author]" <?php if (isset($usp_options['disable_author'])) checked('1', $usp_options['disable_author']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Do not replace post author with submitted user name', 'usp'); ?></span></td>
									</tr>
								</table>
							</div>
							
							<h3 id="usp-custom-field"><?php esc_html_e('Custom Field', 'usp'); ?></h3>
							<p><?php esc_html_e('Here you may change the name and label used by the Custom Field. You can enable this field via the Form Fields setting, above.', 'usp'); ?></p>
								
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="usp_options[custom_name]"><?php esc_html_e('Custom Field Name', 'usp'); ?></label></th>
										<td><input type="text" size="45" name="usp_options[custom_name]" value="<?php if (isset($usp_options['custom_name'])) echo esc_attr($usp_options['custom_name']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('Use only alphanumeric, underscores, and dashes. If unsure, use the default name:', 'usp'); ?> <code>usp_custom_field</code></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[custom_label]"><?php esc_html_e('Custom Field Label', 'usp'); ?></label></th>
										<td><input type="text" size="45" name="usp_options[custom_label]" value="<?php if (isset($usp_options['custom_label'])) echo esc_attr($usp_options['custom_label']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('This will be displayed as the field label on the form. Default: Custom Field', 'usp'); ?></div></td>
									</tr>
								</table>
							</div>
							
							<h3 id="usp-challenge-question"><?php esc_html_e('Challenge Question', 'usp'); ?></h3>
							
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_question]"><?php esc_html_e('Challenge Question', 'usp'); ?></label></th>
										<td><input type="text" size="45" name="usp_options[usp_question]" value="<?php if (isset($usp_options['usp_question'])) echo esc_attr($usp_options['usp_question']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('To prevent spam, enter a question that users must answer before submitting the form', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_response]"><?php esc_html_e('Challenge Response', 'usp'); ?></label></th>
										<td><input type="text" size="45" name="usp_options[usp_response]" value="<?php if (isset($usp_options['usp_response'])) echo esc_attr($usp_options['usp_response']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('Enter the *only* correct answer to the challenge question', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_casing]"><?php esc_html_e('Case-sensitivity', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_casing]" <?php if (isset($usp_options['usp_casing'])) checked('1', $usp_options['usp_casing']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Check this box if you want the challenge response to be case-sensitive', 'usp'); ?></span></td>
									</tr>
								</table>
							</div>
							
							<h3 id="usp-recaptcha"><?php esc_html_e('Google reCAPTCHA', 'usp'); ?></h3>
							<p><?php esc_html_e('To enable Google reCAPTCHA, enter your public and private keys.', 'usp'); ?></p>
								
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="usp_options[recaptcha_public]"><?php esc_html_e('Public Key', 'usp'); ?></label></th>
										<td><input type="text" size="45" name="usp_options[recaptcha_public]" value="<?php if (isset($usp_options['recaptcha_public'])) echo esc_attr($usp_options['recaptcha_public']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('Enter your Public Key', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[recaptcha_private]"><?php esc_html_e('Private Key', 'usp'); ?></label></th>
										<td><input type="text" size="45" name="usp_options[recaptcha_private]" value="<?php if (isset($usp_options['recaptcha_private'])) echo esc_attr($usp_options['recaptcha_private']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('Enter your Private Key', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[recaptcha_version]"><?php esc_html_e('reCaptcha Version', 'usp'); ?></label></th>
										<td>
											<?php echo usp_form_field_recaptcha(); ?>
											<span class="mm-item-caption"><?php esc_html_e('Choose reCaptcha version', 'usp'); ?></span>
										</td>
									</tr>
								</table>
							</div>
							
							<h3><?php esc_html_e('Email Alerts', 'usp'); ?></h3>
							
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_email_alerts]"><?php esc_html_e('Receive Email Alert', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_email_alerts]" <?php if (isset($usp_options['usp_email_alerts'])) checked('1', $usp_options['usp_email_alerts']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Check this box if you want to be notified via email for new post submissions', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_email_html]"><?php esc_html_e('Enable HTML Format', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_email_html]" <?php if (isset($usp_options['usp_email_html'])) checked('1', $usp_options['usp_email_html']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Check this box to enable HTML format for email alerts', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_email_address]"><?php esc_html_e('Email Address for Alerts', 'usp'); ?></label></th>
										<td><input type="text" size="45" maxlength="200" name="usp_options[usp_email_address]" value="<?php if (isset($usp_options['usp_email_address'])) echo esc_attr($usp_options['usp_email_address']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('If you checked the box to receive email alerts, indicate here the address(es) to which the emails should be sent.', 'usp'); ?> 
										<?php esc_html_e('Separate multiple addresses with commas.', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_email_from]"><?php esc_html_e('Email &ldquo;From&rdquo; Address', 'usp'); ?></label></th>
										<td><input type="text" size="45" maxlength="200" name="usp_options[usp_email_from]" value="<?php if (isset($usp_options['usp_email_from'])) echo esc_attr($usp_options['usp_email_from']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('Here you may customize the address(es) used for the &ldquo;From&rdquo; header (see plugin FAQs for info). ', 'usp'); ?> 
										<?php esc_html_e('If multiple addresses are specified in the previous setting, include an equal number of &ldquo;From&rdquo; addresses for this setting (in the same order).', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[email_alert_subject]"><?php esc_html_e('Email Alert Subject', 'usp'); ?></label></th>
										<td><input type="text" size="45" name="usp_options[email_alert_subject]" value="<?php if (isset($usp_options['email_alert_subject'])) echo esc_attr($usp_options['email_alert_subject']); ?>" />
										<div class="mm-item-caption"><?php esc_html_e('Subject line for email alerts. Leave blank to use the default subject line. Note: you can use the following variables: ', 'usp'); ?>
										<code>%%post_title%%</code>, <code>%%post_content%%</code>, <code>%%post_author%%</code>, <code>%%blog_name%%</code>, <code>%%blog_url%%</code>, <code>%%post_url%%</code>, <code>%%admin_url%%</code>, 
										<code>%%edit_link%%</code>, <code>%%user_email%%</code>, <code>%%user_url%%</code>, <code>%%custom_field%%</code></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[email_alert_message]"><?php esc_html_e('Email Alert Message', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[email_alert_message]"><?php if (isset($usp_options['email_alert_message'])) echo esc_textarea($usp_options['email_alert_message']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('Message for email alerts. Leave blank to use the default message. Note: you can use the following variables: ', 'usp'); ?>
										<code>%%post_title%%</code>, <code>%%post_content%%</code>, <code>%%post_author%%</code>, <code>%%blog_name%%</code>, <code>%%blog_url%%</code>, <code>%%post_url%%</code>, <code>%%admin_url%%</code>, 
										<code>%%edit_link%%</code>, <code>%%user_email%%</code>, <code>%%user_url%%</code>, <code>%%custom_field%%</code></div></td>
									</tr>
								</table>
							</div>
							
							<h3 id="usp-image-uploads"><?php esc_html_e('Image Uploads', 'usp'); ?></h3>
							
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_featured_images]"><?php esc_html_e('Featured Image', 'usp'); ?></label></th>
										<td><input type="checkbox" value="1" name="usp_options[usp_featured_images]" <?php if (isset($usp_options['usp_featured_images'])) checked('1', $usp_options['usp_featured_images']); ?> />
										<span class="mm-item-caption"><?php esc_html_e('Set submitted images as Featured Images. Requires theme support for Featured Images (aka Post Thumbnails)', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[upload-message]"><?php esc_html_e('Upload Message', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[upload-message]"><?php if (isset($usp_options['upload-message'])) echo esc_textarea($usp_options['upload-message']); ?></textarea>
										<div class="mm-item-caption"><?php esc_html_e('Message that appears next to the upload field. Useful for stating your upload guidelines/policy/etc. Basic markup allowed.', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[usp_add_another]"><?php esc_html_e('&ldquo;Add another image&rdquo; link', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[usp_add_another]"><?php if (isset($usp_options['usp_add_another'])) echo esc_textarea($usp_options['usp_add_another']); ?></textarea>
										<div class="mm-item-caption"><?php esc_html_e('Custom HTML/markup for the &ldquo;Add another image&rdquo; link. Leave blank to use the default markup (recommended).', 'usp'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[min-images]"><?php esc_html_e('Minimum number of images', 'usp'); ?></label></th>
										<td><input name="usp_options[min-images]" type="number" class="small-text" step="1" min="0" max="999" maxlength="3" value="<?php if (isset($usp_options['min-images'])) echo esc_attr($usp_options['min-images']); ?>" />
										<span class="mm-item-caption"><?php esc_html_e('Specify the minimum number of images', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[max-images]"><?php esc_html_e('Maximum number of images', 'usp'); ?></label></th>
										<td><input name="usp_options[max-images]" type="number" class="small-text" step="1" min="0" max="999" maxlength="3" value="<?php if (isset($usp_options['max-images'])) echo esc_attr($usp_options['max-images']); ?>" />
										<span class="mm-item-caption"><?php esc_html_e('Specify the maximum number of images', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[min-image-width]"><?php esc_html_e('Minimum image width', 'usp'); ?></label></th>
										<td><input name="usp_options[min-image-width]" type="number" class="small-text" step="1" min="0" max="999999999" maxlength="9" value="<?php if (isset($usp_options['min-image-width'])) echo esc_attr($usp_options['min-image-width']); ?>" />
										<span class="mm-item-caption"><?php esc_html_e('Specify a minimum width (in pixels) for uploaded images', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[min-image-height]"><?php esc_html_e('Minimum image height', 'usp'); ?></label></th>
										<td><input name="usp_options[min-image-height]" type="number" class="small-text" step="1" min="0" max="999999999" maxlength="9" value="<?php if (isset($usp_options['min-image-height'])) echo esc_attr($usp_options['min-image-height']); ?>" />
										<span class="mm-item-caption"><?php esc_html_e('Specify a minimum height (in pixels) for uploaded images', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[max-image-width]"><?php esc_html_e('Maximum image width', 'usp'); ?></label></th>
										<td><input name="usp_options[max-image-width]" type="number" class="small-text" step="1" min="0" max="999999999" maxlength="9" value="<?php if (isset($usp_options['max-image-width'])) echo esc_attr($usp_options['max-image-width']); ?>" />
										<span class="mm-item-caption"><?php esc_html_e('Specify a maximum width (in pixels) for uploaded images', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[max-image-height]"><?php esc_html_e('Maximum image height', 'usp'); ?></label></th>
										<td><input name="usp_options[max-image-height]" type="number" class="small-text" step="1" min="0" max="999999999" maxlength="9" value="<?php if (isset($usp_options['max-image-height'])) echo esc_attr($usp_options['max-image-height']); ?>" />
										<span class="mm-item-caption"><?php esc_html_e('Specify a maximum height (in pixels) for uploaded images', 'usp'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description"><?php esc_html_e('More Options', 'usp'); ?></label></th>
										<td>
											<span class="mm-item-caption">
												<?php esc_html_e('For more options, like the ability to upload other file types (like PDF, Word, Zip, videos, and more), check out', 'usp'); ?> 
												<strong><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/usp-pro/" title="<?php esc_attr__('Go Pro!', 'usp'); ?>"><?php esc_html_e('USP Pro', 'usp'); ?>&nbsp;&raquo;</a></strong>
											</span>
										</td>
									</tr>
								</table>
							</div>
							
							<h3><?php esc_html_e('Auto-Display Content', 'usp'); ?></h3>
							
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_display_images]"><?php esc_html_e('Auto-Display Images', 'usp'); ?></label></th>
										<td>
											<span class="mm-item-desc"><?php esc_html_e('Automatically display user-submitted images:', 'usp'); ?></span>
											<?php echo usp_auto_display_options('images'); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_image_markup]"><?php esc_html_e('Image Markup', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[auto_image_markup]"><?php if (isset($usp_options['auto_image_markup'])) echo esc_textarea($usp_options['auto_image_markup']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('Markup to use for each submitted image (when auto-display is enabled). Can use', 'usp'); ?> 
										<code>%%width%%</code>, <code>%%height%%</code>, <code>%%thumb%%</code>, <code>%%medium%%</code>, <code>%%large%%</code>, <code>%%full%%</code>, <code>%%custom%%</code>, 
										<code>%%title%%</code>, <code>%%title_parent%%</code>, <code>%%author%%</code>, <?php esc_html_e('and', 'usp'); ?> <code>%%url%%</code>.</div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_display_email]"><?php esc_html_e('Auto-Display Email', 'usp'); ?></label></th>
										<td>
											<span class="mm-item-desc"><?php esc_html_e('Automatically display user-submitted email:', 'usp'); ?></span>
											<?php echo usp_auto_display_options('email'); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_email_markup]"><?php esc_html_e('Email Markup', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[auto_email_markup]"><?php if (isset($usp_options['auto_email_markup'])) echo esc_textarea($usp_options['auto_email_markup']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('Markup to use for the submitted email address (when auto-display is enabled). Can use', 'usp'); ?> 
										<code>%%email%%</code>, <code>%%author%%</code>, <?php esc_html_e('and', 'usp'); ?> <code>%%title%%</code>.</div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_display_name]"><?php esc_html_e('Auto-Display Name', 'usp'); ?></label></th>
										<td>
											<span class="mm-item-desc"><?php esc_html_e('Automatically display user-submitted author/name:', 'usp'); ?></span>
											<?php echo usp_auto_display_options('name'); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_name_markup]"><?php esc_html_e('Name Markup', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[auto_name_markup]"><?php if (isset($usp_options['auto_name_markup'])) echo esc_textarea($usp_options['auto_name_markup']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('Markup to use for the submitted author/name (when auto-display is enabled). Can use', 'usp'); ?> 
										<code>%%author%%</code> to display the name.</div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_display_url]"><?php esc_html_e('Auto-Display URL', 'usp'); ?></label></th>
										<td>
											<span class="mm-item-desc"><?php esc_html_e('Automatically display user-submitted URL:', 'usp'); ?></span>
											<?php echo usp_auto_display_options('url'); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_url_markup]"><?php esc_html_e('URL Markup', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[auto_url_markup]"><?php if (isset($usp_options['auto_url_markup'])) echo esc_textarea($usp_options['auto_url_markup']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('Markup to use for the submitted URL (when auto-display is enabled). Can use', 'usp'); ?> 
										<code>%%url%%</code>, <code>%%author%%</code>, <?php esc_html_e('and', 'usp'); ?> <code>%%title%%</code>.</div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_display_custom]"><?php esc_html_e('Auto-Display Custom Field', 'usp'); ?></label></th>
										<td>
											<span class="mm-item-desc"><?php esc_html_e('Automatically display user-submitted Custom Field:', 'usp'); ?></span>
											<?php echo usp_auto_display_options('custom'); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="usp_options[auto_custom_markup]"><?php esc_html_e('Custom Field Markup', 'usp'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="usp_options[auto_custom_markup]"><?php if (isset($usp_options['auto_custom_markup'])) echo esc_textarea($usp_options['auto_custom_markup']); ?></textarea> 
										<div class="mm-item-caption"><?php esc_html_e('Markup to use for the submitted Custom Field (when auto-display is enabled). Can use', 'usp'); ?> 
										<code>%%custom_label%%</code>, <code>%%custom_name%%</code>, <code>%%custom_value%%</code>, <code>%%author%%</code>, <?php esc_html_e('and', 'usp'); ?> <code>%%title%%</code>.</div></td>
									</tr>
								</table>
							</div>
							
							<input type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'usp'); ?>" />
						</div>
					</div>
					
					<div id="mm-panel-secondary" class="postbox">
						<h2><?php esc_html_e('Display the Form', 'usp'); ?></h2>
						<div class="toggle default-hidden">
							
							<h3><?php esc_html_e('Post-Submit Form', 'usp'); ?></h3>
							<p><?php esc_html_e('USP enables you to display a post-submission form anywhere on your site.', 'usp'); ?></p>
							<p><?php esc_html_e('Use the shortcode to display the form on any WP Post or Page:', 'usp'); ?></p>
							<p><span class="code mm-code">[user-submitted-posts]</span></p>
							
							<p><?php esc_html_e('Or, use the template tag to display the form anywhere in your theme template:', 'usp'); ?></p>
							<p><span class="code mm-code">&lt;?php if (function_exists('user_submitted_posts')) user_submitted_posts(); ?&gt;</span></p>
							<br>
							
							<h3><?php esc_html_e('Login/Register Form', 'usp'); ?></h3>
							<p><?php esc_html_e('You also can display a form that enables users to log in, register, or reset their password.', 'usp'); ?></p>
							<p><?php esc_html_e('Use the shortcode to display the form on any WP Post or Page:', 'usp'); ?></p>
							<p><span class="code mm-code">[usp-login-form]</span></p>
							
							<p><?php esc_html_e('Or, use the template tag to display the form anywhere in your theme template:', 'usp'); ?></p>
							<p><span class="code mm-code">&lt;?php if (function_exists('usp_login_form')) usp_login_form(); ?&gt;</span></p>
							<br>
							
							<h3><?php esc_html_e('Display Submitted Posts', 'usp'); ?></h3>
							<p><?php esc_html_e('Use this shortcode to display a list of submitted posts on any WP Post or Page:', 'usp'); ?></p>
							<p><span class="code mm-code">[usp_display_posts]</span></p>
							
							<p><?php esc_html_e('Or, use the template tag to display a list of submitted posts anywhere in your theme template:', 'usp'); ?></p>
							<p><span class="code mm-code">&lt;?php if (function_exists('usp_display_posts')) echo usp_display_posts(array('userid' => 'all', 'numposts' => -1)); ?&gt;</span></p>
							
							<p><?php esc_html_e('Here are some examples showing how to configure this shortcode:', 'usp'); ?></p>
<pre>[usp_display_posts]                           : default displays all submitted posts by all authors
[usp_display_posts userid="1"]                : displays all submitted posts by registered user with ID = 1
[usp_display_posts userid="Pat Smith"]        : displays all submitted posts by author name "Pat Smith"
[usp_display_posts userid="all"]              : displays all submitted posts by all users/authors
[usp_display_posts userid="all" numposts="5"] : limit to 5 posts from all users</pre>
							<br>
							
							<h3><?php esc_html_e('Display Image Gallery', 'usp'); ?></h3>
							<p><?php esc_html_e('Use this shortcode to display a gallery of uploaded images for each submitted post:', 'usp'); ?></p>
							<p><span class="code mm-code">[usp_gallery]</span></p>
							
							<p><?php esc_html_e('Or, use the template tag to display an image gallery anywhere in your theme template:', 'usp'); ?></p>
							<p><span class="code mm-code">&lt;?php if (function_exists('usp_get_images')) $images = usp_get_images(); foreach ($images as $image) echo $image; ?&gt;</span></p>
							
							<p><?php esc_html_e('You can customize using any of the follwing attributes:', 'usp'); ?></p>
<pre>$size   = image size as thumbnail, medium, large or full -> default = thumbnail
$before = text/markup displayed before the image URL     -> default = {a href='%%url%%'}{img src='
$after  = text/markup displayed after the image URL      -> default = ' /}{/a}
$number = the number of images to display for each post  -> default = false (display all)
$postId = an optional post ID to use                     -> default = false (uses global/current post)

Notes: 
	Use curly brackets to output angle brackets
	Use single quotes in before/after attributes
	Can use %%url%% to get the URL of the full-size image
	Check out the source code inline notes for more info
</pre>
							<br>
							
							<h3><?php esc_html_e('Reset Form Button', 'usp'); ?></h3>
							<p><?php esc_html_e('This shortcode displays a link that resets the form to its original state:', 'usp'); ?></p>
							<p><span class="code mm-code">[usp-reset-button]</span></p>
							
							<p><?php esc_html_e('This shortcode accepts the following attributes:', 'usp'); ?></p>
<pre>class  = classes for the parent element (optional, default: none)
value  = link text (optional, default: "Reset form")
url    = the URL where your form is displayed (required, default: none)
custom = any attributes or custom code for the link element (optional, default: none)</pre>

							<p><?php esc_html_e('Note that the url attribute accepts', 'usp'); ?> <code>%%current%%</code> <?php esc_html_e('to get the current URL.', 'usp'); ?></p>
							<br>
							
							<h3><?php esc_html_e('Access Control', 'usp'); ?></h3>
							<p><?php esc_html_e('USP provides three shortcodes to control access and restrict content.', 'usp'); ?></p>
							<p><?php esc_html_e('Display content only to users with a specific capability:', 'usp'); ?></p>
							<p><span class="code mm-code">[usp_access cap="read" deny="Message for users without read capability"][/usp_access]</span></p>
							
							<p><?php esc_html_e('Display content to logged-in users:', 'usp'); ?></p>
							<p><span class="code mm-code">[usp_member deny="Message for users who are not logged in"][/usp_member]</span></p>
							
							<p><?php esc_html_e('Display content to visitors only:', 'usp'); ?></p>
							<p><span class="code mm-code">[usp_visitor deny="Message for users who are logged in"][/usp_visitor]</span></p>
							
							<p><strong><?php esc_html_e('Tip:', 'usp'); ?></strong> <?php esc_html_e('to include markup in the deny message, you can use', 'usp'); ?> <code>{tag}</code> <?php esc_html_e('to output', 'usp'); ?> <code>&lt;tag&gt;</code>.</p>
							<br>
							
							<h3><?php esc_html_e('Example', 'usp'); ?></h3>
							<p><?php esc_html_e('If the user is logged in, display the post-submit form; or if the user is not logged in, display the login form:', 'usp'); ?></p>
<pre>[usp_member]
[user-submitted-posts]
[/usp_member]					

[usp_visitor]
[usp-login-form]
[/usp_visitor]</pre>
						</div>
					</div>
					
					<div id="mm-restore-settings" class="postbox">
						<h2><?php esc_html_e('Restore Defaults', 'usp'); ?></h2>
						<div class="toggle default-hidden">
							<p>
								<?php esc_html_e('Leave this option disabled to remember your settings. Or, to go ahead and restore the default plugin options: check the box, save your settings, and then deactivate/reactivate the plugin.', 'usp'); ?>
							</p>
							<p>
								<input name="usp_options[default_options]" type="checkbox" value="1" id="mm_restore_defaults" <?php if (isset($usp_options['default_options'])) checked('1', $usp_options['default_options']); ?> /> 
								<label class="description" for="usp_options[default_options]"><?php esc_html_e('Restore default options upon plugin deactivation/reactivation', 'usp'); ?></label>
							</p>
							<input type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'usp'); ?>" />
						</div>
					</div>
					
					<div id="mm-panel-current" class="postbox">
						<h2><?php esc_html_e('Show Support', 'usp'); ?></h2>
						<div class="toggle<?php if (isset($_GET['settings-updated'])) echo ' default-hidden'; ?>">
							<?php require_once('support-panel.php'); ?>
						</div>
					</div>
				</div>
			</div>
			
			<div id="mm-credit-info">
				<a target="_blank" rel="noopener noreferrer" href="https://perishablepress.com/user-submitted-posts/" title="<?php esc_attr_e('Plugin Homepage', 'usp'); ?>"><?php echo USP_PLUGIN; ?></a> <?php esc_html_e('by', 'usp'); ?> 
				<a target="_blank" rel="noopener noreferrer" href="https://twitter.com/perishable" title="<?php esc_attr_e('Jeff Starr on Twitter', 'usp'); ?>">Jeff Starr</a> @ 
				<a target="_blank" rel="noopener noreferrer" href="https://monzillamedia.com/" title="<?php esc_attr_e('Obsessive Web Design &amp; Development', 'usp'); ?>">Monzilla Media</a>
			</div>
		</form>
	</div>
	
	<script type="text/javascript">
		jQuery(document).ready(function($){
			
			// dismiss alert
			if (!$('.dismiss-alert-wrap input').is(':checked')){
				$('.dismiss-alert-wrap input').one('click', function(){
					$('.dismiss-alert-wrap').after('<input type="submit" class="button-secondary" value="<?php esc_attr_e('Save Preference', 'usp'); ?>" />');
				});
			}
			
			// prevent accidents
			if (!$("#mm_restore_defaults").is(":checked")){
				$('#mm_restore_defaults').click(function(event){
					var r = confirm("<?php esc_html_e('Are you sure you want to restore all default options? (this action cannot be undone)', 'usp'); ?>");
					if (r == true) $("#mm_restore_defaults").attr('checked', true);
					else $("#mm_restore_defaults").attr('checked', false);
				});
			}
			
		});
	</script>

<?php }
