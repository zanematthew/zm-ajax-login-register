<?php

Class ALRLogin {

    public function __construct(){
        $this->prefix = 'alr_login';
        add_action( 'alr_init', array( &$this, 'init' ) );

    }

    public function init(){

        add_shortcode( 'ajax_login_two', array( &$this, 'shortcode' ) );

    }


    public function buildFormFieldsHtml( $fields=null, $prefix=null, $order=null ){

        // Use this filter to add additional classes
        $default_classes = apply_filters( $this->prefix . '_form_classes', array() );
        $html = null;

        foreach( $order as $key ){

            // Key specific filter?
            // $field = apply_filters( $this->prefix . '_filter_field_' . $key, $fields[ $key ] );

            if ( empty( $fields[ $key ] ) ){

                $html .= "invalid key {$key} added for order<br />";
                $html .= PHP_EOL;

            } else {

                do_action( $key . '_above_field' );

                $args = wp_parse_args( $fields[ $key ], array(
                    'extra' => null,
                    'required' => null,
                    'size' => null,
                    'name' => $key,
                    'id' => $prefix . '_' . sanitize_title( $key ),
                    'class' => null,
                    'placeholder' => esc_attr( $fields[ $key ]['title'] ),
                    'type' => esc_attr( $fields[ $key ]['type'] ),
                    'html' => null
                    ) );

                $classes = array_merge( $default_classes, array(
                    ALR_NAMESPACE . '_' . $args['type'],
                    $prefix . '_' . $args['type'],
                    $args['class']
                    ) );

                $html .= '<div class="' . implode( " ", $classes ) . '">';

                switch ( $fields[ $key ]['type'] ) {

                    case 'text':
                        $html .= '<label for="' . $args['id'] . '">' . $args['title'] . '</label>';
                        $html .= '<input type="text" name="' . $args['name'] . '" id="' . $args['id'] . '" class="" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'password':
                        $html .= '<label for="' . $args['id'] . '">' . $args['title'] . '</label>';
                        $html .= '<input type="password" name="' . $args['name'] . '" id="' . $args['id'] . '" class="" placeholder="' . $args['placeholder'] . '" ' . $args['extra'] . ' />';
                        $html .= PHP_EOL;
                        break;

                    case 'checkbox':
                        $html .= '<input type="checkbox" name="' . $args['name'] . '" id="' . $args['id'] . '" class="" ' . $args['extra'] . ' />';
                        $html .= '<label for="' . $args['id'] . '">' . $args['title'] . '</label>';
                        $html .= PHP_EOL;
                        break;

                    case 'html':
                        $html .= $args['html'];
                        $html .= PHP_EOL;
                        break;

                    default:
                        $html .= 'no default';
                        $html .= PHP_EOL;
                        break;
                }

                $html .= '</div>';

                do_action( $key . '_below_field' );

            }

        }
        return $html;
    }


    public function buildFormHtmlLinks( $links=null, $prefix=null ){

        if ( $links ){

            $html = null;
            foreach( $links as $key => $value ){
                 $args = wp_parse_args( $value, array(
                    'href' => '#',
                    'class' => 'foo',
                    'title' => esc_attr( $value['text'] ),
                    'text' => esc_attr( $value['text'] ),
                    'id' => $prefix . '_' . sanitize_title( $value['text'] )
                    ) );

                $classes = array(
                    'noon',
                    ALR_NAMESPACE . '_link',
                    $this->prefix . '_link',
                    $args['class']
                    );

                $html .= '<li><a href="'.$args['href'].'" class="' . implode( " ", $classes ) . '" id="'.$args['id'].'" title="'.$args['title'].'">'.$args['text'].'</a></li>';
                $html .= PHP_EOL;
            }

        } else {

            $html = null;

        }

        return $html;
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
        $fields_html = $this->buildFormFieldsHtml( $fields, $this->prefix, $order );

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

        $links_html = $this->buildFormHtmlLinks( $links, $this->prefix );

        ob_start(); ?>
        <!-- Login Form -->
        <div class="ajax-login-register-login-container">
            <?php if ( is_user_logged_in() ) : ?>
                <p><?php printf("%s <a href=%s title='%s'>%s</a>", __('You are already logged in','ajax_login_register'), wp_logout_url( site_url() ), __('Logout','ajax_login_register'), __('Logout','ajax_login_register') );?></p>
            <?php else : ?>
                <form action="javascript://" class="ajax-login-default-form-container login_form <?php print get_option('ajax_login_register_default_style'); ?>" data-alr_login_security="<?php echo wp_create_nonce( 'login_submit' ); ?>">

                    <?php if ( get_option('ajax_login_register_facebook') && get_option('users_can_register') ) : ?>
                        <div class="fb-login-container">
                            <a href="#" class="fb-login" data-alr_facebook_security="<?php echo wp_create_nonce( 'facebook-nonce' ); ?>"><?php _e( 'Log in using Facebook', 'ajax_login_register' ); ?></a>
                        </div>
                    <?php endif; ?>

                    <div class="form-wrapper">

                        <div class="ajax-login-register-status-container">
                            <div class="ajax-login-register-msg-target"></div>
                        </div>

                        <?php echo $fields_html; ?>
                        <?php echo $links_html; ?>

                        <div class="button-container">
                            <input class="login_button green" type="submit" value="<?php _e('Login','ajax_login_register'); ?>" accesskey="p" name="submit" />
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
    new ALRLogin;
}
add_action( 'plugins_loaded', 'alr_plugins_loaded_login' );