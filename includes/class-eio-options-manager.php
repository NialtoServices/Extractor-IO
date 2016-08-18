<?php
/**
 * Extractor IO - Includes - Options Manager
 * Copyright (C) 2015 Nialto Services
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Check the EIO_Options_Manager class does not already exist.
if (!class_exists('EIO_Options_Manager')):

/**
 * Extractor IO Options Manager
 *
 * This class allows you to store options under
 * a single key in the database.
 *
 * @class      EIO_Options_Manager
 * @category   Manager Class
 * @version    1.0.1
 * @since      2.0.0
 * @author     Nialto Services
 * @copyright  2015 Nialto Services
 * @license    http://opensource.org/licenses/GPL-3.0
 * @package    ExtractorIO
 * @subpackage Includes/Managers
 * @final
 */
final class EIO_Options_Manager {
	/**
	 * The key to store the options under.
	 *
	 * @var string
	 * @access private
	 * @since 1.0.0
	 */
	private $options_key = null;

	/**
	 * Setup an instance of this class
	 *
	 * Sets up an instance of the options manager for
	 * a specific key in the database.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $options_key The key of the option to retrieve.
	 */
	public function __construct($options_key) {
		if (!is_string($options_key) || strlen($options_key) === 0) {
			throw new BadFunctionCallException('You need to provide the key for your options.');
		}

		$this->options_key = $options_key;
	}

	/**
	 * Get an option
	 *
	 * Retrieve an option from the options data in the database.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $key The option's key.
	 * @param mixed $default The default value to return if no value is found.
	 * @return mixed The option or default value.
	 */
	public function get_option($key, $default = null) {
		$options = get_option($this->options_key, array());

		if (count($options) === 0 || !array_key_exists($key, $options) || empty($options[$key])) {
			return $default;
		}

		$options_key = eio_sanitize_key($this->options_key) . '_get_' . eio_sanitize_key($key);
		return apply_filters($options_key, $options[$key], $this->options_key);
	}

	/**
	 * Get a site option
	 *
	 * Retrieve a sitewide option from the options data in the database.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $key The option's key.
	 * @param mixed $default The default value to return if no value is found.
	 * @return mixed The option or default value.
	 */
	public function get_site_option($key, $default = null) {
		$options = get_site_option($this->options_key, array());

		if (count($options) === 0 || !array_key_exists($key, $options) || empty($options[$key])) {
			return $default;
		}

		$options_key = eio_sanitize_key($this->options_key) . '_get_sitewide_' . eio_sanitize_key($key);
		return apply_filters($options_key, $options[$key], $this->options_key);
	}

	/**
	 * Update an option
	 *
	 * Create or update an option in the options data in the database.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $key The option's key.
	 * @param mixed $value The value to update.
	 * @return boolean Whether or not the update was successful.
	 */
	public function update_option($key, $value) {
		$options = get_option($this->options_key, array());

		$options[$key] = apply_filters(eio_sanitize_key($this->options_key) . '_update_' . eio_sanitize_key($key), $value, $this->options_key);

		return update_option($this->options_key, $options);
	}

	/**
	 * Update a site option
	 *
	 * Create or update a sitewide option in the options data in the database.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $key The option's key.
	 * @param mixed $value The value to update.
	 * @return boolean Whether or not the update was successful.
	 */
	public function update_site_option($key, $value) {
		$options = get_site_option($this->options_key, array());

		$options[$key] = apply_filters(eio_sanitize_key($this->options_key) . '_update_sitewide_' . eio_sanitize_key($key), $value, $this->options_key);

		return update_site_option($this->options_key, $options);
	}

	/**
	 * Delete an option
	 *
	 * Delete an option from the options data in the database.
	 *
	 * @access public
	 * @since 1.0.1
	 * @param string $key The option's key.
	 * @return boolean Whether or not the delete was successful.
	 */
	public function delete_option($key) {
		$options = get_option($this->options_key, array());

		do_action(eio_sanitize_key($this->options_key) . '_delete_' . eio_sanitize_key($key));

		unset($options[$key]);

		return update_option($this->options_key, $options);
	}

	/**
	 * Delete a site option
	 *
	 * Delete a sitewide option from the options data in the database.
	 *
	 * @access public
	 * @since 1.0.1
	 * @param string $key The option's key.
	 * @return boolean Whether or not the delete was successful.
	 */
	public function delete_site_option($key) {
		$options = get_option($this->options_key, array());

		do_action(eio_sanitize_key($this->options_key) . '_delete_sitewide_' . eio_sanitize_key($key));

		unset($options[$key]);

		return update_site_option($this->options_key, $options);
	}
}

endif;

?>
