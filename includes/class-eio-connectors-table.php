<?php
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

include_once('class-eio-list-table.php');

// Check the EIO_Connectors_Table class does not already exist.
if (!class_exists('EIO_Connectors_Table')):

/**
 * Extractor IO Connectors Table
 *
 * The EIO_List_Table subclass for Connectors
 * on the settings page.
 *
 * @class      EIO_Connectors_Table
 * @category   Table List Class
 * @version    1.0.0
 * @since      1.0.0
 * @author     Nialto Services
 * @copyright  2015 Nialto Services
 * @license    http://opensource.org/licenses/GPL-3.0
 * @package    ExtractorIO
 * @subpackage Includes/Tables
 * @final
 */
final class EIO_Connectors_Table extends EIO_List_Table {
	/**
	 * The array of connectors.
	 *
	 * @access private
	 * @since 1.0.0
	 */
	private $connectors = array();
	
	/**
	 * Setup a table instance
	 *
	 * Fetches the pages and sets up the parent table class.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $connectors An array of connectors.
	 */
	public function __construct($connectors) {
		$this->connectors = $connectors;
		
		parent::__construct(
			array(
				'singular' => 'connector',
				'plural' => 'connectors',
				'ajax' => false
			)
		);
	}
	
	/**
	 * Column Name
	 *
	 * Called by the table when the name of the connector is required.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $connector The connector's data.
	 * @return string The name of the field.
	 */
	public function column_name($connector) {
		return $connector['fields']['name'];
	}
	
	/**
	 * Column Domain Name
	 *
	 * Called by the table when the domain name the connector is linked to is required.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $connector The connector's data.
	 * @return string The domain name the connector is linked to.
	 */
	public function column_domain_name($connector) {
		return $connector['fields']['domain'];
	}

	/**
	 * Column Actions
	 *
	 * Called by the table when the actions for the connector are required.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $connector The connector's data.
	 * @return string The HTML code for the actions.
	 */
	public function column_actions($connector) {
		$actions = array(
			'edit_mapping' => sprintf(
				'<a href="?page=%s&eio_action=%s&connector=%s">%s</a>',
				$_REQUEST['page'],
				'edit_mapping',
				$connector['fields']['guid'],
				__('Edit Mapping', 'extractor-io')
			),
		);

		return $this->row_actions($actions, true);
	}

	/**
	 * Get table columns
	 *
	 * Called by the table when the columns for the table
	 * header are required.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array The columns in the table's header and footer.
	 */
	public function get_columns() {
		return array(
			'name' => 'Name',
			'domain_name' => 'Domain Name',
			'actions' => 'Actions'
		);
	}

	/**
	 * Prepare the table rows
	 *
	 * Prepare the items that should be put into the table.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->items = $this->connectors;

		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns()
		);

		$this->set_pagination_args(
			array(
				'total_items' => count($this->items),
			)
		);
	}
}

endif;

?>
