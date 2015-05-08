<?php
/**
 * The admin page for a connector's mapping.
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

if ($connector):

?>

<h2>Extractor IO - <?php echo $connector['fields']['name']; ?></h2>
<div class="wrap">
	<form method="post" class="eio-connector-mapping-form">
		<?php wp_nonce_field('eio_update_connector_mapping_' . $connector['fields']['guid'], 'eio_connector_mapping_nonce'); ?>
		<div class="wrap">
			<div id="icon-users" class="icon32"></div>
			<?php $connector_mapping_table->display(); ?>
		</div>
		<?php submit_button('Update Settings'); ?>
	</form>
</div>

<?php else: ?>

<h2>Extractor IO - Unknown Connector</h2>
<p><?php _e("That connector doesn't appear to exist!", 'extractor-io'); ?></p>
<a href="?page=<?php echo $_REQUEST['page']; ?>" class="button"><?php _e('Back to Settings', 'extractor-io'); ?></a>

<?php endif; ?>