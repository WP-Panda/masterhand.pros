<?php
/**
 * The template for displaying the filter for cases.
 * This template can be overridden by copying it to yourtheme/marketengine/resolution/cases-filter.php.
 *
 * @author 		EngineThemes
 * @package 	MarketEngine/Templates
 * @version     1.0.0
 * @since 		1.0.1
 */
?>
<!-- mobile -->
<div class="me-resolution-tab-filter">
	<span class="me-switch-tab-filter-1"><?php _e('Filter', 'enginethemes'); ?></span>
	<span class="me-switch-tab-filter-2"><?php _e('Filter list', 'enginethemes'); ?></span>
</div>
<!--//mobile-->
<div class="me-resolution-filter">
	<form id="me-resolution-filter-form" action="<?php marketengine_dispute_case_filter_form_action(); ?>">
 		<div class="me-row">
			<div class="me-col-md-5">
				<div class="me-row">
					<div class="me-col-md-5 me-col-sm-6">
						<div class="me-status-filter">
							<label><?php _e('Status','enginethemes'); ?></label>
							<?php marketengine_get_template('resolution/cases/case-status-list'); ?>
						</div>
					</div>
					<div class="me-col-md-7 me-col-sm-6">
						<div class="me-open-date-filter">
							<label><?php _e('Open date', 'enginethemes'); ?></label>
							<div class="me-resolution-pick">
								<input class="me-pick-date" id="me-pick-date-1" type="text" placeholder="<?php _e('Form date', 'enginethemes'); ?>" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>">
								<input class="me-pick-date" id="me-pick-date-2" type="text" placeholder="<?php _e('To date', 'enginethemes'); ?>" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="me-col-md-7">
				
				<div class="me-keyword-filter">
					<label><?php _e('Keyword', 'enginethemes'); ?></label>
					<div class="me-resolution-search">
						<input type="text" placeholder="<?php _e('Case number, related party', 'enginethemes'); ?>" name="keyword" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>">
						<div class="me-resolution-clear-filter">
							<a href="<?php echo marketengine_resolution_center_url() ; ?>"><?php _e('Clear Filter', 'enginethemes'); ?></a>
							<input class="me-resolution-filter-btn" type="submit" value="<?php _e('FILTER', 'enginethemes'); ?>">
						</div>
					</div>
				</div>
					
			</div>
		</div>
	</form>
</div>