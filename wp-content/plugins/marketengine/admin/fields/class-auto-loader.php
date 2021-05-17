<?php
/**
 * ME_Field_Autoloader
 *
 * @version        1.0
 * @package        MarketEngine/Fields/
 * @category       Class
 * @author         MarketEngine
 */

if (!defined('ABSPATH')) {
    exit;
}
/**
 * ME_Field_Autoloader
 *
 * @version        1.0
 * @package        MarketEngine/Fields/
 * @category       Class
 * @author         MarketEngine
 */
class ME_Field_Autoloader {

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

        $this->include_path = dirname(__FILE__). '/';
    }

    /**
     * Take a class name and turn it into a file name.
     *
     * @param  string $class
     * @return string
     */
    private function get_file_name_from_class($class) {
        return str_replace('_', '-', $class) . '.php';
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

        if (strpos($class, 'me_tab') === 0 || strpos($class, 'me_section') === 0 || strpos($class, 'me_container') === 0) {
            $this->load_file($this->include_path . $file);
            return;
        } 

        if (strpos($class, 'me_') === 0) {
            $this->load_file($this->include_path . $file);   
        }
    }
}

new ME_Field_Autoloader();
