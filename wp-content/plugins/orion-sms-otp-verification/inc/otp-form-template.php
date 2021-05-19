<?php
/**
 * OTP Form template
 *
 * @package Orion SMS OTP Verification
 */

$text_domain = 'orion-sms-orion-sms-otp-verification';
?>
<div class="wrap orion-otp-mega-wrapper">
	<div class="jumbotron">
		<h6 class="mb-0 text-white lh-100">Orion SMS OTP Verification <i class="fab fa-product-hunt"></i>ro</h6>
		<small><?php echo __( 'by', $text_domain ); ?> Imran Sayed, Smit Patadiya</small>
	</div>

	<!--Plugin Description-->
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<h6 class="border-bottom border-gray pb-2 mb-0"><i class="fa fa-info-circle" aria-hidden="true"></i> <?php echo __( 'Description', $text_domain ); ?></h6>
		<div class="media text-muted pt-3">
			<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
				<ul>
					<!--Common Instructions-->
					<li><?php echo __( 'This plugin allows you to verify mobile number by sending a one time OTP to the user entered mobile number', $text_domain ) ?></li>
					<li><?php echo __( 'You can verify mobile number on Contact form 7 and any registration form. It will not allow the form to be submitted before completing the OTP verification.', $text_domain ) ?></li>
					<br>
					<li><?php echo __( 'This plugin gives you an option to choose between two third party APIs:', $text_domain ) ?></li>
					<li><strong><?php echo __( '1-MSG91: ', $text_domain ); ?></strong> <?php echo __( 'You can choose MSG91 API to send messages' ); ?> ( <a target="_blank" href="<?php echo esc_url( 'https://msg91.com' ) ?>"><i class="fas fa-link"></i> https://msg91.com</a> ). <?php echo __( 'All you have to do is get your auth key from MSG91 to send messages from the below link:', $text_domain ); ?>
						<a target="_blank" href="<?php echo esc_url( 'https://msg91.com/signup' ) ?>"><i class="fas fa-link"></i> https://msg91.com/signup</a>
					</li>
					<li><strong><?php echo __( '2-Twilio: ', $text_domain ); ?></strong> <?php echo __( 'It can use TWILIO API to send messages' ); ?> ( <a target="_blank" href="<?php echo esc_url( 'https://www.twilio.com/' ) ?>"><i class="fas fa-link"></i> https://www.twilio.com</a> ). <?php echo __( 'All you have to do is get your api key from TWILIO to send messages from the below link:', $text_domain ); ?>
						<a target="_blank" href="<?php echo esc_url( 'https://www.twilio.com/console' ) ?>"><i class="fas fa-link"></i> https://www.twilio.com/console</a>
					</li>
					<br>

					<!--Free version-->
					<li class=""><?php echo __( 'You get to verify the user\'s mobile number using mobile OTP on WOOCOMMERCE checkout page before he can place the order. This will verify the authenticity of the mobile number', $text_domain ); ?>

						<?php echo __( 'You also have an option to send and receive Order SMS at the time of Order Processing, Order Cancelled, Order on Hold, Order Completed. Your custom messages that you write here will automatically go when the order status changes. You can also trigger the SMS, when you change the status of the order from woo-commerce order page. To set that up go to Orion OTP > Woo-commerce Settings Page', $text_domain ); ?>
					</li>
					<!--Upgrade to Pro Link-->
					<li class="">
						<?php $link_text = __( 'Upgrade to Another Pack', $text_domain ); ?>
						<a href="<?php echo esc_url( 'https://imransayed.com/orion/' )?>" target="_blank" class="tell-me-hw-link">
							<i class="fab fa-product-hunt"></i>
							<?php echo $link_text; ?>
							<i class="far fa-question-circle"></i>
						</a>
					</li>

					<!--Tell me how link-->
					<li class="ihs-you-tube-link">
						<?php $link_text = __( 'Tell me how to use this plugin', $text_domain ); ?>
						<?php echo ihs_get_tell_me_how_link( $link_text, 'https://youtu.be/hvDkuZowZfM?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
					</li>

					<!--Pro Version Text-->
					<li><?php echo __( 'This pro version supports both OTP and transactional route and also sending OTP to multiple countries.', $text_domain ); ?></li>
					<li><?php echo __( 'User can also reset his/her password using mobile OTP.', $text_domain ); ?></li>

					<!--IMP NOTE-->
					<li class="">
						<?php $link_text = __( 'If you are upgrading to the new version, you may have to change some settings.Hence please watch the new tutorial for the same ', $text_domain ); ?>
						<a href="<?php echo esc_url( 'https://youtu.be/YnqsWA3Ccuc' )?>" target="_blank" class="tell-me-hw-link">
							<i class="fas fa-star"></i>
							<?php echo $link_text; ?>
							<i class="far fa-question-circle"></i>
						</a>
					</li>
					<li class="ihs-you-tube-link">
						<?php $link_text = __( 'New Twilio Features and Settings', $text_domain ); ?>
						<?php echo ihs_get_tell_me_how_link( $link_text, 'https://youtu.be/YnqsWA3Ccuc' )?>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<!--Troubleshooting-->
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<h6 class="border-bottom border-gray pb-2 mb-0"><i class="fa fa-cog" aria-hidden="true"></i> <?php echo __( 'Troubleshooting', $text_domain ); ?></h6>
		<div class="media text-muted pt-3">
			<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
				<ul>
					<!--Common Instructions-->
					<li><?php echo __( 'As shown in the tutorial, the plugin works with WordPress default theme twenty seventeen and is tested successfully with top 5 WordPress registration plugins as mentioned in the plugin description.', $text_domain ) ?></li>
					<li><?php echo __( 'There are millions of plugins and themes on WordPress and it is not practically possible to test it with all of them. Hence we have tested it with default WordPress theme and the top 5 plugins of WordPress.', $text_domain ) ?></li>
					<br>
					<li><strong><?php echo __( 'My plugin does not work', $text_domain ); ?></strong></li>
					<li><?php echo __( 'As explained to you in the terms and conditions( while buying the plugin ) that you should tests the free version before buying the plugin. We have tested the plugin with top 6 plugins( 1-Contact Form 7
2-User Registration -User Profile, Membership and More
3-Ultimate Member
4-Profile Builder -User registration & user profile
5-Profile Press
6-RegistrationMagic.) along with WordPress default theme Twenty seventeen.However we cannot gaurantee that the plugin will work for your specific project. The reason for this is that, based on the queries I have been dealing with and solving them in the past, I have observed that the theme or plugins that you are using may add some styles or scripts that may interfere with the default functionality of Orion OTP Plugin', $text_domain ) ?></li>
					<br>
					<li><strong><?php echo __( 'Solution', $text_domain ); ?></strong></li>
					<li class=""><?php echo __( 'Please follow the trouble shooting steps on :', $text_domain ); ?>
						<a href="<?php echo esc_url( 'https://imransayed.com/orion/troubleshooting/' )?>"><i class="fas fa-link"></i> Faqs/Troubleshooting</a>
					</li>
					<li>
						<?php echo __( 'If you have followed the ', $text_domain ); ?>
						<a href="<?php echo esc_url( 'https://youtu.be/hvDkuZowZfM?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>" target="_blank" class="tell-me-hw-link">
							<i class="fab fa-youtube ihs-you-tube-icon"></i>
							<?php echo __( 'Plugin Tutorial', $text_domain ); ?>
						</a>
						<?php echo __( ', troubleshooting steps and it still does not work, it means we would have to do customization to make the plugin work with your theme/plugin. There will be customization charges, as it involves investing time into customization and making it work for your specific project. For any queries you can write to us on: ', $text_domain ); ?>
						<a href="<?php echo esc_url( 'mailto:orionhiveproducts@gmail.com' )?>" target="_top" class="tell-me-hw-link">
							<i class="fas fa-envelope"></i>
							orionhiveproducts@gmail.com
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<!--Form-->
	<form method="post" action="options.php">
		<?php settings_fields( 'ihs-otp-plugin-settings-group' ); ?>
		<?php do_settings_sections( 'ihs-otp-plugin-settings-group' ); ?>


	<!--1. API Configuration-->
	<!--Heading-->
	<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 bg-purple rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
		<div class="lh-100 ihs-admin-head-cont">
			<h6 class="mb-0 text-white lh-100"><?php echo __( 'Api Configuration', $text_domain ); ?></h6>
			<small><?php __( 'Api settings required for plugin to function', $text_domain ) ?></small>
		</div>
	</div>
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<p class="border-bottom border-gray pb-2 mb-0"><?php echo __( 'You can get the required Key from MSG91/Twilio.', $text_domain ); ?>
		</p>
		<!--Api Type-->
		<div class="media text-muted pt-3">
			<?php $api_type_text = __( 'Select API Type', $text_domain ); ?>
			<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="fas fa-key ihs-my-icons"></i></div>
			<?php echo ihs_get_api_type( 'API TYPE', 'ihs_api_type', true, true, $api_type_text ); ?>
		</div>

		<!--TWILLIO KEYS-->
		<h6 class="border-bottom border-gray pb-3 pt-3 mb-0">
			<?php echo __( 'Twilio API Configuration ', $text_domain ); ?>
			<?php echo ihs_get_tell_me_how_link( 'Tell me how', 'https://youtu.be/hne6x-8nbA0' )?>
		</h6>
		<div class="ihs-twilio-keys">
			<!--Twilio Api Key Input Field-->
			<div class="media text-muted pt-3">
				<?php $tooltip_text = __( 'Get the api key from Twilio', $text_domain ); ?>
				<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'TWILIO API KEY', 'ihs_twilio_api_key',
					'text', false, '', true, $tooltip_text ); ?>
			</div>
			<!--Twilio SID Key Input Field-->
			<div class="media text-muted pt-3">
				<?php $tooltip_text = __( 'Get the sid key from Twilio, for order SMS', $text_domain ); ?>
				<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'TWILIO SID KEY', 'ihs_twilio_sid_key',
					'text', false, '', true, $tooltip_text); ?>
			</div>
			<!--Twilio AUTH Token Input Field-->
			<div class="media text-muted pt-3">
				<?php $tooltip_text = __( 'Get the auth token from Twilio, for order SMS', $text_domain ); ?>
				<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'TWILIO AUTH TOKEN', 'ihs_twilio_auth_token',
					'text', false, '', true, $tooltip_text ); ?>
			</div>
			<!--Twilio PHONE Number Input Field-->
			<div class="media text-muted pt-3">
				<?php $tooltip_text = __( 'Get the Twilio phone number you purchased at twilio.com/console with + sign, for order SMS', $text_domain ); ?>
				<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'TWILIO PHONE NUMBER', 'ihs_twilio_phone_number',
					'text', false, '', true, $tooltip_text ); ?>
			</div>
		</div>

		<!--MSG91 KEYS-->
		<h6 class="border-bottom border-gray pb-3 pt-3 mb-0">
			<?php echo __( 'MSG91 API Configuration ', $text_domain ); ?>
			<?php echo ihs_get_tell_me_how_link( 'Tell me how', 'https://youtu.be/od7f82A7RMw?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
		</h6>
		<div class="ihs-msg91-keys">
			<!--MSG91 Auth Key Input Field-->
			<div class="media text-muted pt-3">
				<?php $tooltip_text = __( 'Get the auth key from MSG91', $text_domain ); ?>
				<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-key" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'MSG91 AUTH KEY', 'ihs_otp_auth_key',
					'text', false, '', true, $tooltip_text ); ?>
			</div>
			<!--Sender's ID-->
			<div class="media text-muted pt-3">
				<?php $tooltip_text = __( 'e.g. IBAZAR', $text_domain ); ?>
				<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="fa fa-id-badge" aria-hidden="true"></i></div>
				<?php echo ihs_get_text_input( 'SENDER\'S ID ( 6 characters )', 'ihs_otp_sender_id', 'text',
					false, '', true, $tooltip_text, 6 ); ?>
			</div>
			<!--Route-->
			<div class="media text-muted pt-3">
				<?php $route_text = __( 'Select the MSG91 Route', $text_domain ); ?>
				<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="fas fa-map-signs ihs-my-icons"></i></div>
				<?php echo ihs_get_route_drop_down( 'ROUTE', 'ihs_mgs_route', false, true, $route_text ); ?>
			</div>
		</div>
		<!--Common Configuration-->
		<h6 class="border-bottom border-gray pb-3 pt-3 mb-0">
			<?php echo __( 'Common Configuration', $text_domain ); ?>
		</h6>
		<!--Mobile No length-->
		<div class="media text-muted pt-3">
			<?php $tooltip_text = __( 'How many digits excluding country code? For e.g. for India enter 10', $text_domain ); ?>
			<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-phone-square" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'MOBILE NO LENGTH', 'ihs_mobile_length', 'text',
				false, '', true, $tooltip_text, 2 ); ?>
		</div>
		<!--Country Code-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="fa fa-globe" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'COUNTRY CODE', 'ihs_otp_country_code', 'select' ); ?>
		</div>
		<!--Rating-->
		<?php echo ihs_get_rate_us_content(); ?>
	</div>

	<!--2. Form Settings-->
	<!--Heading-->
	<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 ihs-bg-blue rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
		<div class="lh-100 ihs-admin-head-cont">
			<h6 class="mb-0 text-white lh-100"><?php echo __( 'Form Settings', $text_domain ); ?></h6>
			<small><?php echo __( 'User Registration Form/ Contact Form 7/ Comment Form/ Any Other Form', $text_domain ); ?></small>
		</div>
	</div>
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<h6 class="border-bottom border-gray pb-2 mb-0"><?php echo __( 'Form Settings', $text_domain ); ?>
			<?php echo ihs_get_tell_me_how_link( 'Tell me how', 'https://youtu.be/3EX1p05pEv0?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf' )?>
		</h6>
		

		<!--Contact form Selector-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
			<?php $tooltip_text = __( 'Please enter a unique bodyclassname followed by classname or id name parent div of the form element. Please prefix a . (dot) for class name and # for ID before the selector', $text_domain ); ?>
			<?php echo ihs_get_text_input( 'CONTACT FORM SELECTOR',
				'ihs_otp_form_selector', 'text', false,
				'e.g .bodyclassname #divclassname', true,
				$tooltip_text ); ?>
		</div>
		<!--Submit Btn Selector-->
		<div class="media text-muted pt-3">
			<?php $tooltip_text = __( 'Please enter a unique body classname followed by submit button id or classname. The two selectors need to be separated by space. Also prefix a . (dot) for class name and # for an ID', $text_domain ); ?>
			<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'SUBMIT BUTTON SELECTOR', 'ihs_otp_submit_btn-selector',
				'text', true, 'e.g .body-classname #submit-btn-id',
				true, $tooltip_text); ?>
		</div>
		<!--New Mobile Input field and preexisting One-->
		<?php echo ihs_get_mobile_input_fields();?>

		<!--OTP template-->
		<?php $textarea_placeholder = __( 'Your One Time Password is {OTP}. This OTP is valid for today and please don\'t share this OTP with anyone for security', $text_domain ); ?>
		<div class="media text-muted pt-3">
			<?php $tooltip_text = __( 'Please make sure you follow the format given in placeholder along with {OTP}', $text_domain ); ?>
			<div class="ihs-input-icon ihs_otp_template_textarea ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-envelope" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'OTP TEMPLATE', 'ihs_otp_msg_template',
				'textarea', true, $textarea_placeholder,
				true, $tooltip_text ); ?>
		</div>
		<!--Rating-->
		<?php echo ihs_get_rate_us_content(); ?>
	</div>

	<!--3. Password Reset-->
	<!--Heading-->
	<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 ihs-bg-light-pink rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
		<div class="lh-100 ihs-admin-head-cont">
			<h6 class="mb-0 text-white lh-100"><?php echo __( 'Forgot Password Settings', $text_domain ); ?></h6>
			<small><?php echo __( 'Send forgot Password SMS Settings ( Add these settings if you want forgot password field to be added in Login form )', $text_domain ); ?></small>
		</div>
	</div>
	<div class="my-3 p-3 bg-white rounded box-shadow ihs-api-config-cont">
		<h6 class="border-bottom border-gray pb-2 mb-0"><?php echo __( 'Form Settings', $text_domain ); ?>
			<?php echo ihs_get_tell_me_how_link( 'Tell me how', 'https://youtu.be/3EX1p05pEv0?list=PLD8nQCAhR3tR2N5k3wy8doceQCyVLQEOf&t=925' )?>
		</h6>
		<!--Login form Selector-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
			<?php $tooltip_text = __( 'Enter a unique body classname followed by form\'s parent selector of the login form. Please prefix a . (dot) for class name and # for ID before the login form selector', $text_domain );?>
			<?php echo ihs_get_text_input( 'FORM/PARENT SELECTOR',
				'ihs_otp_login_form_selector', 'text', false,
				'e.g .classname or #idname', true,
				$tooltip_text ); ?>
		</div>
		<!--Input Name-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="ihs-my-icons fab fa-wpforms" aria-hidden="true"></i></div>
			<?php $tooltip_text = __( 'Enter any one input name inside the login form. e.g. name', $text_domain ); ?>
			<?php echo ihs_get_text_input( 'INPUT NAME',
				'ihs_otp_login_form_input_name', 'text', false,
				'e.g user-name', true,
				$tooltip_text ); ?>
		</div>
		<!--Meta Key for Mobile No-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-code" aria-hidden="true"></i></div>
			<?php $tooltip_text = __( 'Enter meta_key for mobile number provided mobile no. is being saved in wp_usermeta table', $text_domain ); ?>
			<?php echo ihs_get_text_input( 'META_KEY FOR MOBILE NO', 'ihs_otp_mob_meta_key',
				'text', false, '',
				true, $tooltip_text ); ?>
		</div>
		<!--Is Mobile No Saved with Country Code-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-pink d-flex"><i class="ihs-my-icons fa fa-globe" aria-hidden="true"></i></div>
			<?php echo ihs_is_saved_with_country_code( 'SAVED WITH COUNTRY CODE', 'ihs_no_saved_with_country',
				true, true, 'If mobile no is being saved with country code in the database, select yes, no 
				otherwise.' ) ?>
		</div>
		<!--Forgot Password Country Code-->
		<div class="media text-muted pt-3">
			<div class="ihs-input-icon ihs-bg-blue d-flex"><i class="fa fa-globe" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'COUNTRY CODE', 'ihs_otp_mob_country_code',
				'select', false, '',
				true, 'If mobile number is being saved with the country code. Please enter the country code ( e.g. if the mobile number is saved as +919960119780 then enter <b>+91</b> )'); ?>
		</div>


		<!--Message template-->
		<?php $textarea_placeholder = 'Your New Password is {OTP}. Please don\'t share this OTP with anyone for security'; ?>
		<div class="media text-muted pt-3">
			<?php $tooltip_text = __( 'Please make sure you follow the format given in placeholder along with {OTP}', $text_domain ); ?>
			<div class="ihs-input-icon ihs_otp_template_textarea ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-envelope" aria-hidden="true"></i></div>
			<?php echo ihs_get_text_input( 'Msg Template', 'ihs_otp_reset_template',
				'textarea', false, $textarea_placeholder,
				true, $tooltip_text ); ?>
		</div>
		<!--Rating-->
		<?php echo ihs_get_rate_us_content(); ?>
	</div>

		<!--Submit Button-->
		<?php submit_button(); ?>
	</form>

	<!--1- Tutorial Section-->
	<!--Heading-->
	<div class="d-sm-flex align-items-center p-3 my-3 text-white-50 ihs-bg-light-purple rounded box-shadow" style="background-color: #6f42c1; box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, .05);">
		<div class="lh-100 ihs-admin-head-cont">
			<h6 class="mb-0 text-white lh-100"><?php echo __( 'How to use the Plugin?', $text_domain );?></h6>
			<small><?php echo __( 'Watch below demo tutorials to have a better understanding', $text_domain ); ?></small>
		</div>
	</div>
	<div class="">
		<div class="row">
			<div class="col-md-4 col-sm-6 col-12">
				<?php $description = __( 'The Plugin Now Supports both Twilio and msg91', $text_domain ); ?>
				<?php ihs_get_video_cards( 'New feature | Twilio Support', $description, 'https://www.youtube.com/embed/YnqsWA3Ccuc' ); ?>
			</div>
			<div class="col-md-4 col-sm-6 col-12">
				<?php $description = __( 'How to get API Key | SID | Auth Token | Twilio Phone No', $text_domain ); ?>
				<?php ihs_get_video_cards( 'Generate Twilio API Key | SID | Auth Token | Twilio Phone No', $description, 'https://www.youtube.com/embed/hne6x-8nbA0' ); ?>
			</div>
			<div class="col-md-4 col-sm-6 col-12">
				<?php $description = __( 'New Orion OTP SMS WordPress Plugin Demo | msg91', $text_domain ); ?>
				<?php ihs_get_video_cards( 'Plugin Demo', $description, 'https://www.youtube.com/embed/hvDkuZowZfM' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php $description = __( 'Whats new in the Orion SMS OTP MSG91 WordPress Plugin V 1.0.2', $text_domain ); ?>
				<?php ihs_get_video_cards( 'New Features', $description, 'https://www.youtube.com/embed/VzrnXY6i-J8' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php $description = __( 'Get the Auth Key from MSG 91 | OTP and Transactional Route', $text_domain ); ?>
				<?php ihs_get_video_cards( 'Auth Key & Routes', $description, 'https://www.youtube.com/embed/od7f82A7RMw' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php $description = __( 'How to use the Orion SMS OTP WordPress plugin with Contact Form 7', $text_domain ); ?>
				<?php ihs_get_video_cards( 'With Contact Form 7', $description, 'https://www.youtube.com/embed/xkafUWOaIL8' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php $description = __( 'How to use Orion SMS OTP Plugin with Ultimate Member Plugin', $text_domain ); ?>
				<?php ihs_get_video_cards( 'With Ultimate Member', $description, 'https://www.youtube.com/embed/3EX1p05pEv0' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php $description = __( 'How to use Orion SMS OTP Plugin with User Registration Plugin', $text_domain ); ?>
				<?php ihs_get_video_cards( 'With User Registration', $description, 'https://www.youtube.com/embed/8G8Vq0tadoE' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php $description = __( 'How to Use Orion SMS OTP Plugin with Registration Magic Plugin', $text_domain ); ?>
				<?php ihs_get_video_cards( 'With Registration Magic', $description, 'https://www.youtube.com/embed/P7zHEEZyqlg' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php $description = __( 'How to use Orion SMS OTP WordPress Plugin with Profile Press', $text_domain ); ?>
				<?php ihs_get_video_cards( 'With Profile Press', $description, 'https://www.youtube.com/embed/ppsnfUQuFDM' ); ?>
			</div>
			<div class="col-md-4 col-sm-6">
				<?php $description = __( 'How to use the Orion SMS OTP WordPress Plugin | Profile Builder', $text_domain ); ?>
				<?php ihs_get_video_cards( 'With Profile Builder', $description, 'https://www.youtube.com/embed/gDh8oP-zoBA' ); ?>
			</div>
		</div>
	</div>
</div>