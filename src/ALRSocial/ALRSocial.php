<?php

/**
 * This class will contain ALL functionality for the "Allow By IP" section.
 * Including any CSS, JS files, settings, or additional templates, etc.
 */
Class ALRSocial {

    public function __construct(){

        $this->prefix = 'alr_social';

        add_filter( 'quilt_' . ALR_NAMESPACE. '_settings', array( &$this, 'settings') );

    }


    /**
     * Filters the default settings, adding the additional settings below.
     *
     * @since 1.0.0
     */
    public function settings( $current_settings ){

        $settings[ $this->prefix ] = array(
            'title' => __('Social', ALR_TEXT_DOMAIN ),
            'fields' => array(

                // Facebook
                array(
                    'title' => __( 'Facebook Settings', ALR_TEXT_DOMAIN ),
                    'type' => 'header'
                    ),
                array(
                    'id' => $this->prefix . '_fb_enabled',
                    'type' => 'checkbox',
                    'title' => __( 'Enable', ALR_TEXT_DOMAIN ),
                    'std' => 'off',
                    'desc' => __( 'By enabling this setting visitors will be able to login with Facebook.', ALR_TEXT_DOMAIN )
                ),
                array(
                    'id' => $this->prefix . '_fb_url',
                    'type' => 'url',
                    'title' => __( 'URL', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'This is the URL you have set in your Facebook Developer App Settings', ALR_TEXT_DOMAIN )
                ),
                array(
                    'id' => $this->prefix . '_fb_app_id',
                    'type' => 'fancyText',
                    'title' => __( 'App ID', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'This is the App ID as seen in your <a href="https://developers.facebook.com/">Facebook Developer</a> App Dashboard. For detailed instructions visit the <a href="http://zanematthew.com/ajax-login-register-help-videos/" target="_blank">How To add Facebook Settings to AJAX Login & Register</a>.', ALR_TEXT_DOMAIN )

                ),
                array(
                    'id' => $this->prefix . '_fb_use_avatar',
                    'type' => 'checkbox',
                    'title' => __( 'Use Facebook Avatar', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'Checking this box will make Facebook profile picture show as avatar when possible ', ALR_TEXT_DOMAIN )
                )
            )
        );

        return array_merge( $current_settings, $settings );

    }

}
new ALRSocial();