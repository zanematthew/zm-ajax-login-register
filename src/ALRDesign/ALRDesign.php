<?php

/**
 * Design object.
 * @since 2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

Class ALRDesign {


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

        $this->prefix = 'zm_alr_design';

        add_action( 'wp_head', array( &$this, 'header' ) );

        add_filter( 'quilt_' . ZM_ALR_NAMESPACE. '_settings', array( &$this, 'settings') );
        add_filter( 'zm_alr_login_form_links', array( &$this, 'filterLoginLinks' ) );
        add_filter( 'zm_alr_login_form_fields', array( &$this, 'filterLoginFields' ) );
        add_filter( 'zm_alr_login_form_container_classes', array( &$this, 'loginClasses' ) );
        add_filter( 'zm_alr_register_form_container_classes', array( &$this, 'registerClasses' ) );

    }


    /**
     * Adds the design settings as a tab.
     *
     * @since 2.0.0
     *
     * @param   $current_settings   The current settings
     * @return  Merged settings
     *
     */
    public function settings( $current_settings ){

        $settings[ $this->prefix ] = array(
            'title' => __('Design', ZM_ALR_TEXT_DOMAIN ),
            'fields' => apply_filters( $this->prefix . '_settings_fields_tab', array(
                array(
                    'id' => $this->prefix . '_form_layout',
                    'title' => __( 'Form Layout', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'fancySelect',
                    'std' => $this->prefix . '_default',
                    'desc' => __( 'Not applicable in widget areas.',  ZM_ALR_TEXT_DOMAIN ),
                    'options' => array(
                        $this->prefix . '_default' => __( 'Default', ZM_ALR_TEXT_DOMAIN ),
                        $this->prefix . '_wide' => __( 'Wide', ZM_ALR_TEXT_DOMAIN )
                        )
                ),
                array(
                    'id' => $this->prefix . '_additonal_styling',
                    'title' => __( 'Additional Styling', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'css',
                    'desc' => __( 'Type your custom CSS styles that are applied to the dialog boxes.', ZM_ALR_TEXT_DOMAIN )
                ),

                // Show/hide certain fields
                array(
                    'id' => $this->prefix . '_login_disable_keep_me_logged_in',
                    'title' => __( 'Disable "keep me logged in"', ZM_ALR_TEXT_DOMAIN ),
                    'desc' => __( 'Use this option to disable the check box shown to keep users logged in.', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'checkbox'
                ),
                array(
                    'id' => $this->prefix . '_login_disable_register',
                    'title' => __( 'Remove Registration', ZM_ALR_TEXT_DOMAIN ),
                    'desc' => __( 'This prevents users from being able to register on the login page.', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'checkbox'
                ),
                array(
                    'id' => $this->prefix . '_login_disable_forgot_password',
                    'title' => __( 'Remove Forgot Password', ZM_ALR_TEXT_DOMAIN ),
                    'desc' => __( 'This prevents users from being able to use the "forgot password".', ZM_ALR_TEXT_DOMAIN ),
                    'type' => 'checkbox'
                ) )
            )
        );

        return array_merge( $current_settings, $settings );

    }


    /**
     * Filter to show or remove the Register link, and forgot password link.
     *
     * @since 2.0.0
     *
     * @param   $links  The links to filter
     * @return  $links  The links with either the removal of login, or forgot password
     */
    public function filterLoginLinks( $links ){

        global $zm_alr_settings;

        if ( ! empty( $zm_alr_settings[ $this->prefix . '_login_disable_register'] ) )
            unset( $links['zm_alr_login_not_a_member'] );

        if ( ! empty( $zm_alr_settings[ $this->prefix . '_login_disable_forgot_password'] ) )
            unset( $links['zm_alr_login_lost_password_url'] );

        return $links;

    }


    /**
     * Filter used to show or remove the "keep me logged in" checkbox
     *
     * @since 2.0.0
     *
     * @param   $fields     An array containing all form fields
     * @return  $fields     An array either removing the field
     */
    public function filterLoginFields( $fields ){

        global $zm_alr_settings;

        if ( ! empty( $zm_alr_settings[ $this->prefix . '_login_disable_keep_me_logged_in'] ) )
            unset( $fields['zm_alr_login_keep_me_logged_in'] );

        return $fields;

    }


    /**
     * Action to add the additional styling if present.
     *
     * @since   2.0.0
     * @return  Prints out the CSS in the HTML head if present
     */
    public function header(){

        global $zm_alr_settings;

        ?>

        <?php if ( ! empty( $zm_alr_settings[ $this->prefix . '_additonal_styling'] ) ) : ?>
            <!-- Start: ALR Additional Styling -->
            <style type="text/css">
                <?php echo $zm_alr_settings[ $this->prefix . '_additonal_styling']; ?>
            </style>
            <!-- End: ALR Additional Styling -->
        <?php endif; ?>

    <?php }


    /**
     * Filter to add an additional HTML class representing the form layout
     * to the body tag for further styling.
     *
     * @since   2.0.0
     *
     * @param   $classes    The array of body classes from WordPress
     * @return  $classes    The array of classes with the additional class.
     */
    public function loginClasses( $classes ){

        global $zm_alr_settings;

        $classes[] = $zm_alr_settings[ $this->prefix . '_form_layout' ];

        return $classes;

    }


    /**
     * Filter to add an additional HTML class representing the form layout
     * to the body tag for further styling.
     *
     * @since   2.0.0
     *
     * @param   $classes    The array of body classes from WordPress
     * @return  $classes    The array of classes with the additional class.
     */
    public function registerClasses( $classes ){

        global $zm_alr_settings;

        $classes[] = $zm_alr_settings[ $this->prefix . '_form_layout' ];

        return $classes;

    }

}