<div class="wrap" id="mailster-setup">

<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>

<?php

$timeformat = mailster( 'helper' )->timeformat();
$timeoffset = mailster( 'helper' )->gmt_offset( true );

$is_verified        = mailster()->is_verified();
$active_plugins     = get_option( 'active_plugins', array() );
$active_pluginslugs = preg_replace( '/^(.*)\/.*$/', '$1', $active_plugins );
$plugins            = array_keys( get_plugins() );
$pluginslugs        = preg_replace( '/^(.*)\/.*$/', '$1', $plugins );

$utm = array(
	'utm_campaign' => 'Mailster Setup',
	'utm_source'   => preg_replace( '/^https?:\/\//', '', get_bloginfo( 'url' ) ),
	'utm_medium'   => 'link',
);

?>
	<ol class="mailster-setup-steps-nav">
		<li><a href="#basics"><?php esc_html_e( 'Basics', 'mailster' ); ?></a></li>
		<li><a href="#homepage"><?php esc_html_e( 'Homepage', 'mailster' ); ?></a></li>
		<li><a href="#delivery"><?php esc_html_e( 'Delivery', 'mailster' ); ?></a></li>
		<li><a href="#privacy"><?php esc_html_e( 'Privacy', 'mailster' ); ?></a></li>
		<li><a href="#validation"><?php esc_html_e( 'Validation', 'mailster' ); ?></a></li>
		<li class="not-hidden"><a href="#finish"><?php esc_html_e( 'Ready!', 'mailster' ); ?></a></li>
	</ol>

	<input style="display:none"><input type="password" style="display:none">

	<div class="mailster-setup-steps">

		<div class="mailster-setup-step" id="step_start">

			<h2><?php esc_html_e( 'Welcome to Mailster', 'mailster' ); ?></h2>

			<div class="mailster-setup-step-body">

			<form class="mailster-setup-step-form">

			<p><?php esc_html_e( 'Before you can start sending your campaigns Mailster needs some info to get started.', 'mailster' ); ?></p>

			<p><?php esc_html_e( 'This wizard helps you to setup Mailster. All options available can be found later in the settings. You can always skip each step and adjust your settings later if you\'re not sure.', 'mailster' ); ?></p>

			<p><?php printf( esc_html__( 'The wizard is separated into %d different steps:', 'mailster' ), 5 ); ?></p>

			<dl>
				<dt><?php esc_html_e( 'Basic Information', 'mailster' ); ?></dt>
				<dd><?php esc_html_e( 'Mailster needs some essential informations like your personal information and also some legal stuff.', 'mailster' ); ?></dd>
			</dl>
			<dl>
				<dt><?php esc_html_e( 'Newsletter Homepage Setup', 'mailster' ); ?></dt>
				<dd><?php esc_html_e( 'This is where your subscribers signup, manage or cancel their subscriptions.', 'mailster' ); ?></dd>
			</dl>
			<dl>
				<dt><?php esc_html_e( 'Delivery Options', 'mailster' ); ?></dt>
				<dd><?php esc_html_e( 'How Mailster should delivery you campaigns.', 'mailster' ); ?></dd>
			</dl>
			<dl>
				<dt><?php esc_html_e( 'Privacy', 'mailster' ); ?></dt>
				<dd><?php esc_html_e( 'Mailster takes the privacy of your subscribers information seriously. Define which information Mailster should save.', 'mailster' ); ?></dd>
			</dl>
			<dl>
				<dt><?php esc_html_e( 'Validation', 'mailster' ); ?></dt>
				<dd><?php esc_html_e( 'Updates are important and if you have a valid license for Mailster you can automatically update directly from WordPress.', 'mailster' ); ?></dd>
			</dl>

			<p><a class="button button-hero button-primary next-step" href="#basics"><?php esc_html_e( 'Start Wizard', 'mailster' ); ?></a> <?php esc_html_e( 'or', 'mailster' ); ?> <a href="admin.php?page=mailster_dashboard&mailster_setup_complete=<?php echo wp_create_nonce( 'mailster_setup_complete' ); ?>"><?php esc_html_e( 'skip it', 'mailster' ); ?></a></p>

			</form>

			</div>

			<div class="mailster-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-primary next-step" href="#basics"><?php esc_html_e( 'Start Wizard', 'mailster' ); ?></a>

			</div>


		</div>

		<div class="mailster-setup-step" id="step_basics">

			<h2><?php esc_html_e( 'Basic Information', 'mailster' ); ?></h2>

			<div class="mailster-setup-step-body">

			<form class="mailster-setup-step-form">

			<p><?php esc_html_e( 'Please provide some basic information which is used for your newsletter campaigns. Mailster already pre-filled the fields with the default values but you should check them for correctness.', 'mailster' ); ?></p>
			<table class="form-table">

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'From Name', 'mailster' ); ?></th>
					<td><input type="text" name="mailster_options[from_name]" value="<?php echo esc_attr( mailster_option( 'from_name' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'The sender name which is displayed in the from field', 'mailster' ); ?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'From Address', 'mailster' ); ?></th>
					<td><input type="text" name="mailster_options[from]" value="<?php echo esc_attr( mailster_option( 'from' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'The sender email address. Force your receivers to whitelabel this email address.', 'mailster' ); ?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Reply To Address', 'mailster' ); ?></th>
					<td><input type="text" name="mailster_options[reply_to]" value="<?php echo esc_attr( mailster_option( 'reply_to' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'The address users can reply to', 'mailster' ); ?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Logo', 'mailster' ); ?>
					</th>
					<td>
					<?php mailster( 'helper' )->media_editor_link( mailster_option( 'logo', get_theme_mod( 'custom_logo' ) ), 'mailster_options[logo]', 'full' ); ?>
					<p class="description"><label><input type="hidden" name="mailster_options[logo_high_dpi]" value=""><input type="checkbox" name="mailster_options[logo_high_dpi]" value="1" <?php checked( mailster_option( 'logo_high_dpi' ) ); ?>> <?php esc_html_e( 'Use High DPI version if available.', 'mailster' ); ?></label></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Logo Link', 'mailster' ); ?></th>
					<td><input type="text" name="mailster_options[logo_link]" value="<?php echo esc_attr( mailster_option( 'logo_link' ) ); ?>" class="regular-text"> <p class="description"><?php esc_html_e( 'A link for your logo.', 'mailster' ); ?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Module Thumbnails', 'mailster' ); ?></th>
					<td><label><input type="hidden" name="mailster_options[module_thumbnails]" value=""><input type="checkbox" name="mailster_options[module_thumbnails]" value="1" <?php checked( mailster_option( 'module_thumbnails' ) ); ?>> <?php esc_html_e( 'Show thumbnails of modules in the editor if available', 'mailster' ); ?> *</label>
						<p class="description">* <?php esc_html_e( 'this option will send the HTML of your template files to our screen shot server which generates the thumbnails for you.', 'mailster' ); ?></p>
					</td>
				</tr>

			</table>
			<?php $tags = mailster_option( 'tags' ); ?>

			<p><?php esc_html_e( 'Some information is used in the footer of your campaign. Some information is required by law so please ask your lawyer about correct use.', 'mailster' ); ?></p>

			<table class="form-table">

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Company', 'mailster' ); ?></th>
					<td><input type="text" name="mailster_options[tags][company]" value="<?php echo esc_attr( $tags['company'] ); ?>" class="regular-text"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Copyright', 'mailster' ); ?></th>
					<td><input type="text" name="mailster_options[tags][copyright]" value="<?php echo esc_attr( $tags['copyright'] ); ?>" class="regular-text"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Homepage', 'mailster' ); ?></th>
					<td><input type="text" name="mailster_options[tags][homepage]" value="<?php echo esc_attr( $tags['homepage'] ); ?>" class="regular-text"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Address', 'mailster' ); ?></th>
					<td><textarea name="mailster_options[tags][address]" class="large-text" rows="5"><?php echo esc_attr( $tags['address'] ); ?></textarea></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'CAN-SPAM', 'mailster' ); ?></th>
					<td><input type="text" name="mailster_options[tags][can-spam]" value="<?php echo esc_attr( $tags['can-spam'] ); ?>" class="large-text"> <p class="description"><?php esc_html_e( 'This line is required in most countries. Your subscribers need to know why and where they have subscribed.', 'mailster' ); ?></p></td>
				</tr>

			</table>

			<p><?php printf( esc_html__( 'Wonder what these {placeholders} are for? Read more about tags %s.', 'mailster' ), '<a href="' . add_query_arg( $utm, 'https://kb.mailster.co/tags-in-mailster/' ) . '" class="external">' . esc_html__( 'here', 'mailster' ) . '</a>' ); ?></p>

			</div>

			</form>

			<div class="mailster-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step" href="#homepage"><?php esc_html_e( 'Skip this Step', 'mailster' ); ?></a>
				<a class="button button-large button-primary next-step" href="#homepage"><?php esc_html_e( 'Next Step', 'mailster' ); ?></a>

			</div>

		</div>

		<div class="mailster-setup-step" id="step_homepage">

			<h2><?php esc_html_e( 'Newsletter Homepage', 'mailster' ); ?></h2>

			<div class="mailster-setup-step-body">

			<form class="mailster-setup-step-form">

			<p><?php esc_html_e( 'Mailster needs a Newsletter Homepage were users can subscribe, update and unsubscribe their subscription. It\'s a regular page with some required shortcodes.', 'mailster' ); ?></p>

			<?php

			$buttontext = esc_html__( 'Update Newsletter Homepage', 'mailster' );

			if ( ! ( $homepage = (array) get_post( mailster_option( 'homepage' ) ) ) ) {
				include MAILSTER_DIR . 'includes/static.php';

				$buttontext = esc_html__( 'Create Newsletter Homepage', 'mailster' );
				$homepage   = $mailster_homepage;

			}
			?>
			<p>
			<label><strong><?php esc_html_e( 'Page Title', 'mailster' ); ?>:</strong>
			<input id="homepage_title" type="text" name="post_title" size="30" value="<?php echo esc_attr( $homepage['post_title'] ); ?>" id="title" spellcheck="true" autocomplete="off"></label>

			<?php if ( mailster( 'helper' )->using_permalinks() ) : ?>

				<?php $url = trailingslashit( get_bloginfo( 'url' ) ); ?>
				<label><?php echo esc_html_x( 'Location', 'the URL not the place', 'mailster' ); ?>:</label>
				<span>
					<a href="<?php echo $url . sanitize_title( $homepage['post_name'] ); ?>" class="external"><?php echo $url; ?><strong><?php echo sanitize_title( $homepage['post_name'] ); ?></strong>/</a>
					<a class="button button-small hide-if-no-js edit-slug"><?php echo esc_html__( 'Edit', 'mailster' ); ?></a>
				</span>
				<span class="edit-slug-area">
				<?php echo $url; ?><input type="text" name="post_name" value="<?php echo sanitize_title( $homepage['post_name'] ); ?>" class="regular-text">/
				</span>

			<?php endif; ?>

			</p>

			<p><?php echo wp_editor( $homepage['post_content'], 'post_content' ); ?></p>

			</form>

			</div>

			<div class="mailster-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step" href="#delivery"><?php esc_html_e( 'Skip this Step', 'mailster' ); ?></a>
				<a class="button button-large button-primary next-step" href="#delivery"><?php echo esc_html( $buttontext ); ?></a>

			</div>

		</div>

		<div class="mailster-setup-step" id="step_delivery">

			<h2><?php esc_html_e( 'Delivery', 'mailster' ); ?></h2>

			<div class="mailster-setup-step-body">

			<form class="mailster-setup-step-form">

			<p><?php esc_html_e( 'Choose how Mailster should send your campaigns. It\'s recommend to go with a dedicate ESP to prevent rejections and server blocking.', 'mailster' ); ?></p>

			<?php $method = mailster_option( 'deliverymethod', 'simple' ); ?>

			<div id="deliverynav" class="nav-tab-wrapper hide-if-no-js">
				<a class="nav-tab<?php echo 'simple' == $method ? ' nav-tab-active' : ''; ?>" href="#simple"><?php esc_html_e( 'Simple', 'mailster' ); ?></a>
				<a class="nav-tab<?php echo 'smtp' == $method ? ' nav-tab-active' : ''; ?>" href="#smtp">SMTP</a>
				<a class="nav-tab<?php echo 'gmail' == $method ? ' nav-tab-active' : ''; ?>" href="#gmail">Gmail</a>
				<a class="nav-tab<?php echo 'amazonses' == $method ? ' nav-tab-active' : ''; ?>" href="#amazonses">AmazonSES</a>
				<a class="nav-tab<?php echo 'sparkpost' == $method ? ' nav-tab-active' : ''; ?>" href="#sparkpost">SparkPost</a>
				<a class="nav-tab<?php echo 'mailgun' == $method ? ' nav-tab-active' : ''; ?>" href="#mailgun">Mailgun</a>
				<a class="nav-tab<?php echo 'sendgrid' == $method ? ' nav-tab-active' : ''; ?>" href="#sendgrid">SendGrid</a>
				<a class="nav-tab<?php echo 'mandrill' == $method ? ' nav-tab-active' : ''; ?>" href="#mandrill">Mandrill</a>
				<a class="nav-tab<?php echo 'dummymailer' == $method ? ' nav-tab-active' : ''; ?>" href="#dummymailer">DummyMailer</a>
			</div>

			<input type="hidden" name="mailster_options[deliverymethod]" id="deliverymethod" value="<?php echo esc_attr( $method ); ?>" class="regular-text">

			<div class="deliverytab" id="deliverytab-simple"<?php echo 'simple' == $method ? ' style="display:block"' : ''; ?>>
				<?php do_action( 'mailster_deliverymethod_tab_simple' ); ?>
			</div>
			<div class="deliverytab" id="deliverytab-smtp"<?php echo 'smtp' == $method ? ' style="display:block"' : ''; ?>>
				<?php do_action( 'mailster_deliverymethod_tab_smtp' ); ?>
			</div>
			<div class="deliverytab" id="deliverytab-gmail"<?php echo 'gmail' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'mailster-gmail', $active_pluginslugs ) ) :
					do_action( 'mailster_deliverymethod_tab_gmail' );
				else :
					?>
				<div class="wp-plugin">
				<a href="https://wordpress.org/plugins/mailster-gmail/" class="external">
					<img src="//ps.w.org/mailster-gmail/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
					<span>Mailster Gmail Integration</span>
				</a>
				</div>
				<a class="button button-primary quick-install" data-plugin="mailster-gmail" data-method="gmail">
					<?php echo in_array( 'mailster-gmail', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'Gmail' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-amazonses"<?php echo 'amazonses' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'mailster-amazonses', $active_pluginslugs ) ) :
					do_action( 'mailster_deliverymethod_tab_amazonses' );
				else :
					?>
				<div class="wp-plugin">
				<a href="https://wordpress.org/plugins/mailster-amazonses/" class="external">
					<img src="//ps.w.org/mailster-amazonses/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
					<span>Mailster Amazon SES Integration</span>
				</a>
				</div>
				<a class="button button-primary quick-install" data-plugin="mailster-amazonses" data-method="amazonses">
					<?php echo in_array( 'mailster-amazonses', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'Amazon SES' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-sparkpost"<?php echo 'sparkpost' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'mailster-sparkpost', $active_pluginslugs ) ) :
					do_action( 'mailster_deliverymethod_tab_sparkpost' );
				else :
					?>
				<div class="wp-plugin">
				<a href="https://wordpress.org/plugins/mailster-sparkpost/" class="external">
					<img src="//ps.w.org/mailster-sparkpost/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
					<span>Mailster SparkPost Integration</span>
				</a>
				</div>
				<a class="button button-primary quick-install" data-plugin="mailster-sparkpost" data-method="sparkpost">
					<?php echo in_array( 'mailster-sparkpost', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'SparkPost' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-mailgun"<?php echo 'mailgun' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'mailster-mailgun', $active_pluginslugs ) ) :
					do_action( 'mailster_deliverymethod_tab_mailgun' );
				else :
					?>
				<div class="wp-plugin">
				<a href="https://wordpress.org/plugins/mailster-mailgun/" class="external">
					<img src="//ps.w.org/mailster-mailgun/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
					<span>Mailster Mailgun Integration</span>
				</a>
				</div>
				<a class="button button-primary quick-install" data-plugin="mailster-mailgun" data-method="mailgun">
					<?php echo in_array( 'mailster-mailgun', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'Mailgun' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-sendgrid"<?php echo 'sendgrid' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'mailster-sendgrid', $active_pluginslugs ) ) :
					do_action( 'mailster_deliverymethod_tab_sendgrid' );
				else :
					?>
				<div class="wp-plugin">
				<a href="https://wordpress.org/plugins/mailster-sendgrid/" class="external">
					<img src="//ps.w.org/mailster-sendgrid/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
					<span>Mailster SendGrid Integration</span>
				</a>
				</div>
				<a class="button button-primary quick-install" data-plugin="mailster-sendgrid" data-method="sendgrid">
					<?php echo in_array( 'mailster-sendgrid', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'SendGrid' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-mandrill"<?php echo 'mandrill' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'mailster-mandrill', $active_pluginslugs ) ) :
					do_action( 'mailster_deliverymethod_tab_mandrill' );
				else :
					?>
				<div class="wp-plugin">
				<a href="https://wordpress.org/plugins/mailster-mandrill/" class="external">
					<img src="//ps.w.org/mailster-mandrill/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
					<span>Mailster Mandrill Integration</span>
				</a>
				</div>
				<a class="button button-primary quick-install" data-plugin="mailster-mandrill" data-method="mandrill">
					<?php echo in_array( 'mailster-mandrill', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'Mandrill' ); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="deliverytab" id="deliverytab-dummymailer"<?php echo 'dummymailer' == $method ? ' style="display:block"' : ''; ?>>
				<?php
				if ( in_array( 'mailster-dummy-mailer', $active_pluginslugs ) ) :
					do_action( 'mailster_deliverymethod_tab_dummymailer' );
				else :
					?>
				<div class="wp-plugin">
				<a href="https://wordpress.org/plugins/mailster-dummy-mailer/" class="external">
					<img src="//ps.w.org/mailster-dummy-mailer/assets/banner-772x250.png?v=<?php echo MAILSTER_VERSION; ?>" width="772" height="250">
					<span>Mailster Dummy Mailer</span>
				</a>
				</div>
				<a class="button button-primary quick-install" data-plugin="mailster-dummy-mailer" data-method="dummymailer">
					<?php echo in_array( 'mailster-dummy-mailer', $pluginslugs ) ? esc_html__( 'Activate Plugin', 'mailster' ) : sprintf( esc_html__( 'Install %s Extension', 'mailster' ), 'Dummy Mailer' ); ?>
				</a>
				<?php endif; ?>
			</div>

			</form>

			</div>

			<div class="mailster-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step" href="#privacy"><?php esc_html_e( 'Skip this Step', 'mailster' ); ?></a>
				<a class="button button-large button-primary next-step delivery-next-step" href="#privacy"><?php esc_html_e( 'Next Step', 'mailster' ); ?></a>

			</div>

		</div>

		<div class="mailster-setup-step" id="step_privacy">

			<h2><?php esc_html_e( 'Privacy', 'mailster' ); ?></h2>

			<div class="mailster-setup-step-body">

			<form class="mailster-setup-step-form">

			<p><?php esc_html_e( 'Mailster can track specific behaviors and the location of your subscribers to target your audience better. In most countries you must get the consent of the subscriber if you sent them marketing emails. Please get in touch with your lawyer for legal advice in your country.', 'mailster' ); ?></p>
			<p><?php esc_html_e( 'If you have users in the European Union you have to comply with the General Data Protection Regulation (GDPR). Please check our knowledge base on how Mailster can help you.', 'mailster' ); ?></p>
			<p><a href="https://kb.mailster.co/tag/gdpr/" class="external button button-primary"><?php esc_html_e( 'Knowledge Base', 'mailster' ); ?></a></p>

			<?php require MAILSTER_DIR . '/views/settings/privacy.php'; ?>

			</div>

			</form>

			<div class="mailster-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step" href="#validation"><?php esc_html_e( 'Skip this Step', 'mailster' ); ?></a>
				<a class="button button-large button-primary next-step" href="#validation"><?php esc_html_e( 'Next Step', 'mailster' ); ?></a>

			</div>

		</div>

		<div class="mailster-setup-step" id="step_validation">

			<h2><?php esc_html_e( 'Validation', 'mailster' ); ?></h2>

			<div class="mailster-setup-step-body">

			<p><?php esc_html_e( 'Updates are important to get new features and security fixes. An outdated version of your plugins can always bring the risk of getting compromised.', 'mailster' ); ?></p>

			<?php mailster( 'register' )->form(); ?>

			</div>

			<div class="mailster-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large skip-step validation-skip-step<?php echo $is_verified ? ' disabled' : ''; ?>" href="#finish"><?php esc_html_e( 'Remind me later', 'mailster' ); ?></a>
				<a class="button button-large button-primary next-step validation-next-step<?php echo ! $is_verified ? ' disabled' : ''; ?>" href="#finish"><?php esc_html_e( 'Next Step', 'mailster' ); ?></a>

			</div>

		</div>

		<div class="mailster-setup-step" id="step_finish">

			<form class="mailster-setup-step-form">

			<h2><?php esc_html_e( 'Great, you\'re done!', 'mailster' ); ?></h2>

			<div class="mailster-setup-step-body">

			<p><?php esc_html_e( 'Now you can continue to customize Mailster to your needs.', 'mailster' ); ?></p>

			<div class="feature-section two-col">
				<div class="col">
				<ol>
					<li><a href="edit.php?post_type=newsletter&page=mailster_settings"><?php esc_html_e( 'Complete your settings', 'mailster' ); ?></a></li>
					<li><a href="post-new.php?post_type=newsletter"><?php esc_html_e( 'Create your first campaign', 'mailster' ); ?></a></li>
					<li><a href="edit.php?post_type=newsletter&page=mailster_forms"><?php esc_html_e( 'Update your forms', 'mailster' ); ?></a></li>
					<li><a href="edit.php?post_type=newsletter&page=mailster_manage_subscribers"><?php esc_html_e( 'Import your existing subscribers', 'mailster' ); ?></a></li>
					<li><a href="edit.php?post_type=newsletter&page=mailster_templates"><?php esc_html_e( 'Check out the templates', 'mailster' ); ?></a></li>
					<li><a href="edit.php?post_type=newsletter&page=mailster_addons"><?php esc_html_e( 'Extend Mailster', 'mailster' ); ?></a></li>
				</ol>
				</div>
				<div class="col">
				<h3><?php esc_html_e( 'External Resources', 'mailster' ); ?></h3>
				<ol>
					<li><a href="<?php echo add_query_arg( $utm, 'https://kb.mailster.co/working-with-subscriber-based-auto-responders/' ); ?>" class="external"><?php esc_html_e( 'Create a welcome message for new subscribers', 'mailster' ); ?></a></li>
					<li><a href="<?php echo add_query_arg( $utm, 'https://kb.mailster.co/how-can-i-customize-the-notification-template/' ); ?>" class="external"><?php esc_html_e( 'Customize the notification template', 'mailster' ); ?></a></li>
					<li><a href="<?php echo add_query_arg( $utm, 'https://kb.mailster.co/working-with-action-based-auto-responders/' ); ?>" class="external"><?php esc_html_e( 'Send your latest posts automatically', 'mailster' ); ?></a></li>
					<li><a href="<?php echo add_query_arg( $utm, 'https://kb.mailster.co/creating-a-series-in-mailster/' ); ?>" class="external"><?php esc_html_e( 'Creating a series or drip campaign', 'mailster' ); ?></a></li>
					<li><a href="<?php echo add_query_arg( $utm, 'https://kb.mailster.co/segmentation-in-mailster/' ); ?>" class="external"><?php esc_html_e( 'Learn more about segmentation', 'mailster' ); ?></a></li>
				</ol>
				</div>
			</div>
			<p><?php printf( esc_html__( 'Still need help? Go ask on the %s further questions.', 'mailster' ), '<a href="' . add_query_arg( $utm, 'https://kb.mailster.co/' ) . '" class="external">' . esc_html__( 'knowledge base', 'mailster' ) . '</a>' ); ?></p>

			<div class="social-media-buttons">
				<div id="fb-root"></div>
					<a href="https://twitter.com/mailster?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-size="large" data-show-count="false">Follow @mailster</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0&appId=1656804244418051&autoLogAppEvents=1';
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));</script>
				<div class="fb-like" data-href="https://www.facebook.com/mailster/" data-layout="button" data-action="like" data-size="large" data-show-faces="true" data-share="true"></div>
				</div>
			</div>

			<div class="mailster-setup-step-buttons">

				<span class="alignleft status"></span>
				<i class="spinner"></i>

				<a class="button button-large button-primary" href="admin.php?page=mailster_dashboard&mailster_setup_complete=<?php echo wp_create_nonce( 'mailster_setup_complete' ); ?>"><?php esc_html_e( 'Ok, got it!', 'mailster' ); ?></a>

			</div>

		</div>

	</div>

<div id="ajax-response"></div>
<br class="clear">
</div>
