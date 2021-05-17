<?php if(!marketengine_is_activated_user()) : ?>

<div class="me-inactive">
	
	<?php if( marketengine_get_notices() ) : ?>

		<?php marketengine_print_notices(); ?>

	<?php else: ?>
		<div class="me-authen-inactive">
		<p><?php _e("You need to active your account before buy listings.", "enginethemes"); ?></p>

	<?php
		$profile_link = marketengine_get_page_permalink('user_account');
        $activate_email_link = add_query_arg(array( 'resend-confirmation-email' => true, '_wpnonce' => wp_create_nonce('me-resend_confirmation_email') ), $profile_link);
    ?>

        <p><a id="resend-confirmation-email" href="<?php echo $activate_email_link; ?>"><?php _e("Resend activation email.", "enginethemes"); ?></a></p>
		</div>
	<?php endif; ?>

</div>

<?php endif; ?>