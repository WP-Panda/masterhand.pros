<?php 
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$inquiries = marketengine_inquiries_report($_REQUEST);
if(empty($_REQUEST['paged'])) {
	$i = 1;	
}else {
	$i = (absint( $_REQUEST['paged'] ) - 1) * get_option( 'posts_per_page' ) + 1;
}

$quant = empty($_REQUEST['quant']) ? 'day' : esc_attr( $_REQUEST['quant'] );
?>

<div class="me-tabs-content">
	<div class="me-section-container">
		<div class="me-section-content">
			<div class="me-revenue-section">
				<h3><?php _e("Report Inquiries", "enginethemes"); ?></h3>
				<?php marketengine_get_template('admin/filter'); ?>
				<div class="me-table me-report-table">
					<div class="me-table-rhead">
						<div class="me-table-col">
							<span><?php _e("No.", "enginethemes"); ?></span>
						</div>
						<?php marketengine_report_heading('quant', __("Date", "enginethemes")) ?>
						<?php marketengine_report_heading('count', __("Total Inquiries", "enginethemes")) ?>
					</div>
					<?php 

					if(!empty($inquiries['posts'])) {

						foreach ($inquiries['posts'] as $key => $inquiry) : ?>

							<div class="me-table-row">
								<div class="me-table-col"><?php echo $i ?></div>
								<div class="me-table-col">
									<?php echo marketengine_get_start_and_end_date($quant, $inquiry->quant, $inquiry->year); ?>
								</div>
								<div class="me-table-col"><?php echo $inquiry->count; ?></div>
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
				<?php marketengine_get_template('admin/pagination', array('query' => $inquiries)); ?>
			</div>
		</div>
	</div>
</div>