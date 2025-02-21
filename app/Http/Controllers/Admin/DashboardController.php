<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use App\Models\Order;
use App\Models\Payment;
use Util;

class DashboardController extends Controller
{

    public function index()
    {
        $productCount = Product::count();
        $customerCount = User::where('role','user')->count();
        $reviewCount = Review::count();
        $orderCount = Order::count();
        
        return view('admin.dashboard')
            ->with("productCount", $productCount)
            ->with("customerCount", $customerCount)
            ->with("reviewCount", $reviewCount)
            ->with("orderCount", $orderCount);
    }

    public function currentMonthData(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $revenueData = Order::selectRaw("MONTH(created_at) as month, WEEKDAY(created_at) as weekday, SUM(amount) as total")
            ->where('payment_status','completed')
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->groupByRaw('MONTH(created_at), WEEKDAY(created_at)')
            ->get();

        $totalCompleted = Payment::where('status','completed')
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->sum('amount');
        $totalPending = Payment::where('status','pending')
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->sum('amount');
        $totalRefunded = Payment::where('status','refunded')
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->sum('amount');

        $currency = Util::currencySymbol();

        $response = [
            "data" => [
                "currency" => $currency,
                "revenueData" => $revenueData,
                "totalCompleted" => $totalCompleted,
                "totalPending" => $totalPending,
                "totalRefunded" => $totalRefunded
            ]
        ];

        return response()->json($response, 200);
    }
}
