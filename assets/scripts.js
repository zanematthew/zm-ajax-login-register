var $document = jQuery( document );

var zMAjaxLoginRegister = {

    reload: function( redirect ){

        var redirect;

        if ( redirect )
            location.href = redirect;

    },

    // Confirm passwords match
    confirm_password: function( my_obj ){

        var $obj = jQuery( my_obj );
        var value = $obj.val().trim();

        if ( !value.length ) return;

        var $form = $obj.parents('form');
        var match_value = jQuery('.user_password', $form).val();

        if ( value == match_value ) {
            msg = {
                "cssClass": "noon",
                "description": null,
                "code": "success"
            };
        } else {
            msg = {
                "cssClass": "error-container",
                "description": _zm_alr_settings.match_error,
                "code": "show_notice"
            };
        }

        return msg;
    },

    open_login: function(){

        jQuery('#ajax-login-register-login-dialog').dialog('open');

    },

    //
    load_login: function(){

        if ( jQuery('body.logged-in').length ){

            jQuery( "#ajax-login-register-login-target" )
                .stop()
                .fadeIn()
                .html( _zm_alr_settings.logged_in_text );

        } else {
            var data = {
                action: 'load_template',
                referer: 'login_form',
                template: 'login-form',
                security: jQuery('#ajax-login-register-login-dialog').attr('data-security')
            };

            jQuery.ajax({
                global: false,
                type: "POST",
                url: _zm_alr_settings.ajaxurl,
                data: data,
                success: function( msg ){
                    jQuery( "#ajax-login-register-login-target" )
                        .stop()
                        .fadeIn()
                        .html( msg.data ); // Give a smooth fade in effect
                }
            });

        }
    },

    //
    open_register: function(){

        jQuery('#ajax-login-register-dialog').dialog('open');

    },

    //
    load_register: function(){

        if ( jQuery('body.logged-in').length ){

            jQuery( "#ajax-login-register-target" )
                .stop()
                .fadeIn()
                .html( _zm_alr_settings.registered_text ); // Give a smooth fade in effect

        } else {
            var data = {
                action: 'load_register_template',
                template: 'register-form',
                referer_register: 'register_form',
                security_register:  jQuery('#ajax-login-register-dialog').attr('data-security')
            };
            jQuery.ajax({
                global: false,
                data: data,
                type: "POST",
                url: _zm_alr_settings.ajaxurl,
                success: function( msg ){
                    jQuery( "#ajax-login-register-target" )
                        .stop()
                        .fadeIn()
                        .html( msg.data ); // Give a smooth fade in effect
                }
            });
        }
    },

    recaptcha_check_login: function( my_obj ){

        if ( typeof grecaptcha !== 'function' )
            return;

        var google_recaptcha = '',
            $obj = jQuery( my_obj ),
            $dialog_container = $obj.parents('#ajax-login-register-login-dialog');

        if ( $dialog_container.length ){
            response = grecaptcha.getResponse( zm_alr_pro_google_recaptcha_login_dialog );
        } else {
            response = grecaptcha.getResponse( zm_alr_pro_google_recaptcha_login );
        }

        if ( response ){
            google_recaptcha = "g-recaptcha-response=" + response;
        }

        return google_recaptcha;
    },

    recaptcha_check_register: function( my_obj ){

        if ( typeof grecaptcha !== 'function' )
            return;

        var $obj = jQuery( my_obj ),
            $dialog_container = $obj.parents('#ajax-login-register-dialog'),
            google_recaptcha = '';

        if ( $dialog_container.length ){
            response = grecaptcha.getResponse( zm_alr_pro_google_recaptcha_register_dialog );
        } else {
            response = grecaptcha.getResponse( zm_alr_pro_google_recaptcha_register );
        }

        if ( response ){
            google_recaptcha = "g-recaptcha-response=" + response;
        }

        return google_recaptcha;
    }
};


$document.ready(function( $ ){

    window.ajax_login_register_show_message = function( form_obj, msg ) {
        if ( msg.code === 'success_login' || msg.code === 'success_registration' ){
            jQuery('.ajax-login-register-msg-target', form_obj)
                .addClass( msg.cssClass )
                .stop()
                .fadeIn()
                .html( msg.description );
        } else {
            if ( msg.code === 'show_notice' ){
                jQuery('.ajax-login-register-status-container').show();
            } else {
                jQuery('.ajax-login-register-status-container').hide();
            }

            jQuery('.ajax-login-register-msg-target', form_obj)
                .addClass( msg.cssClass )
                .stop()
                .fadeIn()
                .html( msg.description );
        }
    };


    /**
     * Server side email validation.
     */
    window.ajax_login_register_validate_email = function( myObj ){

        var $this = myObj;
        var thisVal = $.trim( $this.val() );

        if ( !thisVal.length ) return;

        $form = $this.parents('form');

        $.ajax({
            global: false,
            data: {
                action: 'validate_email',
                zm_alr_register_email: thisVal
            },
            dataType: 'json',
            type: "POST",
            url: _zm_alr_settings.ajaxurl,
            success: function( msg ){
                ajax_login_register_show_message( $form, msg );
            }
        });
    }


    /**
     * Validate email
     */
    $document.on('blur', '.ajax-login-register-validate-email', function(){
        ajax_login_register_validate_email( $(this) );
    });


    /**
     * Check that username is valid
     */
    $document.on('blur', '.user_login', function(){

        if ( !$.trim( $(this).val() ) ) return;

        $form = $(this).parents('form');

        $.ajax({
            global: false,
            data: {
                action: 'validate_username',
                zm_alr_register_user_name: $( this ).val()
            },
            dataType: 'json',
            type: "POST",
            url: _zm_alr_settings.ajaxurl,
            success: function( msg ){
                ajax_login_register_show_message( $form, msg );
            }
        });
    });

    /**
     * Set-up our default dialog box with the following
     * parameters.
     */
    $('.ajax-login-register-container').dialog({
        autoOpen: false,
        width: _zm_alr_settings.dialog_width,
        height: _zm_alr_settings.dialog_height,
        resizable: false,
        draggable: false,
        modal: true,
        closeText: _zm_alr_settings.close_text
    });


    $( '#ajax-login-register-dialog, #ajax-login-register-login-dialog' ).dialog( "option", "position", {

            my: _zm_alr_settings.dialog_position.my,
            at: _zm_alr_settings.dialog_position.at,
            of: _zm_alr_settings.dialog_position.of

        }
    );


    if ( _zm_alr_settings.pre_load_forms === 'zm_alr_misc_pre_load_yes' ){
        zMAjaxLoginRegister.load_login();
        zMAjaxLoginRegister.load_register();
    }

    $document.on( 'click', '.ui-widget-overlay', function(){

        $('.ajax-login-register-container').dialog('close');

    });
});