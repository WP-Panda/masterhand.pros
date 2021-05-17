<?php
	$comment_ID = $comment->comment_ID;
	$post = get_post($comment->comment_post_ID);
	$user = get_userdata( $post->post_author );
?>

<p><?php _e('Dear Admin', 'enginethemes'); ?></p>
<p><?php _e('There is a new comment on the post', 'enginethemes'); ?> <?php echo $post->post_title; ?></p>
<ul>
<li><?php _e('Author:', 'enginethemes'); ?> <?php echo esc_html( $comment->comment_author ); ?></li>
<li><?php _e('Email:', 'enginethemes'); ?> <?php echo esc_html( $comment->comment_author_email ); ?></li>
<li><?php _e('URL:', 'enginethemes'); ?> <?php echo esc_url( $comment->comment_author_url ); ?></li>
<li><?php _e('Comment:', 'enginethemes'); ?>
	<p><?php echo esc_html( nl2br($comment->comment_content) ); ?></p>
</li>
</ul>

<p><?php _e('You can see all comments on this post here:', 'enginethemes'); ?></p>
<p><?php echo get_permalink($comment->comment_post_ID); ?></p>

<ul>
<li><?php _e('Permalink:', 'enginethemes'); ?> <?php echo admin_url( "comment.php?action=approve&c={$comment_ID}#wpbody-content" ); ?></li>
<li><?php _e('Trash it:', 'enginethemes'); ?> <?php echo admin_url( "comment.php?action=trash&c={$comment_ID}#wpbody-content" ); ?></li>
<li><?php _e('Spam it:', 'enginethemes'); ?> <?php echo admin_url( "comment.php?action=spam&c={$comment_ID}#wpbody-content" ); ?></li>
</ul>

<p><?php _e('Sincerely,', 'enginethemes'); ?></p>