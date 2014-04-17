jQuery( document ).ready(function( $ ){
    /**
     * We hook into the form submission and submit it via ajax.
     * the action maps to our php function, which is added as
     * an action, and we serialize the entire content of the form.
     */
    $( document ).on('submit', '.login_form', function( event ){
        event.preventDefault();
        $.ajax({
            data: "action=login_submit&" + $(this).serialize(),
            type: "POST",
            url: _ajax_login_settings.ajaxurl,
            success: function( msg ){
                if ( msg == 0 ){
                    $('#ajax-login-register-login-dialog').dialog('close');
                } else {
                    zMAjaxLoginRegister.reload();
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
             * If we get a successful authorization response we handle it
             * note the "scope" parameter.
             */
            if ( response.authResponse ) {

                /**
                 * "me" refers to the current FB user, console.log( response )
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
                            security: $('#facebook_security').val()
                        },
                        global: false,
                        type: "POST",
                        url: _ajax_login_settings.ajaxurl,
                        success: function( msg ){
                            $('.fb-login-container').append( msg.description );
                            zMAjaxLoginRegister.reload();
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
            scope: 'email'
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
            });
        }
    }

});