<?php marketengine_get_template('resolution/form/escalate-heading', array('case' => $case)); ?>
<div class="me-escalate-form">
	<?php marketengine_print_notices(); ?>
	<form id="me-escalate-form" method="post" action="<?php echo add_query_arg('action', 'escalate') ?>">
		<div class="me-escalate-box">
			<p class="me-escalate-about"><?php _e("You are about to escalate this dispute.", "enginethemes") ?><br/>
			<?php _e( "Admin will arbitrate based on reports and proofs from both sides.", "enginethemes" ) ?><br/>
			<?php _e("Emails, images, files, etc., all are accepted as proofs.", "enginethemes"); ?></p>
		</div>
		<div class="me-escalate-box">
			<h3><?php _e("Please describe your problem in detail", "enginethemes"); ?></h3>
			<textarea name="post_content" id="escalate-content" cols="30" rows="10"></textarea>
		</div>
		<div class="me-escalate-box">
			<h3><?php _e('Attachments (optional)', 'enginethemes'); ?></h3>
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

		<div class="me-escalate-submit">
			<?php wp_nonce_field('me-escalate_case'); ?>
			<?php wp_nonce_field('marketengine', 'me-dispute-file'); ?>
			<input type="hidden" name="dispute" value="<?php echo $case->ID; ?>" />
			<input type="submit" class="me-escalate-submit-btn" value="<?php _e("Submit", "enginethemes"); ?>">
		</div>
		<a href="<?php echo remove_query_arg( 'action' ); ?>" class="me-backlink">
			&lt; <?php _e("Discard escalate", "enginethemes"); ?>
		</a>
	</form>
</div>