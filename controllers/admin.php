<?php

Class ajax_login_register_Admin Extends AjaxLogin {

    public $campaign_text_link;
    public $campaign_banner_link;

    /**
     * WordPress hooks to be ran during init
     */
    public function __construct(){
        $this->campaign_text_link = 'http://store.zanematthew.com/downloads/zm-ajax-login-register-pro/?utm_source=WordPress&utm_medium=text&utm_campaign=ALR%20Pro';
        $this->campaign_banner_link = 'http://store.zanematthew.com/downloads/zm-ajax-login-register-pro/?utm_source=WordPress&utm_medium=banner&utm_campaign=ALR%20Pro';

        add_action( 'admin_init', array( &$this, 'admin_init' ) );
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links'), 10, 2 );
        add_action( 'admin_notices', array( &$this, 'admin_notice' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
    }

    /**
     * Register settings during the admin init
     */
    public function admin_init(){
        $this->register_settings();
    }


    /**
     * Retrieve our settings from our parent class and register each setting.
     * @todo use add_settings_section()
     */
    public function register_settings(){

        $settings = $this->get_settings();

        foreach( $settings as $k => $v ){
            foreach( $settings[ $k ] as $k ) {
                register_setting('ajax_login_register', $k['key'], array( &$this, 'sanitize' ) );
            }
        }
    }


    /**
     * This method is fired when the settings that are register with register_settings()
     * are saved from the settings page. We filter out ALL html/js using wp_kses().
     */
    public function sanitize( $setting ){
        return wp_kses( $setting,array(),array());
    }


    /**
     * Build our admin menu
     */
    public function admin_menu(){

        $parent = 'options-general.php';

        $sub_menu_pages = array(
            array(
                'parent_slug' => $parent,
                'page_title' => __( 'AJAX Login &amp; Register', 'ajax_login_register' ),
                'menu_title' => __( 'AJAX Login &amp; Register', 'ajax_login_register' ),
                'capability' => 'manage_options',
                'menu_slug' => 'ajax-login-register-settings',
                'function' => 'load_template'
                )
            );

        foreach( $sub_menu_pages as $sub_menu ){
            add_submenu_page(
                $sub_menu['parent_slug'],
                $sub_menu['page_title'],
                $sub_menu['menu_title'],
                $sub_menu['capability'],
                $sub_menu['menu_slug'],
                array( &$this, $sub_menu['function'] )
            );
        }
    }


    /**
     * Call back function which is fired when the admin menu page is loaded.
     */
    public function load_template(){
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . 'views/settings.php');
    }


    /**
     * Add our links to the plugin page, these show under the plugin in the table view.
     *
     * @param $links(array) The links coming in as an array
     * @param $current_plugin_file(string) This is the "plugin basename", i.e., my-plugin/plugin.php
     */
    public function plugin_action_links( $links, $current_plugin_file ){
        if ( $current_plugin_file == 'zm-ajax-login-register/plugin.php' ){
            $links['ajax_login_register_settings'] = '<a href="' . admin_url( 'options-general.php?page=ajax-login-register-settings' ) . '">' . esc_attr__( 'General Settings', 'ajax_login_register' ) . '</a>';
            $links['ajax_login_register_pro'] = sprintf('<a href="%2$s" title="%1$s" target="_blank">%1$s</a>', esc_attr__('Pro Version', 'ajax_login_register'), $this->campaign_text_link );
        }

        return $links;
    }


    /**
     * Show an admin notice when the plugin is activated
     * note the option 'ajax_login_register_plugin_notice_shown', is removed
     * during the 'register_deactivation_hook', see 'ajax_login_register_deactivate()'
     */
    public function admin_notice(){
        if ( ! get_option('ajax_login_register_plugin_notice_shown') && is_plugin_active( 'zm-ajax-login-register/plugin.php' ) ){
            printf('<div class="updated"><p>%1$s %2$s</p></div>',
                __('Thanks for installing zM AJAX Login & Register, be sure to check out the features in the', 'ajax_login_register'),
                '<a href="' . $this->campaign_text_link . '" target="_blank">Pro version</a>.'
            );
            update_option('ajax_login_register_plugin_notice_shown', 'true');
        }
    }


    /**
     * Enqueue our Admin styles only on the ajax login register setting page
     */
    public function admin_enqueue_scripts(){
        $screen = get_current_screen();
        if ( $screen->id == 'settings_page_ajax-login-register-settings' ){
            wp_enqueue_style( 'ajax-login-register-admin-style', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/admin.css' );
        }
    }
}
new ajax_login_register_Admin;