<?php
/**
 * The admin page for the settings menu item.
 *
 * @author     Nialto Services
 * @package    ExtractorIO
 * @subpackage Templates/Admin
 * @version    1.0.0
 */
 
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

?>

<h2>Extractor IO - Settings</h2>
<div class="wrap">
	<?php if ($username): ?>
	<div class="updated" style="padding: 9px; margin-bottom: 40px;"><?php echo __('Connected to Import IO as', 'extractor-io') . ' <strong>' . $username . '</strong>'; ?></div>
	<?php else: ?>
	<div class="error" style="padding: 9px; margin-bottom: 40px;"><?php echo __('Not connected to Import IO', 'extractor-io'); ?></div>
	<?php endif; ?>
	<form method="post" class="eio-settings-form">
		<?php wp_nonce_field('eio_update_settings', 'eio_settings_nonce'); ?>
		<h3>Import IO</h3>
		<p><?php _e("To use this plugin, you'll need to provide your User ID and API Key from your Import IO account.<br />These settings can be found on your account page at https://import.io", 'extractor-io'); ?><p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<?php _e('User ID', 'extractor-io'); ?>
					</th>
					<td>
						<input type="text" name="eio_user_guid" class="regular-text" value="<?php echo EIO()->options->get_option('user_guid'); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e('API Key', 'extractor-io'); ?>
					</th>
					<td>
						<input type="password" name="eio_api_key" class="regular-text" value="<?php echo EIO()->options->get_option('api_key'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php if ($connectors_table): ?>
		<br />
		<h3>Connectors</h3>
		<div class="wrap">
			<div id="icon-users" class="icon32"></div>
			<?php $connectors_table->display(); ?>
		</div>
		<?php endif; ?>
		<?php submit_button('Update Settings'); ?>
	</form>
</div>
	