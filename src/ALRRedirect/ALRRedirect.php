<?php

/**
 * This class will contain ALL functionality for the "Allow By IP" section.
 * Including any CSS, JS files, settings, or additional templates, etc.
 */
Class ALRRedirect {

    public function __construct(){

        $this->prefix = 'alr_redirect';

        add_filter( 'quilt_' . ALR_NAMESPACE. '_settings', array( &$this, 'settings') );

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
                    'desc' => sprintf( '%s <code>%s</code>',
                        __('Enter the URL or slug you want users redirected to after login, example: ', ALR_TEXT_DOMAIN ),
                        __('http://site.com/, /dashboard/, /wp-admin/', ALR_TEXT_DOMAIN ) )
                )
            )
        );

        return array_merge( $current_settings, $settings );

    }

}
new ALRRedirect();