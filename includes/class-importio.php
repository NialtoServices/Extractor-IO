<?php
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Check the ImportIO class does not already exist.
if (!class_exists('ImportIO')):

/**
 * Import IO
 *
 * The interface to the import.io API
 *
 * @class      ImportIO
 * @category   API Class
 * @version    1.0.1
 * @since      1.0.0
 * @author     Nialto Services
 * @copyright  2015 Nialto Services
 * @license    http://opensource.org/licenses/GPL-3.0
 * @package    Extractor_IO
 * @subpackage ImportIO
 */
class ImportIO {
	/**
	 * The URL to the Import IO API.
	 *
	 * @var string
	 * @access private
	 */
	private $api_url = 'https://api.import.io';
	
	/**
	 * The API Key
	 *
	 * @var string
	 * @access private
	 */
	private $api_key = null;
	
	/**
	 * An array of connectors
	 *
	 * @var string
	 * @access public
	 */
	public $connectors = array();
	
	/**
	 * Setup an instance of the ImportIO class.
	 *
	 * Setup an instance of the ImportIO class with the specified
	 * User GUID and API Key.
	 *
	 * @access public
	 * @since 1.0.1
	 * @param string $api_key The API Key to use when communicating with Import IO's API.
	 */
	public function __construct($api_key) {
		if (false === is_string($api_key) || empty($api_key)) {
			throw new BadFunctionCallException(
				__('You must provide an API Key.', 'extractor-io')
			);
		}
		
		$this->api_key = $api_key;
	}
	
	/**
	 * Get the current user
	 *
	 * Get information about the current user.
	 *
	 * @access public
	 * @since 1.0.1
	 * @return array|null The array of user information (if successful) or null (if unsuccessful).
	 */
	public function current_user() {
		$url = $this->api_url;
		$url .= '/auth/currentuser';
		$url .= '?_apikey=' . urlencode($this->api_key);
		
		$response = wp_remote_get($url, $this->build_wp_remote_args());
		$response_code = wp_remote_retrieve_response_code($response);
		
		if (in_array($response_code, range(200, 299))) {
			return json_decode($response['body'], true);
		} else {
			error_log('ImportIO: current_user() failed (' . $response_code . '): ' . json_encode($response));
		}
		
		return null;
	}
	
	/**
	 * Pull Connectors
	 *
	 * Pull down the array of connectors from Import IO.
	 * Only extractors will be added to the list of connectors
	 * as they are the only supported type.
	 *
	 * @access public
	 * @since 1.0.1
	 * @return boolean Whether or not the connectors were updated successfully.
	 */
	public function pullConnectors() {
		$connectors = array();

		$page = 1;
		$per_page = 100;
		
		$sort_direction = 'DESC';
		
		while (true) {
			$url = $this->api_url;
			$url .= '/store/connector/_search';
			$url .= '?_apikey=' . urlencode($this->api_key);
			$url .= '&_page=' . $page;
			$url .= '&_perpage=' . $per_page;
			$url .= '&_sortDirection=' . $sort_direction;
						
			$response = wp_remote_get($url, $this->build_wp_remote_args());
			$response_code = wp_remote_retrieve_response_code($response);
			
			if (in_array($response_code, range(200, 299))) {				
				$body = json_decode($response['body'], true);
				
				if (is_null($body)) {
					error_log('ImportIO: pullConnectors() failed: Unable to parse response (Possibly not a JSON response).');
					return false;
				}
				
				if (array_key_exists('hits', $body) && array_key_exists('hits', $body['hits'])) {
					$hits = $body['hits']['hits'];
					
					if (0 < count($hits)) {
						foreach ($hits as $connector) {
							if ('EXTRACTOR' === $connector['fields']['type']) {
								$connector['fields']['domain'] = implode('.', array_reverse(array_filter(explode('.', $connector['fields']['reversedDomain']))));
								array_push($connectors, $connector);
							}
						}
					} else {
						break;
					}
				} else {
					error_log('ImportIO: pullConnectors() failed: The response data did not contain any connectors');
					return false;
				}
			} else {
				error_log('ImportIO: pullConnectors() failed (' . $response_code . '): ' . json_encode($response));
				return false;
			}
			
			if (15 === $page) {
				break;
			}
			
			$page += 1;
		}
		
		$this->connectors = $connectors;
		
		return true;
	}
	
