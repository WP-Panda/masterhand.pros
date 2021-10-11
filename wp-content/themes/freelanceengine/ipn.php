<?php
/**
 * ТУТ НАДО РАЗБИРАТЬСЯ!!!!
 */
//$data = file_get_contents('php://input');
//file_put_contents(__DIR__ . '/ipn.log', json_encode([
//    'method' => $_SERVER['REQUEST_METHOD'],
//    'data' => $data
//    ], JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

if ( empty( $_POST ) ) {
	die( 'Bad Request!!!' );
}

$path = $_SERVER['DOCUMENT_ROOT'];
include_once $path . '/wp-load.php';
include_once $path . '/vendor/autoload.php';

$escrow_paypal = ae_get_option( 'escrow_paypal' );
$paypalemail   = $escrow_paypal['business_mail']; // Email продавца

$adminemail = "sp3180404@gmail.com";  // e-mail  администратора

$tablename = $wpdb->prefix . 'pro_paid_users';

$ipnPayPal = new \PayPal\PayPalIPN();
$ipnPayPal->usePHPCerts();

if ( (int) ae_get_option( 'test_mode' ) == 1 ) {
	$ipnPayPal->useSandbox();
}


$data = $_POST;
//file_put_contents(__DIR__ . '/ipn_POST.log', json_encode(['$_POST' => $data], JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);
//var_dump($data);

