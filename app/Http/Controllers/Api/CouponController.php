<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\UserCouponCode;

class CouponController extends Controller
{
    public function getCoupons(Request $request)
    {
        $page = $request->page;
        $pageSize = $request->page_size;

        $skip = ($page - 1) * $pageSize;

        $coupons = Coupon::where('status', true)
            ->where('end_date', '>','CURRENT_DATE')
            ->skip($skip)
            ->take($pageSize)
            ->get();

        return response()->json($coupons);
    }

    public function apply(Request $request)
    {
        $user_id = auth('api')->user()->id;

        $coupon = Coupon::where(['code'=> $request->coupon, 'status' => true])
            ->where('end_date', '>','CURRENT_DATE')
            ->first();

        if ($coupon != null) {

            UserCouponCode::where('user_id', $user_id)->delete();

            $userCoupon = new UserCouponCode();
            $userCoupon->user_id = $user_id;
            $userCoupon->coupon_id = $coupon->id;
            $userCoupon->save();
            
             return response()->json([
                'success' => true,
                'message' => "Applied coupon '".$coupon->code."' successfully"
            ]);
           
        }else{
             return response()->json([
                'success' => false,
                'message' => 'Coupon is invalid or expired',
            ], 422);
        }
    }

    public function clear()
    {
        $user_id = auth('api')->user()->id;

        UserCouponCode::where('user_id', $user_id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Coupon code cleared successfully"
        ]);
    }
}
