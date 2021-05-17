<?php

// File generated from our OpenAPI spec

// Stripe singleton
require dirname(__FILE__) . '/Stripe/Stripe.php';

// Utilities
require dirname(__FILE__) . '/Stripe/Util/CaseInsensitiveArray.php';
require dirname(__FILE__) . '/Stripe/Util/LoggerInterface.php';
require dirname(__FILE__) . '/Stripe/Util/DefaultLogger.php';
require dirname(__FILE__) . '/Stripe/Util/RandomGenerator.php';
require dirname(__FILE__) . '/Stripe/Util/RequestOptions.php';
require dirname(__FILE__) . '/Stripe/Util/Set.php';
require dirname(__FILE__) . '/Stripe/Util/Util.php';
require dirname(__FILE__) . '/Stripe/Util/ObjectTypes.php';

// HttpClient
require dirname(__FILE__) . '/Stripe/HttpClient/ClientInterface.php';
require dirname(__FILE__) . '/Stripe/HttpClient/CurlClient.php';

// Exceptions
require dirname(__FILE__) . '/Stripe/Exception/ExceptionInterface.php';
require dirname(__FILE__) . '/Stripe/Exception/ApiErrorException.php';
require dirname(__FILE__) . '/Stripe/Exception/ApiConnectionException.php';
require dirname(__FILE__) . '/Stripe/Exception/AuthenticationException.php';
require dirname(__FILE__) . '/Stripe/Exception/BadMethodCallException.php';
require dirname(__FILE__) . '/Stripe/Exception/CardException.php';
require dirname(__FILE__) . '/Stripe/Exception/IdempotencyException.php';
require dirname(__FILE__) . '/Stripe/Exception/InvalidArgumentException.php';
require dirname(__FILE__) . '/Stripe/Exception/InvalidRequestException.php';
require dirname(__FILE__) . '/Stripe/Exception/PermissionException.php';
require dirname(__FILE__) . '/Stripe/Exception/RateLimitException.php';
require dirname(__FILE__) . '/Stripe/Exception/SignatureVerificationException.php';
require dirname(__FILE__) . '/Stripe/Exception/UnexpectedValueException.php';
require dirname(__FILE__) . '/Stripe/Exception/UnknownApiErrorException.php';

// OAuth exceptions
require dirname(__FILE__) . '/Stripe/Exception/OAuth/ExceptionInterface.php';
require dirname(__FILE__) . '/Stripe/Exception/OAuth/OAuthErrorException.php';
require dirname(__FILE__) . '/Stripe/Exception/OAuth/InvalidClientException.php';
require dirname(__FILE__) . '/Stripe/Exception/OAuth/InvalidGrantException.php';
require dirname(__FILE__) . '/Stripe/Exception/OAuth/InvalidRequestException.php';
require dirname(__FILE__) . '/Stripe/Exception/OAuth/InvalidScopeException.php';
require dirname(__FILE__) . '/Stripe/Exception/OAuth/UnknownOAuthErrorException.php';
require dirname(__FILE__) . '/Stripe/Exception/OAuth/UnsupportedGrantTypeException.php';
require dirname(__FILE__) . '/Stripe/Exception/OAuth/UnsupportedResponseTypeException.php';

// API operations
require dirname(__FILE__) . '/Stripe/ApiOperations/All.php';
require dirname(__FILE__) . '/Stripe/ApiOperations/Create.php';
require dirname(__FILE__) . '/Stripe/ApiOperations/Delete.php';
require dirname(__FILE__) . '/Stripe/ApiOperations/NestedResource.php';
require dirname(__FILE__) . '/Stripe/ApiOperations/Request.php';
require dirname(__FILE__) . '/Stripe/ApiOperations/Retrieve.php';
require dirname(__FILE__) . '/Stripe/ApiOperations/Update.php';

// Plumbing
require dirname(__FILE__) . '/Stripe/ApiResponse.php';
require dirname(__FILE__) . '/Stripe/RequestTelemetry.php';
require dirname(__FILE__) . '/Stripe/StripeObject.php';
require dirname(__FILE__) . '/Stripe/ApiRequestor.php';
require dirname(__FILE__) . '/Stripe/ApiResource.php';
require dirname(__FILE__) . '/Stripe/SingletonApiResource.php';
require dirname(__FILE__) . '/Stripe/Service/AbstractService.php';
require dirname(__FILE__) . '/Stripe/Service/AbstractServiceFactory.php';

