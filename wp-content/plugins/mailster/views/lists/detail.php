<?php

$id = isset( $_GET['ID'] ) ? (int) $_GET['ID'] : null;

$is_new = isset( $_GET['new'] );

if ( ! $is_new ) {
	if ( ! ( $list = $this->get( $id, null, true ) ) ) {
		echo '<h2>' . esc_html__( 'This list does not exist or has been deleted!', 'mailster' ) . '</h2>';
		return;
	}
} else {

	if ( ! current_user_can( 'mailster_add_subscribers' ) ) {
		echo '<h2>' . esc_html__( 'You don\'t have the right permission to add new lists', 'mailster' ) . '</h2>';
		return;
	}

	$list = $this->get_empty();
	if ( isset( $_POST['mailster_data'] ) ) {

		$list = (object) wp_parse_args( $_POST['mailster_data'], (array) $list );

	}
}

$timeformat = mailster( 'helper' )->timeformat();
$timeoffset = mailster( 'helper' )->gmt_offset( true );

$now = time();

$tabindex = 1;

?>
<div class="wrap<?php echo( $is_new ) ? ' new' : ''; ?>">
<form id="subscriber_form" action="edit.php?post_type=newsletter&page=mailster_lists<?php echo ( $is_new ) ? '&new' : '&ID=' . $id; ?>" method="post">
<input type="hidden" id="ID" name="mailster_data[ID]" value="<?php echo $list->ID; ?>">
<?php wp_nonce_field( 'mailster_nonce' ); ?>
<div style="height:0px; width:0px; overflow:hidden;"><input type="submit" name="save" value="1"></div>
<h1>
<?php
if ( $is_new ) :
	esc_html_e( 'Add new List', 'mailster' );
else :
	if ( $list->parent_id && $parent = $this->get( $list->parent_id ) ) {
		echo '<div class="parent_list"><strong><a href="edit.php?post_type=newsletter&page=mailster_lists&ID=' . $parent->ID . '">' . $parent->name . '</a></strong> &rsaquo; </div>';
	}
	printf( esc_html__( 'Edit List %s', 'mailster' ), '<strong>' . $list->name . '</strong>' );
	?>
	<?php if ( current_user_can( 'mailster_add_subscribers' ) ) : ?>
		<a href="edit.php?post_type=newsletter&page=mailster_lists&new" class="page-title-action"><?php esc_html_e( 'Add New', 'mailster' ); ?></a>
	<?php endif; ?>

<?php endif; ?>
<span class="alignright">
	<?php if ( ! $is_new && current_user_can( 'mailster_delete_lists' ) ) : ?>
		<input type="submit" name="delete" class="button button-link-delete" value="<?php esc_attr_e( 'Delete List', 'mailster' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this list?', 'mailster' ); ?>');">
	<?php endif; ?>
	<?php if ( ! $is_new && current_user_can( 'mailster_delete_lists' ) && current_user_can( 'mailster_delete_subscribers' ) ) : ?>
		<input type="submit" name="delete_subscribers" class="button button-link-delete" value="<?php esc_attr_e( 'Delete List with Subscribers', 'mailster' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this list with all subscribers?', 'mailster' ); ?>');">
	<?php endif; ?>
	<input type="submit" name="save" class="button button-primary button-large" value="<?php esc_attr_e( 'Save', 'mailster' ); ?>">
</span>
</h1>
<table class="form-table">
	<tr>
		<th scope="row"><h3><?php esc_html_e( 'Name', 'mailster' ); ?></h3></th>
		<td>
			<h3 class="detail">
				<ul class="click-to-edit">
					<li><?php echo esc_attr( $list->name ); ?>&nbsp;</li>
					<li><input id="name" class="widefat" type="text" name="mailster_data[name]" value="<?php echo esc_attr( $list->name ); ?>" placeholder="<?php esc_attr_e( 'Name of the List', 'mailster' ); ?>" autofocus></li>
				</ul>
			</h3>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Description', 'mailster' ); ?></th>
		<td>
			<div class="detail">
				<ul class="click-to-edit">
					<li><?php echo $list->description ? esc_attr( $list->description ) : '<span class="description">' . esc_html__( 'no description', 'mailster' ) . '</span>'; ?></li>
					<li><textarea id="description" class="widefat" type="text" name="mailster_data[description]"><?php echo esc_textarea( $list->description ); ?></textarea></li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Subscribers', 'mailster' ); ?></th>
		<td>
			<?php echo '<a href="' . add_query_arg( array( 'lists' => array( $list->ID ) ), 'edit.php?post_type=newsletter&page=mailster_subscribers' ) . '">' . sprintf( esc_html__( _n( '%s Subscriber', '%s Subscribers', $list->subscribers, 'mailster' ) ), '<strong>' . number_format_i18n( $list->subscribers ) . '</strong>' ) . '</a>'; ?>
		</td>
	</tr>
</table>

