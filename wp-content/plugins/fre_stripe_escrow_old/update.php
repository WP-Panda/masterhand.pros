<?php
if (!class_exists('AE_Stripe_Escrow_Update') && class_exists('AE_Plugin_Updater')){
    class AE_Stripe_Escrow_Update extends AE_Plugin_Updater{
        const VERSION = '1.3.2';

        // setting up updater
        public function __construct(){
            $this->product_slug     = plugin_basename( dirname(__FILE__) . '/ae_stripe_escrow.php' );
            $this->slug             = 'ae_stripe_escrow';
            $this->license_key      = get_option('et_license_key', '');
            $this->current_version  = self::VERSION;
            $this->update_path      = 'http://update.enginethemes.com/?do=product-update&product=ae_stripe_escrow&type=plugin';

            parent::__construct();
        }
    }
    new AE_Stripe_Escrow_Update();
}