// StripeClient
require dirname(__FILE__) . '/Stripe/StripeClientInterface.php';
require dirname(__FILE__) . '/Stripe/BaseStripeClient.php';
require dirname(__FILE__) . '/Stripe/StripeClient.php';

// Stripe API Resources
require dirname(__FILE__) . '/Stripe/Account.php';
require dirname(__FILE__) . '/Stripe/AccountLink.php';
require dirname(__FILE__) . '/Stripe/AlipayAccount.php';
require dirname(__FILE__) . '/Stripe/ApplePayDomain.php';
require dirname(__FILE__) . '/Stripe/ApplicationFee.php';
require dirname(__FILE__) . '/Stripe/ApplicationFeeRefund.php';
require dirname(__FILE__) . '/Stripe/Balance.php';
require dirname(__FILE__) . '/Stripe/BalanceTransaction.php';
require dirname(__FILE__) . '/Stripe/BankAccount.php';
require dirname(__FILE__) . '/Stripe/BillingPortal/Session.php';
require dirname(__FILE__) . '/Stripe/BitcoinReceiver.php';
require dirname(__FILE__) . '/Stripe/BitcoinTransaction.php';
require dirname(__FILE__) . '/Stripe/Capability.php';
require dirname(__FILE__) . '/Stripe/Card.php';
require dirname(__FILE__) . '/Stripe/Charge.php';
require dirname(__FILE__) . '/Stripe/Checkout/Session.php';
require dirname(__FILE__) . '/Stripe/Collection.php';
require dirname(__FILE__) . '/Stripe/CountrySpec.php';
require dirname(__FILE__) . '/Stripe/Coupon.php';
require dirname(__FILE__) . '/Stripe/CreditNote.php';
require dirname(__FILE__) . '/Stripe/CreditNoteLineItem.php';
require dirname(__FILE__) . '/Stripe/Customer.php';
require dirname(__FILE__) . '/Stripe/CustomerBalanceTransaction.php';
require dirname(__FILE__) . '/Stripe/Discount.php';
require dirname(__FILE__) . '/Stripe/Dispute.php';
require dirname(__FILE__) . '/Stripe/EphemeralKey.php';
require dirname(__FILE__) . '/Stripe/ErrorObject.php';
require dirname(__FILE__) . '/Stripe/Event.php';
require dirname(__FILE__) . '/Stripe/ExchangeRate.php';
require dirname(__FILE__) . '/Stripe/File.php';
require dirname(__FILE__) . '/Stripe/FileLink.php';
require dirname(__FILE__) . '/Stripe/Invoice.php';
require dirname(__FILE__) . '/Stripe/InvoiceItem.php';
require dirname(__FILE__) . '/Stripe/InvoiceLineItem.php';
require dirname(__FILE__) . '/Stripe/Issuing/Authorization.php';
require dirname(__FILE__) . '/Stripe/Issuing/Card.php';
require dirname(__FILE__) . '/Stripe/Issuing/CardDetails.php';
require dirname(__FILE__) . '/Stripe/Issuing/Cardholder.php';
require dirname(__FILE__) . '/Stripe/Issuing/Dispute.php';
require dirname(__FILE__) . '/Stripe/Issuing/Transaction.php';
require dirname(__FILE__) . '/Stripe/LineItem.php';
require dirname(__FILE__) . '/Stripe/LoginLink.php';
require dirname(__FILE__) . '/Stripe/Mandate.php';
require dirname(__FILE__) . '/Stripe/Order.php';
require dirname(__FILE__) . '/Stripe/OrderItem.php';
require dirname(__FILE__) . '/Stripe/OrderReturn.php';
require dirname(__FILE__) . '/Stripe/PaymentIntent.php';
require dirname(__FILE__) . '/Stripe/PaymentMethod.php';
require dirname(__FILE__) . '/Stripe/Payout.php';
require dirname(__FILE__) . '/Stripe/Person.php';
require dirname(__FILE__) . '/Stripe/Plan.php';
require dirname(__FILE__) . '/Stripe/Price.php';
require dirname(__FILE__) . '/Stripe/Product.php';
require dirname(__FILE__) . '/Stripe/PromotionCode.php';
require dirname(__FILE__) . '/Stripe/Radar/EarlyFraudWarning.php';
require dirname(__FILE__) . '/Stripe/Radar/ValueList.php';
require dirname(__FILE__) . '/Stripe/Radar/ValueListItem.php';
require dirname(__FILE__) . '/Stripe/Recipient.php';
require dirname(__FILE__) . '/Stripe/RecipientTransfer.php';
require dirname(__FILE__) . '/Stripe/Refund.php';
require dirname(__FILE__) . '/Stripe/Reporting/ReportRun.php';
require dirname(__FILE__) . '/Stripe/Reporting/ReportType.php';
require dirname(__FILE__) . '/Stripe/Review.php';
require dirname(__FILE__) . '/Stripe/SetupIntent.php';
require dirname(__FILE__) . '/Stripe/Sigma/ScheduledQueryRun.php';
require dirname(__FILE__) . '/Stripe/SKU.php';
require dirname(__FILE__) . '/Stripe/Source.php';
require dirname(__FILE__) . '/Stripe/SourceTransaction.php';
require dirname(__FILE__) . '/Stripe/Subscription.php';
require dirname(__FILE__) . '/Stripe/SubscriptionItem.php';
require dirname(__FILE__) . '/Stripe/SubscriptionSchedule.php';
require dirname(__FILE__) . '/Stripe/TaxId.php';
require dirname(__FILE__) . '/Stripe/TaxRate.php';
require dirname(__FILE__) . '/Stripe/Terminal/ConnectionToken.php';
require dirname(__FILE__) . '/Stripe/Terminal/Location.php';
require dirname(__FILE__) . '/Stripe/Terminal/Reader.php';
require dirname(__FILE__) . '/Stripe/ThreeDSecure.php';
require dirname(__FILE__) . '/Stripe/Token.php';
require dirname(__FILE__) . '/Stripe/Topup.php';
require dirname(__FILE__) . '/Stripe/Transfer.php';
require dirname(__FILE__) . '/Stripe/TransferReversal.php';
require dirname(__FILE__) . '/Stripe/UsageRecord.php';
require dirname(__FILE__) . '/Stripe/UsageRecordSummary.php';
require dirname(__FILE__) . '/Stripe/WebhookEndpoint.php';

