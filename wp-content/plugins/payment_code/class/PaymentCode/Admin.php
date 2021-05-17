<?php
namespace PaymentCode;

class Admin extends Base
{
    protected static $_instance = null;

    public static function getOptKeys()
    {
        return array_keys(self::$optionList);
    }

    public function updateOptions()
    {
        if(!empty($_POST)){
            if(isset($_POST['resetPayCode'])){
                self::deleteOptions();
            } else {
                foreach ($_POST as $item => $value) {
                    self::updateOption($item, $value);
                }
            }
        }
    }

    private static function updateOption($key, $value)
    {
        if(in_array($key, self::getOptKeys())){
            $access = ($value == 0 || !empty($value))? 1 : 0;
            if($access) {
                $opt = self::$optionPrefix . $key;
                update_option($opt, $value);
            }
        }
    }

    private static function deleteOptions()
    {
        $list = self::getOptKeys();
        foreach ($list as $key) {
            $opt = self::$optionPrefix . $key;
            delete_option($opt);
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