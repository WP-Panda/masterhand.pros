<?php
/**
 * ME Input
 *
 * Render HTML input abstract class
 * 
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 * 
 */
abstract class ME_Input
{
    /**
     * Input Field Name
     *
     * @var string
     */
    protected $_name;
    /**
     * Input Field Name
     *
     * @var string
     */
    protected $_type;
    /**
     * Input Field Type
     *
     * @var string
     */
    protected $_label;
    /**
     * Input Feild Description
     *
     * @var string
     */
    protected $_description;

    /**
     * Input Feild Note
     *
     * @var string
     */
    protected $_note;

    /**
     * Input Container
     *
     * @var string
     */
    protected $_container;

    /**
     * Input Value
     *
     * @var void
     */
    protected $_value;

    /**
     * Input Default Value
     *
     * @var void
     */
    protected $_default_value;
    /**
     * Input Options
     * 
     * @var void
     */
    protected $_options;
    /**
     * Render input tag html
     */
    abstract public function render();

    /**
     * Field contructor
     *
     * @param array $args Input attribute
     * @param mix $options Input option value
     * 
     */
    public function __construct($args, $options) {}
    /**
     * Render the field label
     * @return void
     */
    protected function label()
    {
        if (!empty($this->_label)) {
            echo '<label class="me-title">' . $this->_label . '</label>';
        }
    }

    /**
     * Render the field description
     * @return void
     */
    protected function description()
    {
        if (!empty($this->_description)) {
            echo '<span class="me-subtitle">' . $this->_description . '</span>';
        }
    }

    /**
     * Get the field id attribute
     * @return string id="{$id}"
     */
    protected function get_id()
    {
        return $this->_slug ? 'id="' . $this->_slug . '"' : '';
    }

    /**
     * Retrieve field option value
     * @return mix
     */
    protected function get_value()
    {
        if (!$this->_container || !$this->_options) {
            return '';
        }
        $parent      = $this->_options;
        $options     = $this->_options;
        $option_name = $this->_name;

        $option_value = marketengine_option($option_name);

        return !empty($option_value) ? $option_value : $this->_default_value;
    }
}