// Services
require dirname(__FILE__) . '/Stripe/Service/AccountService.php';
require dirname(__FILE__) . '/Stripe/Service/AccountLinkService.php';
require dirname(__FILE__) . '/Stripe/Service/ApplePayDomainService.php';
require dirname(__FILE__) . '/Stripe/Service/ApplicationFeeService.php';
require dirname(__FILE__) . '/Stripe/Service/BalanceService.php';
require dirname(__FILE__) . '/Stripe/Service/BalanceTransactionService.php';
require dirname(__FILE__) . '/Stripe/Service/BillingPortal/SessionService.php';
require dirname(__FILE__) . '/Stripe/Service/ChargeService.php';
require dirname(__FILE__) . '/Stripe/Service/Checkout/SessionService.php';
require dirname(__FILE__) . '/Stripe/Service/CountrySpecService.php';
require dirname(__FILE__) . '/Stripe/Service/CouponService.php';
require dirname(__FILE__) . '/Stripe/Service/CreditNoteService.php';
require dirname(__FILE__) . '/Stripe/Service/CustomerService.php';
require dirname(__FILE__) . '/Stripe/Service/DisputeService.php';
require dirname(__FILE__) . '/Stripe/Service/EphemeralKeyService.php';
require dirname(__FILE__) . '/Stripe/Service/EventService.php';
require dirname(__FILE__) . '/Stripe/Service/ExchangeRateService.php';
require dirname(__FILE__) . '/Stripe/Service/FileService.php';
require dirname(__FILE__) . '/Stripe/Service/FileLinkService.php';
require dirname(__FILE__) . '/Stripe/Service/InvoiceService.php';
require dirname(__FILE__) . '/Stripe/Service/InvoiceItemService.php';
require dirname(__FILE__) . '/Stripe/Service/Issuing/AuthorizationService.php';
require dirname(__FILE__) . '/Stripe/Service/Issuing/CardService.php';
require dirname(__FILE__) . '/Stripe/Service/Issuing/CardholderService.php';
require dirname(__FILE__) . '/Stripe/Service/Issuing/DisputeService.php';
require dirname(__FILE__) . '/Stripe/Service/Issuing/TransactionService.php';
require dirname(__FILE__) . '/Stripe/Service/MandateService.php';
require dirname(__FILE__) . '/Stripe/Service/OrderService.php';
require dirname(__FILE__) . '/Stripe/Service/OrderReturnService.php';
require dirname(__FILE__) . '/Stripe/Service/PaymentIntentService.php';
require dirname(__FILE__) . '/Stripe/Service/PaymentMethodService.php';
require dirname(__FILE__) . '/Stripe/Service/PayoutService.php';
require dirname(__FILE__) . '/Stripe/Service/PlanService.php';
require dirname(__FILE__) . '/Stripe/Service/PriceService.php';
require dirname(__FILE__) . '/Stripe/Service/ProductService.php';
require dirname(__FILE__) . '/Stripe/Service/PromotionCodeService.php';
require dirname(__FILE__) . '/Stripe/Service/Radar/EarlyFraudWarningService.php';
require dirname(__FILE__) . '/Stripe/Service/Radar/ValueListService.php';
require dirname(__FILE__) . '/Stripe/Service/Radar/ValueListItemService.php';
require dirname(__FILE__) . '/Stripe/Service/RefundService.php';
require dirname(__FILE__) . '/Stripe/Service/Reporting/ReportRunService.php';
require dirname(__FILE__) . '/Stripe/Service/Reporting/ReportTypeService.php';
require dirname(__FILE__) . '/Stripe/Service/ReviewService.php';
require dirname(__FILE__) . '/Stripe/Service/SetupIntentService.php';
require dirname(__FILE__) . '/Stripe/Service/Sigma/ScheduledQueryRunService.php';
require dirname(__FILE__) . '/Stripe/Service/SkuService.php';
require dirname(__FILE__) . '/Stripe/Service/SourceService.php';
require dirname(__FILE__) . '/Stripe/Service/SubscriptionService.php';
require dirname(__FILE__) . '/Stripe/Service/SubscriptionItemService.php';
require dirname(__FILE__) . '/Stripe/Service/SubscriptionScheduleService.php';
require dirname(__FILE__) . '/Stripe/Service/TaxRateService.php';
require dirname(__FILE__) . '/Stripe/Service/Terminal/ConnectionTokenService.php';
require dirname(__FILE__) . '/Stripe/Service/Terminal/LocationService.php';
require dirname(__FILE__) . '/Stripe/Service/Terminal/ReaderService.php';
require dirname(__FILE__) . '/Stripe/Service/TokenService.php';
require dirname(__FILE__) . '/Stripe/Service/TopupService.php';
require dirname(__FILE__) . '/Stripe/Service/TransferService.php';
require dirname(__FILE__) . '/Stripe/Service/WebhookEndpointService.php';

// Service factories
require dirname(__FILE__) . '/Stripe/Service/CoreServiceFactory.php';
require dirname(__FILE__) . '/Stripe/Service/BillingPortal/BillingPortalServiceFactory.php';
require dirname(__FILE__) . '/Stripe/Service/Checkout/CheckoutServiceFactory.php';
require dirname(__FILE__) . '/Stripe/Service/Issuing/IssuingServiceFactory.php';
require dirname(__FILE__) . '/Stripe/Service/Radar/RadarServiceFactory.php';
require dirname(__FILE__) . '/Stripe/Service/Reporting/ReportingServiceFactory.php';
require dirname(__FILE__) . '/Stripe/Service/Sigma/SigmaServiceFactory.php';
require dirname(__FILE__) . '/Stripe/Service/Terminal/TerminalServiceFactory.php';

// OAuth
require dirname(__FILE__) . '/Stripe/OAuth.php';
require dirname(__FILE__) . '/Stripe/OAuthErrorObject.php';
require dirname(__FILE__) . '/Stripe/Service/OAuthService.php';

// Webhooks
require dirname(__FILE__) . '/Stripe/Webhook.php';
require dirname(__FILE__) . '/Stripe/WebhookSignature.php';
