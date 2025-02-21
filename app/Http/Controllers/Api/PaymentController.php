<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;
use App\Services\PaypalService;
use App\Models\Cart;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\UserCouponCode;
use App\Models\Address;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Payment;
use App\Models\Shipment;
use Validator;

class PaymentController extends Controller
{
    private $paypal;

    public function __construct(PaypalService $paypal)
    {
        $this->paypal = $paypal;
    }


  /*  public function cardPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'first_name' => 'required|string',
                'last_name'=> 'required|string',
                'number' => 'required|integer|digits_between:13,19',
                'expiry_month'=> 'required|integer|digits_between:1,2',
                'expiry_year'=> 'required|integer|digits:4',
                'cvv' => 'required|integer|digits_between:3,4'
            ]
        );

        
        return response()->json([]);
    }*/

    public function paypalPayment()
    {
        $user_id = auth('api')->user()->id;

        $items = Cart::select('cart.*','products.name','products.image','products.price','products.downloadable')
            ->selectRaw('
                CASE 
                   WHEN(
                        products.sale_price > 0 AND
                        (IFNULL((products.sale_price < flash_sale_products.price), true)) AND
                        NOW() BETWEEN products.start_date AND products.end_date
                    ) THEN products.sale_price
                    WHEN(
                        flash_sale_products.price IS NOT NULL
                    ) THEN flash_sale_products.price
                    ELSE products.price
                END AS active_price
            ')
            ->join('products', 'products.id', '=','cart.product_id')
            ->leftJoin('flash_sales', function(JoinClause $join){
                $join->whereRaw('flash_sales.end_date > CURRENT_DATE');
            })
            ->leftJoin('flash_sale_products', function(JoinClause $join){
                $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id')
                    ->on('products.id', '=', 'flash_sale_products.product_id');
            })
            ->where('cart.user_id', $user_id)
            ->get();

        $currencyCode = Setting::where('key', 'currency_code')->value('value');

        $totalPrice = 0;

        $subTotalPrice = $items->sum(function ($product) {
            return $product->active_price * $product->quantity;
        });

        $coupon = Coupon::join('user_coupon_code', 'user_coupon_code.coupon_id','=', 'coupons.id')
            ->where('user_coupon_code.user_id', $user_id)
            ->first();

        $couponDiscount = 0;

        $isFreeShipping = false;

        if ($coupon != null) {
            $meetMinSpend = $coupon->minimum_spend? ($coupon->minimum_spend <= $totalPrice) : true;

            $meetMaxSpend = $coupon->maximum_spend? ($coupon->maximum_spend >= $totalPrice) : true;

            $meetUsageLimit = $coupon->usage_limit? ($coupon->used < $coupon->usage_limit) : true;

            if ($meetMinSpend && $meetMaxSpend && $meetUsageLimit) {

                switch ($coupon->type) {
                    case 'percent':
                        $couponDiscount = ($subTotalPrice * $coupon->value) / 100;
                        break;
                    case 'amount':
                        $couponDiscount = $coupon->value;
                        break;
                    case 'free_shipping':
                          $isFreeShipping = true;
                        break;
                }
            }
        }

        $tax = 0; //TODO 
        $shippingFee = 0; //TODO 

        $totalPrice += $subTotalPrice;
        $totalPrice += $tax;
        $totalPrice += $shippingFee;

        $totalPrice -= $couponDiscount;

        try {

            $result = $this->paypal->createPayment(
                $totalPrice, 
                $currencyCode, 
                "com.example.deniseshop.payments://paypal.payment?status=success", 
                "com.example.deniseshop.payments://paypal.payment?status=cancel"
            );

           
            $statusCode = $result->getStatusCode();
            $resultData = $result->getResult();

            if ($statusCode != 200) {
                return response()->json([
                    'success' => false,
                    'message'=> "Could not complete transaction try again later"
                ], $statusCode);
            }
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message'=> "Could not complete transaction try again later"
            ], 500);
        }

        $data = "";#json_encode($resultData);

        $url = "";
       foreach ($resultData->getLinks() as $value) {
            if ($value->getRel() == "payer-action") {
                $url = $value->getHref();
            }
        }
    
