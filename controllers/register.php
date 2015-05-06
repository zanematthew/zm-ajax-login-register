<?php

/**
 * This is file is responsible for custom logic needed by all templates. NO
 * admin code should be placed in this file.
 */
Class ajax_login_register_Register Extends AjaxLogin {

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
        add_action( 'wp_ajax_nopriv_setup_new_user', array( &$this,'setup_new_user' ) );
        add_action( 'wp_ajax_setup_new_user', array( &$this,'setup_new_user' ) );

        add_shortcode( 'ajax_register', array( &$this, 'register_shortcode' ) );
    }


    /**
     * Any additional code to be ran during wp_footer
     *
     * If the user is not logged in we display the hidden jQuery UI dialog containers
     */
    public function footer(){
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . 'views/register-dialog.php' );
    }


    /**
     * Handles setting up new user info and passes it to the appropriate.
     *
     * @uses check_ajax_referer() http://codex.wordpress.org/Function_Reference/check_ajax_referer
     * @uses get_user_by_email() http://codex.wordpress.org/Function_Reference/get_user_by_email
     * @uses get_user_by() http://codex.wordpress.org/Function_Reference/get_user_by
     *
     * @param $login
     * @param $password
     * @param $email
     * @param $is_ajax
     *
     * @return Status (array|json) JSON if is_ajax is true, else as array
     */
    public function setup_new_user( $login=null, $password=null, $email=null, $is_ajax=true ) {

        if ( $is_ajax ) check_ajax_referer('setup_new_user','security');

        // TODO consider using wp_generate_password( $length=12, $include_standard_special_chars=false );
        // and wp_mail the users password asking to change it.
        $user = array(
            'user_login' => empty( $_POST['login'] ) ? $login : sanitize_text_field( $_POST['login'] ),
            'email'      => empty( $_POST['email'] ) ? $email : sanitize_text_field( $_POST['email'] ),
            'user_pass'  => empty( $_POST['password'] ) ? $password : sanitize_text_field( $_POST['password'] ),
            'fb_id'      => empty( $_POST['fb_id'] ) ? false : sanitize_text_field( $_POST['fb_id'] )
        );

        $valid['email'] = $this->validate_email( $user['email'], false );
        $valid['username'] = $this->validate_username( $user['user_login'], false );
        $user_id = null;

        if ( $valid['username']['code'] == 'error' ){
            $status = $this->status('invalid_username'); // invalid user
        } else if ( $valid['email']['code'] == 'error' ) {
            $status = $this->status('invalid_username'); // invalid user
        } else {

            if ( ! isset( $status['code'] ) ){

                $user_id = $this->create_user( $user );

                if ( $user_id == false ){

                    $status = $this->status('invalid_username'); // invalid user

                } else {

                    $status = $this->status('success_registration'); // success
                    $status['id'] = $user_id;

                }

            }
        }

        if ( $is_ajax ) {
            wp_send_json( $status );
        } else {
            return $status;
        }
    }


    /**
     * Load the login shortcode
     */
    public function register_shortcode(){
        ob_start();
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . 'views/register-form.php' );
        return ob_get_clean();
    }


    public function multisite_setup( $user_id=null ){
        return add_user_to_blog( get_current_blog_id(), $user_id, 'subscriber');
    }


    /**
     * Setup a new Facebook User
     *
     * @since 1.0.9
     * @param $user (array) Containing the values as seen
     *  in: http://codex.wordpress.org/Function_Reference/wp_insert_user
     * @return $user_obj (object) The user_obj as seen
     *  in: http://codex.wordpress.org/Function_Reference/get_user_by
     */
    public function setup_new_facebook_user( $user=array() ){

        $user_pass = wp_generate_password();

        $user_id = $this->create_user( array_merge( $user, array(
            'user_pass' => $user_pass
        ) ) );

        if ( $user_id == false ){

            $user_obj = false;

        } else {

            // Store random password as user meta
            add_user_meta( $user_id, '_random', $user_pass );
            $user_obj = get_user_by( 'id', $user_id );

        }

        return $user_obj;
    }
}
function ajax_login_register_plugins_loaded_register(){
    new ajax_login_register_Register;
}
add_action( 'plugins_loaded', 'ajax_login_register_plugins_loaded_register' );