<?php

/**
 * This is file is responsible for custom logic needed by all templates. NO
 * admin code should be placed in this file.
 */
Class ajax_login_register_Login Extends AjaxLogin {


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

        $username = empty( $_POST['user_login'] ) ? $user_login : sanitize_text_field( $_POST['user_login'] );
        $password = empty( $_POST['password'] ) ? $password : sanitize_text_field( $_POST['password'] );
        $remember = empty( $_POST['password'] ) ? $password : sanitize_text_field( $_POST['password'] );

        // Currently wp_signon returns the same error code 'invalid_username' if
        // a username does not exists or is invalid
        if ( validate_username( $username ) ){

            if ( username_exists( $username ) ){

                // if option force check password
                if ( get_option( 'ajax_login_register_force_check_password' ) ){

                    $user = get_user_by( 'login', $username );
                    if ( wp_check_password( $password, $user->data->user_pass, $user->ID ) ){

                        $status = $this->status('success_login');
                        wp_signon( array(
                            'user_login'    => $username,
                            'user_password' => $password,
                            'remember'      => $remember
                            ), false );
                    }

                } else {

                    $creds = array(
                        'user_login'    => $username,
                        'user_password' => $password,
                        'remember'      => $remember
                        );
                    $user = wp_signon( $creds, false );
                    $status = is_wp_error( $user ) ? $this->status( $user->get_error_code() ) : $this->status('success_login');
                }

            } else {
                $status = $this->status('username_does_not_exists');
            }
        } else {
            $status = $this->status('invalid_username');
        }

        if ( $is_ajax ) {
            wp_send_json( $status );
        } else {
            return $status;
        }
    }


    /**
     * Creates a new user in WordPress using their FB account info.
     *
     * @uses setup_new_user();
     */
    public function facebook_login(){

        check_ajax_referer( 'facebook-nonce', 'security' );

        // Map our FB response fields to the correct user fields as found in wp_update_user
        $user = array(
            'username'   => $_POST['fb_response']['id'],
            'user_login' => $_POST['fb_response']['id'],
            'first_name' => $_POST['fb_response']['first_name'],
            'last_name'  => $_POST['fb_response']['last_name'],
            'email'      => $_POST['fb_response']['email'],
            'user_url'   => $_POST['fb_response']['link'],
            );

        if ( empty( $user['username'] ) ){

            $status = $this->status('invalid_username');
            $user_id = false;

        } else {

            // If older version use this
            // $user_obj = get_user_by( 'email', $user['email'] );
            $user_obj = get_user_by( 'login', $user['user_login'] );

            if ( $user_obj == false ){
                $register_obj = New ajax_login_register_Register;
                $user_obj = $register_obj->setup_new_facebook_user( $user );
            }

            if ( $user_obj ){

                $user_id = $user_obj->ID;

	            // Save facebook ID to later use with avatars. Ideally this should go when a user is created but we want to catch old users
				update_user_meta( $user_id, '_fb_user_id', $user_obj->user_login );
                // Log our FB user in
                $password = get_user_meta( $user_id, '_random', true );

                $logged_in = $this->login_submit( $user_obj->user_login, $password, false );

                if ( $logged_in == true ){
                    $status = $this->status('success_login');
                } else {
                    $status = $this->status('invalid_username');
                }

            } else {

                $status = $this->status('invalid_username');
            }
        }

        wp_send_json( $status );

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
function ajax_login_register_plugins_loaded_login(){
    new ajax_login_register_Login;
}
add_action( 'plugins_loaded', 'ajax_login_register_plugins_loaded_login' );
