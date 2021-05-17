<?php

$now = time();

$sent = $this->get_sent( $post->ID );

?>

<div class="submitbox" id="submitpost">

<?php do_action( 'post_submitbox_start' ); ?>
<div id="preview-action">
<input type="hidden" name="wp-preview" id="wp-preview" value="" />
</div>
<div class="clear"></div>

<div>
	<div id="misc-publishing-actions">

			<span class="spinner ajax-loading" id="ajax-loading"></span>

			<p class="clear" id="webversion-field" title="<?php esc_attr_e( 'Offer a public web version for this campaign. If disabled this campaign will be marked as "private"', 'mailster' ); ?>">
				<label for="use_webversion"><input type="checkbox" id="use_webversion" name="mailster_data[webversion]" value="1" <?php checked( $this->post_data['webversion'] ); ?>> <?php esc_html_e( 'Web version', 'mailster' ); ?></label>
			</p>

			<p class="clear" id="password-field" title="<?php esc_attr_e( 'Protect the web version with a password.', 'mailster' ); ?>">
				<label for="use_pwd"><input type="checkbox" name="use_pwd" id="use_pwd" value="1" <?php checked( ! ! $post->post_password ); ?>> <?php esc_html_e( 'Password', 'mailster' ); ?></label>
				<span id="password-wrap"<?php echo ! $post->post_password ? ' style="display:none;"' : ''; ?>>
					<input type="hidden" name="post_password" value="">
					<input type="text" class="widefat" name="post_password" id="post_password" value="<?php echo $post->post_password; ?>" maxlength="20"><br>
					<span class="description"><?php esc_html_e( 'Protect the web version with a password.', 'mailster' ); ?></span>
				</span>
			</p>

			<div id="delete-action">
				<?php if ( current_user_can( 'delete_post', $post->ID ) ) : ?>
				<p class="clear" id="delete-field">
					<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>">
						<?php ( ! EMPTY_TRASH_DAYS ) ? esc_html_e( 'Delete Permanently', 'mailster' ) : esc_html_e( 'Move to Trash', 'mailster' ); ?>
					</a>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<div id="major-publishing-actions">
			<div id="publishing-action">
			<?php if ( 'finished' == $post->post_status ) : ?>

				<?php if ( $can_publish ) : ?>
						<input name="save" type="submit" class="button-primary" id="publish" tabindex="15" accesskey="p" value="<?php esc_attr_e( 'Update', 'mailster' ); ?>" />
				<?php endif; ?>

				<?php if ( ( current_user_can( 'duplicate_newsletters' ) && get_current_user_id() == $post->post_author ) || current_user_can( 'duplicate_others_newsletters' ) ) : ?>
				<a class="button duplicate" href="edit.php?post_type=newsletter&duplicate=<?php echo $post->ID; ?>&edit=1&_wpnonce=<?php echo wp_create_nonce( 'mailster_duplicate_nonce' ); ?>"><?php esc_html_e( 'Duplicate', 'mailster' ); ?></a>
				<?php endif; ?>


			<?php elseif ( ! in_array( $post->post_status, array( 'publish', 'future', 'private', 'paused' ) ) || 0 == $post->ID ) : ?>

				<?php if ( isset( $_GET['showstats'] ) ) : ?>

					<?php if ( $can_publish && in_array( $post->post_status, array( 'paused', 'autoresponder' ) ) ) : ?>
					<a class="button" href="post.php?post=<?php echo $post->ID; ?>&action=edit"><?php esc_html_e( 'Edit', 'mailster' ); ?></a>
					<?php else : ?>
					<a class="button pause" href="edit.php?post_type=newsletter&pause=<?php echo $post->ID; ?>&edit=1&_wpnonce=<?php echo wp_create_nonce( 'mailster_pause_nonce' ); ?>"><?php esc_html_e( 'Pause', 'mailster' ); ?></a>
					<?php endif; ?>

				<?php elseif ( $can_publish ) : ?>

					<?php if ( 'active' == $post->post_status ) : ?>

						<?php if ( $can_publish ) : ?>
							<input name="save" type="submit" class="button-primary" id="publish" tabindex="15" accesskey="p" value="<?php esc_attr_e( 'Update', 'mailster' ); ?>" />
						<?php endif; ?>

						<a class="button pause" href="edit.php?post_type=newsletter&pause=<?php echo $post->ID; ?>&edit=1&_wpnonce=<?php echo wp_create_nonce( 'mailster_pause_nonce' ); ?>"><?php esc_html_e( 'Pause', 'mailster' ); ?></a>

					<?php elseif ( 'queued' == $post->post_status ) : ?>

						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'mailster' ); ?>" />
						<?php submit_button( esc_html__( 'Save', 'mailster' ), 'primary', 'publish', false, array( 'accesskey' => 'p' ) ); ?>

						<?php if ( $this->post_data['timestamp'] < $now && in_array( $post->post_status, array( 'paused' ) ) && $sent ) : ?>
							<input name="resume" type="submit" value="<?php esc_attr_e( 'Resume', 'mailster' ); ?>" class="button resume-button" title="<?php esc_attr_e( 'Save and resume campaign', 'mailster' ); ?>" />
						<?php else : ?>
							<input name="sendnow" type="submit" value="<?php esc_attr_e( 'Send now', 'mailster' ); ?>" class="button sendnow-button" title=" <?php esc_attr_e( 'Save and send campaign', 'mailster' ); ?>" />
						<?php endif; ?>

					<?php elseif ( 'autoresponder' == $post->post_status ) : ?>

						<?php if ( $queuecount = mailster( 'queue' )->get_job_count( $post->ID, false ) ) : ?>

						<p><label title="<?php esc_attr_e( 'Removes all pending entries from this campaign in the queue.', 'mailster' ); ?>"><input type="checkbox" name="clearqueue" value="1" checked> <?php esc_html_e( 'Clear Queue of this auto responder.', 'mailster' ); ?></label></p>

						<?php endif; ?>

						<?php
						$url = add_query_arg(
							array(
								'post'      => $post->ID,
								'action'    => 'edit',
								'showstats' => 1,
							),
							''
						);
						?>

						<input name="save" type="submit" class="button-primary" id="publish" tabindex="15" accesskey="p" value="<?php esc_attr_e( 'Update', 'mailster' ); ?>" />
						<a href="<?php echo esc_url( $url ); ?>" class="button statistics"><?php esc_html_e( 'Statistic', 'mailster' ); ?></a>

					<?php elseif ( in_array( $post->post_status, array( 'draft', 'auto-draft' ) ) ) : ?>

						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'mailster' ); ?>" />
						<?php submit_button( esc_html__( 'Save as draft', 'mailster' ), '', 'draft', false, array( 'accesskey' => 'd' ) ); ?>
						<?php submit_button( esc_html__( 'Save', 'mailster' ), 'primary', 'publish', false, array( 'accesskey' => 'p' ) ); ?>

					<?php elseif ( in_array( $post->post_status, array( 'pending' ) ) ) : ?>

						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'mailster' ); ?>" />
						<?php submit_button( esc_html__( 'Save as draft', 'mailster' ), '', 'draft', false, array( 'accesskey' => 'd' ) ); ?>
						<?php submit_button( esc_html__( 'Confirm', 'mailster' ), 'primary', 'publish', false, array( 'accesskey' => 'p' ) ); ?>

					<?php endif; ?>

				<?php else : ?>

					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Submit for Review', 'mailster' ); ?>" />
					<?php submit_button( esc_html__( 'Submit for Review', 'mailster' ), 'primary', 'publish', false, array( 'accesskey' => 'p' ) ); ?>

			<?php endif; ?>

		<?php else : ?>

				<?php if ( ! isset( $_GET['showstats'] ) ) : ?>
				<input name="save" type="submit" class="button-primary" id="publish" tabindex="15" accesskey="p" value="<?php esc_attr_e( 'Update', 'mailster' ); ?>" />

					<?php if ( $can_publish && in_array( $post->post_status, array( 'paused', 'queued' ) ) ) : ?>

						<?php if ( in_array( $post->post_status, array( 'paused' ) ) && $sent ) : ?>
							<input name="resume" type="submit" value="<?php esc_attr_e( 'Resume', 'mailster' ); ?>" class="button resume-button" title="<?php esc_attr_e( 'Save and resume campaign', 'mailster' ); ?>" />
						<?php else : ?>
							<input name="sendnow" type="submit" value="<?php esc_attr_e( 'Send now', 'mailster' ); ?>" class="button sendnow-button" title=" <?php esc_attr_e( 'Save and send campaign', 'mailster' ); ?>" />
						<?php endif; ?>

						<?php
						$url = add_query_arg(
							array(
								'post'      => $post->ID,
								'action'    => 'edit',
								'showstats' => 1,
							),
							''
						);
						?>

						<a href="<?php echo esc_url( $url ); ?>" class="button statistics"><?php esc_html_e( 'Statistic', 'mailster' ); ?></a>
					<?php endif; ?>

				<?php else : ?>

					<p class="clear">
						<?php
						$url = add_query_arg(
							array(
								'post'   => $post->ID,
								'action' => 'edit',
							),
							''
						);
						?>
						<a href="<?php echo esc_url( $url ); ?>" class="button statistics edit"><?php esc_html_e( 'Edit', 'mailster' ); ?></a>
						<?php if ( $sent ) : ?>
							<a class="button" href="<?php echo admin_url( 'edit.php?post_type=newsletter&resume=' . $post->ID . '&edit=1&_wpnonce=' . wp_create_nonce( 'mailster_start_nonce' ) ); ?>" title="<?php esc_attr_e( 'Resume Campaign', 'mailster' ); ?>"><?php esc_html_e( 'Resume', 'mailster' ); ?></a>
						<?php else : ?>
							<a class="button" href="<?php echo admin_url( 'edit.php?post_type=newsletter&start=' . $post->ID . '&edit=1&_wpnonce=' . wp_create_nonce( 'mailster_start_nonce' ) ); ?>" title="<?php esc_attr_e( 'Start Campaign', 'mailster' ); ?>"><?php esc_html_e( 'Send now', 'mailster' ); ?></a>
						<?php endif; ?>
					</p>

				<?php endif; ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update', 'mailster' ); ?>" />
			<?php endif; ?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>

</div>
