<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );
if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$timeformat = mailster( 'helper' )->timeformat();
$timeoffset = mailster( 'helper' )->gmt_offset( true );

?>

<?php if ( $editable ) : ?>
<table class="form-table">
		<tbody>

		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Subject', 'mailster' ); ?></th>
			<td>
				<div class="emoji-selector">
					<input type="text" class="widefat" value="<?php echo esc_attr( $this->post_data['subject'] ); ?>" name="mailster_data[subject]" id="mailster_subject" aria-label="<?php esc_attr_e( 'Subject', 'mailster' ); ?>">
					<button class="button emoji" data-input="mailster_subject">&#128578;</button>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Preheader', 'mailster' ); ?></th>
			<td>
				<div class="emoji-selector">
					<input type="text" class="widefat" value="<?php echo esc_attr( $this->post_data['preheader'] ); ?>" name="mailster_data[preheader]" id="mailster_preheader" aria-label="<?php esc_attr_e( 'Preheader', 'mailster' ); ?>">
					<button class="button emoji" data-input="mailster_preheader">&#128578;</button>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'From Name', 'mailster' ); ?> <a class="default-value mailster-icon" data-for="mailster_from-name" data-value="<?php echo esc_attr( mailster_option( 'from_name' ) ); ?>" title="<?php esc_attr_e( 'restore default', 'mailster' ); ?>"></a></th>
			<td>
				<div class="emoji-selector">
					<input type="text" class="widefat" value="<?php echo esc_attr( $this->post_data['from_name'] ); ?>" name="mailster_data[from_name]" id="mailster_from-name" aria-label="<?php esc_attr_e( 'From Name', 'mailster' ); ?>">
					<button class="button emoji" data-input="mailster_from-name">&#128578;</button>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'From Email', 'mailster' ); ?> <a class="default-value mailster-icon" data-for="mailster_from" data-value="<?php echo esc_attr( mailster_option( 'from' ) ); ?>" title="<?php esc_attr_e( 'restore default', 'mailster' ); ?>"></a></th>
			<td><input type="email" class="widefat" value="<?php echo esc_attr( $this->post_data['from_email'] ); ?>" name="mailster_data[from_email]" id="mailster_from" aria-label="<?php esc_attr_e( 'From Email', 'mailster' ); ?>"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Reply-to Email', 'mailster' ); ?> <a class="default-value mailster-icon" data-for="mailster_reply_to" data-value="<?php echo esc_attr( mailster_option( 'reply_to' ) ); ?>" title="<?php esc_attr_e( 'restore default', 'mailster' ); ?>"></a></th>
			<td><input type="email" class="widefat" value="<?php echo esc_attr( $this->post_data['reply_to'] ); ?>" name="mailster_data[reply_to]" id="mailster_reply_to" aria-label="<?php esc_attr_e( 'reply-to email', 'mailster' ); ?>"></td>
		</tr>
	 </tbody>
</table>
<input type="hidden" value="<?php echo esc_attr( $this->get_template() ); ?>" name="mailster_data[template]" id="mailster_template_name">
<input type="hidden" value="<?php echo esc_attr( $this->get_file() ); ?>" name="mailster_data[file]" id="mailster_template_file">


<?php else : ?>
	<?php
	$sent    = $this->get_sent( $post->ID );
	$totals  = 'autoresponder' != $post->post_status ? $this->get_totals( $post->ID ) : $sent;
	$deleted = $this->get_deleted( $post->ID );

	$errors = $this->get_errors( $post->ID );

	$opens        = $this->get_opens( $post->ID );
	$opens_total  = $this->get_opens( $post->ID, true );
	$clicks       = $this->get_clicks( $post->ID );
	$clicks_total = $this->get_clicks( $post->ID, true );
	$unsubscribes = $this->get_unsubscribes( $post->ID );
	$bounces      = $this->get_bounces( $post->ID );
	?>

