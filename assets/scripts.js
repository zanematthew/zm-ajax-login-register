jQuery( document ).ready(function( $ ){


    var zMAjaxLoginRegister = {
        reload: function(){
            location.href = _ajax_login_settings.redirect;
        }
    };


    window.ajax_login_register_show_message = function( form_obj, msg ) {
        if ( msg.code == 'success_login' || msg.code == 'success_registration' ){
            jQuery('.ajax-login-register-msg-target', form_obj).addClass( msg.cssClass );
            jQuery('.ajax-login-register-msg-target', form_obj).fadeIn().html( msg.description );
            zMAjaxLoginRegister.reload();
        } else if ( msg.description == '' ){
            zMAjaxLoginRegister.reload();
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
                console.log( msg );
                // ajax_login_register_show_message( $form, msg );
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
        modal: true
    });


    /**
     * When the user scrolls close the dialog box
     */
    if ( $('.ajax-login-register-container').length ) {
        $( window ).on('scroll',function(){
            $('.ajax-login-register-container').dialog('close');
        });
    }

});