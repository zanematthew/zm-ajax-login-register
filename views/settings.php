<?php

/**
 * @todo Remove get_option() and settings_fields( 'my-settings-group' ); with do_settings_sections( 'my-plugin' );
 */

$a = New ajax_login_register_Admin;
$settings = $a->get_settings();
$style = get_option( 'ajax_login_register_default_style' );

?>

<div class="wrap" id="ajax-login-register-settings-wrapper">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2><?php _e( 'AJAX Login &amp; Register Settings', 'ajax_login_register' );?></h2>
    <div class="main">
        <h3><?php _e('Usage', 'ajax_login_register'); ?></h3>
        <?php _e('<p>To create a login page using shortcode; add the following shortcode <code>ajax_login</code> for the login page or <code>ajax_register</code> for the registration page to any Page or Post.</p><p>To create a login or registration dialog box do the following; create a menu item, assign a custom class name, then add that custom class name to the field: <em>Login Handle</em> for login or <em>Register Handle</em> for the registration page found in the settings below.</p><p><em>Note your theme must support custom menus</em></p><p><em>Note your site will need to be open to registration</em></p>', 'ajax_login_register'); ?>
        <form action="options.php" method="post" class="form newsletter-settings-form">

            <h3><?php _e( 'General Settings', 'ajax_login_register' ); ?></h3>
            <table class="form-table">
                <?php foreach( $settings['general'] as $setting ) : ?>
                    <tr valign="top">
                        <th scope="row"><?php print $setting['label']; ?></th>
                        <td>
                            <input type="checkbox" name="<?php print $setting['key']; ?>" id="<?php print $setting['key']; ?>" <?php checked( get_option( $setting['key'], "off" ), "on" ); ?> />
                            <label for="<?php print $setting['key']; ?>"><?php echo $setting['description']; ?></label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3><?php _e( 'Facebook Settings', 'ajax_login_register' ); ?></h3>
            <table class="form-table" id="facebook-settings">
            <?php _e('<p>Once you have created your Facebook App, go to your Facebook Developer App Settings and add your site URL(s) to the App Domains field. Then, copy your App ID and paste into your App ID field in the zM AJAX Login & Register Facebook settings.</p><p>In order to use Facebook login you will need to Create a Facebook App, by visiting <a href="https://developers.facebook.com/" target="_blank">Facebook Developers</a>. Once you have created your Facebook App you are now ready to enter your "site URL" as seen in Facebook Developer App Settings and your App ID.</p><p>For detailed instructions visit the <a href="http://zanematthew.com/ajax-login-register-help-videos/" target="_blank">How To add Facebook Settings to AJAX Login &amp; Register</a>, feel free to contact us via our <a href="http://support.zanematthew.com/forum/zm-ajax-login-register/" target="_blank">Support Forum</a> if you need additional help.</p>', 'ajax_login_register' ); ?>
                <?php foreach( $settings['facebook'] as $setting ) : ?>
                    <tr valign="top">
                        <th scope="row"><?php print $setting['label']; ?></th>
                        <td>
                            <?php echo $a->build_input( $setting['type'], $setting['key'] ); ?>
                            <p class="description"><?php echo $setting['description']; ?></p>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3><?php _e( 'Advanced Usage', 'ajax_login_register' ); ?></h3>
            <table class="form-table">
                <?php foreach( $settings['advanced_usage'] as $setting ) : ?>
                    <tr valign="top">
                        <th scope="row"><?php print $setting['label']; ?></th>
                            <td>
                            <?php if ( $setting['key'] == 'ajax_login_register_default_style' ) : ?>
                                <select name="ajax_login_register_default_style">
                                    <?php foreach( array('default','wide') as $option ) : ?>
                                        <option value="<?php print $option; ?>" <?php selected( $style, $option ); ?>><?php print ucfirst( $option );?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else : ?>
                                <?php echo $a->build_input( $setting['type'], $setting['key'] ); ?>
                                <p class="description"><?php echo $setting['description']; ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php settings_fields('ajax_login_register'); ?>
            <?php do_action('ajax_login_register_below_settings'); ?>

            <?php submit_button(); ?>
        </form>
    </div>

    <div class="sidebar">
        <a href="<?php echo $a->campaign_banner_link; ?>" title="<?php _e('Upgrade to the Pro version','ajax_login_register' ); ?>"><img src="<?php echo dirname( plugin_dir_url( __FILE__ ) ); ?>/assets/images/rectangular-banner-240x400.png" /></a>
        <p><?php _e('Remove these ads?','ajax_login_register'); ?><br />
        <a href="<?php echo $a->campaign_text_link; ?>" title="<?php _e('Remove this ad, upgrade to Pro','ajax_login_register'); ?>"><?php _e('Upgrade to Login &amp; Register Pro', 'ajax_login_register' ); ?> &raquo;</a></p>
    </div>
</div>