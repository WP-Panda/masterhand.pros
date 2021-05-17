<?php
namespace ReviewsRating;

class Base
{
	const VERSION = '1.1';

	protected static $_instance = null;

	protected static $_debug = false;

	const tbConfig = 'reviews_config';
	const tbReviews = 'reviews';
	const tbReviewsDetails = 'reviews_details';
	const tbReviewsStatus = 'reviews_status';

	public $tb_prefix = '';
	public $tbConfig = 'reviews_config';
	public $tbReviews = 'reviews';
	public $tbReviewsDetails = 'reviews_details';
	public $tbReviewsStatus = 'reviews_status';

	public $triggerReviews = 'triggerReviews';

	protected $langTagDefault = 'en';
	protected $langTag = '';
	protected $lang = [];
	protected $_config = [];

	private static $defaultTimeZone = 'UTC';

	protected $stars = 5;

	public function __construct()
	{
		global $wpdb;

		$this -> db = $wpdb;

		$this -> tb_prefix = $this -> db -> prefix;
		$this -> db_name = $this -> db -> __get('dbname');

		$this -> tbConfig = $this -> tb_prefix . self::tbConfig;
		$this -> tbReviews = $this -> tb_prefix . self::tbReviews;
		$this -> tbReviewsDetails = $this -> tb_prefix . self::tbReviewsDetails;
		$this -> tbReviewsStatus = $this -> tb_prefix . self::tbReviewsStatus;

		$this -> tbPosts = $this -> tb_prefix . 'posts';
		$this -> tbPostMeta = $this -> tb_prefix . 'postmeta';

		$this -> modulePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
		$this -> langPath = $this->modulePath . 'lang' . DIRECTORY_SEPARATOR;
		$this -> basePath = dirname(dirname(dirname($this->modulePath))) . DIRECTORY_SEPARATOR;

	}

	public function getLangTag()
	{
		return $this->langTag;
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

		return $key == 'ALL'? $this->lang : (!empty($key) && isset($this->lang[$key])? $this->lang[$key] : '');
	}

	public function getMaxScore()
	{
		return $this->stars;
	}

	public function escapeStr($str)
	{
		$str = is_array($str)? '' : trim(strval($str));
		return $this->db->_escape(htmlspecialchars($str));
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

	public function toInt($str = 0)
	{
		return (int)$str;
	}
	public function toFloat($str = 0)
	{
		return floatval($str);
	}

	public function getConfig()
	{
		$config = [];
		$res = $this->db->get_results("SELECT name, value FROM {$this -> tbConfig}", ARRAY_A);
		if(!empty($res)) {
			foreach ($res as $r) {
				$config[$r['name']] = $r['value'];
			}
		}
		return $config;
	}

	public function getParamConfig($name, $force = false)
	{
		if(empty($name)) return NULL;

		if($force){
			$value = $this->db->get_var("SELECT value FROM {$this -> tbConfig} WHERE name = '{$this->escapeStr($name)}'");
			$this->_config[$name] = $value;
		} elseif(!isset($this->_config[$name])) {
			$value = $this->db->get_var("SELECT value FROM {$this -> tbConfig} WHERE name = '{$this->escapeStr($name)}'");
			$this->_config[$name] = ($name == 'page_step' && empty($value))? 15 : $value;
		}

		return $this -> _config[$name];
	}

	public function sendLetter($to, $subj='', $message='',  $from='')
	{
		if(file_exists(dirname($this->modulePath) . 'log_emails/smtp_send.php')){

		}

		return false;
	}

	public static function generateCode($length = 8, $isNum = true, $separator='', $after = 2)
	{
		$chars = !$isNum ? 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP' : '1234567890';
		$numChars = strlen($chars);
		$string = '';
		$count = 1;
		for ($i=0; $i<$length; $i++) {
			$add_separator = '';
			if((strlen(trim($separator))>0) && $count == $after){
				$add_separator = trim($separator);
				$count = 0;
			}

			$string .= substr($chars, rand(1, $numChars) - 1, 1) . $add_separator;
			$count++;
		}

		return trim($string, trim($separator));
	}

	public static function outputJSON($data = array(), $status = false)
	{
		$response = [];

		if(is_string($data))
			$response['msg'] = $data;
		else
			$response = !empty($data)? $data : [];

		$response['status'] = isset($response['status'])? $response['status'] : ($status? 'success' : 'error');

		if(self::$_debug) {
			if ($log = Log::getInstance()->getLog()) {
				$response['log'] = $log;
			}

			$calledClass = get_called_class();
			$sqlError = $calledClass::getInstance()->db->last_error;
			if ($sqlError) {
				$response['sqlError'] = $sqlError;
				$response['sqlRequest'] = $calledClass::getInstance()->db->last_query;
			}
//			$response['debug'] = debug_backtrace();
		}

		$response['msg'] = ($response['status'] == 'error' && empty($response['msg']))? 'Error!!!' : $response['msg'];

//		if(true)
//			$response['debug'] = debug_backtrace();

		ob_clean();
		header_remove();
		header('Content-type: text/json; charset=UTF-8');
		echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		exit;
	}

	public function tbIsExists($tb_name = '')
	{
		$tb_name = empty($tb_name)? $this->tbConfig : $tb_name;
		$q = $this -> db -> query( "SHOW TABLES FROM `{$this -> db_name}` LIKE '{$tb_name}'" );
		return ($q) ? true : false;
	}

	public static function isValidDateTime($date)
	{
		return (\DateTime::createFromFormat('Y-m-d H:i:s', $date) !== FALSE);
	}

	public static function getTimestamp($time = null, $timeZone = '', $msec = false)
	{
		$time = is_null($time)? time() : $time;
		$timeZone = !empty($timeZone)? $timeZone : self::$defaultTimeZone;
		$now = \DateTime::createFromFormat(($msec? 'U.u':'U'), $time);
		if(!empty($timeZone))
			$now->setTimeZone(new \DateTimeZone($timeZone));

		$now_date = $now->format($msec? 'Y-m-d H:i:s.u':'Y-m-d H:i:s');

		return $now_date;
	}

	public static function getDatetime($time = null, $timeZone = '')
	{
		$time = is_null($time)? time() : $time;
		$timeZone = !empty($timeZone)? $timeZone : self::$defaultTimeZone;
		$now = \DateTime::createFromFormat('U', $time);
		if(!empty($timeZone))
			$now->setTimeZone(new \DateTimeZone($timeZone));

		$now_date = $now->format('Y-m-d');

		return $now_date;
	}

	public static function getUTimeTimezone($timeZone = '')
	{
		$time = time();
		$timeZone = !empty($timeZone)? $timeZone : self::$defaultTimeZone;
		$now = \DateTime::createFromFormat('U', $time);
		if(!empty($timeZone))
			$now->setTimeZone(new \DateTimeZone($timeZone));

		return $now->getTimestamp();
	}

	public static function getListTimeZones()
	{
		return \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}