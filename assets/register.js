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

            $('#ajax-login-register-dialog').dialog('open');

            var data = {
                action: 'load_template',
                template: 'register-form',
                referer: 'register_form',
                security:  $('#ajax-login-register-dialog').attr('data-security')
            };

            $.ajax({
                data: data,
                success: function( msg ){
                    $( "#ajax-login-register-target" ).fadeIn().html( msg ); // Give a smooth fade in effect
                }
            });
        });
    }

    /**
     * Confirms that two input fields match
     */
    $( document ).on('blur', '#user_confirm_password', function(){
        if ( $.trim( $(this).val() ) == '' ) return;

        match_id = $( this ).attr( 'data-match_id' );
        match_value = $( match_id ).val();

        value = $( this ).val();
        register_button_id = $( this ).attr( 'data-register_button_id' );

        if ( value == match_value ) {
            $( register_button_id ).removeAttr('disabled');
            $( register_button_id ).animate({ opacity: 1 });
        } else {
            $( register_button_id ).attr('disabled',true);
            $( register_button_id ).animate({ opacity: 0.5 });
            ajax_login_register_show_message({
                "cssClass": "error",
                "description": "<div class='error-container'>Passwords do not match.</div>"
            });
        }
     });


    /**
     * Our form is loaded via AJAX, so we need to attach our event to the document.
     * When the form is submitted process the AJAX request.
     */
    $( document ).on('submit', '#register_form', function( event ){
        event.preventDefault();
        $.ajax({
            data: "action=register_submit&" + $( this ).serialize(),
            dataType: 'json',
            success: function( msg ) {
console.log( msg );
                ajax_login_register_show_message( msg );
                if ( msg.status == 0 ) window.location.replace( _ajax_login_settings.redirect );
            }
        });
    });

});