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

$connection_class = $username ? 'updated' : 'error';
$connection_style = implode(';', array('padding: 9px', 'margin-bottom: 40px'));
$connection_message = $username ? sprintf(__('Connected to Import IO as <strong>%s</strong>', 'extractor-io'), $username) : __('Not connected to Import IO', 'extractor-io');

$connection_status = '<div class="' . $connection_class . '" style="' . $connection_style . '">' . $connection_message . '</div>';

?>

<div class="wrap">
	<?php echo $connection_status; ?>
</div>

<h2>Extractor IO - Settings</h2>
<br />
<div class="wrap">
	<form method="post" class="eio-settings-form">
		<?php wp_nonce_field('eio_update_settings', 'eio_settings_nonce'); ?>
		<h3>Import IO</h3>
		<p><?php _e("To use this plugin, you'll need to provide your User ID and API Key from your Import IO account.<br />These settings can be found on your account page at https://import.io", 'extractor-io'); ?><p>
		<table class="form-table">
			<tbody>
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
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Settings">
	</form>
</div>
	