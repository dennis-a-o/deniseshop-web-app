<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Payment;
use App\Models\Product;
use Util;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.report.index');
    }

    public function report(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $range = $request->range;

        if ($range == "Today" || $range == "ThisWeek" || $range == "Last7Days") {
            //Group by days
            $customerData = User::selectRaw("WEEK(created_at) as Year, DAYNAME(created_at) as Month, COUNT(id) as Total")
                ->where("role", "user")
                ->whereBetween('created_at',[$fromDate, $toDate])
                ->groupByRaw('WEEK(created_at), DAY(created_at)')
                ->get();
            $orderData = Order::selectRaw("WEEK(created_at) as Year, DAYNAME(created_at) as Month, COUNT(id) as Total")
                ->where('payment_status','completed')
                ->whereBetween('created_at',[$fromDate, $toDate])
                ->groupByRaw('WEEK(created_at), DAY(created_at)')
                ->get();
            $saleData = Order::selectRaw("MONTH(created_at) as Year, DAYNAME(created_at) as Month, SUM(payment_amount) as Total")
                ->where('payment_status','completed')
                ->whereBetween('created_at',[$fromDate, $toDate])
                ->groupByRaw('WEEK(created_at), DAY(created_at)')
                ->get();
        }elseif($range == "Last30Days" || $range == "ThisMonth"){
            //Group by weeks
            $customerData = User::selectRaw("MONTH(created_at) as Year, WEEKDAY(created_at) as Month, COUNT(id) as Total")
                ->where("role", "user")
                ->whereBetween('created_at',[$fromDate, $toDate])
                ->groupByRaw('MONTH(created_at), WEEKDAY(created_at)')
                ->get();
            $orderData = Order::selectRaw("MONTH(created_at) as Year, WEEKDAY(created_at) as Month, COUNT(id) as Total")
                ->where('payment_status','completed')
                ->whereBetween('created_at',[$fromDate, $toDate])
                ->groupByRaw('MONTH(created_at), WEEKDAY(created_at)')
                ->get();
            $saleData = Order::selectRaw("MONTH(created_at) as Year, WEEKDAY(created_at) as Month, SUM(amount) as Total")
                ->where('payment_status','completed')
                ->whereBetween('created_at',[$fromDate, $toDate])
                ->groupByRaw('MONTH(created_at), WEEKDAY(created_at)')
                ->get();
        }else{
            //Group by months
            $customerData = User::selectRaw("YEAR(created_at) as Year, MONTHNAME(created_at) as Month, COUNT(id) as Total")
                ->where("role", "user")
                ->whereBetween('created_at',[$fromDate, $toDate])
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->get();
            $orderData = Order::selectRaw("YEAR(created_at) as Year, MONTHNAME(created_at) as Month, COUNT(id) as Total")
                ->where('payment_status','completed')
                ->whereBetween('created_at',[$fromDate, $toDate])
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->get();
            $saleData = Order::selectRaw("YEAR(created_at) as Year, MONTHNAME(created_at) as Month, SUM(amount) as Total")
                ->where('payment_status','completed')
                ->whereBetween('created_at',[$fromDate, $toDate])
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->get();
        }
            
        $earningCompleted = Payment::where('status','completed')
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->sum('amount');
        $earningPending = Payment::where('status','pending')
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->sum('amount');

        $totalCustomer = User::where("role", "user")
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->count();
        $totalRevenue = Payment::where('status','completed')
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->sum('amount');
        $totalProduct = Product::where('status','published')
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->count();
        $totalOrder = Order::where('status','completed')
            ->whereBetween('created_at',[$fromDate, $toDate])
            ->count();
        $currency = Util::currencySymbol();

        $response = [
            "data" => [
                "customerData" => $customerData,
                "orderData" => $orderData,
                "saleData" => $saleData,
                "currency" => $currency,
                "earningCompleted" => $earningCompleted,
                "earningPending" => $earningPending,
                "totalCustomer" => $totalCustomer,
                "totalRevenue" => $totalRevenue,
                "totalProduct" => $totalProduct,
                "totalOrder" => $totalOrder
            ]
        ]; 

       return response()->json($response, 200);
    }

    public function topSelling(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $products = Product::select('id','name','image','price','quantity','sold')
            ->where('status', 'published')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('sold', 'desc')
            ->limit(10)
            ->get();

        $currency = Util::currencySymbol();

        $response = [
            "data" => [
                "currency" => $currency,
                "products" => $products
            ]
        ];

        return response()->json($response, 200);
    }
}
