<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shipment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShipmentHistory;


use Util;//Custom helper

class ShipmentController extends Controller
{
    public function index()
    {
        return view('admin.shipment.index');
    }

    public function shipmentList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Shipment::count();
        $totalRecordWithFilter = Shipment::count();

        $shipments = Shipment::select('shipments.*','orders.code','users.first_name as customer_name')
            ->where("code", "like", "%".$searchValue."%")
            ->orWhere("users.first_name", "like", "%".$searchValue."%")
            ->orWhere("users.last_name", "like", "%".$searchValue."%")
            ->leftJoin("orders", "orders.id","=","shipments.order_id")
            ->leftJoin('users','users.id','=','shipments.user_id')
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($shipments as $key => $shipment) {
            $status = match ($shipment->status) {
                "pending" => '<span class="badge badge-warning">pending</span>',
                "confirmed" => '<span class="badge badge-info">confirmed</span>',
                "processing" => '<span class="badge badge-info">processing</span>',
                "picked" => '<span class="badge badge-success">Picked</span>',
                "delivered" => '<span class="badge badge-success">delivered</span>',
                default => '<span class="badge badge-warning">'.$shipment->status.'</span>',
            };

            $codStatus = match ($shipment->cod_status) {
                "pending" => '<span class="badge badge-warning">pending</span>',
                "completed" => '<span class="badge badge-success">completed</span>',
                default => '<span class="badge badge-info">'.$shipment->cod_status.'</span>',
            };

            $data[] = array(
                "id" => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="id" value="'.$shipment->id.'">
                        </div>',
                "code" => '<a href="'.url('/admin/order/edit').'/'.$shipment->order_id.'">'.$shipment->code.'</a>',
                "customer_name" => $shipment->customer_name,
                "price" => Util::currencySymbol().$shipment->price,
                "cod_status" => $codStatus,
                "status" => $status,
                "created_at" => date("d/m/Y", strtotime($shipment->created_at)),
                "action" => '<a href="'.url('/admin/shipment/edit').'/'.$shipment->id.'">
                                <span class=""><i class="bi-pencil"></i></span>
                            </a>
                            <a href="Javascript:" id="delete-shipment" data-id="'.$shipment->id.'">
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
        $shipment = Shipment::findorfail($id);
        $order = Order::find($shipment->order_id);
        $orderItems = OrderItem::select("order_items.*", 'products.image','products.sku', 'products.name')
            ->where("order_id", $order->id)
            ->leftJoin("products","products.id","=","order_items.product_id")
            ->get();
        $histories = ShipmentHistory::where("shipment_id", $id)->get();

        return view('admin.shipment.edit')
            ->with("shipment", $shipment)
            ->with("order", $order)
            ->with("orderItems", $orderItems)
            ->with("histories", $histories);;
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "shipping_company_name" => "nullable|string",
            "tracking_id" => "nullable|string",
            "tracking_url" => "nullable|url",
            "estimate_date_shipped" => "nullable|date",
            "note" => "nullable|string"
        ]);

        $shipment = Shipment::findorfail($id);
        $shipment->shipping_company_name = $request->shipping_company_name;
        $shipment->tracking_id = $request->tracking_id;
        $shipment->tracking_link = $request->tracking_url;
        $shipment->estimate_date_shipped = $request->estimate_date_shipped;
        $shipment->save();

        return back()->with("success", "Shipment information updated successfully.");
    }

    public function status(Request $request)
    {
        Shipment::whereIn("id", $request->id)->update(["status" => $request->status]);

        foreach($request->id as $id){
            ShipmentHistory::insert([
                "shipment_id" => $id,
                "action" => "update_status",
                "description" => "Changed shipping status to ".$request->status." by ".Auth::user()->first_name." ".Auth::user()->last_name,
                "user_id" => Auth::user()->id,
                "created_at" => now()
            ]);
        }
     
        return response()->json(["message" => "Status shipment(s) updated successfully."]);
    }

    public function destroy(Request $request)
    {
        Shipment::whereIn("id", $request->id)->delete();
        ShipmentHistory::whereIn("shipment_id", $request->id)->delete();

        return response()->json(["message" => "Shipment(s) deleted successfully."]);
    }
}
