<?php
	require_once( str_replace( '//', '/', dirname( __FILE__ ) . '/' ) . '../../../wp-config.php' );
	global $wpdb;

	$secret = 'xpCbBoXR40Y4rORgIjRGVoK4an6lvP20mmrlxN4hOTGtm2mXwOJywCPY3dsIxMcKHlotRF';

	$request_secret = $_GET[ 'data' ];

	if ( $secret != $request_secret ) {
		echo 'not working';
	} else {
		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-Disposition: attachment; filename=unsubscribe_users.xls" );
		$result = $wpdb->get_results( "SELECT * FROM wp_unsubscribe_users" );
		echo "<table><tr><th>Email</th><th>Answer</th><th>Date</th></tr><tr>";
		foreach ( $result as $value ) {

			echo '<tr><td>' . $value->user_email . '</td>';
			echo '<td>' . $value->user_answer . '</td>';
			echo '<td>' . $value->date . '</td></tr>';

		}
		echo "</table>";
	}