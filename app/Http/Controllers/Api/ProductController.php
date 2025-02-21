<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductAttribute;
use App\Models\Setting;
use App\Models\Review;
use App\Models\RecentViewed;

class ProductController extends Controller
{
    public function products(Request $request)
    {
        $user_id = auth('api')->user()? auth('api')->user()->id : 0;

        $query = $request->q;
        $page = $request->page;
        $pageSize = $request->page_size;
        $sortBy = $request->sort_by;
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price; 
        $categories = $request->categories;
        $brands = $request->brands;
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
            ->where('products.status', '=','published')
            ->where("products.name", "like", "%".$query."%")
            ->whereBetween('products.price',[$minPrice,$maxPrice])
            ->when($rating != null,function($query)use($rating){
                $query->havingRaw('AVG(IFNULL(reviews.star, 1)) >= ?', [$rating]);
            })
            ->when(($categories != null) && ($categories > 0), function($query)use($categories){
                $query->whereIn("categories.name", $categories);
            })
            ->when(($brands != null) && ($brands > 0), function($query)use($brands){
                $query->whereIn("brands.name", $brands);
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

    public function productDetail($id)
    {
        $user_id = auth('api')->user()? auth('api')->user()->id : 0;

         $product  = Product::select('products.*')
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
            ->where('products.id', $id)
            ->groupBy('products.id')
           ->first();

        $currencySymbol = Setting::where('key', 'currency_code')->value('value');
        $product->percentage_discount = (($product->active_price - $product->price) / $product->price) * 100;
        $product->active_price = $currencySymbol.$product->active_price;
        $product->price = $currencySymbol.$product->price;

        $product->image = url('/assets/img/products').'/'.$product->image;
        $galleries = json_decode($product->gallery);

        $galleryArray = Arr::map($galleries, function ($value) {
            return url('/assets/img/products').'/'.$value;
        });

        $product->gallery = $galleryArray;

        $product->brand = Brand::where('id',$product->brand_id)->first();
        
        $product->categories = Category::select('categories.*')
            ->join('product_category','categories.id', '=', 'product_category.category_id')
            ->where('product_category.product_id', $product->id)
            ->get();

        $product->color = ProductAttribute::selectRaw('CONCAT(name,"=",value ) AS color')
            ->where('product_attributes.type', 'color')
            ->where('product_id', $product->id)
            ->get()
            ->map(function($item){
                return $item->color;
            });

       
        $product->size = ProductAttribute::select('name')
            ->where('product_attributes.type', 'size')
            ->where('product_id', $product->id)
            ->get()
            ->map(function($item){
                return $item->name;
            });

        $reviews = Review::select('reviews.id', 'reviews.star','reviews.comment', 'reviews.created_at','users.image as reviewer_image')
            ->selectRaw(' CONCAT(users.first_name," ",users.last_name) AS reviewer_name')
            ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
            ->where('reviews.product_id', $product->id)
            ->where('reviews.status', 'approved')
            ->groupBy('reviews.id')
            ->take(3)
            ->get();

        $reviews = $reviews->map(function($review){
             $review->reviewer_image = url('/assets/img/users').'/'.$review->reviewer_image;
            return $review;
        });

        $totalReview = Review::where('product_id', $product->id)->count();
        $averageRating = Review::where('product_id', $product->id)->avg('star');

        $star_1 = Review::where(['product_id' => $product->id, 'star' => 1])->count();
        $star_2 = Review::where(['product_id' => $product->id, 'star' => 2])->count();
        $star_3 = Review::where(['product_id' => $product->id, 'star' => 3])->count();
        $star_4 = Review::where(['product_id' => $product->id, 'star' => 4])->count();
        $star_5 = Review::where(['product_id' => $product->id, 'star' => 5])->count();

        return response()->json([
            "product" => $product,
            "reviewStat" => array(
                "total_review" => $totalReview,
                "average_rating" => $averageRating?: 0,
                "star_count" => array(
                    1 => $star_1,
                    2 => $star_2,
                    3 => $star_3,
                    4 => $star_4,
                    5 => $star_5
                ),
            ),
            "reviews" => $reviews
        ]);
    }

    public function productViewed($product_id)
    {
        $user_id = auth('api')->user()? auth('api')->user()->id : 0;

        if($user_id > 0){
            RecentViewed::where('user_id', $user_id)
                ->where('product_id', $product_id)
                ->delete();
                
            $recentViewed = new RecentViewed();
            $recentViewed->product_id = $product_id;
            $recentViewed->user_id = $user_id;
            $recentViewed->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'ok'
        ]);
    }

    public function productFilter(Request $request)
    {
        if($request->has("category") && ($request->category != null) && ($request->category > 0)){
            $categories = [];
        }else{
            $categories = Category::get()
                ->map(function($item){
                    return $item->name;
                });
        }

        if ($request->has("brand") && ($request->brand != null) && ($request->brand > 0)) {
            $brands = [];
        }else{
            $brands = Brand::get()
                ->map(function($item){
                    return $item->name;
                });
        }

        $colors = ProductAttribute::select('name')
            ->where('type', 'color')
            ->distinct()
            ->get()
            ->map(function($item){
                return $item->name;
            });

        $sizes = ProductAttribute::select('name')
            ->where('type', 'size')
            ->distinct()
            ->get()
            ->map(function($item){
                return $item->name;
            });

        $maxPrice = Product::max('price');
        $currency = Setting::where('key', 'currency_code')->value('value');


        return response()->json([
            "categories" => $categories,
            "brands" => $brands,
            "colors" => $colors,
            "sizes" => $sizes,
            "max_price" => $maxPrice,
            "currency" => $currency?: '$',
        ]);
    }
   
}
