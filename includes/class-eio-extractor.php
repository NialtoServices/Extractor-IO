<?php
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Check the EIO_Extractor class does not already exist.
if (!class_exists('EIO_Extractor')):

/**
 * Extractor IO Extractor
 *
 * This class both extracts the data using Import IO from a specified URL
 * and converts it into a WordPress post.
 *
 * @class      EIO_Extractor
 * @category   Parser Class
 * @version    1.0.0
 * @since      2.0.0
 * @author     Nialto Services
 * @copyright  2015 Nialto Services
 * @license    http://opensource.org/licenses/GPL-3.0
 * @package    ExtractorIO
 * @subpackage Includes/Core
 */
class EIO_Extractor {
	/**
	 * The extracted data was null.
	 */
	const EXTRACTED_DATA_NULL = 1;
	
	/**
	 * The extracted data's results were null.
	 */
	const EXTRACTION_RESULTS_NULL = 2;
	
	/**
	 * Failed to insert WordPress post.
	 */
	const POST_INSERT_FAILED = 3;
	
	/**
	 * WordPress post inserted.
	 */
	const POST_EXTRACTED = 4;
	
	/**
   * The connector mapping was null.
   */
  const CONNECTOR_MAPPING_NULL = 5;
	
	/**
	 * Report Mode data extracted.
	 */
	const REPORT_DATA_EXTRACTED = 10;
	
	/**
	 * Report Mode post updated.
	 */
	const REPORT_POST_UPDATED = 11;
	
	/**
	 * Extract data from a URL
	 *
	 * Using Import IO, extract data from the specified URL.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $url The URL data should be extracted from.
	 * @param string $connector_guid The connector to use when extracting data.
	 * @return array|null The extracted data (if successful) or null (if unsuccessful)
	 */
	public function extract_data($url, $connector_guid = null) {
		if (false === is_string($connector_guid) || empty($connector_guid)) {
    	throw new BadFunctionCallException('You must provide a Connector GUID to use when extracting data.');
  	}
  	
		if (EIO()->import_io) {
			return EIO()->import_io->extractData($connector_guid, $url);
		}
		
		return null;
	}
	
	/**
	 * Build post from URL
	 *
	 * This will extract data from the specified URL,
	 * then parse it into a WordPress post.
	 *
	 * In report mode, the extracted and parsed data is returned
	 * via the callback. When the extraction completes all media
	 * associated with the post (including the post itself) is deleted.
	 *
	 * @param string $url The URL to extract data from.
	 * @param string $connector_guid The GUID of the connector to use when extracting data.
	 * @param function $callback The function to use as a callback whilst extracting data.
	 * @param boolean $report_mode Whether or not to use report mode.
	 * @return boolean Whether or not the data was successfully parsed.
	 */
  public function build_post_url($url, $connector_guid, $callback = null, $report_mode = false) {
    if (false === is_string($url) || empty($url) || false === filter_var($_POST['eio_extraction_url'], FILTER_VALIDATE_URL)) {
			throw new BadFunctionCallException('You must provide a valid URL.');
		}
		
		if (false === is_null($callback) && (false === is_object($callback) || 'Closure' !== get_class($callback))) {
			throw new BadFunctionCallException('You must provide a valid closure or a null callback.');
		}
		
		$connector_mapping = EIO()->connector_mappings->get_option($connector_guid);
		
		return $this->build_post($this->extract_data($url, $connector_guid), $connector_mapping, $callback, $report_mode);
  }
	
