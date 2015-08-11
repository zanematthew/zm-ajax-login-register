<?php

Class ALRHelpers {

    /**
     * Validation status responses
     */
    static function status( $key=null, $value=null ){

        $status = array(

            'valid_username' => array(
                'description' => null,
                'cssClass' => 'noon',
                'code' => 'success'
                ),
            'username_exists' => array(
                'description' => __('Invalid username', ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),
            'invalid_username' => array(
                'description' => __( 'Invalid username', ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),
            'username_does_not_exists' => array(
                'description' => __( 'Invalid username', ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),

            'incorrect_password' => array(
                'description' => __( 'Invalid', ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),
            'passwords_do_not_match' => array(
                'description' => __('Passwords do not match.', ALR_TEXT_DOMAIN ),
                'cssClass' =>'error-container',
                'code' => 'error'
                ),

            'email_valid' => array(
                'description' => null,
                'cssClass' => 'noon',
                'code' => 'success'
                ),
            'email_invalid' => array(
                'description' => __( 'Invalid Email', ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),
            'email_in_use' => array(
                'description' => __( 'Invalid Email', ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'error'
                ),

            'success_login' => array(
                'description' => __( 'Success! One moment while we log you in...', ALR_TEXT_DOMAIN ),
                'cssClass' => 'success-container',
                'code' => 'success_login'
                ),
            'success_registration' => array(
                'description' => __( 'Success! One moment while we log you in...', ALR_TEXT_DOMAIN ),
                'cssClass' => 'noon',
                'code' => 'success_registration'
                )
            );

        $status = apply_filters( 'ajax_login_register_status_codes', $status );

        if ( empty( $value ) ){
            return $status[ $key ];
        } else {
            return $status[ $key ][ $value ];
        }
    }

}