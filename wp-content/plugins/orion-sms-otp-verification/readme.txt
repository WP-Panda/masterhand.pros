=== Orion SMS OTP Verification ===
Contributors: gsayed786, smitpatadiya
Tags: twilio, msg91, two-step-verification, otp, mobile verification, verification, mobile, phone, sms, one time, password
Requires at least: 4.6
Tested up to: 4.9.2
Stable tag: 4.9.2
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to verify mobile number by sending a one time OTP to the mobile number.
It works with Contact Form 7 and any registration form. You can also reset password using mobile number OTP.

== Description ==

This plugin allows you to verify mobile number by sending a one time OTP to the user entered mobile number.
You can verify mobile number on Contact form 7 and any registration form. It will not allow the form to be submitted before completing the OTP verification.

This plugin gives you an option to choose between two third party APIs:

1-MSG91: You can choose MSG91 API to send messages ( https://msg91.com ). All you have to do is get your auth key from MSG91 to send messages from the below link:
https://msg91.com/signup

2-Twilio: It can use TWILIO API to send messages ( https://www.twilio.com/ ). All you have to do is get your api key from TWILIO to send messages from the below link:
https://www.twilio.com/console

As a pro version You can also set up the OTP verification on Woo-commerce Checkout page and to send Woo-commerce custom Order SMS ( e.g. on Order Cancel, Complete, On Hold, Processing etc ).

This plugin has been tested with WordPress default theme Twentyseventeen along with the top 6 forms plugins( with their versions available at the time of release ) and works successfully:
1-Contact Form 7
2-User Registration -User Profile, Membership and More
3-Ultimate Member
4-Profile Builder -User registration & user profile
5-Profile Press
6-RegistrationMagic.

For some reason if you want to switch back to any of our older versions. You can download them from :
https://imransayed.com/orion/download-prev-versions/



User can also reset his/her password using mobile OTP.:


== Demo Videos ==

Please check the demo videos

[2018-04-04] Plugin Demo.

[youtube https://https://youtu.be/yKGmc_nzqCU]

[2018-10-19] TWILIO API CAN NOW BE USED TO SEND SMS/OTP | Cancel New Feature in Orion OTP Plugin

[youtube https://youtu.be/izQTwIvejh8]

[2018-10-19] Generate Twilio API Key | SID | Auth Token | Twilio Phone No

[youtube https://youtu.be/hne6x-8nbA0]

[2018-04-04] How to use the Plugin Get MSG91 auth key and mobile otp verification with Contact Form 7.

[youtube https://youtu.be/ux-XESYim2s]

[2018-04-04] Reset Password with Mobile OTP in WordPress.

[youtube https://youtu.be/CZOBtC-htvA]


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to WordPress admin Dashboard under Orion OTP Menu and fill the required fields. Watch demo video for better explanation.
4. Create the necessary keys from MS91 or Twilio. Please watch the demo video how to do that. Please find the link to create auth key in the demo video description.
== Frequently Asked Questions ==

= Its not working.

Step 1. Check if your Plugin is activated.
Step 2. Deactivate all plugins and reactivate ihs geo location.
Step 3. Deactivate all plugins and reactivate ihs geo location.
Step 4. Check if all required fields are filled in the WordPress admin Dashboard under Orion OTP menu.
Step 5. For some reason if you want to switch back to any of our older versions. You can download them from :
        https://imransayed.com/orion/download-prev-versions/

== Screenshots ==

1-Add required fields in the dashboard plugin settings page. screenshot-1.png
2-Use OTP verification during user registration with your own pre-existing form. screenshot-2.png
3-You get send OTP button in your existing form. screenshot-3.png
4-Get a success message when OTP is successfully verified. screenshot-4.png
5-You can get a new OTP on user's mobile and which resets the password to the new one. screenshot-5.png