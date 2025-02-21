<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use App\Models\User;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Slider;
use App\Models\Setting;
use App\Models\RecentViewed;

class HomeController extends Controller
{
    public function index()
    {
        $user_id = auth('api')->user()? auth('api')->user()->id : 0;

        $currencySymbol = Setting::where('key', 'currency_code')->value('value');
        //sliders
        $sliders = slider::where('status', 'published')->limit(5)->get();

        $sliders = $sliders->map(function($slider){
             $slider->image = url('/assets/img/sliders').'/'.$slider->image;
            return $slider;
        });
        //categories
        $categories = Category::where('parent_id',0)->get();
        //flash sales
        $flashSale = FlashSale::where('status', 'published')
            ->whereRaw('end_date > CURRENT_DATE')
            ->first();

        $flashSaleProducts = FlashSaleProduct::select('products.id', 'products.name', 'products.image', 'products.price', 'products.quantity')
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
                AVG(IFNULL(reviews.star, 0)) AS average_rating,
                COUNT(reviews.id) AS review_count
            ')
            ->join('products','products.id', '=','flash_sale_products.product_id')
            ->leftJoin('reviews','reviews.product_id', '=', 'products.id')
            ->leftJoin('cart', function(JoinClause $join) use($user_id){
                $join->on('products.id','=','cart.product_id')->where('cart.user_id','=', $user_id);
            })
            ->leftJoin('wishlists', function(JoinClause $join) use($user_id){
                $join->on('products.id','=','wishlists.product_id')->where('wishlists.user_id','=', $user_id);
            })
            ->where('products.status', '=','published')
            ->where('flash_sale_products.flash_sale_id', '=',($flashSale?  $flashSale->id : 0))
            ->groupBy('flash_sale_products.id')
            ->take(20)
            ->get();

        $flashSaleProducts = $flashSaleProducts->map(function($product) use($currencySymbol) {
            $product->percentage_discount = (($product->active_price - $product->price) / $product->price) * 100;
            $product->active_price = $currencySymbol.$product->active_price;
            $product->price = $currencySymbol.$product->price;
            $product->image = url('/assets/img/products').'/'.$product->image;
            return $product;
        });
        //featured products
        $featured = Product::select('products.id', 'products.name', 'products.image', 'products.price', 'products.quantity')
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
            ->where('products.status', '=','published')
            ->groupBy('products.id')
            ->orderBy('products.sold','desc')
            ->take(10)
            ->get();

        $featured = $featured->map(function($featured) use($currencySymbol) {
            $featured->percentage_discount = (($featured->active_price - $featured->price) / $featured->price) * 100;
            $featured->active_price = $currencySymbol.$featured->active_price;
            $featured->price = $currencySymbol.$featured->price;
            $featured->image = url('/assets/img/products').'/'.$featured->image;
            return $featured;
        });
        // featured brands
        $brands = Brand::where('status','published')->limit(20)->get();

        $brands = $brands->map(function($brand){
            $brand->logo = url('/assets/img/brands').'/'.$brand->logo;
            return $brand;
        });
        //Recent viewed
        $recentViewed = RecentViewed::select('products.id', 'products.name', 'products.image', 'products.price', 'products.quantity')
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
                AVG(IFNULL(reviews.star, 0)) AS average_rating,
                COUNT(reviews.id) AS review_count
            ')
            ->join('products','products.id', '=','recent_viewed.product_id')
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
            ->where('products.status', '=','published')
            ->where('recent_viewed.user_id', '=', $user_id)
            ->groupBy('recent_viewed.id')
            ->orderBy('recent_viewed.id','desc')
            ->take(10)
            ->get();

        $recentViewed = $recentViewed->map(function($product) use($currencySymbol) {
            $product->percentage_discount = (($product->active_price - $product->price) / $product->price) * 100;
            $product->active_price = $currencySymbol.$product->active_price;
            $product->price = $currencySymbol.$product->price;
            $product->image = url('/assets/img/products').'/'.$product->image;
            return $product;
        });
        //new arrival
        $newArrival = Product::select('products.id', 'products.name', 'products.image', 'products.price', 'products.quantity')
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
            ->where('products.status', '=','published')
            ->groupBy('products.id')
            ->orderBy('products.id', 'desc')
            ->take(20)
            ->get();

        $newArrival = $newArrival->map(function($product) use($currencySymbol) {
            $product->percentage_discount = (($product->active_price - $product->price) / $product->price) * 100;
            $product->active_price = $currencySymbol.$product->active_price;
            $product->price = $currencySymbol.$product->price;
            $product->image = url('/assets/img/products').'/'.$product->image;
            return $product;
        });


        $featuredFlashSale = $flashSale ?  [
                'flash_sale' => $flashSale,
                'products' => $flashSaleProducts 
            ] : null; 

        return response()->json([
            'sliders' => $sliders,
            'categories' => $categories,
            'featured_flash_sale' => $featuredFlashSale,
            'featured' => $featured,
            'brands' => $brands,
            'recent_viewed' => $recentViewed,
            'new_arrival' => $newArrival
        ]);
    }
}
