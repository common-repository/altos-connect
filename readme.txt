=== Altos Connect Widget ===

Version: 1.3.0
Stable tag: trunk
Tested up to: 4.1
Requires at least: 2.7
Plugin Name: Altos Connect

Author: AltosResearch.com
Contributors: AltosResearch
Author URI: http://www.altosresearch.com/
License: http://www.gnu.org/licenses/gpl-2.0.txt
Plugin URI: http://www.altosresearch.com/wordpress-plugins
Tags: widget, widgets, altos, altos research, altosresearch, real estate, property, form, leads
Description: Altos Connect registration widget for WordPress®.

Altos Connect registration widget for WordPress®. The Altos Connect plugin can be used to display your contact registration form in the sidebar of your blog. Once installed and configured, visitors to your blog will be able to subscribe directly to your personalized reports.

== Installation ==

1. Upload the `/altos-connect` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress®.
3. Navigate to `Appearance->Widgets` and add the widget.

== Description ==

Altos Connect registration widget for WordPress®.

== Screenshots ==

1. Altos Connect Widget / Screenshot #1
2. Altos Connect Widget / Screenshot #2

== Changelog ==

= 1.3.0  = 
* Updated to confirm working with WP 3.6 and updated icons

= 1.2.8r = 
* Updated jQuery Autotab extension to fix IDX conflicts.

= 1.2.7r = 
* Updated login to also have login features as the other two plugins

= 1.2.7 =
* Updated login feature

= 1.2.6 = 
* Removed some validation and auto tab from the scripts to being loaded to temp fix the media player issue introduced with Wordpress 3.5, fix not available

= 1.2.5 =
* Testing to fix if searchable by wordpress.com plugin feature

= 1.2.4 =
* A hidden Capchya system has been integrated into Altos Connect form submissions. This works behind-the-scene to prevent spammers/bots from submitting Altos Connect forms on your site. Please note, this has been integrated `silently` behind-the-scene; you won't see it - there is nothing visual.

= 1.2.3 =
* Bug fix. A bug was discovered in the `listreports` / `fetch_url_contents()` method; first introduced in v1.2.1. This bug was affecting installation servers that have `allow_url_fopen = off`. If you were using the Altos Connect widget in conjunction with other widgets, on a server that has `allow_url_fopen = off`; the Altos Connect widget would cause a fatal error. This bug has been resolved in v1.2.3.

= 1.2.2 =
* Added support for WordPress® 3.0 `wp_loaded`.
* Fully tested in WordPress® 3.0, including with `MULTISITE` ( i.e. networking ) enabled. Everything looks good.

= 1.2.1 =
* Bug fixed in `validate_pai()` method. This function now returns null on failure.
* Support for both `file_get_contents()`, and a fallback on cURL has been established through a new method, `fetch_url_contents()`. This makes the plugin compatible with GoDaddy, Dreamhost, and other hosts that disable `allow_url_fopen` by default.
* A new administrative notice is displayed on the Plugins Panel whenever `pai` authentication is non-existent or invalid.

= 1.2 =
* Initial release on WordPress.org.
