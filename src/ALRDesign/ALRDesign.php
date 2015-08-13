<?php

/**
 * This class will contain ALL functionality for the "Allow By IP" section.
 * Including any CSS, JS files, settings, or additional templates, etc.
 */
Class ALRDesign {

    public function __construct(){

        $this->prefix = 'alr_design';

        add_action( 'wp_head', array( &$this, 'header' ) );

        add_filter( 'quilt_' . ALR_NAMESPACE. '_settings', array( &$this, 'settings') );
        add_filter( 'alr_login_form_links', array( &$this, 'filterLoginLinks' ) );
        add_filter( 'alr_login_form_fields', array( &$this, 'filterLoginFields' ) );
        add_filter( 'alr_login_form_container_classes', array( &$this, 'loginClasses' ) );
        add_filter( 'alr_register_form_container_classes', array( &$this, 'registerClasses' ) );

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
                    'type' => 'fancySelect',
                    'std' => $this->prefix . '_default',
                    'options' => array(
                        $this->prefix . '_default' => __( 'Default', ALR_TEXT_DOMAIN ),
                        $this->prefix . '_wide' => __( 'Wide', ALR_TEXT_DOMAIN )
                        )
                ),
                array(
                    'id' => $this->prefix . '_login_additonal_styling',
                    'title' => __( 'Additional Styling', ALR_TEXT_DOMAIN ),
                    'type' => 'css',
                    'desc' => __( 'Type your custom CSS styles that are applied to the dialog boxes.', ALR_TEXT_DOMAIN )
                ),

                // Show/hide certain fields
                array(
                    'id' => $this->prefix . '_login_disable_keep_me_logged_in',
                    'title' => __( 'Disable "keep me logged in"', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'Use this option to disable the check box shown to keep users logged in.', ALR_TEXT_DOMAIN ),
                    'type' => 'checkbox'
                ),
                array(
                    'id' => $this->prefix . '_login_disable_register',
                    'title' => __( 'Remove Registration', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'This prevents users from being able to register on the login page.', ALR_TEXT_DOMAIN ),
                    'type' => 'checkbox'
                ),
                array(
                    'id' => $this->prefix . '_login_disable_forgot_password',
                    'title' => __( 'Remove Forgot Password', ALR_TEXT_DOMAIN ),
                    'desc' => __( 'This prevents users from being able to use the "forgot password".', ALR_TEXT_DOMAIN ),
                    'type' => 'checkbox'
                )
            )
        );

        return array_merge( $current_settings, $settings );

    }


    public function filterLoginLinks( $links ){

        global $alr_settings;

        if ( ! empty( $alr_settings[ $this->prefix . '_login_disable_register'] ) )
            unset( $links['alr_login_not_a_member'] );

        if ( ! empty( $alr_settings[ $this->prefix . '_login_disable_forgot_password'] ) )
            unset( $links['alr_login_lost_password_url'] );

        return $links;

    }


    public function filterLoginFields( $fields ){

        global $alr_settings;

        if ( ! empty( $alr_settings[ $this->prefix . '_login_disable_keep_me_logged_in'] ) )
            unset( $fields['alr_login_keep_me_logged_in'] );

        return $fields;

    }


    public function header(){

        global $alr_settings;

        ?>

        <?php if ( ! empty( $alr_settings[ $this->prefix . '_login_additonal_styling'] ) ) : ?>
            <!-- Start: ALR Additional Styling -->
            <style type="text/css">
                <?php echo $alr_settings[ $this->prefix . '_login_additonal_styling']; ?>
            </style>
            <!-- End: ALR Additional Styling -->
        <?php endif; ?>

    <?php }


    public function loginClasses( $classes ){

        global $alr_settings;

        $classes[] = $alr_settings[ $this->prefix . '_login_form_layout' ];

        return $classes;

    }


    public function registerClasses( $classes ){

        global $alr_settings;

        $classes[] = $alr_settings[ $this->prefix . '_login_form_layout' ];

        return $classes;

    }

}
new ALRDesign();