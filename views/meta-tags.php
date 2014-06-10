<!-- Start: Ajax Login Register Meta Tags -->
<?php if ( get_option('ajax_login_register_facebook') ) : ?>
    <?php $a = New Login; $fb = $a->get_settings(); foreach( $fb['facebook'] as $setting ) : ?>
        <meta property="<?php echo ( $setting['key'] == 'admins' || $setting['key'] == 'app_id' ) ? 'fb:' : 'og:'; ?><?php print $setting['key']; ?>" content="<?php print get_option( $setting['key'] ); ?>" />
        <?php $app_id = $setting['key'] == 'app_id' ? get_option( $setting['key'] ) : null; ?>
    <?php endforeach; ?>
    <meta property="og:title" content="<?php wp_title( '|', true, 'right' ); ?>" />
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
</script>

<style type="text/css"><?php echo wp_kses_stripslashes( get_option('ajax_login_register_additional_styling') ); ?></style>
<!-- End: Ajax Login Register Meta Tags -->