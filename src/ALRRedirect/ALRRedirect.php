<?php

/**
 * This class will contain ALL functionality for the "Allow By IP" section.
 * Including any CSS, JS files, settings, or additional templates, etc.
 */
Class ALRRedirect {

    public function __construct(){

        $this->prefix = 'alr_redirect';

        add_filter( 'quilt_' . ALR_NAMESPACE . '_settings', array( &$this, 'settings') );
        add_filter( 'quilt_' . ALR_NAMESPACE . '_all_default_options', array( &$this, 'setDefaultRedirectUrl' ) );

    }


    /**
     * Filters the default settings, adding the additional settings below.
     *
     * @since 1.0.0
     */
    public function settings( $current_settings ){

        $settings[ $this->prefix ] = array(
            'title' => __('Redirect', ALR_TEXT_DOMAIN ),
            'fields' => array(
                array(
                    'id' => $this->prefix . '_redirect_after_login_url',
                    'title' => __( 'Redirect After Login URL', ALR_TEXT_DOMAIN ),
                    'type' => 'url',
                    'std' => '', // set to '' so it shows in settings as empty
                    'desc' => sprintf( '%s <code>%s</code>',
                        __('Enter the URL or slug you want users redirected to after login, example: ', ALR_TEXT_DOMAIN ),
                        __('http://site.com/, /dashboard/, /wp-admin/', ALR_TEXT_DOMAIN ) )
                )
            )
        );

        return array_merge( $current_settings, $settings );

    }


    // This is needed rather setting the 'std' => '', because we need to know the
    // visitors current URL.
    public function setDefaultRedirectUrl( $settings ){

        if ( empty( $settings['alr_redirect_redirect_after_login_url'] ) ){
            // c/o https://kovshenin.com/2012/current-url-in-wordpress/
            global $wp;
            $current_url = trailingslashit( add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
            $settings['alr_redirect_redirect_after_login_url'] = $current_url;
        }

        return $settings;

    }

}
new ALRRedirect();