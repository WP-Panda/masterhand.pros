<?php
	//Include the database configuration file
	include 'dbConfig.php';
	header( 'Content-type: text/html; charset=utf-8' );

	if ( ! empty( $_POST[ "country_id2" ] ) ) {

		$members = get_users( [
			'meta_key'   => 'country',
			'meta_value' => $_POST[ "country_id2" ]
		] );

		if ( $members != '' ) {

			foreach ( $members as $member ) {
				echo '<option value="user_' . $member->ID . '">' . $member->user_login . '</option>';
			}

		} else {
			echo '<option value=" ">No authors</option>';
		}

	}