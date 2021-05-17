<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$members = marketengine_members_report($_REQUEST);
if(empty($_REQUEST['paged'])) {
	$i = 1;
}else {
	$i = (absint( $_REQUEST['paged'] ) - 1) * get_option( 'posts_per_page' ) + 1;
}

$quant = empty($_REQUEST['quant']) ? 'day' : esc_attr( $_REQUEST['quant'] );
?>
<div class="me-tabs-content">
	<!-- <ul class="me-nav me-section-nav">
		<li class="active"><span>Revenue</span></li>
		<li><span>Members</span></li>
		<li><span>Orders &amp; Inquiries</span></li>
	</ul> -->
	<div class="me-section-container">

		<div class="me-section-content">
			<div class="me-revenue-section">

				<h3><?php _e("Report Members", "enginethemes"); ?></h3>

				<?php marketengine_get_template('admin/filter'); ?>

				<div class="me-table me-report-table">
					<div class="me-table-rhead">
						<div class="me-table-col">
							<span><?php _e("No.", "enginethemes"); ?></a></span>
						</div>
						<?php marketengine_report_heading('quant', __("Registration Date", "enginethemes")) ?>
						<?php marketengine_report_heading('count', __("Total Members", "enginethemes")) ?>
					</div>
					<?php

					if(!empty($members['posts'])) {

						foreach ($members['posts'] as $key => $member) : ?>
							<div class="me-table-row">
								<div class="me-table-col"><?php echo $i ?></div>
								<div class="me-table-col">
									<?php echo marketengine_get_start_and_end_date($quant, $member->quant, $member->year); ?>
								</div>
								<div class="me-table-col"><?php echo $member->count; ?></div>
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

				<?php marketengine_get_template('admin/pagination', array('query' => $members)); ?>

			</div>
		</div>
	</div>
</div>