<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function reviews($product_id, Request  $request)
    {
        $page = $request->page;
        $pageSize = $request->page_size;
        $skip = ($page - 1) * $pageSize;

        $reviews = Review::select('reviews.id', 'reviews.star','reviews.comment', 'reviews.created_at','users.image as reviewer_image')
            ->selectRaw(' CONCAT(users.first_name," ",users.last_name) AS reviewer_name')
            ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
            ->where('reviews.product_id', $product_id)
            ->where('reviews.status', 'approved')
            ->groupBy('reviews.id')
            ->skip($skip)
            ->take($pageSize)
            ->get();

        $reviews = $reviews->map(function($review){
             $review->reviewer_image = url('/assets/img/users').'/'.$review->reviewer_image;
            return $review;
        });

        return response()->json($reviews);
    }

    public function reviewStat($product_id)
    {
        $reviewCount = Review::where('product_id', $product_id)->where('reviews.status', 'approved')->count();
        $averageRating = Review::where('product_id', $product_id)->where('reviews.status', 'approved')->avg('star');

        $star_1 = Review::where(['product_id' => $product_id, 'star' => 1])->where('reviews.status', 'approved')->count();
        $star_2 = Review::where(['product_id' => $product_id, 'star' => 2])->where('reviews.status', 'approved')->count();
        $star_3 = Review::where(['product_id' => $product_id, 'star' => 3])->where('reviews.status', 'approved')->count();
        $star_4 = Review::where(['product_id' => $product_id, 'star' => 4])->where('reviews.status', 'approved')->count();
        $star_5 = Review::where(['product_id' => $product_id, 'star' => 5])->where('reviews.status', 'approved')->count();

        $reviewStat = array(
                "total_review" => $reviewCount,
                "average_rating" => $averageRating?:0,
                "star_count" => [
                    1 => $star_1,
                    2 => $star_2,
                    3 => $star_3,
                    4 => $star_4,
                    5 => $star_5
                ]
            );

        return response()->json($reviewStat);
    }
}
