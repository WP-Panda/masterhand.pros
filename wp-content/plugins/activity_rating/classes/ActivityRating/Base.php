<?php
namespace ActivityRating;

class Base
{
	protected static $_instance = null;

	protected static $_debug = true;

	const VERSION = '1.1';

	const tbRating = 'activity_rating';
	const tbRatingDetail = 'activity_rating_detail';
	const tbConfig = 'activity_rating_config';

	public $tbConfig = '';
	public $tbRating = '';
	public $tbRatingDetail = '';

	public function __construct()
	{
		global $wpdb;

		$this->db = $wpdb;

		$this->tb_prefix = $this->db->prefix;
		$this->db_name = $this->db->__get('dbname');

		$this->tbConfig = $this->tb_prefix . self::tbConfig;
		$this->tbRating = $this->tb_prefix . self::tbRating;
		$this->tbRatingDetail = $this->tb_prefix . self::tbRatingDetail;

		$this -> modulePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
		$this -> basePath = dirname(dirname(dirname($this->modulePath))) . DIRECTORY_SEPARATOR;

		$this -> logger = Log::getInstance();
	}

	public function run($action)
	{
		$action = 'action' . ucfirst(trim($action));
		if (!method_exists($this, $action)) {
			return null;
		}

		$output = $this->{$action}();

		if ($output === null) {
			self::outputJSON();
		}

		if (is_string($output)) {
			self::outputJSON($output);
		}

		self::outputJSON($output, 1);
	}

	public function getLangTag()
	{
		return Lang::getInstance()->getLangTag();
	}
	public function getLangPath()
	{
		return Lang::getInstance()->getLangPath();
	}

	public function setLangTag($lang)
	{
		return Lang::getInstance()->setLangTag($lang);
	}

	public function setLangPath($path = '', $relative = true)
	{
		return Lang::getInstance()->setLangPath($path, $relative);
	}

	public function getLangText($lang = null, $key = '')
	{
		return Lang::getInstance()->getLangText($lang, $key);
	}

	public function translate($key = '')
	{
		return $this->getLang($key);
	}

	public function getLang($key = '')
	{
		return Lang::getInstance()->getLang($key);
	}

	public function addError($msg)
	{
		$this->logger->addLog('error', $msg);
	}

	public function getError()
	{
		$error = $this->logger->getLog('error');

		return (is_array($error))? implode(', ', $error) : $error;
	}

	public function escapeStr($str)
	{
		$str = (is_array($str) || empty($str))? '' : trim(strval($str));
		return $this->db->_escape(htmlspecialchars($str));
	}

	public function toInt($str = 0)
	{
		return is_numeric($str) || is_string($str)? intval($str) : 0;
	}

	public function toFloat($str = 0)
	{
		return is_numeric($str) || is_string($str)? floatval($str) : 0;
	}

	public static function outputJSON($data = array(), $status = false)
	{
		$response = [];

		if(is_string($data))
			$response['msg'] = $data;
		else
			$response = !empty($data)? $data : [];

		$response['status'] = isset($response['status'])? $response['status'] : ($status? 'success' : 'error');

		$response['msg'] = ($response['status'] == 'error' && empty($response['msg']))? 'Error!!!' : $response['msg'];

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

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}