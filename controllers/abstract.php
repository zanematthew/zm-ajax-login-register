<?php

/**
 * Our abstract class for zM Ajax Login.
 *
 * This class is designed to reduce and factor out the shared details between our classes.
 * Thus allowing us to focus on as few concepts at a time.
 */
abstract Class AjaxLogin {

    public $scripts = array();

    /**
     * WordPress hooks to be ran during init
     */
    public function __construct(){

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
                    'description' => __('Type your custom CSS styles that are applied to the dialog boxes.','ajax_login_register')
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
                    ),
                array(
                    'key' => 'ajax_login_register_force_check_password',
                    'label' => __('Force Check Password','ajax_login_register'),
                    'type' => 'checkbox',
                    'description' => __('Use this option if your are experiencing compatibility issues with other login and or register plugins.','ajax_login_register')
                    ),
                array(
                    'key' => 'ajax_login_register_pre_load_forms',
                    'label' => __('Pre-load Forms','ajax_login_register'),
                    'type' => 'checkbox',
                    'description' => __('Setting this option will pre-load the forms, allowing them to be loaded prior to being clicked on.','ajax_login_register')
                    )
                );

        $settings['facebook'] = array(
                array(
                    'key' => 'url',
                    'label' => __('URL','ajax_login_register'),
                    'type' => 'text',
                    'description' => __('This is the URL you have set in your Facebook Developer App Settings','ajax_login_register')
                    ),
                array(
                    'key' => 'app_id',
                    'label' => __('App ID','ajax_login_register'),
                    'type' => 'text',
                    'description' => __('This is the App ID as seen in your Facebook Developer App Dashboard','ajax_login_register')
                    )
            );

        $settings['general'] = array(
                array(
                    'key' => 'ajax_login_register_facebook',
                    'label' => __('Enable Facebook Login','ajax_login_register'),
                    'description' => __('By disabling this, your Facebook settings will still be saved.','ajax_login_register')
                ),
                array(
                    'key' => 'ajax_login_register_keep_me_logged_in',
                    'label' => __('Disable "keep me logged in"', 'ajax_login_register'),
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
                $field = '<input type="text" name="' . $key . '" id="' . $key . '" class="regular-text" value="' . esc_attr( get_option( $key ) ) . '" />';
                break;

            case 'checkbox' :
                $field = '<input type="checkbox" name="' . $key . '" id="' . $key . '" ' . checked( get_option( $key, "off" ), "on", false ) . '/>';
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

        if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $msg = $this->status('email_invalid');
        } else if ( email_exists( $email ) ){
            $msg = $this->status('email_in_use');
        } else {
            $msg = $this->status('email_valid');
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

        $username = empty( $_POST['login'] ) ? esc_attr( $username ) : $_POST['login'];

        if ( validate_username( $username ) ) {
            $user_id = username_exists( $username );
            if ( $user_id ){
                $msg = $this->status('username_exists');
            } else {
                $msg = $this->status('valid_username');
            }
        } else {
            $msg = $this->status('invalid_username');
        }

        if ( $is_ajax ){
            wp_send_json( $msg );
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
     * Validation status responses
     */
    static function status( $key=null, $value=null ){

        $status = array(

            'valid_username' => array(
                'description' => null,
                'cssClass' => 'noon',
                'code' => 'success'
                ),
            'username_exists' => array(
                'description' => __('Invalid username', 'ajax_login_register'),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),
            'invalid_username' => array(
                'description' => __( 'Invalid username', 'ajax_login_register' ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),
            'username_does_not_exists' => array(
                'description' => __( 'Invalid username', 'ajax_login_register' ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),

            'incorrect_password' => array(
                'description' => __( 'Invalid', 'ajax_login_register' ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),
            'passwords_do_not_match' => array(
                'description' => __('Passwords do not match.','ajax_login_register'),
                'cssClass' =>'error-container',
                'code' => 'error'
                ),

            'email_valid' => array(
                'description' => null,
                'cssClass' => 'noon',
                'code' => 'success'
                ),
            'email_invalid' => array(
                'description' => __( 'Invalid Email', 'ajax_login_register' ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),
            'email_in_use' => array(
                'description' => __( 'Invalid Email', 'ajax_login_register' ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),

            'success_login' => array(
                'description' => __( 'Success! One moment while we log you in...', 'ajax_login_register' ),
                'cssClass' => 'success-container',
                'code' => 'success_login'
                ),
            'success_registration' => array(
                'description' => __( 'Success! One moment while we log you in...', 'ajax_login_register' ),
                'cssClass' => 'noon',
                'code' => 'success_registration'
                )
            );

        $status = apply_filters( 'ajax_login_register_status_codes', $status );

        if ( empty( $value ) ){
            return $status[ $key ];
        } else {
            return $status[ $key ][ $value ];
        }
    }


    /**
     * Adds the user to the networked blog they are currently visiting
     *
     * @since 1.0.9
     * @param $user_id
     * @return true, wp_error object
     */
    public function multisite_setup( $user_id=null ){

        $added_to_blog = add_user_to_blog(
            get_current_blog_id(),
            $user_id,
            apply_filters( 'ajax_login_register_default_role', get_option('default_role') )
        );

        return $added_to_blog;

    }


    /**
     * Handles creating a new user using native WordPress functions,
     * and signs the user on if successful.
     *
     * @uses wp_parse_args
     * @uses apply_filters
     * @uses update_user_meta
     * @uses is_multisite
     * @uses wp_signon
     * @uses wp_new_user_notification
     *
     * @param $user (array) User array as seen
     *  in: http://codex.wordpress.org/Function_Reference/wp_insert_user
     * @param $password (string) The password to be used
     *
     * @return $user_id (mixed) False on failure, user_id on success
     */
    public function create_user( $user=null, $password=null ){

        $user = wp_parse_args( $user, array(
            'role' => apply_filters( 'ajax_login_register_default_role', get_option('default_role') ),
            'user_registered' => date('Y-m-d H:i:s'),
            'user_email' => $user['email']
            ) );

        $user_id = wp_insert_user( $user );

        if ( is_wp_error( $user_id ) ) {

            $user_id = false;

        } else {

            // update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
            if ( ! empty( $user['fb_id'] ) ){
                update_user_meta( $user_id, 'fb_id', $user['fb_id'] );
            }

            if ( is_multisite() ){
                $this->multisite_setup( $user_id );
            }

            $wp_signon = wp_signon( array(
                'user_login' => $user['user_login'],
                'user_password' => $user['user_pass'],
                'remember' => true ),
            false );

            wp_new_user_notification( $user_id );

            do_action( 'zm_ajax_login_after_successfull_registration', $user_id );

        }

        return $user_id;

    }
}
