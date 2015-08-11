<?php

function alr_legacy_form_container_class( $classes ){

    $classes[] = 'ajax-login-register-login-container';
    return $classes;

}
add_filter( 'alr_login_form_container_classes', 'alr_legacy_form_container_class' );


// do_action( 'zm_ajax_login_register_below_email_field' );

// Legacy register email field classes
// user_email ajax-login-register-validate-email

// Add legacy classes to register form
function alr_register_field_filter( $field ){

    if ( $field['name'] == 'alr_register_user_name' ){
        $field['classes'][] = 'user_login';
    }

    if ( $field['name'] == 'alr_register_email' ){
        $field['classes'][] = 'ajax-login-register-validate-email';
        $field['classes'][] = 'user_email';
    }

    if ( $field['name'] == 'alr_register_password' ){
        $field['classes'][] = 'user_password';
    }

    if ( $field['name'] == 'alr_register_confirm_password' ){
        $field['classes'][] = 'user_confirm_password';
    }


    return $field;
}
add_filter( 'alr_register_fields_args', 'alr_register_field_filter' );


// Add legacy classes to the login form fields
function alr_login_fields_filter( $classes ){

    $classes[] = 'noon';

    return $classes;

}
add_filter( 'alr_login_field_container_classes', 'alr_login_fields_filter' );


// Sample on how to change field order
function alr_regiser_form_field_order( $order ){

    $order = array(
        'alr_register_email',
        'alr_register_password',
        'alr_register_user_name',
        'alr_register_confirm_password'
        );

    return $order;
}
// add_filter( 'alr_register_order_fields', 'alr_regiser_form_field_order' );


// Just call the legacy filter.
function alr_register_link_args_filter( $link ){

    if ( $link['name'] == 'alr_register_not_a_member' ){
        $link['text'] = apply_filters( 'ajax_login_register_already_registered_text', __( 'Already registered?', ALR_TEXT_DOMAIN ) );
        $link['title'] = apply_filters( 'ajax_login_register_already_registered_text', __( 'Already registered?', ALR_TEXT_DOMAIN ) );
    }

    return $link;

}
add_filter( 'alr_register_link_args', 'alr_register_link_args_filter' );


// Just call the legacy filter.
function alr_login_links_args_filter( $link ){
    if ( $link['name'] == 'alr_login_not_a_member' ){
        $link['text'] = apply_filters( 'ajax_login_not_a_member_text', __( 'Are you a member?', ALR_TEXT_DOMAIN ) );
        $link['title'] = apply_filters( 'ajax_login_not_a_member_text', __( 'Are you a member?', ALR_TEXT_DOMAIN ) );
    }
}
add_filter( 'alr_login_links_args', 'alr_login_links_args_filter' );