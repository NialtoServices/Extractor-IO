<?php
  if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
  }
  
  if (false !== get_option('eio_options')) {
    delete_option('eio_options');
  }
  
  if (false !== get_option('eio_connector_mappings')) {
    delete_option('eio_connector_mappings');
  }
?>