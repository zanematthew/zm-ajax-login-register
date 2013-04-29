jQuery( document ).ready(function( $ ){
    /**
     * We hook into the form submission and submit it via ajax.
     * the action maps to our php function, which is added as
     * an action, and we serialize the entire content of the form.
     */
    $( document ).on('submit', '#ajax-login-register-login-dialog form, #login_form', function( event ){
        event.preventDefault();
        $.ajax({
            data: "action=login_submit&" + $(this).serialize(),
            success: function( msg ){
                if ( msg == 0 ){
                    $('#ajax-login-register-login-dialog').dialog('close');
                } else {
                    window.location.replace( _ajax_login_settings.redirect );
                }
            }
        });
    });


    /**
     * Our element we are attaching the 'click' event to is loaded via ajax.
     */
    $( document ).on( 'click', '.fb-login', function( event ){
        event.preventDefault();

        /**
         * Doc code from FB, shows fb pop-up box
         *
         * @url https://developers.facebook.com/docs/reference/javascript/FB.login/
         */
        FB.login( function( response ) {
            /**
             * If we get a succesful authorization response we handle it
             * note the "scope" parameter.
             */
            if ( response.authResponse ) {

                /**
                 * "me" referes to the current FB user, console.log( response )
                 * for a full list.
                 */
                FB.api('/me', function(response) {
                    var fb_response = response;

                    /**
                     * Yes, bad, very bad!
                     */
                    email = response.email;
                    var user_login = email.split("@");
                    user_login = user_login[0];

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
                            username: user_login,
                            fb_id: fb_response.id,
                            email: fb_response.email,
                            security: $('.ajax-login-default-form-container #security').val()
                        },
                        global: false,
                        success: function( msg ){
                            window.location.replace( _ajax_login_settings.redirect );
                        }
                    });
                });
            } else {
                console.log('User cancelled login or did not fully authorize.');
            }
        },{
            /**
             * See the following for full list:
             * @url https://developers.facebook.com/docs/authentication/permissions/
             */
            scope: 'email'
        });
    });

    /**
     * Open the dialog box based on the handle, send the AJAX request.
     */
    if ( _ajax_login_settings.login_handle.length ){
        $( document ).on('click', _ajax_login_settings.login_handle, function( event ){
            event.preventDefault();

            $('#ajax-login-register-login-dialog').dialog('open');

            var data = {
                action: 'load_template',
                referer: 'login_form',
                template: 'login-form',
                security: $('#ajax-login-register-login-dialog').attr('data-security')
            };

            $.ajax({
                data: data,
                success: function( msg ){
                    $( "#ajax-login-register-login-target" ).fadeIn().html( msg ); // Give a smooth fade in effect
                }
            });
        });
    }

});