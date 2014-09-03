<?php
/*
Plugin Name: Extractor IO
Plugin URI: https://github.com/NialtoServices/Extractor-IO
Description: WordPress plugin that utilizes the Import.IO service to extract data from a given URL and convert it into a post.
Author: Nialto Services
Version: 1.0
Author URI: https://nialtoservices.co.uk/
License: GPLv2
*/

if (!defined('ABSPATH')) exit;
if (get_bloginfo('version') < 3.8) die('Extractor IO requires a WordPress version of 3.8 or greater. Please update your WordPress installation to use this plugin.');

require_once('classes/importio.class.php');
require_once('classes/ns_array_hash.class.php');
require_once('classes/ns_connectors_table.class.php');
require_once('classes/ns_connector_fields_table.class.php');

if (!function_exists('ns_extractor_admin_init')) {
	function ns_extractor_admin_init() {
		register_setting( 'ns_extractor_settings_group', 'ns_extractor_api_key' );
		register_setting( 'ns_extractor_settings_group', 'ns_extractor_user_guid' );
		register_setting( 'ns_extractor_connectors_group', 'ns_extractor_connectors' );
	}
	add_action( 'admin_init', 'ns_extractor_admin_init' );
}

if (!function_exists('ns_extractor_admin_sidebar')) {
	function ns_extractor_admin_sidebar() {
		global $submenu;
		add_menu_page('Extractor IO', 'Extractor IO', 'manage_options', 'ns_extractor', 'ns_extractor', 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiB4bWw6c3BhY2U9InByZXNlcnZlIj48cGF0aCBpZD0iYmV6aWVyODAiIHN0cm9rZT0ibm9uZSIgZmlsbD0icmdiKDAsMCwwKSIgZD0iTSA0NDYuMDYsOTQuMDYgTCAzODUuOTQsMzMuOTQgQyAzNjcuMjcsMTUuMjcgMzMwLjQsMCAzMDQsMCBMIDgwLDAgQyA1My42LDAgMzIsMjEuNiAzMiw0OCBMIDMyLDQ2NCBDIDMyLDQ5MC40IDUzLjYsNTEyIDgwLDUxMiBMIDQzMiw1MTIgQyA0NTguNCw1MTIgNDgwLDQ5MC40IDQ4MCw0NjQgTCA0ODAsMTc2IEMgNDgwLDE0OS42IDQ2NC43MywxMTIuNzMgNDQ2LjA2LDk0LjA2IFogTSAzMjAsNjcuNzggQyAzMjEuMSw2OC4xOCAzMjIuMjMsNjguNjIgMzIzLjM4LDY5LjEgMzMyLjQxLDcyLjg0IDMzOC42OCw3Ny4xOSAzNDAuNjksNzkuMiBMIDQwMC44MSwxMzkuMzEgQyA0MDIuODIsMTQxLjMyIDQwNy4xNiwxNDcuNTkgNDEwLjksMTU2LjYyIDQxMS4zOCwxNTcuNzcgNDExLjgyLDE1OC45IDQxMi4yMiwxNjAgTCAzMjAsMTYwIDMyMCw2Ny43OCBaIE0gNDE2LDQ0OCBMIDk2LDQ0OCA5Niw2NCAyODgsNjQgMjg4LDE5MiA0MTYsMTkyIDQxNiw0NDggWiBNIDQxNiw0NDgiIC8+PHBhdGggaWQ9ImJlemllcjgxIiBzdHJva2U9Im5vbmUiIGZpbGw9InJnYigwLDAsMCkiIGQ9Ik0gMTI4LDIyNCBMIDE2MCwyMjQgMTYwLDQxNiAxMjgsNDE2IiAvPjxwYXRoIGlkPSJiZXppZXI4MiIgc3Ryb2tlPSJub25lIiBmaWxsPSJyZ2IoMCwwLDApIiBkPSJNIDEyOCwxNjAgTCAxNjAsMTYwIDE2MCwxOTIgMTI4LDE5MiIgLz48cGF0aCBpZD0ib3ZhbCIgc3Ryb2tlPSJub25lIiBmaWxsPSJyZ2IoMCwwLDApIiBkPSJNIDMzNC41NiwzNjYuNjQgQyAzNjAuNDgsMzQwLjg3IDM2MC40OCwyOTkuMTMgMzM0LjU2LDI3My4zNiAzMDguNTgsMjQ3LjU1IDI2Ni40MiwyNDcuNTUgMjQwLjQ0LDI3My4zNiAyMTQuNTIsMjk5LjEzIDIxNC41MiwzNDAuODcgMjQwLjQ0LDM2Ni42NCAyNjYuNDIsMzkyLjQ1IDMwOC41OCwzOTIuNDUgMzM0LjU2LDM2Ni42NCBMIDM1NS43LDM4Ny45MSBDIDMxOC4wMyw0MjUuMzYgMjU2Ljk3LDQyNS4zNiAyMTkuMywzODcuOTEgMTgxLjU3LDM1MC40MiAxODEuNTcsMjg5LjU4IDIxOS4zLDI1Mi4wOSAyNTYuOTcsMjE0LjY0IDMxOC4wMywyMTQuNjQgMzU1LjcsMjUyLjA5IDM5My40MywyODkuNTggMzkzLjQzLDM1MC40MiAzNTUuNywzODcuOTEgTCAzMzQuNTYsMzY2LjY0IFogTSAzMzQuNTYsMzY2LjY0IiAvPjwvc3ZnPgo=');
		add_submenu_page('ns_extractor', 'Extractor IO Settings', 'Settings', 'manage_options', 'ns_extractor_settings', 'ns_extractor_settings');
		$submenu['ns_extractor'][0][0] = 'Extract';
	}
	add_action('admin_menu', 'ns_extractor_admin_sidebar');
}

if (!function_exists('ns_extractor_sanitize_connectors')) {
	function ns_extractor_sanitize_connectors( $connectors ) {
		$index = 0;
		foreach ($connectors as $connector) {
			$valid = false;
			foreach($connector as $key => $value) {
				if ( !empty( $value ) ) {
					$valid = true;
				}
				$fields = array();
				if ( $connector['fields'] ) {
					$assigned_fields = array();
					foreach($connector['fields'] as $field) {
						if ( !in_array( $field['name'], $assigned_fields ) ) {
							array_push( $fields, $field );
							array_push( $assigned_fields, $field['name'] );
						}
					}
				}
				$connector['fields'] = $fields;
			}
			if ( $valid === false ) {
				unset($connectors[$index]);
			}
			$index += 1;
		}
		return array_values( $connectors );
	}
}

if (!function_exists('ns_extractor')) {
	function ns_extractor() {
		if ( empty( $_POST['step'] ) ) {
?>
	<h2>Extractor IO</h2>
	<?php $connectors = get_option( 'ns_extractor_connectors' ); ?>
	<?php if ( $connectors && count( $connectors ) > 0 ) { ?>
		<form method="post" action="admin.php?page=ns_extractor">
			<input type="hidden" name="step" value="fetch_data" />
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">Connector</th>
						<td>
							<select name="connector">
								<?php foreach ($connectors as $connector) { ?>
								<option value="<?php echo $connector['name']; ?>"><?php echo $connector['name']; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Website URL</th>
						<td>
							<input name="url" type="text" /><br /><br />
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button( 'Import Data' ); ?>
		</form>
	<?php } else { ?>
		<p>You don't appear to have any <strong>Connectors</strong>. You can create one <a href="admin.php?page=ns_extractor_settings">here</a>.</p>
	<?php } ?>
<?php } else if ( $_POST['step'] == 'fetch_data' ) { ?>
	<div class="loading_animation">
		<div class="pulse_loader">
			<div class="double-bounce1"></div>
			<div class="double-bounce2"></div>
		</div>
		<center><h3>Extracting...</h3></center>
	</div>
<?php
			ob_flush();
			$connector = NSIO()->get_connector( 'name', $_POST['connector'] );
			$data = NSIO()->query_url( $_POST['url'], $connector )[0];
			$post_id = wp_insert_post(
				array(
					"post_title" => "Extractor IO - Importing Your Data ...",
					"post_content" => "This post is currently being created by Extractor IO. If this post still appears to be importing after several minutes, then the import likely failed. If this is the case, you should be able to delete this post."
				)
			);
			$post_data = array(
				"ID" => $post_id,
				"post_title" => "",
				"post_content" => ""
			);
			if ($data && !empty($data)) {
				foreach($data as $data_field => $data_value) {
					foreach($connector['fields'] as $connector_field) {
						if ( $data_field == $connector_field['name'] ) {
							$actual_value = is_array( $data_value ) ? $data_value : array( $data_value );
							switch ( $connector_field['import_to'] ) {
								case "post_title":
									$post_title = $post_data['post_title'];
									if ( strlen( $post_title ) > 0 ) {
										$post_title .= " ";
									}
									$post_title .= implode(" ", $actual_value);
									$post_data['post_title'] = $post_title;
									break;

								case "post_content":
									$post_content = $post_data['post_content'];
									if ( strlen( $post_content ) > 0 ) {
										$post_content .= "\n\n";
									}
									$post_content .= implode("\n\n", $actual_value);
									$post_data['post_content'] = $post_content;
									break;

								case "post_image":
									$image_index = 0;
									foreach ($actual_value as $image) {
										$image_alts = is_array( $data[$data_field . '/_alt'] ) ? $data[$data_field . '/_alt'] : array( $data[$data_field . '/_alt'] );
										$description = ( !empty( $image_alts[$image_index] ) ? $image_alts[$image_index] : null );
										media_sideload_image( $image, $post_id, $description );
										$image_index += 1;
									}
									break;

								default:
									break;
							}
						}
					}
				}
				wp_update_post( $post_data );
				ns_extractor_safe_redirect( get_edit_post_link( $post_id ) );
			} else {
				die('Oops! There was a problem trying to extract data from that URL. Check that you entered the correct URL.');
			}
?>
	<style>.loading_animation { display: none; }</style>
<?php
		}
	}
}

if (!function_exists('ns_extractor_import_data_contains_key')) {
	function ns_extractor_import_data_contains_key( $import_data, $connector, $key ) {
		foreach($import_data as $data_field => $data_value) {
			foreach($connector['fields'] as $connector_field) {
				if ( $data_field == $connector_field['name'] ) {
					array_push( $used_fields, $connector_field['import_to'] );
				}
			}
		}
		return false;
	}
}

if (!function_exists('ns_extractor_settings')) {
	function ns_extractor_settings() {
		if ($_GET['a'] == 'new') {
			ns_extractor_modify_connector( $_GET['c'], 'Add Connector' );
		} else if ($_GET['a'] == 'edit') {
			ns_extractor_modify_connector( $_GET['c'], 'Edit Connector' );
		} else if ($_GET['a'] == 'delete') {
			ns_extractor_delete_connector( $_GET['c'], $_GET['cvh'] );
		} else if ($_GET['a'] == 'update') {
			ns_extractor_update_connector( $_GET['c'], $_GET['cvh'] );
		} else if ($_GET['a'] == 'fetch_connector_fields') {
			ns_extractor_fetch_connector_fields( $_GET['c'] );
		} else {
			ns_extractor_settings_index();
		}
	}
}

if (!function_exists('ns_extractor_settings_index')) {
	function ns_extractor_settings_index() {
		$connectors = get_option('ns_extractor_connectors');
?>
	<div class="wrap">
		<h2>Extractor IO Settings</h2>
		<form class="ns_extractor_general_settings_form" method="post" action="options.php">
			<?php settings_fields( 'ns_extractor_settings_group' ); ?>
			<?php do_settings_sections( 'ns_extractor_settings_group' ); ?>
			<h3>Import IO</h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">API Key</th>
						<td>
							<input name="ns_extractor_api_key" type="text" value="<?php echo get_option('ns_extractor_api_key'); ?>" class="regular-text code" />
						</td>
					</tr>
					<tr>
						<th scope="row">User GUID</th>
						<td>
							<input name="ns_extractor_user_guid" type="text" value="<?php echo get_option('ns_extractor_user_guid'); ?>" class="regular-text code" />
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
		<?php $api_key = get_option( 'ns_extractor_api_key' ); ?>
		<?php $user_guid = get_option( 'ns_extractor_user_guid' ); ?>
		<?php if ( !empty( $api_key ) && !empty( $user_guid ) ) { ?>
			<hr />
			<div class="ns_extractor_connectors_list">
				<h3 class="connectors-h3">Connectors</h3>
				<?php $table = new Connectors_List_Table(); ?>
				<?php $table->prepare_items(); ?>
				<?php $table->display(); ?>
				<?php if ( empty( $connectors ) ) { ?>
					<div class="error">
						<p>You don't appear to have any <strong>Connectors</strong>. You won't be able to extract data from any websites until you create a <strong>Connector</strong>.</p>
					</div>
				<?php } ?>
				<div class="connectors_list_actions">
					<a href="admin.php?page=ns_extractor_settings&a=new&c=<?php echo ($connectors) ? count( $connectors ) : 0; ?>" class="button">Add Connector</a>
				</div>
			</div>
		<?php } ?>
	</div>
<?php
	}
}

if (!function_exists('ns_extractor_modify_connector')) {
	function ns_extractor_modify_connector( $connector_id, $page_title ) {
		$connectors = get_option( 'ns_extractor_connectors' );
?>
	<div class="wrap">
		<h2><?php echo $page_title; ?></h2>
		<form class="ns_extractor_connector_form" method="post" action="admin.php?page=ns_extractor_settings&a=update&c=<?php echo $connector_id; ?>&cvh=<?php echo NSAH()->generate_array_hash( $connectors ); ?>">
			<?php settings_fields( 'ns_extractor_connectors_group' ); ?>
			<?php do_settings_sections( 'ns_extractor_connectors_group' ); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">Name</th>
						<td>
							<input name="ns_extractor_connectors[<?php echo $connector_id; ?>][name]" type="text" value="<?php echo (!empty($connectors[$connector_id])) ? $connectors[$connector_id]['name'] : ''; ?>" class="regular-text code" />
						</td>
					</tr>
					<tr>
						<th scope="row">Connector GUID</th>
						<td>
							<input name="ns_extractor_connectors[<?php echo $connector_id; ?>][guid]" type="text" value="<?php echo (!empty($connectors[$connector_id])) ? $connectors[$connector_id]['guid'] : ''; ?>" class="regular-text code" />
						</td>
					</tr>
				</tbody>
			</table>
			<br />
			<noscript>
				<p>You must have JavaScipt enabled in order to edit this Connector's Fields.</p>
			</noscript>
			<div id="connector_fields" style="display: none;">
				<?php if (!empty($connectors[$connector_id]['guid'])) { ?>
					<h2 class="fields-h2">Fields<a href="admin.php?page=ns_extractor_settings&a=fetch_connector_fields&c=<?php echo $connector_id ?>" class="refresh-button"></a></h2>
					<?php $table = new Connector_Fields_List_Table(); ?>
					<?php $table->prepare_items( $connector_id ); ?>
					<?php $table->display(); ?>
					<div class="connector_fields_actions"><a onclick="addField()" class="button add-button">Add Field</a></div>
<script>
window.onload = function() {
	<?php if ( $connectors && array_key_exists( $connector_id, $connectors ) && array_key_exists( 'fields' , $connectors[$connector_id] ) ) { ?>
		<?php foreach($connectors[$connector_id]['fields'] as $connector_field) { ?>
	addField('<?php echo $connector_field["name"]; ?>', '<?php echo $connector_field["import_to"]; ?>');
		<?php } ?>
	<?php } ?>
}

function addField(field_name, import_to) {
	var table_reference = document.getElementsByClassName('connector_fields')[0].getElementsByTagName('tbody')[0];
	var index = 0;

	for (var i = 0; i < table_reference.rows.length; i++) {
		if (table_reference.rows[i].className.split(" ").indexOf("no-items") > -1) {
			table_reference.deleteRow(i);
		}
	}

	var new_row = table_reference.insertRow(table_reference.rows.length);

	if ((table_reference.rows.length % 2) == true) {
		new_row.className = "alternate";
	}

	var field_name_cell = new_row.insertCell(0);
	var import_to_cell = new_row.insertCell(1);
	var actions_cell = new_row.insertCell(2);

	var import_to_select_html = '<select name="ns_extractor_connectors[<?php echo $connector_id; ?>][fields][' + table_reference.rows.length + '][import_to]" onchange="updateAllProtectedFields();" required>';
	import_to_select_html += '<option value=""' + ((!import_to || import_to == '') ? ' selected' : '') + '>-- Choose Field --</option>';
	import_to_select_html += '<option value="post_title"' + ((import_to && import_to == 'post_title') ? ' selected' : '') + '>Post Title</option>';
	import_to_select_html += '<option value="post_image"' + ((import_to && import_to == 'post_image') ? ' selected' : '') + '>Post Image</option>';
	import_to_select_html += '<option value="post_content"' + ((import_to && import_to == 'post_content') ? ' selected' : '') + '>Post Content</option>';
	import_to_select_html += '</select>';
	import_to_cell.innerHTML = import_to_select_html;

	var field_name_select_html = '<select name="ns_extractor_connectors[<?php echo $connector_id; ?>][fields][' + table_reference.rows.length + '][name]" onchange="updateAllProtectedFields();" required>';
	<?php $connector_fields = get_option( 'ns_extractor_connector_fields' ); ?>
	<?php $name_fields = ($connector_fields) ? $connector_fields[$connectors[$connector_id]['name']] : array(); ?>
	<?php $name_fields = ($name_fields) ? $name_fields : array(); ?>
	field_name_select_html += '<option value="">-- Choose Field --</option>';
	<?php foreach($name_fields as $name) { ?>
		<?php $title = ucwords( strtolower( str_replace( '_', ' ', str_replace( '-', ' ', $name ) ) ) ); ?>
	field_name_select_html += <?php echo "'<option value=\"" . $name . "\"' + ((field_name && field_name == '" . $name . "') ? ' selected' : '') + '>" . $title . " (" . $name . ")</option>'" ?>;
	<?php } ?>
	field_name_select_html += '</select>';
	field_name_cell.innerHTML = field_name_select_html;

	actions_cell.innerHTML = '<div class="row-actions"><span class="delete"><a onclick="removeField(' + (table_reference.rows.length - 1) + ');">Delete</a></span></div>';

	var items_label = document.getElementsByClassName('displaying-num');
	for (var i = 0; i < items_label.length; i++) {
		var item_text = " items";
		if (table_reference.rows.length == 1) {
			item_text = " item";
		}
		items_label[i].innerHTML = table_reference.rows.length.toString() + item_text;
	}

	updateAllProtectedFields();
}

function removeField(row) {
	var table_reference = document.getElementsByClassName('connector_fields')[0].getElementsByTagName('tbody')[0];
	table_reference.deleteRow(row);

	for (var i = 0; i < table_reference.rows.length; i++) {
		var row = table_reference.rows[i];
		row.className = ((i % 2) == false) ? "alternate" : "";
		row.cells[2].innerHTML = '<div class="row-actions"><span class="delete"><a onclick="removeField(' + (i) + ');">Delete</a></span></div>';
	}

	var items_length = table_reference.rows.length;

	if (table_reference.rows.length == 0) {
		table_reference.innerHTML = '<tr class="no-items"><td class="colspanchange" colspan="3">No items found.</td></tr>';
		items_length = 0;
	}

	var items_label = document.getElementsByClassName('displaying-num');
	for (var i = 0; i < items_label.length; i++) {
		var item_text = " items";
		if (items_length == 1) {
			item_text = " item";
		}
		items_label[i].innerHTML = items_length.toString() + item_text;
	}

	updateAllProtectedFields();
}

function updateAllProtectedFields() {
	<?php $fields_list = array(); ?>
	<?php foreach($name_fields as $name) { ?>
		<?php $fields_list[] = "'$name'"; ?>
	<?php } ?>
	updateProtectedFields([<?php echo implode( ',', $fields_list) ?>], 0);
	updateProtectedFields(['post_title'], 1);
}

function updateProtectedFields(protected_values, select_index) {
	var table = document.getElementsByClassName('connector_fields')[0].getElementsByTagName('tbody')[0];
	var used_values = {};
	for (var a = 0; a < protected_values.length; a++) {
		var protected_value = protected_values[a];
		used_values[protected_value] = false;
		for (var b = 0; b < table.rows.length; b++) {
			var s = table.rows[b].getElementsByTagName('td')[select_index].getElementsByTagName('select')[0];
			var selected_value = s.options[s.selectedIndex].value;
			if (selected_value == protected_value) {
				used_values[protected_value] = true;
				for (var c = 0; c < table.rows.length; c++) {
					var s2 = table.rows[c].getElementsByTagName('td')[select_index].getElementsByTagName('select')[0];
					if (s.name != s2.name) {
						for (var d = 0; d < s2.options.length; d++) {
							var option = s2.options[d];
							if (option.value == protected_value) {
								option.disabled = true;
							}
						}
					}
				}
			}
		}
	}
	for (var a = 0; a < protected_values.length; a++) {
		var protected_value = protected_values[a];
		if (used_values[protected_value] == false) {
			for (var b = 0; b < table.rows.length; b++) {
				var s = table.rows[b].getElementsByTagName('td')[select_index].getElementsByTagName('select')[0];
				for (var c = 0; c < table.rows.length; c++) {
					var option = s.options[c];
					if (option.value == protected_value) {
						option.disabled = false;
					}
				}
			}
		}
	}
}
					</script>
				<?php } ?>
			</div>
			<script>
				document.getElementById('connector_fields').style.display = 'block';
			</script>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
	}
}

if (!function_exists('ns_extractor_delete_connector')) {
	function ns_extractor_delete_connector( $connector_id, $connectors_hash ) {
		$connectors = get_option( 'ns_extractor_connectors' );
		if ( NSAH()->verify_array_hash( $connectors, $connectors_hash ) ) {
			unset($connectors[$connector_id]);
			update_option( 'ns_extractor_connectors', array_values($connectors) );
		}
		ns_extractor_safe_redirect( site_url( '/wp-admin/admin.php?page=ns_extractor_settings' ) );
	}
}

if (!function_exists('ns_extractor_update_connector')) {
	function ns_extractor_update_connector( $connector_id, $connectors_hash ) {
		$api_key = get_option( 'ns_extractor_api_key' );
		$user_guid = get_option( 'ns_extractor_user_guid' );
		if ( !empty( $api_key ) && !empty( $user_guid ) ) {
			if ( !empty( $_POST['ns_extractor_connectors'] ) ) {
				$connectors = get_option( 'ns_extractor_connectors' );
				$connectors = ($connectors) ? $connectors : array();
				if ( $connectors == false || count( $connectors ) == 0 || NSAH()->verify_array_hash( $connectors, $connectors_hash ) ) {
					$has_fields = false;
					$new_connectors = ns_extractor_sanitize_connectors( array_values( $_POST['ns_extractor_connectors'] ) );
					if ( !empty( $new_connectors ) && count( $new_connectors ) > 0 ) {
						$valid = true;
						$connector = $connectors[$connector_id];
						$new_connector = $new_connectors[0];
						$compare_index = 0;
						foreach($connectors as $compare_connector) {
							if ( $new_connector['name'] == $compare_connector['name'] && $connector_id != $compare_index ) {
								$valid = false;
								ns_extractor_safe_redirect( site_url( '/wp-admin/admin.php?page=ns_extractor_settings&a=edit&c=' . $connector_id . '&cvh=' . NSAH()->generate_array_hash( $connectors ) . '&e=' . urlencode('A connector already exists with that name.') ) );
							}
							$compare_index += 1;
						}
						$refresh_fields = true;
						if ($connector && $new_connector) {
							if ( $connector['guid'] == $new_connector['guid'] && $connector['name'] == $new_connector['name'] ) {
								$refresh_fields = false;
							}
						}
						if ($valid) {
							$connectors[$connector_id] = $new_connector;
							update_option( 'ns_extractor_connectors', array_values( $connectors ) );
						}
						if ($refresh_fields) {
							ns_extractor_fetch_connector_fields( $connector_id );
						}
						if ( count( $new_connector['fields'] ) > 0 ) {
							ns_extractor_safe_redirect( site_url( '/wp-admin/admin.php?page=ns_extractor_settings' ) );
						} else {
							ns_extractor_safe_redirect( site_url( '/wp-admin/admin.php?page=ns_extractor_settings&a=edit&c=' . $connector_id . '&cvh=' . NSAH()->generate_array_hash( $connectors ) ) );
						}
					} else {
						ns_extractor_safe_redirect( site_url( '/wp-admin/admin.php?page=ns_extractor_settings&a=new&c=' . $connector_id ) );
					}
				} else {
					ns_extractor_safe_redirect( site_url( '/wp-admin/admin.php?page=ns_extractor_settings' ) );
				}
			} else {
				ns_extractor_safe_redirect( site_url( '/wp-admin/admin.php?page=ns_extractor_settings' ) );
			}
		} else {
			echo "<div class=\"error\"><p>Missing Import IO API Key or User GUID.</p></div>";
		}
	}
}

if (!function_exists('ns_extractor_fetch_connector_fields')) {
	function ns_extractor_fetch_connector_fields( $connector_id ) {
		$connector = get_option( 'ns_extractor_connectors' )[$connector_id];
		$fields = NSIO()->connector_schema($connector);
		$connector_fields = get_option( 'ns_extractor_connector_fields' );
		$connector_fields = ($connector_fields) ? $connector_fields : array();
		$connector_fields[$connector['name']] = $fields;
		update_option( 'ns_extractor_connector_fields', $connector_fields );
		ns_extractor_safe_redirect( site_url( '/wp-admin/admin.php?page=ns_extractor_settings&a=edit&c=' . $connector_id . '&cvh=' . NSAH()->generate_array_hash( get_option( 'ns_extractor_connectors' ) ) ) );
	}
}

if (!function_exists('ns_extractor_register_admin_styles_and_scripts')) {
	function ns_extractor_register_admin_styles_and_scripts( $hook_suffix ) {
		if ( $hook_suffix == "ns-extractor_page_ns_extractor_settings" ) {
			wp_enqueue_style( 'ns_extractor_settings_css', plugins_url( 'css/settings.css', __FILE__ ) );
		} else if ( $hook_suffix == "toplevel_page_ns_extractor" ) {
			wp_enqueue_style( 'ns_extractor_import_css', plugins_url( 'css/import.css', __FILE__ ) );
		}
	}
	add_action( 'admin_enqueue_scripts', 'ns_extractor_register_admin_styles_and_scripts' );
}

if (!function_exists('ns_extractor_admin_notices')) {
	function ns_extractor_admin_notices() {
		$api_key = get_option( 'ns_extractor_api_key' );
		$user_guid = get_option( 'ns_extractor_user_guid' );
		if ( empty( $api_key ) || empty( $user_guid ) ) {
			echo "<div class=\"error\"><p>Extractor IO requires an <strong>Import IO</strong> account to extract data. Please enter your <strong>Import IO</strong> account details on the settings page. You can signup for an account at <a href=\"https://import.io\">https://import.io</a></div>";
		}
		if ( $_GET['updated_notice'] ) {
			echo "<div class=\"updated\"><p>" . $_GET['updated_notice'] . "</p></div>";
		}
		if ( $_GET['error_notice'] ) {
			echo "<div class=\"error\"><p>" . $_GET['error_notice'] . "</p></div>";
		}
	}
	add_action( 'admin_notices', 'ns_extractor_admin_notices' );
}

if (!function_exists('ns_extractor_safe_redirect')) {
	function ns_extractor_safe_redirect( $url ) {
		if ( headers_sent() ) {
			echo "<script>window.location=\"$url\".replace(/&amp;/g, \"&\")</script><div class=\"error\"><p>Click <a href=\"$url\">here</a> if you are not redirected within 5 seconds.</p></div>";
		} else {
			wp_safe_redirect( $url );
		}
		exit;
	}
}

?>