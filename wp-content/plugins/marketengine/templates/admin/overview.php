<?php
/**
 * The template for displaying Backend report.
 *
 * @package MarketEngine/Templates/Admin
 * @version 1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
$admin_report_tabs = array(
	'listings' => __("Listings", "enginethemes"),
	'members' => __("Members", "enginethemes"),
	'orders' => __("Orders", "enginethemes"),
	'inquiries' => __("Inquiries", "enginethemes"),
);

if(empty($_REQUEST['tab']) || !isset($admin_report_tabs[$_REQUEST['tab']])) {
	$requested_tab = 'listings';
}else {
	$requested_tab = $_REQUEST['tab'];
}
?>

<div class="marketengine-tabs">
	<ul class="me-nav me-tabs-nav">
		<?php
		foreach ($admin_report_tabs as $key => $tab) : ?>
			<li <?php if($requested_tab == $key ) {echo 'class="active"';} ?>><a href="?page=me-reports&tab=<?php echo $key; ?>"><?php echo $tab; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="me-tabs-container">
	<?php
		marketengine_get_template('admin/'. $requested_tab);
	?>
	</div>
</div>
