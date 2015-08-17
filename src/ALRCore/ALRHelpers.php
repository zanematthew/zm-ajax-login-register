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


    /**
     * Handles creating a new user using native WordPress functions,
     * and signs the user on if successful.
     *
     * @since 2.0.0
     * @uses wp_parse_args
     * @uses apply_filters
     * @uses update_user_meta
     * @uses is_multisite
     * @uses wp_signon
     * @uses wp_new_user_notification
     *
     * @param $user (array) User array as seen
     *  in: http://codex.wordpress.org/Function_Reference/wp_insert_user
     *
     * @return $user_id (mixed) False on failure, user_id on success
     */
    public function createUser( $user=null, $prefix=null ){

        $user = wp_parse_args( $user, array(
            'role' => apply_filters( $prefix . '_default_role', get_option('default_role') ),
            'user_registered' => date('Y-m-d H:i:s'),
            'user_email' => $user['email']
            ) );

        $user_id = wp_insert_user( $user );

        if ( is_wp_error( $user_id ) ) {

            $user_id = false;

        } else {

            // update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
            if ( ! empty( $user['fb_id'] ) ){
                update_user_meta( $user_id, 'fb_id', $user['fb_id'] );
            }

            if ( is_multisite() ){
                $this->multisiteSetup( $user_id );
            }

            $wp_signon = wp_signon( array(
                'user_login' => $user['user_login'],
                'user_password' => $user['user_pass'],
                'remember' => true ),
            false );

            wp_new_user_notification( $user_id );

            do_action( $prefix . '_after_successfull_registration', $user_id );

        }

        return $user_id;

    }


    /**
     * Adds the user to the networked blog they are currently visiting
     *
     * @since 2.0.0
     * @param $user_id
     * @return true, wp_error object
     */
    public function multisiteSetup( $user_id=null, $prefix=null ){

        $added_to_blog = add_user_to_blog(
            get_current_blog_id(),
            $user_id,
            apply_filters( $prefix . '_default_role', get_option('default_role') )
        );

        return $added_to_blog;

    }

}