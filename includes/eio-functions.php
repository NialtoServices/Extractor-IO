<?php

if (!function_exists('EIO')) {
	/**
	 * Get ExtractorIO instance
	 *
	 * A shorthand function to get the shared instance
	 * of the ExtractorIO class.
	 *
	 * @package ExtractorIO
	 * @subpackage Functions
	 * @access public
	 * @since 2.0.0
	 */
	function EIO() {
		return ExtractorIO::instance();
	}
}

if (!function_exists('eio_sanitize_key')) {
	/**
	 * Sanitize a database key
	 *
	 * Sanitize a key string by converting all invalid characters
	 * to underscores and lowercasing it.
	 *
	 * @package ExtractorIO
	 * @subpackage Functions
	 * @access public
	 * @since 2.0.0
	 */
	function eio_sanitize_key($key) {
		$sanitized_key = strtolower($key);
		foreach (str_split('!@£$%^&*()+-= ') as $symbol) {
			$sanitized_key = str_replace($symbol, '_', $sanitized_key);
		}

		$sanitized_key = preg_replace('/_+/', '_', $sanitized_key);
		if (substr($sanitized_key, 0, 1) === '_') {
			$sanitized_key = substr($sanitized_key, 1);
		}

		if (substr($sanitized_key, -1) === '_') {
			$sanitized_key = substr($sanitized_key, 0, -1);
		}

		return $sanitized_key;
	}
}

if (!function_exists('eio_get_plugin_dir')) {
	/**
	 * Get the ExtractorIO plugin's root directory
	 *
	 * Get the path to the root directory of the ExtractorIO plugin
	 * and optionally append the $path variable to it.
	 *
	 * @package ExtractorIO
	 * @subpackage Functions
	 * @access public
	 * @since 2.0.0
	 * @param $path The path to append to the root directory.
	 * @return string The root path (optionally with the $plugin variable appended).
	 */
	function eio_get_plugin_dir($path = '') {
		$plugin_basedir = preg_split('/\//', plugin_basename(__FILE__));
		$plugin_basedir = $plugin_basedir[0];

		if (empty($path) || '/' !== substr($path, 0, 1)) {
			$plugin_basedir .= '/';
		}

		return ABSPATH . 'wp-content/plugins/' . $plugin_basedir . $path;
	}
}

if (!function_exists('eio_get_plugin_url')) {
	/**
	 * Get the ExtractorIO plugin's root URL
	 *
	 * Get the URL to the root directory of the ExtractorIO plugin
	 * and optionally append the $path variable to it.
	 *
	 * @package ExtractorIO
	 * @subpackage Functions
	 * @access public
	 * @since 2.0.0
	 * @param $path The path to append to the root URL.
	 * @return string The root URL (optionally with the $plugin variable appended).
	 */
	function eio_get_plugin_url($path = '') {
		$plugin_basedir = preg_split('/\//', plugin_basename(__FILE__));
		$plugin_basedir = $plugin_basedir[0];

		if (empty($path) || '/' !== substr($path, 0, 1)) {
			$plugin_basedir .= '/';
		}


		return get_site_url() . '/wp-content/plugins/' . $plugin_basedir . $path;
	}
}

if (!function_exists('eio_get_plugin_basename')) {
	/**
	 * Get the ExtractorIO plugin's basename
	 *
	 * Get the plugin basename for the ExtractorIO plugin,
	 * by using the main plugin file.
	 *
	 * @package ExtractorIO
	 * @subpackage Functions
	 * @access public
	 * @since 2.0.0
	 * @return string The basename of the current plugin.
	 */
	function eio_get_plugin_basename() {
		$plugin_basename = preg_split('/\//', plugin_basename(__FILE__));
		$plugin_basename = $plugin_basename[0];
		$plugin_basename = $plugin_basename . '/' . $plugin_basename . '.php';
		return $plugin_basename;
	}
}

if (!function_exists('eio_safe_redirect')) {
	/**
	 * Redirect to a URL.
	 *
	 * Redirect to the specified URL using
	 * the safest redirection method.
	 *
	 * @package ExtractorIO
	 * @subpackage Functions
	 * @access public
	 * @since 2.0.0
	 */
	function eio_safe_redirect($url) {
		if (false === is_string($url)) {
			throw new BadFunctionCallException('The specified URL was invalid.');
		}
		
		if (headers_sent()) {
			echo '<script>window.location="' . $url . '".replace(/&amp;/g, "&")</script><div class="error" style="margin: 9px;"><p>Click <a href="' . $url . '">here</a> if you are not redirected within 5 seconds.</p></div>';
		} else {
			wp_safe_redirect($url);
		}
		
		exit;
	}
}

if (!function_exists('eio_add_updated_notice')) {
  /**
   * Add an updated notice
   *
   * Add a notice about something that was updated or successful.
   *
   * @package ExtractorIO
   * @subpackage Functions
   * @access public
   * @since 2.0.0
   * @return boolean Whether or not the notice has been logged.
   */
  function eio_add_updated_notice($message) {
    EIO_Notice_Manager::instance()->add_notice($message, true);
  }
}

if (!function_exists('eio_add_error_notice')) {
  /**
   * Add an error notice
   *
   * Add a notice about something that went wrong or was unsuccessful.
   *
   * @package ExtractorIO
   * @subpackage Functions
   * @access public
   * @since 2.0.0
   * @return boolean Whether or not the notice has been logged.
   */
  function eio_add_error_notice($message) {
    EIO_Notice_Manager::instance()->add_notice($message, false);
  }
}

if (!function_exists('eio_prettify_name')) {
	/**
	 * Prettify Name
	 *
	 * Prettify a string.
	 *
	 * @access private
	 * @since 1.0.0
	 * @param string $name The name to prettify.
	 * @return string The prettified name.
	 */
	function eio_prettify_name($name) {
		$name = strtolower($name);
		$name = str_replace('_', ' ', $name);
		$name = str_replace('-', ' ', $name);
		$name = ucwords($name);
		
		return $name;
	}
}

?>