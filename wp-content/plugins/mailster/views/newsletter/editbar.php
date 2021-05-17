<div id="editbar">
	<a class="cancel top-cancel" href="#">&#10005;</a>
	<h4 class="editbar-title"></h4><span class="spinner" id="editbar-ajax-loading"></span>

		<div class="conditions">
			<span class="condition-labels">
				<span class="condition-if">if</span>
				<span class="condition-elseif">elseif</span>
				<span class="condition-else">else</span>
			</span>
		<?php

		$fields = array(
			'email'     => mailster_text( 'email' ),
			'firstname' => mailster_text( 'firstname' ),
			'lastname'  => mailster_text( 'lastname' ),
		);

		$customfields = mailster()->get_custom_fields();
		foreach ( $customfields as $field => $data ) {
			$fields[ $field ] = $data['name'];
		}
		$operators = array(
			'is'           => esc_html__( 'is', 'mailster' ),
			'is_not'       => esc_html__( 'is not', 'mailster' ),
			'contains'     => esc_html__( 'contains', 'mailster' ),
			'contains_not' => esc_html__( 'contains not', 'mailster' ),
			'begin_with'   => esc_html__( 'begins with', 'mailster' ),
			'end_with'     => esc_html__( 'ends with', 'mailster' ),
			'is_greater'   => esc_html__( 'is greater', 'mailster' ),
			'is_smaller'   => esc_html__( 'is smaller', 'mailster' ),
			'pattern'      => esc_html__( 'match regex pattern', 'mailster' ),
			'not_pattern'  => esc_html__( 'does not match regex pattern', 'mailster' ),
		);

		?>
		<select class="condition-fields">
		<?php
		foreach ( $fields as $key => $name ) {
			echo '<option value="' . $key . '">' . $name . '</option>';
		}
		?>
		</select>
		<select class="condition-operators">
		<?php
		foreach ( $operators as $key => $name ) {
			echo '<option value="' . $key . '">' . $name . '</option>';
		}
		?>
		</select>
		<input class="condition-value" type="text" value="" class="widefat">
		</div>

		<div class="editbar-types">

		<div class="type single">
			<div class="conditinal-area-wrap">
				<div class="conditinal-area">
					<div class="type-input"><input type="text" class="input live widefat" value=""></div>
				</div>
			</div>
			<div class="clear clearfix">
				<a class="single-link-content" href="#"><?php esc_html_e( 'convert to link', 'mailster' ); ?></a> |
				<a class="replace-image" href="#"><?php esc_html_e( 'replace with image', 'mailster' ); ?></a>
			</div>
			<div id="single-link">
				<div class="clearfix">
						<label class="block"><div class="left"><?php esc_html_e( 'Link', 'mailster' ); ?></div><div class="right"><input type="text" class="input singlelink" value="" placeholder="<?php esc_attr_e( 'insert URL', 'mailster' ); ?>"></div></label>
				</div>
				<div class="link-wrap">
					<div class="postlist">
					</div>
				</div>
			</div>
		</div>

		<div class="type btn">

			<div id="button-type-bar" class="nav-tab-wrapper hide-if-no-js">
				<a class="nav-tab" href="#text_button" data-type="dynamic" aria-label="<?php esc_attr_e( 'Text Button', 'mailster' ); ?>"><?php esc_html_e( 'Text Button', 'mailster' ); ?></a>
				<a class="nav-tab nav-tab-active" href="#image_button" aria-label="<?php esc_attr_e( 'Image Button', 'mailster' ); ?>"><?php esc_html_e( 'Image Button', 'mailster' ); ?></a>
			</div>
			<div id="image_button" class="tab">
			<?php $this->templateobj->buttons(); ?>
			<div class="clearfix">
					<label class="block"><div class="left"><?php esc_html_e( 'Alt Text', 'mailster' ); ?></div><div class="right"><input type="text" class="input buttonalt" value="" placeholder="<?php esc_attr_e( 'image description', 'mailster' ); ?>" aria-label="<?php esc_attr_e( 'Alternative Text', 'mailster' ); ?>"></div></label>
			</div>
			</div>
			<div id="text_button" class="tab" style="display:none">
			<div class="clearfix">
					<label class="block"><div class="left"><?php esc_html_e( 'Button Label', 'mailster' ); ?></div><div class="right"><input type="text" class="input buttonlabel" value="" placeholder="<?php esc_attr_e( 'button label', 'mailster' ); ?>" aria-label="<?php esc_attr_e( 'Button Label', 'mailster' ); ?>"></div></label>
			</div>
			</div>

			<div class="clearfix">
					<label class="block"><div class="left"><?php esc_html_e( 'Link Button', 'mailster' ); ?> <span class="description">(<?php esc_html_e( 'required', 'mailster' ); ?>)</span></div><div class="right"><input type="text" class="input buttonlink" value="" placeholder="<?php esc_attr_e( 'insert URL', 'mailster' ); ?>" aria-label="<?php esc_attr_e( 'Link of the button', 'mailster' ); ?>"></div></label>
			</div>
			<div class="link-wrap">
				<div class="postlist">
				</div>
			</div>

		</div>

		<div class="type multi">
