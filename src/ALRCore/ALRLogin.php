<?php

Class ALRLogin {

    /**
     * Init various classes, and run the init action for ALR
     *
     * @since 2.0.0
     *
     * @param An classes that this class needs
     * @return
     */
    public function __construct( ZM_Dependency_Container $di ){

        $this->_alr_html = $di->get_instance( 'html', 'ALRHtml', null );
        $this->_alr_helpers = $di->get_instance( 'helpers', 'ALRHelpers', null );
        $this->prefix = 'alr_login';

        add_action( 'alr_init', array( &$this, 'init' ) );

    }


    /**
     * Various WordPress hooks are fired here
     *
     * @since 2.0.0
     *
     */
    public function init(){

        add_shortcode( 'ajax_login', array( &$this, 'shortcode' ) );
        add_action( 'wp_ajax_login_submit', array( &$this, 'loginSubmit' ) );
        add_action( 'wp_ajax_nopriv_login_submit', array( &$this, 'loginSubmit' ) );
        add_action( 'wp_footer', array( &$this, 'footer' ) );

        add_action( 'wp_ajax_nopriv_load_template', array( &$this, 'load_template' ) );
        add_action( 'wp_ajax_load_template', array( &$this, 'load_template' ) );

    }


    /**
     * Load the login form via an AJAX request.
     *
     * @since 2.0.0
     *
     */
    public function load_template(){

        // check_ajax_referer( $_POST['referer'], 'security' );

        $msg = $this->getLogInForm();

        wp_send_json_success( $msg );

    }


    /**
     * Process the shortcode, either load the login form, or display a message
     *
     * @since 2.0.0
     *
     * @return
     */
    public function shortcode(){

        if ( is_user_logged_in() ) {

            $html = sprintf(
                "<p class='%s_text'>%s <a href=%s title='%s'>%s</a></p>",
                $this->prefix,
                __('You are already logged in', ALR_TEXT_DOMAIN ), // Text
                wp_logout_url( site_url() ), // URL
                __('Logout', ALR_TEXT_DOMAIN ), // Link text
                __('Logout', ALR_TEXT_DOMAIN ) // Link title text
            );

        } else {

            $html = $this->getLogInForm();

        }

        return $html;
    }


    /**
     * Build the Login form HTML.
     *
     * Dynamic filters/actions are done in the method buildFormFieldsHtml() and NOT here!
     *
     * @since 2.0.0
     *
     * @return
     */
    public function getLogInForm(){

        $links_html = $this->_alr_html->buildFormHtmlLinks( array(
            $this->prefix . '_not_a_member' => array(
                'href' => '#',
                'class' => 'not-a-member-handle',
                'text' => __( 'Are you a member?', ALR_TEXT_DOMAIN ),
                ),
            $this->prefix . '_lost_password_url' => array(
                'href' => wp_lostpassword_url(),
                'class' => '',
                'text' => __( 'Forgot Password',ALR_TEXT_DOMAIN )
                )
            ), $this->prefix );

        $form_classes = implode( " ", apply_filters( $this->prefix . '_form_classes', array(
            ALR_NAMESPACE . '_form'
        ) ) );

        $fields_html = $this->_alr_html->buildFormFieldsHtml( array(
            $this->prefix . '_user_name' => array(
                'title' => 'User Name',
                'type' => 'text',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_password' => array(
                'title' => 'Password',
                'type' => 'password',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_keep_me_logged_in' => array(
                'title' => 'Keep Me Logged In',
                'type' => 'checkbox'
                ),
            $this->prefix . '_submit_button' => array(
                'title' => 'Login',
                'type' => 'submit'
                )
            ), $this->prefix );

        $container_classes = implode( " ", apply_filters( $this->prefix . '_form_container_classes', array(
            ALR_NAMESPACE . '_form_container',
            $this->prefix . '_form_container'
        ) ) );

        $html = null;
        $html .= '<form action="javascript://" class="'. $form_classes . '" data-alr_login_security="'. wp_create_nonce( 'login_submit' ) . '">';
        $html .= '<div class="form-wrapper">';
        $html .= '<div class="ajax-login-register-status-container">';
        $html .= '<div class="ajax-login-register-msg-target"></div>';
        $html .= '</div>';
        $html .= $fields_html . $links_html;
        $html .= '</div>';
        $html .= '</form>';

        return '<div class="'. $container_classes . '">' . $html . '</div>';

    }


    /**
     * Processes credentials to pass into wp_signon to log a user into WordPress.
     *
     * @since 2.0.0.
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
    public function loginSubmit( $user_login=null, $password=null, $is_ajax=true ) {

        /**
         * Verify the AJAX request
         */
        // if ( $is_ajax ) check_ajax_referer('login_submit','security');

        $args = array(
            'login' => sanitize_user( $_POST['alr_login_user_name'] ),
            'password' => $_POST['alr_login_password'],
            'remember' => empty( $_POST['remember'] ) ? false : ture
        );

        // Currently wp_signon returns the same error code 'invalid_username' if
        // a username does not exists or is invalid
        if ( validate_username( $args['login'] ) ){

            if ( username_exists( $args['login'] ) ){

                // if option force check password
                global $alr_settings;

                // Better to do via a hook from within alr_misc.
                if ( $alr_settings['alr_misc_force_check_password'] == 'alr_misc_yes' ){

                    $user = get_user_by( 'login', $args['login'] );
                    if ( wp_check_password( $args['password'], $user->data->user_pass, $user->ID ) ){

                        $status = $this->_alr_helpers->status('success_login');
                        wp_signon( array(
                            'user_login'    => $args['login'],
                            'user_password' => $args['password'],
                            'remember'      => $args['remember']
                            ), false );
                    }

                } else {

                    $creds = array(
                        'user_login'    => $args['login'],
                        'user_password' => $args['password'],
                        'remember'      => $args['remember']
                        );

                    $user = wp_signon( $creds, false );

                    if ( is_wp_error( $user ) ){
                        $status = $this->_alr_helpers->status( $user->get_error_code() );
                    } else {
                        $status = $this->_alr_helpers->status('success_login');
                    }
                }

            } else {

                $status = $this->_alr_helpers->status('username_does_not_exists');

            }

        } else {

            $status = $this->_alr_helpers->status('invalid_username');

        }

        if ( $is_ajax ) {
            wp_send_json( $status );
        } else {
            return $status;
        }
    }


    /**
     * Add the Login HTML dialog box by hooking into wp_footer
     *
     * @since 2.0.0
     *
     * @return
     */
    public function footer(){

        /**
         * Markup needed for jQuery UI dialog, our form is actually loaded via AJAX
         */
        $classes = implode( ' ', apply_filters( $this->prefix . '_dialog_class', array(
            $this->prefix . '_dialog',
            ALR_NAMESPACE . '_dialog'
            ) ) ); ?>
        <div id="ajax-login-register-login-dialog" class="<?php echo $classes; ?>" title="<?php _e( 'Login', ALR_TEXT_DOMAIN ); ?>" data-security="<?php print wp_create_nonce( 'login_form' ); ?>">
            <div id="ajax-login-register-login-target" class="ajax-login-register-login-dialog"><?php _e( 'Loading...', ALR_TEXT_DOMAIN ); ?>
            </div>
            <?php do_action( $this->prefix . '_after_dialog' ); ?>
        </div>
    <?php }
}


/**
 * Once plugins are loaded init our class
 */
function alr_plugins_loaded_login(){

    new ALRLogin( new ZM_Dependency_Container( null ) );

}
add_action( 'plugins_loaded', 'alr_plugins_loaded_login' );