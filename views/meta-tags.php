<!-- Start: Ajax Login Register Meta Tags -->
<?php if ( get_option('ajax_login_register_facebook') ) : ?>
    <?php $a = New Login; $fb = $a->get_settings(); foreach( $fb['facebook'] as $setting ) : ?>
        <meta property="og:<?php print $setting['key']; ?>" content="<?php print get_option( $setting['key'] ); ?>" />
        <?php $app_id = $setting['key'] == 'app_id' ? get_option( $setting['key'] ) : null; ?>
    <?php endforeach; ?>
<?php endif; ?>

<script type="text/javascript">
    <?php if ( get_option('ajax_login_register_facebook') ) : ?>
        window.fbAsyncInit = function() {
            FB.init({
                appId      : <?php print $app_id; ?>, // App ID
                channelUrl : '//'+location.origin+'/channel.html', // Channel File
                status     : true, // check login status
                cookie     : true, // enable cookies to allow the server to access the session
                xfbml      : true  // parse XFBML
            });
        };
        (function(d){
            var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement('script'); js.id = id; js.async = true;
            js.src = "//connect.facebook.net/en_US/all.js";
            ref.parentNode.insertBefore(js, ref);
        }(document));
    <?php endif; ?>

    <?php

    $redirect_url = get_option('ajax_login_register_redirect');
    $redirect_url = empty( $redirect_url ) ? site_url($_SERVER['REQUEST_URI']) : $redirect_url;
    $redirect_url = apply_filters( 'zm_ajax_login_redirect', $redirect_url );

    ?>
    var _ajax_login_settings = {
        login_handle: "<?php print get_option('ajax_login_register_advanced_usage_login'); ?>",
        register_handle: "<?php print get_option('ajax_login_register_advanced_usage_register'); ?>",
        redirect: "<?php echo $redirect_url; ?>",
        dialog_width: "<?php

        $width = array(
            'default' => 265,
            'wide' => 440,
            'extra_buttons' => 666
            );

        $style = get_option('ajax_login_register_default_style');
        $fb_button = get_option('ajax_login_register_facebook');

        if ( $style == 'wide' && $fb_button ){
            $key = 'extra_buttons';
        } elseif ( $style == 'wide' ){
            $key = 'wide';
        } else {
            $key = 'default';
        }

        print $width[ $key ];

        ?>"
    };

</script>

<style type="text/css"><?php echo wp_kses_stripslashes( get_option('ajax_login_register_additional_styling') ); ?></style>
<!-- End: Ajax Login Register Meta Tags -->