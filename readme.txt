=== zM Ajax Login & Register ===

Contributors: ZaneMatthew
Donate link: http://zanematthew.com/
Tags: admin, AJAX, login, manage, modal, password, plugin, redirect, register, username
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to create a custom login and registration page, complete with login and registration modals.

== Description ==

With zM Ajax Login & Register, you can create a custom login and registration page using the following shortcode `[ajax_login]`, `[ajax_register]` or you can use modal boxes in the "Advanced Usage" setting.

= Features =

* Facebook login support
* Redirect users to a custom URL or page after login, such as; `/dashboard/` or `/welcome/`
* AJAX verification for username and email accounts
* Choose between the default (stacked) or wide style
* Advanced usage includes: custom triggers for login and register modals, support for custom CSS

= Usage =

Add the following shortocde to a post or page `[ajax_login]` or `[ajax_register]` (make sure your site is set to "Anyone can register" for this shortcode).

Advanced usage allows you to use any `HTML` element to trigger the login and register modal boxes. Simply visit the settings page from the WordPress Admin (Settings --> Ajax Login & Register) and in the section "Advanced Usage" enter the HTML tag, class name or ID of the item you want to trigger the modal box.

== Installation ==

1. Install the plugin via WordPress or download and upload the plugin to the `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the shortcode `[ajax_login]` or `[ajax_register]` (make sure your site is set to "anyone can register") to any post or page

= Optional =

* To customize, add custom CSS in the textarea found in settings
* To use the modal boxes, add the name of the desired element that will trigger the boxes in your settings.

== Frequently Asked Questions ==

= How do I disable registraion? =

From the WordPress admin click on Settings --> General and uncheck "Anyone can register"

= Why does the Facebook button not load? =

Ensure that you have the setting "Enable Facebook Login" checked.

= Facebook login isn't working? =

Please check your settings in WordPress Admin -> Settings -> AJax Login & Register with the settings found
here: https://developers.facebook.com/apps/YOUR_APP_ID/summary


== Screenshots ==

1.
2.
3.


== Upgrade Notice ==

* Check settings


== Changelog ==
= 1.1 =
* Initial release