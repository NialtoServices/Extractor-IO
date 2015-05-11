<?php
/*
 * Plugin Name: Extractor IO
 * Plugin URI: https://github.com/NialtoServices/Extractor-IO
 * Description: A simple import.io extractor for WordPress.
 * Author: Nialto Services
 * Version: 2.0.0
 * Author URI: https://nialtoservices.co.uk
 * License: GPLv2
 */

// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Include other resources.
require_once('includes/eio-functions.php');
require_once('includes/class-importio.php');
require_once('includes/class-eio-extractor.php');
require_once('includes/class-eio-options-manager.php');
require_once('includes/class-eio-notice-manager.php');
require_once('includes/class-eio-menu-manager.php');
require_once('includes/class-eio-settings-manager.php');

// Check the ExtractorIO class does not already exist.
if (!class_exists('ExtractorIO')):

/**
 * Extractor IO
 *
 * The main class for the Extractor IO plugin.
 *
 * @class      ExtractorIO
 * @category   Core Class
 * @version    1.0.0
 * @since      2.0.0
 * @author     Nialto Services
 * @copyright  2015 Nialto Services
 * @license    http://opensource.org/licenses/GPL-3.0
 * @package    ExtractorIO
 * @subpackage 
 */
class ExtractorIO {
    /**
     * The shared instance of this class.
     *
     * @staticvar ExtractorIO
     * @access private
     * @since 1.0.0
     */
    private static $instance = null;
    
    /**
	 * The options manager for this plugin.
	 *
	 * @var EIO_Options_Manager
	 * @access public
	 * @since 1.0.0
	 */
	public $options = null;
	
	/**
	 * The options manager for this connector mappings.
	 *
	 * @var EIO_Options_Manager
	 * @access public
	 * @since 1.0.0
	 */
	public $connector_mappings = null;
	
	/**
	 * The instance of the ImportIO class.
	 *
	 * @var ImportIO
	 * @access public
	 * @since 1.0.0
	 */
	public $import_io = null;

    /**
     * Get the shared instance
     *
     * Get the shared instance of this class.
     *
     * @access public
     * @since 1.0.0
     * @return ExtractorIO The instance of this class.
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Setup an instance of the ExtractorIO class.
     *
     * Setup an instance of the ExtractorIO class.
     * This should only be used internally by the static
     * instance method.
     *
     * @access private
     * @since 1.0.0
     * @internal
     */
	private function __construct() {
		$this->options = new EIO_Options_Manager('eio_options');
		$this->connector_mappings = new EIO_Options_Manager('eio_connector_mappings');
		$this->setup_import_io();
		
		add_action('eio_settings_updated', array($this, 'after_settings_updated'));
    }
    
    /**
	 * Get plugin URL
	 *
	 * Get the URL to the plugin's directory.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return string The URL to the plugin's directory.
	 */
	public function plugin_url() {
		return untrailingslashit(plugin_dir_url(__FILE__));
	}

	/**
	 * Get plugin path
	 *
	 * Get the path to the plugin's directory.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return string The path to the plugin's directory.
	 */
	public function plugin_path() {
		return untrailingslashit(plugin_dir_path(__FILE__));
	}

	/**
	 * Get template path
	 *
	 * Get the path to the template directory.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return string The path to the template directory.
	 */
	public function template_path() {
		return apply_filters('eio_template_path', 'Extractor-IO/');
	}
	
	/**
	 * After settings updated
	 *
	 * After the Extractor IO settings have been updated,
	 * we should reload the ImportIO instance.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function after_settings_updated() {
		$this->setup_import_io();
	}
	
	/**
	 * Set the Import IO instance
	 *
	 * Setup an instance of the ImportIO class using
	 * the User GUID and API Key from the Options Manager.
	 *
	 * @access private
	 * @since 1.0.0
	 */
	private function setup_import_io() {
		$user_guid = $this->options->get_option('user_guid');
		$api_key = $this->options->get_option('api_key');
		
		if (empty($user_guid) || empty($api_key)) {
			$this->import_io = null;
		} else {
			$this->import_io = new ImportIO($user_guid, $api_key);
		}
	}
}

ExtractorIO::instance();

endif;

?>