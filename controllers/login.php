<?php

/**
 * This is file is responsible for custom logic needed by all templates. NO
 * admin code should be placed in this file.
 */
Class Login Extends AjaxLogin {

    /**
     * Array of JavaScript, note name, must match FILE name!
     */
    public $scripts = array( 'login' );


    /**
     * Array of stylesheets, note name, must match FILE name!
     */
    public $styles = array( 'login' );


    /**
     * Run the following methods when this class is loaded
     */
    public function __construct(){
        add_action( 'init', array( &$this, 'init' ) );
        add_action( 'wp_footer', array( &$this, 'footer' ) );
        parent::__construct();
    }


    /**
     * During WordPress' init load various methods.
     */
    public function init(){
        add_action( 'wp_ajax_login_submit', array( &$this, 'login_submit' ) );
        add_action( 'wp_ajax_nopriv_login_submit', array( &$this, 'login_submit' ) );

        add_action( 'wp_ajax_facebook_login', array( &$this, 'facebook_login' ) );
        add_action( 'wp_ajax_nopriv_facebook_login', array( &$this, 'facebook_login') );

        add_shortcode( 'ajax_login', array( &$this, 'login_shortcode' ) );

        add_action( 'wp_ajax_nopriv_load_template', array( &$this, 'load_template' ) );
        add_action( 'wp_ajax_load_template', array( &$this, 'load_template' ) );
    }


    /**
     * Any additional code to be ran during wp_footer
     *
     * If the user is not logged in we display the hidden jQuery UI dialog containers
     */
    public function footer(){
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . 'views/login-dialog.php' );
    }


    /**
     * Processes credentials to pass into wp_signon to log a user into WordPress.
     *
     * @uses check_ajax_referer()
     * @uses wp_signon()
     * @uses is_wp_error()
     *
     * @param $user_login (string) Defaults to $_POST['user_login']
     * @param $password (string)
     * @param $is_ajax (bool) Process as an AJAX request
     * @package AJAX
     *
     * @return userlogin on success; 0 on false;
     */
    public function login_submit( $user_login=null, $password=null, $is_ajax=true ) {

        /**
         * Verify the AJAX request
         */
        if ( $is_ajax ) check_ajax_referer('login_submit','security');


        /**
         * Build our array of credentials to be passed into wp_signon.
         * Default is to look for $_POST variables
         */
        $creds = array(
            'user_login'    => empty( $_POST['user_login'] ) ? $user_login : $_POST['user_login'],
            'user_password' => empty( $_POST['password'] ) ? $password : $_POST['password'],
            'remember'      => isset( $_POST['remember'] ) ? null : true
            );
        $user = wp_signon( $creds, false );


        /**
         * If signon is successful we print the user name if not we print "0" for
         * false
         */
        $status = is_wp_error( $user ) ? "0" : $user->data->user_login;

        if ( $is_ajax ) {
            die();
        } else {
            return $status;
        }
    }


    /**
     * Creates a new user in WordPress using their FB account info.
     *
     * @uses register_submit();
     */
    public function facebook_login(){

        check_ajax_referer('login_submit','security');

        $user = array(
            'username' => $_POST['username'],
            'password' => $_POST['fb_id'],
            'email' => $_POST['email'],
            'fb_id' => $_POST['fb_id']
            );

        $register_obj = New Register;

        if ( empty( $user['username'] ) || empty( $user['password'] ) ){

            $msg = $this->status[3];

        } else {

            // Attempt to log out user in, if not we register them
            $logged_in = $this->login_submit( $user['username'], $user['password'], false );

            if ( $logged_in == false ) {
                $msg = $register_obj->register_submit( $user['username'], $user['password'], $user['email'], $is_ajax=false );
                do_action( 'ajax_login_register_after_facebook_login', $user['username'] );
            } else {
                $msg = $this->status[0];
            }
        }

        wp_send_json( $msg );
    }


    /**
     * Load the login shortcode
     */
    public function login_shortcode(){
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . 'views/login-form.php' );
    }
}
new Login;