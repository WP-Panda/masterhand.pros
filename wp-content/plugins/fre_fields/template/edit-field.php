<?php
// field data
global $ae_post_factory;
$field_object = $ae_post_factory->get('ae_field');
$current_field = $field_object->current_post;
$field_type = $current_field->field_type;
$required = $current_field->required;
if($required) {
    $required = array('required' => true);
}else {
    $required = array();
}
$required['placeholder'] = $current_field->placeholder;

/* post data */
$post = $ae_post_factory->get($current_field->field_for);
if(!$post) return;
$current_post = $post->current_post;
$field_name = strtolower($current_field->post_title);

$value = '';
if(isset($current_post->$field_name)) {
    $value = $current_post->$field_name;
}
?>
<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 form-field form-group custom-field <?php echo $field_type; ?>-field">
    <label for="<?php echo $current_field->post_title ?>" class="control-label title-plan field-label">
    	<?php echo $current_field->label; ?>
    </label>
    <?php 
    switch ($field_type) {
        case 'text':
            AE_Form::text($current_field->post_title, $value, $required);
            break;
        case 'url':
            AE_Form::url($current_field->post_title, $value, $required);
            break;
        case 'email':
            AE_Form::email($current_field->post_title, $value, $required);
            break;
        case 'textarea':
            AE_Form::textarea($current_field->post_title, $value, $required);
            break;
        case 'select':
            $attr = array(  "data-chosen-width"=>"95%",
                            "data-chosen-disable-search" => "",
                            "data-placeholder" => $current_field->placeholder
                        );
            if($current_field->required) {
                $attr['required'] = true;
            }
            AE_Form::ae_tax($current_field->post_title, $value, $attr);
            break;
        case 'multi_select':
            $attr = array(  "data-chosen-width"=>"95%",
                            "data-chosen-disable-search" => "",
                            "data-placeholder" => $current_field->placeholder
                        );
            if($current_field->required) {
                $attr['required'] = true;
            }
            AE_Form::ae_tax_multi($current_field->post_title, $value, $attr);
            break;
        case 'radio':
            AE_Form::radio($current_field->post_title, $value,$required);
            break;
        case 'checkbox':
            AE_Form::checkbox($current_field->post_title, $value,$required);
            break;
        case 'datepicker':
            $attr = array(
                        "id" => "datepicker",
                        "data-placeholder" => $current_field->placeholder
                    );
            if($current_field->required){
                $attr['required'] = true;
            }
            AE_Form::datepicker($current_field->post_title, $value, $attr);
            break;
        case 'carousel':
            $attr = array(
                        "id" => "carousel",
                        "data-placeholder" => $current_field->placeholder
                    );
            if($current_field->required){
                $attr['required'] = true;
            }
            AE_Form::carousel($current_field->post_title, $value, $attr);
            break;
        default:
            break;
    }
    ?>
    
</div>