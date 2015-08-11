<?php

Class ALRRegister {

    public function __construct( ZM_Dependency_Container $di ){

        $this->_alr_html = $di->get_instance( 'html', 'ALRHtml', null );
        $this->prefix = 'alr_register';
        add_action( 'alr_init', array( &$this, 'init' ) );

    }

    public function init(){

        add_shortcode( 'ajax_register_v2', array( &$this, 'shortcode' ) );

    }


    public function shortcode(){

        // No filter here, filter in the buildFormFieldsHtml instead
        $fields = array(
            $this->prefix . '_user_name' => array(
                'title' => 'User Name',
                'type' => 'text',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_email' => array(
                'title' => 'Email',
                'type' => 'email',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_password' => array(
                'title' => 'Password',
                'type' => 'password',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_confirm_password' => array(
                'title' => 'Confirm Passowrd',
                'type' => 'password',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                )
            );

        $fields_html = $this->_alr_html->buildFormFieldsHtml( $fields, $this->prefix );

        $links = array(
            $this->prefix . '_not_a_member' => array(
                'href' => '#',
                'class' => 'already-registered-handle',
                'text' => __( 'Already registered?', ALR_TEXT_DOMAIN ),
                )
            );

        $links_html = $this->_alr_html->buildFormHtmlLinks( $links, $this->prefix );

        ob_start(); ?>

        <!-- Register Modal -->
        <?php if ( get_option('users_can_register') ) : ?>
            <div class="ajax-login-register-register-container">
                <?php if ( is_user_logged_in() ) : ?>
                    <p><?php printf('%s <a href="%s" title="%s">%s</a>',
                        __( 'You are already registered', ALR_TEXT_DOMAIN ),
                        wp_logout_url( site_url() ),
                        __( 'Logout', ALR_TEXT_DOMAIN ),
                        __( 'Logout', ALR_TEXT_DOMAIN )
                    ); ?></p>
                <?php else : ?>
                    <form action="javascript://" name="registerform" class="ajax-login-default-form-container register_form <?php print get_option('ajax_login_register_default_style'); ?>" data-alr_register_security="<?php echo wp_create_nonce( 'setup_new_user' ); ?>">

                        <div class="form-wrapper">
                            <div class="ajax-login-register-status-container">
                                <div class="ajax-login-register-msg-target"></div>
                            </div>

                            <?php echo $fields_html; ?>
                            <?php echo $links_html; ?>

                            <div class="button-container">
                                <input class="register_button green" type="submit" value="<?php _e('Register', ALR_TEXT_DOMAIN ); ?>" accesskey="p" name="register" disabled />
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <p><?php _e('Registration is currently closed.', ALR_TEXT_DOMAIN ); ?></p>
        <?php endif; ?>

        <?php return ob_get_clean();
    }
}

function alr_plugins_loaded_register(){

    new ALRRegister( new ZM_Dependency_Container( null ) );

}
add_action( 'plugins_loaded', 'alr_plugins_loaded_register' );