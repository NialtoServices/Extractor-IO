<?php
/**
 * Import IO
 *
 * This class provides an interface that allows you to easily
 * communicate with the Import IO API.
 *
 * @package NSExtractor
 * @subpackage Includes/ImportIO
 * @copyright 2015 Nialto Services
 * @license http://opensource.org/license/gpl-2.0.php
 * @since 1.0.0
 */

// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Check the ImportIO class does not already exist.
if (!class_exists('ImportIO')):

/**
 * The ImportIO class
 */
class ImportIO {
	/**
	 * The static instance of this class.
	 *
	 * @access private
	 * @var ImportIO
	 */
	private static $instance = null;

	/**
	 * The GUID of the import.io user's account.
	 *
	 * @access private
	 * @var string
	 */
	private $user_guid = null;

	/**
	 * The API Key of the import.io user's account.
	 *
	 * @access private
	 * @var string
	 */
	private $api_key = null;

	/**
	 * Instance
	 *
	 * Get the shared instance of this class.
	 *
	 * @access public
	 * @return ImportIO The shared instance of the ImportIO class.
	 */
	public static function instance() {
    if (is_null(self::$instance)) {
      $user_guid = get_option('ns_extractor_user_guid');
      $api_key = get_option('ns_extractor_api_key');
      self::$instance = new self($user_guid, $api_key);
    }

    return self::$instance;
	}

	/**
	 * Construct
	 *
	 * Called when an instance of this class is instantiated.
	 *
	 * @access public
	 * @param string $user_guid The GUID of the import.io user's account.
	 * @param string $api_key The API Key of the import.io user's account.
	 */
	public function __construct($user_guid, $api_key) {
		if (empty($user_guid) || empty($api_key)) {
			throw new Exception('The $user_guid and $api_key must not be empty.');
		}

		$this->user_guid = $user_guid;
		$this->api_key = $api_key;
	}

	/**
	 * Get Connector
	 *
	 * Get a connector from the options database.
	 *
	 * @access public
	 * @param string $key The key to look for on the connector.
	 * @param string $value The value to look for on the connector.
	 * @return array|null The connector or null.
	 */
	public function get_connector($key, $value) {
		$connectors = get_option('ns_extractor_connectors');

		if ($key == 'id') {
			return $connectors[$value];
		} else {
			foreach($connectors as $connector) {
				if ($connector[$key] == $value) {
					return $connector;
				}
			}
		}

		return null;
	}

	/**
	 * Query
	 *
	 * Run a query on the ImportIO API.
	 *
	 * @access public
	 * @param array $data The request data.
	 * @param array $connector The connector to use for the extraction.
	 * @param boolean $strip_meta Strip other metadata outside of the results.
	 * @return array The response data.
	 */
	public function query($data, $connector, $strip_meta = true) {
		if (empty($connector) || !is_array($connector)) {
			return null;
		}

		$url = 'https://api.import.io/store/connector/';
		$url .= $connector['guid'];
		$url .= '/_query?_user=';
		$url .= urlencode($this->user_guid);
		$url .= '&_apikey=';
		$url .= urlencode($this->api_key);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$result = curl_exec($ch);

		curl_close($ch);

		if (is_null($result)) {
			return null;
		}

		$result = json_decode($result, true);

		return ($strip_meta) ? $result['results'] : $result;
	}

	/**
	 * Query URL
	 *
	 * Run a URL query on the ImportIO API.
	 *
	 * @access public
	 * @param string $url The URL to extract data from.
	 * @param array $connector The connector to use for the extraction.
	 * @param boolean $strip_meta Strip other metadata outside of the results.
	 * @return array The response data.
	 */
	public function query_url($url, $connector, $strip_meta = true) {
		if (empty($url)) {
			throw new Exception('The URL should not be empty.');
		}

		return $this->query(array('input' => array('webpage/url' => $url)), $connector, $strip_meta);
	}

	/**
	 * Connector Schema
	 *
	 * Get the schema of a connector.
	 *
	 * @access public
	 * @param array $connector The connector to fetch the schema of.
	 * @return array The schema of the connector.
	 */
	public function connector_schema($connector) {
		if (empty($connector) || !is_array($connector)) {
			return null;
		}

		$url = 'https://api.import.io/store/connector/';
		$url .= $connector['guid'];
		$url .= '?_user=';
		$url .= urlencode($this->user_guid);
		$url .= '&_apikey=';
		$url .= urlencode($this->api_key);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$result = curl_exec($ch);

		curl_close($ch);

		if (is_null($result)) {
			return null;
		}

		$result = json_decode($result, true);

		if (!is_array($result)) {
			return null;
		}

		$schema = array();

		if ($result['latestVersionGuid']) {
			$url = 'https://api.import.io/store/connectorversion/_io?id=';
			$url .= $connector_result['latestVersionGuid'];
			$url .= '&_user=';
			$url .= urlencode($this->user_guid);
			$url .= '&_apikey=';
			$url .= urlencode($this->api_key);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);

			$version_result = curl_exec($ch);

			curl_close($ch);

			if (is_null($version_result)) {
				return null;
			}

			$version_result = json_decode($version_result, true);

			if (!is_array($version_result)) {
				return null;
			}

			if ($version_result['outputProperties']) {
				foreach($version_result['outputProperties'] as $property) {
					array_push($schema, $property['name']);
				}
			}
		}

		return $schema;
	}
}

endif;

if (!function_exists('IIO')):

/**
 * IIO
 *
 * Get the shared instance of the ImportIO class.
 *
 * @access public
 * @return ImportIO The shared instance of the ImportIO class.
 */
function IIO() {
	return ImportIO::instance();
}

endif;

?>
