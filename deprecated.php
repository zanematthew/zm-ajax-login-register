<?php

function alr_legacy_form_container_class( $classes ){

    $classes[] = 'ajax-login-register-login-container';
    return $classes;

}
add_filter( 'alr_login_form_container_classes', 'alr_legacy_form_container_class' );


// do_action( 'zm_ajax_login_register_below_email_field' );


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

    if ( $field['name'] == 'alr_register_submit_button' ){
        $field['classes'][] = 'register_button green';
    }


    return $field;
}
add_filter( 'alr_register_fields_args', 'alr_register_field_filter' );


function alr_register_container_classes( $classes ){

    $classes[] = 'ajax-login-register-register-container';

    return $classes;

}
add_filter( 'alr_register_form_container_classes', 'alr_register_container_classes' );


function alr_login_field_filter( $field ){

    if ( $field['name'] == 'alr_login_submit_button' ){
        $field['classes'][] = 'login_button green';
    }

    return $field;

}
add_filter( 'alr_login_fields_args', 'alr_login_field_filter' );


function alr_login_button_container_filter( $classes ){

    $classes[] = 'button-container';
    return $classes;

}
add_filter( 'alr_login_button_container_classes', 'alr_login_button_container_filter' );


function alr_register_button_container_filter( $classes ){

    $classes[] = 'button-container';
    return $classes;

}
add_filter( 'alr_register_button_container_classes', 'alr_register_button_container_filter' );


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


function alr_localized_js_filter( $localized ){

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
add_filter( 'alr_localized_js', 'alr_localized_js_filter' );


// Apply the legacy filter for the default role
function alr_register_filter( $role ){

    $role = apply_filters( 'ajax_login_register_default_role', $role );

    return $role;

}
add_filter( 'alr_register_default_role', 'alr_regsiter_filter' );


function alr_register_after_registration(){
    do_action( 'zm_ajax_login_after_successfull_registration', $user_id );
}
add_action( 'alr_register_after_successfull_registration', 'alr_register_after_registration' );


// Add legacy form classes
function alr_login_form_container_classes( $classes ){

    $classes[] = 'ajax-login-default-form-container';
    $classes[] = 'login_form';

    return $classes;

}
add_filter( 'alr_login_form_classes', 'alr_login_form_container_classes' );



function alr_register_form_container_classes_filter( $classes ){

    $classes[] = 'ajax-login-default-form-container register_form';

    return $classes;

}
add_filter( 'alr_register_form_classes', 'alr_register_form_container_classes_filter' );


function alr_login_legacy_dialog_classes( $classes ){

    $classes[] = 'ajax-login-register-container';

    return $classes;

}
add_filter( 'alr_login_dialog_class', 'alr_login_legacy_dialog_classes' );


function alr_register_legacy_dialog_classes( $classes ){

    $classes[] = 'ajax-login-register-container';

    return $classes;

}
add_filter( 'alr_register_dialog_class', 'alr_register_legacy_dialog_classes' );
