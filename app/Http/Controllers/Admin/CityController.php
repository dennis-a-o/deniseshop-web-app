<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\State;
use App\Models\Country;

class CityController extends Controller
{
    public function index()
    {
        return view("admin.city.index");
    }

    public function cityList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = city::count();
        $totalRecordWithFilter = city::where("name", "like", "%".$searchValue."%")
           ->count();

        $cities = City::select("cities.*","countries.name as country", "states.name as state")
            ->where("cities.name", "like", "%".$searchValue."%")
            ->leftJoin("countries","countries.id","=","cities.country_id")
            ->leftJoin("states","states.id","=","cities.state_id")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($cities as $city) {

            $status = match ($city->status) {
                "draft","pending" => '<span class="badge badge-warning">'.$city->status.'</span>',
                "published" => '<span class="badge badge-success">published</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$city->id.'">
                        </div>' ,
                'name' => ' <a href="'.url('/admin/city/edit').'/'.$city->id.'">'.$city->name.'</a>',
                'country' => $city->country,
                'state' => $city->state,
                'created_at' => date("d/m/Y", strtotime($city->created_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/city/edit').'/'.$city->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-item" data-id="'.$city->id.'">
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
        $countries = Country::get();
        $states = State::get();
        return view("admin.city.create")
            ->with("countries", $countries)
            ->with("states", $states);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:cities,name",
            "country" => "required|integer",
            "state" => "required|integer",
            "status" => "required"
        ]);

        $city = new City;
        $city->name = $request->name;
        $city->country_id = $request->country;
        $city->state_id = $request->state;
        $city->status = $request->status;
        $city->save();
        
        return redirect("/admin/city/edit"."/".$city->id)->with("success", "City added successfully.");
    }

    public function edit($id)
    {
        $city = City::findorfail($id);
        $countries = Country::get();
        $states = State::get();
        return view("admin.city.edit")
            ->with("city", $city)
            ->with("countries", $countries)
             ->with("states", $states);
    }

    public function update(Request $request, $id)
    {
       
        $validated = $request->validate([
            "name" => "required|string|unique:cities,name,".$id."",
            "country" => "required|integer",
            "state" => "required|integer",
            "status" => "required"
        ]);

        $city = City::findorfail($id);
        $city->name = $request->name;
        $city->country_id = $request->country;
        $city->state_id = $request->state;
        $city->status = $request->status;
        $city->save();

        return redirect()->back()->with("success", "City updated successfully.");
    }

    public function status(Request $request)
    {
        City::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "City status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        City::whereIn("id", $request->id)->delete();
        
        return response()->json(["message" => "City deleted successfully."]);
    }
}
