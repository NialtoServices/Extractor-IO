<?php
/**
 * The admin page for the extractor.
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

<h2>Extractor IO</h2>

<?php if (0 < count($connectors)): ?>
<p><?php _e('Fill in the form below to use this extractor.', 'extractor-io'); ?></p>
<div class="wrap">
	<form method="post" class="eio-settings-form">
		<?php wp_nonce_field('eio_extract', 'eio_extract_nonce'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">Connector</th>
					<td>
						<select name="eio_connector" width="100" style="width: 100%; max-width: 300px;">
							<?php foreach ($connectors as $connector): ?>
							<option value="<?php echo $connector['fields']['guid']; ?>"><?php echo $connector['fields']['name']; ?> (<?php echo $connector['fields']['domain']; ?>)</option>
							<?php endforeach; ?>
						</select>
					</td>
				<tr>
					<th scope="row">Extraction URL</th>
					<td>
						<input type="text" name="eio_extraction_url" style="width: 100%; max-width: 500px;" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button('Extract Data'); ?>
	</form>
</div>
<?php else: ?>
<p>You don't have any connectors. You can add a connector using the Import IO app from https://import.io</p>
<?php endif; ?>