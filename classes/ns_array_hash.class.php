<?php
// License: GPLv2
	class NS_Array_Hash {
		var $algorithm;

		function generate_array_hash( $array ) {
			if ( is_array( $array ) && !empty( $array ) ) {
				return hash( $this->algorithm, json_encode( $array ) );
			}
			return false;
		}

		function verify_array_hash( $array, $hash ) {
			if ( is_array( $array ) && !empty( $array ) && !empty( $hash ) ) {
				$current_hash = hash( $this->algorithm, json_encode( $array ) );
				return ( $current_hash === $hash );
			}
			return false;
		}
	}

	function NSAH() {
		$instance = null;
		if ( $instance === null ) {
			$instance = new NS_Array_Hash();
			$instance->algorithm = 'sha256';
		}
		return $instance;
	}
?>