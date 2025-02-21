<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Setting;
use App\Models\Review;
use Validator;
use PDF;


class OrderController extends Controller
{
    public function getOrders(Request $request)
    {
        $user_id = auth('api')->user()->id;

        $page = $request->page;
        $pageSize = $request->page_size;

        $skip = ($page - 1) * $pageSize;

        $orders = Order::where('user_id', $user_id)
            ->skip($skip)
            ->take($pageSize)
            ->get();

        $orders = $orders->map(function($order){
            $order->image = url('assets/img/products').'/'.$order->image;
            return $order;
        });

        return response()->json($orders);
    }

    public function getOrderDetail($id)
    {
        $user_id = auth('api')->user()->id;

        $order = Order::select('orders.*', 'shipments.status as shipping_status')
            ->leftJoin('shipments', 'shipments.order_id', '=', 'orders.id')
            ->findorfail($id);

        $currency = Setting::where('key', 'currency_code')->value('value');

        $orderAddress = OrderAddress::where('order_id', $id)->first();

        $orderItems = OrderItem::select('order_items.*','products.id as product_id','products.name','products.image')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->where('order_id', $id)
            ->get();

        $orderItems = $orderItems->map(function($item)use($currency){
            $item->price = $currency.$item->price;
            $item->image = url('assets/img/products').'/'.$item->image;
            return $item;
        });

        $containPhysicalItem = $orderItems->contains(function ($item) {
            return ($item->downloadable == 0);
        });

        return response()->json([
            'id' => $order->id,
            'code' => $order->code,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'amount' => $currency.$order->amount,
            'tax' => $currency.$order->tax_amount,
            'shipping_fee' => $currency.$order->shipping_amount,
            'discount' => $currency.$order->discount_amount,
            'products' => $orderItems,
            'order_address' => $orderAddress,
            'shipping_status' => $order->shipping_status,
            'shipping' => $order->shipping,
            'date' => $order->created_at,
            'contain_physical_product' => $containPhysicalItem,

        ]);
    }

    public function addReview($item_id, Request $request)
    {
        $user_id = auth('api')->user()->id;

        $validator = Validator::make($request->all(),[
            'rating' => 'required|integer|digits_between:1,5',
            'review' => 'required|string',
        ]);

       if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => Arr::join($validator->errors()->all(),"\n"),
            ], 422);
        }

        $orderItem = OrderItem::findorfail($item_id);

        if ($orderItem->rated) {
            return response()->json([
                'success' => true,
                'message' => "You already rated the product"
            ]);
        }

        $review = new Review();
        $review->user_id = $user_id;
        $review->product_id = $orderItem->product_id;
        $review->star = $request->rating;
        $review->comment = $request->review;
        $review->save();

        $orderItem->rated = true;
        $orderItem->save();

        return response()->json([
            'success' => true,
            'message' => "Review added successfully and will appear to public after moderation"
        ]);
    }

    public function downloadInvoice($order_id, Request $request)
    {
        $order = Order::find($order_id);
        $setting = Setting::whereIn('key',['contact_address','contact_phone','contact_email'])->get();
        $order->orderAddress = OrderAddress::where('order_id', $order->id)->first();
    

        $business =  [
            'address' => $setting[2]->value,
            'phone' => $setting[1]->value,
            'email' => $setting[0]->value
        ];

        $pdf = PDF::loadView('templates.invoice', compact('order','business'));

        if ($request->has('type') && $request->type === "print") {
            return $pdf->stream('invoice.pdf');
        }
        
        return $pdf->download('invoice.pdf');
    }

    public function downloadOrderItemFile($item_id)
    {
        $orderItem = OrderItem::findorfail($item_id);

        return Storage::download($orderItem->download_file);
    }
}
