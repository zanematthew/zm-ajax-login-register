<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

Class ALRSocial {

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
     *
     * @param
     * @return
     */
    public function __construct(){

        $this->prefix = 'zm_alr_social';

        add_filter( 'quilt_' . ZM_ALR_NAMESPACE. '_settings', array( &$this, 'settings') );

    }


    /**
     * Filters the default settings, adding the additional settings below.
     * Currently this acts as a place holder and allows for additional social settings
     * via the filter "zm_alr_social_settings_fields_tab".
     *
     * @since 2.0.0
     */
    public function settings( $current_settings ){

        $settings[ $this->prefix ] = array(
            'title' => __('Social', ZM_ALR_TEXT_DOMAIN ),
            'fields' => apply_filters( $this->prefix . '_settings_fields_tab', array() )
        );

        return array_merge( $current_settings, $settings );

    }

}