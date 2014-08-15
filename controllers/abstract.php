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
}