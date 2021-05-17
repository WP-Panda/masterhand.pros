<?php
/**
 * The template for displaying the case statuses for filtering.
 * This template can be overridden by copying it to yourtheme/marketengine/resolution/case-status-list.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 * @since 		1.0.1
 */
?>

<select name="status">
	<option value=""><?php _e('All', 'enginethemes'); ?></option>
<?php
	$statuses = marketengine_dispute_statuses();
	foreach ($statuses as $key => $status) :
?>
	<option <?php selected(isset($_GET['status']) && $_GET['status'] == $key); ?> value="<?php echo $key; ?>"><?php echo $status; ?></option>
<?php endforeach; ?>
</select>