<?php

	add_filter(
		'quicktags_settings',
		function( $qtint, $editor_id ) {
			$qtint['buttons'] = apply_filters( 'mymail_editor_quicktags', apply_filters( 'mailster_editor_quicktags', 'strong,em,link,block,del,img,ul,ol,li,spell,close' ) );
			return $qtint;

		},
		99,
		2
	);

	$toolbar1 = (string) apply_filters( 'mymail_editor_toolbar1', apply_filters( 'mailster_editor_toolbar1', 'bold,italic,underline,strikethrough,|,mailster_mce_button,|,bullist,numlist,|,alignleft,aligncenter,alignright,alignjustify,|,forecolor,backcolor,|,undo,redo,|,link,unlink,|,removeformat' ) );
	$toolbar2 = (string) apply_filters( 'mymail_editor_toolbar2', apply_filters( 'mailster_editor_toolbar2', '' ) );
	$toolbar3 = (string) apply_filters( 'mymail_editor_toolbar3', apply_filters( 'mailster_editor_toolbar3', '' ) );

	if ( ( $toolbar2 || $toolbar3 ) && false === strpos( $toolbar1, 'wp_adv' ) ) {
		$toolbar1 .= ',|,wp_adv';
	}

	$editor_height = 295;

	$usersettings = get_all_user_settings();
	if ( isset( $usersettings['hidetb'] ) && $usersettings['hidetb'] ) {
		if ( $toolbar2 ) {
			$editor_height -= 30;
		}
		if ( $toolbar3 ) {
			$editor_height -= 60;
		}
	}


	wp_editor(
		'',
		'mailster-editor',
		array(
			'wpautop'           => false,
			'remove_linebreaks' => false,
			'media_buttons'     => false,
			'textarea_rows'     => 18,
			'teeny'             => false,
			'quicktags'         => true,
			'editor_height'     => $editor_height,
			'tinymce'           => array(
				'theme_advanced_buttons1' => $toolbar1,
				'theme_advanced_buttons2' => $toolbar2,
				'theme_advanced_buttons3' => $toolbar3,
				'toolbar1'                => $toolbar1,
				'toolbar2'                => $toolbar2,
				'toolbar3'                => $toolbar3,
				'apply_source_formatting' => true,
				'content_css'             => MAILSTER_URI . 'assets/css/tinymce-style.css?v=' . MAILSTER_VERSION,
			),
		)
	);
	?>
		</div>

		<div class="type img">
			<div class="imagecontentwrap">
				<div class="left">
					<p><input type="number" class="imagewidth" aria-label="<?php esc_attr_e( 'Image width', 'mailster' ); ?>">&times;<input type="number" class="imageheight" aria-label="<?php esc_attr_e( 'Image height', 'mailster' ); ?>">px
					<label class="imagecroplabel" title="<?php esc_attr_e( 'Toggle Crop', 'mailster' ); ?>"><input type="checkbox" class="imagecrop" aria-label="<?php esc_attr_e( 'Toggle crop option', 'mailster' ); ?>"><span class="mailster-icon"></span></label>
					</p>
					<div class="imagewrap">
					<img src="" alt="" class="imagepreview">
					</div>
				</div>
				<div class="right">
					<p class="image-search-wrap">
						<label><input type="text" class="widefat" id="image-search" placeholder="<?php esc_attr_e( 'Search for images', 'mailster' ); ?>&hellip;" autocomplete="off" aria-label="<?php esc_attr_e( 'Search for images', 'mailster' ); ?>"></label>
					</p>
					<p class="image-search-type-wrap">
						<label><input type="radio" name="image-search-type" value="media" checked aria-label="<?php esc_attr_e( 'Media Library', 'mailster' ); ?>"> <?php esc_html_e( 'Media Library', 'mailster' ); ?> </label>
						<label><input type="radio" name="image-search-type" value="unsplash" aria-label="Unsplash"> Unsplash </label>
					</p>
					<div class="imagelist">
					</div>
					<p>
						<a class="button button-small add_image"  aria-label="<?php esc_attr_e( 'Media Manager', 'mailster' ); ?>" aria-role="button"><?php esc_html_e( 'Media Manager', 'mailster' ); ?></a>
						<a class="button button-small reload" aria-label="<?php esc_attr_e( 'Reload', 'mailster' ); ?>" aria-role="button"><?php esc_html_e( 'Reload', 'mailster' ); ?></a>
						<a class="button button-small add_image_url" aria-label="<?php esc_attr_e( 'Insert from URL', 'mailster' ); ?>" aria-role="button"><?php esc_html_e( 'Insert from URL', 'mailster' ); ?></a>
					</p>
				</div>
			<br class="clear">
			</div>
			<div class="clearfix">
				<div class="imageurl-popup">
					<label class="block"><div class="left"><?php esc_html_e( 'Image URL', 'mailster' ); ?></div><div class="right"><input type="text" class="input imageurl" value="" placeholder="https://example.com/image.jpg" aria-label="<?php esc_attr_e( 'Image URL', 'mailster' ); ?>"></div></label>
				</div>
					<label class="block"><div class="left"><?php esc_html_e( 'Alt Text', 'mailster' ); ?></div><div class="right"><input type="text" class="input imagealt" value="" placeholder="<?php esc_attr_e( 'image description', 'mailster' ); ?>" aria-label="<?php esc_attr_e( 'Alternative Text', 'mailster' ); ?>"></div></label>
					<label class="block"><div class="left"><?php esc_html_e( 'Link image to the this URL', 'mailster' ); ?></div><div class="right"><input type="text" class="input imagelink" value="" placeholder="<?php esc_attr_e( 'insert URL', 'mailster' ); ?>" aria-label="<?php esc_attr_e( 'insert URL', 'mailster' ); ?>"></div></label>
					<input type="hidden" class="input orgimageurl" value="">
			</div>
			<br class="clear">
		</div>

		<div class="type auto">

			<p class="editbarposition" title="<?php esc_attr_e( 'The area in which content gets placed.', 'mailster' ); ?>"></p>

			<div id="embedoption-bar" class="nav-tab-wrapper hide-if-no-js">
				<a class="nav-tab nav-tab-active" href="#static_embed_options" data-type="static"><?php esc_html_e( 'Static', 'mailster' ); ?></a>
				<a class="nav-tab" href="#dynamic_embed_options" data-type="dynamic"><?php esc_html_e( 'Dynamic', 'mailster' ); ?></a>
			</div>

			<div id="static_embed_options" class="tab">
				<p class="editbarinfo"><?php esc_html_e( 'Select a post', 'mailster' ); ?></p>
				<p class="alignleft">
					<label title="<?php esc_attr_e( 'use the excerpt if exists otherwise use the content', 'mailster' ); ?>"><input type="radio" name="embed_options_content" class="embed_options_content" value="excerpt" checked> <?php esc_html_e( 'Excerpt', 'mailster' ); ?> </label>
					<label title="<?php esc_attr_e( 'use the content', 'mailster' ); ?>"><input type="radio" name="embed_options_content" class="embed_options_content" value="content"> <?php esc_html_e( 'Full Content', 'mailster' ); ?> </label>
				</p>
				<p id="post_type_select" class="alignright">
				<?php
				$pts = mailster( 'helper' )->get_post_types( true, 'objects' );
				?>
				<?php foreach ( $pts as $pt => $data ) : ?>
					<label><input type="checkbox" name="post_types[]" value="<?php echo esc_attr( $pt ); ?>" <?php checked( 'post' == $pt, true ); ?>> <?php echo esc_html( $data->labels->name ); ?> </label>
				<?php endforeach; ?>
				</p>
				<p>
					<label><input type="text" class="widefat" id="post-search" placeholder="<?php esc_attr_e( 'Search for posts', 'mailster' ); ?>..." ></label>
				</p>
				<div class="postlist">
				</div>
			</div>

			<div id="dynamic_embed_options" class="clear tab" style="display:none;">

				<p>
				<?php
					$content = '<select id="dynamic_embed_options_content" class="check-for-posts"><option value="excerpt">' . esc_html__( 'the excerpt', 'mailster' ) . '</option><option value="content">' . esc_html__( 'the full content', 'mailster' ) . '</option></select>';

					$relative      = '<select id="dynamic_embed_options_relative" class="check-for-posts">';
					$relativenames = array(

						'-1'  => esc_html__( 'the latest', 'mailster' ),
						'-2'  => esc_html__( 'the second latest', 'mailster' ),
						'-3'  => esc_html__( 'the third latest', 'mailster' ),
						'-4'  => esc_html__( 'the fourth latest', 'mailster' ),
						'-5'  => esc_html__( 'the fifth latest', 'mailster' ),
						'-6'  => esc_html__( 'the sixth latest', 'mailster' ),
						'-7'  => esc_html__( 'the seventh latest', 'mailster' ),
						'-8'  => esc_html__( 'the eighth latest', 'mailster' ),
						'-9'  => esc_html__( 'the ninth latest', 'mailster' ),
						'-10' => esc_html__( 'the tenth latest', 'mailster' ),
						'-11' => esc_html__( 'the eleventh latest', 'mailster' ),
						'-12' => esc_html__( 'the twelfth latest', 'mailster' ),
					);
					$randomnames   = array(
						'~1'  => esc_html__( '1st random', 'mailster' ),
						'~2'  => esc_html__( '2nd random', 'mailster' ),
						'~3'  => esc_html__( '3rd random', 'mailster' ),
						'~4'  => esc_html__( '4th random', 'mailster' ),
						'~5'  => esc_html__( '5th random', 'mailster' ),
						'~6'  => esc_html__( '6th random', 'mailster' ),
						'~7'  => esc_html__( '7th random', 'mailster' ),
						'~8'  => esc_html__( '8th random', 'mailster' ),
						'~9'  => esc_html__( '9th random', 'mailster' ),
						'~10' => esc_html__( '10th random', 'mailster' ),
						'~11' => esc_html__( '11th random', 'mailster' ),
						'~12' => esc_html__( '12th random', 'mailster' ),

					);

					$relative .= '<optgroup label="' . esc_html__( 'Relative', 'mailster' ) . '">';
					foreach ( $relativenames as $key => $name ) {
						$relative .= '<option value="' . $key . '">' . $name . '</option>';
					}
					$relative .= '</optgroup>';

					$relative .= '<optgroup label="' . esc_html__( 'Random', 'mailster' ) . '">';
					foreach ( $randomnames as $key => $name ) {
						$relative .= '<option value="' . $key . '">' . $name . '</option>';
					}
					$relative .= '</optgroup>';

					$relative  .= '</select>';
					$pts        = mailster( 'helper' )->get_dynamic_post_types( true, 'objects' );
					$post_types = '<select id="dynamic_embed_options_post_type">';
					foreach ( $pts as $pt => $data ) {
						if ( in_array( $pt, array( 'attachment', 'newsletter' ) ) ) {
							continue;
						}

						$post_types .= '<option value="' . $pt . '">' . $data->labels->singular_name . '</option>';
					}
					$post_types .= '<option value="rss">' . __( 'RSS Feed', 'mailster' ) . '</option>';
					$post_types .= '</select>';

					printf( esc_html_x( 'Insert %1$s of %2$s %3$s', 'Insert [excerpt] of [latest] [post]', 'mailster' ), $content, $relative, $post_types );
					?>
				<span class="dynamic-rss">
					<?php esc_html_e( 'from', 'mailster' ); ?> <label class="dynamic-rss-url-label"><input type="url" id="dynamic_rss_url" class="widefat" placeholder="https://example.com/feed.xml" value=""></label>
				</span>
				</p>
				<div class="right">
					<div class="current-preview">
						<label><?php esc_html_e( 'Current Match', 'mailster' ); ?>:</label>
						<h4 class="current-match">&hellip;</h4>
						<div class="current-tag code">&hellip;</div>
					</div>
				</div>
				<div class="left">
					<div id="dynamic_embed_options_cats"></div>
				</div>
				<div class="editbar-description">
					<p class="description clear">
						<?php esc_html_e( 'Dynamic content get replaced with the proper content as soon as the campaign get send. Check the quick preview to see the current status of dynamic elements.', 'mailster' ); ?>
					</p>
					<p class="description clear">
						<?php esc_html_e( 'Random tags will display a random content while the number is used as an identifier. Same identifier will display content from the same post.', 'mailster' ); ?>
						<?php esc_html_e( 'Different identifier will never display the same post in the same campaign.', 'mailster' ); ?>
					</p>
				</div>
			</div>

		</div>
			<div class="type codeview">
				<textarea id="module-codeview-textarea" autocomplete="off"></textarea>
			</div>

		</div>

		<div class="buttons clearfix">
			<button class="button button-primary save" aria-label="<?php esc_attr_e( 'Save', 'mailster' ); ?>"><?php esc_html_e( 'Save', 'mailster' ); ?></button>
			<button class="button cancel" aria-label="<?php esc_attr_e( 'Cancel', 'mailster' ); ?>"><?php esc_html_e( 'Cancel', 'mailster' ); ?></button>
			<label class="original-checkbox" title="<?php esc_attr_e( 'use the original image file and prevent cropping/modifing the image.', 'mailster' ); ?>">
				<input type="checkbox" class="original" aria-label="<?php esc_attr_e( 'Use original image', 'mailster' ); ?>"> <?php esc_html_e( 'Use original image', 'mailster' ); ?>
			</label>
			<label class="highdpi-checkbox" title="<?php esc_attr_e( 'use HighDPI/Retina ready images if available', 'mailster' ); ?>">
				<input type="checkbox" class="highdpi" <?php checked( mailster_option( 'high_dpi' ) ); ?> aria-label="<?php esc_attr_e( 'use High DPI image', 'mailster' ); ?>"> <?php esc_html_e( 'HighDPI/Retina ready', 'mailster' ); ?>
			</label>
			<a class="remove mailster-icon" title="<?php esc_attr_e( 'remove element', 'mailster' ); ?>" aria-label="<?php esc_attr_e( 'remove element', 'mailster' ); ?>"></a>
		</div>
		<input type="hidden" class="factor" value="1">

	</div>
