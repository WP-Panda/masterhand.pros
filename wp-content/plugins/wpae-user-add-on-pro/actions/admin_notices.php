<?php 

function pmue_admin_notices() {
	// notify user if history folder is not writable		
	if ( ! class_exists( 'PMXE_Plugin' ) || PMXE_EDITION == 'free' ) {
		?>
		<div class="error"><p>
			<?php printf(
					__('<b>%s Plugin</b>: WP All Export Pro must be installed and activated. You can download it here <a href="http://www.wpallimport.com/portal" target="_blank">http://www.wpallimport.com/portal/</a>', 'pmue_plugin'),
					PMUE_Plugin::getInstance()->getName()
			) ?>
		</p></div>
		<?php
		
		deactivate_plugins( PMUE_ROOT_DIR . '/wpae-user-add-on-pro.php');
		return;
		
	}

	if ( class_exists( 'PMXE_Plugin' ) and ( version_compare(PMXE_VERSION, '1.5.6') < 0 and PMXE_EDITION != 'paid') ) {
		?>
		<div class="error"><p>
			<?php printf(
					__('<b>%s Plugin</b>: Please update your WP All Export to the latest version', 'wp_all_import_user_add_on'),
					PMUE_Plugin::getInstance()->getName()
			) ?>
		</p></div>
		<?php
		
		deactivate_plugins( PMUE_ROOT_DIR . '/wpae-user-add-on-pro.php');
	}

	$input = new PMUE_Input();
	$messages = $input->get('pmue_nt', array());
	if ($messages) {
		is_array($messages) or $messages = array($messages);
		foreach ($messages as $type => $m) {
			in_array((string)$type, array('updated', 'error')) or $type = 'updated';
			?>
			<div class="<?php echo $type ?>"><p><?php echo $m ?></p></div>
			<?php 
		}
	}

	if ( ! empty($_GET['type']) and $_GET['type'] == 'user'){
		?>
		<script type="text/javascript">
			(function($){$(function () {
				$('#toplevel_page_pmxi-admin-home').find('.wp-submenu').find('li').removeClass('current');
				$('#toplevel_page_pmxi-admin-home').find('.wp-submenu').find('a').removeClass('current');
				$('#toplevel_page_pmxi-admin-home').find('.wp-submenu').find('li').eq(2).addClass('current').find('a').addClass('current');
			});})(jQuery);
		</script>
		<?php
	}
}
