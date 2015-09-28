=== ZM Ajax Login & Register ===

Contributors: ZaneMatthew, dvk
Donate link: http://zanematthew.com/donate/
Tags: admin, AJAX, login, manage, modal, password, plugin, redirect, register, username, Facebook
Requires at least: 3.5
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to create a custom login and registration page or login and registration modals. Complete with AJAX verification and Facebook login support.

== Description ==

With ZM Ajax Login & Register, you can create a custom login and registration page. No need for any custom post types, just create a normal WordPress page, add your own custom logo, text, and use the following shortcode `[ajax_login]`, `[ajax_register]`.

From the settings you can assign login and register modal boxes to menu items, and add a redirect URL. By assigning the login and register modals to menu items users will be able to click menu items, will display a single login or register form in a modal without any post content. Once the users login they can be redirected to a custom page, for example: "dashboard", or "welcome".

Now your visitors can login or register from the page you've set-up.

Each form has pre-set styling options and uses AJAX. From the settings you can choose either; stacked (default) or wide styling. Additional styling you can be achieved by adding your custom CSS or using one of the available hooks.
Feel free to contact us and will add any additional hooks you need. The forms use AJAX to verify that the username and email are valid and are not already in use.

If you've enabled the Facebook login or register, from your settings, the Facebook button will display in the form. Each user that logins with Facebook will be register as a "subscriber" and their Facebook profile picture will be used as their avatar.

= Features =

