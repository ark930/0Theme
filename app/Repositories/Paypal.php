<?php

namespace App\Repositories;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\FlowConfig;
use PayPal\Api\InputFields;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Presentation;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Refund;
use PayPal\Api\Sale;

use PayPal\Api\WebProfile;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

use Exception;

class Paypal
{
    private $apiContext;

    public function __construct()
    {
        $clientId = env('PAYPAL_CLIENT_ID');
        $clientSecret = env('PAYPAL_CLIENT_SECRET');

        $this->apiContext = $this->getApiContext($clientId, $clientSecret);
    }

    public function createPaymentUsingPayPal($productName, $orderNo, $price, $successUrl, $failUrl)
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item1 = new Item();
        $item1->setName($productName)
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setSku($orderNo)
            ->setDescription('this is some description')
            ->setPrice($price)
            ->setCategory('DIGITAL');

        $itemList = new ItemList();
        $itemList->setItems(array($item1));

        $amount = new Amount();
        $amount->setCurrency("USD")
               ->setTotal($price);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description xxoo")
            ->setInvoiceNumber(uniqid())
//            ->setPurchaseUnitReferenceId($orderNo)
        ;

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($successUrl)
            ->setCancelUrl($failUrl);

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setNoteToPayer('一些对产品的介绍 你可以看到吗 哈哈哈哈哈哈哈哈哈哈')
            ->setTransactions(array($transaction));

        try {
            $payment->create($this->apiContext);
        } catch (\Exception $ex) {
            exit(1);
        }

        return $payment;
    }

    public function executePayment($paymentId, $payerId, $totalAmount)
    {
            $payment = Payment::get($paymentId, $this->apiContext);

            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            // ### Optional Changes to Amount
            // If you wish to update the amount that you wish to charge the customer,
            // based on the shipping address or any other reason, you could
            // do that by passing the transaction object with just `amount` field in it.
            // Here is the example on how we changed the shipping to $1 more than before.
//            $transaction = new Transaction();
//            $amount = new Amount();
//            $amount->setCurrency('USD');
//            $amount->setTotal(0.2);
//            $transaction->setAmount($amount);
//
//            // Add the above transaction object inside our Execution object.
//            $execution->addTransaction($transaction);

            try {
                return $result = $payment->execute($execution, $this->apiContext);
                try {
                    $payment = Payment::get($paymentId, $this->apiContext);
                } catch (Exception $ex) {
                    dd($ex);
                    exit(1);
                }
            } catch (Exception $ex) {
                dd($ex);
                exit(1);
            }

            return $payment;

    }

    public function saleRefund($saleId)
    {
        // ### Refund amount
        // Includes both the refunded amount (to Payer)
        // and refunded fee (to Payee). Use the $amt->details
        // field to mention fees refund details.
        $amt = new Amount();
        $amt->setCurrency('USD')
            ->setTotal(112.01);

        // ### Refund object
        $refund = new Refund();
        $refund->setAmount($amt);

        // ###Sale
        // A sale transaction.
        // Create a Sale object with the
        // given sale transaction id.
        $sale = new Sale();
        $sale->setId($saleId);
        try {
            // Create a new apiContext object so we send a new
            // PayPal-Request-Id (idempotency) header for this resource
        //    $apiContext = getApiContext($clientId, $clientSecret);

            // Refund the sale
            // (See bootstrap.php for more on `ApiContext`)
            $refundedSale = $sale->refund($refund, $this->apiContext);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        return $refundedSale;
    }

    public function getSale($saleId)
    {
        try {
            // ### Retrieve the sale object
            // Pass the ID of the sale
            // transaction from your payment resource.
            $sale = Sale::get($saleId, $this->apiContext);
        } catch (Exception $ex) {
            exit(1);
        }

        return $sale;
    }

    /**
     * Helper method for getting an APIContext for all calls
     * @param string $clientId Client ID
     * @param string $clientSecret Client Secret
     *
     * @return ApiContext
     */
    private function getApiContext($clientId, $clientSecret)
    {
        // #### SDK configuration
        // Register the sdk_config.ini file in current directory
        // as the configuration source.
        /*
        if(!defined("PP_CONFIG_PATH")) {
            define("PP_CONFIG_PATH", __DIR__);
        }
        */

        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );

        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration

        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );

        // Partner Attribution Id
        // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
        // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
        // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');

        return $apiContext;
    }

    public function CreateWebProfile()
    {
        // Lets create an instance of FlowConfig and add
        // landing page type information
        $flowConfig = new FlowConfig();
        // Type of PayPal page to be displayed when a user lands on the PayPal site for checkout. Allowed values: Billing or Login. When set to Billing, the Non-PayPal account landing page is used. When set to Login, the PayPal account login landing page is used.
        $flowConfig->setLandingPageType("Billing");
        // The URL on the merchant site for transferring to after a bank transfer payment.
        $flowConfig->setBankTxnPendingUrl("http://www.0theme.com/");

        // Parameters for style and presentation.
        $presentation = new Presentation();

        // A URL to logo image. Allowed vaues: .gif, .jpg, or .png.
        $presentation->setLogoImage("http://cdn.aixifan.com/acfun-pc/1.6.10/img/logo.png")
        //	A label that overrides the business name in the PayPal account on the PayPal pages.
                     ->setBrandName("0Theme Paypal")
        //  Locale of pages displayed by PayPal payment experience.
                     ->setLocaleCode("US");

        // Parameters for input fields customization.
        $inputFields = new InputFields();
        // Enables the buyer to enter a note to the merchant on the PayPal page during checkout.
        $inputFields->setAllowNote(true)
            // Determines whether or not PayPal displays shipping address fields on the experience pages. Allowed values: 0, 1, or 2. When set to 0, PayPal displays the shipping address on the PayPal pages. When set to 1, PayPal does not display shipping address fields whatsoever. When set to 2, if you do not pass the shipping address, PayPal obtains it from the buyer’s account profile. For digital goods, this field is required, and you must set it to 1.
                    ->setNoShipping(1)
            // Determines whether or not the PayPal pages should display the shipping address and not the shipping address on file with PayPal for this buyer. Displaying the PayPal street address on file does not allow the buyer to edit that address. Allowed values: 0 or 1. When set to 0, the PayPal pages should not display the shipping address. When set to 1, the PayPal pages should display the shipping address.
                    ->setAddressOverride(0);

        // #### Payment Web experience profile resource
        $webProfile = new WebProfile();

        // Name of the web experience profile. Required. Must be unique
        $webProfile->setName("0Theme shop" . uniqid())
            // Parameters for flow configuration.
                   ->setFlowConfig($flowConfig)
            // Parameters for style and presentation.
                   ->setPresentation($presentation)
            // Parameters for input field customization.
                   ->setInputFields($inputFields);

        try {
            // Use this call to create a profile.
            $createProfileResponse = $webProfile->create($this->apiContext);
        } catch (PayPalConnectionException $ex) {
            dd($ex);
            exit(1);
        }

        return $createProfileResponse;
    }
}

