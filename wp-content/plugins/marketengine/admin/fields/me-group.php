<?php
/**
 * Class ME_Group
 * 
 * Option group container
 * 
 * @category Class
 * @package Admin/Options
 * @since 1.0
 * 
 * @version 1.0
 */
class ME_Group extends ME_Container {
    protected $_title;
    protected $_description;
    protected $_fields;
    /**
     *
     */
    public function __construct($args, $option) {
        $this->_name     = $args['slug'];
        $this->_controls = array();
    }

    public function start() {
        echo '<div class="me-group-field">';
    }

    public function end() {
        echo '</div>';
    }
}