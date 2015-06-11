<?php
/**
 * The admin page for parsing a report.
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
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="eio_action" value="parse_report" />
  <input type="file" name="report" accept=".eiodat" /><br /><br />
  <input type="submit" name="submit" id="submit" class="button" value="Parse Report">
</form>

<?php endif; ?>