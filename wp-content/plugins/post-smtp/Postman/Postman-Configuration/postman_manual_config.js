var transports = [];

jQuery(document).ready(
		function($) {

			// display password on entry
			enablePasswordDisplayOnEntry('input_basic_auth_password',
					'togglePasswordField');

			// tabs
			jQuery("#config_tabs").tabs();

			// on first viewing, determine whether to show password or
			// oauth section
			reloadOauthSection();

			// add an event on the transport input field
			// when the user changes the transport, determine whether
			// to show or hide the SMTP Settings
			jQuery('select#input_transport_type').change(function() {
				hide('#wizard_oauth2_help');
				reloadOauthSection();
				switchBetweenPasswordAndOAuth();
			});

			jQuery('select#input_notification_service').change(function() {
				var selected = $( this ).val();

				if ( selected == 'default' ) {
					$('#slack_cred').fadeOut('fast');
					$('#pushover_cred').fadeOut('fast');
				}

				if ( selected == 'pushover' ) {
					$('#slack_cred').fadeOut('fast');
					$('#pushover_cred').fadeIn();
				}

				if ( selected == 'slack' ) {
					$('#pushover_cred').fadeOut('fast');
					$('#slack_cred').fadeIn();
				}

				Hook.call( 'post_smtp_notification_change', selected );

			});
			

			// add an event on the authentication input field
			// on user changing the auth type, determine whether to show
			// password or oauth section
			jQuery('select#input_auth_type').change(function() {
				switchBetweenPasswordAndOAuth();
				doneTyping();
			});

			// setup before functions
			var typingTimer; // timer identifier
			var doneTypingInterval = 250; // time in ms, 5 second for
			// example

			// add an event on the hostname input field
			// on keyup, start the countdown
			jQuery(postman_hostname_element_name).keyup(function() {
				clearTimeout(typingTimer);
				if (jQuery(postman_hostname_element_name).val) {
					typingTimer = setTimeout(doneTyping, doneTypingInterval);
				}
			});

			// user is "finished typing," do something
			function doneTyping() {
				if (jQuery(postman_input_auth_type).val() == 'oauth2') {
					reloadOauthSection();
				}
			}
		});

function reloadOauthSection() {
	var hostname = jQuery(postman_hostname_element_name).val();
	var transport = jQuery('#input_transport_type').val();
	var authtype = jQuery('select#input_auth_type').val();
	var security = jQuery('#security').val();
	var data = {
		'action' : 'manual_config',
		'auth_type' : authtype,
		'hostname' : hostname,
		'transport' : transport,
		'security' : security
	};
	jQuery.post(ajaxurl, data, function(response) {
		if (response.success) {
			handleConfigurationResponse(response);
		}
	}).fail(function(response) {
		ajaxFailed(response);
	});
}
function switchBetweenPasswordAndOAuth() {
	var transportName = jQuery('select#input_transport_type').val();
	transports.forEach(function(item) {
		item.handleTransportChange(transportName);
	});
}
