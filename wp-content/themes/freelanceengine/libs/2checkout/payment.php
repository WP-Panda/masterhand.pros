<?php
require_once( "lib/Twocheckout.php" );

Twocheckout::privateKey( '14C847C5-4F8C-4245-A94B-FB295D13A883' ); //Private Key
Twocheckout::sellerId( '250425983793' ); // 2Checkout Account Number
Twocheckout::sandbox( true ); // Set to false for production accounts.

try {
	$charge = Twocheckout_Charge::auth( array(
		"merchantOrderId" => "123321",
		"token"           => $_POST['token'],
		"currency"        => 'USD',
		"demo"            => true,
		"lineItems"       => array(
			array(
				"name"     => "Demo Item",
				"price"    => "4.99",
				"type"     => "product",
				"quantity" => "1"
			)
		),
		"total"           => '10.00',
		"billingAddr"     => array(
			"name"        => $_POST['cardHolder'],
			"addrLine1"   => '123 Test St',
			"city"        => 'Columbus',
			"state"       => 'OH',
			"zipCode"     => '43123',
			"country"     => 'USA',
			"email"       => 'example@2co.com',
			"phoneNumber" => '555-555-5555'
		)
	) );

	if ( $charge['response']['responseCode'] == 'APPROVED' ) {
		echo "Thanks for your Order!";
		echo "<h3>Return Parameters:</h3>";
		echo "<pre>";
		var_dump( $charge );
		echo "</pre>";
	}
} catch ( Twocheckout_Error $e ) {
	echo 'Error: ';
	var_dump( $e );
	print_r( $e->getMessage() );
}