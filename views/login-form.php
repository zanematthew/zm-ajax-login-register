<?php

/**
 * This is the template for our login form. It should contain as less logic as possible
 */

?>
<!-- Login Form -->
<div class="ajax-login-register-login-container">
    <?php if ( is_user_logged_in() ) : ?>
        <p><?php printf("%s <a href=%s title='Logout'>%s</a>", __('You are already logged in','ajax_login_register'), wp_logout_url( site_url() ), __('Logout','ajax_login_register') );?></p>
    <?php else : ?>
        <form action="javascript://" id="login_form" class="ajax-login-default-form-container <?php print get_option('ajax_login_register_default_style'); ?>">
            <?php if ( get_option('ajax_login_register_facebook') && get_option('users_can_register') ) : ?>
                <div class="fb-login-container">
                    <a href="#" class="fb-login"><img src="<?php print plugin_dir_url( dirname( __FILE__ ) ); ?>assets/images/fb-login-button.png" /></a>
                </div>
            <?php endif; ?>
            <div class="form-wrapper">
                <?php wp_nonce_field( 'login_submit', 'security' ); ?>
                <div class="noon"><label><?php _e('User Name','ajax_login_register'); ?></label><input type="text" name="user_login" size="30" required /></div>
                <div class="noon"><label><?php _e('Password','ajax_login_register'); ?></label><input type="password" name="password" id="password" size="30" required /></div>
                <?php
                $keep_logged_in = get_option('ajax_login_register_keep_me_logged_in');
                if ( $keep_logged_in != "on") : ?>
                    <input type="checkbox" name="remember" id="remember" />
                    <span class="meta"><?php _e('Keep me logged in','ajax_login_register'); ?>.</span>
                <?php endif; ?>
                <a href="<?php echo wp_lostpassword_url(); ?>" title="Lost Password"><?php _e('Lost Password','ajax_login_register'); ?></a>
                <div class="button-container">
                    <input id="login_button" class="green" type="submit" value="Login" accesskey="p" name="submit" />
                    <input class="text cancel" type="button" value="cancel" name="cancel" />
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
<!-- End Login Form -->