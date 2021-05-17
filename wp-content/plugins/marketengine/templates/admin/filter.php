<?php
$quants = array(
	'day' => __( "Day" , "enginethemes" ),
	'week' => __( "Week" , "enginethemes" ),
	'month' => __( "Month" , "enginethemes" ),
	'quarter' => __( "Quarter" , "enginethemes" ),
	'year' => __( "Year" , "enginethemes" ),
);
$selected_quant = !empty($_REQUEST['quant']) ? esc_attr( $_REQUEST['quant'] ) : 'day';
$nonce = wp_create_nonce( 'me-export' );
?>
<style type="text/css">
	.me-table-rhead .me-table-col:hover a.me-sort-asc:before, 
	.me-table-rhead .me-table-col:hover a.me-sort-desc:after {
		display: none;
	}

	.me-table-rhead .me-table-col:hover a.me-sort-asc:after, 
	.me-table-rhead .me-table-col:hover a.me-sort-desc:before {
		display: block;
	}
</style>

<div class="me-report-filter">
	<span class="me-pick-date-box">
		<form action="" method="get">
			<input name="page" value="me-reports" type="hidden" />
			<input name="tab" value="<?php echo empty($_REQUEST['tab']) ? 'listing' : esc_attr( $_REQUEST['tab'] ); ?>" type="hidden" />
			<input name="section" value="<?php echo empty($_REQUEST['section']) ? '' : esc_attr( $_REQUEST['section'] ); ?>" type="hidden" />
			<span class="me-group-pick-date">
				<span class="me-report-start-date"><?php _e("From", "enginethemes"); ?></span>
				<span class="me-pick-date">
					<input id="me-pick-date-1" type="text" name="from_date" value="<?php echo empty($_REQUEST['from_date']) ? '' : $_REQUEST['from_date']; ?>">
				</span>
			</span>
			<span class="me-group-pick-date">
				<span class="me-report-end-date"><?php _e("To", "enginethemes"); ?></span>
				<span class="me-pick-date">
					<input id="me-pick-date-2" type="text" name="to_date" value="<?php echo empty($_REQUEST['to_date']) ? '' : $_REQUEST['to_date']; ?>">
				</span>
			</span>
			
			<span class="me-group-pick-date">
				<span class="me-report-quantity"><?php _e("Quantity", "enginethemes"); ?></span>
				<span class="me-quantity-day">
					<select name="quant" >
						<?php foreach ($quants as $key => $quant) : ?>
							<option value="<?php echo $key ?>" <?php  selected( $selected_quant, $key ) ?>><?php echo $quant; ?></option>
						<?php endforeach; ?>
					</select>
				</span>
			</span>
			<input type="submit" class="me-report-submit-btn" value="<?php _e("Run Report", "enginethemes"); ?>">
		</form>
	</span>
	<span class="me-export-report">
		<a href="<?php echo add_query_arg( array('export' => 'csv', '_wpnonce' => $nonce)); ?>" target="_blank"><?php _e("Export", "enginethemes"); ?></a>
	</span>
</div>