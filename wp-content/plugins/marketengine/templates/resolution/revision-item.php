<li  id="message-<?php echo $message->ID; ?>" class="me-contact-dispute-event <?php echo $message->post_status; ?>">
<?php 
switch ($message->post_status) :
	case 'me-closed':
		$name = get_the_author_meta( 'display_name', $message->sender );
	?>
		<p>			
			<?php printf(__("<b>%s</b> <i>has closed the dispute</i>", "enginethemes"), $name); ?>
		</p>
	<?php
		break;
	case 'me-waiting':
		$name = get_the_author_meta( 'display_name', $message->sender );
	?>
		<p>			
			<?php printf(__("<b>%s</b> <i>has requested to close the dispute</i>", "enginethemes"), $name); ?>
		</p>
	<?php
		break;
	case 'me-escalated':
		$name = get_the_author_meta( 'display_name', $message->sender );
	?>
		<p>			
			<?php printf(__("<b>%s</b> <i>has escalated the dispute to admin.</i>", "enginethemes"), $name); ?>
		</p>
	<?php
		break;

	case 'me-open':
		$name = get_the_author_meta( 'display_name', $message->sender );
	?>
		<p>			
			<?php printf(__("<b>%s</b> <i>has started the dispute.</i>", "enginethemes"), $name); ?>
		</p>
	<?php
		break;
	case 'me-resolved':
	?>
		<p>			
			<?php _e("<b>Admin</b> <i>has resolved the dispute.</i>", "enginethemes"); ?>
		</p>
	<?php
	break;
endswitch;
 ?>
	<span><?php echo date_i18n(get_option('date_format') .' ' . get_option('time_format'), strtotime($message->post_date) ); ?></span>
</li>