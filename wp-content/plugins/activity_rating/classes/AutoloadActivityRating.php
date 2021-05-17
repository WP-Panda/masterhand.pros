<?php
class AutoloadActivityRating
{
	public static function init($dir = null)
	{
		if (!$dir) {
			$dir = __DIR__;
		}
		return spl_autoload_register(
			function ($classname) use ($dir) {

				$file = $dir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';

				if (is_file($file) && !class_exists($classname)) {
					require_once $file;
				}
			}
		);
	}
}