<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use App\Models\Wishlist;
use App\Models\Setting;
use Validator;


class WishlistController extends Controller
{
    public function wishlists(Request $request)
    {
        $user_id = auth('api')->user()->id;

        $page = $request->page;
        $pageSize = $request->page_size;

        $skip = ($page - 1) * $pageSize;

        $wishlists = Wishlist::select('wishlists.id','products.id as product_id', 'products.name', 'products.image', 'products.price', 'products.quantity')
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
            ->join('products', 'products.id', '=','wishlists.product_id')
            ->leftJoin('flash_sales', function(JoinClause $join){
                $join->whereRaw('flash_sales.end_date > CURRENT_DATE');
            })
            ->leftJoin('flash_sale_products', function(JoinClause $join){
                $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id')
                    ->on('products.id', '=', 'flash_sale_products.product_id');
            })
            ->where('wishlists.user_id', $user_id)
            ->groupBy('wishlists.id')
            ->skip($skip)
            ->take($pageSize)
            ->get();

        $currencySymbol = Setting::where('key', 'currency_code')->value('value');

        $wishlists = $wishlists->map(function($wishlist)use($currencySymbol){
            $wishlist->percentage_discount = (($wishlist->active_price - $wishlist->price) / $wishlist->price) * 100;
            $wishlist->active_price = $currencySymbol.$wishlist->active_price;
            $wishlist->price = $currencySymbol.$wishlist->price;
            $wishlist->image =  url('/assets/img/products').'/'.$wishlist->image;

            return $wishlist;
        });

        return response()->json($wishlists);
    }

    public function wishlistProducts()
    {
        $user_id = auth('api')->user()->id;
        $products = Wishlist::select('products.id')
            ->join('products', 'products.id', '=','wishlists.product_id')
            ->where('wishlists.user_id', $user_id)
            ->groupBy('wishlists.id')
            ->get();

        $products = $products->map(function($product){
            return $product->id;
        });

        return response()->json($products);
    }

    public function wishlistAdd(Request $request)
    {
        $user_id = auth('api')->user()->id;

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|unique:wishlists,product_id',
        ],
        [
            'product_id.required' => 'Something went wrong try again later',
            'product_id.unique' => 'Already added to wishlists'
        ]
    );

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" =>  $validator->errors()->get("product_id")[0],
            ], 422);
        }

        $wishlist = new wishlist();
        $wishlist->user_id = $user_id;
        $wishlist->product_id = $request->product_id;
        $wishlist->save();

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully.'
        ]);
    }

    public function wishlistRemove($product_id)
    {
        $user_id = auth('api')->user()->id;

        Wishlist::where([
            'product_id' => $product_id,
            'user_id' => $user_id
        ])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist removed successfully.'
        ]);
    }
}
