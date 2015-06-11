<?php
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

include_once('class-eio-list-table.php');

// Check the EIO_Basic_Table class does not already exist.
if (!class_exists('EIO_Basic_Table')):

/**
 * Extractor IO Basic Table
 *
 * The EIO_List_Table subclass for creating a
 * table with custom headers.
 *
 * @class      EIO_Basic_Table
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
final class EIO_Basic_Table extends EIO_List_Table {
  /**
   * The array of columns.
   *
   * @access private
   * @since 1.0.0
   */
  private $columns = array();

  /**
   * The array of data.
   *
   * @access private
   * @since 1.0.0
   */
  private $data = array();
  
  /**
   * Setup a table instance
   *
   * Fetches the pages and sets up the parent table class.
   *
   * @access public
   * @since 1.0.0
   * @param array $columns An array containing the columns.
   * @param array $data An array of keys and values.
   */
  public function __construct($columns, $data) {
    $this->columns = $columns;
    $this->data = $data;
    
    parent::__construct(
      array(
        'singular' => 'info',
        'plural' => 'info',
        'ajax' => false
      )
    );
  }
  
  /**
   * Column Default
   *
   * Called by the table when the value for a column is required.
   *
   * @access public
   * @since 1.0.0
   * @param array $row The data for the current row.
   * @param string $column_name The slug of the current column.
   * @return string The value of the column in the row.
   */
  public function column_default($row, $column_name) {
    return $row[$column_name];
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
    $columns = array();

    foreach ($this->columns as $column) {
      $columns[$column['slug']] = $column['name'];
    }

    return $columns;
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
    $this->items = $this->data;

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
