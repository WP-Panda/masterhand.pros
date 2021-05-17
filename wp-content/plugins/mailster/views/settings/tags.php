<p class="description"><?php printf( esc_html__( 'Tags are placeholder for your newsletter. You can set them anywhere in your newsletter template with the format %s. Custom field tags are individual for each subscriber.', 'mailster' ), '<code>{tagname}</code>' ); ?></p>
<p class="description"><?php printf( esc_html__( 'You can set alternative content with %1$s which will be uses if %2$s is not defined. All unused tags will get removed in the final message', 'mailster' ), '<code>{tagname|alternative content}</code>', '[tagname]' ); ?></p>
		<?php $reserved = array( 'unsub', 'unsublink', 'webversion', 'webversionlink', 'forward', 'forwardlink', 'subject', 'preheader', 'profile', 'profilelink', 'headline', 'content', 'link', 'email', 'emailaddress', 'firstname', 'lastname', 'fullname', 'year', 'month', 'day', 'share', 'tweet', 'hash', 'wp_id', 'status', 'added', 'updated', 'signup', 'confirm', 'ip_signup', 'ip_confirm', 'rating' ); ?>
<p id="reserved-tags" data-tags='["<?php echo implode( '","', $reserved ); ?>"]'><?php esc_html_e( 'reserved tags', 'mailster' ); ?>: <code>{<?php echo implode( '}</code>, <code>{', $reserved ); ?>}</code></p>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-permanent-tags">
		<th scope="row"><?php esc_html_e( 'Permanent Tags', 'mailster' ); ?>:</th>
		<td class="tags">
		<p class="description"><?php esc_html_e( 'These are permanent tags which cannot get deleted. The CAN-SPAM tag is required in many countries.', 'mailster' ); ?> <a href="https://en.wikipedia.org/wiki/CAN-SPAM_Act_of_2003" class="external"><?php esc_html_e( 'Read more', 'mailster' ); ?></a></p>
<?php if ( $tags = mailster_option( 'tags' ) ) : ?>
	<?php foreach ( $tags as $tag => $content ) : ?>
		<div class="tag">
		<span><code>{<?php echo $tag; ?>}</code></span> &#10152;
		<?php if ( 'address' == $tag ) : ?>
		<textarea name="mailster_options[tags][<?php echo esc_attr( $tag ); ?>]" class="regular-text tag-value" rows="5"><?php echo esc_attr( $content ); ?></textarea>
		<?php else : ?>
		<input type="text" name="mailster_options[tags][<?php echo esc_attr( $tag ); ?>]" value="<?php echo esc_attr( $content ); ?>" class="regular-text tag-value">
		<?php endif; ?>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-custom-tags">
		<th scope="row"><?php esc_html_e( 'Custom Tags', 'mailster' ); ?>:</th>
		<td class="tags">
		<p class="description"><?php esc_html_e( 'Add your custom tags here. They work like permanent tags', 'mailster' ); ?></p>
<?php if ( $tags = mailster_option( 'custom_tags' ) ) : ?>
	<?php foreach ( $tags as $tag => $content ) : ?>
		<div class="tag"><span><code>{<?php echo esc_html( $tag ); ?>}</code></span> &#10152; <input type="text" name="mailster_options[custom_tags][<?php echo $tag; ?>]" value="<?php echo esc_attr( $content ); ?>" class="regular-text tag-value"> <a class="tag-remove">&#10005;</a></div>
	<?php endforeach; ?>
