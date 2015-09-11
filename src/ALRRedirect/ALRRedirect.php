<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

Class ALRRedirect {


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

        $this->prefix = 'zm_alr_redirect';

        add_filter( 'quilt_' . ZM_ALR_NAMESPACE . '_settings', array( &$this, 'settings' ) );
        add_filter( 'quilt_' . ZM_ALR_NAMESPACE . '_all_default_options', array( &$this, 'setDefaultRedirectUrl' ) );
        add_filter( 'zm_alr_login_redirect_url', array( &$this, 'loginRedirectUrl' ), 15, 3 );
        add_filter( 'zm_alr_register_redirect_url', array( &$this, 'loginRedirectUrl' ), 15, 3 );
        add_filter( 'zm_alr_social_facebook_redirect_url', array( &$this, 'loginRedirectUrl' ), 15, 3 );

    }


    /**
     * Adds the Redirect settings as a tab.
     *
     * @since 2.0.0
     *
     * @param   $current_settings
     * @return
     */
    public function settings( $current_settings ){
        $pages = get_pages();
        if ( $pages ) {
            foreach ( $pages as $page ) {
                $pages_options[ $page->ID ] = $page->post_title;
            }
        }
        $settings[ $this->prefix ] = array(
            'title' => __('Redirect', ZM_ALR_TEXT_DOMAIN ),
            'fields' => apply_filters( $this->prefix . '_settings_fields_tab', array(
                array(
                    'id' => $this->prefix . '_redirect_after_login_url',
                    'title' => __( 'Redirect After Login URL', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'select',
                    'options' => $pages_options,
                    'std' => '', // set to '' so it shows in settings as empty
                    'desc' => sprintf( '%s <code>%s</code>',
                        __('Enter the URL or slug you want users redirected to after login, example: ', ZM_ALR_TEXT_DOMAIN ),
                        __('http://site.com/, /dashboard/, /wp-admin/', ZM_ALR_TEXT_DOMAIN ) )
                )
            )
        ) );

        return array_merge( $current_settings, $settings );

    }


    /**
     * Determine the default redirect.
     *
     * @since 2.0.0
     *
     * @param   $settings   The settings
     * @return  $settings   The settings with the default redirect
     */
    public function setDefaultRedirectUrl( $settings ){

        if ( empty( $settings[ $this->prefix . '_redirect_after_login_url'] ) ){
            // c/o https://kovshenin.com/2012/current-url-in-wordpress/
            global $wp;
            $current_url = trailingslashit( add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
            $settings[ $this->prefix . '_redirect_after_login_url'] = $current_url;
        }

        return $settings;

    }


    /**
     * Filter for the login URL.
     *
     * @since   2.0.0
     *
     * @param   $url         The current URL
     * @param   $user_login  The User login
     * @param   $status      The current status
     *
     * @return  $url         The URL the user is redirected to
     */
    public function loginRedirectUrl( $url, $user_login, $status ){

        global $zm_alr_settings;

        if ( empty( $zm_alr_settings[ $this->prefix . '_redirect_after_login_url'] ) ){
            $url = empty( $_SERVER['HTTP_REFERER'] ) ? site_url( $_SERVER['REQUEST_URI'] ) : $_SERVER['HTTP_REFERER'];
        } else {
            $url = get_permalink( $zm_alr_settings[ $this->prefix . '_redirect_after_login_url' ] );
        }

        return $url;

    }

}
