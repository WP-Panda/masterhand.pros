<?php
namespace PaymentCode;

class Activate extends Base
{
    protected static $_instance = null;

    public function installTb()
    {
        $this->db->query($this->sgl_tbCodes());
    }

    public function uninstallTb()
    {
        $this->db->query("DROP TABLE IF EXISTS {$this->tbCodes}");
    }

    public function sgl_tbCodes()
    {
        return "CREATE TABLE IF NOT EXISTS `{$this->tbCodes}` (
   			`bid_id` BIGINT(20) NOT NULL DEFAULT '0',
            `code` VARCHAR(50) NULL DEFAULT '',
            `used` TINYINT(1) NULL DEFAULT '0',
            PRIMARY KEY (`bid_id`)
        )
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		";
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}