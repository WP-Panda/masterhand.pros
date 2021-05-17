<?php

if( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly
}

include_once('autoload.php') ;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\PaymentExecution;

/**
 * Handle PayPal Payment API
 * 
 * @class FP_WC_PP_PayPal_Payout
 * @category Class
 */

class FP_WC_PP_PayPal_Payment {
    protected $environment          = 'sandbox';

    protected $payer                =  false;

    protected $quantity             = 1;

    protected $receivers            = array() ;

    protected $details              = null;

    protected $currency             = 'USD';

    protected $amount               = null;

    protected $transaction          = null;

    protected $redirects            = null;

    protected $payment              = null;

    protected $items                = [];

    protected $args                 = [];

    protected $api_credentials      = array() ;

    public function __construct( $args = [] ) {
        $this->args = $args;
        $this->api_credentials    = $this->get_credentials();
    }

    public function preparePayment() {
        $this->makePayer($this->args["payer"]);

        if($this->args["items"] && count($this->args["items"]) > 0) {
            foreach ($this->args["items"] as $key => $item) {
                $this->items[] = $this->makeItem($item);
            }
        } else
            $this->items[] = $this->makeItem(["currency", $this->args["currency"]]);

        $this->makeItemList();
        
        $this->makeDetails($this->args["tax"]);

        $this->makeAmount($this->args["total"], $this->args["currency"]);

        $this->makeTransaction($this->args["id"]);

        $this->makeRedirect($this->args["returnUrl"], $this->args["canselUrl"]);
        
        $this->makePayment();
    }

    public function getPaymentDetails($payment_id = null) {
        if( ! $payment_id && ! $this->args["payment_id"] ) return false;

        if(!$payment_id)
            $payment_id = $this->args["payment_id"];

        $this->setEnvironment( $this->getApiContext() ) ;

        return Payment::get($payment_id, $this->getApiContext());
    }

    public function execution($data = []) {
        if( ! $data['PayerID'] || ! $data["paymentId"] ) return false;
        $token = $this->getApiContext();
        $this->setEnvironment( $token ) ;
        $execution = new PaymentExecution();
        $execution->setPayerId($data['PayerID']);
        $payment = $this->getPaymentDetails($data["paymentId"]);
        $payment->execute($execution, $token);

        return $this->getPaymentDetails($data["paymentId"]);
    }

    public function pay() {
        try {
            $this->setEnvironment( $this->getApiContext() ) ;
            $payment = $this->payment->create( $this->getApiContext() );
        } catch (Exception $ex) {
            wp_send_json(array(
                'success' => false,
                'msg' => $ex->getMessage()
            ));
        }
        
        return $payment;
    }

    public function makePayment() {
        $payment = new Payment();
        $this->payment = $payment->setIntent("sale")
            ->setPayer($this->payer)
            ->setRedirectUrls($this->redirects)
            ->setTransactions(array($this->transaction));
    }

    public function makeRedirect($returnUrl, $canselUrl) {
        global $wp;
        //$baseUrl = home_url( $wp->request );
        $redirectUrls = new RedirectUrls();
        $this->redirects = $redirectUrls->setReturnUrl($returnUrl)
            ->setCancelUrl($canselUrl);
    }

    public function makeTransaction($id) {
        $transaction = new Transaction();
        $this->transaction = $transaction->setAmount($this->amount)
            ->setItemList($this->itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber($id ?? uniqid());
    }

    public function makeAmount($total, $currency = "USD") {
        $amount = new Amount();
        return $this->amount = $amount->setCurrency($currency)
            ->setTotal($total)
            ->setDetails($details);
    }

    public function makeDetails($tax = 0) {
        $details = new Details();
        $details->setTax($tax);
    }

    public function makeItemList() {
        $itemList = new ItemList();
        return $itemList->setItems($this->items);
    }

    public function makePayer($method) {
        if(isset($args["payer"]) && !empty($args["payer"]))
            $method = $args["payer"];
        else
            $method = "paypal";

        $payer = new Payer();
        $this->payer = $payer->setPaymentMethod($method);
    }

    public function makeItem($item) {
        $it = new Item();
        $it->setName($item["name"] ?? "Payment for service")
            ->setCurrency($item["currency"] ?? $this->currency)
            ->setQuantity($item["quantity"] ?? $this->quantity)
            ->setSku($item["sku"] ?? uniqid())
            ->setPrice($item["price"] ?? 0);

        return $it;
    }

    public function get_credentials() {
        $data = ae_get_option('escrow_paypal_api');
        return array(
            'client_id'  => $data["clientID"] ,
            'secret_key' => $data["secretKey"] ,
                ) ;
    }

    public function setEnvironment( $apiContext ) {

        $apiContext->setConfig(
            array(
                'mode'             => $this->environment ,
                'log.LogEnabled'   => true ,
                'log.FileName'     => plugin_dir_path( __FILE__ ).'PayPal.txt' ,
                'log.LogLevel'     => 'sandbox' === $this->environment ? 'DEBUG' : 'FINE' ,
                'validation.level' => 'log' ,
                'cache.enabled'    => true ,
            )
        ) ;
    }
    public function getApiContext() {
        $oAuthTokenCredential = new OAuthTokenCredential( $this->api_credentials[ 'client_id' ] , $this->api_credentials[ 'secret_key' ] ) ;

        return new ApiContext( $oAuthTokenCredential ) ;
    }

    public function setPayment($method = null) {

    }

}