<table>
	<tr><th width="16.666%"><?php esc_html_e( 'Subject', 'mailster' ); ?></th><td><strong><?php echo $this->post_data['subject']; ?></strong></td></tr>
	<?php if ( 'autoresponder' != $post->post_status ) : ?>
	<tr><th><?php esc_html_e( 'Date', 'mailster' ); ?></th><td>
		<?php echo date( $timeformat, $this->post_data['timestamp'] + $timeoffset ); ?>
		<?php
		if ( 'finished' == $post->post_status ) :
			echo ' &ndash; ' . date( $timeformat, $this->post_data['finished'] + $timeoffset );
			echo ' (' . sprintf( esc_html__( 'took %s', 'mailster' ), human_time_diff( $this->post_data['timestamp'], $this->post_data['finished'] ) ) . ')';
			endif;
		?>
	</td></tr>
	<?php endif; ?>
	<tr><th><?php esc_html_e( 'Preheader', 'mailster' ); ?></th><td><?php echo $this->post_data['preheader'] ? $this->post_data['preheader'] : '<span class="description">' . esc_html__( 'no preheader', 'mailster' ) . '</span>'; ?></td></tr>
</table>

<ul id="stats">
	<li class="receivers">
		<label class="recipients-limit"><span class="verybold hb-sent"><?php echo number_format_i18n( $sent ); ?></span> <?php echo ( 'autoresponder' == $post->post_status ) ? esc_html__( 'sent', 'mailster' ) : _nx( 'receiver', 'receivers', $sent, 'in pie chart', 'mailster' ); ?></label>
	</li>
	<?php if ( $this->post_data['track_opens'] ) : ?>
	<li>
		<div id="stats_open" class="piechart" data-percent="<?php echo $this->get_open_rate( $post->ID ) * 100; ?>"><span>0</span>%</div>
		<label class="show-open"><span class="verybold hb-opens"><?php echo number_format_i18n( $opens ); ?></span> <?php echo _nx( 'opened', 'opens', $opens, 'in pie chart', 'mailster' ); ?></label>
	</li>
	<?php endif; ?>
	<?php if ( $this->post_data['track_clicks'] ) : ?>
	<li>
		<div id="stats_click" class="piechart" data-percent="<?php echo $this->get_click_rate( $post->ID ) * 100; ?>"><span>0</span>%</div>
		<label class="show-click"><span class="verybold hb-clicks"><?php echo number_format_i18n( $clicks ); ?></span> <?php echo _nx( 'click', 'clicks', $clicks, 'in pie chart', 'mailster' ); ?></label>
	</li>
	<?php endif; ?>
	<li>
		<div id="stats_unsubscribes" class="piechart" data-percent="<?php echo $this->get_unsubscribe_rate( $post->ID ) * 100; ?>"><span>0</span>%</div>
		<label class="show-unsubscribes"><span class="verybold hb-unsubs"><?php echo number_format_i18n( $unsubscribes ); ?></span> <?php echo _nx( 'unsubscribe', 'unsubscribes', $unsubscribes, 'in pie chart', 'mailster' ); ?></label>
	</li>
	<li>
		<div id="stats_bounces" class="piechart" data-percent="<?php echo $this->get_bounce_rate( $post->ID ) * 100; ?>"><span>0</span>%</div>
		<label class="show-bounces"><span class="verybold hb-bounces"><?php echo number_format_i18n( $bounces ); ?></span> <?php echo _nx( 'bounce', 'bounces', $bounces, 'in pie chart', 'mailster' ); ?></label>
	</li>
