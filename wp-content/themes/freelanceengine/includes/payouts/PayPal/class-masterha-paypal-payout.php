<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( 'autoload.php' );

use PayPal\Api\Payout;
use PayPal\Api\PayoutItem;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

/**
 * Handle PayPal Payout API
 *
 * @class FP_WC_PP_PayPal_Payout
 * @category Class
 */
class FP_WC_PP_PayPal_Payout {

	protected $environment = 'sandbox';

	protected $receivers = array();

	protected $status = null;

	protected $currency = 'USD';

	protected $items = [];

	public $errors = [];

	protected $output = null;

	protected $api_credentials = array();

	protected $payment = false;

	public function __construct( $args = [] ) {
		$this->api_credentials = $this->get_credentials();
		$this->receivers       = $args['receivers'];
		$this->currency        = $args['currency'];
	}

	public function get_credentials() {
		$this->environment = 'sandbox';

		$data = ae_get_option( 'escrow_paypal_api' );

		return array(
			'client_id'  => $data["clientID"],
			'secret_key' => $data["secretKey"],
		);
	}

	public function getApiContext() {
		$oAuthTokenCredential = new OAuthTokenCredential( $this->api_credentials['client_id'], $this->api_credentials['secret_key'] );

		return new ApiContext( $oAuthTokenCredential );
	}

	public function getPayoutItems() {
		foreach ( $this->receivers as $key => $reciever ) {

			$this->items[] = new PayoutItem( array(
				'recipient_type' => 'EMAIL',
				'receiver'       => sanitize_email( $reciever["email"] ),
				'note'           => $reciever["note"] ?? "",
				'sender_item_id' => uniqid(),
				'amount'         => array(
					'value'    => $reciever["price"],
					'currency' => $reciever["currency"] ?? "USD",
				),
			) );
			//masterhand_send_teams( $items );
		}
	}

	public function getPayoutBatchStatus( $payoutItemId = null, $process = false ) {
		if ( $process ) {
			$payoutItemId = $this->output->getBatchHeader()->getPayoutBatchId();
		}

		try {
			$this->setEnvironment( $this->getApiContext() );
			$payoutBatchStatus = Payout::get( $payoutItemId, $this->getApiContext() );
		} catch ( Exception $e ) {
			wp_send_json( array(
				'success' => false,
				'msg'     => [ $e->getMessage() ]
			) );
		}

		return $payoutBatchStatus->toArray();
	}

	public function setEnvironment( $apiContext ) {
		$apiContext->setConfig(
			array(
				'mode'             => $this->environment,
				'log.LogEnabled'   => true,
				'log.FileName'     => plugin_dir_path( __FILE__ ) . 'PayPal.txt',
				'log.LogLevel'     => 'sandbox' === $this->environment ? 'DEBUG' : 'FINE',
				'validation.level' => 'log',
				'cache.enabled'    => true,
			)
		);
	}

	public function preparePayout() {
		$senderBatchHeader = new PayoutSenderBatchHeader();
		$senderBatchHeader->setSenderBatchId( uniqid() );
		$senderBatchHeader->setEmailSubject( "You have a payment" );

		$this->payment = new Payout();
		$this->payment->setSenderBatchHeader( $senderBatchHeader );

		$this->getPayoutItems();

		foreach ( $this->items as $item ) {
			$this->payment->addItem( $item );
		}
	}

	public function createBatchPayout() {
		try {
			$this->setEnvironment( $this->getApiContext() );
			$this->output = $this->payment->create( null, $this->getApiContext() );
		} catch ( Exception $e ) {
			wp_send_json( array(
				'success' => false,
				'msg'     => [ $e->getMessage() ]
			) );
		}

		return true;
	}

	public function init() {
		$this->preparePayout();
		$this->createBatchPayout();

		return $this->getPayoutBatchStatus( null, true );
	}

	public function getErrors() {
		return $this->errors;
	}
}
