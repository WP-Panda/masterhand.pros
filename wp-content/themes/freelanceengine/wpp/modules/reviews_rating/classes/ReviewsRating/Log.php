<?php
namespace ReviewsRating;

class Log
{
	static $_instance = null;

	private $debug = true;
	private $logDebug = [];

	public function __construct()
	{

	}

	public function logging($flag = true)
	{
		$this->debug = $flag? true : false;
	}

	public function addLog($flag = '', $msg = '', $count = false)
	{
		if($this->debug){
			if(is_array($msg)){
				if($count){
					$this->logDebug[$flag][] = $msg;
				} else {
					$ar = isset($this->logDebug[$flag])? $this->logDebug[$flag] : [];
					$this->logDebug[$flag] = array_merge($ar, $msg);
				}
			}
			else{
				if($count){
					$c = isset($this->logDebug[$flag])? (int)$this->logDebug[$flag] : 0;
					$this->logDebug[$flag] = $c + ((is_numeric($msg) && $msg > 1)? $msg : 1);
				}
				else{
					if(empty($flag))
						$this->logDebug[] = $msg;
					else
						$this->logDebug[$flag].= !empty($this->logDebug[$flag])? ', ' . $msg : $msg;
				}
			}
		}
	}

	public function getLog($key = null)
	{
		if(is_null($key))
			return $this->logDebug;

		return isset($this->logDebug[$key])? $this->logDebug[$key] : null;
	}

	public function reset()
	{
		$this->logDebug = [];
	}

	public function save()
	{
		$log = $this->getLog();
		if(!empty($log)) {
			$curr_m = microtime(true);
			$now = \DateTime::createFromFormat('U.u', $curr_m);
			$now->setTimeZone(new \DateTimeZone('Europe/Kiev'));

			file_put_contents(dirname(dirname(__DIR__)) . '/log_' . $now->format("d-m-Y H:i") . '.log',
					json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), FILE_APPEND);

			$this->reset();
		}
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}