	/**
	 * Get Connector Schema
	 *
	 * Get the schema of a connector.
	 * You must provide the GUID of the VERSION of the connector,
	 * not the GUID of the connector itself.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array|null The array containing the schema of the connector (if successful) or null (if unsuccessful).
	 */
	public function getConnectorSchema($connector_version_guid) {
		if (!is_string($connector_version_guid) || empty($connector_version_guid)) {
			throw new BadFunctionCallException('You must provide the GUID of the connector version.');
		}
		
		$url = $this->api_url;
		$url .= '/store/connectorversion/' . $connector_version_guid . '/schema';
		$url .= '?_apikey=' . urlencode($this->api_key);
		
		$response = wp_remote_get($url, $this->build_wp_remote_args());
		$response_code = wp_remote_retrieve_response_code($response);
		
		if (in_array($response_code, range(200, 299))) {
			return json_decode($response['body'], true);
		} else {
			error_log('ImportIO: getConnectorSchema("' . $connector_version_guid . '") failed (' . $response_code . '): ' . json_encode($response));
		}
		
		return null;
	}
	
	/**
	 * Extract data from a URL
	 *
	 * Perform an extraction using the specified URL and Connector.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $connector_guid The GUID of the connector to use when extracting the data.
	 * @param string $extract_url The URL to extract data from.
	 * @return array|null An array containing the extracted data (if successful) or null (if unsuccessful).
	 */
	public function extractData($connector_guid, $extract_url) {
		if (empty($connector_guid)) {
			throw new BadFunctionCallException('Invalid connector specified.');
		}
		
		if (empty($extract_url) || false === filter_var($extract_url, FILTER_VALIDATE_URL)) {
			throw new BadFunctionCallException('Invalid URL specified.');
		}
		
		$url = $this->api_url;
		$url .= '/store/connector/' . $connector_guid . '/_query';
		$url .= '?_apikey=' . urlencode($this->api_key);
		
		$headers = array(
			'Content-Type' => 'application/json'
		);
		
		$body = json_encode(array(
			'input' => array(
				'webpage/url' => $extract_url
			)
		));
		
		$response = wp_remote_post($url, $this->build_wp_remote_args($headers, $body));
		$response_code = wp_remote_retrieve_response_code($response);
		
		if (in_array($response_code, range(200, 299))) {
			return json_decode($response['body'], true);
		} else {
			error_log('ImportIO: extractData("' . $connector['fields']['guid'] . '", "' . $extract_url . '") failed (' . $response_code . '): ' . json_encode($response));
		}
		
		return null;
	}
	
	/**
	 * Build arguments for the Wordpress Remote functions
	 *
	 * Build an array of arguments for the wp_remote_* set of functions.
	 *
	 * @param array $headers An array of headers.
	 * @param string $body The body of the request.
	 * @access private
	 * @since 1.0.1
	 */
	private function build_wp_remote_args($headers = array(), $body = null) {
		if (!is_array($headers)) {
			throw new BadFunctionCallException('The $headers parameter should be an array.');
		}
		
		return array(
			'timeout' => 5,
			'redirection' => 5,
			'httpversion' => '1.1',
			'user-agent' => 'WPExtractorIO/1.0',
			'blocking' => true,
			'headers' => $headers,
			'body' => $body,
			'compress' => true,
			'decompress' => true,
			'sslverify' => true,
			'stream' => false
		);
	}
}

endif;

?>