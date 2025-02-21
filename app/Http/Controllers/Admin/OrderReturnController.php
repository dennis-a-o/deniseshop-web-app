<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;

class OrderReturnController extends Controller
{
    public function index()
    {
        return view("admin.order-return.index");
    }

    public function returnList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = OrderReturn::count();
        $totalRecordWithFilter = OrderReturn::where("code", "like", "%".$searchValue."%")->count();

        $returns = OrderReturn::select('order_returns.*', 'users.first_name', 'users.last_name')
            ->selectRaw('count(order_return_items.id) as product_count')
            ->where("order_returns.code", "like", "%".$searchValue."%")
            ->leftJoin("users","users.id", "=", "order_returns.user_id")
            ->leftJoin("order_return_items","order_return_items.order_return_id", "=","order_returns.id")
            ->groupBy('order_returns.id')
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($returns as $key => $return) {
            $returnStatus = match ($return->return_status) {
                "pending" => '<span class="badge badge-warning">pending</span>',
                "processing" => '<span class="badge badge-info">processing</span>',
                "failed" => '<span class="badge badge-danger">failed</span>',
                "completed" => '<span class="badge badge-success">completed</span>',
                default => '<span class="badge badge-danger">'.$return->return_status.'</span>'
            };

            $data[] = array(
                "id" => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="id" value="'.$return->id.'">
                        </div>',
                "code" => $return->code,
                "first_name" => $return->first_name.' '.$return->last_name,
                "product_count" => $return->product_count,
                "reason" => '<span class="text-info">'.$return->reason.'</span>',
                "return_status" => $returnStatus,
                "created_at" => date("d/m/Y", strtotime($return->created_at)),
                "action" => '<a href="'.url('/admin/order-return/edit').'/'.$return->id.'">
                                <span class=""><i class="bi-pencil"></i></span>
                            </a>
                            <a href="Javascript:" id="delete-item" data-id="'.$return->id.'">
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
        $order = OrderReturn::select("order_returns.*","users.image as customer_image", "orders.customer_name","orders.customer_email","orders.customer_phone","orders.shipping_name","orders.shipping_email","orders.shipping_phone","orders.shipping_country","orders.shipping_address","orders.shipping_city","orders.shipping_state","orders.shipping_zip","orders.payment_amount")
            ->leftJoin("users","users.id","=","order_returns.user_id")
            ->leftJoin("orders","orders.id","=","order_returns.order_id")
            ->findorfail($id);

        $items = OrderReturnItem::select("order_return_items.*", "order_items.color", "order_items.size","products.sku")
            ->where("order_return_id", $id)
            ->leftJoin("order_items", "order_items.product_id","=","order_return_items.product_id")
            ->leftJoin("products","products.id","=","order_return_items.product_id")
            ->groupBy("order_return_items.id")
            ->get();

        return view("admin.order-return.edit")
            ->with("order",$order)
            ->with("items",$items);
    }

    public function status(Request $request)
    {
        OrderReturn::whereIn("id", $request->id)->update(["return_status" => $request->status]);

        return response()->json(["message" => "Order return status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        OrderReturn::whereIn("id", $request->id)->delete();
        OrderReturnItem::whereIn("order_return_id", $request->id)->delete();

        return response()->json(["message" => "Order return deleted successfully."]);
    }
}
