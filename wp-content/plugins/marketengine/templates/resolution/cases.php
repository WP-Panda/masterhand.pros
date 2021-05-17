<?php
/**
 * The template for displaying the resolution center page.
 * This template can be overridden by copying it to yourtheme/marketengine/resolution/cases.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 * @since 		1.0.1
 */

$query = marketengine_rc_dispute_case_query($_GET);
?>

<div class="me-resolution">

	<?php marketengine_get_template('resolution/cases/cases-filter'); ?>

	<?php marketengine_get_template('resolution/cases/dispute-case-list', array('query' => $query)); ?>

	<?php marketengine_get_template('resolution/cases/cases-pagination', array('query' => $query)); ?>

</div>