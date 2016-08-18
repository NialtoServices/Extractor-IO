<?php
/**
 * The admin page for parsing a report.
 *
 * @author     Nialto Services
 * @package    ExtractorIO
 * @subpackage Templates/Admin
 * @version    1.0.1
 *
 * Extractor IO - Admin Template - Parse Report Page
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

if ($report):

if (false === empty($thickbox_content)) {
	add_thickbox();
}

?>

<h3>URLs</h3>
<div class="wrap notablenavhead notablenavbottom">
  <div id="icon-users" class="icon32"></div>
  <?php $basic_info_table->display(); ?>
</div>

<br />

<h3>Plugin Info</h3>
<div class="wrap notablenavhead notablenavbottom">
  <div id="icon-users" class="icon32"></div>
  <?php $plugin_info_table->display(); ?>
</div>

<br />

<h3>Connector Mapping</h3>
<div class="wrap notablenavhead notablenavbottom">
  <div id="icon-users" class="icon32"></div>
  <?php $connector_mappings_table->display(); ?>
</div>

<br />

<h3>Event Log (<?php echo $event_log_total_items ?>)</h3>
<div class="wrap notablenavhead notablenavbottom">
  <div id="icon-users" class="icon32"></div>
  <?php $event_log_table->display(); ?>
</div>

<?php echo $thickbox_content; ?>

<?php else: ?>

<h2>Extractor IO - Parse Report</h2>

<p><?php _e('You can use this feature to parse an <strong>Extractor IO</strong> report file (eiodat).', 'extractor-io'); ?></p>

<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="eio_action" value="parse_report" />
  <input type="file" name="report" accept=".eiodat" /><br /><br />
  <input type="submit" name="submit" id="submit" class="button" value="Parse Report">
</form>

<?php endif; ?>