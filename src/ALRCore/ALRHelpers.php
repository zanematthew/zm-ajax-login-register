<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


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
                'description' => __('Invalid username', ZM_ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'show_notice'
                ),
            'invalid_username' => array(
                'description' => __( 'Invalid username', ZM_ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'show_notice'
                ),
            'username_does_not_exists' => array(
                'description' => __( 'Invalid username', ZM_ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'show_notice'
                ),

            'incorrect_password' => array(
                'description' => __( 'Invalid', ZM_ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'show_notice'
                ),
            'passwords_do_not_match' => array(
                'description' => __('Passwords do not match.', ZM_ALR_TEXT_DOMAIN ),
                'cssClass' =>'error-container',
                'code' => 'show_notice'
                ),

            'email_valid' => array(
                'description' => null,
                'cssClass' => 'noon',
                'code' => 'success'
                ),
            'email_invalid' => array(
                'description' => __( 'Invalid Email', ZM_ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'show_notice'
                ),
            'email_in_use' => array(
                'description' => __( 'Invalid Email', ZM_ALR_TEXT_DOMAIN ),
                'cssClass' => 'error-container',
                'code' => 'show_notice'
                ),

            'success_login' => array(
                'description' => __( 'Success! One moment while we log you in...', ZM_ALR_TEXT_DOMAIN ),
                'cssClass' => 'success-container',
                'code' => 'success_login'
                ),
            'success_registration' => array(
                'description' => __( 'Success! One moment while we log you in...', ZM_ALR_TEXT_DOMAIN ),
                'cssClass' => 'noon success-container',
                'code' => 'success_registration'
                )
            );

        $status = apply_filters( 'zm_alr_status_codes', $status );

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

            $user_id = $user_id;

        } else {

            // update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
            if ( ! empty( $user['fb_id'] ) ){
                update_user_meta( $user_id, 'fb_id', $user['fb_id'] );
            }

            if ( is_multisite() ){
                $this->multisiteSetup( $user_id, $prefix );
            }

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


    /**
     * Searches a string of text for certain "tags", replaces the tags
     * with the given value.
     *
     * @since   1.0.0
     * @param   $string     The value to replace tags from
     * @param   $tags       The default tags used contained key => value
     * @return  $string     The new string with replaced tags
     */
    public function templateTags( $string=null, $default_tags=null ){

        $message = str_replace( array_keys( $default_tags ), $default_tags, nl2br( $string ) );
        $message = wp_kses_decode_entities( $message,
            array(
                'code' => array(),
                'br' => array(),
                'a' => array()
                )
            );

        return $message;
    }


    /**
     * A more reliable way to determine the IP address from the HTTP headers
     *
     * @since 1.2
     * @param void
     * @return IP address
     */
    public function getIp(){
        foreach( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key ){
            if ( array_key_exists( $key, $_SERVER ) === true ){
                foreach( explode( ',', $_SERVER[ $key ] ) as $ip ){
                    $ip = trim( $ip ); // just to be safe

                    if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false ){
                        $ip_address = $ip;
                    }
                }
            }
        }
    }


    /**
     * Determine the redirect URL for login, and registration.
     *
     * @since   2.0.2
     *
     * @param   (string)    $user_login     The user login
     * @param   (string)    $status         The status code to check against.
     * @param   (string)    $prefix         The prefix used for filters
     */
    public function getRedirectUrl( $user_login=null, $status=null, $prefix=null ){

        $success = array(
            'success_registration',
            'success_login'
            );

        if ( in_array( $status, $success )){

            $current_url = empty( $_SERVER['HTTP_REFERER'] ) ? site_url( $_SERVER['REQUEST_URI'] ) : $_SERVER['HTTP_REFERER'];

            $redirect_url = apply_filters( $prefix . '_redirect_url',
                $current_url,
                $user_login,
                $status
            );

        } else {

            $redirect_url = null;

        }

        return $redirect_url;
    }

}