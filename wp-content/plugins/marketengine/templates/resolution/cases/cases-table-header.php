<?php
/**
 * The template for displaying the table header of dispute cases list.
 * This template can be overridden by copying it to yourtheme/marketengine/resolution/cases-table-header.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 * @since 		1.0.1
 */
?>
<div class="me-table-rhead">
	<div class="me-table-col me-rslt-case"><?php _e('CASE ID', 'enginethemes'); ?></div>
	<div class="me-table-col me-rslt-status"><?php _e('STATUS', 'enginethemes'); ?></div>
	<div class="me-table-col me-rslt-problem"><?php _e('PROBLEM', 'enginethemes'); ?></div>
	<div class="me-table-col me-rslt-date"><?php _e('OPEN DATE', 'enginethemes'); ?></div>
	<div class="me-table-col me-rslt-related"><?php _e('RELATED PARTY', 'enginethemes'); ?></div>
</div>