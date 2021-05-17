<?php

if ( $subscribers = mailster( 'subscribers' )->get_totals( 1 ) ) : ?>
<div class="mailster-mb-subscribers mailster-loading">
	<div class="mailster-mb-heading">
		<select class="mailster-mb-select" id="mailster-subscriber-range">
			<option value="7 days"><?php esc_html_e( '7 days', 'mailster' ); ?></option>
			<option value="30 days"><?php esc_html_e( '30 days', 'mailster' ); ?></option>
			<option value="3 month"><?php esc_html_e( '3 month', 'mailster' ); ?></option>
			<option value="1 year"><?php esc_html_e( '1 year', 'mailster' ); ?></option>
		</select>
		<span class="alignright"><?php esc_html_e( 'Subscriber Grows', 'mailster' ); ?>:</span>
	<?php if ( ! $this->is_dashboard ) : ?>
		<?php printf( esc_html__( 'You have %s', 'mailster' ), '<a class="mailster-subscribers" href="edit.php?post_type=newsletter&page=mailster_subscribers&status=1">' . number_format_i18n( $subscribers ) . ' ' . esc_html__( _nx( 'Subscriber', 'Subscribers', $subscribers, 'number of', 'mailster' ) ) . '</a>' ); ?>
	<?php endif; ?>
	</div>
	<div class="mailster-db-wrap">
		<div id="subscriber-chart-wrap">
			<canvas class="subscriber-charts" id="subscriber-chart"></canvas>
		</div>
	</div>
</div>

	<?php if ( ! $this->is_dashboard ) : ?>
<p class="alignright">
	<a href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=import"><?php esc_html_e( 'Import', 'mailster' ); ?></a>,
	<a href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=export"><?php esc_html_e( 'Export', 'mailster' ); ?></a>
		<?php esc_html_e( 'or', 'mailster' ); ?>
	<a class="button button-primary" href="edit.php?post_type=newsletter&page=mailster_subscribers&new"><?php esc_html_e( 'Add Subscriber', 'mailster' ); ?></a>
</p>
	<?php endif; ?>

<?php else : ?>
<div class="mailster-welcome-panel">
		<h4><?php esc_html_e( 'You have no subscribers yet!', 'mailster' ); ?></h4>
		<ul>
			<li><a href="edit.php?post_type=newsletter&page=mailster_subscribers&new"><?php esc_html_e( 'Add a single Subscriber', 'mailster' ); ?></a></li>
			<li><a href="edit.php?post_type=newsletter&page=mailster_manage_subscribers"><?php esc_html_e( 'Import your existing Subscribers', 'mailster' ); ?></a></li>
			<li><a href="edit.php?post_type=newsletter&page=mailster_forms&new"><?php esc_html_e( 'Create a Form to engage new Subscribers', 'mailster' ); ?></a></li>
		</ul>
	</div>
<?php endif; ?>
