<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

Class ALRUpgrade {

    /**
     * The previous version meta key name
     *
     * @since   2.0.0
     */
    public $previous_version_key;


    /**
     * The legacy version meta key name
     *
     * @since   2.0.0
     */
    public $legacy_version_key;


    /**
     * The array containing the old key, and new key
     *
     * @since   2.0.0
     */
    public $previous_setting_mapped_keys;


    /**
     *
     * @since   2.0.0
     */
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
            'fb_avatar'                                   => 'zm_alr_social_facebook_use_avatar'
        );

        if ( $this->needsUpgrade() === true ){
            add_action( 'quilt_zm_alr_above_form', array( &$this, 'upgradeNotice' ) );
            add_action( 'admin_notices', array( &$this, 'adminNoticeUpgrade' ) );
        }

    }


    /**
     * Determine if this version needs and upgrade
     *
     * @since   2.0.0
     *
     * @return  $upgrade (bool)
     */
    public function needsUpgrade(){

        // upgrade from Legacy to Quilt
        $did_update = get_option( 'zm_alr_did_update' );

        if ( $did_update == "1" ){
            $upgrade = true;
        }

        $previous_version = false;

        // Since the version is deleted when the plugin is deactivated this is the only
        // way to check if the user is upgrading from the legacy version to the new version
        foreach( $this->previous_setting_mapped_keys as $key => $value ){
            if ( get_option( $key ) == true ){
                $previous_version = '1.1.1';
                break;
            }
        }


        if ( $previous_version !== false
             && $did_update === false
             && $previous_version <= '1.1.1' ){
            $upgrade = true;
        } else {
            $upgrade = false;
        }

        return $upgrade;

    }


    /**
     * Given we have a valid nonce we:
     *      convert the legacy settings
     *      update the settings in the db
     *      delete the legacy settings
     *
     * @since   2.0.0
     */
    public function upgradeNotice(){

        if ( isset( $_GET['zm_alr_update_nonce'] ) && wp_verify_nonce( $_GET['zm_alr_update_nonce'], 'zm_alr_do_update') ) {

            $this->convertLegacySettingToQuilt();
            $this->deleteLegacySettings();

        }

    }


    /**
     * Convert the legacy settings to the new Quilt settings format. Handling any
     * unique cases along the way.
     *
     * @since   2.0.0
     */
    public function convertLegacySettingToQuilt(){

        foreach( $this->previous_setting_mapped_keys as $old => $new ){

            // Convert string path, i.e., /dashboard/ into page URL
            if ( $old == 'ajax_login_register_redirect' ){

                $page_obj = get_page_by_path( basename( untrailingslashit( parse_url( get_option( $old ), PHP_URL_PATH ) ) ) );

                if ( ! empty( $page_obj ) ){
                    $new_settings[ $new ] = $page_obj->ID;
                }

            }

            // Convert on/off to string for ...yes, ...no, i.e., field went from
            // checkbox to a series of select options
            elseif ( $old == 'ajax_login_register_pre_load_forms' ){

                $pre_load = get_option( $old );
                if ( $pre_load == 'on' ){
                    $pre = 'zm_alr_misc_pre_load_yes';
                } else {
                    $pre = 'zm_alr_misc_pre_load_no';
                }

                $new_settings[ $new ] = $pre;

            }

            // Convert on to 1
            elseif( $old == 'ajax_login_register_facebook' ){
                $fb = get_option( $old );
                if ( $fb == 'on' ){
                    $new_settings[ $new ] = 1;
                }
            }

            // Convert on to 1
            elseif( $old == 'ajax_login_register_keep_me_logged_in' ){
                $fb = get_option( $old );
                if ( $fb == 'on' ){
                    $new_settings[ $new ] = 1;
                }
            }

            // Convert on/off to string for ...yes, ...no, i.e., field went from
            // checkbox to a series of select options
            elseif ( $old == 'ajax_login_register_force_check_password' ){
                $force = get_option( $old );
                if ( $force == 'on' ){
                    $check = 'zm_alr_misc_force_check_password_yes';
                } else {
                    $check = 'zm_alr_misc_force_check_password_no';
                }
                $new_settings[ $new ] = $check;
            }

            // Convert default style to zm_alr_design_default, or zm_alr_design_wide
            elseif( $old == 'ajax_login_register_default_style' ){

                $style = get_option( $old );

                switch ( $style ){
                    case 'wide' :
                        $style = 'zm_alr_design_wide';
                        break;
                    case 'default' :
                        $style = 'zm_alr_design_default';
                        break;
                }

                $new_settings[ $new ] = $style;

            } else {

                $new_settings[ $new ] = get_option( $old );

            }
        }

        update_option( ZM_ALR_NAMESPACE, $new_settings );
        update_option( 'zm_alr_did_update', true );

    }


    /**
     * Delete all legacy settings.
     *
     * @since   2.0.0
     */
    public function deleteLegacySettings(){

        $keys = array_keys( $this->previous_setting_mapped_keys );

        foreach( $keys as $key ){
            delete_option( $key );
        }

    }


    /**
     * Text for admin notice.
     *
     * @since   2.0.0
     */
    public function adminNoticeUpgrade(){

        printf( '<div class="updated"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
            'Thank you for updating to the latest ZM ALR. Please finish the update by allowing ZM ALR to update your settings.',
            wp_nonce_url(admin_url('options-general.php?page='.ZM_ALR_NAMESPACE), 'zm_alr_do_update', 'zm_alr_update_nonce'),
            'Update now.'
            );

    }

}