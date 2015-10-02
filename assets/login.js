$document.ready(function( $ ){

    /**
     * We hook into the form submission and submit it via ajax.
     * the action maps to our php function, which is added as
     * an action, and we serialize the entire content of the form.
     */
    $document.on('submit', '.login_form', function( event ){
        event.preventDefault();

        var $this = $(this),
            google_recaptcha = zMAjaxLoginRegister.recaptcha_check_login( $this ),
            serialized_form = $this.serialize(),
            form_fields = 'input[type="password"], input[type="text"], input[type="email"], input[type="checkbox"], input[type="submit"]',
            data = {
                action: 'login_submit',
                security: $this.data('zm_alr_login_security')
            };

        $this.find( form_fields ).attr('disabled','disabled');

        $.ajax({
            global: false,
            data: "action=login_submit&" + serialized_form + "&security=" + $this.data('zm_alr_login_security') + "&" + google_recaptcha,
            type: "POST",
            url: _zm_alr_settings.ajaxurl,
            success: function( msg ){

                ajax_login_register_show_message( $this, msg );
                $this.find( form_fields ).removeAttr('disabled');
                zMAjaxLoginRegister.reload( msg.redirect_url );

            }
        });
    });


    /**
     * Our element we are attaching the 'click' event to is loaded via ajax.
     */
    $document.on( 'click', '.fb-login', function( event ){

        event.preventDefault();

        var $this = $( this );
        var $form_obj = $this.parents('form');

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
                            security: $this.data('zm_alr_facebook_security')
                        },
                        global: false,
                        type: "POST",
                        url: _zm_alr_settings.ajaxurl,
                        success: function( msg ){
                            ajax_login_register_show_message( $form_obj, msg );
                            zMAjaxLoginRegister.reload( msg.redirect_url );
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
    if ( _zm_alr_settings.login_handle.length ){


        // Set the "login" text to be "logout" if the user is logged in.
        if ( $('body.logged-in').length ){

            $this = $( _zm_alr_settings.login_handle ).children('a');

            $this.html( _zm_alr_settings.logout_text );
            $this.attr( 'href', _zm_alr_settings.wp_logout_url );

        }

        // Open the dialog when they click on it.
        else {

            $document.on('click', _zm_alr_settings.login_handle, function( event ){

                event.preventDefault();
                zMAjaxLoginRegister.open_login();

                // There's a setting to either load the container div, then have the
                // forms load inside via AJAX, or to have them already loaded, and "hidden"
                // via display:none.
                if ( _zm_alr_settings.pre_load_forms === 'zm_alr_misc_pre_load_no' ){
                    zMAjaxLoginRegister.load_login();
                }

            });
        }
    }

    $document.on('click', '.not-a-member-handle', function( e ){
        e.preventDefault();
        $('#ajax-login-register-login-dialog').dialog('close');
        zMAjaxLoginRegister.open_register();
        if ( _zm_alr_settings.pre_load_forms === 'zm_alr_misc_pre_load_no' ){
            zMAjaxLoginRegister.load_register();
        }
    });
});
