<?php

/**
 * Plugin Name: ZM Ajax Login & Register
 * Plugin URI: http://zanematthew.com/products/zm-ajax-login-register/
 * Description: Creates a simple login and register modal with an optional shortcode.
 * Version: 2.0.2
 * Author: Zane Matthew
 * Author URI: http://zanematthew.com/
 * License: GPL V2 or Later
 */

define( 'ZM_ALR_URL', plugin_dir_url( __FILE__ ) );
define( 'ZM_ALR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZM_ALR_NAMESPACE', 'zm_alr' );
define( 'ZM_ALR_TEXT_DOMAIN', 'ajax_login_register' );
define( 'ZM_ALR_VERSION', '2.0.2' );
define( 'ZM_ALR_PLUGIN_FILE', __FILE__ );

define( 'ZM_ALR_PRODUCT_NAME', 'ZM AJAX Login Regiser' ); // Must match download title in EDD store!
define( 'ZM_ALR_AUTHOR', 'Zane Matthew Kolnik' );

require ZM_ALR_PATH . 'lib/lumber/lumber.php';
require ZM_ALR_PATH . 'lib/quilt/quilt.php';
require ZM_ALR_PATH . 'lib/zm-dependency-container/zm-dependency-container.php';

require ZM_ALR_PATH . 'deprecated.php';
require ZM_ALR_PATH . 'upgrade.php';
require ZM_ALR_PATH . 'settings.php';

require ZM_ALR_PATH . 'src/ALRCore/ALRHelpers.php';
require ZM_ALR_PATH . 'src/ALRCore/ALRHtml.php';
require ZM_ALR_PATH . 'src/ALRCore/ALRLogin.php';
require ZM_ALR_PATH . 'src/ALRCore/ALRRegister.php';

require ZM_ALR_PATH . 'src/ALRDesign/ALRDesign.php';
require ZM_ALR_PATH . 'src/ALRSocial/ALRSocial.php';
require ZM_ALR_PATH . 'src/ALRSocial/ALRSocialFacebook.php';
require ZM_ALR_PATH . 'src/ALRMisc/ALRMisc.php';
require ZM_ALR_PATH . 'src/ALRRedirect/ALRRedirect.php';

/**
 * This is the main, init, and only. Any features should call the 'zm_alr_init' action.
 * Text domain is loaded here, classes init, and initial settings.
 *
 * @since 2.0.0
 *
 */
function zm_alr_init(){

    load_plugin_textdomain( ZM_ALR_TEXT_DOMAIN, false, plugin_basename(dirname(__FILE__)) . '/languages' );

    new ALRLogin( new ZM_Dependency_Container( null ) );
    new ALRRegister( new ZM_Dependency_Container( null ) );
    new ALRDesign();
    new ALRMisc();
    new ALRRedirect();
    new ALRSocial();
    new ALRSocialFacebook( new ZM_Dependency_Container( null ) );
    new ALRUpgrade();

    global $zm_alr_settings_obj;
    $zm_alr_settings_obj = new Quilt(
        ZM_ALR_NAMESPACE,
        array(),
        'plugin'
    );

    global $zm_alr_settings;
    $zm_alr_settings = $zm_alr_settings_obj->getSaneOptions();

    do_action( ZM_ALR_NAMESPACE . '_init' );

}
add_action( 'init', 'zm_alr_init' );


function zm_alr_admin_init(){

    do_action( ZM_ALR_NAMESPACE . '_admin_init' );

}
add_action( 'admin_init', 'zm_alr_admin_init' );


/**
 * Enqueues our JS, CSS, and localize any needed JS variables.
 *
 * @since 2.0.0
 *
 */
function zm_ajax_login_register_enqueue_scripts(){

    $dependencies = array(
        'jquery',
        'jquery-ui-dialog'
    );

    wp_enqueue_style( 'jquery-ui-custom', plugin_dir_url( __FILE__ ) . "assets/jquery-ui.css" );

    $styles = apply_filters( ZM_ALR_NAMESPACE . '_styles', array(
        array(
            'handle' => 'ajax-login-register-style',
            'url' => plugin_dir_url( __FILE__ ) . "assets/style.css"
            )
    ) );

    foreach( $styles as $style ){
        wp_enqueue_style( $style['handle'], $style['url'] );
    }

    $scripts = apply_filters( ZM_ALR_NAMESPACE . '_scripts', array(
        array(
            'handle' => 'ajax-login-register-script',
            'src' => plugin_dir_url( __FILE__ ) . 'assets/scripts.js',
            'deps' => $dependencies
        ),
        array(
            'handle' => 'ajax-login-register-login-script',
            'src' => plugin_dir_url( __FILE__ ) . 'assets/login.js',
            'deps' => $dependencies
        ),
        array(
            'handle' => 'ajax-login-register-register-script',
            'src' => plugin_dir_url( __FILE__ ) . 'assets/register.js',
            'deps' => $dependencies
        )
    ) );

    foreach( $scripts as $script ){
        wp_enqueue_script( $script['handle'], $script['src'], $script['deps'] );
    }

    global $zm_alr_settings;

    wp_localize_script( 'ajax-login-register-script', '_zm_alr_settings', apply_filters( 'zm_alr_localized_js', array(
        'ajaxurl'         => admin_url("admin-ajax.php"),
        'login_handle'    => $zm_alr_settings['zm_alr_misc_login_handle'],
        'register_handle' => $zm_alr_settings['zm_alr_misc_register_handle'],
        'redirect'        => $zm_alr_settings['zm_alr_redirect_redirect_after_login_url'],
        // 'match_error'    => AjaxLogin::status('passwords_do_not_match','description'), // Deprecated
        'wp_logout_url'   => wp_logout_url( site_url() ),
        'logout_text'     => __( 'Logout', ZM_ALR_TEXT_DOMAIN ),
        'close_text'      => __( 'Close', ZM_ALR_TEXT_DOMAIN ),
        'pre_load_forms'  => $zm_alr_settings['zm_alr_misc_pre_load_forms'],
        'logged_in_text'  => __('You are already logged in', ZM_ALR_TEXT_DOMAIN ),
        'registered_text' => __( 'You are already registered', ZM_ALR_TEXT_DOMAIN ),
        'dialog_width'    => 'auto',
        'dialog_height'   => 'auto',
        'dialog_position' => array(
            'my' => 'center top',
            'at' => 'center top+5%',
            'of' => 'body'
        )
    ) ) );

}
add_action( 'wp_enqueue_scripts', 'zm_ajax_login_register_enqueue_scripts');


