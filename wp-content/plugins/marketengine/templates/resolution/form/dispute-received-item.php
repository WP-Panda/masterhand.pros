<?php
	$checked = !isset($_POST['is_received_item']) || (isset($_POST['is_received_item']) && $_POST['is_received_item'] == 'true') ? true : false;
?>

<div class="me-receive-item">
	<h3><?php _e('Did you receive your item?', 'enginethemes'); ?></h3>
	<div class="marketengine-radio-field">
	    <div>
	    	<label class="me-radio" for="me-receive-item-yes">
	    		<input class="me-receive-item-field" id="me-receive-item-yes" name="is_received_item" type="radio" data-get-refund-block="dispute-get-refund-yes" value="true" <?php checked($checked, true); ?>>
	    		<span><?php _e('Yes', 'enginethemes'); ?></span>
	    		</label>	
		    <label class="me-radio" for="me-receive-item-no">
		    	<input class="me-receive-item-field" id="me-receive-item-no" name="is_received_item" type="radio" data-get-refund-block="dispute-get-refund-no" value="false" <?php checked($checked, false); ?>>
		    	<span><?php _e('No', 'enginethemes'); ?></span>
	    	</label>	
		</div>
	</div>
</div>