	/**
	 * Build post from extracted data.
	 *
	 * This will parse the specified data into a WordPress post.
	 *
	 * In report mode, the extracted and parsed data is returned
	 * via the callback. When the extraction completes all media
	 * associated with the post (including the post itself) is deleted.
	 *
	 * @param array $extracted_data The data to parse into a post.
	 * @param array $connector_mapping The mapping of where the extracted data should be imported to.
	 * @param function $callback The function to use as a callback whilst extracting data.
	 * @param boolean $report_mode Whether or not to use report mode.
	 * @return boolean Whether or not the data was successfully parsed.
	 */
	public function build_post($extracted_data, $connector_mapping, $callback = null, $report_mode = false) {
  	if (is_null($extracted_data) || false === is_array($extracted_data)) {
			if (false === is_null($callback)) {
				$callback(self::EXTRACTED_DATA_NULL, null);
			}
			
			return false;
		}
		
		if (is_null($connector_mapping) || false === is_array($connector_mapping)) {
    	if (false === is_null($callback)) {
      	$callback(self::CONNECTOR_MAPPING_NULL, null);
    	}
    	
    	return false;
  	}
  	
		if ($report_mode && false === is_null($callback)) {
			$callback(self::REPORT_DATA_EXTRACTED, $extracted_data);
		}
		
		if (1 > count($extracted_data['results'])) {
			if (false === is_null($callback)) {
				$callback(self::EXTRACTION_RESULTS_NULL, null);
			}
			
			return false;
		}
		
		$post_data = array(
			'post_status' => 'draft',
			'post_title' => __('Extractor IO - Currently Importing', 'extractor-io'),
			'post_content' => sprintf(
				__('This post is currently being imported by the Extractor IO plugin. It should be finished shortly. You can safely delete this post if Extractor IO failed to extract data from:<br /><strong>%s</strong>', 'extractor-io'),
				$url
			)
		);
		
		$post_id = wp_insert_post($post_data);
		
		if (0 === $post_id) {
			if (false === is_null($callback)) {
				$callback(self::POST_INSERT_FAILED, null);
			}
		
			return false;
		}
		
		if ($report_mode && false === is_null($callback)) {
			$callback(self::REPORT_POST_UPDATED, $post_data);
		}

		$post_data = array(
			'ID' => $post_id,
			'post_title' => '',
			'post_content' => ''
		);

		foreach ($extracted_data['results'] as $result) {
			foreach ($result as $key => $value) {
				$type = null;
				
				foreach ($extracted_data['outputProperties'] as $property) {
					if ($property['name'] === $key) {
						$type = $property['type'];
						break;
					}
				}
				
				if (false === is_array($value)) {
					$value = array($value);
				}
				
				switch ($connector_mapping[$key]) {
					case 'post_title':
						if ('STRING' === $type) {
							foreach ($value as $title) {
								if (false === empty($post_data['post_title'])) {
									$post_data['post_title'] .= ', ';
								}
								
								$post_data['post_title'] .= $title;
							}
						}
						break;
					
					case 'post_content':
						if ('STRING' === $type) {
							foreach ($value as $content) {
								if (false === empty($post_data['post_content'])) {
									$post_data['post_content'] .= "\n";
								}
								
								$post_data['post_content'] .= $content;
							}
						} else if ('IMAGE' === $type) {
							$alts = null;
							
							if (array_key_exists('image/_alt', $result) && $result['image/_alt']) {
								$alts = is_array($result['image/_alt']) ? $result['image/_alt'] : array($result['image/_alt']);
							}
							
							foreach ($value as $index => $image) {
								$img = media_sideload_image($image, $post_id, ($alts ? $alts[$index] : null));
								
								if (is_string($img)) {
									if (false === empty($post_data['post_content'])) {
										$post_data['post_content'] .= "\n";
									}
									
									$post_data['post_content'] .= $img;
								}
							}
						}
						break;
					
					case 'import_only':
						if ('IMAGE' === $type) {
							$alts = null;
							
							if (array_key_exists('image/_alt', $result) && $result['image/_alt']) {
								$alts = is_array($result['image/_alt']) ? $result['image/_alt'] : array($result['image/_alt']);
							}
							
							foreach ($value as $index => $image) {
								media_sideload_image($image, $post_id, ($alts ? $alts[$index] : null));
							}
						}
						break;
				}
			}
		}
		
		if (0 === wp_update_post($post_data)) {
			wp_delete_post($post_id, true);
			
			if (false === is_null($callback)) {
				$callback(self::POST_INSERT_FAILED, null);
			}
		
			return false;
		}
		
		if ($report_mode) {
			$children = get_children(array(
				'post_parent' => $post_id
			));
			
			foreach ($children as $child) {
				wp_delete_attachment($child->ID, true);
			}
			
			wp_delete_post($post_id, true);
			
			if (false === is_null($callback)) {
				$callback(self::REPORT_POST_UPDATED, $post_data);
			}
		}
		
		if (false === is_null($callback)) {
			$callback(self::POST_EXTRACTED, $post_id);
		}
		
		return true;
	}
}

endif;

?>