<?php
if ( ! $is_new ) :

	$actions = mailster( 'actions' )->get_by_list( $list->ID, null, true );

	$sent         = $actions['sent'];
	$opens        = $actions['opens'];
	$clicks       = $actions['clicks'];
	$unsubscribes = $actions['unsubscribes'];
	$bounces      = $actions['bounces'];

	$openrate        = ( $sent ) ? $opens / $sent * 100 : 0;
	$clickrate       = ( $opens ) ? $clicks / $opens * 100 : 0;
	$unsubscriberate = ( $opens ) ? $unsubscribes / $opens * 100 : 0;
	$bouncerate      = ( $sent ) ? $bounces / $sent * 100 : 0;

	?>
		<div class="stats-wrap">
			<table id="stats">
				<tr>
				<td><span class="verybold"><?php echo number_format_i18n( $sent ); ?></span> <?php echo esc_html__( _n( 'Mail sent', 'Mails sent', $sent, 'mailster' ) ); ?></td>
				<td width="60">
					<div id="stats_open" class="piechart" data-percent="<?php echo $openrate; ?>"><span>0</span>%</div>
				</td><td><span class="verybold"></span> <?php esc_html_e( 'open rate', 'mailster' ); ?></td>
				<td width="60">
					<div id="stats_click" class="piechart" data-percent="<?php echo $clickrate; ?>"><span>0</span>%</div>
				</td><td><span class="verybold"></span> <?php esc_html_e( 'click rate', 'mailster' ); ?></td>
				<td width="60">
					<div id="stats_unsub" class="piechart" data-percent="<?php echo $unsubscriberate; ?>"><span>0</span>%</div>
				</td><td><span class="verybold"></span> <?php esc_html_e( 'unsubscribe rate', 'mailster' ); ?></td>
				<td width="60">
					<div id="stats_bounce" class="piechart" data-percent="<?php echo $bouncerate; ?>"><span>0</span>%</div>
				</td><td><span class="verybold"></span> <?php esc_html_e( 'bounce rate', 'mailster' ); ?></td>
				</tr>
			</table>
		</div>

		<div class="activity-wrap">
			<?php if ( $activities = $this->get_activity( $list->ID ) ) : ?>

				<h3><?php esc_html_e( 'Activity', 'mailster' ); ?></h3>

				<table class="wp-list-table widefat">
					<thead>
						<tr><th><?php esc_html_e( 'Date', 'mailster' ); ?></th><th></th><th><?php esc_html_e( 'Action', 'mailster' ); ?></th><th><?php esc_html_e( 'Campaign', 'mailster' ); ?></th><th></th></tr>
					</thead>
					<tbody>
				<?php foreach ( $activities as $i => $activity ) : ?>
						<tr class="<?php echo ! ( $i % 2 ) ? ' alternate' : ''; ?>">
							<td><?php echo $now - $activity->timestamp < 3600 ? sprintf( esc_html__( '%s ago', 'mailster' ), human_time_diff( $now, $activity->timestamp ) ) : date( $timeformat, $activity->timestamp + $timeoffset ); ?></td>
							<td>
							<?php
							switch ( $activity->type ) {
								case 1:
									echo '<span class="mailster-icon mailster-icon-progress"></span></td><td>';
									printf( esc_html__( 'Campaign %s has start sending', 'mailster' ), '<a href="' . admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) . '">' . $activity->campaign_title . '</a>' );
									break;
								case 2:
										echo '<span class="mailster-icon mailster-icon-open"></span></td><td>';
										printf( esc_html__( 'First open in Campaign %s', 'mailster' ), '<a href="' . admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) . '">' . $activity->campaign_title . '</a>' );
									break;
								case 3:
										echo '<span class="mailster-icon mailster-icon-click"></span></td><td>';
										printf( esc_html__( '%1$s in Campaign %2$s clicked', 'mailster' ), '<a href="' . $activity->link . '">' . esc_html__( 'Link', 'mailster' ) . '</a>', '<a href="' . admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ) . '">' . $activity->campaign_title . '</a>' );
									break;
								case 4:
										echo '<span class="mailster-icon mailster-icon-unsubscribe"></span></td><td>';
										echo esc_html__( 'First subscription canceled', 'mailster' );
									break;
								case 5:
										echo '<span class="mailster-icon mailster-icon-bounce"></span></td><td>';
										printf( esc_html__( 'Soft bounce (%d tries)', 'mailster' ), $activity->count );
									break;
								case 6:
										echo '<span class="mailster-icon mailster-icon-bounce hard"></span></td><td>';
										echo esc_html__( 'Hard bounce', 'mailster' );
									break;
								default:
										echo '</td><td>';
									break;
							}
							?>

							</td>
							<td><a href="<?php echo admin_url( 'post.php?post=' . $activity->campaign_id . '&action=edit' ); ?>"><?php echo $activity->campaign_title; ?></a></td>
							<td width="50%">
							<?php if ( $activity->link ) : ?>
								<a href="<?php echo esc_url( $activity->link ); ?>"><?php echo esc_url( $activity->link ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
			<?php endforeach; ?>
				</tbody>
			</table>
<?php else : ?>
		<p class="description"><?php esc_html_e( 'no activity yet', 'mailster' ); ?></p>
<?php endif; ?>
	</div>
<?php endif; ?>
</form>
</div>
