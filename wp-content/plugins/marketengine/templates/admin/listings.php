<?php 
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$listings = marketengine_listing_report($_REQUEST);
if(empty($_REQUEST['paged'])) {
	$i = 1;	
}else {
	$i = (absint( $_REQUEST['paged'] ) - 1) * get_option( 'posts_per_page' ) + 1;
}

$quant = empty($_REQUEST['quant']) ? 'day' : esc_attr( $_REQUEST['quant'] );
$active_section = empty($_REQUEST['section']) ? '' : esc_attr( $_REQUEST['section'] );
?>

<div class="me-tabs-content">
	<ul class="me-nav me-section-nav">
		<li class="<?php if($active_section == '') {echo 'active';} ?>">
			<a href="?page=me-reports&tab=listings">
				<?php _e("All", "enginethemes"); ?>
			</a>
		</li>
		<li class="<?php if($active_section == 'purchase') {echo 'active';} ?>">
			<a href="?page=me-reports&tab=listings&section=purchase">
				<?php _e("Purchase", "enginethemes"); ?>
			</a>
		</li>
		<li class="<?php if($active_section == 'contact') {echo 'active';} ?>">
			<a href="?page=me-reports&tab=listings&section=contact">
				<?php _e("Contact", "enginethemes"); ?>
			</a>
		</li>
	</ul>
	<div class="me-section-container">

		<div class="me-section-content">
			<div class="me-revenue-section">
				<h3><?php _e("Report Listings", "enginethemes"); ?></h3>
				<?php marketengine_get_template('admin/filter'); ?>
				<div class="me-table me-report-table">
					<div class="me-table-rhead">
						<div class="me-table-col">
							<span><?php _e("No.", "enginethemes"); ?></span>
						</div>
						<?php marketengine_report_heading('quant', __("Date", "enginethemes")) ?>
						
						<?php 
							if($active_section == '') {
								marketengine_report_heading('count', __("Total Listings", "enginethemes"));
							}
						?>
						<?php 
							if($active_section == '' || $active_section =='purchase') {
								marketengine_report_heading('purchase_type', __("Purchase", "enginethemes"));
							}
						?>

						<?php 
							if($active_section == '' || $active_section =='contact') {
								marketengine_report_heading('contact_type', __("Contact", "enginethemes"));
							}
						?>
					</div>
					<?php 

					if(!empty($listings['posts'])) {

						foreach ($listings['posts'] as $key => $listing) : ?>

							<div class="me-table-row">
								<div class="me-table-col"><?php echo $i ?></div>
								<div class="me-table-col">
									<?php echo marketengine_get_start_and_end_date($quant, $listing->quant, $listing->year); ?>
								</div>

								<?php if($active_section == '') : ?>
									<div class="me-table-col"><?php echo $listing->count; ?></div>
								<?php endif; ?>
								
								<?php if($active_section == '' || $active_section =='purchase') : ?>
									<div class="me-table-col"><?php echo $listing->purchase_type; ?></div>
								<?php endif; ?>

								<?php if($active_section == '' || $active_section =='contact') : ?>
									<div class="me-table-col"><?php echo $listing->contact_type; ?></div>
								<?php endif; ?>

							</div>

							<?php $i++; ?>

						<?php endforeach; ?>

					</div>
					
					<?php }else { ?>
						</div>
						<div class="me-result-filter">
							<?php marketengine_get_template('admin/report-none'); ?>
						</div>
						
					<?php } ?>
					 
				<!-- </div> -->
				<?php marketengine_get_template('admin/pagination', array('query' => $listings)); ?>
			</div>
		</div>
	</div>
</div>