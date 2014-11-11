var zMAjaxLoginRegister = {

    reload: function( my_obj ){
        if ( my_obj.hasClass('login_form') &&  typeof _ajax_login_settings.redirect.login !== 'undefined' ){
            location.href = _ajax_login_settings.redirect.login.url;
        } else if ( my_obj.hasClass('register_form') && typeof _ajax_login_settings.redirect.registration !== 'undefined' ){
            location.href = _ajax_login_settings.redirect.registration.url;
        } else {
            location.href = _ajax_login_settings.redirect;
        }
    },

    // Confirm passwords match
    confirm_password: function( my_obj ){

        $obj = jQuery( my_obj );
        value = $obj.val().trim();

        if ( value == '' ) return;

        $form = $obj.parents('form');

        match_value = jQuery('.user_password', $form).val();

        if ( value == match_value ) {
            msg = {
                "cssClass": "noon",
                "description": null,
                "code": "success"
            };
        } else {
            msg = {
                "cssClass": "error-container",
                "description": _ajax_login_settings.match_error,
                "code": "error"
            };
        }

        return msg;
    }
};


jQuery( document ).ready(function( $ ){

    window.zMAjaxLoginDialog = {
        open: function(){
            $('#ajax-login-register-login-dialog').dialog('open');

            $.ajax({
                type: "POST",
                url: _ajax_login_settings.ajaxurl,
                data: {
                    action: 'load_template',
                    referer: 'login_form',
                    template: 'login-form',
                    security: $('#ajax-login-register-login-dialog').attr('data-security')
                },
                success: function( msg ){
                    $( "#ajax-login-register-login-target" ).fadeIn().html( msg ); // Give a smooth fade in effect
                }
            });
        }
    };


    window.ajax_login_register_show_message = function( form_obj, msg ) {
        if ( msg.code == 'success_login' || msg.code == 'success_registration' ){
            jQuery('.ajax-login-register-msg-target', form_obj).addClass( msg.cssClass );
            jQuery('.ajax-login-register-msg-target', form_obj).fadeIn().html( msg.description );
            zMAjaxLoginRegister.reload( form_obj );
        } else if ( msg.description == '' ){
            zMAjaxLoginRegister.reload( form_obj );
        } else {
            if ( msg.cssClass == 'noon' ){
                jQuery('.ajax-login-register-status-container').hide();
            } else {
                jQuery('.ajax-login-register-status-container').show();
            }

            jQuery('.ajax-login-register-msg-target', form_obj).addClass( msg.cssClass );
            jQuery('.ajax-login-register-msg-target', form_obj).fadeIn().html( msg.description );
        }
    };


    /**
     * Server side email validation.
     */
    window.ajax_login_register_validate_email = function( myObj ){
        $this = myObj;

        if ( $.trim( $this.val() ) == '' ) return;

        $form = $this.parents('form');

        $.ajax({
            data: "action=validate_email&email=" + $this.val(),
            dataType: 'json',
            type: "POST",
            url: _ajax_login_settings.ajaxurl,
            success: function( msg ){
                ajax_login_register_show_message( $form, msg );
            }
        });
    }


    /**
     * Validate email
     */
    $( document ).on('blur', '.ajax-login-register-validate-email', function(){
        ajax_login_register_validate_email( $(this) );
    });


    /**
     * Check that username is valid
     */
    $( document ).on('blur', '.user_login', function(){

        if ( $.trim( $(this).val() ) == '' ) return;

        $form = $(this).parents('form');

        $.ajax({
            data: "action=validate_username&login=" + $( this ).val(),
            dataType: 'json',
            type: "POST",
            url: _ajax_login_settings.ajaxurl,
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
        width: _ajax_login_settings.dialog_width,
        resizable: false,
        draggable: false,
        modal: true,
        closeText: _ajax_login_settings.close_text
    });

    $( '#ajax-login-register-dialog, #ajax-login-register-login-dialog' ).dialog( "option", "position", {
        my: "center top",
        at: "center top+5%",
        of: 'body'
    });

});