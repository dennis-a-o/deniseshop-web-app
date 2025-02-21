<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Country;

class StateController extends Controller
{
    public function index()
    {
        return view("admin.state.index");
    }

    public function stateList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = State::count();
        $totalRecordWithFilter = State::where("name", "like", "%".$searchValue."%")
           ->count();

        $states = State::select("states.*","countries.name as country")
            ->where("states.name", "like", "%".$searchValue."%")
            ->leftJoin("countries","countries.id","=","states.country_id")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($states as $state) {

            $status = match ($state->status) {
                "draft","pending" => '<span class="badge badge-warning">'.$state->status.'</span>',
                "published" => '<span class="badge badge-success">published</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$state->id.'">
                        </div>' ,
                'name' => ' <a href="'.url('/admin/state/edit').'/'.$state->id.'">'.$state->name.'</a>',
                'country' => $state->country,
                'created_at' => date("d/m/Y", strtotime($state->created_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/state/edit').'/'.$state->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-item" data-id="'.$state->id.'">
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
        return view("admin.state.create")->with("countries", $countries);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:states,name",
            "country" => "required|integer",
            "status" => "required"
        ]);

        $state = new State;
        $state->name = $request->name;
        $state->country_id = $request->country;
        $state->status = $request->status;
        $state->save();
        
        return redirect("/admin/state/edit"."/".$state->id)->with("success", "State added successfully.");
    }

    public function edit($id)
    {
        $state = State::findorfail($id);
        $countries = Country::get();

        return view("admin.state.edit")
            ->with("state", $state)
            ->with("countries", $countries);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:states,name,".$id."",
            "country" => "required|integer",
            "status" => "required"
        ]);

        $state = State::findorfail($id);
        $state->name = $request->name;
        $state->country_id = $request->country;
        $state->status = $request->status;
        $state->save();

        return redirect()->back()->with("success", "State updated successfully.");
    }

    public function status(Request $request)
    {
        State::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "State status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        State::whereIn("id", $request->id)->delete();
        
        return response()->json(["message" => "State deleted successfully."]);
    }
}
