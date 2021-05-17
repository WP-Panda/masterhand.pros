<?php
namespace LogWriter;

class Log
{
    static $_instance = null;

    private $logDebug = [];

    public function __construct()
    {

    }

    public function addLog($flag = '', $message = '', $count = false)
    {
        $flag = is_string($flag)? $flag : 'debug';
        if(is_array($message)){
            if($count){
                $this->logDebug[$flag][] = $message;
            } else {
                $ar = isset($this->logDebug[$flag])? $this->logDebug[$flag] : [];
                $this->logDebug[$flag] = array_merge((array)$ar, $message);
            }
        }
        else{
            if($count){
                $c = isset($this->logDebug[$flag])? (int)$this->logDebug[$flag] : 0;
                $this->logDebug[$flag] = $c + ((is_numeric($message) && $message > 1)? $message : 1);
            }
            else{
                if(empty($flag))
                    $this->logDebug[] = $message;
                else {
                    if(isset($this->logDebug[$flag]) && is_array($this->logDebug[$flag])){
                        $this->logDebug[$flag][] = $message;
                    } else {
                        if(!isset($this->logDebug[$flag])) {
                            $this->logDebug[$flag] = '';
                        }
                        $this->logDebug[$flag] .= !empty($this->logDebug[$flag]) ? ', ' . $message : $message;
                    }
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

    public function reset($key = null)
    {
        if(is_null($key)) {
            $this->logDebug = [];
        } else {
            $this->logDebug[$key] = [];
        }
    }

    public function save($fileSuffix = '', $path  = '')
    {
        $log = $this->getLog();
        if(!empty($log)) {
            $curr_m = microtime(true);
            $now = \DateTime::createFromFormat('U.u', $curr_m);
            $now->setTimeZone(new \DateTimeZone('UTC'));
            
            $fileName = 'log_' . (!empty(strval($fileSuffix))? strval($fileSuffix) . '_' : '') . $now->format("Y-m-d_H") . '.log';
            $filePath = (!empty($path) && is_dir($path))? rtrim($path, '/') . DIRECTORY_SEPARATOR : dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
            file_put_contents($filePath . $fileName,
                $now->format("Y-m-d H:i:s.u") . "\n" . json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n", FILE_APPEND);

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