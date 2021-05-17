<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<?php

	$title       = get_the_title();
	$post_id     = get_the_ID();
	$description = wp_trim_words( mailster( 'campaigns' )->get_excerpt( $post_id ), 55, '...' );
	$permalink   = get_permalink();
	$blogname    = get_bloginfo( 'name' );
	$logo_link   = apply_filters( 'mymail_frontpage_logo_link', apply_filters( 'mailster_frontpage_logo_link', get_bloginfo( 'url' ) ) );
	$logo        = apply_filters( 'mymail_frontpage_logo', apply_filters( 'mailster_frontpage_logo', $blogname ) );

	if ( $post_thumbnail_id = get_post_thumbnail_id( $post_id ) ) {

		$size  = mailster( 'campaigns', 'auto_post_thumbnail' )->meta( $post_id ) ? array( 600, 800 ) : 'large';
		$image = wp_get_attachment_image_src( $post_thumbnail_id, $size );

	}

	?>
	<title><?php echo esc_html( $title ); ?></title>

	<link rel="canonical" href="<?php echo add_query_arg( 'frame', 0, $permalink ); ?>">

<?php if ( function_exists( 'get_oembed_endpoint_url' ) ) : ?>
	<link rel="alternate" type="application/json+oembed" href="<?php echo get_oembed_endpoint_url( $permalink, 'json' ); ?>">
	<link rel="alternate" type="application/xml+oembed" href="<?php echo get_oembed_endpoint_url( $permalink, 'xml' ); ?>">
<?php endif; ?>

	<meta property="og:locale" content="<?php echo str_replace( '-', '_', get_bloginfo( 'language' ) ); ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>"/>
	<meta property="og:url" content="<?php echo $permalink; ?>" />
	<meta property="og:site_name" content="<?php echo esc_attr( $blogname ); ?>" />
<?php if ( $post_thumbnail_id ) : ?>
	<meta property="og:image" content="<?php echo esc_attr( $image[0] ); ?>" />
	<meta property="og:image:width" content="<?php echo (int) $image[1]; ?>" />
	<meta property="og:image:height" content="<?php echo (int) $image[2]; ?>" />
<?php endif; ?>

	<meta name="twitter:card" content="<?php echo esc_attr( apply_filters( 'mymail_frontpage_twitter_card', apply_filters( 'mailster_frontpage_twitter_card', 'summary' ) ) ); ?>"/>
	<meta name="twitter:site" content="@<?php echo esc_attr( apply_filters( 'mymail_frontpage_twitter_username', apply_filters( 'mailster_frontpage_twitter_username', 'mailster' ) ) ); ?>"/>
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>"/>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php if ( mailster_option( 'frontpage_public' ) || ! get_option( 'blog_public' ) ) : ?>
	<meta name='robots' content='noindex,nofollow' />
<?php endif; ?>

	<?php do_action( 'mailster_wphead' ); ?>

</head>
<body <?php body_class(); ?>>
	<ul id="header">
		<li class="logo header"><a href="<?php echo esc_url( $logo_link ); ?>"><?php echo esc_html( $blogname ); ?></a></li>
<?php if ( get_previous_post() && mailster_option( 'frontpage_pagination' ) ) : ?>
			<li class="button header previous"><?php previous_post_link( '%link', '' ); ?></li>
<?php endif; ?>
		<li class="subject header"><a href="<?php echo $permalink; ?>">
			<?php if ( ! $meta['webversion'] ) : ?>
				<strong>[ <?php esc_html_e( 'Private', 'mailster' ); ?> ]</strong>
			<?php endif; ?>
			<?php echo esc_html( $title ); ?></a>
		</li>
<?php if ( current_user_can( 'edit_post', $post_id ) ) : ?>
		<li class="editlink header"><a href="<?php echo admin_url( 'post.php?post=' . $post_id . '&action=edit' ); ?>"><?php esc_html_e( 'Edit', 'mailster' ); ?></a></li>
<?php endif; ?>
<?php if ( get_next_post() && mailster_option( 'frontpage_pagination' ) ) : ?>
		<li class="button header next"><?php next_post_link( '%link', '' ); ?></li>
<?php endif; ?>
		<li class="button header closeframe"><a title="remove frame" href="<?php echo add_query_arg( 'frame', 0, $permalink ); ?>">&#10005;</a></li>