<?php endif; ?>

	<input type="button" value="<?php esc_attr_e( 'add', 'mailster' ); ?>" class="button" id="mailster_add_tag">
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-special-tags">
		<th scope="row"><?php esc_html_e( 'Special Tags', 'mailster' ); ?>:</th>
		<td class="customfields">
		<p class="description"><?php esc_html_e( 'Special tags display dynamic content and are equally for all subscribers', 'mailster' ); ?></p>
		<div class="customfield"><span><code>{tweet:username}</code></span> &#10152; <?php printf( esc_html__( 'displays the last tweet from Twitter user [username] (cache it for %s minutes)', 'mailster' ), '<input type="text" name="mailster_options[tweet_cache_time]" value="' . esc_attr( mailster_option( 'tweet_cache_time' ) ) . '" class="small-text">' ); ?></div>
		<p class="description">
			<?php printf( esc_html__( 'To enable the tweet feature you have to create a new %s and insert your credentials', 'mailster' ), '<a href="https://dev.twitter.com/apps/new" class="external">Twitter App</a>' ); ?>
		</p>
		<p>
		<div class="mailster_text">&nbsp;<label><?php esc_html_e( 'Access token', 'mailster' ); ?>:</label> <input type="text" name="mailster_options[twitter_token]" value="<?php echo esc_attr( mailster_option( 'twitter_token' ) ); ?>" class="regular-text" autocomplete="off"></div>
		<div class="mailster_text">&nbsp;<label><?php esc_html_e( 'Access token Secret', 'mailster' ); ?>:</label> <input type="password" name="mailster_options[twitter_token_secret]" value="<?php echo esc_attr( mailster_option( 'twitter_token_secret' ) ); ?>" class="regular-text" autocomplete="off"></div>
		<div class="mailster_text">&nbsp;<label><?php esc_html_e( 'Consumer key', 'mailster' ); ?>:</label> <input type="text" name="mailster_options[twitter_consumer_key]" value="<?php echo esc_attr( mailster_option( 'twitter_consumer_key' ) ); ?>" class="regular-text" autocomplete="off"></div>
		<div class="mailster_text">&nbsp;<label><?php esc_html_e( 'Consumer secret', 'mailster' ); ?>:</label> <input type="password" name="mailster_options[twitter_consumer_secret]" value="<?php echo esc_attr( mailster_option( 'twitter_consumer_secret' ) ); ?>" class="regular-text" autocomplete="off"></div>
		</p>
		<br>
		<div class="customfield"><span><code>{share:twitter}</code></span> &#10152; <?php printf( esc_html__( 'displays %1$s to share the newsletter via %2$s', 'mailster' ), '<img src="' . MAILSTER_URI . '/assets/img/share/share_twitter.png">', 'Twitter' ); ?></div>
		<div class="customfield"><span><code>{share:facebook}</code></span> &#10152; <?php printf( esc_html__( 'displays %1$s to share the newsletter via %2$s', 'mailster' ), '<img src="' . MAILSTER_URI . '/assets/img/share/share_facebook.png">', 'Facebook' ); ?></div>
		<div class="customfield"><span><code>{share:google}</code></span> &#10152; <?php printf( esc_html__( 'displays %1$s to share the newsletter via %2$s', 'mailster' ), '<img src="' . MAILSTER_URI . '/assets/img/share/share_google.png">', 'Google+' ); ?></div>
		<div class="customfield"><span><code>{share:linkedin}</code></span> &#10152; <?php printf( esc_html__( 'displays %1$s to share the newsletter via %2$s', 'mailster' ), '<img src="' . MAILSTER_URI . '/assets/img/share/share_linkedin.png">', 'LinkedIn' ); ?></div>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-dynamic-tags">
		<th scope="row"><?php esc_html_e( 'Dynamic Tags', 'mailster' ); ?></th>
		<td><p class="description"><?php esc_html_e( 'Dynamic tags let you display your posts or pages in a reverse chronicle order. Some examples:', 'mailster' ); ?></p>
		<div class="customfield"><span><code>{post_title:-1}</code></span> &#10152; <?php esc_html_e( 'displays the latest post title', 'mailster' ); ?></div>
		<div class="customfield"><span><code>{page_title:-4}</code></span> &#10152; <?php esc_html_e( 'displays the fourth latest page title', 'mailster' ); ?></div>
		<div class="customfield"><span><code>{post_image:-1}</code></span> &#10152; <?php esc_html_e( 'displays the feature image of the latest posts', 'mailster' ); ?></div>
		<div class="customfield"><span><code>{post_image:-4|23}</code></span> &#10152; <?php esc_html_e( 'displays the feature image of the fourth latest posts. Uses the image with ID 23 if the post doesn\'t have a feature image', 'mailster' ); ?></div>
		<div class="customfield"><span><code>{post_content:-1}</code></span> &#10152; <?php esc_html_e( 'displays the latest posts content', 'mailster' ); ?></div>
		<div class="customfield"><span><code>{post_excerpt:-1}</code></span> &#10152; <?php esc_html_e( 'displays the latest posts excerpt or content if no excerpt is defined', 'mailster' ); ?></div>
		<div class="customfield"><span><code>{post_date:-1}</code></span> &#10152; <?php esc_html_e( 'displays the latest posts date', 'mailster' ); ?></div>
		<p class="description"><?php esc_html_e( 'you can also use absolute values', 'mailster' ); ?></p>
		<div class="customfield"><span><code>{post_title:23}</code></span> &#10152; <?php esc_html_e( 'displays the post title of post ID 23', 'mailster' ); ?></div>
		<div class="customfield"><span><code>{post_link:15}</code></span> &#10152; <?php esc_html_e( 'displays the permalink of post ID 15', 'mailster' ); ?></div>
		<p class="description"><?php esc_html_e( 'Instead of "post_" and "page_" you can use custom post types too', 'mailster' ); ?></p>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-image-fallback">
		<th scope="row"><?php esc_html_e( 'Image Fallback', 'mailster' ); ?></th>
		<td><label>
		<p class="description"><?php esc_html_e( 'Use a fallback for dynamic image tags if the image doesn\'t exist.', 'mailster' ); ?></p>
		<?php mailster( 'helper' )->media_editor_link( mailster_option( 'fallback_image', 0 ), 'mailster_options[fallback_image]' ); ?>
		</td>
	</tr>
</table>
