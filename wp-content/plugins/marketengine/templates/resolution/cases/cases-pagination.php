<?php
/**
 * The template for displaying pagination of dispute cases.
 * This template can be overridden by copying it to yourtheme/marketengine/resolution/case-pagination.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 * @since 		1.0.1
 */
?>
<div class="me-paginations">
	<?php marketengine_paginate_link($query); ?>
</div>