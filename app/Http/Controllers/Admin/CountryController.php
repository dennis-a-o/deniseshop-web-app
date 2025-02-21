<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        return view("admin.country.index");
    }

    public function countryList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Country::count();
        $totalRecordWithFilter = Country::where("name", "like", "%".$searchValue."%")
           ->count();

        $countries = Country::where("name", "like", "%".$searchValue."%")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($countries as $country) {

            $status = match ($country->status) {
                "draft","pending" => '<span class="badge badge-warning">'.$country->status.'</span>',
                "published" => '<span class="badge badge-success">published</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$country->id.'">
                        </div>' ,
                'name' => ' <a href="'.url('/admin/country/edit').'/'.$country->id.'">'.$country->name.'</a>',
                'isocode' => $country->isocode,
                'created_at' => date("d/m/Y", strtotime($country->created_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/country/edit').'/'.$country->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-item" data-id="'.$country->id.'">
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
        return view("admin.country.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:countries,name",
            "isocode" => "required|string|unique:countries,isocode",
            "status" => "required"
        ]);

        $country = new Country;
        $country->name = $request->name;
        $country->isocode = $request->isocode;
        $country->status = $request->status;
        $country->save();
        
        return redirect("/admin/country/edit"."/".$country->id)->with("success", "Country added successfully.");
    }

    public function edit($id)
    {
        $country = Country::findorfail($id);
        return view("admin.country.edit")->with("country", $country);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:countries,name,".$id."",
            "isocode" => "required|string|unique:countries,isocode,".$id."",
            "status" => "required"
        ]);

        $country = Country::findorfail($id);
        $country->name = $request->name;
        $country->isocode = $request->isocode;
        $country->status = $request->status;
        $country->save();

        return redirect()->back()->with("success", "Country updated successfully.");
    }

    public function status(Request $request)
    {
        Country::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Country status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        Country::whereIn("id", $request->id)->delete();
        
        return response()->json(["message" => "Country deleted successfully."]);
    }
}
