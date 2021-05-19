<?php
	//Include the database configuration file
	include 'dbConfig.php';
	header( 'Content-type: text/html; charset=utf-8' );
	if ( ! empty( $_POST[ "country_id2" ] ) ) {
		$vv = $_POST[ "country_id2" ];

		if ( $vv != '' ) {
			$blogusers = get_users( [ 'meta_key' => 'country' ] );
			foreach ( $blogusers as $user ) {
				echo '<option>' . esc_html( $user->user_email ) . '</option>';
			}
			//echo '<option value="'. $vv .'">'. $vv .'</option>';
		} else {
			echo '<option value=" ">No authors</option>';
		}
	} else {
		echo '<option value=" ">No authors2</option>';
	}
