<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ShippingZone;
use App\Models\City;

class ShippingZoneController extends Controller
{
    public function index()
    {
        return view("admin.shipping-zone.index");
    }

    public function zoneList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = ShippingZone::count();
        $totalRecordWithFilter = ShippingZone::where("name", "like", "%".$searchValue."%")
           ->count();

        $zones = ShippingZone::select("shipping_zones.*")
            ->selectRaw('count(shipping_zone_locations.id) as city_count')
            ->where("shipping_zones.name", "like", "%".$searchValue."%")
            ->leftJoin("shipping_zone_locations","shipping_zone_locations.zone_id","=","shipping_zones.id")
            ->groupBy("shipping_zones.id")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($zones as $zone) {

            $status = match ($zone->status) {
                "draft","pending" => '<span class="badge badge-warning">'.$zone->status.'</span>',
                "published" => '<span class="badge badge-success">published</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$zone->id.'">
                        </div>' ,
                'name' => ' <a href="'.url('/admin/shipping-zone/edit').'/'.$zone->id.'">'.$zone->name.'</a>',
                'rate' => $zone->rate."%",
                'city_count' => $zone->city_count,
                'created_at' => date("d/m/Y", strtotime($zone->created_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/shipping-zone/edit').'/'.$zone->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-item" data-id="'.$zone->id.'">
                                <span class="">
                                    <i class="bi-trash"></i>
                                </span>
                            </a>',  
            ];
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecord,
            "iTotalDisplayRecords" => $totalRecordWithFilter,
            "aaData" => $data,
        );

        return response()->json($response);
    }

    public function create()
    {
        return view("admin.shipping-zone.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:shipping_zones,name",
            "rate" => "required|integer",
            "description" => "required|string",
            "status" => "required"
        ]);

        $zone = new ShippingZone;
        $zone->name = $request->name;
        $zone->rate = $request->rate;
        $zone->description = $request->description;
        $zone->status = $request->status;
        $zone->save();
        
        return redirect("/admin/shipping-zone/edit"."/".$zone->id)->with("success", "Shipping zone added successfully.");
    }

    public function edit($id)
    {
        $zone = ShippingZone::findorfail($id);
        return view("admin.shipping-zone.edit")
            ->with("zone", $zone);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:shipping_zones,name,".$id."",
            "rate" => "required|integer",
            "description" => "required|string",
            "status" => "required"
        ]);

        $zone = ShippingZone::findorfail($id);
        $zone->name = $request->name;
        $zone->rate = $request->rate;
        $zone->description = $request->description;
        $zone->status = $request->status;
        $zone->save();
        
        return redirect()->back()->with("success", "Shipping zone updated successfully.");
    }

    public function status(Request $request)
    {
        ShippingZone::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Shipping zone status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        ShippingZone::whereIn("id", $request->id)->delete();
        
        return response()->json(["message" => "Shipping zone deleted successfully."]);
    }
}
