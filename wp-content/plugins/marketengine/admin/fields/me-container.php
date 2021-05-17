<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ME Container
 *
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 */
abstract class ME_Container
{
    /**
     * Container name
     * @var string
     */
    protected $_name;
    /**
     * The container option fields to render
     * @var array|string
     */
    protected $_template;
    /**
     * Render container header
     */
    abstract public function start();
    /**
     * Render container footer
     */
    abstract public function end();
    /**
     * Render container menu
     */
    public function menus()
    {

    }
    /**
     * Render html
     */
    public function render()
    {
        $this->start();

        $template = $this->_template;
        if (is_string($template) && is_file($template)) {
            include $template;
        } else {
            $this->menus();
            $this->wrapper_start();

            do_action('get_custom_field_template');

            $first = true;
            foreach ($template as $key => $control) {
                $class            = 'ME_' . ucfirst($control['type']);
                $control['first'] = $first;
                $control          = new $class($control, $this);
                $control->render();
                $first = false;
            }

            $this->wrapper_end();
        }

        $this->end();
    }
    /**
     * Render container sub section header
     */
    public function wrapper_start()
    {}
    /**
     * Render container sub section footer
     */
    public function wrapper_end()
    {}
}
