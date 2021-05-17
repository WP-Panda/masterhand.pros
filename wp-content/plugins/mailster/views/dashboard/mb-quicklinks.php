<?php

$autoresponder = count( mailster_get_autoresponder_campaigns() );

$campaigns = count( mailster_get_campaigns() ) - $autoresponder;

$subscribers = mailster( 'subscribers' )->get_totals( 1 );

$lists = count( mailster( 'lists' )->get() );

$forms = count( mailster( 'forms' )->get_all() );

?>
<dl class="mailster-icon mailster-icon-newsletter-outline">
	<dt><a href="edit.php?post_type=newsletter"><?php esc_html_e( 'Campaigns', 'mailster' ); ?></a></dt>
	<dd><span class="version">
		<?php echo number_format_i18n( $campaigns ) . ' ' . esc_html__( _nx( 'Campaign', 'Campaigns', $campaigns, 'number of', 'mailster' ) ); ?>
		<?php echo $autoresponder ? ', ' . number_format_i18n( $autoresponder ) . ' ' . esc_html__( _nx( 'Autoresponder', 'Autoresponders', $autoresponder, 'number of', 'mailster' ) ) : ''; ?></span>
	</dd>
	<dd>
		<a href="edit.php?post_type=newsletter"><?php esc_html_e( 'View', 'mailster' ); ?></a> |
		<a href="post-new.php?post_type=newsletter"><?php esc_html_e( 'Create Campaign', 'mailster' ); ?></a> |
		<a href="post-new.php?post_type=newsletter&post_status=autoresponder"><?php esc_html_e( 'Create Autoresponder', 'mailster' ); ?></a>
	</dd>
</dl>
<dl class="mailster-icon mailster-icon-users">
	<dt><a href="edit.php?post_type=newsletter&page=mailster_subscribers"><?php esc_html_e( 'Subscribers', 'mailster' ); ?></a></dt>
	<dd><span class="version"><?php echo number_format_i18n( $subscribers ) . ' ' . esc_html__( _nx( 'Subscriber', 'Subscribers', $subscribers, 'number of', 'mailster' ) ); ?></span></dd>
	<dd>
		<a href="edit.php?post_type=newsletter&page=mailster_subscribers"><?php esc_html_e( 'View', 'mailster' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=import"><?php esc_html_e( 'Import', 'mailster' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=export"><?php esc_html_e( 'Export', 'mailster' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=mailster_subscribers&new"><?php esc_html_e( 'Add Subscriber', 'mailster' ); ?></a>
	</dd>
</dl>
<dl class="mailster-icon mailster-icon-list">
	<dt><a href="edit.php?post_type=newsletter&page=mailster_lists"><?php esc_html_e( 'Lists', 'mailster' ); ?></a></dt>
	<dd><span class="version"><?php echo number_format_i18n( $lists ) . ' ' . esc_html__( _nx( 'List', 'Lists', $lists, 'number of', 'mailster' ) ); ?></span></dd>
	<dd>
		<a href="edit.php?post_type=newsletter&page=mailster_lists"><?php esc_html_e( 'View', 'mailster' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=mailster_lists&new"><?php esc_html_e( 'Add List', 'mailster' ); ?></a>
	</dd>
</dl>
<dl class="mailster-icon mailster-icon-forms">
	<dt><a href="edit.php?post_type=newsletter&page=mailster_forms"><?php esc_html_e( 'Forms', 'mailster' ); ?></a></dt>
	<dd><span class="version"><?php echo number_format_i18n( $forms ) . ' ' . esc_html__( _nx( 'Form', 'Forms', $forms, 'number of', 'mailster' ) ); ?></span></dd>
	<dd>
		<a href="edit.php?post_type=newsletter&page=mailster_forms"><?php esc_html_e( 'View', 'mailster' ); ?></a> |
		<a href="edit.php?post_type=newsletter&page=mailster_forms&new"><?php esc_html_e( 'Add Form', 'mailster' ); ?></a>
	</dd>
</dl>