* Facebook login support
* Redirect users to a custom URL or page after login, such as; "Dashboard" or "Welcome"
* AJAX verification for username and email accounts
* Choose between different styles: stacked (default) or wide
* Advanced usage includes: Assign login and register modals to menu items, support for custom CSS, several hooks are available as well
* Now available in Russian thanks to [artbelov](https://github.com/artbelov), Spanish and French thanks to [Thomas G](https://twitter.com/Jukd)

= Additional Features =

Interested in more features? View our [Pro Version](http://zanematthew.com/products/zm-ajax-login-register-pro/), which includes the following:

* Force user login – Create your own private site
* Independent Redirect – Send existing users to a "dashboard" page, or new users to a "welcome" page.
* Free dedicated support for 1-year
* Additional features to come!

= Translations =

ZM AJAX Login & Register is available in the following languages. Thanks to the respective contributors:

* Albanian via [shpberisha](http://www.twitter.com/shpberisha)
* Brazilian Portuguese via [PageLab Pull Request](https://github.com/zanematthew/zm-ajax-login-register/pull/59)
* Chinese (front end only), via [Pull Request](https://github.com/zanematthew/zm-ajax-login-register/pull/71)
* Croatian via Fran
* French via [@jukd](https://twitter.com/jukd)
* Italian via [@FilippoAceto](http://www.filippoaceto.it/)
* Polish via [Abdul](http://www.couponmachine.in)
* Persian via [Behzad](https://www.facebook.com/theme20)
* Romanian via [Sandu](http://www.vtube.ro/)
* Russian (Russian) via [artbelov](https://github.com/artbelov)
* Russian (Ukraine) via Ivanka from [Coupofy](http://www.coupofy.com/)
* Serbian via [ogi](http://firstsiteguide.com/)
* Spanish via [dvk](http://profiles.wordpress.org/dvk/)


Interested in contributing a translation? Please reach out to us via:

* Send us a [tweet](http://twitter.com/zanematthew)
* Submit a Pull Request via [GitHub](http://github.com/zanematthew/zm-ajax-login-register)
* Direct [contact](http://zanematthew.com/contact)

= Usage =

*Note your site will need to be open to registration.*

**Shortcode**

The following implies for creating a login or register page.

1. Create a page
1. Add the following shortcode `[ajax_login]` or `[ajax_register]`
1. Publish the page


**Facebook Integration**

1. Create a Facebook App
1. Add your Facebook App ID
1. Add the URL your Facebook App is associated with

Additionally we've created [AJAX Login & Register WordPress Plugin help videos](http://zanematthew.com/ajax-login-register-help-videos/) to aid in the process


**Dialogs/Modals**

*Note your page must support custom menus*

1. Create a menu item; such as "login" you can set the URL to # if need be
1. Assign a unique class name to the menu item. If you do not see the "class name section", click on the "Screen options" in the upper corner and check the box for "CSS Classes"
1. Copy/paste the class name you just assigned to the menu item
1. Save the changes for the new menu item
1. Visit the settings page from the WordPress Admin (Settings --> Ajax Login & Register)
1. Paste the CSS class name in the appropriate field, either "Login Handle" or "Register Handle"
1. Save the settings

*Additionally you can assign a URL to a page the user is redirected to once logged in. The default is the site home page.*

= Support =

Please use the following resources for support.

* [Dedicated Support Forum](http://support.zanematthew.com/forum/zm-ajax-login-register/)
* [GitHub Repository](https://github.com/zanematthew/zm-ajax-login-register)

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

Additionally you can view our [AJAX Login & Register WordPress Plugin help videos](http://zanematthew.com/ajax-login-register-help-videos/)

= I've added the shortcode, yet I see a message that says "Registration is currently closed."? =

Your seeing this message because your site is not open for registration. In order to open your site for registration, do the following:

*Note the following does **not** apply to WordPress Networking. For WordPress Networking please consult your Network admin.*

1. Log into your WordPress admin
1. Click on "Settings"
1. Click on "General"
1. Click the checkbox for "Anyone can register"
1. Click "Save"

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

= 2.0.1 =

 * Adding Swedish translation, [#123](https://github.com/zanematthew/zm-ajax-login-register/issues/123)
 * Bug: Fixed issue where additional styling (textarea CSS) did not show, [#124](https://github.com/zanematthew/zm-ajax-login-register/issues/124)
 * Adding Polish translation, [#125](https://github.com/zanematthew/zm-ajax-login-register/issues/125)

= 2.0.0 =

 * Settings page is now tabbed based.
 * Settings are now optimized.
 * Enhancement: Clicking outside of a dialog now closes the dialog

= 1.1.1 =

 * Translation: Adding Ukrainian via Ivanak from [Coupofy](http://www.coupofy.com/)
 * Translation: Adding simplified Chinese [via Pull request](https://github.com/zanematthew/zm-ajax-login-register/pull/104)
 * Enhancement: Facebook Avatar support [pull #109](https://github.com/zanematthew/zm-ajax-login-register/pull/109)
 * Enhancement: New (translatable) Facebook login button
 * Enhancement: Utilizing less user meta [pull #107](https://github.com/zanematthew/zm-ajax-login-register/pull/107)


= 1.1.0 =

 * Security fix to prevent a local file inclusion vulnerability, and XSS attacks.

= 1.0.9 =

 * Maintenance: Removed deprecated function, [#103](https://github.com/zanematthew/zm-ajax-login-register/issues/103)
 * Maintenance: Removing unused options, [#53](https://github.com/zanematthew/zm-ajax-login-register/issues/53)
 * Translation: Added Romanian translation via (Dumitru)[http://www.vtube.ro], [#100](https://github.com/zanematthew/zm-ajax-login-register/issues/100)
 * Translation: Added Italian translation via Francesco D'Alia, [#99](https://github.com/zanematthew/zm-ajax-login-register/issues/99)
 * Translation: Adding German translation, via Jonas Meier
 * Feature: Disabled global AJAX callbacks, [#98](https://github.com/zanematthew/zm-ajax-login-register/issues/98)
 * Feature: Add new setting to pre-load dialog boxes, [#80](https://github.com/zanematthew/zm-ajax-login-register/issues/80)
 * Enhancement: Adding placeholder values for front end forms, [88](https://github.com/zanematthew/zm-ajax-login-register/issues/88)
 * Compatibility: Adding better support for Limit Login Attempts, and other plugins that interact with the login, register process, [#77](https://github.com/zanematthew/zm-
 * Compatibility: Removed the not needed Facebook meta og title tag, [#89](https://github.com/zanematthew/zm-ajax-login-register/issues/89)
 * Bug: Disabled auto-complete for login and password field, [#97](https://github.com/zanematthew/zm-ajax-login-register/issues/97)
 * Bug: Better sanitizing of Facebook App ID, [#96](https://github.com/zanematthew/zm-ajax-login-register/issues/96)
 * Bug: Better handling of default redirect, [#95](https://github.com/zanematthew/zm-ajax-login-register/issues/95)
 * Bug: Fixed bug that prevented default new user notices from being sent, [#92](https://github.com/zanematthew/zm-ajax-login-register/issues/92)
 * Bug: Allowing for case sensitive passwords, [#82](https://github.com/zanematthew/zm-ajax-login-register/issues/82)
 * Bug: Addressed issue with duplicate HTML IDs, [#14](https://github.com/zanematthew/zm-ajax-login-register/issues/14)

= 1.0.8 =

 * Bug: Fixing broken link, [#66](https://github.com/zanematthew/zm-ajax-login-register/issues/66)
 * Bug: Close text in modals is now translation ready, [#62](https://github.com/zanematthew/zm-ajax-login-register/issues/62)
 * Bug: Fixing PHP Strict Standards issue, [#56](https://github.com/zanematthew/zm-ajax-login-register/issues/56)
 * Enhancement: Name spacing classes
 * Enhancement: Various UI improvements, [#58](https://github.com/zanematthew/zm-ajax-login-register/pull/58)
 * Enhancement: Facebook Developer and support links open in new window, updated support link
 * Enhancement: Adding plugin notices, [#68](https://github.com/zanematthew/zm-ajax-login-register/issues/68)
 * Enhancement: Updated various parts of the plugin description, [#70](https://github.com/zanematthew/zm-ajax-login-register/issues/70)
 * Translation: Adding Serbian translation via [ogi](http://firstsiteguide.com/), [#63](https://github.com/zanematthew/zm-ajax-login-register/issues/63)
 * Translation: Adding Albanian translation via [shpberisha](http://www.twitter.com/shpberisha)
 * Translation: Updating Russian translation via [artbelov](https://github.com/artbelov)
 * Translation: Adding Brazilian Portuguese
 * Translation: Adding Persian language
 * Translation: Adding Croatian
 * Translation: Adding traditional Chinese (front end only), via [Pull Request](https://github.com/zanematthew/zm-ajax-login-register/pull/71)

= 1.0.7 =

 * Enhancement: Added "usage" section to the settings page, [#21](https://github.com/zanematthew/zm-ajax-login-register/issues/21)
 * Enhancement: Added Facebook Login instructions, [#51](https://github.com/zanematthew/zm-ajax-login-register/issues/51)
 * Enhancement: Improved Facebook Login set-up by updating to v2.0 SDK, [#50](https://github.com/zanematthew/zm-ajax-login-register/issues/50)
 * Enhancement: Facebook Login with phone number is now supported, [#52](https://github.com/zanematthew/zm-ajax-login-register/issues/52)
 * Enhancement: Facebook Login now saves; first name, last name, and Facebook URL with the user profile, [#50](https://github.com/zanematthew/zm-ajax-login-register/issues/50)
 * Enhancement: Facebook OG tags should no longer be duplicate, they can be left blank if another plugin is already implementing them [#42](https://github.com/zanematthew/zm-ajax-login-register/issues/42)
 * Enhancement: New users can now link to the Registration form from the Login form and existing users can link from the Registration form to the Login form, [#16](https://github.com/zanematthew/zm-ajax-login-register/issues/16)
 * Enhancement: Improved user registration interaction, [#44](https://github.com/zanematthew/zm-ajax-login-register/issues/44).
 * Enhancement: Improved support for Login and Register dialogs on mobile devices, [46](https://github.com/zanematthew/zm-ajax-login-register/issues/46)
 * Bug: Fixing bug were some users reported user name existed with Facebook Login, [#48](https://github.com/zanematthew/zm-ajax-login-register/issues/48)

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
