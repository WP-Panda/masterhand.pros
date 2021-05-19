=== Simple CSV/XLS Exporter ===

Contributors: Shambix, Dukessa, thaikolja, akforsyt
Author URL: https://www.shambix.com
Tags: csv, xls, export, excel, custom fields, custom post types, export products, export posts
Requires at least: 5
Tested up to: 5.7.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Export any content to CSV or XLS, through a link/button, from backend / frontend. Supports custom post types, WooCommerce, custom taxonomies, post statuses, users & fields.

== Description ==

This plugin allows you to export your posts to CSV or XLS file, through a simple link/button, from either backend or frontend.
**Make sure you are using PHP 7.3, if you see any errors, older versions will not be supported anymore.**

**Supports**

* any custom post type
* custom post status
* custom taxonomies
* custom fields
* WooCommerce products, orders, status, categories and fields
* export only current user posts
* export specific user ID posts
* export specific post IDs

You can set the default post type, with its taxonomies and custom fields, that you wish to export, from the Settings page.

After that, anytime you will use the urls `https://yoursite.com/?export=csv` for a CSV file, or `https://yoursite.com/?export=xls`, you will get that post type data.

"You must choose the post type and save the settings before you can see the taxonomies or custom fields for a custom post type. Once the page reloads, you will see the connected taxonomies and custom fields for the post type."

If you want to export from a different post type than the one saved in these settings, also from frontend, use the url `https://yoursite.com/?export=csv&post_type=your_post_type_slug` for a CSV file, or `https://yoursite.com/?export=xls&post_type=your_post_type_slug` to get a XLS.

