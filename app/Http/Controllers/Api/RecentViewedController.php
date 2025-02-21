<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use App\Models\Product;
use App\Models\RecentViewed;
use App\Models\Setting;

class RecentViewedController extends Controller
{
    public function products(Request $request)
    {
        $user_id = auth('api')->user()->id;

        $page = $request->page;
        $pageSize = $request->page_size;

        $skip = ($page - 1) * $pageSize;

        $products  = Product::select('products.id', 'products.name', 'products.image', 'products.price', 'products.quantity')
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
                END AS active_price,
                CASE WHEN cart.product_id IS NOT NULL THEN true ELSE false END AS in_cart,
                CASE WHEN wishlists.product_id IS NOT NULL THEN true ELSE false END AS in_wishlist,
                AVG(IFNULL(reviews.star, 0)) AS average_rating,
                COUNT(reviews.id) AS review_count
            ')
            ->leftJoin('flash_sales', function(JoinClause $join){
                $join->whereRaw('flash_sales.end_date > CURRENT_DATE');
            })
            ->leftJoin('flash_sale_products', function(JoinClause $join){
                $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id')
                    ->on('products.id', '=', 'flash_sale_products.product_id');
            })
            ->leftJoin('reviews','reviews.product_id', '=', 'products.id')
            ->leftJoin('cart', function(JoinClause $join) use($user_id){
                $join->on('products.id','=','cart.product_id')->where('cart.user_id','=', $user_id);
            })
            ->leftJoin('wishlists', function(JoinClause $join) use($user_id){
                $join->on('products.id','=','wishlists.product_id')->where('wishlists.user_id','=', $user_id);
            })
            ->join('recent_viewed','products.id', '=', 'recent_viewed.product_id')
            ->where('recent_viewed.user_id', $user_id)
            ->where('products.status', '=','published')
            ->groupBy('products.id')
            ->orderBy('products.created_at', 'desc')
            ->skip($skip)
            ->take($pageSize)
            ->get();

        $currencySymbol = Setting::where('key', 'currency_code')->value('value');

         $products = $products->map(function($product) use($currencySymbol) {
            $product->percentage_discount = (($product->active_price - $product->price) / $product->price) * 100;
            $product->active_price = $currencySymbol.$product->active_price;
            $product->price = $currencySymbol.$product->price;
            $product->image = url('/assets/img/products').'/'.$product->image;
            return $product;
        });

        return response()->json($products);
    }

    public function clear(Request $request)
    {
        $user_id = auth('api')->user()->id;

        RecentViewed::where('user_id', $user_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recent viewed cleared successfully'
        ]);
    }
}
