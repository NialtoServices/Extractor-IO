<?php
// License: GPLv2
if (!defined('ABSPATH')) exit;
if (!class_exists('WP_List_Table')) require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
require_once('importio.class.php');
require_once('ns_array_hash.class.php');

class Connector_Fields_List_Table extends WP_List_Table {
	function __construct() {
		global $status, $page;
		parent::__construct(
			array(
				'singular' => 'connector_field',
				'plural' => 'connector_fields',
				'ajax' => false
			)
		);
	}

	function get_columns() {
		return array(
			'name' => 'Name',
			'import_to' => 'Import To',
			'actions' => 'Actions'
		);
	}

	function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	function prepare_items() {
		$this->_column_headers = array($this->get_columns(), array(), array());
		$this->items = array();
		$this->set_pagination_args(
			array(
				'total_items' => 0,
				'per_page'    => 1,
				'total_pages' => 1
			)
		);
	}
}
?>