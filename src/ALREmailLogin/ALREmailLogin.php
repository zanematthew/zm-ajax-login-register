<?php


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class ALREmailLogin {

    private $prefix;

    public function __construct(){

        $this->prefix = 'zm_alr_email_login';

        add_filter( 'zm_alr_misc_settings_fields_tab', array( &$this, 'settings' ) );
        add_filter( 'above_zm_alr_login_password', array( &$this, 'abovePassword' ) );
        add_filter( 'zm_alr_login_form_fields', array( &$this, 'removeLoginField' ) );
        add_filter( 'zm_alr_login_form_params', array( &$this, 'setUserName' ) );

    }


    /**
     * Adds the Redirect settings as a tab.
     *
     * @since 2.0.0
     *
     * @param   $current_settings
     * @return
     */
    public function settings( $current_fields ){

        $fields = array( array(
            'id' => $this->prefix . '_enable_email_login',
            'title' => __( 'Enable Email Login', ZM_ALR_TEXT_DOMAIN ),
            'type' => 'fancySelect',
            'options' => array(
                'yes' => 'Yes',
                'no' => 'No'
                ),
            'std' => 'no', // set to '' so it shows in settings as empty
            'desc' =>
                __('By enabling this setting users can login with their email. Note users MUST still <strong>register</strong> with a user name.',ZM_ALR_TEXT_DOMAIN )
        ) );

        return array_merge( $current_fields, $fields );

    }


    public function abovePassword( $field ){

        global $zm_alr_settings;

        if ( $zm_alr_settings[ $this->prefix . '_enable_email_login' ] == 'no' )
            return $field;

        return array_merge( array(
            $this->prefix . '_form_field' => array(
                'title' => __( 'Email', ZM_ALR_TEXT_DOMAIN ),
                'type' => 'email',
                'placeholder' => 'your@email.com'
                )
            ), $field );

    }


    public function removeLoginField( $fields ){

        global $zm_alr_settings;

        if ( $zm_alr_settings[ $this->prefix . '_enable_email_login' ] == 'yes' ){

            unset( $fields['zm_alr_login_user_name'] );

        }

        return $fields;

    }


    public function setUserName( $args ){

        global $zm_alr_settings;

        if ( $zm_alr_settings[ $this->prefix . '_enable_email_login' ] == 'yes' ){

            $user_obj = get_user_by( 'email', sanitize_email(
                $_POST[ $this->prefix . '_form_field']
            ) );

            if ( $user_obj ){
                $args['user_login'] = $user_obj->user_login;
            }

        }

        return $args;

    }

}