<?php

Class ALRLogin {

    public function __construct( ZM_Dependency_Container $di ){

        $this->_alr_html = $di->get_instance( 'html', 'ALRHtml', null );
        $this->prefix = 'alr_login';
        add_action( 'alr_init', array( &$this, 'init' ) );

    }

    public function init(){

        add_shortcode( 'ajax_login_two', array( &$this, 'shortcode' ) );

    }


    public function shortcode(){

        // Use this to filter HTML, just target the specific array by key
        $fields = apply_filters( $this->prefix . '_fields', array(
            $this->prefix . '_user_name' => array(
                'title' => 'User Name',
                'type' => 'text',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_password' => array(
                'title' => 'Password',
                'type' => 'password',
                'extra' => 'autocorrect="none" autocapitalize="none"'
                ),
            $this->prefix . '_keep_me_logged_in' => array(
                'title' => 'Keep Me Logged In',
                'type' => 'checkbox'
                )
            ) );

        $order = apply_filters( $this->prefix . '_order_fields', array_keys( $fields ) );
        $fields_html = $this->_alr_html->buildFormFieldsHtml( $fields, $this->prefix, $order );

        $links = apply_filters( $this->prefix . '_form_links', array(
            $this->prefix . '_not_a_member' => array(
                'href' => '#',
                'class' => 'not-a-member-handle',
                'text' => apply_filters( 'ajax_login_not_a_member_text', __('Are you a member?','ajax_login_register') ),
                ),
            $this->prefix . '_lost_password_url' => array(
                'href' => wp_lostpassword_url(),
                'class' => '',
                'text' => __('Forgot Password','ajax_login_register')
                )
        ) );

        $links_html = $this->_alr_html->buildFormHtmlLinks( $links, $this->prefix );


        $container_classes = apply_filters( $this->prefix . '_form_container_classes', array(
            ALR_NAMESPACE . '_form_container',
            $this->prefix . '_form_container'
            ) );

        $form_classes = apply_filters( $this->prefix . '_form_classes', array(
            'ajax-login-default-form-container login_form',
            get_option('ajax_login_register_default_style')
            ) );

        global $alr_settings;
        ob_start(); ?>

        <!-- Login Form -->
        <div class="<?php echo implode( " ", $container_classes ); ?>">

            <?php if ( is_user_logged_in() ) : ?>

                <p class="<?php echo $this->prefix; ?>_text"><?php printf("%s <a href=%s title='%s'>%s</a>",
                    __('You are already logged in', ALR_TEXT_DOMAIN ), // Text
                    wp_logout_url( site_url() ), // URL
                    __('Logout', ALR_TEXT_DOMAIN ), // Link text
                    __('Logout', ALR_TEXT_DOMAIN ) // Link title text
                );?></p>

            <?php else : ?>

                <form action="javascript://" class="<?php echo implode( " ", $form_classes ); ?>" data-alr_login_security="<?php echo wp_create_nonce( 'login_submit' ); ?>">

                    <?php if ( $alr_settings['alr_social_fb_enabled']
                          && get_option('users_can_register') ) : ?>

                        <div class="fb-login-container">
                            <a href="#" class="fb-login" data-alr_facebook_security="<?php echo wp_create_nonce( 'facebook-nonce' ); ?>"><?php _e( 'Log in using Facebook', ALR_TEXT_DOMAIN ); ?></a>
                        </div>

                    <?php endif; ?>

                    <div class="form-wrapper">

                        <div class="ajax-login-register-status-container">
                            <div class="ajax-login-register-msg-target"></div>
                        </div>

                        <?php echo $fields_html; ?>
                        <?php echo $links_html; ?>

                        <div class="button-container">
                            <input class="login_button green" type="submit" value="<?php _e('Login',ALR_TEXT_DOMAIN); ?>" accesskey="p" name="submit" />
                        </div>
                    </div>

                </form>
            <?php endif; ?>
        </div>
        <!-- End Login Form -->

        <?php return ob_get_clean();
    }
}

function alr_plugins_loaded_login(){

    new ALRLogin( new ZM_Dependency_Container( null ) );

}
add_action( 'plugins_loaded', 'alr_plugins_loaded_login' );