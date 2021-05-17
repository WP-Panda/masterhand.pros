<?php 
$message = marketengine_get_message(); 
$new_message = marketengine_get_message_meta($message->ID, '_me_recevier_new_message', true);
$inquiry_page = marketengine_get_page_permalink('inquiry');
?>

<li <?php if($message->ID == $current_inquiry) {echo 'class="active"';} ?>>
	<a href="<?php echo add_query_arg('inquiry_id', $message->ID, $inquiry_page ); ?>">
		<span class="me-user-avatar">
			<?php echo marketengine_get_avatar( $message->sender, 36); ?>
		</span>
		<span class="me-contact-author">
			<span><?php echo esc_html( get_the_author_meta( 'display_name', $message->sender ) ); ?></span>

			<?php if($new_message) : ?>
				<span class="me-message-count"><?php echo esc_html( $new_message ); ?></span>
			<?php endif; ?>

		</span>
	</a>
</li>
