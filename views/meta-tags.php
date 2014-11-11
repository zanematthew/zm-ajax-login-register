<?php if ( get_option('ajax_login_register_facebook') ) : ?>
    <!-- Start: Ajax Login Register Facebook meta tags -->
    <?php
    $a = New ajax_login_register_Login;
    $fb = $a->get_settings();
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
    <meta property="og:title" content="<?php wp_title( '|', true, 'right' ); ?>" />
    <!-- End: Ajax Login Register Facebook meta tags -->
<?php endif; ?>

<?php if ( get_option('ajax_login_register_facebook') ) : ?>
    <?php $app_id = $setting['key'] == 'app_id' ? get_option( $setting['key'] ) : null; ?>
    <!-- Start: Ajax Login Register Facebook script -->
    <script type="text/javascript">
        window.fbAsyncInit = function() {
            FB.init({
                appId      : <?php print $app_id; ?>, // App ID
                cookie     : true,  // enable cookies to allow the server to access the session
                xfbml      : true,  // parse XFBML
                version    : 'v2.0' // use version 2.0
            });
        };

        // Load the SDK asynchronously
        // This is updated as the old version went to all.js
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    <!-- End: Ajax Login Register Facebook script -->
<?php endif; ?>

<?php $styling = get_option('ajax_login_register_additional_styling');
if ( $styling ) : ?>
<!-- Start: Ajax Login Register Additional Styling -->
<style type="text/css">
    <?php echo wp_kses_stripslashes( $styling ); ?>
</style>
<!-- End: Ajax Login Register Additional Styling -->
<?php endif; ?>
