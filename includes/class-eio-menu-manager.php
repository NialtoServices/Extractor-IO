<?php
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Check the EIO_Menu_Manager class does not already exist.
if (!class_exists('EIO_Menu_Manager')):

/**
 * Extractor IO Menu Manager
 *
 * This class manages the menus for this plugin.
 *
 * @class      EIO_Menu_Manager
 * @category   Manager Class
 * @version    1.0.1
 * @since      1.0.0
 * @author     Nialto Services
 * @copyright  2015 Nialto Services
 * @license    http://opensource.org/licenses/GPL-3.0
 * @package    ExtractorIO
 * @subpackage Includes/Managers
 * @internal
 * @final
 */
final class EIO_Menu_Manager {
	/**
	 * The shared instance of this class.
	 *
	 * @staticvar EIO_Menu_Manager
	 * @access private
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Get the shared instance
	 *
	 * Get the shared instance of this class, or if it
	 * doesn't exist, create an instance.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @return EIO_Menu_Manager The shared instance of this class.
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup an instance of the menu manager class
	 *
	 * Sets up an instance of the menu manager.
	 *
	 * @access private
	 * @since 1.0.0
	 * @internal
	 */
	private function __construct() {
		add_action('admin_menu', array($this, 'admin_menu'));
		
		$this->check_report_download();
	}

	/**
	 * WordPress administration menus
	 *
	 * Called when we can create our administration menus.
	 *
	 * @access public
	 * @since 1.0.0
	 * @internal
	 */
	public function admin_menu() {
		global $submenu;

		if (current_user_can('edit_posts')) {
			add_menu_page(
				__('Extractor IO - Extract', 'extractor-io'),
				__('Extractor IO', 'extractor-io'),
				'edit_posts',
				'eio-extract',
				array($this, 'extract_menu_page'),
				eio_get_plugin_url('assets/images/Extractor-IO.png')
			);
			
			if (current_user_can('activate_plugins')) {
				add_submenu_page(
					'eio-extract',
					__('Extractor IO - Settings', 'extractor-io'),
					__('Settings', 'extractor-io'),
					'activate_plugins',
					'eio-settings',
					array($this, 'settings_menu_page')
				);
				
				$submenu['eio-extract'][0][0] = __('Extract', 'extractor-io');
			}
		}
	}

	/**
	 * Content for the extract page
	 *
	 * Called when we can print out the html contents of the
	 * extract admin page.
	 *
	 * @access public
	 * @since 1.0.0
	 * @internal
	 */
	public function extract_menu_page() {
		if (false === empty($_POST['eio_connector']) && false === empty($_POST['eio_extraction_url']) && false === empty($_POST['eio_extract_nonce']) && filter_var($_POST['eio_extraction_url'], FILTER_VALIDATE_URL) && false === is_null(EIO()->import_io) && wp_verify_nonce($_POST['eio_extract_nonce'], 'eio_extract')) {
			$extractor = new EIO_Extractor($_POST['eio_connector']);
			
			if ('Extract Data' === $_POST['submit']) {
				$extractor->build_post($_POST['eio_extraction_url'], function($status, $param) {
					$error = null;
					
					switch ($status) {
						case EIO_Extractor::POST_INSERT_FAILED:
							$error = sprintf(
								__('Oops! Something went wrong extracting data from the URL:<br /><strong>%s</strong>', 'extrator-io'),
								$_POST['eio_extraction_url']
							);
							break;
						
						case EIO_Extractor::EXTRACTED_DATA_NULL:
						case EIO_Extractor::EXTRACTION_RESULTS_NULL:
							$error = sprintf(
								__('No data could be extracted from the URL:<br /><strong>%s</strong><br /><br />Make sure you choose the correct connector that matches up to the URL.', 'extractor-io'),
								$_POST['eio_extraction_url']
							);
							break;
						
						case EIO_Extractor::POST_EXTRACTED:
							eio_safe_redirect(get_edit_post_link($param));
							break;
					}
					
					if (false === is_null($error)) {
						ob_start();
						
						include(eio_get_plugin_dir('templates/admin/eio-admin-page-extract-error.php'));
						
						die(ob_get_clean());
					}
				});
			} else if ('Generate Report' === $_POST['submit']) {
				global $eio_report_data;
				$eio_report_data = array(
					'site_url' => get_site_url(),
					'extraction_url' => $_POST['eio_extraction_url'],
					'plugin_data' => EIO()->plugin_data(),
					'connector_mapping' => EIO()->connector_mappings->get_option($_POST['eio_connector']),
					'data' => array()
				);
				
				$extractor->build_post($_POST['eio_extraction_url'], function($status, $param) {
					global $eio_report_data;
					
					switch ($status) {
						case EIO_Extractor::EXTRACTED_DATA_NULL:
							$eio_report_data['data'][] = array(
								'type' => 'error',
								'message' => 'The extracted data was null. Possibly an Import IO error.'
							);
							break;
						
						case EIO_Extractor::EXTRACTION_RESULTS_NULL:
							$eio_report_data['data'][] = array(
								'type' => 'error',
								'message' => 'There were no results in the data extracted from the URL.'
							);
							break;
						
						case EIO_Extractor::POST_INSERT_FAILED:
							$eio_report_data['data'][] = array(
								'type' => 'error',
								'message' => 'Failed to create/insert the WordPress post object.'
							);
							break;
						
						case EIO_Extractor::POST_EXTRACTED:
							$eio_report_data['data'][] = array(
								'type' => 'info',
								'message' => 'The extracted data was successfully parsed into a post object.',
								'post_id' => $param
							);
							break;
						
						case EIO_Extractor::REPORT_DATA_EXTRACTED:
							$eio_report_data['data'][] = array(
								'type' => 'info',
								'message' => 'Data was successfully extracted using Import IO.',
								'extracted_data' => $param
							);
							break;
						
						case EIO_Extractor::REPORT_POST_UPDATED:
							$eio_report_data['data'][] = array(
								'type' => 'info',
								'message' => 'The WordPress post object was successfully created/updated.',
								'post_data' => $param
							);
							break;
					}
				}, true);
				
				$report_id = uniqid();
				
				set_transient('eio_report_' . $report_id, $eio_report_data, HOUR_IN_SECONDS);
				
				unset($eio_report_data);
				
				include(eio_get_plugin_dir('templates/admin/eio-admin-page-report-data.php'));
			}
		} else {
			$connectors = null;
			
			if (EIO()->import_io && EIO()->import_io->pullConnectors()) {
				$connectors = EIO()->import_io->connectors;
			}
			
			include(eio_get_plugin_dir('templates/admin/eio-admin-page-extract.php'));
		}
	}

