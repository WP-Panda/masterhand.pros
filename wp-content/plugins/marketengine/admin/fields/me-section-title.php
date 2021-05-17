<?php
/**
 * ME Section Title
 * @since 1.0.1
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ME Section Title
 *
 * @since 1.0.1
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Section_Title extends ME_Input {
    /**
     * Field contructor
     *
     * @param array $args Input attribute
     * @param mix $options Input option value
     *
     */
    public function __construct($args, $option) {
        $args = wp_parse_args($args, array('title' => 'Section title', 'wrapper' => 'h2'));
        $this->_title   = $args['title'];
        $this->_wrapper = $args['wrapper'];
    }

    public function render() {
        echo "<" . $this->_wrapper . ">";
        echo $this->_title;
        echo "</" . $this->_wrapper . ">";
    }
}

