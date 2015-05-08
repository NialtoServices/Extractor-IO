<?php
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

include_once('class-eio-list-table.php');

// Check the EIO_Connector_Mapping_Table class does not already exist.
if (!class_exists('EIO_Connector_Mapping_Table')):

/**
 * Extractor IO Connector Mapping Table
 *
 * The EIO_List_Table subclass for mappings of a Connector.
 *
 * @class      EIO_Connector_Mapping_Table
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
final class EIO_Connector_Mapping_Table extends EIO_List_Table {
	/**
	 * The existing connector mappings.
	 *
	 * @access private
	 * @since 1.0.0
	 */
	private $connector_mapping = null;
	
	/**
	 * The schema of the connector.
	 *
	 * @access private
	 * @since 1.0.0
	 */
	private $schema = array();
	
	/**
	 * Setup a table instance
	 *
	 * Fetches the pages and sets up the parent table class.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $schema The schema of the connector.
	 */
	public function __construct($connector) {		
		$this->connector_mapping = EIO()->connector_mappings->get_option($connector['fields']['guid']);
		$this->schema = EIO()->import_io->getConnectorSchema($connector['fields']['latestVersionGuid']);
		
		parent::__construct(
			array(
				'singular' => 'connector_mapping',
				'plural' => 'connector_mappings',
				'ajax' => false
			)
		);
	}
	
	/**
	 * Column Name
	 *
	 * Called by the table when the name of the field is required.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $field The field settings.
	 * @return string The name of the field.
	 */
	public function column_name($field) {
		return $this->prettify_name($field['name']);
	}
	
	/**
	 * Column Import To
	 *
	 * Called by the table when the import to field is required.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $field The field settings.
	 * @return string The import to location for the field.
	 */
	public function column_import_to($field) {
		$importable_locations = array();
		
		if ('STRING' === $field['type']) {
			$importable_locations = array(
				'ignore' => 'Ignore',
				'post_title' => 'Post Title',
				'post_content' => 'Post Content'
			);
		} else if ('IMAGE' === $field['type']) {
			$importable_locations = array(
				'ignore' => 'Ignore',
				'import_only' => 'Import Only',
				'post_content' => 'Post Content'
			);
		}
		
		if (0 < count($importable_locations)) {
			$html = '<select name="eio_import_to[' . $field['name'] . ']" width="150" style="width: 150px;">';
			
			foreach ($importable_locations as $key => $value) {
				$extra = null;
				
				if ($this->connector_mapping && array_key_exists($field['name'], $this->connector_mapping) && $key === $this->connector_mapping[$field['name']]) {
					$extra = ' selected';
				}
				
				$html .= '<option value="' . $key . '"' . $extra . '>' . $value . '</option>';
			}
			
			$html .= '</select>';
		} else {
			$html = '<p><strong>Unsupported</strong></p>';
		}
		
		return $html;
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
			'import_to' => 'Import To'
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
		$this->items = $this->schema['outputProperties'];

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
	public function prettify_name($name) {
		$name = strtolower($name);
		$name = str_replace('_', ' ', $name);
		$name = str_replace('-', ' ', $name);
		$name = ucwords($name);
		
		return $name;
	}
}

endif;

?>