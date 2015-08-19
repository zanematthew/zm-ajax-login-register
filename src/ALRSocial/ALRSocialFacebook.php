<?php

Class ALRSocialFacebook {

    public function __construct( ZM_Dependency_Container $di ){

        $this->prefix = 'zm_alr_social_facebook';
        $this->_zm_alr_helpers = $di->get_instance( 'helpers', 'ALRHelpers', null );

        add_action( 'wp_ajax_facebook_login', array( &$this, 'facebook_login' ) );
        add_action( 'wp_ajax_nopriv_facebook_login', array( &$this, 'facebook_login') );
        add_action( 'wp_head', array( &$this, 'head' ) );

        add_filter( 'get_avatar', array( &$this, 'load_fb_avatar' ) , 1, 5 );
        add_filter( 'zm_alr_login_above_fields', array( &$this, 'aboveLoginFields' ) );
        add_filter( 'zm_alr_social_settings_fields_tab', array( &$this, 'settings' ) );
    }


    /**
     * Creates a new user in WordPress using their FB account info.
     *
     * @uses setup_new_user();
     */
    public function facebook_login(){

        // check_ajax_referer( 'facebook-nonce', 'security' );

// print_r( $_POST );
// die("\nfb");

        // Map our FB response fields to the correct user fields as found in wp_update_user
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

        if ( empty( $zm_alr_settings[ $this->prefix . '_fb_use_avatar' ] )
            && $zm_alr_settings[ $this->prefix . '_fb_use_avatar' ] != 1 ){

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


    // Is it enabled
    public function isEnabled(){

        global $zm_alr_settings;

        if ( $zm_alr_settings[ $this->prefix . '_fb_enabled' ] == 'off' ){
            $enabled = false;
        } else {
            $enabled = true;
        }

        return $enabled;

    }


    // The Fb button
    public function aboveLoginFields( $above_html ){

        if ( ! $this->isEnabled() )
            return $above_html;

        $container_classes = implode( " ", array(
            'fb-login-container',
            ZM_ALR_NAMESPACE . '_fb_login_container',
            $this->prefix . '_fb_login_container'
            ) );

        $above_html .= sprintf( '<div class="%s"><a href="#" class="fb-login" data-zm_alr_facebook_security="%s">%s</a></div>',
            $container_classes,
            wp_create_nonce( 'facebook-nonce' ),
            __( 'Log in using Facebook', ZM_ALR_TEXT_DOMAIN )
            );

        return $above_html;

    }


    /**
     * Filters the default settings, adding the additional settings below.
     *
     * @since 1.0.0
     */
    public function settings( $current_settings ){

        // Facebook
        $settings = array(
            array(
                'title' => __( 'Facebook Settings', ZM_ALR_TEXT_DOMAIN ),
                'type' => 'header'
                ),
            array(
                'id' => $this->prefix . '_fb_enabled',
                'type' => 'checkbox',
                'title' => __( 'Enable', ZM_ALR_TEXT_DOMAIN ),
                'std' => 'off',
                'desc' => __( 'By enabling this setting visitors will be able to login with Facebook.', ZM_ALR_TEXT_DOMAIN )
            ),
            array(
                'id' => $this->prefix . '_fb_app_id',
                'type' => 'fancyText',
                'title' => __( 'App ID', ZM_ALR_TEXT_DOMAIN ),
                'desc' => __( 'This is the App ID as seen in your <a href="https://developers.facebook.com/">Facebook Developer</a> App Dashboard. For detailed instructions visit the <a href="http://zanematthew.com/ajax-login-register-help-videos/" target="_blank">How To add Facebook Settings to AJAX Login & Register</a>.', ZM_ALR_TEXT_DOMAIN )

            ),
            array(
                'id' => $this->prefix . '_fb_use_avatar',
                'type' => 'checkbox',
                'std' => 'off',
                'title' => __( 'Use Facebook Avatar', ZM_ALR_TEXT_DOMAIN ),
                'desc' => __( 'Checking this box will make Facebook profile picture show as avatar when possible ', ZM_ALR_TEXT_DOMAIN )
            )
        );


        $current_settings = array_merge( $current_settings, $settings );

        return $current_settings;

    }


    // add our meta and FB script
    public function head(){

        if ( ! $this->isEnabled() )
            return;

        global $zm_alr_settings;

        $app_id = esc_attr( $zm_alr_settings[ $this->prefix . '_fb_app_id' ] );

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
}
/**
 * Once plugins are loaded init our class
 */
function zm_alr_plugins_loaded_social_facebook(){

    new ALRSocialFacebook( new ZM_Dependency_Container( null ) );

}
add_action( 'plugins_loaded', 'zm_alr_plugins_loaded_social_facebook' );