<?php

/**
 * This class will contain ALL functionality for the "Allow By IP" section.
 * Including any CSS, JS files, settings, or additional templates, etc.
 */
Class ALRDesign {

    public function __construct(){

        $this->prefix = 'alr_design';

        add_filter( 'quilt_' . ALR_NAMESPACE. '_settings', array( &$this, 'settings') );

        add_filter( 'alr_login_form_links', array( &$this, 'filterLoginLinks' ) );
        add_filter( 'alr_login_fields', array( &$this, 'filterLoginFields' ) );

    }


    /**
     * Filters the default settings, adding the additional settings below.
     *
     * @since 1.0.0
     */
    public function settings( $current_settings ){

        $settings[ $this->prefix ] = array(
            'title' => __('Design', ALR_TEXT_DOMAIN ),
            'fields' => array(
                array(
                    'id' => $this->prefix . '_login_form_layout',
                    'title' => __( 'Form Layout', ALR_TEXT_DOMAIN ),
                    'type' => 'select',
                    'std' => 'default',
                    'options' => array(
                        'default' => __( 'Default', ALR_TEXT_DOMAIN ),
                        'wide' => __( 'Wide', ALR_TEXT_DOMAIN )
                        )
                ),
                array(
                    'id' => $this->prefix . '_login_additonal_styling',
                    'title' => __( 'Additional Styling', ALR_TEXT_DOMAIN ),
                    'type' => 'css',
                    'desc' => __( 'Type your custom CSS styles that are applied to the dialog boxes.', ALR_TEXT_DOMAIN )
                ),
                array(
                    'id' => $this->prefix . '_login_disable_keep_me_logged_in',
                    'title' => __( 'Disable "keep me logged in"', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'Use this option to disable the check box shown to keep users logged in.', ALR_TEXT_DOMAIN ),
                    'std' => 'off',
                    'type' => 'checkbox'
                ),

                // Show/hide certain fields
                array(
                    'id' => $this->prefix . '_login_disable_keep_me_logged_in',
                    'title' => __( 'Disable "keep me logged in"', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'Use this option to disable the check box shown to keep users logged in.', ALR_TEXT_DOMAIN ),
                    'std' => 'off',
                    'type' => 'checkbox'
                ),
                array(
                    'id' => $this->prefix . '_login_disable_register',
                    'title' => __( 'Remove Registration', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'This prevents users from being able to register on the login page.', ALR_TEXT_DOMAIN ),
                    'std' => 'off',
                    'type' => 'checkbox'
                ),
                array(
                    'id' => $this->prefix . '_login_disable_forgot_password',
                    'title' => __( 'Remove Forgot Password', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'This prevents users from being able to use the "forgot password".', ALR_TEXT_DOMAIN ),
                    'std' => 'off',
                    'type' => 'checkbox'
                )
            )
        );

        return array_merge( $current_settings, $settings );

    }


    public function filterLoginLinks( $links ){

        global $alr_settings;

        if ( $alr_settings[ $this->prefix . '_login_disable_register'] == 'on' )
            unset( $links['alr_login_not_a_member'] );

        if ( $alr_settings[ $this->prefix . '_login_disable_forgot_password'] == 'on' )
            unset( $links['alr_login_lost_password_url'] );

        return $links;

    }


    public function filterLoginFields( $fields ){

        global $alr_settings;

        if ( $alr_settings[ $this->prefix . '_login_disable_keep_me_logged_in'] == 'on' )
            unset( $fields['alr_login_keep_me_logged_in'] );

        return $fields;

    }
}
new ALRDesign();