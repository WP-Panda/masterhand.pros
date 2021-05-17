<?php if ( current_user_can( 'mailster_edit_subscribers' ) && $subscriber = $this->get_by_wpid( $user->ID, true ) ) : ?>

	<h3>Mailster</h3>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><label for="user_login"><?php esc_html_e( 'Profile', 'mailster' ); ?></label></th>
			<td>
				<a class="button" href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&ID=' . $subscriber->ID ); ?>">
					<?php IS_PROFILE_PAGE ? esc_html_e( 'Edit my Mailster Profile', 'mailster' ) : esc_html_e( 'Edit Users Mailster Profile', 'mailster' ); ?>
				</a>
			</td>
		</tr>
	</table>

<?php elseif ( current_user_can( 'mailster_add_subscribers' ) ) : ?>

	<h3>Mailster</h3>
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><label for="user_login"><?php esc_html_e( 'Create', 'mailster' ); ?></label></th>
			<td>
				<a class="button" href="<?php echo admin_url( 'edit.php?post_type=newsletter&page=mailster_subscribers&new&wp_user=' . $user->ID . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) ); ?>">
					<?php IS_PROFILE_PAGE ? esc_html_e( 'Create Mailster Subscriber', 'mailster' ) : esc_html_e( 'Create Mailster Subscriber', 'mailster' ); ?>
				</a>
			</td>
		</tr>
	</table>

<?php endif; ?>
