<?php

function zm_alr_legacy_form_container_class( $classes ){

    $classes[] = 'ajax-login-register-login-container';
    return $classes;

}
add_filter( 'zm_alr_login_form_container_classes', 'zm_alr_legacy_form_container_class' );


// do_action( 'zm_ajax_login_register_below_email_field' );


// Add legacy classes to register form
function zm_alr_register_field_filter( $field ){

    if ( $field['name'] == 'zm_alr_register_user_name' ){
        $field['classes'][] = 'user_login';
    }

    if ( $field['name'] == 'zm_alr_register_email' ){
        $field['classes'][] = 'ajax-login-register-validate-email';
        $field['classes'][] = 'user_email';
    }

    if ( $field['name'] == 'zm_alr_register_password' ){
        $field['classes'][] = 'user_password';
    }

    if ( $field['name'] == 'zm_alr_register_confirm_password' ){
        $field['classes'][] = 'user_confirm_password';
    }

    if ( $field['name'] == 'zm_alr_register_submit_button' ){
        $field['classes'][] = 'register_button green';
    }


    return $field;
}
add_filter( 'zm_alr_register_fields_args', 'zm_alr_register_field_filter' );


function zm_alr_register_container_classes( $classes ){

    $classes[] = 'ajax-login-register-register-container';

    return $classes;

}
add_filter( 'zm_alr_register_form_container_classes', 'zm_alr_register_container_classes' );


function zm_alr_login_field_filter( $field ){

    if ( $field['name'] == 'zm_alr_login_submit_button' ){
        $field['classes'][] = 'login_button green';
    }

    return $field;

}
add_filter( 'zm_alr_login_fields_args', 'zm_alr_login_field_filter' );


function zm_alr_register_button_container_filter( $classes ){

    $classes[] = 'button-container';
    return $classes;

}
add_filter( 'zm_alr_register_button_container_classes', 'zm_alr_register_button_container_filter' );


// Add legacy classes to the login form fields
function zm_alr_login_fields_filter( $classes ){

    $classes[] = 'noon';

    return $classes;

}
add_filter( 'zm_alr_login_field_container_classes', 'zm_alr_login_fields_filter' );


// Sample on how to change field order
function zm_alr_regiser_form_field_order( $order ){

    $order = array(
        'zm_alr_register_email',
        'zm_alr_register_password',
        'zm_alr_register_user_name',
        'zm_alr_register_confirm_password'
        );

    return $order;
}
// add_filter( 'zm_alr_register_order_fields', 'zm_alr_regiser_form_field_order' );


// Just call the legacy filter.
function zm_alr_register_link_args_filter( $link ){

    if ( $link['name'] == 'zm_alr_register_not_a_member' ){
        $link['text'] = apply_filters( 'ajax_login_register_already_registered_text', __( 'Already registered?', ZM_ALR_TEXT_DOMAIN ) );
        $link['title'] = apply_filters( 'ajax_login_register_already_registered_text', __( 'Already registered?', ZM_ALR_TEXT_DOMAIN ) );
    }

    return $link;

}
add_filter( 'zm_alr_register_link_args', 'zm_alr_register_link_args_filter' );


// Just call the legacy filter.
function zm_alr_login_links_args_filter( $link ){
    if ( $link['name'] == 'zm_alr_login_not_a_member' ){
        $link['text'] = apply_filters( 'ajax_login_not_a_member_text', __( 'Are you a member?', ZM_ALR_TEXT_DOMAIN ) );
        $link['title'] = apply_filters( 'ajax_login_not_a_member_text', __( 'Are you a member?', ZM_ALR_TEXT_DOMAIN ) );
    }
}
add_filter( 'zm_alr_login_links_args', 'zm_alr_login_links_args_filter' );


function zm_alr_localized_js_filter( $localized ){

    // Apply the legacy filter
    $localized['redirect'] = apply_filters( 'zm_ajax_login_redirect', $localized['redirect'] );

        $width = array(
        'default' => 265,
        'wide' => 440,
        'extra_buttons' => 666,
        'mobile' => 300
        );

    $style = get_option('ajax_login_register_default_style');
    $fb_button = get_option('ajax_login_register_facebook');

    if ( $style == 'wide' && $fb_button ){
        $key = 'extra_buttons';
    } elseif( wp_is_mobile() ) {
        $key = 'mobile';
    } elseif ( $style == 'wide' ){
        $key = 'wide';
    } else {
        $key = 'default';
    }

    $localized['dialog_width'] = $width[ $key ];

    $localized = apply_filters( 'zm_ajax_login_register_localized_js', $localized );

    return $localized;
}
add_filter( 'zm_alr_localized_js', 'zm_alr_localized_js_filter' );


// Apply the legacy filter for the default role
function zm_alr_register_filter( $role ){

    $role = apply_filters( 'ajax_login_register_default_role', $role );

    return $role;

}
add_filter( 'zm_alr_register_default_role', 'zm_alr_register_filter' );


function zm_alr_register_after_registration( $user_id ){
    do_action( 'zm_ajax_login_after_successfull_registration', $user_id );
}
add_action( 'zm_alr_register_after_successfull_registration', 'zm_alr_register_after_registration' );


// Add legacy form classes
function zm_alr_login_form_container_classes( $classes ){

    $classes[] = 'ajax-login-default-form-container';
    $classes[] = 'login_form';

    return $classes;

}
add_filter( 'zm_alr_login_form_classes', 'zm_alr_login_form_container_classes' );



function zm_alr_register_form_container_classes_filter( $classes ){

    $classes[] = 'ajax-login-default-form-container register_form';

    return $classes;

}
add_filter( 'zm_alr_register_form_classes', 'zm_alr_register_form_container_classes_filter' );


function zm_alr_login_legacy_dialog_classes( $classes ){

    $classes[] = 'ajax-login-register-container';

    return $classes;

}
add_filter( 'zm_alr_login_dialog_class', 'zm_alr_login_legacy_dialog_classes' );


function zm_alr_register_legacy_dialog_classes( $classes ){

    $classes[] = 'ajax-login-register-container';

    return $classes;

}
add_filter( 'zm_alr_register_dialog_class', 'zm_alr_register_legacy_dialog_classes' );


function zm_alr_filter_status_codes( $status ){
    $status = apply_filters( 'ajax_login_register_status_codes', $status );
}
// add_filter( 'zm_alr_status_codes', 'zm_alr_filter_status_codes' );


// $redirect_url = apply_filters( 'ajax_login_register_login_redirect', $current_url, $user_login, $status )
// $redirect['redirect_url'] = apply_filters( 'ajax_login_register_register_redirect', $current_url, $user_login );