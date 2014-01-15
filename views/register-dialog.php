<?php

/**
 * Markup needed for jQuery UI dialog, our form is actually loaded via AJAX
 */

?><div id="ajax-login-register-dialog" class="ajax-login-register-container" title="Register" data-security="<?php print wp_create_nonce( 'register_form' ); ?>">
    <div id="ajax-login-register-target" class="ajax-login-register-dialog"><?php _e('Loading...','ajax_login_register'); ?></div>
</div>