<?php
if( !class_exists( 'AE_Milestone_Update' ) && class_exists( 'AE_Plugin_Updater' ) ) {
	class AE_Milestone_Update extends AE_Plugin_Updater
	{
		const VERSION = '1.3.2';

		// setting updater
		public function __construct() {
			$this->plugin_slug = plugin_basename( dirname( __FILE__ ) . '/ae_milestone.php' );
			$this->slug = 'fre_milestone';
			$this->license_key = get_option( 'et_license_key' );
			$this->update_path = 'http://update.enginethemes.com/?do=product-update&product=fre_milestone&type=plugin';

			parent::__construct();
		}
	}

	new AE_Milestone_Update();
}