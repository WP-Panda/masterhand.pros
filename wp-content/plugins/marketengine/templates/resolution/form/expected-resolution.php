<?php
/**
 * The template for displaying resolutions expected.
 * This template can be overridden by copying it to yourtheme/marketengine/resolution/expected-resolution.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 * @since 		1.0.1
 */

?>
<div class="me-dispute-refund">
	<h3><?php _e('You want to:', 'enginethemes'); ?></h3>

	<div id="dispute-get-refund-yes" <?php echo  !isset($_POST['is_received_item']) || (isset($_POST['is_received_item']) && $_POST['is_received_item'] == 'true') ? 'class="active"' : ''; ?>>
		<?php
			$resolutions = marketengine_rc_item_received_expected_solutions(true);
			marketengine_get_template('resolution/form/resolution-item', array('resolutions' => $resolutions));
		?>
	</div>

	<div id="dispute-get-refund-no" <?php echo isset($_POST['is_received_item']) && $_POST['is_received_item'] == 'false' ? 'class="active"' : ''; ?>>
		<?php
			$resolutions = marketengine_rc_item_not_received_expected_solutions();
			marketengine_get_template('resolution/form/resolution-item', array('resolutions' => $resolutions));
		?>
	</div>

</div>