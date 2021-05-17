<h2 class="dialog-label"><?php esc_html_e( 'Mailster Deactivation', 'mailster' ); ?></h2>

<form id="mailster-deactivation-survey" method="POST" action="<?php echo add_query_arg( 'mailster_deactivation_survey', true ); ?>">
	<?php wp_nonce_field( 'mailster_deactivation_survey', 'mailster_nonce', false ); ?>
	<input type="hidden" name="mailster_surey_extra" value=''>
	<p><?php esc_html_e( 'If you have a moment, please let us know why you are deactivating Mailster. We only use this feedback to improve the plugin.', 'mailster' ); ?></p>
	<div>
		<label><input type="radio" name="mailster_surey_reason" value="It's a temporary deactivation." required> <?php esc_html_e( 'It\'s a temporary deactivation.', 'mailster' ); ?></label>
	</div>
	<div>
		<label><input type="radio" name="mailster_surey_reason" value="I no longer need the plugin." required> <?php esc_html_e( 'I no longer need the plugin.', 'mailster' ); ?></label>
	</div>
	<div>
		<label><input type="radio" name="mailster_surey_reason" value="The plugin didn't work." required> <?php esc_html_e( 'The plugin didn\'t work.', 'mailster' ); ?></label>
		<div class="mailster-survey-extra">
			<p><?php sprintf( esc_html__( 'We\'re sorry about that. Please get in touch with our %s.', 'mailster' ), '<a href="https://evp.to/support">' . esc_html__( 'support', 'mailster' ) . '</a>' ); ?></p>
			<textarea disabled name="mailster_surey_extra" class="widefat" rows="5"></textarea>
		</div>
	</div>
	<div>
		<label><input type="radio" name="mailster_surey_reason" value="The plugin broke my site." required> <?php esc_html_e( 'The plugin broke my site.', 'mailster' ); ?></label>
		<div class="mailster-survey-extra">
			<p><?php sprintf( esc_html__( 'We\'re sorry about that. Please get in touch with our %s.', 'mailster' ), '<a href="https://evp.to/support">' . esc_html__( 'support', 'mailster' ) . '</a>' ); ?></p>
			<textarea disabled name="mailster_surey_extra" class="widefat" rows="5"></textarea>
		</div>
	</div>
	<div>
		<label><input type="radio" name="mailster_surey_reason" value="I found a better plugin." required> <?php esc_html_e( 'I found a better plugin.', 'mailster' ); ?></label>
		<div class="mailster-survey-extra">
			<p><?php esc_html_e( 'What is the name of the plugin?', 'mailster' ); ?></p>
			<textarea disabled name="mailster_surey_extra" class="widefat" rows="5"></textarea>
		</div>
	</div>
	<div>
		<label><input type="radio" name="mailster_surey_reason" value="Other" required> <?php esc_html_e( 'Other', 'mailster' ); ?></label>
		<div class="mailster-survey-extra">
			<p><?php esc_html_e( 'Please describe why you\'re deactivating Mailster.', 'mailster' ); ?></p>
			<textarea disabled name="mailster_surey_extra" class="widefat" rows="5"></textarea>
		</div>
	</div>

	<div class="mailster-delete-data">
		<p>
			<label><input type="checkbox" name="delete_data" value="1"> <?php esc_html_e( 'Would you like to delete all data?', 'mailster' ); ?></label>
		</p>
		<p>
			 <label><input type="checkbox" name="delete_campaigns" value="1" disabled> <?php esc_html_e( 'Delete Campaigns', 'mailster' ); ?></label><br>
			 <label><input type="checkbox" name="delete_capabilities" value="1" disabled> <?php esc_html_e( 'Delete Capabilities', 'mailster' ); ?></label><br>
			 <label><input type="checkbox" name="delete_tables" value="1" disabled> <?php esc_html_e( 'Delete Tables', 'mailster' ); ?></label><br>
			 <label><input type="checkbox" name="delete_options" value="1" disabled> <?php esc_html_e( 'Delete Options', 'mailster' ); ?></label><br>
			 <label><input type="checkbox" name="delete_files" value="1" disabled> <?php esc_html_e( 'Delete Files', 'mailster' ); ?></label>
		</p>
		<p><?php esc_html_e( 'Mailster does not delete any data on plugin deactivation by default. If you like to start with a fresh setup you can check this option and Mailster will remove all campaigns, subscribers, actions and other data in your database.', 'mailster' ); ?><br><strong><?php esc_html_e( 'Note: This will permanently delete all Mailster data from your database.', 'mailster' ); ?></strong>
		</p>
	</div>
</form>
