<?php
/**
 * The template for displaying the dispute form.
 * This template can be overridden by copying it to yourtheme/marketengine/resolution/dispute-form.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 * @since 		1.0.1
 */
$dispute_files = !empty($_POST['dispute_file']) ? $_POST['dispute_file'] : array();
?>

<?php do_action('marketengine_before_dispute_form'); ?>

<div class="me-dispute-form">

	<form id="me-dispute-form" method="post" action="" enctype="multipart/form-data">
		
		<?php do_action('marketengine_dispute_form_start'); ?>

		<?php marketengine_get_template('resolution/form/disputed-product-info', array('transaction' => $transaction)); ?>

		<?php marketengine_get_template('resolution/form/dispute-received-item'); ?>

		<?php marketengine_get_template('resolution/form/dispute-problem'); ?>

		<div class="me-dispute-description">
			<h3><?php _e('Please describe your problem in detail', 'enginethemes'); ?></h3>
			<textarea name="dispute_content"><?php echo isset($_POST['dispute_content']) ? esc_js($_POST['dispute_content']) : ''; ?></textarea>
		</div>

		<div class="me-dispute-image">
			<h3><?php _e('Attachments (images or documents)', 'enginethemes'); ?></h3>
			<?php

	        ob_start();
	        if($dispute_files) {
	            foreach($dispute_files as $gallery) {
	                marketengine_get_template('upload-file/multi-file-form', array(
	                    'image_id' => $gallery,
	                    'filename' => 'dispute_file',
	                    'close' => true
	                ));
	            }
	        }
	        $dispute_files = ob_get_clean();

	        marketengine_get_template('upload-file/upload-form', array(
	            'id' => 'dispute-file',
	            'name' => 'dispute_file',
	            'source' => $dispute_files,
	            'button' => 'me-dipute-upload',
	            'button_text' => __("Choose File", "enginethemes"),
	            'multi' => true,
	            'maxsize' => esc_html( '2mb' ),
	            'maxcount' => 5,
	            'close' => true
	        ));
	        ?>
		</div>

		<?php marketengine_get_template('resolution/form/expected-resolution'); ?>

		<?php wp_nonce_field('me-open_dispute_case'); ?>
		<?php wp_nonce_field('marketengine', 'me-dispute-file'); ?>

		<div class="me-dispute-submit">
			<input type="submit" class="me-dispute-submit-btn" name="me-open-dispute-case" value="<?php _e('SUBMIT', 'enginethemes'); ?>">
		</div>
		<a href="<?php echo remove_query_arg('action'); ?>" class="me-backlink"><?php _e('&lt; Back to transaction details', 'enginethemes'); ?></a>

		<input type="hidden" name="transaction-id" value="<?php echo $transaction->id; ?>">
		<input type="hidden" name="redirect" value="<?php echo get_the_permalink($transaction->id); ?>">

		<?php do_action('marketengine_dispute_form_end'); ?>

	</form>

</div>

<?php do_action('marketengine_after_dispute_form'); ?>