<?php
// License: GPLv2
if (!defined('ABSPATH')) exit;
if (!class_exists('WP_List_Table')) require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
require_once('ns_array_hash.class.php');

class Connectors_List_Table extends WP_List_Table {
	function __construct() {
		global $status, $page;
		parent::__construct(
			array(
				'singular' => 'connector',
				'plural' => 'connectors',
				'ajax' => false
			)
		);
	}

	function get_columns() {
		return array(
			'name' => 'Name',
			'guid' => 'GUID',
			'fields' => 'Fields'
		);
	}

	function column_name($item) {
		$connectors_hash = NSAH()->generate_array_hash( get_option( 'ns_extractor_connectors' ) );

		$actions = array(
			'edit' => sprintf('<a href="?page=%s&a=%s&c=%s&cvh=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['index'], $connectors_hash),
			'delete' => sprintf('<a href="?page=%s&a=%s&c=%s&cvh=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['index'], $connectors_hash),
		);

		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
    }

	function column_default($item, $column_name) {
		return $item[$column_name];
	}

	function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	function prepare_items() {
		$this->_column_headers = array($this->get_columns(), array(), array());
		$data = array();
		$index = 0;
		$connectors = get_option('ns_extractor_connectors');
		if ( !empty( $connectors ) ) {
			foreach ($connectors as $connector) {
				$fields = array();
				if ( empty( $connector['fields'] ) ) {
					$fields = "<strong>No Fields Selected</strong>";
				} else {
					foreach($connector['fields'] as $field) {
						array_push( $fields, ucwords( strtolower( str_replace( '_', ' ', str_replace( '-', ' ', $field['name'] ) ) ) ) );
					}
					$fields = implode( ', ', $fields );
				}
				$data[] = array(
					'name' => $connector['name'],
					'guid' => $connector['guid'],
					'fields' => $fields,
					'index' => $index
				);
				$index += 1;
			}
		}
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$per_page = 5;
		$this->items = array_slice($data, (($current_page - 1) * $per_page), $per_page);
		$this->set_pagination_args(
			array(
				'total_items' => count($data),
				'per_page'    => $per_page,
				'total_pages' => ceil($total_items / $per_page)
			)
		);
	}
}
?>