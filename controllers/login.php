<?php

/**
 * This is file is responsible for custom logic needed by all templates. NO
 * admin code should be placed in this file.
 */
Class Login Extends AjaxLogin {


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
            'user_login'    => empty( $_POST['user_login'] ) ? $user_login : sanitize_text_field( $_POST['user_login'] ),
            'user_password' => empty( $_POST['password'] ) ? $password : sanitize_text_field( $_POST['password'] ),
            'remember'      => isset( $_POST['rememberme'] ) ? null : true
            );
        $user = wp_signon( $creds, false );


        /**
         * If signon is successful we print the user name if not we print "0" for
         * false
         */
        $status = is_wp_error( $user ) ? $this->status( $user->get_error_code() ) : $this->status('success_login');


        if ( $is_ajax ) {
            wp_send_json( $status );
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

        check_ajax_referer( 'facebook-nonce', 'security' );

        $user = array(
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'fb_id' => $_POST['fb_id']
            );

        if ( empty( $user['username'] ) ){

            $msg = $this->status('invalid_username');

        } else {

            // Get our user object, if this user does not exists we create it
            $user_obj = get_user_by( 'email', $user['email'] );


            if ( $user_obj == false ){
                $register_obj = New Register;
                $user_obj = $register_obj->create_facebook_user( $user );
            }
            //

            // Log our FB user in
            $password = get_usermeta( $user_obj->ID, '_random' );
            $logged_in = $this->login_submit( $user_obj->user_login, $password, false );

            if ( $logged_in == true ){
                $msg = $this->status('success_login');
            } else {
                die("\nSomething to do here");
            }

        }

        wp_send_json( $msg );
    }


    /**
     * Load the login shortcode
     */
    public function login_shortcode(){
        ob_start();
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . 'views/login-form.php' );
        return ob_get_clean();
    }

}
new Login;