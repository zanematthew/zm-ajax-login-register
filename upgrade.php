<?php

Class ALRUpgrade {

    public $previous_version_key;
    public $legacy_version_key;
    public $previous_setting_mapped_keys;

    public function __construct(){

        $this->previous_version_key = ZM_ALR_NAMESPACE . '_previous_version';
        $this->legacy_version_key = 'ajax_login_register_version';
        $this->previous_setting_mapped_keys = array(
            'ajax_login_register_additional_styling'      => 'zm_alr_design_additonal_styling',
            'ajax_login_register_advanced_usage_login'    => 'zm_alr_misc_login_handle',
            'ajax_login_register_advanced_usage_register' => 'zm_alr_misc_register_handle',
            'ajax_login_register_default_style'           => 'zm_alr_design_form_layout',
            'ajax_login_register_facebook'                => 'zm_alr_social_facebook_enabled',
            'ajax_login_register_force_check_password'    => 'zm_alr_misc_force_check_password',
            'ajax_login_register_keep_me_logged_in'       => 'zm_alr_design_login_disable_keep_me_logged_in',
            'ajax_login_register_pre_load_forms'          => 'zm_alr_pre_load_forms',
            'ajax_login_register_redirect'                => 'zm_alr_redirect_redirect_after_login_url',
            'app_id'                                      => 'zm_alr_social_facebook_app_id',
            'url'                                         => 'zm_alr_social_facebook_url'
        );

        add_action( 'quilt_zm_alr_above_form', array( &$this, 'zm_alr_upgrade_notice' ) );
        add_action( 'admin_notices', array( &$this, 'zm_alr_admin_notice_upgrade' ) );
    }


    public function needsUpgrade(){

        // upgrade from Legacy to Quilt
        $previous_version = get_option( $this->previous_version_key );
        $did_update = get_option( 'zm_alr_did_update' );

        if ( $previous_version !== false
             && $did_update === false
             && $previous_version < '1.1.1' ){
            $upgrade = true;
        } else {
            $upgrade = false;
        }

        return $upgrade;

    }


    // Handle updating the settings
    public function zm_alr_upgrade_notice(){

        if ( isset( $_GET['zm_alr_update_nonce'] ) && wp_verify_nonce( $_GET['zm_alr_update_nonce'], 'zm_alr_do_update')
            && $this->needsUpgrade() ) {

            $this->convertLegacySettingToQuilt();

        }

    }


    public function convertLegacySettingToQuilt(){

        foreach( $this->previous_setting_mapped_keys as $old => $new ){

            if ( $old == 'ajax_login_register_redirect' ){

                $page_obj = get_page_by_path( basename( untrailingslashit( parse_url( get_option( $old ), PHP_URL_PATH ) ) ) );

                if ( ! empty( $page_obj ) ){
                    $new_settings[ $new ] = $page_obj->ID;
                }

            } else {
                $new_settings[ $new ] = get_option( $old );
            }
        }

        update_option( ZM_ALR_NAMESPACE, $new_settings );
        update_option( 'zm_alr_did_update', true );

    }


    public function zm_alr_admin_notice_upgrade(){

        if ( $this->needsUpgrade() === false )
            return;

        // Update notice
        printf( '<div class="updated"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
            'Thank you for updating to the latest ZM ALR. Please finish the update by allowing ZM ALR to update your settings.',
            wp_nonce_url(admin_url('options-general.php?page='.ZM_ALR_NAMESPACE), 'zm_alr_do_update', 'zm_alr_update_nonce'),
            'Update now.'
            );

    }


}