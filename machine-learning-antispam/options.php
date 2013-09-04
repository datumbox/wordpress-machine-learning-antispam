<?php
if (!function_exists('add_action')) {
    die();
}

add_action('admin_menu', 'machinelearningantispam_admin_menu');

function machinelearningantispam_admin_menu() {
    add_submenu_page('options-general.php', __('Machine Learning Antispam'), __('Machine Learning Antispam'), 'manage_options', 'machine-learning-antispam-config', 'machinelearningantispam_conf_page');
    
    //call register settings function
    add_action( 'admin_init', 'machinelearningantispam_settings' );
}

function machinelearningantispam_settings() {
    register_setting( 'machinelearningantispam-settings-group', 'datumbox_api_key');
    register_setting( 'machinelearningantispam-settings-group', 'machinelearningantispam_filterspam');
    register_setting( 'machinelearningantispam-settings-group', 'machinelearningantispam_filteradult');
    register_setting( 'machinelearningantispam-settings-group', 'machinelearningantispam_filternegative');
}

function machinelearningantispam_conf_page() {    
    ?>
    <div class="wrap">
    <h2><?php echo __('Machine Learning Antispam'); ?></h2>

    <?php
        if(get_option('datumbox_api_key')=='') {
    ?>
        <p><b><?php echo __('In order to use this plugin you must have a Datumbox API key. Sign up for a Free Datumbox Account:'); ?></b></p>
        <button onclick="window.location='http://www.datumbox.com/users/register/';" class="button button-primary"><?php echo __('Register Now'); ?></button>
        <br/>
        <br/>
        <hr/><br/>
    <?php
        }
    ?>
    
    <form method="post" action="options.php">
        <?php settings_fields( 'machinelearningantispam-settings-group' ); ?>
        <?php //do_settings( 'machinelearningantispam-settings-group' ); ?>
        <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php echo __('Datumbox API Key'); ?></th>
            <td><input type="text" name="datumbox_api_key" value="<?php echo get_option('datumbox_api_key'); ?>" /></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php echo __('Filter Spam Comments'); ?></th>
            <td><input type="checkbox" name="machinelearningantispam_filterspam" value="1" <?php echo (get_option('machinelearningantispam_filterspam'))?'checked="checked"':''; ?> /></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php echo __('Filter Adult Comments'); ?></th>
            <td><input type="checkbox" name="machinelearningantispam_filteradult" value="1" <?php echo (get_option('machinelearningantispam_filteradult'))?'checked="checked"':''; ?> /></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php echo __('Filter Negative Comments'); ?></th>
            <td><input type="checkbox" name="machinelearningantispam_filternegative" value="1" <?php echo (get_option('machinelearningantispam_filternegative'))?'checked="checked"':''; ?> /></td>
            </tr>
        </table>
        
        <?php submit_button(); ?>

    </form>
    </div>
    <?php 
} 
