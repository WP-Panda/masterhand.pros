<?php

namespace Pmue\Common\Bootstrap;

class AdminDispatcher
{

    private $prefix;

    private $_admin_current_screen = NULL;


    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function dispatch($page, $action)
    {
        if (preg_match('%^' . preg_quote(str_replace('_', '-', $this->prefix), '%') . '([\w-]+)$%', $page)) {
            $this->adminDispatcher($page, strtolower($action));
        }
    }

    /**
     * Dispatch admin page: call corresponding controller based on get parameter `page`
     * The method is called twice: 1st time as handler `parse_header` action and then as admin menu item handler
     * @param string[optional] $page When $page set to empty string ealier buffered content is outputted, otherwise controller is called based on $page value
     */
    public function adminDispatcher($page = '', $action = 'index') {

        static $buffer = NULL;
        static $buffer_callback = NULL;
        if ('' === $page) {
            if ( ! is_null($buffer)) {
                echo '<div class="wrap">';
                echo $buffer;
                do_action('PMUE_action_after');
                echo '</div>';
            } elseif ( ! is_null($buffer_callback)) {
                echo '<div class="wrap">';
                call_user_func($buffer_callback);
                do_action('PMUE_action_after');
                echo '</div>';
            } else {
                throw new Exception('There is no previousely buffered content to display.');
            }
        } else {
            $controllerName = preg_replace_callback('%(^' . preg_quote($this->prefix, '%') . '|_).%', array($this, "replace_callback"),str_replace('-', '_', $page));
            $actionName = str_replace('-', '_', $action);
            if (method_exists($controllerName, $actionName)) {

                if ( ! get_current_user_id() or ! current_user_can('manage_options')) {
                    // This nonce is not valid.
                    die( 'Security check' );

                } else {

                    $this->_admin_current_screen = (object)array(
                        'id' => $controllerName,
                        'base' => $controllerName,
                        'action' => $actionName,
                        'is_ajax' => isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest',
                        'is_network' => is_network_admin(),
                        'is_user' => is_user_admin(),
                    );
                    add_filter('current_screen', array($this, 'getAdminCurrentScreen'));
                    add_filter('admin_body_class', function(){return $this->prefix . "plugin";});

                    $controller = new $controllerName();
                    if ( ! $controller instanceof PMUE_Controller_Admin) {
                        throw new Exception("Administration page `$page` matches to a wrong controller type.");
                    }

                    if ($this->_admin_current_screen->is_ajax) { // ajax request
                        $controller->$action();
                        do_action('PMUE_action_after');
                        die(); // stop processing since we want to output only what controller is randered, nothing in addition
                    } elseif ( ! $controller->isInline) {
                        ob_start();
                        $controller->$action();
                        $buffer = ob_get_clean();
                    } else {
                        $buffer_callback = array($controller, $action);
                    }
                }

            } else { // redirect to dashboard if requested page and/or action don't exist
                wp_redirect(admin_url()); die();
            }
        }
    }

    public function getAdminCurrentScreen()
    {
        return $this->_admin_current_screen;
    }
}