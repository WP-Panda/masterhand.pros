<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME Image Option
 *
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Image extends ME_Input{
    function __construct( $args, $options ) {
        $args = wp_parse_args($args, array('name' => 'option_name', 'description' => '', 'label' => ''));

        $this->_type        = 'image';
        $this->_name        = $args['name'];
        $this->_label       = $args['label'];
        $this->_description = $args['description'];
        $this->_id          = isset($args['id']) ? $args['id'] : '';;
        $this->_gallery     = isset($args['gallery']) ? $args['gallery'] : '';
        $this->_container   = $options;

        $this->_options = $options;
    }

    function render() {
        echo '<div class="me-group-field">';
        $this->label();
        $this->description();
        echo '<div class="me-upload">
                <ul class="marketengine-gallery-img">';
        if( !empty($this->_gallery) ){
            foreach($this->_gallery as $key => $value){
                echo '<li class="me-item-img">
                            <span class="me-gallery-img">
                                <img src="' .$value['src']. '" alt="' .$value['alt']. '">
                                <a class="me-delete-img"></a>
                            </span>
                        </li>';
            }
        }
        echo '<li class="me-item-img">
                        <span class="me-gallery-img me-gallery-add-img">
                            <a class="me-add-img">
                                <i class="icon-me-add-image"></i>
                                <input type="file" value="">
                            </a>
                        </span>
                    </li>
                </ul>
            </div>';
        echo '</div>';
    }

}