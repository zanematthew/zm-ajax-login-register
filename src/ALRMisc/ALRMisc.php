<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class ALRMisc {

    /**
     * The prefix used for meta keys, CSS classes, html IDs, etc.
     *
     * @since 2.0.0
     */
    public $prefix;


    /**
     * Adding of all hooks
     *
     * @since 2.0.0
     */
    public function __construct(){

        $this->prefix = 'zm_alr_misc';

        add_filter( 'quilt_' . ZM_ALR_NAMESPACE. '_settings', array( &$this, 'settings') );

    }


    /**
     * Adds the Misc. settings as a tab.
     *
     * @since 2.0.0
     *
     * @param   $current_settings   The current settings
     * @return  Merged settings
     *
     */
    public function settings( $current_settings ){

        $settings[ $this->prefix ] = array(
            'title' => __('Misc.', ZM_ALR_TEXT_DOMAIN ),
            'fields' => apply_filters( $this->prefix . '_settings_fields_tab', array(
                array(
                    'id' => $this->prefix . '_login_handle',
                    'title' => __( 'Login Handle', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'fancyText',
                    'std' => '',
                    'desc' => __( 'Type the class name or ID of the element you want to launch the dialog box when clicked, example <code>.login-link</code>', ZM_ALR_TEXT_DOMAIN )
                ),
                array(
                    'id' => $this->prefix . '_register_handle',
                    'title' => __( 'Register Handle', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'fancyText',
                    'std' => '',
                    'desc' => __( 'Type the class name or ID of the element you want to launch the dialog box when clicked, example <code>.register-link</code>', ZM_ALR_TEXT_DOMAIN )
                ),
                array(
                    'id' => $this->prefix . '_force_check_password',
                    'title' => __( 'Force Check Password', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'fancySelect',
                    'std' => $this->prefix . '_no',
                    'desc' => __( 'Use this option if your are experiencing compatibility issues with other login and or register plugins.', ZM_ALR_TEXT_DOMAIN ),
                    'options' => array(
                        $this->prefix . '_force_check_password_yes' => 'Yes',
                        $this->prefix . '_force_check_password_no' => 'No'
                        )

                ),
                array(
                    'id' => $this->prefix . '_pre_load_forms',
                    'title' => __( 'Pre-load Forms', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'fancySelect',
                    'std' => $this->prefix . '_pre_load_no',
                    'options' => array(
                        $this->prefix . '_pre_load_yes' => 'Yes',
                        $this->prefix . '_pre_load_no' => 'No'
                        ),
                    'desc' => __( 'Setting this option will pre-load the forms, allowing them to be loaded prior to being clicked on.', ZM_ALR_TEXT_DOMAIN )
                )
            )
        ) );

        return array_merge( $current_settings, $settings );

    }

}