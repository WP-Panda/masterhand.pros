<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

global $wpdb, $option_for_project;

//
$sql  = "select NOW() from wp_posts p where p.id =1";
$time = $wpdb->get_col( $sql );
file_put_contents( __DIR__ . '/cron.txt', "\r\n" . 'time-' . json_encode( $time, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ), FILE_APPEND );
//

$sql            = "SELECT p.id, p.user_id, p.status_id, u.user_email, s.status_name
        FROM {$wpdb->prefix}pro_paid_users AS p
        LEFT JOIN {$wpdb->prefix}users u ON u.ID=p.user_id
        LEFT JOIN wp_pro_status s ON s.id=p.status_id
        WHERE p.expired_date < NOW() AND p.status_id NOT IN (" . PRO_BASIC_STATUS_FREELANCER . ", " . PRO_BASIC_STATUS_EMPLOYER . ")";
$users_pro_paid = $wpdb->get_results( $sql, ARRAY_A );

if ( ! empty( $users_pro_paid ) ) {
	$mailing = AE_Mailing::get_instance();
	$subject = 'Your PRO plan at Masterhand PRO has been expired';

	foreach ( $users_pro_paid as $pro_paid_id ) {
		if ( ae_user_role( $pro_paid_id['user_id'] ) == FREELANCER ) {
			$wpdb->update( "{$wpdb->prefix}pro_paid_users", [ 'status_id' => PRO_BASIC_STATUS_FREELANCER ], [ 'id' => $pro_paid_id['id'] ] );
		} else {
			$wpdb->update( "{$wpdb->prefix}pro_paid_users", [ 'status_id' => PRO_BASIC_STATUS_EMPLOYER ], [ 'id' => $pro_paid_id['id'] ] );
		}

		$message = ae_get_option( 'expired_pro_status_template' );
		$message = str_replace( '[status_data]', $pro_paid_id['status_name'], $message );

		$res = $mailing->wp_mail( $pro_paid_id['user_email'], $subject, $message, [ 'user_id' => $pro_paid_id['user_id'] ] );
		//         file_put_contents(__DIR__ . '/cron.txt', "\r\n" . '$res send-' . $pro_paid_id['user_email'] . '-' . json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), FILE_APPEND);

		if ( empty( $res ) ) {
			file_put_contents( __DIR__ . '/cron.txt', "\r\n" . 'er send email-' . $pro_paid_id['user_email'] . ' by user id=' . $pro_paid_id['user_id'], FILE_APPEND );
		}
	}
}

foreach ( $option_for_project as $item ) {
	$sql      = "select p.ID from {$wpdb->posts} p
                    left join {$wpdb->postmeta} pm on p.ID=pm.post_id and pm.meta_key = 'et_{$item}'
                    where ( pm.meta_value < NOW())";
	$id_posts = $wpdb->get_col( $sql );
	foreach ( $id_posts as $value ) {
		delete_post_meta( $value, $item );
		delete_post_meta( $value, 'et_' . $item );
	}
	file_put_contents( __DIR__ . '/cron.txt', "\r\n" . 'sql-' . $item . '-' . json_encode( $id_posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ), FILE_APPEND );
}
