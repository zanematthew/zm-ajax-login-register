jQuery( document ).ready(function( $ ){

    /**
     * Default ajax setup
     */
    $.ajaxSetup({
        type: "POST",
        url: ajaxurl
    });

    window.ajax_login_register_show_message = function( msg ) {

        if ( msg == null ) {
            jQuery('.ajax-login-register-msg-target').fadeOut('fast');
        }

        if ( ! msg ) return;

        jQuery('.ajax-login-register-msg-target').toggleClass( msg.cssClass );
        jQuery('.ajax-login-register-msg-target').fadeIn().html( msg.description );
    };


    /**
     * Server side email validation.
     */
    window.ajax_login_register_validate_email = function( myObj ){
        $this = myObj;

        if ( $.trim( $this.val() ) == '' ) return;

        $.ajax({
            data: "action=validate_email&email=" + $this.val(),
            dataType: 'json',
            success: function( msg ){
                ajax_login_register_show_message( msg );
console.log( msg );
            }
        });
    }


    /**
     * Validate email
     */
    $( document ).on('blur', '.ajax-login-register-validate-email', function(){
        console.log( $(this) );
        ajax_login_register_validate_email( $(this) );
    });


    /**
     * Check that username is valid
     */
    $( document ).on('blur', '#user_login', function(){
        if ( $.trim( $(this).val() ) == '' ) return;

        $.ajax({
            data: "action=validate_username&login=" + $( this ).val(),
            dataType: 'json',
            success: function( msg ){
                ajax_login_register_show_message( msg );
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
        modal: true,
        open: function(){
            $('.ui-widget-overlay').bind('click',function(){
                $('.ajax-login-register-container').dialog('close');
            });
        }
    });

});