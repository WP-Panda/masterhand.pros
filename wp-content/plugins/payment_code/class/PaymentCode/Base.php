<?php
namespace PaymentCode;

use LogWriter\Log;

class Base
{
    protected static $_instance = null;

    protected static $_debug = true;

    const VERSION = '1.0';

    const tbCodes = 'payment_code';

    public $tbCodes = '';

    protected static $optionPrefix = 'paycode_';

    protected static $optionList = [
        'subjectLetter' => 'Payment code for project',
        'messageLetter' => 'For your project <b>[project_name]</b> was created payment code.<br>Payment code: [payment_code]',
        'messageSMS' => 'Payment code [project_name]: [payment_code]',
        'noticeSMS' => 1,
    ];

    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;

        $this->tb_prefix = $this->db->prefix;
        $this->db_name = $this->db->__get('dbname');

        $this->tbCodes = $this->tb_prefix . self::tbCodes;
    }

    public static function getOptNoticeSMS()
    {
        return (int)self::getOption('noticeSMS', self::$optionList['noticeSMS']);
    }

    public static function getOptMsgSMS()
    {
        return self::getOption('messageSMS', self::$optionList['messageSMS']);
    }

//    public static function getOptMsgLetter()
//    {
//        return self::getOption('messageLetter', self::$optionList['messageLetter']);
//    }

    public static function getOptSubjLetter()
    {
        return self::getOption('subjectLetter', self::$optionList['subjectLetter']);
    }

    public static function getOption($option = '', $default = '')
    {
//        $default = !empty($default)? $default : self::$optionList[$option];
        return get_option(self::$optionPrefix . $option, $default);
    }

    public function escapeStr($str)
    {
        $str = (is_array($str) || empty($str)) ? '' : trim(strval($str));
        return $this->db->_escape(htmlspecialchars($str));
    }

    public function toInt($str = 0)
    {
        return is_numeric($str) || is_string($str) ? intval($str) : 0;
    }

    public function toFloat($str = 0)
    {
        return is_numeric($str) || is_string($str) ? floatval($str) : 0;
    }

    public static function outputJSON($data = array(), $status = false)
    {
        $response = [];

        if (is_string($data))
            $response['msg'] = $data;
        else
            $response = !empty($data) ? $data : [];

        $response['status'] = isset($response['status']) ? $response['status'] : ($status ? 'success' : 'error');

        $response['msg'] = ($response['status'] == 'error' && empty($response['msg'])) ? 'Error!!!' : $response['msg'];

        if (self::$_debug) {
            if ($log = Log::getInstance()->getLog()) {
                $response['log'] = $log;
            }
            $calledClass = get_called_class();
            $sqlError = $calledClass::getInstance()->db->last_error;
            if ($sqlError) {
                $response['sqlError'] = $sqlError;
                $response['sqlRequest'] = $calledClass::getInstance()->db->last_query;
            }
			$response['debug'] = debug_backtrace();
        }

        ob_clean();
        header_remove();
        header('Content-type: text/json; charset=UTF-8');
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function tbIsExists($tb_name = '')
    {
        $tb_name = empty($tb_name) ? $this->tbCodes : $tb_name;
        $q = $this->db->query("SHOW TABLES FROM `{$this -> db_name}` LIKE '{$tb_name}'");
        return ($q) ? true : false;
    }
}