**Please check the [Plugin's FAQ](https://wordpress.org/plugins/simple-csv-xls-exporter/#faq) for all possible options and available custom parameters you can use.**

When opening the exported xls, Excel will prompt the user with a warning, but the file is perfectly fine and can then be opened. Unfortunately this can't be avoided, [read more here](http://blogs.msdn.com/b/vsofficedeveloper/archive/2008/03/11/excel-2007-extension-warning.aspx).

= Questions? =

Check the FAQ before opening new threads in the forum!

> Contact me if you want a **custom version of the plugin**, for a fee (contact email on [shambix.com](http://www.shambix.com)).

* [Current Plugin on Github](https://github.com/Jany-M/simple-csv-xls-exporter)

= Credits =

* [Last forked plugin's version](https://github.com/mediebruket/custom-csv-exporter)
* [Original plugin's version](https://github.com/ethanhinson/custom-csv-exporter)

== Installation ==

1. Upload `simple-csv-xls-export.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Tools -> CSV/XLS Export to access the plugins settings and export files.

== Frequently Asked Questions ==

= How do I choose my post type, taxonomies and custom fields? =

Head over to the plugin's settings page, choose the post type. Then click "Save Changes" at the end of the page.

At this point, a list of the custom taxonomies and fields associated with that post type will appear.

Choose all of the fields you wish to export (use CTRL+click to select multiple ones) and click "Save Changes" again.

Then click on the "Export" buttons to get your CSV or XLS file.

= I don't see any custom fields on the Settings page. How come? =

You mush first choose your post type and click "Save Changes" before you can see a list of the associated custom fields.

Be sure to click "Save Changes" again in order to save your choices.

= Can I export to CSV from frontend? =
Yes, just place this URL where you want the download link/button to be: `<a class="btn" href="?export=csv">Export to CSV</a>`

This will export as per plugin Settings.

= Can I export to XLS from frontend? =
Yes, just place this URL where you want the download link/button to be: `<a class="btn" href="?export=xls">Export to XLS</a>`

This will export as per plugin Settings.

= Can I change the post type, from the frontend URL? =
Yes, use the URL var `?post_type=yourcustomposttypeslug`

Keep in mind however, that it will still look for the taxonomies and custom fields as per plugin Settings.

Eg. `https://yoursite.com/?export=xls&post_type=portfolio`

= Can I only export parents or children? =
Yes, use the URL var `?only=x`, where x is either `children` or `parents`.

Default is both.

Eg. `https://yoursite.com/?export=xls&post_type=portfolio&only=parents`

= Does it support cyrillic characters? (eg. Russian) =
Yes, but only for the CSV format (for now).

= Can I export only posts with a certain post status? =
Yes, as of v. 1.3.8.

You can set it up from the Settings page as default, or add to your url `?post_status=nameofstatus`

Eg. `https://yoursite.com/?export=xls&post_type=portfolio&post_status=draft`

= Can I export only posts from a specific user, or the current one? =
Yes, as of v. 1.4.

To export the file with content from the currently logged-in user, add this to your url `?user`

To export the file of a specific user ID, use this in your url `?user=x`, where X is the user ID you need.

Default is all users.

Eg. `https://yoursite.com/?export=xls&post_type=portfolio&user`

= Can I export specific posts? =

Yes, as of v. 1.5.

You need the parameter `?specific_posts` in the export url.

Eg. `https://yoursite.com/?export=xls&specific_posts=1,2,3`

= Can I export only posts from a specific date onward? =
Yes, as of v. 1.5.5.

To export the file with content created from a specific date, either use the global options or add this to your url `?date_min`

The date format, when using the url parameter, must be `mm-dd-yyyy`.

Eg. `https://yoursite.com/?export=csv&date_min=07-11-2020` (July 11 2020)

== Screenshots ==

1. Settings Page
2. Settings Page
3. Settings Page

== Changelog ==

= 1.5.6 = 
* Re-introduced the `ccsve_export_returns` filter hook

= 1.5.5 = 
* Added a Date global option, to export content only from that date onward
* Added `date_min` parameter (will override the global option)
* The export now ignores sticky posts

= 1.5.4.1 =

* Fixed PHP < 7 error: Argument 1 passed to simple_csv_xls_exporter_generate_file_name() must be an instance of string, string given.
* Plugin will only support PHP 7.3+ from now on.

= 1.5.3 =

* Pulled GabrielFalkoski's merge request
* Added a Hook Filter to handle content to export `ccsve_export_returns`

= 1.5.2 =

* Fix for "Warning: Use of undefined constant SIMPLE_CSV_EXPORTER_VERSION"

= 1.5.1 =

* Fixed action url from plugins page
* Fixed PHP syntax error (unexpected ?)

= 1.5 =

* Merged with thaikolja's fork and refactoring
* Added `specific_posts` parameter (Special thanks to: akforsyt)

= 1.4.9 =

* Plugin Refactoring (Special thanks to: thaikolja)
* Replaced spaces with tabs for intendation.
* Renamed classes (`SIMPLE_CSV_EXPORTER` to `Simple_CSV_Exporter`) to avoid confusion with constants.
* Added documentation to some functions and classes.
* Used strict comparison (`===`) where needed.
* Removed some comment areas used for development purposes.
* Added and rewrote some comments.
* Added textdomain and made several strings localizable.
* Restructured files, functions and classes and put them in appropriate directories.
* Renamed some constants to be more accurate.
* Added filter `simple_csv_xls_exporter_export_file_name`

= 1.4.6 =
* Added option to set custom Delimiter
* Fixed issue with html entities

= 1.4.5 =
* Fixed PHP 7.1 Fatal error 7.1 bug (Uncaught ArgumentCountError: Too few arguments to function do_settings_fields() )

= 1.4.4 =
* Minor fixes

= 1.4.3 =
* Set error_display to 0 during process

= 1.4.2 =
* Fixed bug *Parse error: syntax error, unexpected ‘[‘ in …./woo/wp-content/plugins/simple-csv-xls-exporter/process/simple_csv_xls_exporter_csv_xls.php on line 19*

= 1.4.1 =
* Added support for ANY post type (including internal/non-public ones)
* Added labels for post types instead of slug
* Added support for WooCommerce Orders
* Removed PHP format error above WP default fields settings box

= 1.4 =
* Added support for current user download
* Added support for specific user ID download

= 1.3.9.1 =
* Fixed a minor PHP notice

= 1.3.9 =
* Fixed bugs: `Illegal string offset ‘selectinput’ in simple-csv-xls-exporter\settings.php on line 147`, `in_array() expects parameter 2 to be array, string given in simple-csv-xls-exporter\settings.php on line 147`, `syntax error, unexpected ‘[‘ in simple-csv-xls-exporter/settings.php on line 157`

= 1.3.8 =
* Added support for Post Status
* Added option for Backend only export
* Minor Fixes

= 1.3.5 =
* Added support for WooCommerce
* Fixed some bugs when not finding any selected custom fields/taxonomies
* Fixed XLS not exporting correctly since 1.3.2
* Support for German characters for XLS and CSV

= 1.3.2 =
* Added url var to only export parents or children
* Added cyrillic characters support for CSV
* Restructured part of the plugin
* Saved options get removed when uninstalling plugin

= 1.3 =
* Fixed issue with new custom fields not showing
* Better caching of metas (24h)

= 1 =
* Added xls support
* Fixed bug with plugin not finding taxonomies during export because launched too early `(init->wp_loaded)`

= .4 =
* Fixed issue with SYLK format (ID in capital letters gives Excel error for CSV)
* Added url parameter `&post_type`, to use in stand-alone url

= .3 =
* Introduce taxonomy and default WordPress field export capabailities

= .2 =
* Fixed bug that limited number of posts that could be exported

= .1 =
* Initial release of plugin