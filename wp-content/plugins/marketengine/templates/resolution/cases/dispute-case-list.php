<?php
/**
 * The template for displaying the list of dispute cases.
 * This template can be overridden by copying it to yourtheme/marketengine/resolution/dispute-case-list.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 * @since 		1.0.1
 */
?>

<?php if($query->found_posts) : ?>

	<div class="me-table me-table-resolution">

		<?php marketengine_get_template('resolution/cases/cases-table-header'); ?>

		<?php foreach ($query->posts as $case) : ?>

		<?php marketengine_get_template('resolution/cases/cases-rows', array('case' => $case)); ?>

		<?php endforeach; ?>
	
	</div>

<?php else: ?>

	<div class="me-table me-table-empty me-table-resolution">

		<?php marketengine_get_template('resolution/cases/cases-table-header'); ?>

	</div>
	
	<?php marketengine_get_template('resolution/cases/no-cases'); ?>
	
<?php endif; ?>
