<?php

if ( $lists = mailster( 'lists' )->get() ) : ?>
<div class="mailster-mb-lists mailster-loading">
	<div class="mailster-mb-heading">
		<select class="mailster-mb-select">
		<?php foreach ( $lists as $list ) : ?>
			<option value="<?php echo (int) $list->ID; ?>"><?php echo esc_html( $list->name ); ?></option>
		<?php endforeach; ?>
		</select>
		<span class="mailster-mb-label"><?php esc_html_e( 'List', 'mailster' ); ?>:</span> <a class="mailster-mb-link" href="edit.php?post_type=newsletter&page=mailster_lists&ID=%d" title="<?php esc_attr_e( 'edit', 'mailster' ); ?>"><?php echo esc_html( $list->name ); ?></a>
	</div>
	<div class="mailster-mb-stats">
		<ul class="campaign-charts">
			<li><div class="stats-total"></div></li>
			<li><div class="stats-open piechart" data-percent="0"><span>0</span>%</div></li>
			<li><div class="stats-clicks piechart" data-percent="0"><span>0</span>%</div></li>
			<li><div class="stats-unsubscribes piechart" data-percent="0"><span>0</span>%</div></li>
			<li><div class="stats-bounces piechart" data-percent="0"><span>0</span>%</div></li>
		</ul>
		<ul class="labels">
			<li><label><?php echo esc_html_x( 'total', 'in pie chart', 'mailster' ); ?></label></li>
			<li><label><?php echo esc_html_x( 'opens', 'in pie chart', 'mailster' ); ?></label></li>
			<li><label><?php echo esc_html_x( 'clicks', 'in pie chart', 'mailster' ); ?></label></li>
			<li><label><?php echo esc_html_x( 'unsubscribes', 'in pie chart', 'mailster' ); ?></label></li>
			<li><label><?php echo esc_html_x( 'bounces', 'in pie chart', 'mailster' ); ?></label></li>
		</ul>
	</div>
		<span class="loader"></span>
</div>
<?php else : ?>

<div class="mailster-welcome-panel">
	<h4><?php esc_html_e( 'Sorry, no Lists found!', 'mailster' ); ?></h4>
	<ul>
		<li><a href="edit.php?post_type=newsletter&page=mailster_lists&new"><?php esc_html_e( 'Create a new List', 'mailster' ); ?></a></li>
	</ul>
</div>
<?php endif; ?>

<p class="alignright">
<a class="button button-primary" href="edit.php?post_type=newsletter&page=mailster_lists&new"><?php esc_html_e( 'Create List', 'mailster' ); ?></a>
</p>
