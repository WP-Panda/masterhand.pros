<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class ME Tab
 *
 * Render admin option tabs
 *
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Tab extends ME_Container {
    /**
     * Contructor
     * @param array $args
     */
    public function __construct($args) {
        $args = wp_parse_args( $args, array('class' => '' ) );
        $this->_name     = $args['slug'];
        $this->_template = $args['template'];
        $this->_class    = $args['class'];
    }

    public function menus() {
        if (count($this->_template) == 1) {
            return;
        }

        echo '<ul class="me-nav me-section-nav '.$this->_class.'">';
        $count = 0;
        $sections = apply_filters('marketengine_section', $this->_template);
        foreach ($sections as $key => $tab) {
            $class = ((!isset($_REQUEST['section']) && $count == 0) || (isset($_REQUEST['section']) && $_REQUEST['section'] == $tab['slug'])) ? 'class="active"' : '';
            if ($tab['type'] == 'section') {
                echo '<li ' . $class . '><a href="'.add_query_arg('section', $tab['slug'], '?page=me-settings&tab='.$this->_name).'"><span>' . $tab['title'] . '</span></a></li>';
            }
            $class = '';
            $count = 1;
        }
        echo '</ul>';
    }

    public function start() {
        echo '<div class="me-tab" >';
    }

    public function end() {
        echo '</div>';
    }

    public function wrapper_start() {
        echo '<div class="me-section-container">';
    }

    public function wrapper_end() {
        echo '</div>';
    }
}