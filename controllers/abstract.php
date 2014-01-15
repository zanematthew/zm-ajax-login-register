<?php

/**
 * Our abstract class for zM Ajax Login.
 *
 * This class is designed to reduce and factor out the shared details between our classes.
 * Thus allowing us to focus on as few concepts at a time.
 */
abstract Class AjaxLogin {

    /**
     * Validation status responses
     */
    public $status = array(
        array(
            'status' => 0,
            'cssClass' => 'success',
            'msg' => 'Pass',
            'field' => '',
            'description' => '<div class="success-container">Success! One moment while we log you in...</div>'
            ),
        array(
            'status' => 1,
            'cssClass' => 'error',
            'msg' => 'Default Error',
            'description' => '<div class="error-container">Error</div>'
            ),
        array(
            'status' => 2,
            'cssClass' => 'error',
            'msg' => 'Invalid User',
            'description' => '<div class="error-container">Login is in use or invalid</div>'
            ),
        array(
            'status' => 3,
            'msg' => 'Fail',
            'cssClass' => 'error',
            'description' => '<div class="error-container">Email in use or invalid</div>'
            )
        );

    public $scripts = array();

    /**
     * WordPress hooks to be ran during init
     */
    public function __construct(){
        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

        add_action( 'wp_head', array( &$this, 'header' ) );

        add_action( 'wp_ajax_nopriv_validate_email', array( &$this, 'validate_email' ) );
        add_action( 'wp_ajax_validate_email', array( &$this, 'validate_email' ) );

        add_action( 'wp_ajax_nopriv_validate_username', array( &$this, 'validate_username' ) );
        add_action( 'wp_ajax_validate_username', array( &$this, 'validate_username' ) );

        add_action( 'wp_ajax_nopriv_load_template', array( &$this, 'load_template' ) );
        add_action( 'wp_ajax_load_template', array( &$this, 'load_template' ) );
    }


    /**
     * Any additional code to be ran during wp_head
     *
     * Prints the ajaxurl in the html header.
     * Prints the meta tags template.
     */
    public function header(){
        print '<script type="text/javascript"> var ajaxurl = "'. admin_url("admin-ajax.php") .'";</script>';
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . "views/meta-tags.php" );
    }


    /**
     * Build the settings array
     *
     * @todo use add_settings_field()
     */
    public function get_settings(){
        $settings['advanced_usage'] = array(
                array(
                    'key' => 'ajax_login_register_advanced_usage_login',
                    'label' => __('Login Handle','ajax_login_register'),
                    'type' => 'text',
                    'description' => sprintf('%s <code>%s</code>', __('Type the class name or ID of the element you want to launch the dialog box when clicked, example','ajax_login_register'), __('.login-link','ajax_login_register') )
                    ),
                array(
                    'key' => 'ajax_login_register_advanced_usage_register',
                    'label' => __('Register Handle','ajax_login_register'),
                    'type' => 'text',
                    'description' => sprintf('%s <code>%s</code>',__('Type the class name or ID of the element you want to launch the dialog box when clicked, example','ajax_login_register'), __('.register-link','ajax_login_register'))
                    ),
                array(
                    'key' => 'ajax_login_register_additional_styling',
                    'label' => __('Additional Styling','ajax_login_register'),
                    'type' => 'textarea',
                    'description' => __('','')
                    ),
                array(
                    'key' => 'ajax_login_register_redirect',
                    'label' => __('Redirect After Login URL','ajax_login_register'),
                    'type' => 'text',
                    'description' => sprintf( '%s <code>%s</code>', __('Enter the URL or slug you want users redirected to after login, example: ','ajax_login_register'), __('http://site.com/, /dashboard/, /wp-admin/','ajax_login_register') )
                    ),
                array(
                    'key' => 'ajax_login_register_default_style',
                    'label' => __('Form Layout','ajax_login_register'),
                    'type' => 'text',
                    'description' => ''
                    )
                );

        $settings['facebook'] = array(
                array(
                    'key' => 'url',
                    'label' => __('URL','ajax_login_register'),
                    'type' => 'text'
                    ),
                array(
                    'key' => 'admins',
                    'label' => __('Admin ID','ajax_login_register'),
                    'type' => 'text'
                    ),
                array(
                    'key' => 'app_id',
                    'label' => __('App ID','ajax_login_register'),
                    'type' => 'text'
                    )
            );

        $settings['general'] = array(
                array(
                    'key' => 'ajax_login_register_facebook',
                    'label' => __('Enable Facebook Login','ajax_login_register'),
                    'description' => __('By disabling this your Facebook settings will still be saved.','ajax_login_register')
                ),
                array(
                    'key' => 'ajax_login_register_keep_me_logged_in',
                    'label' => __('Disable keep me logged in', 'ajax_login_register'),
                    'description' => __('Use this option to disable the check box shown to keep users logged in.','ajax_login_register')
                    )
            );

        return $settings;
    }


    /**
     * Generates the needed markup for a given form field.
     *
     * @param $type string text, textarea
     * @param $key string used for the form field "name" and "id"
     * @param $extras array containing the following keys: 'class','attributes'
     * @todo use add_settings_field()
     */
    public function build_input( $type=null, $key=null, $extras=null ){

        switch( $type ){
            case 'textarea':
                $field = '<textarea name="' . $key . '" id="' . $key . '" rows="10" cols="80" class="code">' . wp_kses( get_option( $key ),'' ) . '</textarea>';
                break;

            case 'text':
                $field = '<input type="text" name="' . $key . '" id="' . $key . '" class="regular-text" value="' . get_option( $key ) . '" />';
                break;

            case 'select':
                $field = 'select here';
                break;
        }
        return $field;
    }


    /**
     * Check if an email is "valid" using PHPs filter_var & WordPress
     * email_exists();
     *
     * @param $email (string) Emailt to be validated
     * @param $is_ajax (bool)
     * @todo check ajax refer
     */
    public function validate_email( $email=null, $is_ajax=true ) {

        $email = is_null( $email ) ? $email : $_POST['email'];

        if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $msg = email_exists( $email ) ? $this->status[3] : null;
        } else {
            $msg = $this->status[3];
        }

        if ( $is_ajax ){
            print json_encode( $msg );
            die();
        } else {
            return $msg;
        }
    }


    /**
     * Process request to pass variables into WordPress' validate_username();
     *
     * @uses validate_username()
     * @param $username (string)
     * @param $is_ajax (bool) Process as an AJAX request or not.
     */
    public function validate_username( $username=null, $is_ajax=true ) {

        $username = empty( $_POST['login'] ) ? $username : $_POST['login'];

        if ( validate_username( $username ) && ! is_object( get_user_by( 'login', $username ) ) ) {
            $msg = null;
        } else {
            $msg =$this->status[2];
        }

        if ( $is_ajax ){
            print json_encode( $msg );
            die();
        } else {
            return $msg;
        }
    }


    /**
     * Load the login form via an AJAX request.
     *
     * @package AJAX
     */
    public function load_template(){
        check_ajax_referer( $_POST['referer'],'security');
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . "views/" . $_POST['template'] . '.php' );
        die();
    }


    /**
     * Loads our default CSS and JS along with controller specific CSS and JS
     */
    public function enqueue_scripts( $scripts=null ){

        if ( empty( $this->scripts ) ) return;

        $dependencies = array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-mouse',
            'jquery-ui-position',
            'jquery-ui-draggable',
            'jquery-ui-resizable',
            'jquery-ui-button',
            'jquery-ui-dialog'
        );

        wp_enqueue_style( 'ajax-login-style', plugin_dir_url( dirname( __FILE__ ) ) . "assets/style.css" );
        wp_enqueue_style( 'jquery-ui-custom', plugin_dir_url( dirname( __FILE__ ) ) . "assets/jquery-ui.css" );
        wp_enqueue_script( 'ajax-login-script', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/scripts.js', $dependencies  );

        foreach( $this->scripts as $script )
            wp_enqueue_script( $script, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/' . $script . '.js', array('jquery')  );

        if ( ! empty( $this->styles ) ){
            foreach( $this->styles as $style )
                wp_enqueue_style( $style, plugin_dir_url( dirname( __FILE__ ) ) . 'assets/' . $style . '.css' );
        }
    }
}