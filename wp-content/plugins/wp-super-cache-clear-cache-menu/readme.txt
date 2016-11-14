=== WP Super Cache - Clear all cache ===
Contributors: apasionados, netconsulting
Donate link: http://apasionados.es/
Tags: empty cache, emtpy wp super cache, cache, caching, performance, wp-cache, wp-super-cache, web performance optimization, WPO, YUI, yslow, google speed
Requires at least: 3.0.1
Tested up to: 4.6
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Clear all cache for WP Super Cache adds a menu entry to the admin menu to clear the cache completely .

== Description ==

The plugin clears completely  the cache from WP Super Cache, directly from the admin menu.

We created this plugin, in order to be able to clear the cache completely  from the admin menu and not having to navigate to CONTENTS tab in WP Super Cache Settings.

In some configuration WP Super Cache shows a menu entry to empty the cache in the menu, but it only empties the cache from the current page. See: [The "Delete Cache" button on the admin menu only deletes the cache for the current page.](http://wordpress.org/support/topic/plugin-wp-super-cache-menu-delete-cache-doesnt-work-but-on-contents-page-it-does)
Nevertheless in the majority of the sites that have WP Super Cache installed it doesn't display this menu entry.

Please note that this menu option is only visible for WordPress ADMINISTRATORS.

> <strong>This is an add-on to WP SUPER CACHE:</strong><br>
> Please keep in mind that this plugin is an add-on to the [WP SUPER CACHE](https://wordpress.org/plugins/wp-super-cache/) plugin. You need to install and activate WP SUPER CACHE so that this add-on works.
>
> If you are having trouble with this plugin, feel free to use the [WP Super Cache - Clear all cache Support](https://wordpress.org/support/plugin/wp-super-cache-clear-cache-menu). If you are having problems with the WP SUPER CACHE plugin, you should ask for support at [WP Super Cache Support](https://wordpress.org/support/plugin/wp-super-cache).

= What can I do with this plugin? =
The plugin clears completely the cache from WP Super Cache, directly from the admin menu.

= WP Super Cache - Clear all cache Plugin in your Language! =
This first release is avaliable in English and Spanish. In the languages folder we have included the necessarry files to translate this plugin.

If you would like the plugin in your language and you're good at translating, please drop us a line at [Contact us](http://apasionados.es/contacto/index.php?desde=wordpress-org-wp-super-cache-clear-cache-menu-home).

= Further Reading =
You can access the description of the plugin in Spanish at: [WP Super Cache - Clear all cache Plugin en castellano](http://apasionados.es/blog/vaciar-cache-wp-super-cache-plugin-wordpress-1933/).


== Installation ==

1. Upload the `wp-super-cache-clear-cache-menu` folder to the `/wp-content/plugins/` directory (or to the directory where your WordPress plugins are located)
1. Activate the WP Super Cache - Clear all cache Plugin through the 'Plugins' menu in WordPress.
1. Puling doesn't need any configuration.

Please don't use it with WordPress MultiSite, as it has not been tested.

== Frequently Asked Questions ==

= What is WP Super Cache - Clear all cache Plugin good for? =
It empties completely the WP SUPER CACHE cache without having to navigate to the CONTENTS tab in WP Super Cache Settings, this plugin is for you.

= Why should I make use of the WP Super Cache - Clear all cache Plugin? = 
If you use WP Super Cache and want to be able to empty completely the cache without having to navigate to the CONTENTS tab in WP Super Cache Settings, this plugin is for you.

= Do I need to install the WP Super Cache plugin to make this one work? =
Yes. This is an add-on of the [WP SUPER CACHE](https://wordpress.org/plugins/wp-super-cache/) plugin. You need to install and activate this plugin before you can use our plugin.

= Are you the developers of the WP SUPER CACHE plugin? =
No we are not. We have only developed this add-on to make life with WP Super Cache easier for everybody that uses it.

= Does WP Super Cache - Clear all cache make changes to the database? =
No.

= How can I check out if the plugin works for me? =
Install and activate. You will see a new menu entry in the administration area to empty the cache. Access this menu entry and your cache will be emptied and you are taken to the CONTENTS tab in WP Super Cache Settings.

= What can I do if I need support? =
If you are having trouble with this plugin, feel free to use the [WP Super Cache - Clear all cache Support](https://wordpress.org/support/plugin/wp-super-cache-clear-cache-menu). If you are having problems with the WP SUPER CACHE plugin, you should ask for support at [WP Super Cache Support](https://wordpress.org/support/plugin/wp-super-cache) because they will be able to help you better.

= Can every registered user use this plugin? =
No. This menu option is only available for WordPress ADMINISTRATORS.

= How can I remove WP Super Cache - Clear all cache? =
You can simply activate, deactivate or delete it in your plugin management section.

= What happens if I activate the plugin without using WP SUPER CACHE? =
Nothing. The plugin checks that WP SUPER CACHE is activated.

= Are there any known incompatibilities? =
Please don't use it with WordPress MultiSite, as it has not been tested.

= Do you make use of WP Super Cache - Clear all cache yourself? = 
Of course we do. ;-)

== Screenshots ==

1. There is no configuration screen for "WP Super Cache - Clear all cache". This is a ScreenShot of the menu entry that it adds (in Spanish).
2. This second example shows the menu entry from WP Super Cache we see on some installations to empty the cache of the current page. See: [The "Delete Cache" button on the admin menu only deletes the cache for the current page.](http://wordpress.org/support/topic/plugin-wp-super-cache-menu-delete-cache-doesnt-work-but-on-contents-page-it-does)

== Changelog ==

= 1.4.0 =
* Changed function_exists('is_plugin_active') handling so that it doesn't load when outside of the WordPress administration.

= 1.3.1 =
* Minor update to fix a problem with automatic update from WordPress plugin repository.
* Corrected two typos. Thanks to Kevin W. McCarthy.

= 1.3 =
* Updated the code to fix the bug inspirationdate found: "The link added is relative so it doesn't work when the toolbar is displayed on the frontend".

= 1.2 =
* Updated and corrected readme.txt.

= 1.1 =
* Updated and corrected readme.txt.

= 1.0 =
* First stable release.

= 0.5 =
* Beta release.

== Upgrade Notice ==

= 1.4 =
Updated the code to fix a possible problem with checking if WP SUPER CACHE is active.

== Contact ==

For further information please send us an [email](http://apasionados.es/contacto/index.php?desde=wordpress-org-wp-super-cache-clear-cache-menu-contact).
