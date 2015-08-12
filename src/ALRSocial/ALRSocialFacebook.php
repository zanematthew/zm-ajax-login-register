<?php

class ALRSocialFacebook {

    public function __construct(){

        add_action( 'wp_ajax_facebook_login', array( &$this, 'facebook_login' ) );
        add_action( 'wp_ajax_nopriv_facebook_login', array( &$this, 'facebook_login') );

        // if( get_option( 'ajax_login_register_facebook' ) && get_option( 'fb_avatar' ) )
        //     add_filter( 'get_avatar', array( &$this, 'load_fb_avatar' ) , 1, 5 );
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


    public function fbStuff(){ ?>

        <?php if ( get_option('ajax_login_register_facebook') ) : ?>
        <!-- Start: Ajax Login Register Facebook meta tags -->
        <?php
        // $a = New ajax_login_register_Login;
        // $fb = $a->get_settings();
        $fb = null;
        foreach( $fb['facebook'] as $setting ) :

            if ( $setting['key'] == 'app_id' ) {
                $key = 'fb';
            } else {
                $key = 'og';
            }

            $value = get_option( $setting['key'] );

            ?>
            <?php if ( ! empty( $value ) ) : ?>
                <meta property="<?php echo $key; ?>:<?php echo $value; ?>" content="<?php print $value; ?>" />
            <?php endif; ?>
        <?php endforeach; ?>
        <!-- End: Ajax Login Register Facebook meta tags -->
        <?php $app_id = get_option( 'app_id' ) ; ?>
        <!-- Start: Ajax Login Register Facebook script -->
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : "<?php esc_attr_e( $app_id ); ?>", // App ID
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
        <!-- End: Ajax Login Register Facebook script -->
    <?php endif; ?>
    <?php }
}