<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Setting;

class BrandController extends Controller
{
    public function brands(Request $request)
    {

        $page = $request->page;
        $pageSize = $request->page_size;
        $skip = ($page - 1) * $pageSize;

        $brands = Brand::skip($skip)
            ->take($pageSize)
            ->get();

        $brands = $brands->map(function($brand){
             $brand->logo = url('/assets/img/brands').'/'.$brand->logo;
            return $brand;
        });


        return response()->json($brands);
    }

    public function brandCategory($category_id)
    {
        $brands = Brand::leftJoin('category_brand', 'brands.id', '=', 'category_brand.brand_id')
            ->where('category_brand.category_id', $category_id)
            ->get();

        $brands = $brands->map(function($brand){
             $brand->logo = url('/assets/img/brands').'/'.$brand->logo;
            return $brand;
        });

        return response()->json($brands);
    }

    public function brand($id)
    {
        $brand = Brand::findorfail($id);
        $brand->logo = url('/assets/img/brands').'/'.$brand->logo;

        return response()->json($brand);
    }

    public function brandProducts($brand_id, Request $request)
    {
        $user_id = auth('api')->user()? auth('api')->user()->id : 0;

        $page = $request->page;
        $pageSize = $request->page_size;
        $sortBy = $request->sort_by;
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price; 
        $categories = $request->categories;
        $colors  = $request->colors;
        $sizes = $request->sizes;
        $rating = $request->rating;

        $skip = ($page - 1) * $pageSize;

        $sortColumn = "products.created_at";
        $sortOrder = "desc";

        switch ($sortBy) {
            case 'date_asc':
                $sortColumn = "products.created_at";
                $sortOrder = "asc";
                break;
            case 'date_desc':
                $sortColumn = "products.created_at";
                $sortOrder = "desc";
                break;
            case 'name_asc':
                $sortColumn = "products.name";
                $sortOrder = "asc";
                break;
            case 'name_desc':
                $sortColumn = "products.name";
                $sortOrder = "desc";
                break;
            case 'price_asc':
                $sortColumn = "products.price";
                $sortOrder = "asc";
                break;
            case 'price_desc':
                $sortColumn = "products.price";
                $sortOrder = "desc";
                break;
            case 'rating_asc':
                $sortColumn = "average_rating";
                $sortOrder = "asc";
                break;
            case 'rating_desc':
                $sortColumn = "average_rating";
                $sortOrder = "desc";
                break;
            default:
                break;
        }

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
            ->leftJoin('product_category', 'products.id', '=', 'product_category.product_id')
            ->leftJoin('categories', 'product_category.category_id', '=', 'categories.id')
            ->leftJoin('brands','products.brand_id','=','brands.id')
            ->leftJoin('product_attributes','products.id', '=', 'product_attributes.product_id')
            ->where('products.brand_id', $brand_id)//HERE
            ->where('products.status', '=','published')
            ->whereBetween('products.price',[$minPrice,$maxPrice])
            ->when($rating != null,function($query)use($rating){
                $query->havingRaw('AVG(IFNULL(reviews.star, 1)) >= ?', [$rating]);
            })
            ->when(($categories != null) && ($categories > 0), function($query)use($categories){
                $query->whereIn("categories.name", $categories);
            })
            ->when(($colors != null) && ($colors > 0), function($query)use($colors){
                $query->whereIn("product_attributes.name", $colors);
            })
            ->when(($sizes != null) && ($sizes > 0), function($query)use($sizes){
                $query->whereIn("product_attributes.name", $sizes);
            })
            ->groupBy('products.id')
            ->orderBy($sortColumn, $sortOrder)
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
}
