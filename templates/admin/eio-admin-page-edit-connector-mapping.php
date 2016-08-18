<?php
/**
 * The admin page for a connector's mapping.
 *
 * @author     Nialto Services
 * @package    ExtractorIO
 * @subpackage Templates/Admin
 * @version    1.0.0
 *
 * Extractor IO - Admin Template - Edit Connector Mapping Page
 * Copyright (C) 2015 Nialto Services
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
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