/**
 * When the plugin is deactivated remove the shown notice option
 *
 * @since 1.1
 *
 */
function ajax_login_register_deactivate(){

    delete_option( 'ajax_login_register_plugin_notice_shown' );
    delete_option( 'ajax_login_register_version' );

}
register_deactivation_hook( __FILE__, 'ajax_login_register_deactivate' );


/**
 *
 * @since 1.1
 */
function ajax_login_register_activate(){

    // If this is a current installation we store the current version, as the
    // previous version. This allows us to track which version users are upgrading
    // to/from.

    $current_version = get_option( 'ajax_login_register_version' );
    if ( $current_version !== false ){
        update_option( ZM_ALR_NAMESPACE . '_previous_version', $current_version );
        delete_option( 'ajax_login_register_version' ); // remove the legacy version namespace
    }

    $version = update_option( ZM_ALR_NAMESPACE . '_version', ZM_ALR_VERSION );

    if ( $version == '1.0.9' ){
        // Remove the legacy option 'admins', which was used for Facebook admin IDs
        delete_option( 'admins' );
    }

}
register_activation_hook( __FILE__, 'ajax_login_register_activate' );


/**
 * Add our links to the plugin page, these show under the plugin in the table view.
 *
 * @since 1.1.0
 *
 * @param $links(array) The links coming in as an array
 * @param $current_plugin_file(string) This is the "plugin basename", i.e., my-plugin/plugin.php
 *
 */
function zm_alr_plugin_action_links( $links, $current_plugin_file ){

    // Plugin Table campaign URL
    $campaign_text_link = 'http://store.zanematthew.com/downloads/zm-ajax-login-register-pro/?utm_source=wordpress.org&utm_medium=zm_alr_plugin&utm_content=textlink&utm_campaign=zm_alr_pro_upsell_link';

    // $this->campaign_banner_link = 'http://store.zanematthew.com/downloads/zm-ajax-login-register-pro/?utm_source=wordpress&utm_medium=zm_alr_plugin&utm_content=bannerlink&utm_campaign=zm_alr_pro_upsell_banner';

    if ( $current_plugin_file == 'zm-ajax-login-register/zm-ajax-login-register.php' ){
        $links['zm_alr_settings'] = '<a href="' . admin_url( 'options-general.php?page=' . ZM_ALR_NAMESPACE ) . '">' . esc_attr__( 'Settings', ZM_ALR_NAMESPACE ) . '</a>';
        $links['zm_alr_pro'] = sprintf('<a href="%2$s" title="%1$s" target="_blank">%1$s</a>', esc_attr__('Pro Version', ZM_ALR_TEXT_DOMAIN ), $campaign_text_link );
    }

    return $links;
}
add_filter( 'plugin_action_links', 'zm_alr_plugin_action_links', 10, 2 );


/**
 * Show an admin notice when the plugin is activated
 * note the option 'ajax_login_register_plugin_notice_shown', is removed
 * during the 'register_deactivation_hook', see 'ajax_login_register_deactivate()'
 */
function zm_alr_admin_notice_campaig_url(){

    if ( ! is_plugin_active( plugin_basename( ZM_ALR_PLUGIN_FILE ) ) ){
        return;
    }


    // Campaign notice
    $campaign_text_link = 'http://store.zanematthew.com/downloads/zm-ajax-login-register-pro/?utm_source=wordpress.org&utm_medium=alr_plugin&utm_content=textlink&utm_campaign=alr_pro_upsell_link';

    if ( ! get_option('ajax_login_register_plugin_notice_shown') ){
        printf('<div class="updated"><p>%1$s %2$s</p></div>',
            __('Thanks for installing ZM AJAX Login & Register, be sure to check out the features in the', ZM_ALR_TEXT_DOMAIN ),
            '<a href="' . $campaign_text_link . '" target="_blank">Pro version</a>.'
        );
        update_option('ajax_login_register_plugin_notice_shown', 'true');
    }

}
add_action( 'admin_notices', 'zm_alr_admin_notice_campaig_url' );