<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Mail\OrderConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\OrderHistory;
use App\Models\OrderAddress;
use App\Models\User;
use Exception;
class OrderController extends Controller
{
    public function index()
    {
        return view("admin.order.index");
    }

    public function orderList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Order::count();
        $totalRecordWithFilter = Order::where("code", "like", "%".$searchValue."%")->count();

        $orders = Order::select('orders.id','orders.code', 'orders.amount', 'orders.payment_status', 'orders.payment_method','orders.created_at', 'orders.status','users.image as user_image', 'users.first_name','users.last_name')
            ->where("orders.code", "like", "%".$searchValue."%")
            ->leftJoin("users","users.id", "=", "orders.user_id")
            //->groupBy([''])
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($orders as $key => $order) {
            $status = match ($order->status) {
                "pending" => '<span class="badge badge-warning">pending</span>',
                "confirmed" => '<span class="badge badge-info">confirmed</span>',
                "processing" => '<span class="badge badge-info">processing</span>',
                "completed" => '<span class="badge badge-success">completed</span>',
                "cancelled" => '<span class="badge badge-danger">cancelled</span>',
                "refunded" => '<span class="badge badge-danger">refunded</span>'
            };

            $paymentStatus = match ($order->payment_status) {
                "pending" => '<span class="badge badge-warning">pending</span>',
                "processing" => '<span class="badge badge-info">processing</span>',
                "failed" => '<span class="badge badge-danger">failed</span>',
                "completed" => '<span class="badge badge-success">completed</span>',
                "refunded" => '<span class="badge badge-danger">refunded</span>'
            };

            $data[] = array(
                "id" => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="id" value="'.$order->id.'">
                        </div>',
                "code" => '<a href="'.url('/admin/order/edit').'/'.$order->id.'"><h6>'.$order->code.'</h6></a>',
                "customer_name" => '<div class="d-flex align-items-center">
                                        <img class="rounded-circle shadow-sm me-2" src="'.url('/assets/img/users').'/'.$order->user_image.'" width="30" height="30">
                                        <span>'.$order->first_name.' '.$order->last_name.'</span>
                                    </div>',
                "amount" => 'Ksh '.$order->amount,
                "payment_status" => $paymentStatus,
                "payment_method" => $order->payment_method,
                "status" => $status,
                "created_at" => date("d/m/Y", strtotime($order->created_at)),
                "action" => '<a href="'.url('/admin/order/edit').'/'.$order->id.'">
                                <span class=""><i class="bi-eye"></i></span>
                            </a>
                            <a href="Javascript:" id="delete-order" data-id="'.$order->id.'">
                                <span class="ms-3"><i class="bi-trash"></i></span>
                            </a>'
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecord,
            "iTotalDisplayRecords" => $totalRecordWithFilter,
            "aaData" => $data,
        );

        return response()->json($response, 200);
    }

    public function edit($id)
    {
        $order = Order::findorfail($id);
        $order_items = OrderItem::select('order_items.*', 'products.image', 'products.name', 'products.sku')
            ->where('order_id', $id)
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->get();
        $order_address = OrderAddress::where("order_id", $id)->first();
        $histories = OrderHistory::where("order_id", $id)->get();
        $order_user = User::select('*')->where('id',$order->user_id)->first();


        return view("admin.order.detail")
            ->with("order", $order)
            ->with("order_items", $order_items)
            ->with('order_address', $order_address)
            ->with('order_user', $order_user)
            ->with("histories", $histories);
    }

    public function confirm(Request $request)
    {
        Order::where("id", $request->id)->update(["is_confirmed" => true]);

        OrderHistory::insert([
            "order_id" => $request->id,
            "action" => "confirm_order",
            "description" => "Order verified by ".Auth::user()->first_name." ".Auth::user()->last_name,
            "user_id" => Auth::user()->id,
            "created_at" => now(),
        ]);

        $order = Order::select('orders.*','users.email','users.first_name')
            ->where('orders.id', $request->id)
            ->join('users', 'users.id','=', 'orders.user_id')
            ->groupBy('orders.id')
            ->first();

       try {
            Mail::to($order->email)->send(new OrderConfirmed($order));
       } catch (Exception $e) {
           
       }

        return response()->json(["message" => "Order confirmed successfully."]);
    }

    public function confirmPayment(Request $request)
    {
        Payment::where("order_id", $request->id)->update(["status" => "completed"]);
        Order::where("id", $request->id)->update(["payment_status" => "completed"]);

        OrderHistory::insert([
            "order_id" => $request->id,
            "action" => "confirm_paymentr",
            "description" => "Payment confirmed by ".Auth::user()->first_name." ".Auth::user()->last_name,
            "user_id" => Auth::user()->id,
            "created_at" => now(),
        ]);

        \App\Models\AdminNotification::insert([
            "title" => "Confirmed order",
             "action_label" => "View",
             "action_url" => "/admin/order/edit/".$request->id,
             "description" => Auth::user()->username." confirmed the order",
             "created_at" => now()
        ]);

        return response()->json(["message" => "Order payment confirmed successfully."]);
    }

    public function downloadAccess(Request $request)
    {
        Order::where("id", $request->id)->update(["download_access" => $request->access]);
        OrderItem::where("order_id", $request->id)
            ->where("downloadable", true)
            ->update(["access" => $request->access]);

        return response()->json(["message" => "Order download access updated successfully."]);
    }

    public function refund(Request $request)
    {
        //TODO payment gateway semd money to customer
        Order::where("id", $request->id)->update(["status" => "refunded", "payment_status" => "refunded"]);

        \App\Models\AdminNotification::insert([
            "title" => "Order refund",
             "action_label" => "View",
             "action_url" => "/admin/order/edit/".$request->id,
             "description" => Auth::user()->username." refunded the order",
             "created_at" => now()
        ]);

        return response()->json(["message" => "Order refunded successfully."]);
    }

    public function status(Request $request)
    {
        Order::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Order status updated successfully."]);
    }

    public function shippingAddressUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|max:255',
            'state' => 'required|max:255',
            'zip_code' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        $order = OrderAddress::find($request->id);
        $order->name = $request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->city = $request->city;
        $order->country = $request->country;
        $order->state = $request->state;
        $order->zip_code = $request->zip_code;
        $order->save();

        return response()->json(["message" => "Order shipping address updated successfully."]);
    }

    public function destroy(Request $request)
    {
        foreach($request->id as $id){
            Order::where("id", $id)->delete();
            OrderItem::where("order_id", $id)->delete();
            OrderHistory::where("order_id", $id)->delete();
            OrderAddress::where("order_id", $id)->delete();
        }

        return response()->json(["message" => "Order deleted successfully."]);
    }
}
