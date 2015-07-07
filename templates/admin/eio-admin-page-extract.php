<?php
/**
 * The admin page for the extractor.
 *
 * @author     Nialto Services
 * @package    ExtractorIO
 * @subpackage Templates/Admin
 * @version    1.0.1
 */
 
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

?>

<h2>Extractor IO</h2>

<?php if (0 < count($connectors)): ?>
<p><?php _e('Fill in the form below to use this extractor.', 'extractor-io'); ?></p>
<div class="wrap">
	<form method="post" class="eio-extraction-form">
		<?php wp_nonce_field('eio_extract', 'eio_extract_nonce'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">Connector</th>
					<td>
						<select name="eio_connector" width="100" style="width: 100%; max-width: 300px;">
							<?php foreach ($connectors as $connector): ?>
							<?php $value = base64_encode(json_encode(array('name' => $connector['fields']['name'], 'guid' => $connector['fields']['guid']))); ?>
							<option value="<?php echo $value; ?>"><?php echo $connector['fields']['name']; ?> (<?php echo $connector['fields']['domain']; ?>)</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">Extraction URL</th>
					<td>
						<input type="text" name="eio_extraction_url" style="width: 100%; max-width: 500px;" />
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Extract Data">&nbsp;
			<input type="submit" name="submit" id="submit" class="button" value="Generate Report">
		</p>
	</form>
</div>
<?php elseif (EIO()->import_io): ?>
<p><?php echo __('<strong>No Connectors Found</strong><br />You need a connector to be able to extract data from sites.<br />You can add a connector from your Import IO account.', 'extractor-io'); ?></p>
<?php else: ?>
<p><?php echo __('<strong>No Account Configured</strong><br />You need to setup your Import IO account to be able to use this plugin.<br />Go to the settings page to configure your account:<br /><br /><a href="?page=eio-settings" class="button">Settings</a></p>', 'extractor-io'); ?></p>
<?php endif; ?>