<?php

/**
 * @todo Remove get_option() and settings_fields( 'my-settings-group' ); with do_settings_sections( 'my-plugin' );
 */

$a = New Admin;
$settings = $a->get_settings();
$style = get_option( 'ajax_login_register_default_style' );

?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2><?php _e( 'Ajax Login &amp; Register Settings', 'ajax_login_register' );?></h2>
    <form action="options.php" method="post" class="form newsletter-settings-form">
        <?php settings_fields('ajax_login_register'); ?>

        <h3><?php _e( 'General Settings', 'ajax_login_register' ); ?></h3>
        <table class="form-table">
            <?php foreach( $settings['general'] as $setting ) : ?>
                <tr valign="top">
                    <th scope="row"><?php print $setting['label']; ?></th>
                    <td>
                        <input type="checkbox" name="<?php print $setting['key']; ?>" id="<?php print $setting['key']; ?>" <?php checked( get_option( $setting['key'], "off" ), "on" ); ?> />
                        <p class="description"><?php echo $setting['description']; ?></p>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3><?php _e( 'Facebook Settings', 'ajax_login_register' ); ?></h3>
        <table class="form-table" id="facebook-settings">
            <?php foreach( $settings['facebook'] as $setting ) : ?>
                <tr valign="top">
                    <th scope="row"><?php print $setting['label']; ?></th>
                    <td>
                        <?php echo $a->build_input( $setting['type'], $setting['key'] ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3><?php _e( 'Advanced Usage', 'ajax_login_register' ); ?></h3>
        <table class="form-table">
            <?php foreach( $settings['advanced_usage'] as $setting ) : ?>
                <tr valign="top">
                    <th scope="row"><?php print $setting['label']; ?></th>
                        <td>
                        <?php if ( $setting['key'] == 'ajax_login_register_default_style' ) : ?>
                            <select name="ajax_login_register_default_style">
                                <?php foreach( array('default','wide') as $option ) : ?>
                                    <option value="<?php print $option; ?>" <?php selected( $style, $option ); ?>><?php print ucfirst( $option );?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            <?php echo $a->build_input( $setting['type'], $setting['key'] ); ?>
                            <p class="description"><?php echo $setting['description']; ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php submit_button(); ?>
    </form>
</div>