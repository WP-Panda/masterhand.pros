=== Narwhal Microblog ===
Contributors: wilcosky
Tags: micro, microblog, frontend, front-end, front end, post, form, quick post, minimal, lightweight, posting, narwhal
Requires at least: 2.7
Tested up to: 5.6
Stable tag: 2.2.11
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Narwhal magically turns most themes into powerful microblogs.

== Description ==

Narwhal magically turns most themes into powerful microblogs. It is lightweight, simple, and it will take on your theme's look and feel (you may need to tweak with your own CSS). It adds a front-end new post area on your blog's main page (or other pages if you so desire). 

Narwhal is a modified version of Posthaste by Jon Smajda.

A few notes about the plugin's behavior: 

* Select which fields you want to appear in the form and what pages you want the form to appear on. Settings are in "Settings -> Writing -> Narwhal Settings."
* If you leave the "Title" field blank, it takes the first 40 characters of the post content and makes that the title.
* If you leave the "Category" box at its default setting it posts to your default category. _However..._
* If you have a category named 'asides' it will put posts with empty titles into the 'asides' category even if you do not explicitly specify the 'asides' category in the dropdown. You can then [style them as asides](http://codex.wordpress.org/Adding_Asides).
* The included CSS is deliberately simple. If your theme already styles forms, it will probably inherit your theme's styling. If you want to customize the appearance of the form, just customize your own css files. For example, target `#narwhalForm` in your custom CSS to change the width.
* Your blog must have at least one post for the form to appear.

== Installation ==

Just upload the `narwhal-microblog` directory to `/wp-content/plugins/` and activate. You will find some basic settings within your Writing settings.

== Frequently Asked Questions ==

= Can I customize the automatic 'asides' behavior? =

The plugin will automatically make any post categorized as aside, an aside. You can change the category name at the top of the narwhal-microblog.php file.

= Help! I activated the plugin but the form isn't showing up! =

It's possible your theme has `get_sidebar()` placed _before_ the loop at the top of your theme (Most themes call `get_sidebar()` after the loop, but some do it before). This plugin attaches the form at the start of the loop, which usually works fine. In order to prevent the "Recent Posts" widget (or any other widgets which call [multiple loops](http://codex.wordpress.org/The_Loop#Multiple_Loops)) from _also_ causing the form to display, the plugin deactivates the form once `get_sidebar()` is called. So if `get_sidebar()` runs first, the form won't appear in the "real loop" either.

If you're willing to edit your theme's `index.php` file, the fix is easy. Just place the following where you want the form to appear on your page (probably right before [the loop](http://codex.wordpress.org/The_Loop)):

`<?php if(function_exists(posthasteForm)) { posthasteForm(); } ?>`

Another option, if you have no other loops called on a page and would rather edit the plugin instead of your theme, is to comment out the action that removes the loop at `get_sidebar()`. Find the following line near the bottom of the plugin:

`add_action('get_sidebar', removePosthasteInSidebar);`

and comment it out by adding two slashes at the beginning of the line:

`//add_action('get_sidebar', removePosthasteInSidebar);`

== Screenshots ==
 
1. Post form on a mobile device with Twenty Twenty-One theme.

== Changelog ==

= 2.2 =
* Fixed new PHP warnings if PHP 7.2 or 7.3 is used. Only tested with PHP up to 7.3.x. Also, removed JavaScript which was no longer being used and tweaked the minimal default styling. Tested on WordPress 5.6 with Twenty Twenty-One theme.

= 2.1 =
* Removed an unnecessary HTML bold tag that was hanging out in the post form's intro, and added a success message which will appear after successfully publishing a post.

= 2.0 =
* All new plugin essentially. Version 2.0 adds a title, category, and draft post option, along with admin settings.

= 1.0 = 
* First release. A simple textarea and tag field.
