<?php

class ALRSocialFacebook {

    public function __construct(){

        $this->prefix = 'alr_social_facebook';

        add_action( 'wp_ajax_facebook_login', array( &$this, 'facebook_login' ) );
        add_action( 'wp_ajax_nopriv_facebook_login', array( &$this, 'facebook_login') );
        add_action( 'wp_head', array( &$this, 'head' ) );

        // if( get_option( 'ajax_login_register_facebook' ) && get_option( 'fb_avatar' ) )
        //     add_filter( 'get_avatar', array( &$this, 'load_fb_avatar' ) , 1, 5 );


        add_filter( 'alr_login_above_fields', array( &$this, 'aboveLoginFields' ) );
        add_filter( 'alr_social_settings_fields_tab', array( &$this, 'settings' ) );
    }


    /**
     * Creates a new user in WordPress using their FB account info.
     *
     * @uses setup_new_user();
     */
    public function facebook_login(){

        // check_ajax_referer( 'facebook-nonce', 'security' );

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

                wp_set_auth_cookie( $user_id, true );

                $status = $this->status('success_login');

            } else {
                $status = $this->status('invalid_username');
            }
        }

        wp_send_json( $status );

    }


    /**
     * Replaces the default engravatar with the Facebook profile picture.
     *
     * @param string $avatar The default avatar
     *
     * @param int $id_or_email The user id
     *
     * @param int $size The size of the avatar
     *
     * @param string $default The url of the Wordpress default avatar
     *
     * @param string $alt Alternate text for the avatar.
     *
     * @return string $avatar The modified avatar
     */
    public function load_fb_avatar($avatar, $id_or_email, $size, $default, $alt ) {
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

        global $alr_settings;

        if ( $alr_settings[ $this->prefix . '_fb_enabled' ] == 'off' ){
            $enabled = false;
        } else {
            $enabled = true;
        }

        return $enabled;

    }


    // The Fb button
    public function aboveLoginFields(){

        if ( ! $this->isEnabled() )
            return;

        $container_classes = implode( " ", array(
            'fb-login-container',
            ALR_NAMESPACE . '_fb_login_container',
            $this->prefix . '_fb_login_container'
            ) );

        $html = sprintf( '<div class="%s"><a href="#" class="fb-login" data-alr_facebook_security="%s">%s</a></div>',
            $container_classes,
            wp_create_nonce( 'facebook-nonce' ),
            __( 'Log in using Facebook', ALR_TEXT_DOMAIN )
            );

        return $html;

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
                'title' => __( 'Facebook Settings', ALR_TEXT_DOMAIN ),
                'type' => 'header'
                ),
            array(
                'id' => $this->prefix . '_fb_enabled',
                'type' => 'checkbox',
                'title' => __( 'Enable', ALR_TEXT_DOMAIN ),
                'std' => 'off',
                'desc' => __( 'By enabling this setting visitors will be able to login with Facebook.', ALR_TEXT_DOMAIN )
            ),
            array(
                'id' => $this->prefix . '_fb_url',
                'type' => 'url',
                'title' => __( 'URL', ALR_TEXT_DOMAIN ),
                'desc' => __( 'This is the URL you have set in your Facebook Developer App Settings', ALR_TEXT_DOMAIN )
            ),
            array(
                'id' => $this->prefix . '_fb_app_id',
                'type' => 'fancyText',
                'title' => __( 'App ID', ALR_TEXT_DOMAIN ),
                'desc' => __( 'This is the App ID as seen in your <a href="https://developers.facebook.com/">Facebook Developer</a> App Dashboard. For detailed instructions visit the <a href="http://zanematthew.com/ajax-login-register-help-videos/" target="_blank">How To add Facebook Settings to AJAX Login & Register</a>.', ALR_TEXT_DOMAIN )

            ),
            array(
                'id' => $this->prefix . '_fb_use_avatar',
                'type' => 'checkbox',
                'title' => __( 'Use Facebook Avatar', ALR_TEXT_DOMAIN ),
                'desc' => __( 'Checking this box will make Facebook profile picture show as avatar when possible ', ALR_TEXT_DOMAIN )
            )
        );


        $current_settings = array_merge( $current_settings, $settings );

        return $current_settings;

    }


    // add our meta and FB script
    public function head(){

        if ( ! $this->isEnabled() )
            return;

        global $alr_settings;

        $fb_url = esc_url( $alr_settings[ $this->prefix . '_fb_url' ] );
        $app_id = esc_attr( $alr_settings[ $this->prefix . '_fb_app_id' ] );

        ?>

        <!-- Start: <?php echo ALR_NAMESPACE; ?> Facebook meta property -->
        <meta property="og:<?php echo $fb_url; ?>" content="<?php echo $fb_url; ?>"/>
        <meta property="fb:<?php echo $app_id; ?>" content="<?php echo $app_id; ?>"/>
        <!-- End: <?php echo ALR_NAMESPACE; ?> Facebook meta property -->

        <!-- Start: <?php echo ALR_NAMESPACE; ?> Facebook script -->
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
        <!-- End: <?php echo ALR_NAMESPACE; ?> Facebook script -->

    <?php }
}
new ALRSocialFacebook;