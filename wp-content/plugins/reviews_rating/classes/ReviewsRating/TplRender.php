<?php
namespace ReviewsRating;

class TplRender
{
	static $_instance = null;
	public $fenom = null;
	public $tplPath = null;
	public function __construct()
	{
		$this->tplPath = dirname(dirname(__DIR__)) . '/templates/';
		$tplCache = $this->tplPath . 'cache';
		if(!file_exists($tplCache)){
			mkdir($tplCache, 0755, true);
		}


		$this->fenom = \Fenom::factory($this->tplPath, $tplCache);
		$this->fenom->setOptions(\Fenom::AUTO_RELOAD);
	}

	public static function display($tpl, $vars)
	{
		self::getInstance()->fenom->display($tpl, $vars);
	}

	public static function fetch($tpl, $vars)
	{
		return self::getInstance()->fenom->fetch($tpl, $vars);
	}

	public static function parseText($tpl = '', $ph = [], $left = '[+', $right = '+]')
	{
		if(is_array($ph) && !empty($ph)) {
			foreach ($ph as $key => $value) {
				$k = "{$left}{$key}{$right}";
				$tpl = str_replace($k, $value, $tpl);
			}
		}

		$tpl = preg_replace('~\[\+(.*?)\+\]~', '', $tpl);

		return $tpl;
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}