<table class="form-table">
	<tr valign="top" class="settings-row settings-row-newsletter-homepage">
		<?php $mailster_homepage = mailster_option( 'homepage' ); ?>
		<th scope="row"><?php esc_html_e( 'Newsletter Homepage', 'mailster' ); ?></th>
		<td>
		<?php if ( array_sum( (array) wp_count_posts( 'page' ) ) > 100 ) : ?>
			<p><?php esc_html_e( 'Page ID:', 'mailster' ); ?> <input type="text" name="mailster_options[homepage]" value="<?php echo $mailster_homepage; ?>" class="small-text"> <span class="description"><?php esc_html_e( 'Find your Page ID in the address bar of the edit screen of this page.', 'mailster' ); ?></span></p>
		<?php else : ?>
			<?php
			$pages = get_posts(
				array(
					'post_type'      => 'page',
					'post_status'    => 'publish,private,draft',
					'posts_per_page' => -1,
				)
			);
			?>
			<select name="mailster_options[homepage]" class="postform">
				<option value="0"><?php esc_html_e( 'Choose', 'mailster' ); ?></option>
			<?php foreach ( $pages as $page ) { ?>
				<option value="<?php echo $page->ID; ?>"<?php selected( $mailster_homepage, $page->ID ); ?>>
				<?php
				echo esc_html( $page->post_title );
				if ( $page->post_status != 'publish' ) {
					echo ' (' . $wp_post_statuses[ $page->post_status ]->label . ')';
				}
				?>
				</option>
			<?php } ?>
			</select>
		<?php endif; ?>

		<?php if ( $mailster_homepage ) : ?>
			<span class="description">
				<a href="post.php?post=<?php echo (int) $mailster_homepage; ?>&action=edit"><?php esc_html_e( 'edit', 'mailster' ); ?></a>
				<?php esc_html_e( 'or', 'mailster' ); ?>
				<a href="<?php echo get_permalink( $mailster_homepage ); ?>" class="external"><?php esc_html_e( 'visit', 'mailster' ); ?></a>
			</span>
		<?php else : ?>
			<span class="description"><a href="<?php echo add_query_arg( 'mailster_create_homepage', wp_create_nonce( 'mailster_create_homepage' ), admin_url() ); ?>"><?php esc_html_e( 'create it right now', 'mailster' ); ?></a>
			</span>
		<?php endif; ?>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-search-engine-visibility">
		<th scope="row"><?php esc_html_e( 'Search Engine Visibility', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[frontpage_public]" value=""><input type="checkbox" name="mailster_options[frontpage_public]" value="1" <?php checked( mailster_option( 'frontpage_public' ) ); ?>> <?php esc_html_e( 'Discourage search engines from indexing your campaigns', 'mailster' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-webversion-bar">
		<th scope="row"><?php esc_html_e( 'Webversion Bar', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[webversion_bar]" value=""><input type="checkbox" class="webversion-bar-checkbox" name="mailster_options[webversion_bar]" value="1" <?php checked( mailster_option( 'webversion_bar' ) ); ?>> <?php esc_html_e( 'Show the top bar on the web version', 'mailster' ); ?></label>
		</td>
	</tr>
</table>
<div id="webversion-bar-options"<?php echo ! mailster_option( 'webversion_bar' ) ? ' style="display:none"' : ''; ?>>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-pagination">
		<th scope="row"><?php esc_html_e( 'Pagination', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[frontpage_pagination]" value=""><input type="checkbox" name="mailster_options[frontpage_pagination]" value="1" <?php checked( mailster_option( 'frontpage_pagination' ) ); ?>> <?php esc_html_e( 'Allow users to view the next/last newsletters', 'mailster' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-share-button">
		<th scope="row"><?php esc_html_e( 'Share Button', 'mailster' ); ?></th>
		<td><label><input type="hidden" name="mailster_options[share_button]" value=""><input type="checkbox" name="mailster_options[share_button]" value="1" <?php checked( mailster_option( 'share_button' ) ); ?>> <?php esc_html_e( 'Offer share option for your customers', 'mailster' ); ?></label>
		</td>
	</tr>
	<tr valign="top" class="settings-row settings-row-services">
		<th scope="row"><?php esc_html_e( 'Services', 'mailster' ); ?></th>
		<td><ul class="frontpage-social-services">
		<?php

		$social_services = mailster( 'helper' )->social_services();

		$services = mailster_option( 'share_services', array() );
		?>
		<?php foreach ( $social_services as $service => $data ) : ?>
			<li class="<?php echo $service; ?>"><label><input type="checkbox" name="mailster_options[share_services][]" value="<?php echo esc_attr( $service ); ?>" <?php checked( in_array( $service, $services ) ); ?>> <?php echo $data['name']; ?></label></li>
		<?php endforeach; ?>
		</ul></td>
	</tr>
</table>
</div>
<table class="form-table">
	<tr valign="top" class="settings-row settings-row-campaign-slug">
		<th scope="row"><?php esc_html_e( 'Campaign slug', 'mailster' ); ?></th>
		<td><p>
		<?php if ( mailster( 'helper' )->using_permalinks() ) : ?>
		<span class="description"><?php echo get_bloginfo( 'url' ); ?>/</span><input type="text" name="mailster_options[slug]" value="<?php echo esc_attr( mailster_option( 'slug', 'newsletter' ) ); ?>" class="small-text" style="width:80px"><span class="description">/my-campaign</span><br><span class="description"><?php esc_html_e( 'changing the slug may cause broken links in previous sent campaigns!', 'mailster' ); ?></span>
		<?php else : ?>
		<span class="description"><?php printf( esc_html_x( 'Define a %s to enable custom slugs', 'Campaign slug', 'mailster' ), '<a href="options-permalink.php">' . esc_html__( 'Permalink Structure', 'mailster' ) . '</a>' ); ?></span>
		<input type="hidden" name="mailster_options[slug]" value="<?php echo esc_attr( mailster_option( 'slug', 'newsletter' ) ); ?>">
		<?php endif; ?>
		</p>
		</td>
	</tr>
	<?php
	$slugs = mailster_option(
		'slugs',
		array(
			'confirm'     => 'confirm',
			'subscribe'   => 'subscribe',
			'unsubscribe' => 'unsubscribe',
			'profile'     => 'profile',
		)
	);

	if ( mailster( 'helper' )->using_permalinks() && mailster_option( 'homepage' ) ) :
		$homepage = trailingslashit( get_permalink( mailster_option( 'homepage' ) ) );
		?>
		<tr valign="top" class="settings-row settings-row-homepage-slugs">
			<th scope="row"><?php esc_html_e( 'Homepage slugs', 'mailster' ); ?></th>
			<td class="homepage-slugs">
			<p>
			<label><?php esc_html_e( 'Confirm Slug', 'mailster' ); ?>:</label><br>
				<span>
					<?php echo $homepage; ?><strong><?php echo $slugs['confirm']; ?></strong>/
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'mailster' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $homepage; ?><input type="text" name="mailster_options[slugs][confirm]" value="<?php echo esc_attr( $slugs['confirm'] ); ?>" class="small-text">/
				</span>
			</p>
			<p>
			<label><?php esc_html_e( 'Subscribe Slug', 'mailster' ); ?>:</label><br>
				<span>
					<?php echo $homepage; ?><strong><?php echo $slugs['subscribe']; ?></strong>/
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'mailster' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $homepage; ?><input type="text" name="mailster_options[slugs][subscribe]" value="<?php echo esc_attr( $slugs['subscribe'] ); ?>" class="small-text">/
				</span>
			</p>
			<p>
			<label><?php esc_html_e( 'Unsubscribe Slug', 'mailster' ); ?>:</label><br>
				<span>
					<a href="<?php echo $homepage . esc_attr( $slugs['unsubscribe'] ); ?>" class="external"><?php echo $homepage; ?><strong><?php echo $slugs['unsubscribe']; ?></strong>/</a>
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'mailster' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $homepage; ?><input type="text" name="mailster_options[slugs][unsubscribe]" value="<?php echo esc_attr( $slugs['unsubscribe'] ); ?>" class="small-text">/
				</span>
			</p>
			<p>
			<label><?php esc_html_e( 'Profile Slug', 'mailster' ); ?>:</label><br>
				<span>
					<a href="<?php echo $homepage . esc_attr( $slugs['profile'] ); ?>" class="external"><?php echo $homepage; ?><strong><?php echo $slugs['profile']; ?></strong>/</a>
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'mailster' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $homepage; ?><input type="text" name="mailster_options[slugs][profile]" value="<?php echo esc_attr( $slugs['profile'] ); ?>" class="small-text">/
				</span>
			</p>
			</td>
		</tr>
	<?php else : ?>

		<input type="hidden" name="mailster_options[slugs][confirm]" value="<?php echo esc_attr( $slugs['confirm'] ); ?>">
		<input type="hidden" name="mailster_options[slugs][subscribe]" value="<?php echo esc_attr( $slugs['subscribe'] ); ?>">
		<input type="hidden" name="mailster_options[slugs][unsubscribe]" value="<?php echo esc_attr( $slugs['unsubscribe'] ); ?>">
		<input type="hidden" name="mailster_options[slugs][profile]" value="<?php echo esc_attr( $slugs['profile'] ); ?>">

	<?php endif; ?>

	<?php if ( mailster( 'helper' )->using_permalinks() ) : ?>
		<tr valign="top" class="settings-row settings-row-archive">
			<th scope="row"><?php esc_html_e( 'Archive', 'mailster' ); ?></th>
			<td class="homepage-slugs"><p><label><input type="hidden" name="mailster_options[hasarchive]" value=""><input type="checkbox" name="mailster_options[hasarchive]" class="has-archive-check" value="1" <?php checked( mailster_option( 'hasarchive' ) ); ?>> <?php esc_html_e( 'enable archive function to display your newsletters in a reverse chronological order', 'mailster' ); ?></label>
				</p>
			<div class="archive-slug"<?php echo ! mailster_option( 'hasarchive' ) ? ' style="display:none"' : ''; ?>>
				<p>
				<label><?php esc_html_e( 'Archive Slug', 'mailster' ); ?>:</label><br>
				<?php
					$homepage = home_url( '/' );
					$slug     = mailster_option( 'archive_slug', 'newsletter' );
				?>
				<span>
					<a href="<?php echo $homepage . esc_attr( $slug ); ?>" class="external"><?php echo $homepage; ?><strong><?php echo $slug; ?></strong>/</a>
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'mailster' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo esc_html( $homepage ); ?><input type="text" name="mailster_options[archive_slug]" value="<?php echo esc_attr( $slug ); ?>" class="small-text">/
				</span>
				</p>
				<p>
					<label>
					<?php esc_html_e( 'show only', 'mailster' ); ?>:
					</label>
					<?php $archive_types = mailster_option( 'archive_types', array( 'finished', 'active' ) ); ?>
					<label> <input type="checkbox" name="mailster_options[archive_types][]" value="finished" <?php checked( in_array( 'finished', $archive_types ) ); ?>> <?php esc_html_e( 'finished', 'mailster' ); ?> </label>
					<label> <input type="checkbox" name="mailster_options[archive_types][]" value="active" <?php checked( in_array( 'active', $archive_types ) ); ?>> <?php esc_html_e( 'active', 'mailster' ); ?> </label>
					<label> <input type="checkbox" name="mailster_options[archive_types][]" value="paused" <?php checked( in_array( 'paused', $archive_types ) ); ?>> <?php esc_html_e( 'paused', 'mailster' ); ?> </label>
					<label> <input type="checkbox" name="mailster_options[archive_types][]" value="queued" <?php checked( in_array( 'queued', $archive_types ) ); ?>> <?php esc_html_e( 'queued', 'mailster' ); ?> </label>
				</p>
			</div>
			</td>
		</tr>
	<?php else : ?>
		<input type="hidden" name="mailster_options[hasarchive]" value="<?php echo esc_attr( mailster_option( 'hasarchive' ) ); ?>">
		<input type="hidden" name="mailster_options[archive_slug]" value="<?php echo esc_attr( mailster_option( 'archive_slug', 'newsletter' ) ); ?>">
	<?php endif; ?>

</table>