if ( $ipnPayPal->verifyIPN() ) {

	$payment_status = $data["payment_status"];
	$mc_gross       = $data["mc_gross"];
	$mc_currency    = $data['mc_currency'];
	$txn_id         = $data['txn_id'];
	$txn_type       = $data["txn_type"];
	$user_id        = intval( $data['item_number'] );
	$receiver_email = $data['receiver_email'];
	$payer_email    = $data['payer_email'];
	$status_order   = $data['custom']; // string 8_10
	$first_name     = $data['first_name'];
	$last_name      = $data['last_name'];

	if ( strtolower( $payment_status ) != "completed" ) {
		file_put_contents( __DIR__ . '/ipn.log', "\n payment_status: $payment_status\n", FILE_APPEND );
		exit;
	}


	// check that txn_id has not been previously processed

	/******** убедимся в том, что эта транзакция не была обработана ранее ********/

	$duplicate = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}pro_paid_users WHERE txn_id = '{$wpdb->_escape($txn_id)}'" );

	if ( $duplicate ) {
		file_put_contents( __DIR__ . '/ipn.log', "\n duplicate txn_id: $txn_id\n", FILE_APPEND );
		exit;
	}

	/********
	 * // check that receiver_email is your Primary PayPal email
	 * // check that payment_currency are correct
	 * /******** проверяем получателя платежа и тип транзакции, и выходим, если не наш аккаунт в $paypalemail - наш  primary e-mail, поэтому проверяем receiver_email      ********/

	if ( $receiver_email != $paypalemail || $txn_type != "web_accept" || $mc_currency != 'USD' ) {

		file_put_contents( __DIR__ . '/ipn.log', "\n check: $receiver_email != $paypalemail || $txn_type != 'web_accept' || $mc_currency \n", FILE_APPEND );
		exit;
	}

	/******** проверяем есть ли такой юзер у нас в базе ********/
	$user = $wpdb->get_row( 'SELECT id FROM ' . $wpdb->prefix . 'users WHERE id = ' . $user_id );

	if ( ! $user ) {
		file_put_contents( __DIR__ . '/ipn.log', "\n check user \n", FILE_APPEND );
		exit;
	}

	/******** получаем id статуса и время order duration ********/
	$status_order_exp = explode( '_', $status_order );

	$status_order_id    = $status_order_exp[0];
	$status_property_id = $status_order_exp[1];

	// Проверяем есть ли такой статус
	$status_id = $wpdb->get_row( 'SELECT id FROM ' . $wpdb->prefix . 'pro_status WHERE id = ' . $status_order_id );

	if ( ! $status_id ) {

		file_put_contents( __DIR__ . '/ipn.log', "\n check status_id: $status_id != $status_order_id \n", FILE_APPEND );
		exit;
	}

	// Проверяем есть ли такое время
	$option_property = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'pro_options WHERE property_id = ' . $status_property_id );

	if ( ! $option_property ) {

		file_put_contents( __DIR__ . '/ipn.log', "\n check option_property: $option_property != $status_property_id \n", FILE_APPEND );
		exit;
	}

	$order_duration = $option_property->option_value;

	// check that payment_amount are correct
	$get_values = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'pro_values WHERE status_id = ' . $status_order_id . ' AND property_id = ' . $status_property_id );

	if ( ! $get_values ) {
		file_put_contents( __DIR__ . '/ipn.log', "\n check get_values: $get_values\n", FILE_APPEND );
		exit;
	}

	if ( $mc_gross != number_format( (float) $get_values->property_value, 2, '.', '' ) ) {
		file_put_contents( __DIR__ . '/ipn.log', "\n check mc_gross: $mc_gross\n", FILE_APPEND );
		exit;
	}

	// Если ошибок нет, пишем данные в базу
	// Если в таблице есть юзер с таким id, то обновляем строку
	// Увеличиваем текущую дату на срок купленного статуса

	$expired_date = ( new DateTime() )->add( new DateInterval( 'P' . $order_duration . 'M' ) )->format( 'Y-m-d h:i:s' );

	$duplicate_user = $wpdb->get_row( 'SELECT id FROM ' . $wpdb->prefix . 'pro_paid_users WHERE user_id = ' . $user_id );

	if ( $duplicate_user ) {
		$result = $wpdb->update( $tablename, [
			'txn_id'          => $txn_id,
			'user_id'         => $user_id,
			'first_name'      => $first_name,
			'last_name'       => $last_name,
			'payer_email'     => $payer_email,
			'status_id'       => $status_order_id,
			'order_duration'  => $order_duration,
			'price'           => $mc_gross,
			'activation_date' => current_time( 'mysql' ),
			'expired_date'    => $expired_date,
		], [ 'user_id' => $user_id ] );

	} else {
		$result = $wpdb->insert( $tablename, [
			'txn_id'          => $txn_id,
			'user_id'         => $user_id,
			'first_name'      => $first_name,
			'last_name'       => $last_name,
			'payer_email'     => $payer_email,
			'status_id'       => $status_order_id,
			'order_duration'  => $order_duration,
			'price'           => $mc_gross,
			'activation_date' => current_time( 'mysql' ),
			'expired_date'    => $expired_date,
		] );
	}

	$setData = [
		'result'      => $result,
		'last_error'  => $wpdb->last_error,
		'last_query'  => $wpdb->last_query,
		'last_result' => $wpdb->last_result,
	];
	file_put_contents( __DIR__ . '/ipn.log', json_encode( $setData, JSON_PRETTY_PRINT ) . "\n", FILE_APPEND );

	$user       = get_userdata( $user_id );
	$user_email = $user->user_email;

	$subject = 'You PRO plan at Masterhand PRO has been activated';
	$mailing = AE_Mailing::get_instance();

	if ( 0 < $result ) {
		$profile_id = get_user_meta( $user_id, 'user_profile_id', true );
		if ( ! add_post_meta( $profile_id, 'pro_status', $status_order_id, true ) ) {
			update_post_meta( $profile_id, 'pro_status', $status_order_id );
		}

		//do_action( 'activityRating_amountPayment', $user_id, $mc_gross );

		//
		//        $status_id = $status_order_id;
		//        $type_user = ae_user_role($user_id);
		//        $where_and = "AND s.id={$status_id} AND p.property_published=1";
		//        $property_for_status = table_properties($type_user, $where_and, 1);
		//        file_put_contents(__DIR__ . '/ipn.log', "$property_for_status" . json_encode($property_for_status,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);
		//

		$message = ae_get_option( 'bay_pro_status_template' );
		$message = str_replace( '[display_name]', $user->display_name, $message );
		$message = str_replace( '[status_data]', get_name_pro_status( $status_order_id ) . ' - $' . $mc_gross, $message );
		$message = str_replace( '[expired_date]', date( "Y-m-d", strtotime( $expired_date ) ), $message );

		$mailing->wp_mail( $user_email, $subject, $message, [ 'user_id' => $user_id ] );
	} else {
		$mailing->wp_mail( $adminemail, $subject, 'Оплата ПРО пользователя - ошибка', [ 'user_id' => $user_id ] );
	}

} else {
	//    echo 'Error';
	//    var_dump($ipnPayPal->errorMessage);

	$res = $ipnPayPal->errorMessage;
	file_put_contents( __DIR__ . '/ipn.log', "IPN verify result failed:\n" . ( is_array( $res ) ? json_encode( $res ) : $res ) . "\n\n", FILE_APPEND );

	wp_mail( $adminemail, 'INVALID IPN', 'INVALID IPN', [ 'Content-Type: text/html; charset=UTF-8' ] );
}
exit;