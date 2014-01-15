<?php

/**
 * This is the template for our register form. It should contain as less logic as possible
 */

?>

<!-- Register Modal -->
<?php if ( get_option('users_can_register') ) : ?>
    <div class="ajax-login-register-register-container">
        <?php if ( is_user_logged_in() ) : ?>
            <p><?php printf('%s <a href="%s" title="Logout">%s</a>.', __('You are already registered',''), wp_logout_url( site_url() ), __('Logout', 'ajax_login_register') ); ?></p>
        <?php else : ?>
            <form action="javascript://" id="register_form" name="registerform" class="ajax-login-default-form-container <?php print get_option('ajax_login_register_default_style'); ?>">

                <?php if ( get_option('ajax_login_register_facebook') ) : ?>
                    <div class="fb-login-container">
                        <a href="#" class="fb-login"><img src="<?php print plugin_dir_url( dirname( __FILE__ ) ); ?>assets/images/fb-login-button.png" /></a>
                    </div>
                <?php endif; ?>

                <div class="form-wrapper">
                    <?php wp_nonce_field( 'register_submit', 'security' ); ?>
                    <div class="ajax-login-register-status-container">
                        <div class="ajax-login-register-msg-target"></div>
                    </div>
                    <div class="noon"><label><?php _e('User Name', 'ajax_login_register'); ?></label><input type="text" name="login" id="user_login" class="" /></div>
                    <div class="noon"><label><?php _e('Email', 'ajax_login_register'); ?></label><input type="text" name="email" id="user_email" class="ajax-login-register-validate-email" /></div>
                    <div class="noon"><label><?php _e('Password', 'ajax_login_register'); ?></label><input type="password" name="password" id="user_password" class="" /></div>
                    <div class="noon"><label><?php _e('Confirm Password', 'ajax_login_register'); ?></label><input type="password" name="confirm_password" id="user_confirm_password" data-match_id="#user_password" data-register_button_id="#register_button_id" class="" /></div>
                    <div class="button-container" id="register_button_pane">
                        <input id="register_button_id" type="submit" value="<?php _e('Register','ajax_login_register'); ?>" accesskey="p" name="register" class="green" disabled />
                        <input type="button" value="Cancel" class="text cancel" id="ajax-login-register-close" />
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php else : ?>
    <p><?php _e('Registration is currently closed.',''); ?></p>
<?php endif; ?>
<!-- End 'modal' -->