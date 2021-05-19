=== User Email Verification for WooCommerce ===
Contributors: xlplugins, sandeepsoni214
Tags: Woocommerce, Woocommerce Email Verification, Email Verification
Requires at least: 4.2.1
Tested up to: 5.2.1
Stable tag: 3.5.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Woocommerce Email Verification plugin verifies the email address of user by sending the verification link to the email of a user at registration time.

== Description ==

Woocommerce Email Verification plugin verifies the email address of user by sending the verification link to the email of a user at registration time.

Once the user verifies his identity he can login into his account.

== Woocommerce email verification ensures that: ==

* Order you are getting is not a fraud or fake order.
* Admin can manually send a verification email to confirm the buyer's identity.
* Fully customisable verification email is sent.

== Why use this plugin? ==

* To verify buyer's identity
* Fight Spam
* Have additional details in case of Chargebacks

== Learn About Our Premium Plugins: ==

>[Finale](https://xlplugins.com/finale-woocommerce-sales-countdown-timer-discount-plugin/?utm_source=woo-confirmation-email&utm_campaign=wp-repo&utm_medium=readme&utm_term=Finale) is the ONLY WooCommerce plugin that allows you to create urgency and scarcity inducing promotional campaigns. Urgency and scarcity are both powerful psychological triggers that motivate shoppers to take fast action and avoid missing out.

>[Next Move](https://xlplugins.com/woocommerce-thank-you-page-nextmove/?utm_source=woo-confirmation-email&utm_campaign=wp-repo&utm_medium=readme&utm_term=Nextmove) is a powerful plugin for WooCommerce that allows you to build custom Thank You pages to pull more profits.

>[Sales Triggers](https://xlplugins.com/woocommerce-sales-triggers/?utm_source=woo-confirmation-email&utm_campaign=wp-repo&utm_medium=readme&utm_term=SalesTriggers) display time-sensitive deals, low stock warnings, potential savings, sales insights & bullet-proof guarantees on product pages. Built to convert skeptical shoppers to confident buyers.

== Learn About Our Other Free Plugins: ==
>[Finale Lite](https://wordpress.org/plugins/finale-woocommerce-sales-countdown-timer-discount/)
>[Next Move](https://wordpress.org/plugins/woo-thank-you-page-nextmove-lite/)
>[UTM Leads Tracker](https://wordpress.org/plugins/utm-leads-tracker-lite/)

== Features: ==

* Native WooCommerce email template
* Customisable email template
* WPML Compatibility
* Merge tags to customise email

>Note: Over the last two weeks, we have gone through a lot of pending support tickets, improved codebase and created a list of new features.

>[XLPlugins](https://xlplugins.com/?utm_source=wc-email-verification&utm_campaign=wp-repo&utm_medium=readme) has taken over the development of this plugin. We are improving this plugin and will be pushing out new features soon.
We thank Sandeep Soni for developing this plugin and allowing us to continue its development.


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `woo commerce-confirmation-email.zip` to the add new plugins in WordPress admin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure Template and email address in WC Email Setting Menu


== Screenshots ==

1. User notice when access is not allowed.
2. User Email to verify Email (Custom)
3. User Email to verify Email (WC Native)
4. User Notice when account created using Checkout
5. Admin Settings: Email Content
6. Admin Settings: Test/Preview Email
7. Admin Settings: Customize all user notices
8. Admin Settings: Bulk Verification
9. Admin Settings: Misc Settings

== Changelog ==

= 3.5.0
* Added: Compatible with 'WooCommerce social login' plugin by SkyVerge. Auto verifying socially login users.
* Fixed: Resent email with Elementor, issue resolved.
* Improved: Some code improvement done.

= 3.4.1
* Compatible with WordPress 5.2.1.
* Compatible with WooCommerce 3.6.4.

= 3.4.0
* Fixed: Vulnerability resolved for admin settings. Thank you [Brad Griffin](https://wordpress.org/support/users/burlesonbrad/) for bringing it to our attention.

= 3.3.0
* Added: Compatible with WordPress 5.0 and 5.1.
* Added: Compatible with WooCommerce 3.5.5.
* Fixed: Security fix with login.

= 3.2.0
* Security update: Prohibited direct access.
* Added: htaccess file to block access in supportive xl folders inside uploads.
* Added: New filter option has been added for filtering the verified and unverified users from users listing screen.
* Changed: Default email subject and email heading, added display_name to them to avoid mail going into spam.
* Fixed: Changed notice 'Resend Confirmation Email' to 'Your email is already verified' if a user is verified in other window and clicks on 'Resend Confirmation Eamil' link again.
* Added: Provided new merge tag {{xlwuev_display_name}} to add in email subject to avoid spam mail.

= 3.1.15
* Fixed: Session issue.
* Added: Verification link text merge tag added for showing the verification link text.

= 3.1.14
* Fixed: Auth cookie issue has been resolved after order when user has not verified its email.
* Fixed: Verification notice issue has been resolved.

= 3.1.13
* Fixed: Huge gap issue in the verification email has been resolved for the Woocommerce native Email Header option
* Added: A link to un-verify a user

= 3.1.12
* Added: Compatibility for the verification check on forcefully login the user by setting the auth cookie is added.
* Added: wp_mail() is replaced by WooCommerce Mailer for sending verification emails.
* Added: New option for showing error messages on custom page is added in plugin settings screen.

= 3.1.11
* Fixed: Issue for adding values for the columns added in the user listing screen by other plugins has been resolved.

= 3.1.10
* Fixed: Headers already sent issue on bulk verification tab.

= 3.1.9
* Added: Textarea added for verification link text in plugin settings screen.

= 3.1.8
* Fixed: Notice on Preview Email settings tabs under WooCommerce settings has been fixed.
* Fixed: Email Deliverability has been improved.
* Added: Support for custom WooCommerce forms.

= 3.1.7
* Fixed: Resend Confirmation Email issue has been fixed.

= 3.1.6
* Fixed: Notice issue has been fixed when unverified user tries to login.
* Added: New option added to allow user to automatically logged into the My-Account after successful email verification.

= 3.1.5
* Fixed: Old shortcode of email verification link can now be used in native WooCommerce native email template.
* Added: 'xlwuev_modify_before_email' filter added for modifying the email content before sending the verification email when custom header footer option is selected.
* Added: 'xlwuev_trigger_after_email' action added after the verification email is sent.
* Added: 'xlwuev_on_email_verification' action added after the customer verifies the email.

= 3.1.4
* Fixed: Apostrophe saving issue in email subject has been fixed.
* Fixed: My-Account Redirect issue from WooCommerce order pay page has been fixed.

= 3.1.3
* Fixed: Verification link has been corrected when user has saved the native WooCommerce Welcome Email option from plugin setting screen.
* Added: Doctype & HTML body added in email to reduce the email delivery time.
* Added: Compatible with WooCommerce 3.2.4


= 3.1.2
* Fixed: Test Email plugin setting is now fixed.


= 3.1.1
* Fixed: PHP 7 compatibility issue while saving the plugin settings.


= 3.1.0
* Added: Wordpress compatibility upto version 4.9
* Modified: Changed the plugin settings into tabular UI.
* Added: Provided the option for Custom email body or WooCommerce email header, footer and styling.
* Added: Provided the option for restricting the user to login if email is not verified.
* Added: Provided the option for verification success page.
* Added: Provided the option for styling the email body with WYSIWYG editor.
* Added: Provided merge tags as {{xlwuev_user_login}} {{xlwuev_user_email}} {{xlwuev_user_verification_link}} {{xlwuev_resend_link}} {{xlwuev_site_login_link}} {{sitename}} {{sitename_with_link}}
* Added: Provided the options for showing custom messages to customers.
* Added: The plugin now supports WPML Compatibility.


= 3.0.2
* Fixed: Session issues in the notification messages (issue found against WC Vendor plugin)
* Added: In WordPress dashboard, a notification is displayed after manual user verification.
* Added: Email verification feature added on the Checkout page. A notification displayed in checkout page after verification email sent.
* Removed: In WordPress dashboard, plugin menu is removed and shifted under xlplugin menu.


= 3.0.1  =
* Fixed: XlPlugins's take over notice was not getting dismissed, link was going to wrong path.


= 3.0.0 =

* Added: New metabox added for the Message to show when a user has verified the email account ( Success Message ) and Message to show when a user has not verified email account ( Error Message ).
* Added: New metabox added to test sending emails.
* Added: New user listing column added to send a verification email to users.


= 2.4 =
Adding Bulk Verification from admin


= 2.3 =
Adding Manual Verify user from admin


= 1.1 =
* remove styling issue
* remove Resend Bug


= 1.0 =
* this is first version of plugin


= 0.5 =
* List versions from most recent at the top to oldest at the bottom.
