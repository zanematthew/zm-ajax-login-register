<?php

/**
 * This class will contain ALL functionality for the "Allow By IP" section.
 * Including any CSS, JS files, settings, or additional templates, etc.
 */
Class ALRMisc {

    public function __construct(){

        $this->prefix = 'alr_misc';

        add_filter( 'quilt_' . ALR_NAMESPACE. '_settings', array( &$this, 'settings') );

    }


    /**
     * Filters the default settings, adding the additional settings below.
     *
     * @since 1.0.0
     */
    public function settings( $current_settings ){

        $settings[ $this->prefix ] = array(
            'title' => __('Misc.', ALR_TEXT_DOMAIN ),
            'fields' => array(
                array(
                    'title' => __( 'Advanced Usage', ALR_TEXT_DOMAIN ),
                    'type' => 'header'
                ),
                array(
                    'id' => $this->prefix . '_login_handle',
                    'title' => __( 'Login Handle', ALR_TEXT_DOMAIN ),
                    'type' => 'fancyText',
                    'std' => '',
                    'desc' => __( 'Type the class name or ID of the element you want to launch the dialog box when clicked, example <code>.login-link</code>', ALR_TEXT_DOMAIN )
                ),
                array(
                    'id' => $this->prefix . '_register_handle',
                    'title' => __( 'Register Handle', ALR_TEXT_DOMAIN ),
                    'type' => 'fancyText',
                    'std' => '',
                    'desc' => __( 'Type the class name or ID of the element you want to launch the dialog box when clicked, example <code>.register-link</code>', ALR_TEXT_DOMAIN )
                ),
                array(
                    'id' => $this->prefix . '_force_check_password',
                    'title' => __( 'Force Check Password', ALR_TEXT_DOMAIN ),
                    'type' => 'checkbox',
                    'std' => 'off',
                    'desc' => __( 'Use this option if your are experiencing compatibility issues with other login and or register plugins.', ALR_TEXT_DOMAIN )

                ),
                array(
                    'id' => $this->prefix . '_pre_load_forms',
                    'title' => __( 'Pre-load Forms', ALR_TEXT_DOMAIN ),
                    'type' => 'checkbox',
                    'std' => 'off',
                    'desc' => __( 'Setting this option will pre-load the forms, allowing them to be loaded prior to being clicked on.', ALR_TEXT_DOMAIN )
                )
            )
        );

        return array_merge( $current_settings, $settings );

    }

}
new ALRMisc();