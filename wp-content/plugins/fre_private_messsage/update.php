<?php
if (!class_exists('AE_Private_Message') && class_exists('AE_Plugin_Updater')){
    class AE_Private_Message extends AE_Plugin_Updater{
        const VERSION = '1.2.3';
        // setting up updater
        public function __construct(){
            $this->product_slug     = plugin_basename( dirname(__FILE__) . '/fre_private_message.php' );
            $this->slug             = 'fre_private_message';
            $this->license_key      = get_option('et_license_key', '');
            $this->current_version  = self::VERSION;
            $this->update_path      = 'http://update.enginethemes.com/?do=product-update&product=fre_private_message&type=plugin';

            parent::__construct();
        }
    }
    new AE_Private_Message();
}