        return response()->json(["url" => $url]);
    }

    public function paypalSuccess(Request $request)
    {
        $user_id = auth('api')->user()->id;

        $token = $request->token;
        $payerId = $request->payer_id;

         try {

            $result = $this->paypal->executePayment($token, $payerId);

           $statusCode = $result->getStatusCode();
           $resultData = $result->getResult();

            if ($statusCode != 201) {
                return response()->json([
                    'success' => false,
                    'message'=> "Could not complete transaction try again later",
                ], $statusCode);
            }
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message'=> "Could not complete transaction try again later"
            ], 500);
        }

        $transactionId = $resultData->getPurchaseUnits()[0]->getPayments()->getAuthorizations()[0]->getId();

        $items = Cart::select('cart.*','products.name','products.image','products.price','products.downloadable','products.download_file')
            ->selectRaw('
                CASE 
                   WHEN(
                        products.sale_price > 0 AND
                        (IFNULL((products.sale_price < flash_sale_products.price), true)) AND
                        NOW() BETWEEN products.start_date AND products.end_date
                    ) THEN products.sale_price
                    WHEN(
                        flash_sale_products.price IS NOT NULL
                    ) THEN flash_sale_products.price
                    ELSE products.price
                END AS active_price
            ')
            ->join('products', 'products.id', '=','cart.product_id')
            ->leftJoin('flash_sales', function(JoinClause $join){
                $join->whereRaw('flash_sales.end_date > CURRENT_DATE');
            })
            ->leftJoin('flash_sale_products', function(JoinClause $join){
                $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id')
                    ->on('products.id', '=', 'flash_sale_products.product_id');
            })
            ->where('cart.user_id', $user_id)
            ->get();

        $currencySymbol = Setting::where('key', 'currency_code')->value('value');

        $containPhysicalItem = $items->contains(function ($item) {
            return ($item->downloadable == 0);
        });

        $totalPrice = 0;

        $subTotalPrice = $items->sum(function ($product) {
            return $product->active_price * $product->quantity;
        });

        $quantity = $items->sum(function ($product) {
            return $product->quantity;
        });

        $coupon = Coupon::join('user_coupon_code', 'user_coupon_code.coupon_id','=', 'coupons.id')
            ->where('user_coupon_code.user_id', $user_id)
            ->first();

        $couponDiscount = 0;
        $isFreeShipping = false;

        if ($coupon != null) {
            $meetMinSpend = $coupon->minimum_spend? ($coupon->minimum_spend <= $totalPrice) : true;

            $meetMaxSpend = $coupon->maximum_spend? ($coupon->maximum_spend >= $totalPrice) : true;

            $meetUsageLimit = $coupon->usage_limit? ($coupon->used < $coupon->usage_limit) : true;

            if ($meetMinSpend && $meetMaxSpend && $meetUsageLimit) {

                switch ($coupon->type) {
                    case 'percent':
                        $couponDiscount = ($subTotalPrice * $coupon->value) / 100;
                        break;
                    case 'amount':
                        $couponDiscount = $coupon->value;
                        break;
                    case 'free_shipping':
                          $isFreeShipping = true;
                        break;
                }
            }
            
        }

        $tax = 0;//TODO
        $shippingFee = 0;//TODO

        $totalPrice += $subTotalPrice;
        $totalPrice += $tax;
        $totalPrice += $shippingFee;

        $totalPrice -= $couponDiscount;

        $paymentMethod = PaymentMethod::where(['status' => 'active', 'name' => 'paypal'])->first();

        $address = Address::where(['user_id' => $user_id, 'default' => true])->first();

        $order = new Order();
        $order->user_id = $user_id;
        $order->name = $items->first()->name;
        $order->image = $items->first()->image;
        $order->code = "#".date('Y').Str::random(6);
        $order->payment_id =$paymentMethod->id;
        $order->payment_status ="completed";
        $order->amount = $totalPrice;
        $order->sub_total =$subTotalPrice;
        $order->discount_amount = $couponDiscount;
        $order->payment_method = "Paypal";
        $order->status = "pending";
        $order->shipping = "Local pickup";
        $order->pickup_location = $address->city;
        $order->quantity = $quantity;
        $order->coupon_code = ($coupon != null)? $coupon->code :"";
        $order->coupon_type = ($coupon != null)? $coupon->type : "";
        $order->shipping_amount = $shippingFee;
        $order->tax_amount = $tax;
        $order->downloadable = !$containPhysicalItem;
        $order->save();

        foreach ($items as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item->product_id;
            $orderItem->quantity = $item->quantity;
            $orderItem->price = $item->active_price;
            $orderItem->total_price =($item->active_price * $item->quantity);
            $orderItem->color  = $item->color;
            $orderItem->size = $item->size;
            $orderItem->downloadable = $item->downloadable;
            $orderItem->download_file = $item->download_file;
            $orderItem->save();
        }

        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->user_id = $user_id;
        $payment->currency = $currencySymbol;
        $payment->status = "completed";
        $payment->transaction_id = $transactionId;#$request->payment_id;
        $payment->payment_channel = "Paypal";
        $payment->payment_method_id = $paymentMethod->id;
        $payment->amount = $totalPrice;
        $payment->save();

        if ($containPhysicalItem) {
            $orderAddress = new OrderAddress();
            $orderAddress->order_id = $order->id;
            $orderAddress->name = $address->name;
            $orderAddress->phone = $address->phone;
            $orderAddress->email = $address->email;
            $orderAddress->address = $address->address;
            $orderAddress->zip_code = $address->zip_code;
            $orderAddress->country = $address->country;
            $orderAddress->state = $address->state;
            $orderAddress->city = $address->city;
            $orderAddress->save();
        }

        Cart::where('user_id', $user_id)->delete();
        UserCouponCode::where('user_id', $user_id)->delete();

        if ($containPhysicalItem) {
            $shipment = new Shipment();
            $shipment->user_id = $user_id;
            $shipment->order_id = $order->id;
            $shipment->save();
        }

        /* TODO 
        -send email to customer 
        */

         //Update quantity
        foreach ($items as $item) {
            $product = Product::find($item->product_id);
            $product->quantity  = $product->quantity - $item->quantity;
            $product->sold = $product->sold + $item->quantity;
            $product->save();
        }

        $message = "Fullname: ".$address->name."\n";
        $message .="Phone: ".$address->phone."\n"; 
        $message .="Email: ".$address->email."\n"; 
        $message .="Address: ".$address->address."\n";
        $message .="Pament method: Paypal\n";
        $message .="Payment status: COMPLETED\n";  

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function paypalCancel()
    {
        return response()->json([
            'success' => true,
            'message' => 'You cancelled payment successfully'
        ]);
    }
}
