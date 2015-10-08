$document.ready(function( $ ){

    /**
     * Close our dialog box when the user clicks
     * cancel/exit/close.
     */
    $document.on('click', '.ajax-login-register-container .cancel', function(){
        $(this).closest('.ajax-login-register-container').dialog('close');
    });


    if ( _zm_alr_settings.register_handle.length ){
        $document.on('click', _zm_alr_settings.register_handle, function( event ){

            event.preventDefault();

            zMAjaxLoginRegister.open_register();

            if ( _zm_alr_settings.pre_load_forms == 'zm_alr_misc_pre_load_no' ){
                zMAjaxLoginRegister.load_register();
            }

        });
    }


    /**
     * Confirms that two input fields match
     */
    $document.on('keyup change', '.user_confirm_password', function(){
        var $form = $(this).parents('form'),
            $formButton = $( '.register_button', $form );

        if ( !$(this).val() ){
            $formButton
                .attr('disabled',true)
                .stop()
                .animate({ opacity: 0.5 });
        } else {
            $formButton
                .removeAttr('disabled')
                .stop()
                .animate({ opacity: 1 });
        }
    });


    /**
     * Our form is loaded via AJAX, so we need to attach our event to the document.
     * When the form is submitted process the AJAX request.
     */
    $document.on('submit', '.register_form', function( event ){

        event.preventDefault();

        var $this = $( this ),
            serialized_form = $this.serialize(),
            form_fields = 'input[type="password"], input[type="text"], input[type="email"]',
            google_recaptcha = zMAjaxLoginRegister.recaptcha_check_register();

        $this.find( form_fields ).attr('disabled','disabled');

        if ( $('.user_confirm_password').length ){
            passwords_match = zMAjaxLoginRegister.confirm_password('.user_confirm_password');

            if ( passwords_match.code == 'show_notice' ){
                ajax_login_register_show_message( $this, msg );
                $this.find( form_fields ).removeAttr('disabled');
                zMAjaxLoginRegister.reload( msg.redirect_url );
                return false;
            }
        }

        $.ajax({
            global: false,
            data: "action=setup_new_user&" + serialized_form + "&security=" + $this.data('zm_alr_register_security') + "&" + google_recaptcha,
            dataType: 'json',
            type: "POST",
            url: _zm_alr_settings.ajaxurl,
            success: function( msg ) {
                ajax_login_register_show_message( $this, msg );
                $this.find( form_fields ).removeAttr('disabled');
                zMAjaxLoginRegister.reload( msg.redirect_url );
            }
        });

    });

    $document.on('click', '.already-registered-handle', function(e){
        e.preventDefault();
        $('#ajax-login-register-dialog').dialog('close');
        zMAjaxLoginRegister.open_login();
        if ( _zm_alr_settings.pre_load_forms == 'zm_alr_misc_pre_load_no' ){
            zMAjaxLoginRegister.load_login();
        }
    });
});