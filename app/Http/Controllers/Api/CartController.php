<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use App\Models\Cart;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\UserCouponCode;
use App\Models\Address;
use App\Models\PaymentMethod;
use Validator;

class CartController extends Controller
{
    public function getCart()
    {
        $user_id = auth('api')->user()->id;

        $carts = Cart::select('cart.*','products.name','products.image','products.price')
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

        $carts = $carts->map(function($cart)use($currencySymbol){
            $cart->percentage_discount = (($cart->active_price - $cart->price) / $cart->price) * 100;
            $cart->total_price_raw = $cart->active_price * $cart->quantity;//numerical
            $cart->total_price = $currencySymbol.$cart->total_price_raw;//string with currecy
            $cart->active_price = $currencySymbol.$cart->active_price;
            $cart->price = $currencySymbol.$cart->price;
            $cart->image =  url('/assets/img/products').'/'.$cart->image;

            return $cart;
        });

        $totalPrice = $carts->sum('total_price_raw');

        $coupon = Coupon::join('user_coupon_code', 'user_coupon_code.coupon_id','=', 'coupons.id')
            ->where('user_coupon_code.user_id', $user_id)
            ->first();

        $couponDiscount = 0;

        if ($coupon != null) {
            $meetMinSpend = $coupon->minimum_spend? ($coupon->minimum_spend <= $totalPrice) : true;

            $meetMaxSpend = $coupon->maximum_spend? ($coupon->maximum_spend >= $totalPrice) : true;

            $meetUsageLimit = $coupon->usage_limit? ($coupon->used < $coupon->usage_limit) : true;

            if ($meetMinSpend && $meetMaxSpend && $meetUsageLimit) {

                switch ($coupon->type) {
                    case 'percent':
                        $couponDiscount = ($totalPrice * $coupon->value) / 100;
                        $couponDiscount = sprintf("%.2f", $couponDiscount);
                        $totalPrice -= $couponDiscount;
                        $totalPrice = sprintf('%.2f', $totalPrice);
                        break;
                    case 'amount':
                        $couponDiscount = $coupon->value;
                        $totalPrice -= $couponDiscount;
                        break;
                    case 'free_shipping':
                          
                        break;
                }
            }
            
        }

        $totalPrice -= $couponDiscount;

        return response()->json([
            'cart_items' => $carts,
            'total_price' => $currencySymbol.$totalPrice,
            'coupon' => $coupon?$coupon->code: null,
            'coupon_type' => $coupon? $coupon->type: null,
            'coupon_discount' => $currencySymbol.$couponDiscount
        ]);
    }

    public function addCart(Request $request)
    {
        $user_id = auth('api')->user()->id;

        $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
                'quantity' => 'nullable|integer',
                'color' => 'nullable|string',
                'size' => 'nullable|string',

            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "An error occured try again later",
            ], 422);
        }

        $cart = Cart::where(['user_id' => $user_id, 'product_id' => $request->product_id])->first();

        if($cart == null){
            $cart = new Cart();
            $cart->user_id = $user_id;
            $cart->product_id = $request->product_id;
            $cart->quantity = $request->quantity? $request->quantity : 1;
            $cart->color = $request->color;
            $cart->size = $request->size;
            $cart->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Product added to cart successfully"
        ]);
    }

    public function increaseQuantity($id)
    {
        $user_id = auth('api')->user()->id;


        $cart = Cart::findorfail($id);

        $product = Product::find($cart->product_id);

        if (($cart->quantity + 1) <= $product->quantity) {
             $cart->quantity = $cart->quantity + 1;
            $cart->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Cart quantity increased successfully"
        ]);
    }

    public function decreaseQuantity($id)
    {
        $user_id = auth('api')->user()->id;

        $cart = Cart::findorfail($id);
        $cart->quantity = $cart->quantity > 1? $cart->quantity - 1 : 1;
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => "Cart quantity decreased successfully"
        ]);

    }

    public function deleteCart($product_id)
    {
        $user_id = auth('api')->user()->id;

        Cart::where(['user_id' => $user_id, 'product_id' => $product_id])->delete();

        return response()->json([
            'success' => true,
            'message' => "Cart removed successfully"
        ]);
    }

    public function clearCart()
    {
        $user_id = auth('api')->user()->id;

        Cart::where('user_id', $user_id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Cart cleared successfully"
        ]);
    }
}
