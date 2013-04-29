<?php

/**
 * Markup needed for jQuery UI dialog, our form is actually loaded via AJAX
 */

?>
<div id="ajax-login-register-login-dialog" class="ajax-login-register-container" title="<?php _e('Login','ajax_login_register'); ?>" data-security="<?php print wp_create_nonce( 'login_form' ); ?>">
    <div id="ajax-login-register-login-target" class="ajax-login-register-login-dialog"><?php _e('Loading...','ajax_login_register'); ?></div>
</div>
