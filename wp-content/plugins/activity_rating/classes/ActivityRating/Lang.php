<?php
namespace ActivityRating;

class Lang
{
	protected static $_instance = null;

	protected $langTagDefault = 'en';
	protected $langTag = '';
	protected $lang = [];

	public function __construct()
	{
		$this->modulePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
		$this->langPath = $this->modulePath . 'lang' . DIRECTORY_SEPARATOR;
	}

	public function getLangTag()
	{
		return $this->langTag;
	}
	public function getLangPath()
	{
		return $this->langPath;
	}

	public function setLangTag($lang)
	{
		$this->langTag = !empty($lang)? $lang : $this->langTagDefault;

		return $this;
	}

	public function setLangPath($path = '', $relative = true)
	{
		$fullPath = $relative ? $this->modulePath . $path . DIRECTORY_SEPARATOR : trim($path, '/') . DIRECTORY_SEPARATOR;
		if(is_dir($fullPath)) {
			$this->langPath = $fullPath;
		}

		return $this;
	}

	public function getLangText($lang = null, $key = '')
	{
		$lang = is_null($lang)? (!empty($this->langTag)? $this->langTag : $this->langTagDefault) : (!empty($lang)? $lang : $this->langTagDefault);

		$langMsg = file_exists($this -> langPath . "{$lang}.php")? include($this -> langPath . "{$lang}.php") : [];

		return !empty($key) && isset($langMsg[$key])? $langMsg[$key] : $langMsg;
	}

	public function translate($key = '')
	{
		return $this->getLang($key);
	}

	public function getLang($key = '')
	{
		$lang = $this->getLangTag()? $this->getLangTag() : $this->langTagDefault;

		if(empty($this->lang)){
			$this->lang = file_exists($this -> langPath . "{$lang}.php")? include($this -> langPath . "{$lang}.php") : [];
		}

		return $key === 'ALL'? $this->lang : (!empty($key) && isset($this->lang[$key])? $this->lang[$key] : '');
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}