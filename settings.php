<?php

function alr_settings_page_title( $title, $namespace ){

    return 'AJAX Login & Register';

}
add_filter( 'quilt_' . ALR_NAMESPACE . '_page_title', 'alr_settings_page_title', 15, 2 );


function alr_settings_menu_title( $title, $namespace ){

    return 'AJAX Login & Register 2.0';

}
add_filter( 'quilt_' . ALR_NAMESPACE . '_menu_title', 'alr_settings_menu_title', 15, 2 );


function alr_settings_footer_content( $content ){
    $settings_campaign_url = 'http://store.zanematthew.com/downloads/tag/client-access-add-ons/?utm_source=WordPress&utm_medium=Settings%20Footer&utm_campaign=Client%20Access%20Add-ons';

    return sprintf( '%s | v%s | <a href="%s" target="_blank">%s</a> | <a href="%s" target="_blank">%s</a>',
        __( 'Thank you for using ZM AJAX Login & Register', ALR_NAMESPACE ),
        ALR_VERSION,
        esc_url( 'http://support.zanematthew.com/forum/zm-ajax-login-register/'),
        __( 'Support', ALR_NAMESPACE ),
        esc_url( $settings_campaign_url ),
        __( 'Add-ons', ALR_NAMESPACE )
        );

}
add_filter( 'quilt_' . ALR_NAMESPACE . '_footer', 'alr_settings_footer_content', 15, 2 );