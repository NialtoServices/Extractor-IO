<?php
// License: GPLv2

class ImportIO {
	private $user_guid;
	private $api_key;

	public static function sharedInstance() {
		static $instance = null;
        if ($instance === null) {
        	$user_guid = get_option('ns_extractor_user_guid');
        	$api_key = get_option('ns_extractor_api_key');
            $instance = new ImportIO($user_guid, $api_key);
        }
        return $instance;
	}

	function __construct($user_guid, $api_key) {
		if (!empty($user_guid) && !empty($api_key)) {
			$this->user_guid = $user_guid;
			$this->api_key = $api_key;
		}
	}

	function get_connector($key, $value) {
		$connectors = get_option( 'ns_extractor_connectors' );
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

	function query($input, $connector, $additional_input = false, $result_only = true) {
		$this->error_check($connector);

		$url = "https://api.import.io/store/connector/" . $connector['guid'] . "/_query?_user=" . urlencode($this->user_guid) . "&_apikey=" . urlencode($this->api_key);

		$data = array("input" => $input);
		if ($additional_input) {
			$data["additionalInput"] = $additional_input;
		}

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$result = curl_exec($ch);

		curl_close($ch);

		if ($result_only) {
			return json_decode($result, true)['results'];
		}

		return json_decode($result);
	}

	function query_url($url, $connector, $result_only = true) {
		if (empty($url)) {
			die('ImportIO (query_url): URL is empty');
		}

		$this->error_check($connector);

		return $this->query(array("webpage/url" => $url), $connector, false, $result_only);
	}

	function connector_schema($connector) {
		$this->error_check($connector);

		$url = "https://api.import.io/store/connector/" . $connector['guid'] . "?_user=3c3651b7-f93e-4535-96ef-f0cfee1cb2a8&_apikey=" . urlencode($this->api_key);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$connector_result = json_decode(curl_exec($ch), true);

		curl_close($ch);

		$schema = array();

		if ($connector_result && $connector_result['latestVersionGuid']) {
			$url = "https://api.import.io/store/connectorversion/_io?id=" . $connector_result['latestVersionGuid'] . "&_user=3c3651b7-f93e-4535-96ef-f0cfee1cb2a8&_apikey=" . urlencode($this->api_key);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);

			$connectorversion_result = json_decode(curl_exec($ch), true);

			curl_close($ch);

			if ($connectorversion_result && $connectorversion_result['outputProperties']) {
				foreach($connectorversion_result['outputProperties'] as $property) {
					array_push($schema, $property['name']);
				}
			}
		}

		return $schema;
	}

	private function error_check($connector) {
		if ( empty( $this->user_guid ) || empty( $this->api_key ) ) {
			die('ImportIO: Unspecified user_guid or api_key.');
		}

		if ( empty( $connector ) || !is_array( $connector ) ) {
			die('ImportIO: Invalid Connector');
		}
	}
}

function NSIO() {
	return ImportIO::sharedInstance();
}

?>