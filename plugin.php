<?php

/**
 * Plugin Name: zM Ajax Login & Register
 * Plugin URI: http://zanematthew.com/products/zm-ajax-login-register/
 * Description: Creates a simple login and register modal with an optional shortcode.
 * Version: 1.0.8
 * Author: Zane Matthew
 * Author URI: http://zanematthew.com/
 * License: GPL V2 or Later
 */



/**
 * Plugin initial setup
 */
function zm_ajax_login_register_init(){
    // Set up localization
    load_plugin_textdomain( 'ajax_login_register', false, plugin_basename(dirname(__FILE__)) . '/languages' );
}
add_action( 'init', 'zm_ajax_login_register_init' );


function zm_ajax_login_register_enqueue_scripts(){

    $dependencies = array(
        'jquery',
        'jquery-ui-dialog'
    );

    // Generic
    wp_enqueue_style( 'jquery-ui-custom', plugin_dir_url( __FILE__ ) . "assets/jquery-ui.css" );
    wp_enqueue_style( 'ajax-login-register-style', plugin_dir_url( __FILE__ ) . "assets/style.css" );
    wp_enqueue_script( 'ajax-login-register-script', plugin_dir_url( __FILE__ ) . 'assets/scripts.js', $dependencies  );

    // Login
    wp_enqueue_style( 'ajax-login-register-login-style', plugin_dir_url( __FILE__ ) . "assets/login.css" );
    wp_enqueue_script( 'ajax-login-register-login-script', plugin_dir_url( __FILE__ ) . 'assets/login.js', $dependencies  );

    // Register
    wp_enqueue_style( 'ajax-login-register-register-style', plugin_dir_url( __FILE__ ) . "assets/register.css" );
    wp_enqueue_script( 'ajax-login-register-register-script', plugin_dir_url( __FILE__ ) . 'assets/register.js', $dependencies  );

    wp_localize_script( 'ajax-login-register-script', '_ajax_login_settings', zm_ajax_login_register_localized_js() );
}
add_action( 'wp_enqueue_scripts', 'zm_ajax_login_register_enqueue_scripts');


function zm_ajax_login_register_localized_js(){
    $redirect_url = get_option('ajax_login_register_redirect');
    $redirect_url = empty( $redirect_url ) ? network_site_url($_SERVER['REQUEST_URI']) : $redirect_url;
    $redirect_url = apply_filters( 'zm_ajax_login_redirect', $redirect_url );
    $width = array(
        'default' => 265,
        'wide' => 440,
        'extra_buttons' => 666,
        'mobile' => 300
        );

    $style = get_option('ajax_login_register_default_style');
    $fb_button = get_option('ajax_login_register_facebook');

    if ( $style == 'wide' && $fb_button ){
        $key = 'extra_buttons';
    } elseif( wp_is_mobile() ) {
        $key = 'mobile';
    } elseif ( $style == 'wide' ){
        $key = 'wide';
    } else {
        $key = 'default';
    }

    $defaults = array(
        'ajaxurl' => admin_url("admin-ajax.php"),
        'login_handle' => get_option('ajax_login_register_advanced_usage_login'),
        'register_handle' => get_option('ajax_login_register_advanced_usage_register'),
        'redirect' => $redirect_url,
        'dialog_width' => $width[ $key ],
        'match_error' => AjaxLogin::status('passwords_do_not_match','description'),
        'is_user_logged_in' => is_user_logged_in() ? 1 : 0,
        'wp_logout_url' => wp_logout_url( site_url() ),
        'logout_text' => __('Logout', 'ajax_login_register' ),
        'close_text' => __('Close', 'ajax_login_register' )
        );

    $localized = apply_filters( 'zm_ajax_login_register_localized_js', $defaults );

    return $localized;
}


/**
 * When the plugin is deactivated remove the shown notice option
 */
function ajax_login_register_deactivate(){
    delete_option( 'ajax_login_register_plugin_notice_shown' );
}
register_deactivation_hook( __FILE__, 'ajax_login_register_deactivate' );


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