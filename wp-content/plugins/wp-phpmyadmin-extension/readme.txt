=== WP phpMyAdmin ===

Tags			 : phpmyadmin,phpminiadmin,mysql,sql,database,manager,management,browser
Stable tag		 : 5.0.4.1
WordPress URI	 : https://wordpress.org/plugins/wp-phpmyadmin-extension/
Plugin URI		 : https://puvox.software/wordpress/
Contributors	 : puvoxsoftware,ttodua
Author			 : Puvox.software
Author URI		 : https://puvox.software/
Donate link		 : https://paypal.me/puvox
License			 : GPL-3.0
License URI		 : https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 4.4
Tested up to	 : 5.5.3

[ ✅ 𝐒𝐄𝐂𝐔𝐑𝐄 𝐏𝐋𝐔𝐆𝐈𝐍𝐒 b𝓎 𝒫𝓊𝓋𝑜𝓍 ]
phpMyAdmin -  Database Browser & Manager (for MySQL & MariaDB)

== Description ==
= [ ✅ 𝐒𝐄𝐂𝐔𝐑𝐄 𝐏𝐋𝐔𝐆𝐈𝐍𝐒 b𝓎 𝒫𝓊𝓋𝑜𝓍 ] : =
> • Revised for security to be reliable and free of vulnerability holes.
> • Efficient, not to add any extra load/slowness to site.
> • Don't collect private data.
= Plugin Description =
The famous database browser & manager (for MySQL & MariaDB) - use it inside WordPress Dashboard without an extra hassle.

== NOTES ==
* PHP >= 7.1.3 is required to for <strong>phpMyAdmin</strong> latest version (otherwise you will have option to use older version of PMA, which is not encouraged for usage).
* This plugin has been started from 2018 year, and we have no relations to the old age's (from 3rd party scammers) vulnerable <b>wp-phpMyAdmin</b> plugin. This plugin is just a wrapper for official phpMyAdmin release and depends itself on the realiability & security of the `phpMyAdmin` itself. Also, initially we wanted to put PhpMyAdmin release `.zip` file (with same checksum, to ensure the checksums are same) to unpack that `.zip` directly upon plugin's installation, but unfortunately WordPress.Org plugin team didn't allow to put `.zip` file in the package (saying that SVN doesn't like .zip files). Thus, we had to submit original PMA (extracted, untouched) to the repository.
* For the reason to make it compact, some extra or unnecessary files (language files,GIS map, etc) are removed.


= Liability =
We are not official developers of PhpMyAdmin, neither affiliated with them. We just made this plugin as a container of PhpMyAdmin, so, people could use it in WP. This plugin uses official, unmodified (only whatever mentioned above) PhpMyAdmin package. However, we don't control, check or revise the PhpMyAdmin package's source code ot behavior itself, and thus, in overall, we take no responsibility about this plugin. Use it at your own responsibility. Only around 1% of users complained about this not to be working in any environment or causing any problems.

= Available Options =
See all available options and their description on plugin's settings page.


== Screenshots ==
1. screenshot


== Installation ==
A) Enter your website "Admin Dashboard > Plugins > Add New" and enter the plugin name
or
B) Download plugin from WordPress.org , Extract the zip file and upload the container folder to "wp-content/plugins/"


== Frequently Asked Questions ==
- More at <a href="https://puvox.software/software/wordpress-plugins/">our WP plugins</a> page.


== Changelog ==

= 3.02 =
* PMA 5.0.2 

= 3.0 =
* Added latest PMA: version 5.XX (supported on PHP >= 7.1.3) and version 4.XX (supported on PHP >= 5.6 )

= 1.01 - 2.99 =
* Numerous updates and changes

= 1.0 =
* First release.