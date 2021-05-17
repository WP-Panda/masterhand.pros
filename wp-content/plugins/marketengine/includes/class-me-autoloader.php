<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * MarketEngine Autoloader.
 *
 * @version        1.0
 * @package        MarketEngine/Includes/
 * @category    Class
 * @author         MarketEngine
 */
class ME_Autoloader {

    /**
     * Path to the includes directory.
     *
     * @var string
     */
    private $include_path = '';

    /**
     * The Constructor.
     */
    public function __construct() {
        if (function_exists("__autoload")) {
            spl_autoload_register("__autoload");
        }

        spl_autoload_register(array($this, 'autoload'));

        $this->include_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/';
    }

    /**
     * Take a class name and turn it into a file name.
     *
     * @param  string $class
     * @return string
     */
    private function get_file_name_from_class($class) {
        return 'class-' . str_replace('_', '-', $class) . '.php';
    }

    /**
     * Include a class file.
     *
     * @param  string $path
     * @return bool successful or not
     */
    private function load_file($path) {
        if ($path && is_readable($path)) {
            include_once $path;
            return true;
        }
        return false;
    }

    /**
     * Auto-load ME classes on demand to reduce memory consumption.
     *
     * @param string $class
     */
    public function autoload($class) {
        $class = strtolower($class);
        $file = $this->get_file_name_from_class($class);
        $path = '';
        // cho nay kiem tra ten class de thay doi include path cho phu hop
        if (strpos($class, 'marketengine_listings') === 0) {
            $path = $this->include_path . 'listings/' . substr(str_replace('_', '-', $class), 18) . '/';
        } elseif (strpos($class, 'marketengine_user') === 0) {
            $path = $this->include_path . 'users/' . substr(str_replace('_', '-', $class), 11) . '/';
        } elseif (strpos($class, 'marketengine_authentication') === 0) {
            $path = $this->include_path . 'authentication/' . substr(str_replace('_', '-', $class), 12) . '/';
        } elseif (strpos($class, 'marketengine_shortcodes') === 0) {
            $path = $this->include_path . 'shortcodes/' . substr(str_replace('_', '-', $class), 12) . '/';
        }

        if (empty($path) || (!$this->load_file($path . $file) && strpos($class, 'marketengine_') === 0)) {
            $this->load_file($this->include_path . $file);
        }
    }
}
new ME_Autoloader();