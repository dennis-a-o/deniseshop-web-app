<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;
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

class CheckoutController extends Controller
{
    public function checkout()
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

        if ($items->count() <= 0) {
            return response()->json([],404);
        }


        $currencySymbol = Setting::where('key', 'currency_code')->value('value');

        $items = $items->map(function($cart)use($currencySymbol){
            $cart->percentage_discount = (($cart->active_price - $cart->price) / $cart->price) * 100;
            $cart->total_price_raw = $cart->active_price * $cart->quantity;//numerical
            $cart->total_price = $currencySymbol.$cart->total_price_raw;//string with currecy
            $cart->active_price = $currencySymbol.$cart->active_price;
            $cart->price = $currencySymbol.$cart->price;
            $cart->image =  url('/assets/img/products').'/'.$cart->image;

            return $cart;
        });


        $containPhysicalItem = $items->contains(function ($item) {
            return ($item->downloadable == 0);
        });

        $totalPrice = 0;

        $subTotalPrice = $items->sum('total_price_raw');

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

        /* TODO */
        $tax = 0;
        $shippingFee = 0;

        $totalPrice += $subTotalPrice;
        $totalPrice += $tax;
        $totalPrice += $shippingFee;

        $totalPrice -= $couponDiscount;

        $totalPrice = sprintf('%.2f', $totalPrice);
        $couponDiscount = sprintf("%.2f", $couponDiscount);

        $paymentMethod = PaymentMethod::select('id','name','slug','logo')->where('status','active')->get();
        $address = Address::where(['user_id' => $user_id, 'default' => true])->first();


        $paymentMethod = $paymentMethod->map(function($payment){
            $payment->logo = url('/assets/img/payment-channel').'/'.$payment->logo;
            return $payment;
        });

        return response()->json([
            'items' => $items,
            'address' => $address,
            'payment_methods' => $paymentMethod,
            'contain_physical_item'=> $containPhysicalItem, 
            'sub_total' => $currencySymbol.$subTotalPrice,
            'coupon_discount' => ($coupon != null)? $currencySymbol.$couponDiscount : null,
            'shipping_fee' => ($isFreeShipping)? $currencySymbol."0" : $currencySymbol.$shippingFee ,
            'tax' => $currencySymbol.$tax,
            'total_amount' => $currencySymbol.$totalPrice
        ]);
    }

    public function placeOrder()
    {
        $user_id = auth('api')->user()->id;

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

        if ($items->count() <= 0) {
            return response()->json([],404);
        }

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

        $paymentMethod = PaymentMethod::where(['status' => 'active', 'slug' => 'cash-on-delivery'])->first();

        $address = Address::where(['user_id' => $user_id, 'default' => true])->first();
        
        $order = new Order();
        $order->user_id = $user_id;
        $order->name = $items->first()->name;
        $order->image = $items->first()->image;
        $order->code = "#".date('Y').Str::random(6);
        $order->payment_id =$paymentMethod->id;
        $order->payment_status ="pending";
        $order->amount = $totalPrice;
        $order->sub_total =$subTotalPrice;
        $order->discount_amount = $couponDiscount;
        $order->payment_method = $paymentMethod->name;
        $order->status = "pending";
        $order->shipping = "Local pickup";
        $order->pickup_location = $address->city;
        $order->quantity = $quantity;
        $order->coupon_code = ($coupon != null)? $coupon->code: "";
        $order->coupon_type = ($coupon != null)? $coupon->type: "";
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
        $payment->status = "pending";
        $payment->transaction_id = "";
        $payment->payment_channel = "Cash On Delivery";
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
        $message .="Pament method: Cash On Delivery\n";
        $message .="Payment status: PENDING\n";  

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
