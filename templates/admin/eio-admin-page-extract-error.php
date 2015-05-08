<?php
/**
 * The admin page for when an extraction error occurs.
 *
 * @author     Nialto Services
 * @package    ExtractorIO
 * @subpackage Templates/Admin
 * @version    1.0.0
 */
 
// Ensure we are not being accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

?>

<h2>Extractor IO - Error</h2>

<div class="wrap">
	<p><?php echo $error; ?></p>
	<a href="?page=<?php echo $_GET['page']; ?>" class="button">Back to Extractor</a>
</div>
