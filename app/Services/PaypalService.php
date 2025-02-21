<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use Psr\Log\LogLevel;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;  
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentSourceBuilder;
use PaypalServerSdkLib\Models\Builders\PaypalWalletBuilder;
use PaypalServerSdkLib\Models\Builders\PaypalWalletExperienceContextBuilder;

class PayPalService
{
    private $client;

    public function __construct()
    {
        $result = DB::select('select * from payment_methods where status = ? and name = ?', ["active", "Paypal"]);

        $credentials =json_decode($result[0]->credential);

        $this->client = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    $credentials->client_id,
                    $credentials->client_secret
                )
            )
            ->environment(Environment::SANDBOX)
           /* ->loggingConfiguration(
                LoggingConfigurationBuilder::init()
                    ->level(LogLevel::DEBUG)
                    ->requestConfiguration(RequestLoggingConfigurationBuilder::init()->body(true))
                    ->responseConfiguration(ResponseLoggingConfigurationBuilder::init()->headers(true))
                )*/
            ->build();
    }

    public function createPayment($amount, $currency, $successUrl, $cancelUrl)
    {
        $ordersController = $this->client->getOrdersController();

        $collect = [
            'body' => [
                "intent" => "AUTHORIZE",
                "payment_source" => [
                    "paypal" => [
                        "experience_context"=> [
                           "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                            "landing_page" => "LOGIN",
                            "shipping_preference" => "GET_FROM_FILE",
                            "user_action" => "PAY_NOW",
                            "return_url" => $successUrl,
                            "cancel_url" => $cancelUrl,
                        ]
                    ]
                ],
                'purchase_units' => [
                    [
                        'amount' => [
                            "currency_code" => $currency,
                            "value" => $amount,
                        ]
                    ]
                ]
            ],
           'prefer' => 'return=minimal'
        ];

        return $ordersController->ordersCreate($collect);
    }

    public function executePayment($token, $payerId)
    {
        $ordersController = $this->client->getOrdersController();

        $collect = [
            'id' => $token,
            'prefer' => 'return=minimal'
        ];

        return $ordersController->ordersAuthorize($collect);
    }
}
























?>