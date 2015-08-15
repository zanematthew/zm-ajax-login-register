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

        // Use the below filter to add additional social settings
        $settings[ $this->prefix ] = array(
            'title' => __('Social', ALR_TEXT_DOMAIN ),
            'fields' => apply_filters( $this->prefix . '_settings_fields_tab', array() )
        );

        return array_merge( $current_settings, $settings );

    }

}
new ALRSocial();