</ul>
<table>

	<tr>
	<th><?php ( 'autoresponder' == $post->post_status ) ? esc_html_e( 'Total Sent', 'mailster' ) : esc_html_e( 'Total Recipients', 'mailster' ); ?></th>
	<td class="nopadding"> <span class="big hb-totals"><?php echo number_format_i18n( $totals ); ?></span>
	<?php
	if ( ! in_array( $post->post_status, array( 'finished', 'autoresponder' ) ) ) :
		echo '<span class="hb-sent">' . number_format_i18n( $sent ) . '</span> ' . esc_html__( 'sent', 'mailster' ) . '';
	endif;
	?>
	<?php
	if ( $deleted ) :
		echo '&ndash; <span class="hb-deleted">' . number_format_i18n( $deleted ) . '</span> ' . esc_html__( 'deleted', 'mailster' ) . '';
	endif;
	?>
	<?php if ( ! empty( $sent ) ) : ?>
		<a href="#" id="show_recipients" class="alignright mailster-icon showdetails"><?php esc_html_e( 'details', 'mailster' ); ?></a>
		<span class="spinner" id="recipients-ajax-loading"></span><div class="ajax-list" id="recipients-list"></div>
	<?php endif; ?>
	</td></tr>
	<?php if ( ! empty( $errors ) ) : ?>
	<tr><th><?php esc_html_e( 'Total Errors', 'mailster' ); ?></th><td class="nopadding"> <span class="big hb-errors"><?php echo number_format_i18n( $errors ); ?></span>
		<?php if ( ! empty( $errors ) ) : ?>
		<a href="#" id="show_errors" class="alignright mailster-icon showdetails"><?php esc_html_e( 'details', 'mailster' ); ?></a>
		<span class="spinner" id="error-ajax-loading"></span><div class="ajax-list" id="error-list"></div>
	<?php endif; ?>
	</td></tr>
	<?php endif; ?>
	<?php if ( $this->post_data['track_clicks'] ) : ?>
	<tr><th><?php esc_html_e( 'Total Clicks', 'mailster' ); ?></th><td class="nopadding"> <span class="big hb-clicks_total"><?php echo number_format_i18n( $clicks_total ); ?></span>
		<?php if ( ! empty( $clicks_total ) ) : ?>
		<a href="#" id="show_clicks" class="alignright mailster-icon showdetails"><?php esc_html_e( 'details', 'mailster' ); ?></a>
		<span class="spinner" id="clicks-ajax-loading"></span><div class="ajax-list" id="clicks-list"></div>
	<?php endif; ?>
	</td></tr>
<?php endif; ?>
	<?php
	if ( $environment = $this->get_environment( $post->ID ) ) :
		$types = array(
			'desktop' => esc_html__( 'Desktop', 'mailster' ),
			'mobile'  => esc_html__( 'Mobile', 'mailster' ),
			'webmail' => esc_html__( 'Web Client', 'mailster' ),
		);
		?>
	<tr class="environment"><th><?php esc_html_e( 'Environment', 'mailster' ); ?></th><td class="nopadding">
		<?php foreach ( $environment as $type => $data ) { ?>
		<label><span class="big"><span class="hb-<?php echo $type; ?>"><?php echo round( $data['percentage'] * 100, 2 ); ?>%</span> <span class="mailster-icon client-<?php echo $type; ?>"></span></span> <?php echo isset( $types[ $type ] ) ? $types[ $type ] : esc_html__( 'unknown', 'mailster' ); ?></label>
		<?php } ?>
		<a href="#" id="show_environment" class="alignright mailster-icon showdetails"><?php esc_html_e( 'details', 'mailster' ); ?></a>
		<span class="spinner" id="environment-ajax-loading"></span><div class="ajax-list" id="environment-list"></div>
	</td></tr>
	<?php endif; ?>
	<?php
	if ( $geo_data = $this->get_geo_data( $post->ID ) ) :

		$unknown_cities = array();
		$countrycodes   = array();

		foreach ( $geo_data as $countrycode => $data ) {
			$x = wp_list_pluck( $data, 3 );
			if ( $x ) {
				$countrycodes[ $countrycode ] = array_sum( $x );
			}

			if ( $data[0][3] ) {
				$unknown_cities[ $countrycode ] = $data[0][3];
			}
		}

		arsort( $countrycodes );
		$total = array_sum( $countrycodes );
		?>
	<tr class="geolocation"><th><?php esc_html_e( 'Geo Location', 'mailster' ); ?></th><td class="nopadding">
	<span class="hb-geo_location">
		<?php
		$i = 0;
		foreach ( $countrycodes as $countrycode => $count ) {
			?>
			<label title="<?php echo mailster( 'geo' )->code2Country( $countrycode ); ?>"><span class="big"><span class="mailster-flag-24 flag-<?php echo strtolower( $countrycode ); ?>"></span> <?php echo round( $count / $opens * 100, 2 ); ?>%</span></label>
			<?php
			if ( ++$i >= 5 ) {
				break;
			}
		}
		?>
		</span>
		<a href="#" id="show_geolocation" class="alignright mailster-icon showdetails"><?php esc_html_e( 'details', 'mailster' ); ?></a>
		<span class="spinner" id="geolocation-ajax-loading"></span>
		</td></tr><tr><td colspan="2" class="nopadding">
		<div class="ajax-list countries" id="geolocation-list"></div>
	</td></tr>

	<?php endif; ?>

</table>

<br class="clear">
<?php endif; ?>
<input type="hidden" value="<?php echo ! $editable; ?>" id="mailster_disabled" readonly>
