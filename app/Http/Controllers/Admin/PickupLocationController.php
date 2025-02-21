<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PickupLocation;
use App\Models\ShippingZone;
use App\Models\City;

class PickupLocationController extends Controller
{
     public function index()
    {
        return view("admin.pickup-location.index");
    }

    public function pickupList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = PickupLocation::count();
        $totalRecordWithFilter = PickupLocation::where("name", "like", "%".$searchValue."%")
           ->count();

        $pls = PickupLocation::select("pickup_locations.*","cities.name as city", "shipping_zones.name as zone")
            ->where("pickup_locations.name", "like", "%".$searchValue."%")
            ->leftJoin("cities","cities.id","=","pickup_locations.city_id")
            ->leftJoin("shipping_zones","shipping_zones.id","=","pickup_locations.zone_id")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($pls as $pl) {

            $status = match ($pl->status) {
                "draft","pending" => '<span class="badge badge-warning">'.$pl->status.'</span>',
                "published" => '<span class="badge badge-success">published</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$pl->id.'">
                        </div>' ,
                'name' => ' <a href="'.url('/admin/pickup-location/edit').'/'.$pl->id.'">'.$pl->name.'</a>',
                'city' => $pl->city,
                'zone' => $pl->zone,
                'created_at' => date("d/m/Y", strtotime($pl->created_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/pickup-location/edit').'/'.$pl->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-item" data-id="'.$pl->id.'">
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
        $cities = City::get();
        $zones = ShippingZone::get();
        return view("admin.pickup-location.create")
            ->with("cities", $cities)
            ->with("zones", $zones);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:pickup_locations,name",
            "city" => "required|integer",
            "zone" => "required|integer",
            "description" => "required|string",
            "gps_link" => "required|string",
            "status" => "required"
        ]);

        $pl = new PickupLocation;
        $pl->name = $request->name;
        $pl->city_id = $request->city;
        $pl->zone_id = $request->zone;
        $pl->description = $request->description;
        $pl->gps_link = $request->gps_link;
        $pl->status = $request->status;
        $pl->save();
        
        return redirect("/admin/pickup-location/edit"."/".$pl->id)->with("success", "Pickup location added successfully.");
    }

    public function edit($id)
    {
        $pl = PickupLocation::findorfail($id);
        $cities = City::get();
        $zones = ShippingZone::get();
        return view("admin.pickup-location.edit")
            ->with('pickupLocation', $pl)
            ->with("cities", $cities)
            ->with("zones", $zones);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:pickup_locations,name,".$id."",
            "city" => "required|integer",
            "zone" => "required|integer",
            "description" => "required|string",
            "gps_link" => "required|string",
            "status" => "required"
        ]);

        $pl = PickupLocation::findorfail($id);
        $pl->name = $request->name;
        $pl->city_id = $request->city;
        $pl->zone_id = $request->zone;
        $pl->description = $request->description;
        $pl->gps_link = $request->gps_link;
        $pl->status = $request->status;
        $pl->save();
        
        return redirect()->back()->with("success", "Pickup location updated successfully.");
    }

    public function status(Request $request)
    {
        PickupLocation::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Pickup location status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        PickupLocation::whereIn("id", $request->id)->delete();
        
        return response()->json(["message" => "Pickup locations deleted successfully."]);
    }
}
