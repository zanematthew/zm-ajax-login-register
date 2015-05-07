jQuery( document ).ready(function( $ ){

    /**
     * Close our dialog box when the user clicks
     * cancel/exit/close.
     */
    $( document ).on('click', '.ajax-login-register-container .cancel', function(){
        $(this).closest('.ajax-login-register-container').dialog('close');
    });


    if ( _ajax_login_settings.register_handle.length ){
        $( document ).on('click', _ajax_login_settings.register_handle, function( event ){
            event.preventDefault();
            zMAjaxLoginRegister.open_register();
            if ( ! _ajax_login_settings.pre_load_forms.length ){
                zMAjaxLoginRegister.load_register();
            }
        });
    }


    /**
     * Confirms that two input fields match
     */
    $( document ).on('keyup', '.user_confirm_password', function(){
        $form = $(this).parents('form');

        if ( $(this).val() == '' ){
            $( '.register_button', $form ).attr('disabled',true);
            $( '.register_button', $form ).animate({ opacity: 0.5 });
        } else {
            $( '.register_button', $form ).removeAttr('disabled');
            $( '.register_button', $form ).animate({ opacity: 1 });
        }
     });


    /**
     * Our form is loaded via AJAX, so we need to attach our event to the document.
     * When the form is submitted process the AJAX request.
     */
    $( document ).on('submit', '.register_form', function( event ){

        event.preventDefault();
        var $this = $( this );

        passwords_match = zMAjaxLoginRegister.confirm_password('.user_confirm_password');

        if ( passwords_match.code == 'error' ){
            ajax_login_register_show_message( $this, msg );
        } else {
            $.ajax({
                global: false,
                data: "action=setup_new_user&" + $this.serialize() + "&security=" + $this.data('alr_register_security'),
                dataType: 'json',
                type: "POST",
                url: _ajax_login_settings.ajaxurl,
                success: function( msg ) {
                    ajax_login_register_show_message( $this, msg );
                }
            });
        }
    });

    $( document ).on('click', '.already-registered-handle', function(){
        $('#ajax-login-register-dialog').dialog('close');
        zMAjaxLoginDialog.open();
    });
});