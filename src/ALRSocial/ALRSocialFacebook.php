<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class ALRSocialFacebook {

    /**
     * The prefix used for meta keys, CSS classes, html IDs, etc.
     *
     * @since 2.0.0
     */
    public $prefix;


    /**
     * An object containing additional helper functions
     *
     * @since 2.0.0
     */
    public $_zm_alr_helpers;


    /**
     * Adding of all hooks
     *
     * @since 2.0.0
     *
     * @param
     * @return
     */
    public function __construct( ZM_Dependency_Container $di ){

        $this->prefix = 'zm_alr_social_facebook';
        $this->_zm_alr_helpers = $di->get_instance( 'helpers', 'ALRHelpers', null );

        add_action( 'wp_ajax_facebook_login', array( &$this, 'facebook_login' ) );
        add_action( 'wp_ajax_nopriv_facebook_login', array( &$this, 'facebook_login') );
        add_action( 'wp_head', array( &$this, 'head' ) );

        add_filter( 'get_avatar', array( &$this, 'load_fb_avatar' ) , 1, 5 );
        add_filter( 'zm_alr_login_above_fields', array( &$this, 'aboveLoginFields' ) );
        add_filter( 'zm_alr_register_above_fields', array( &$this, 'aboveLoginFields' ) );
        add_filter( 'zm_alr_social_settings_fields_tab', array( &$this, 'settings' ) );

    }


    /**
     * Maps our FB response fields to the correct user fields as found in wp_update_user. Then
     * calls setUpNewFacebookUser, and passes the correct response via JSON to JS.
     *
     * @since 2.0.0
     *
     * @return  JSON    A JSON object
     */
    public function facebook_login(){

        check_ajax_referer( 'facebook-nonce', 'security' );

        $user = array(
            'username'   => $_POST['fb_response']['id'],
            'user_login' => $_POST['fb_response']['id'],
            'first_name' => $_POST['fb_response']['first_name'],
            'last_name'  => $_POST['fb_response']['last_name'],
            'email'      => $_POST['fb_response']['email'],
            'user_url'   => $_POST['fb_response']['link'],
            'fb_id'      => $_POST['fb_response']['id']
            );

        if ( empty( $user['username'] ) ){

            $status = $this->_zm_alr_helpers->status('invalid_username');
            $user_id = false;

        } else {

            $user_obj = get_user_by( 'login', $user['user_login'] );

            if ( $user_obj == false ){

                $user_obj = $this->setupNewFacebookUser( $user );

            }

            // A WP user account already exists that is NOT associated with a FB account
            if ( $user_obj == 'existing_user_email' ){

                $status = $this->_zm_alr_helpers->status('username_exists');

            } elseif ( $user_obj ){

                $user_id = $user_obj->ID;
                wp_set_auth_cookie( $user_id, true );
                $status = $this->_zm_alr_helpers->status('success_login');

            } else {

                $status = $this->_zm_alr_helpers->status('invalid_username');

            }
        }

        $status = array_merge( $status, $this->registerRedirect( $user['user_login'] ) );

        wp_send_json( $status );

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
    public function setupNewFacebookUser( $user=array() ){

        $user_id = $this->_zm_alr_helpers->createUser( array_merge( $user, array(
            'user_pass' => wp_generate_password()
        ) ), $this->prefix );


        if ( is_wp_error( $user_id ) ){

            $user_obj = $user_id->get_error_code();

        } else {

            $user_obj = get_user_by( 'id', $user_id );

        }

        return $user_obj;

    }


    /**
     * Replaces the default gravatar with the Facebook profile picture.
     *
     * @param string $avatar The default avatar
     * @param int $id_or_email The user id
     * @param int $size The size of the avatar
     * @param string $default The URL of the WordPress default avatar
     * @param string $alt Alternate text for the avatar.
     *
     * @return string $avatar The modified avatar
     */
    public function load_fb_avatar( $avatar, $id_or_email, $size, $default, $alt ) {

        global $zm_alr_settings;

        if ( empty( $zm_alr_settings[ $this->prefix . '_use_avatar' ] )
            && $zm_alr_settings[ $this->prefix . '_use_avatar' ] != 1 ){

            return $avatar;

        }

        $user = false;

        if ( is_numeric( $id_or_email ) ) {

            $id = (int) $id_or_email;
            $user = get_user_by( 'id' , $id );

        } elseif ( is_object( $id_or_email ) ) {

            if ( ! empty( $id_or_email->user_id ) ) {
                $id = (int) $id_or_email->user_id;
                $user = get_user_by( 'id' , $id );
            }

        } else {
            $user = get_user_by( 'email', $id_or_email );
        }
        if ( $user && is_object( $user ) ) {
            $user_id = $user->data->ID;

            // We can use username as ID but checking the usermeta we are sure this is a facebook user
            if( $fb_id = get_user_meta( $user_id, 'fb_id', true ) ) {
                $fb_url = 'https://graph.facebook.com/' . $fb_id . '/picture?width='. $size . '&height=' . $size;
                $avatar = "<img alt='facebook-profile-picture' src='{$fb_url}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";

            }

        }
        return $avatar;

    }


    /**
     * Determine if the Facebook login setting is set.
     *
     * @since 2.0.0
     *
     * @return BOOL
     */
    public function isEnabled(){

        global $zm_alr_settings;

        if ( $zm_alr_settings[ $this->prefix . '_enabled' ] == 'off' ){
            $enabled = false;
        } else {
            $enabled = true;
        }

        return $enabled;

    }


    /**
     * Filters the fields above the Login form and displays the FB button.
     *
     * @since 2.0.0
     *
     * @return The FB button
     */
    public function aboveLoginFields( $above_html ){

        if ( ! $this->isEnabled() )
            return $above_html;

        $container_classes = implode( " ", array(
            'fb-login-container',
            ZM_ALR_NAMESPACE . '_login_container',
            $this->prefix . '_login_container'
            ) );

        global $zm_alr_settings;

        if ( is_int( $zm_alr_settings[ $this->prefix . '_login_button' ] ) ){
            $logo_class = null;
            $text = '<img src="' . wp_get_attachment_url( $zm_alr_settings[ $this->prefix . '_login_button' ] ) . '" />';
        } else {
            $logo_class = 'fb-login-logo';
            $text = __( 'Log in using Facebook', ZM_ALR_TEXT_DOMAIN );
        }

        $above_html .= sprintf( '<div class="%s"><a href="#" class="fb-login %s" data-zm_alr_facebook_security="%s">%s</a></div>',
            $container_classes,
            $logo_class,
            wp_create_nonce( 'facebook-nonce' ),
            $text
            );

        return $above_html;

    }


    /**
     * Filters the default settings, adding the additional settings below.
     *
     * @since   2.0.0
     *
     * @param   $current_settings   The current global settings
     *
     * @return  $settings           The current global settings with the additional FB settings
     */
    public function settings( $current_settings ){

        // Facebook
        $settings = array(
            array(
                'title' => __( 'Facebook Settings', ZM_ALR_TEXT_DOMAIN ),
                'type' => 'header'
                ),
            array(
                'id' => $this->prefix . '_enabled',
                'type' => 'checkbox',
                'title' => __( 'Enable', ZM_ALR_TEXT_DOMAIN ),
                'std' => 'off',
                'desc' => __( 'By enabling this setting visitors will be able to login with Facebook.', ZM_ALR_TEXT_DOMAIN )
            ),
            array(
                'id' => $this->prefix . '_login_button',
                'type' => 'upload',
                'title' => __( 'Login Button', ZM_ALR_TEXT_DOMAIN ),
                'std' => ZM_ALR_URL . 'assets/images/facebook-screen-grab.png',
                'desc' => __( 'Upload a custom image to be displayed as the Facebook login button.', ZM_ALR_TEXT_DOMAIN )
            ),
            array(
                'id' => $this->prefix . '_app_id',
                'type' => 'fancyText',
                'title' => __( 'App ID', ZM_ALR_TEXT_DOMAIN ),
                'desc' => __( 'This is the App ID as seen in your <a href="https://developers.facebook.com/">Facebook Developer</a> App Dashboard. For detailed instructions visit the <a href="http://zanematthew.com/ajax-login-register-help-videos/" target="_blank">How To add Facebook Settings to AJAX Login & Register</a>.', ZM_ALR_TEXT_DOMAIN )

            ),
            array(
                'id' => $this->prefix . '_use_avatar',
                'type' => 'checkbox',
                'std' => 'off',
                'title' => __( 'Use Facebook Avatar', ZM_ALR_TEXT_DOMAIN ),
                'desc' => __( 'Checking this box will make Facebook profile picture show as avatar when possible ', ZM_ALR_TEXT_DOMAIN )
            )
        );


        $current_settings = array_merge( $current_settings, $settings );

        return $current_settings;

    }



    /**
     * Adds our meta and FB script to the HTML head via wp_head
     *
     * @since   2.0.0
     *
     * @return  Adds the needed meta fields for FB.
     */
    public function head(){

        if ( ! $this->isEnabled() )
            return;

        global $zm_alr_settings;

        $app_id = esc_attr( $zm_alr_settings[ $this->prefix . '_app_id' ] );

        ?>

        <!-- Start: <?php echo ZM_ALR_NAMESPACE; ?> Facebook meta property -->
        <meta property="fb:<?php echo $app_id; ?>" content="<?php echo $app_id; ?>"/>
        <!-- End: <?php echo ZM_ALR_NAMESPACE; ?> Facebook meta property -->

        <!-- Start: <?php echo ZM_ALR_NAMESPACE; ?> Facebook script -->
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : "<?php echo $app_id; ?>", // App ID
                    cookie     : true,  // enable cookies to allow the server to access the session
                    xfbml      : true,  // parse XFBML
                    version    : 'v2.3' // use version 2.3
                });
            };
            // Load the SDK asynchronously
            // This is updated as the old version went to all.js
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/<?php echo get_locale(); ?>/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <!-- End: <?php echo ZM_ALR_NAMESPACE; ?> Facebook script -->

    <?php }


    public function registerRedirect( $user_login=null, $status=null ){
        // Since this is handled via an AJAX request $wp->request is always empty
        // @todo Submit to core
        // global $wp;
        // $tmp = trailingslashit( add_query_arg( '', '', site_url( $wp->request ) ) );
        $current_url = empty( $_SERVER['HTTP_REFERER'] ) ? site_url( $_SERVER['REQUEST_URI'] ) : $_SERVER['HTTP_REFERER'];
        $redirect['redirect_url'] = apply_filters( $this->prefix . '_redirect_url', $current_url, $user_login, $status );

        return $redirect;
    }

}