<?php if ( mailster_option( 'share_button' ) && ! $preview && ! post_password_required() ) : ?>
	<?php $is_forward = isset( $_GET['mailster_forward'] ) ? $_GET['mailster_forward'] : ''; ?>
			<li class="share header">
				<a><?php esc_html_e( 'Share', 'mailster' ); ?></a>
				<div class="sharebox"<?php echo $is_forward ? ' style="display:block"' : ''; ?>>
					<div class="sharebox-inner">
					<ul class="sharebox-panel">
				<?php if ( $services = mailster_option( 'share_services' ) ) : ?>
						<li class="sharebox-panel-option<?php echo ! $is_forward ? ' active' : ''; ?>">
							<h4><?php printf( esc_html__( 'Share this via %s', 'mailster' ), '&hellip;' ); ?></h4>
							<div>
								<ul class="social-services">
								<?php foreach ( $services as $service ) : ?>
									<?php
									if ( ! isset( $social_services[ $service ] ) ) {
										continue;
									}

									?>
								<li>
									<a title="<?php printf( esc_html__( 'Share this via %s', 'mailster' ), $social_services[ $service ]['name'] ); ?>" class="<?php echo $service; ?>" href="<?php echo str_replace( '%title', urlencode( $title ), str_replace( '%url', urlencode( $permalink ), htmlentities( $social_services[ $service ]['url'] ) ) ); ?>" data-width="<?php echo isset( $social_services[ $service ]['width'] ) ? (int) $social_services[ $service ]['width'] : 650; ?>" data-height="<?php echo isset( $social_services[ $service ]['height'] ) ? (int) $social_services[ $service ]['height'] : 405; ?>" >
									<?php echo esc_html( $social_services[ $service ]['name'] ); ?>
									</a>
								</li>
								<?php endforeach; ?>
								</ul>
							</div>
						</li>
				<?php endif; ?>
					<li class="sharebox-panel-option<?php echo $is_forward ? ' active' : ''; ?>">
						<h4><?php printf( esc_html__( 'Share with %s', 'mailster' ), esc_html__( 'email', 'mailster' ) ); ?></h4>
						<div>
							<form id="emailform" novalidate>
								<p>
									<input type="text" name="sendername" id="sendername" placeholder="<?php esc_attr_e( 'Your name', 'mailster' ); ?>" value="">
								</p>
								<p>
									<input type="email" name="sender" id="sender" placeholder="<?php esc_attr_e( 'Your email address', 'mailster' ); ?>" value="<?php echo $is_forward; ?>">
								</p>
								<p>
									<input type="email" name="receiver" id="receiver" placeholder="<?php esc_attr_e( 'Your friend\'s email address', 'mailster' ); ?>" value="">
								</p>
								<p>
									<textarea name="message" id="message" placeholder="<?php esc_attr_e( 'A personal note to your friend', 'mailster' ); ?>"></textarea>
								</p>
								<p>
									<span class="status">&nbsp;</span>
									<input type="submit" class="button" value="<?php esc_attr_e( 'Send now', 'mailster' ); ?>" >
								</p>
									<div class="loading" id="ajax-loading"></div>
								<p>
									<a class="appsend" href="mailto:?body=%0D%0A%0D%0A<?php echo $permalink; ?>"><?php esc_html_e( 'or send it with your mail application', 'mailster' ); ?></a>
								</p>
								<p class="info"><?php esc_html_e( 'We respect your privacy. Nothing you enter on this page is saved by anyone', 'mailster' ); ?></p>
								<?php wp_nonce_field( $permalink ); ?>
								<input type="hidden" name="url" id="url" value="<?php echo esc_attr( $permalink ); ?>">
							</form>
						</div>
					</li>
					<li class="sharebox-panel-option">
						<h4><?php esc_html_e( 'Share the link', 'mailster' ); ?></h4>
						<div>
							<input type="text" value="<?php echo esc_attr( $permalink ); ?>" onclick="this.select()">
							<?php if ( ! apply_filters( 'mailster_hide_poweredby', false ) ) : ?>
							<div class="powered-by">powered by <a href="https://mailster.co">Mailster</a></div>
							<?php endif; ?>
						</div>
					</li>
				</ul>
				</div>
			</div>
		</li>
<?php endif; ?>
	</ul>
	<div id="iframe-wrap">
		<iframe src="<?php echo add_query_arg( 'frame', 0, $permalink ); ?>" data-no-lazy=""></iframe>
	</div>

	<?php do_action( 'mailster_wpfooter' ); ?>

</body>
</html>