	/**
	 * Content for the settings page
	 *
	 * Called when we can print out the html contents of the
	 * settings admin page.
	 *
	 * @access public
	 * @since 1.0.0
	 * @internal
	 */
	public function settings_menu_page() {
		if ('edit_mapping' === $_GET['eio_action'] && false === empty($_GET['connector'])) {
			$connector = null;
			$connector_mapping_table = null;
			
			if (EIO()->import_io) {
				if (EIO()->import_io->pullConnectors()) {
					foreach (EIO()->import_io->connectors as $c) {
						if ($_GET['connector'] === $c['fields']['guid']) {
							$connector = $c;
							break;
						}
					}
				}
				
				if ($connector) {
					include_once(eio_get_plugin_dir('includes/class-eio-connector-mapping-table.php'));
					
					$connector_mapping_table = new EIO_Connector_Mapping_Table($connector);
					$connector_mapping_table->prepare_items();
				}
			}
			
			include(eio_get_plugin_dir('templates/admin/eio-admin-page-edit-connector-mapping.php'));
		} else {
			$username = null;
			$connectors_table = null;
			
			if (EIO()->import_io) {
				$current_user = EIO()->import_io->current_user();
				$username = $current_user['username'];
				
				if (EIO()->import_io->pullConnectors()) {
					include_once(eio_get_plugin_dir('includes/class-eio-connectors-table.php'));
					
					$connectors = EIO()->import_io->connectors;
										
					$connectors_table = new EIO_Connectors_Table($connectors);
					$connectors_table->prepare_items();
				}
			}
			
			include(eio_get_plugin_dir('templates/admin/eio-admin-page-settings.php'));
		}
	}
	
	/**
	 * Download a report
	 *
	 * Download a stored report if a report id has been specified.
	 *
	 * @access private
	 * @since 1.0.1
	 */
	private function check_report_download() {
		$eio_download_report = $_GET['eio_download_report'];
		if (is_string($eio_download_report) && false === empty($eio_download_report)) {
			$report = get_transient('eio_report_' . esc_attr($eio_download_report));
			
			if (is_array($report)) {
				$report = json_encode($report);
				$report = base64_encode($report);
				$report = chunk_split($report);
				
				$report_filename = strtolower(get_bloginfo('name'));
				
				foreach (str_split("!@£$%^&*()+-={}[]:\"|;'\\<>?,./ ") as $symbol) {
					$report_filename = str_replace($symbol, '_', $report_filename);
				}
				
				$report_filename = preg_replace('/_+/', '_', $report_filename);
				$report_filename .= '.eiodat';
				
				header('Content-Type: application/octet-stream; name="' . $report_filename .'"');
				header('Content-Disposition: attachment; filename="' . $report_filename . '"');
				
				die($report);
			}
		}
	}
}

EIO_Menu_Manager::instance();

endif;

?>