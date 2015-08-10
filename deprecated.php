<?php

function alr_add_legacy_login_class( $classes ){
    $classes[] = 'noon';
    return $classes;
}
add_filter( 'alr_login_form_classes', 'alr_add_legacy_login_class' );


function alr_legacy_form_container_class( $classes ){

    $classes[] = 'ajax-login-register-login-container';
    return $classes;

}
add_filter( 'alr_login_form_container_classes', 'alr_legacy_form_container_class' );