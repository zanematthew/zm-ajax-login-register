<?php

/**
 * Plugin Name: zM Ajax Login & Register
 * Plugin URI: --
 * Description: Creates a simple login and register modal with an optional shortcode.
 * Version: 1.0.4
 * Author: Zane Matthew
 * Author URI: http://zanematthew.com/
 * License: GPL V2 or Later
 */



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

/**
 * Plugin initial setup and constants
 */
function zm_ajax_login_register_setup() {
  /**
   * Make plugin available for translation
   */
  load_theme_textdomain( 'ajax_login_register', dirname(__FILE__) . '/lang' );
}
add_action( 'after_setup_theme', 'zm_ajax_login_register_setup' );