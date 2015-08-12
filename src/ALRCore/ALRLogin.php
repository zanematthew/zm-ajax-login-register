<?php

Class ALRLogin {

    public function __construct( ZM_Dependency_Container $di ){

        $this->_alr_html = $di->get_instance( 'html', 'ALRHtml', null );
        $this->_alr_helpers = $di->get_instance( 'helpers', 'ALRHelpers', null );
        $this->prefix = 'alr_login';

        add_action( 'alr_init', array( &$this, 'init' ) );

    }

    public function init(){

        add_shortcode( 'ajax_login', array( &$this, 'shortcode' ) );
        add_action( 'wp_ajax_login_submit', array( &$this, 'loginSubmit' ) );
        add_action( 'wp_ajax_nopriv_login_submit', array( &$this, 'loginSubmit' ) );

        // add_action( 'wp_ajax_nopriv_load_template', array( &$this, 'load_template' ) );
        // add_action( 'wp_ajax_load_template', array( &$this, 'load_template' ) );
        /**
         * Load the login form via an AJAX request.
         *
         * @package AJAX
         */
        // public function load_template(){

        //     check_ajax_referer( $_POST['referer'],'security');

        //     $file_name = sanitize_file_name( $_POST['template'] );
        //     $valid_file_names = array(
        //         'login-form',
        //         'register-form'
        //         );

        //     if ( in_array( $file_name, $valid_file_names ) ){
        //         load_template( plugin_dir_path( dirname( __FILE__ ) ) . "views/" . $file_name . '.php' );
        //     }

        //     die();
        // }

    }


    public function shortcode(){


        // Build various HTML elements
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
                )
            ), $this->prefix );

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

        $buttons_html = $this->_alr_html->buildFormFieldsHtml( array(
            $this->prefix . '_submit_button' => array(
                'title' => 'Login',
                'type' => 'submit'
                )
            ), $this->prefix );


        // Assign various CSS classes to be filterable via themes/plugins/add-ons
        $container_classes = apply_filters( $this->prefix . '_form_container_classes', array(
            ALR_NAMESPACE . '_form_container',
            $this->prefix . '_form_container'
            ) );

        $form_classes = apply_filters( $this->prefix . '_form_classes', array() );

        $button_container_classes = apply_filters( $this->prefix . '_button_container_classes', array(
            ALR_NAMESPACE . '_button_container'
            ) );

        ob_start(); ?>

        <!-- Login Form -->
        <div class="<?php echo implode( " ", $container_classes ); ?>">

            <?php if ( is_user_logged_in() ) : ?>

                <p class="<?php echo $this->prefix; ?>_text"><?php printf("%s <a href=%s title='%s'>%s</a>",
                    __('You are already logged in', ALR_TEXT_DOMAIN ), // Text
                    wp_logout_url( site_url() ), // URL
                    __('Logout', ALR_TEXT_DOMAIN ), // Link text
                    __('Logout', ALR_TEXT_DOMAIN ) // Link title text
                );?></p>

            <?php else : ?>

                <form action="javascript://" class="<?php echo implode( " ", $form_classes ); ?>" data-alr_login_security="<?php echo wp_create_nonce( 'login_submit' ); ?>">

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
        <!-- End Login Form -->

        <?php return ob_get_clean();
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
    public function loginSubmit( $user_login=null, $password=null, $is_ajax=true ) {

        /**
         * Verify the AJAX request
         */
        // if ( $is_ajax ) check_ajax_referer('login_submit','security');

        $args = array(
            'login' => sanitize_user( $_POST['alr_login_user_name'] ),
            'password' => $_POST['alr_login_password'],
            'remember' => ( $_POST['remember'] == 'on' ) ? true : false
        );

        // Currently wp_signon returns the same error code 'invalid_username' if
        // a username does not exists or is invalid
        if ( validate_username( $args['login'] ) ){

            if ( username_exists( $args['login'] ) ){

                // if option force check password
                global $alr_settings;

                if ( $alr_settings['alr_misc_force_check_password'] == 'on' ){

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
}

function alr_plugins_loaded_login(){

    new ALRLogin( new ZM_Dependency_Container( null ) );

}
add_action( 'plugins_loaded', 'alr_plugins_loaded_login' );