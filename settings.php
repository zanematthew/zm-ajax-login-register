<?php

function zm_alr_settings_page_title( $title, $namespace ){

    return 'AJAX Login & Register';

}
add_filter( 'quilt_zm_alr_page_title', 'zm_alr_settings_page_title', 15, 2 );


function zm_alr_settings_menu_title( $title, $namespace ){

    return 'AJAX Login & Register 2.0';

}
add_filter( 'quilt_zm_alr_menu_title', 'zm_alr_settings_menu_title', 15, 2 );


function zm_alr_settings_footer_content( $content ){
    $settings_campaign_url = 'http://store.zanematthew.com/downloads/tag/client-access-add-ons/?utm_source=WordPress&utm_medium=Settings%20Footer&utm_campaign=Client%20Access%20Add-ons';

    return sprintf( '%s | v%s | <a href="%s" target="_blank">%s</a> | <a href="%s" target="_blank">%s</a>',
        __( 'Thank you for using ZM AJAX Login & Register', ZM_ALR_NAMESPACE ),
        ZM_ALR_VERSION,
        esc_url( 'http://support.zanematthew.com/forum/zm-ajax-login-register/'),
        __( 'Support', ZM_ALR_NAMESPACE ),
        esc_url( $settings_campaign_url ),
        __( 'Add-ons', ZM_ALR_NAMESPACE )
        );

}
add_filter( 'quilt_zm_alr_footer', 'zm_alr_settings_footer_content', 15, 2 );