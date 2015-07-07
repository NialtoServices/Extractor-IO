<?php
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Check the EIO_Settings_Manager class does not already exist.
if (!class_exists('EIO_Settings_Manager')):

/**
 * Extractor IO Settings Manager
 *
 * This class manages the settings for this plugin.
 *
 * @class      EIO_Settings_Manager
 * @category   Manager Class
 * @version    1.0.0
 * @since      1.0.0
 * @author     Nialto Services
 * @copyright  2015 Nialto Services
 * @license    http://opensource.org/licenses/GPL-3.0
 * @package    ExtractorIO
 * @subpackage Includes/Managers
 * @internal
 * @final
 */
final class EIO_Settings_Manager {
	/**
	 * The shared instance of this class.
	 *
	 * @staticvar EIO_Settings_Manager
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
	 * @return EIO_Settings_Manager The shared instance of this class.
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup an instance of the settings manager class
	 *
	 * Sets up an instance of the settings manager.
	 *
	 * @access private
	 * @since 1.0.0
	 * @internal
	 */
	private function __construct() {
		add_action('init', array($this, 'save_settings'));
	}

	/**
	 * Save settings
	 *
	 * Save the settings on the menu page.
	 *
	 * @access public
	 * @since 1.0.0
	 * @internal
	 */
	public function save_settings() {
		if (false === current_user_can('activate_plugins')) {
			return;
		}
		
		if (false === empty($_POST['eio_settings_nonce']) && wp_verify_nonce($_POST['eio_settings_nonce'], 'eio_update_settings')) {
			$this->save_main_settings();
		} else if (false === empty($_POST['eio_connector_mapping_nonce']) && false === empty($_GET['connector']) && wp_verify_nonce($_POST['eio_connector_mapping_nonce'], 'eio_update_connector_mapping_' . $_GET['connector'])) {
			$this->save_connector_mapping_settings();
		} else {
			return;
		}
		
		do_action('eio_settings_updated');
	}
	
	/**
	 * Save Extractor IO Settings
	 *
	 * Save the main settings for the plugin.
	 *
	 * @access private
	 * @since 1.0.0
	 */
	private function save_main_settings() {
		$api_key = esc_attr($_POST['eio_api_key']);
		
		if (false === is_string($api_key) || empty($api_key)) {
			EIO()->options->delete_option('api_key');
			
			return;
		}
		
		$api_key = preg_replace('/\s+/', '', $api_key);
		
		try {
			$import_io = new ImportIO($api_key);
		} catch (BadFunctionCallException $e) {
			eio_add_error_notice($e->getMessage());
			
			return;
		}

		$user = $import_io->current_user();
		
		if (false === is_array($user) || empty($user) || false === array_key_exists('username', $user) || false === is_string($user['username'])) {
			eio_add_error_notice(
				__('The API Key was incorrect.', 'extractor-io')
			);
			
			return;
		}
		
		EIO()->options->update_option('api_key', $api_key);

		eio_add_updated_notice(
			__('The <strong>API Key</strong> has been saved.', 'extractor-io')
		);
	}
	
	/**
	 * Save a Connector's Mapping Settings
	 *
	 * Save the mapping for a connector.
	 *
	 * @access private
	 * @since 1.0.0
	 */
	private function save_connector_mapping_settings() {
		if (is_array($_POST['eio_import_to'])) {
			EIO()->connector_mappings->update_option($_GET['connector'], $_POST['eio_import_to']);

			eio_add_updated_notice(
				__('The connector mappings have been saved.', 'extractor-io')
			);
		}
	}
}

EIO_Settings_Manager::instance();

endif;

?>