<li id="message-<?php echo $message->ID; ?>">
	<a href="<?php echo get_author_posts_url($message->sender); ?>" >
		<span class="me-mgs-author-avatar">
			<?php echo marketengine_get_avatar( $message->sender, '36'); ?>
		</span>
	</a>
	<div class="me-message-author">
		<a class="me-mauthor" href="<?php echo get_author_posts_url($message->sender); ?>" >
			<?php echo get_the_author_meta( 'display_name', $message->sender ); ?>
		</a>
		<?php echo apply_filters( 'the_marketengine_message', $message->post_content ); ?>
		<span><?php echo human_time_diff( strtotime($message->post_date), current_time( 'timestamp' ) ); ?></span>
	</div>
</li>