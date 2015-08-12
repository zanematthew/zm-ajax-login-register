<?php

Class ALRRegister {

    public function __construct( ZM_Dependency_Container $di ){

        $this->_alr_html = $di->get_instance( 'html', 'ALRHtml', null );
        $this->_alr_helpers = $di->get_instance( 'helpers', 'ALRHelpers', null );
        $this->prefix = 'alr_register';
        add_action( 'alr_init', array( &$this, 'init' ) );

    }

    public function init(){

        add_action( 'wp_ajax_nopriv_setup_new_user', array( &$this,'setupNewUser' ) );
        add_action( 'wp_ajax_setup_new_user', array( &$this,'setupNewUser' ) );

        add_action( 'wp_ajax_nopriv_validate_username', array( &$this,'validateUsername' ) );
        add_action( 'wp_ajax_validate_username', array( &$this,'validateUsername' ) );

        add_action( 'wp_ajax_nopriv_validate_email', array( &$this,'validateEmail' ) );
        add_action( 'wp_ajax_validate_email', array( &$this,'validateEmail' ) );

        add_shortcode( 'ajax_register', array( &$this, 'shortcode' ) );

    }


    public function shortcode(){

        // No filter here, filter in the buildFormFieldsHtml instead
        $fields_html = $this->_alr_html->buildFormFieldsHtml( array(
            $this->prefix . '_user_name' => array(
                'title' => 'User Name',
                'type' => 'text',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_email' => array(
                'title' => 'Email',
                'type' => 'email',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_password' => array(
                'title' => 'Password',
                'type' => 'password',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_confirm_password' => array(
                'title' => 'Confirm Passowrd',
                'type' => 'password',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                )
            ), $this->prefix );

        $links_html = $this->_alr_html->buildFormHtmlLinks( array(
            $this->prefix . '_not_a_member' => array(
                'href' => '#',
                'class' => 'already-registered-handle',
                'text' => __( 'Already registered?', ALR_TEXT_DOMAIN ),
                )
            ), $this->prefix );

        $buttons_html = $this->_alr_html->buildFormFieldsHtml( array(
            $this->prefix . '_submit_button' => array(
                'title' => 'Register',
                'type' => 'submit',
                'extra' => 'disabled'
                )
            ), $this->prefix );

        $container_classes = apply_filters( $this->prefix . '_form_container_classes', array(
            ALR_NAMESPACE . '_form_container',
            $this->prefix . '_form_container'
            ) );

        $form_classes = apply_filters( $this->prefix . '_form_classes', array(
            ALR_NAMESPACE . '_form'
            ) );

        $button_container_classes = apply_filters( $this->prefix . '_button_container_classes', array(
            ALR_NAMESPACE . '_button_container'
            ) );

        ob_start(); ?>

        <!-- Register Modal -->
        <?php if ( get_option('users_can_register') ) : ?>
            <div class="<?php echo implode( " ", $container_classes ); ?>">
                <?php if ( is_user_logged_in() ) : ?>
                    <p><?php printf('%s <a href="%s" title="%s">%s</a>',
                        __( 'You are already registered', ALR_TEXT_DOMAIN ),
                        wp_logout_url( site_url() ),
                        __( 'Logout', ALR_TEXT_DOMAIN ),
                        __( 'Logout', ALR_TEXT_DOMAIN )
                    ); ?></p>
                <?php else : ?>
                    <form action="javascript://" name="registerform" class="<?php echo implode( " " , $form_classes ); ?>" data-alr_register_security="<?php echo wp_create_nonce( 'setup_new_user' ); ?>">

                        <div class="form-wrapper">
                            <div class="ajax-login-register-status-container">
                                <div class="ajax-login-register-msg-target"></div>
                            </div>

                            <?php echo $fields_html; ?>
                            <?php echo $links_html; ?>

                            <div class="<?php echo implode( " ", $button_container_classes ); ?>">
                                <?php echo $buttons_html; ?>
                            </div>

                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <p><?php _e('Registration is currently closed.', ALR_TEXT_DOMAIN ); ?></p>
        <?php endif; ?>

        <?php return ob_get_clean();
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
    public function setupNewUser( $args=null, $is_ajax=true ) {

        // if ( $is_ajax ) check_ajax_referer('setup_new_user','security');

        // TODO consider using wp_generate_password( $length=12, $include_standard_special_chars=false );
        // and wp_mail the users password asking to change it.

        $user = wp_parse_args( $args, array(
            'user_login' => $_POST['alr_register_user_name'],
            'email' => $_POST['alr_register_email'],
            'user_pass' => $_POST['alr_register_confirm_password']
            ) );

        $valid['email'] = $this->validateEmail( $user['email'], false );
        $valid['username'] = $this->validateUsername( $user['user_login'], false );
        $user_id = null;

        if ( $valid['username']['code'] == 'error' ){

            $status = $this->_alr_helpers->status('invalid_username');

        }

        elseif ( $valid['email']['code'] == 'error' ){

            $status = $this->_alr_helpers->status('invalid_username');

        } else {

            if ( ! isset( $status['code'] ) ){

                $user_id = $this->createUser( $user );

                if ( $user_id == false ){

                    $status = $this->_alr_helpers->status('invalid_username'); // invalid user

                } else {

                    $status = $this->_alr_helpers->status('success_registration'); // success
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
    public function createUser( $user=null, $password=null ){

        $user = wp_parse_args( $user, array(
            'role' => apply_filters( $this->prefix . '_default_role', get_option('default_role') ),
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
                $this->multisiteSetup( $user_id );
            }

            $wp_signon = wp_signon( array(
                'user_login' => $user['user_login'],
                'user_password' => $user['user_pass'],
                'remember' => true ),
            false );

            wp_new_user_notification( $user_id );

            do_action( $this->prefix . '_after_successfull_registration', $user_id );

        }

        return $user_id;

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

            $user_obj = get_user_by( 'id', $user_id );

        }

        return $user_obj;
    }


    /**
     * Adds the user to the networked blog they are currently visiting
     *
     * @since 1.0.9
     * @param $user_id
     * @return true, wp_error object
     */
    public function multisiteSetup( $user_id=null ){

        $added_to_blog = add_user_to_blog(
            get_current_blog_id(),
            $user_id,
            apply_filters( $this->prefix . '_default_role', get_option('default_role') )
        );

        return $added_to_blog;

    }


    /**
     * Process request to pass variables into WordPress' validate_username();
     *
     * @since 1.0.0
     * @uses validate_username()
     * @param $username (string)
     * @param $is_ajax (bool) Process as an AJAX request or not.
     */
    public function validateUsername( $username=null, $is_ajax=true ) {

        $username = empty( $_POST['alr_register_user_name'] ) ? esc_attr( $username ) : $_POST['alr_register_user_name'];

        if ( validate_username( $username ) ) {
            $user_id = username_exists( $username );
            if ( $user_id ){
                $msg = $this->_alr_helpers->status('username_exists');
            } else {
                $msg = $this->_alr_helpers->status('valid_username');
            }
        } else {
            $msg = $this->_alr_helpers->status('invalid_username');
        }

        if ( $is_ajax ){
            wp_send_json( $msg );
        } else {
            return $msg;
        }
    }


    /**
     * Check if an email is "valid" using PHPs filter_var & WordPress
     * email_exists();
     *
     * @param $email (string) Emailt to be validated
     * @param $is_ajax (bool)
     * @todo check ajax refer
     */
    public function validateEmail( $email=null, $is_ajax=true ) {

        $email = is_null( $email ) ? $email : $_POST['alr_register_email'];

        if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $msg = $this->_alr_helpers->status('email_invalid');
        } else if ( email_exists( $email ) ){
            $msg = $this->_alr_helpers->status('email_in_use');
        } else {
            $msg = $this->_alr_helpers->status('email_valid');
        }

        if ( $is_ajax ){
            wp_send_json( $msg );
        } else {
            return $msg;
        }
    }


}

function alr_plugins_loaded_register(){

    new ALRRegister( new ZM_Dependency_Container( null ) );

}
add_action( 'plugins_loaded', 'alr_plugins_loaded_register' );