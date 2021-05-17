<?php

namespace Pmue\Common\Bootstrap;

class Autoloader
{

    private $rootDir;

    private $prefix;

    const AUTOLOAD_PREFIX = 'Pmue';

    public function __construct($rootDir, $prefix)
    {
        $this->rootDir = $rootDir;
        $this->prefix = $prefix;
    }

    public function init()
    {
        $this->registerHelpers();
        $this->registerActions();
        $this->registerFilters();
        $this->registerShortcodes();

    }

    public function autoload($className) {
        $this->legacyAutoload($className);
        $this->psrAutoload($className);
    }

    /**
     * Autoloader
     * It's assumed class name consists of prefix followed by its name which in turn corresponds to location of source file
     * if `_` symbols replaced by directory path separator. File name consists of prefix folloed by last part in class name (i.e.
     * symbols after last `_` in class name)
     * When class has prefix it's source is looked in `models`, `controllers`, `shortcodes` folders, otherwise it looked in `core` or `library` folder
     *
     * @param string $className
     * @return bool
     */
    private function legacyAutoload($className)
    {

        $is_prefix = false;
        $filePath = str_replace('_', '/', preg_replace('%^' . preg_quote($this->prefix, '%') . '%', '', strtolower($className), 1, $is_prefix)) . '.php';
        if (!$is_prefix) { // also check file with original letter case
            $filePathAlt = $className . '.php';
        }
        foreach ($is_prefix ? array('models', 'controllers', 'shortcodes', 'classes', 'libraries') : array() as $subdir) {
            $path = $this->rootDir . '/' . $subdir . '/' . $filePath;
            if (is_file($path)) {
                require $path;
                return TRUE;
            }
            if (!$is_prefix) {
                $pathAlt = $this->rootDir . '/' . $subdir . '/' . $filePathAlt;
                if (is_file($pathAlt)) {
                    require $pathAlt;
                    return TRUE;
                }
            }
        }

        if (file_exists($this->rootDir . '/libraries/' . $className . '.php')) {
            require $this->rootDir . '/libraries/' . $className . '.php';
            return true;
        }

        return false;
    }

    public function psrAutoload($className)
    {
        if(strpos($className, '\\') !== false){

            // project-specific namespace prefix
            $prefix = self::AUTOLOAD_PREFIX . '\\';

            // base directory for the namespace prefix
            $base_dir = $this->rootDir . '/src/';

            // does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $className, $len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }

            // get the relative class name
            $relative_class = substr($className, $len);

            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            // if the file exists, require it
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    private function registerHelpers()
    {
        // register helpers
        if (is_dir($this->rootDir . '/helpers')) foreach (\PMUE_Helper::safe_glob($this->rootDir . '/helpers/*.php', \PMUE_Helper::GLOB_RECURSE | \PMUE_Helper::GLOB_PATH) as $filePath) {
            require_once $filePath;
        }

    }

    private function registerActions()
    {
        // register action handlers
        if (is_dir($this->rootDir . '/actions')) if (is_dir($this->rootDir . '/actions')) foreach (\PMUE_Helper::safe_glob($this->rootDir . '/actions/*.php', \PMUE_Helper::GLOB_RECURSE | \PMUE_Helper::GLOB_PATH) as $filePath) {
            require_once $filePath;
            $function = $actionName = basename($filePath, '.php');
            if (preg_match('%^(.+?)[_-](\d+)$%', $actionName, $m)) {
                $actionName = $m[1];
                $priority = intval($m[2]);
            } else {
                $priority = 10;
            }
            \add_action($actionName, $this->prefix . str_replace('-', '_', $function), $priority, 99); // since we don't know at this point how many parameters each plugin expects, we make sure they will be provided with all of them (it's unlikely any developer will specify more than 99 parameters in a function)
        }
    }

    private function registerFilters()
    {
        if (is_dir($this->rootDir . '/filters')) foreach (\PMUE_Helper::safe_glob($this->rootDir . '/filters/*.php', \PMUE_Helper::GLOB_RECURSE | \PMUE_Helper::GLOB_PATH) as $filePath) {
            require_once $filePath;
            $function = $actionName = basename($filePath, '.php');
            if (preg_match('%^(.+?)[_-](\d+)$%', $actionName, $m)) {
                $actionName = $m[1];
                $priority = intval($m[2]);
            } else {
                $priority = 10;
            }
            \add_filter($actionName, $this->prefix . str_replace('-', '_', $function), $priority, 99); // since we don't know at this point how many parameters each plugin expects, we make sure they will be provided with all of them (it's unlikely any developer will specify more than 99 parameters in a function)
        }

    }

    private function registerShortcodes()
    {
        // register shortcodes handlers
        if (is_dir($this->rootDir . '/shortcodes')) foreach (\PMUE_Helper::safe_glob($this->rootDir . '/shortcodes/*.php', \PMUE_Helper::GLOB_RECURSE | \PMUE_Helper::GLOB_PATH) as $filePath) {
            $tag = strtolower(str_replace('/', '_', preg_replace('%^' . preg_quote($this->rootDir . '/shortcodes/', '%') . '|\.php$%', '', $filePath)));
            \add_shortcode($tag, array($this, 'shortcodeDispatcher'));
        }
    }
}