jQuery( document ).ready(function( $ ){

    /**
     * We hook into the form submission and submit it via ajax.
     * the action maps to our php function, which is added as
     * an action, and we serialize the entire content of the form.
     */
    $( document ).on('submit', '.login_form', function( event ){
        event.preventDefault();
        var $this = $(this);
        $.ajax({
            global: false,
            data: "action=login_submit&" + $this.serialize() + "&security=" + $this.data('alr_login_security'),
            type: "POST",
            url: _ajax_login_settings.ajaxurl,
            success: function( msg ){

                ajax_login_register_show_message( $this, msg );

            }
        });
    });


    /**
     * Our element we are attaching the 'click' event to is loaded via ajax.
     */
    $( document ).on( 'click', '.fb-login', function( event ){

        event.preventDefault();

        var $form_obj = $( this ).parents('form'),
            $this = $( this );

        /**
         * Doc code from FB, shows fb pop-up box
         *
         * @url https://developers.facebook.com/docs/reference/javascript/FB.login/
         */
         // Better to check via?
         // FB.api('/me/permissions', function( response ){});

         // Since WordPress requires the email we cannot continue if they
         // do not provide their email address
        FB.login( function( response ) {
            /**
             * If we get a successful authorization response we handle it
             * note the "scope" parameter.
             */
            var requested_scopes = ['public_profile','email','contact_email'];
            var response_scopes = $.map( response.authResponse.grantedScopes.split(","), $.trim );
            var diff = $( requested_scopes ).not( response_scopes ).get();
            var granted_access = diff.length;

            if ( ! granted_access ){

                /**
                 * "me" refers to the current FB user, console.log( response )
                 * for a full list.
                 */
                FB.api('/me', function(response) {
                    var fb_response = response;

                    /**
                     * Make an Ajax request to the "facebook_login" function
                     * passing the params: username, fb_id and email.
                     *
                     * @note Not all users have user names, but all have email
                     * @note Must set global to false to prevent gloabl ajax methods
                     */
                    $.ajax({
                        data: {
                            action: "facebook_login",
                            fb_response: fb_response,
                            security: $this.data('alr_facebook_security')
                        },
                        global: false,
                        type: "POST",
                        url: _ajax_login_settings.ajaxurl,
                        success: function( msg ){
                            ajax_login_register_show_message( $form_obj, msg );
                        }
                    });
                });
            } else {
                console.log('User canceled login or did not fully authorize.');
            }
        },{
            /**
             * See the following for full list:
             * @url https://developers.facebook.com/docs/authentication/permissions/
             */
            scope: 'email',
            return_scopes: true
        });
    });

    /**
     * Open the dialog box based on the handle, send the AJAX request.
     */
    if ( _ajax_login_settings.login_handle.length ){

        if ( _ajax_login_settings.is_user_logged_in == 1 ){

            $this = $( _ajax_login_settings.login_handle ).children('a');

            $this.html( _ajax_login_settings.logout_text );
            $this.attr( 'href', _ajax_login_settings.wp_logout_url );

        } else {

            $( document ).on('click', _ajax_login_settings.login_handle, function( event ){

                event.preventDefault();
                zMAjaxLoginRegister.open_login();

                if ( ! _ajax_login_settings.pre_load_forms.length ){
                    zMAjaxLoginRegister.load_login();
                }

            });
        }
    }

    $( document ).on('click', '.not-a-member-handle', function( e ){
        e.preventDefault();
        $('#ajax-login-register-login-dialog').dialog('close');
        zMAjaxLoginRegisterDialog.open();
    });
});
