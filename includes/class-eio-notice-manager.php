<?php
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Check the EIO_Notice_Manager class does not already exist.
if (!class_exists('EIO_Notice_Manager')):

/**
 * Extractor IO Notices
 *
 * This class manages notices that should be printed
 * to the user.
 *
 * @class      EIO_Notice_Manager
 * @category   Manager Class
 * @version    1.0.0
 * @since      1.0.0
 * @author     Nialto Services
 * @copyright  2015 Nialto Services
 * @license    http://opensource.org/licenses/GPL-3.0
 * @package    ExtractorIO
 * @subpackage Includes/Managers
 * @final
 */
final class EIO_Notice_Manager {
	/**
	 * The shared instance of this class.
	 *
	 * @staticvar EIO_Notice_Manager
	 * @access private
	 * @since 1.0.0
	 */
	private static $instance = null;
	
	/**
	 * The key of the notices in the transients database.
	 *
	 * @var string
	 * @access private
	 * @since 1.0.0
	 */
	private $transient_key = null;
	
	/**
	 * Get the shared instance
	 *
	 * Get the shared instance of this class, or if it
	 * doesn't exist, create an instance.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @return EIO_Notice_Manager The shared instance of this class.
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Setup an instance of the notices manager class
	 *
	 * Sets up a session and creates (if it doesn't exist) a notices
	 * key to store your notices under, then hook's into WordPress functionality.
	 *
	 * @access private
	 * @since 1.0.0
	 * @internal
	 */
	private function __construct() {    
		if (array_key_exists('eio_transient_key', $_COOKIE) && !empty($_COOKIE['eio_transient_key'])) {
			$this->transient_key = $_COOKIE['eio_transient_key'];
		} else {
			$this->transient_key = uniqid('', true);
			setcookie('eio_transient_key', $this->transient_key, time() + 600, '/');
		}
		
		add_action('admin_notices', array($this, 'print_notices'));
	}
	
	/**
	 * Print the notices to the screen
	 *
	 * Loop through each notice in the session and print
	 * it to the screen.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function print_notices() {
		$notices = get_transient($this->transient_key);
		
		if (false === is_array($notices) || empty($notices)) {
			return;
		}
		
		echo '<div class="wrap">';
		
		foreach ($notices as $notice) {
			$class = $notice['success'] ? 'updated' : 'error';
			$style = implode(';', array('padding: 9px', 'margin-bottom: 40px'));
			$message = $notice['message'];
			
			echo '<div class="' . $class . '" style="' . $style . '">' . $message . '</div>';
		}
		
		echo '</div>';
		
		delete_transient($this->transient_key);
	}
	
	/**
	 * Add a notice
	 *
	 * Add a message with required capabilities to the array
	 * of notices in the session.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $message The message to display.
	 * @param array $success Whether the class applied to the notice div should be updated (if true) or error (if false)
	 * @return boolean Whether or not the message was added to the array of notices.
	 */
	public function add_notice($message, $success = true) {
		if (false === is_null($this->transient_key) && false === empty($message)) {
			$notices = get_transient($this->transient_key);
			
			if (false === is_array($notices) || empty($notices)) {
				$notices = array();
			}
			
			foreach ($notices as $notice) {
				if (0 === strcmp($notice['message'], $message)) {
					return false;
				}
			}
			
			$notices[] = array(
				'message' => $message,
				'success' => $success
			);
			
			set_transient($this->transient_key, $notices, 60);
			
			return true;
		}
		
		return false;
	}
}

EIO_Notice_Manager::instance();

endif;

?>
