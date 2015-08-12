<?php

/**
 * Plugin Name: zM Ajax Login & Register
 * Plugin URI: http://zanematthew.com/products/zm-ajax-login-register/
 * Description: Creates a simple login and register modal with an optional shortcode.
 * Version: 1.1.1
 * Author: Zane Matthew
 * Author URI: http://zanematthew.com/
 * License: GPL V2 or Later
 */

define( 'ALR_URL', plugin_dir_url( __FILE__ ) );
define( 'ALR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALR_NAMESPACE', 'alr' );
define( 'ALR_TEXT_DOMAIN', 'ajax_login_register' );
define( 'ALR_VERSION', '1.0.0' );
define( 'ALR_PLUGIN_FILE', __FILE__ );
define( 'ALR_PRODUCT_NAME', 'ZM AJAX Login Regiser' ); // Must match download title in EDD store!
define( 'ALR_AUTHOR', 'Zane Matthew' );

require ALR_PATH . 'lib/lumber/lumber.php';
require ALR_PATH . 'lib/quilt/quilt.php';
require ALR_PATH . 'lib/zm-dependency-container/zm-dependency-container.php';

require ALR_PATH . 'deprecated.php';

require ALR_PATH . 'src/ALRCore/ALRHelpers.php';
require ALR_PATH . 'src/ALRCore/ALRHtml.php';
require ALR_PATH . 'src/ALRCore/ALRLogin.php';
require ALR_PATH . 'src/ALRCore/ALRRegister.php';

require ALR_PATH . 'src/ALRDesign/ALRDesign.php';
require ALR_PATH . 'src/ALRSocial/ALRSocial.php';
require ALR_PATH . 'src/ALRMisc/ALRMisc.php';
require ALR_PATH . 'src/ALRRedirect/ALRRedirect.php';


function alr_init(){

    load_plugin_textdomain( ALR_TEXT_DOMAIN, false, plugin_basename(dirname(__FILE__)) . '/languages' );

    global $alr_settings_obj;
    $alr_settings_obj = new Quilt(
        ALR_NAMESPACE,
        array(),
        'plugin'
    );

    global $alr_settings;
    $alr_settings = $alr_settings_obj->getSaneOptions();

    do_action( ALR_NAMESPACE . '_init' );

}
add_action( 'init', 'alr_init' );


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
    wp_enqueue_script( 'ajax-login-register-login-script', plugin_dir_url( __FILE__ ) . 'assets/login.js', $dependencies  );

    // Register
    wp_enqueue_style( 'ajax-login-register-register-style', plugin_dir_url( __FILE__ ) . "assets/register.css" );
    wp_enqueue_script( 'ajax-login-register-register-script', plugin_dir_url( __FILE__ ) . 'assets/register.js', $dependencies  );

    global $alr_settings;
    wp_localize_script( 'ajax-login-register-script', '_ajax_login_settings', apply_filters( 'alr_localized_js', array(
        'ajaxurl' => admin_url("admin-ajax.php"),
        'login_handle' => $alr_settings['alr_misc_login_handle'],
        'register_handle' => $alr_settings['alr_misc_register_handle'],
        'redirect' => $alr_settings['alr_redirect_redirect_after_login_url'],
        // 'match_error' => AjaxLogin::status('passwords_do_not_match','description'), // Deprecated
        'is_user_logged_in' => is_user_logged_in() ? 1 : 0,
        'wp_logout_url' => wp_logout_url( site_url() ),
        'logout_text' => __( 'Logout', ALR_TEXT_DOMAIN ),
        'close_text' => __( 'Close', ALR_TEXT_DOMAIN ),
        'pre_load_forms' => $alr_settings['alr_misc_pre_load_forms']
        ) ) );
}
add_action( 'wp_enqueue_scripts', 'zm_ajax_login_register_enqueue_scripts');


/**
 * When the plugin is deactivated remove the shown notice option
 */
function ajax_login_register_deactivate(){

    delete_option( 'ajax_login_register_plugin_notice_shown' );
    delete_option( 'ajax_login_register_version' );

}
register_deactivation_hook( __FILE__, 'ajax_login_register_deactivate' );


function ajax_login_register_activate(){

    $version = update_option( 'ajax_login_register_version', AJAX_LOGIN_REGISTER_VERSION );

    if ( $version == '1.0.9' ){

        // Remove the legacy option 'admins', which was used for Facebook admin IDs
        delete_option( 'admins' );
    }

}
register_activation_hook( __FILE__, 'ajax_login_register_activate' );


/**
 * Add our links to the plugin page, these show under the plugin in the table view.
 *
 * @param $links(array) The links coming in as an array
 * @param $current_plugin_file(string) This is the "plugin basename", i.e., my-plugin/plugin.php
 */
function alr_plugin_action_links( $links, $current_plugin_file ){

    // Plugin Table campaign URL
    $campaign_text_link = 'http://store.zanematthew.com/downloads/zm-ajax-login-register-pro/?utm_source=wordpress.org&utm_medium=alr_plugin&utm_content=textlink&utm_campaign=alr_pro_upsell_link';

    // $this->campaign_banner_link = 'http://store.zanematthew.com/downloads/zm-ajax-login-register-pro/?utm_source=wordpress&utm_medium=alr_plugin&utm_content=bannerlink&utm_campaign=alr_pro_upsell_banner';

    if ( $current_plugin_file == 'zm-ajax-login-register/zm-ajax-login-register.php' ){
        $links['alr_settings'] = '<a href="' . admin_url( 'options-general.php?page=' . ALR_NAMESPACE ) . '">' . esc_attr__( 'Settings', ALR_NAMESPACE ) . '</a>';
        $links['client_access_addons'] = sprintf('<a href="%2$s" title="%1$s" target="_blank">%1$s</a>', esc_attr__('Add-ons', ALR_TEXT_DOMAIN ), $campaign_text_link );
    }

    return $links;
}
add_filter( 'plugin_action_links', 'alr_plugin_action_links', 10, 2 );


function alr_settings_page_title( $title, $namespace ){

    return 'AJAX Login & Register';

}
add_filter( 'quilt_alr_page_title', 'alr_settings_page_title', 15, 2 );


function alr_settings_menu_title( $title, $namespace ){

    return 'AJAX Login & Register 2.0';

}
add_filter( 'quilt_alr_menu_title', 'alr_settings_menu_title', 15, 2 );


function alr_settings_footer_content( $content ){
    $settings_campaign_url = 'http://store.zanematthew.com/downloads/tag/client-access-add-ons/?utm_source=WordPress&utm_medium=Settings%20Footer&utm_campaign=Client%20Access%20Add-ons';

    return sprintf( '%s | v%s | <a href="%s" target="_blank">%s</a> | <a href="%s" target="_blank">%s</a>',
        __( 'Thank you for using ZM AJAX Login & Register', ALR_NAMESPACE ),
        ALR_VERSION,
        esc_url( 'http://support.zanematthew.com/forum/zm-ajax-login-register/'),
        __( 'Support', ALR_NAMESPACE ),
        esc_url( $settings_campaign_url ),
        __( 'Add-ons', ALR_NAMESPACE )
        );

}
add_filter( 'quilt_alr_footer', 'alr_settings_footer_content', 15, 2 );