<?php
/**
 * The admin page for a generated report.
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

<h2>Extractor IO - Report</h2>

<p><?php echo _e('A report has been generated and the following information has been included in it:', 'extractor-io'); ?></p>

<div class="wrap">
	<div class="updated" style="padding: 9px; margin-bottom: 40px;">
		<p>
			<strong><?php _e("Your Site's URL:", 'extractor-io'); ?></strong>&nbsp;
			<?php echo get_bloginfo('url'); ?>
		</p>
		<p>
			<strong><?php _e("Your WordPress Version:", 'extractor-io'); ?></strong>&nbsp;
			<?php echo get_bloginfo('version'); ?>
		</p>
		<p>
			<strong><?php _e("The Extraction URL:", 'extractor-io'); ?></strong>&nbsp;
			<?php echo $_POST['eio_extraction_url']; ?>
		</p>
		<p>
			<strong><?php _e("Plugin Information:", 'extractor-io'); ?></strong>&nbsp;
			<?php _e('Name, Version, etc ...', 'extractor-io'); ?>
		</p>
		<p>
			<strong><?php _e("Connector Information:", 'extractor-io'); ?></strong>&nbsp;
			<?php _e('GUID, Mapping, etc ...', 'extractor-io'); ?>
		</p>
		<p>
			<?php _e("Any data that has been extracted from the Extraction URL.", 'extractor-io'); ?>
		</p>
	</div>
	<p><?php _e('You can download the report by clicking/tapping the button below.', 'extractor-io'); ?></p>
	<a href="?page=<?php echo $_REQUEST['page']; ?>&eio_download_report=<?php echo $report_id; ?>" class="button"><?php _e('Download Report', 'extractor-io'); ?></a>
</div>
