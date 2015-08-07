<?php

function alr_add_legacy_login_class( $classes ){
    $classes[] = 'noon';
    return $classes;
}
add_filter( 'alr_login_form_classes', 'alr_add_legacy_login_class' );