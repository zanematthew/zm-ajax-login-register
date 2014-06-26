=== zM Ajax Login & Register ===

Contributors: ZaneMatthew, dvk
Donate link: http://zanematthew.com/
Tags: admin, AJAX, login, manage, modal, password, plugin, redirect, register, username, Facebook
Requires at least: 3.5
Tested up to: 3.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to create a custom login and registration page or login and registration modals. Complete with AJAX verification and Facebook login support.

== Description ==

With zM Ajax Login & Register, you can create a custom login and registration page. No need for any custom post types, just create a normal WordPress page, add your own custom logo, text, and use the following shortcode `[ajax_login]`, `[ajax_register]`.

From the settings you can assign login and register modal boxes to menu items, and add a redirect URL. By assigning the login and register modals to menu items users will be able to click menu items, will display a single login or register form in a modal without any post content. Once the users login they can be redirected to a custom page, for example: "dashboard", or "welcome".

Now your visitors can login or register from the page you've set-up.

Each form has pre-set styling options and uses AJAX. From the settings you can choose either; stacked (default) or wide styling. Additional styling you can be achieved by adding your custom CSS or using one of the available hooks.
Feel free to contact us and will add any additional hooks you need. The forms use AJAX to verify that the username and email are valid and are not already in use.

If you've enabled the Facebook login or register, from your settings, the Facebook button will display in the form. Each user that logins with Facebook will be register as a "subscriber" and their Facebook profile picture will be used as their avatar.

* [Support](http://support.zanematthew.com/forum/zm-ajax-login-register/)
* [GitHub Repository](https://github.com/zanematthew/zm-ajax-login-register)

= Features =

* Now available in Russian thanks to [artbelov](https://github.com/artbelov), Spanish and French thanks to [Thomas G](https://twitter.com/Jukd)
* Facebook login support
* Redirect users to a custom URL or page after login, such as; "Dashboard" or "Welcome"
* AJAX verification for username and email accounts
* Choose between different styles: stacked (default) or wide
* Advanced usage includes: Assign login and register modals to menu items, support for custom CSS, several hooks are available as well

= Usage =

**Note your site will need to be open to registration**

1. Create a page
1. Add the following shortcode `[ajax_login]` or `[ajax_register]`

Advanced usage allows you to use any menu item to launch the login and register modal boxes.

**Note your page must support custom menus**

1. Create a menu item; such as "login" you can set the URL to # if need be
1. Assign a unique class name to the menu item. If you do not see the "class name section", click on the "Screen options" in the upper corner and check the box for "CSS Classes"
1. Copy/paste the class name you just assigned to the menu item
1. Save the changes for the new menu item
1. Visit the settings page from the WordPress Admin (Settings --> Ajax Login & Register)
1. Paste the CSS class name in the appropriate field, either "Login Handle" or "Register Handle"
1. Save the settings

**Additionally you can assign a URL to a page the user is redirected to once logged in. The default is the site home page**

== Installation ==

1. Install the plugin via WordPress or download and upload the plugin to the `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a page called "login" or "register"
1. Add the shortcode `[ajax_login]` or `[ajax_register]` (make sure your site is set to "anyone can register") to any post or page


== Frequently Asked Questions ==

= How do I disable registration? =

From the WordPress admin click on Settings --> General and uncheck "Anyone can register"

= Why does the Facebook button not load? =

Ensure that you have the setting "Enable Facebook Login" checked.

= Facebook login isn't working? =

Please check your settings in WordPress Admin -> Settings -> AJax Login & Register with the settings found
here: https://developers.facebook.com/apps/YOUR_APP_ID/summary


== Screenshots ==

1. Login modal with Facebook enabled
2. Registration modal with Facebook enabled
3. Login page (used with shortcode)
4. Registration page (alternate layout, used with shortcode)
5. Registration modal (alternate style)
6. Settings

== Upgrade Notice ==

* Check settings


== Changelog ==

= 1.0.7 =
 * Enhancement: Facebook OG tags should no longer be duplicate, they can be left blank if another plugin is already loading them [#42](https://github.com/zanematthew/zm-ajax-login-register/issues/42)
 * Enhancement: UI for enabling and disabling user registration button, [#44](https://github.com/zanematthew/zm-ajax-login-register/issues/44).
 * Enhancement: Login dialog has a link to open the registration dialog and vice versa [issue #16](https://github.com/zanematthew/zm-ajax-login-register/issues/16)
 * Bug: Improved mobile support for dialogs, [#46](https://github.com/zanematthew/zm-ajax-login-register/issues/46)

= 1.0.6 =

 * Enhancement: Dialog/Modal is now closed if the user scrolls, issue [#34](https://github.com/zanematthew/zm-ajax-login-register/issues/34)
 * Enhancement: Dialog/Modal is no longer draggable, issue [#35](https://github.com/zanematthew/zm-ajax-login-register/issues/35)
 * Enhancement: French translation issue [#33](https://github.com/zanematthew/zm-ajax-login-register/pull/33) Thanks too [@jukd](https://twitter.com/jukd)
 * Addressed compatibility issues with Easy-to-use issue [#38](https://github.com/zanematthew/zm-ajax-login-register/issues/38)
 * Fixed bug: Facebook Meta Tags issue [#41](https://github.com/zanematthew/zm-ajax-login-register/issues/41)
 * Enhancement: Added Spanish translation

= 1.0.5 =

 * Added Multi-Language Support.
 * Added Russian translation.
 * Created .POT file.

= 1.0.4 =

 * Added Multi-Language Support.
 * Added Russian translation.
 * Created .POT file.

= 1.0.3 =

* When Login modal is used the Login link changes text to say "Logout" once the user is logged in, [#9](https://github.com/zanematthew/zm-ajax-login-register/issues/9)
* Removing duplicate IDs [#14](https://github.com/zanematthew/zm-ajax-login-register/issues/14)
* Localizing JS, [https://github.com/zanematthew/zm-ajax-login-register/issues/13](#13)
* Fixing issue where users could not register when the modal and shortcode was in use at the same time, [#11](https://github.com/zanematthew/zm-ajax-login-register/issues/11)
* Bug: Fixed issue where Facebook login did not work on the registration page, [#12](https://github.com/zanematthew/zm-ajax-login-register/issues/12)

= 1.0.2 =

* Improved `readme.txt`
* Feature: Added WordPress networking support
* Bug: Fixing issue when Facebook login did not work with latest WordPress
* Bug: Shortcode now returns HTML rather than printing it
* Security: Enhanced security for credentials when creating Facebook users


= 1.0.1 =

* Added setting to disable/enable "keep me logged in" checkbox
* Added filter `zm_ajax_login_redirect`, which allows developers to change the redirect url.
* 3.6 Styling - z-index issue
* 3.6 Styling - Close button no longer has default focus and default styling


= 1.0.0 =

* Initial release
