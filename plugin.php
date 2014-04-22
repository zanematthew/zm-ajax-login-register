<?php

/**
 * Plugin Name: zM Ajax Login & Register
 * Plugin URI: --
 * Description: Creates a simple login and register modal with an optional shortcode.
 * Version: 1.0.5
 * Author: Zane Matthew
 * Author URI: http://zanematthew.com/
 * License: GPL V2 or Later
 */



/**
 * Plugin initial setup
 */
function zm_ajax_login_register_setup() {
  /**
   * Load plugin textdomain
   */
  $domain = 'ajax_login_register';
  $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
  if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' ) ) {
      return $loaded;
  } else {
      load_plugin_textdomain( $domain, false, basename( dirname( __FILE__ ) ) . '/languages/' );
  }
}
add_action( 'init', 'zm_ajax_login_register_setup' );


/**
 * Include our abstract which is a Class of shared Methods for our Classes.
 */
require_once 'controllers/abstract.php';


/**
 * If the admin is being displayed load the admin class and run it.
 */
if ( is_admin() ){
    require_once 'controllers/admin.php';
}


/**
 * If users are allowed to register we require the registration class
 */
require_once 'controllers/register.php';


/**
 * Load the login class
 */
require_once 'controllers/login.php';
