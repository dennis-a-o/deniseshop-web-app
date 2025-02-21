<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;

class SubscriberController extends Controller
{
    public function index()
    {
        return view("admin.subscriber.index");
    }

    public function subscriberList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Subscriber::count();
        $totalRecordWithFilter = Subscriber::where("name", "like", "%".$searchValue."%")
            ->orWhere("email", "like", "%".$searchValue."%")
           ->count();

        $subscribers = Subscriber::where("name", "like", "%".$searchValue."%")
            ->orWhere("email", "like", "%".$searchValue."%")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($subscribers as $subscriber) {

            $status = match ($subscriber->status) {
                "unsubscribed" => '<span class="badge badge-warning">Unsubscribed</span>',
                "subscribed" => '<span class="badge badge-success">subscribed</span>',
            };

            $isUser = match ($subscriber->user_id) {
                0 => '<span class="badge badge-warning">No</span>',
                default => '<span class="badge badge-success">Yes</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$subscriber->id.'">
                        </div>' ,
                'email' => '<a href="mailto:'.$subscriber->email.'">'.$subscriber->email.'</a>',
                'name' => $subscriber->name,
                'user' => $isUser,
                'created_at' => date("d/m/Y", strtotime($subscriber->created_at)),
                'status' => $status,
                'action' => ' <a  href="Javascript:" id="delete-subscriber" data-id="'.$subscriber->id.'">
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

    public function status(Request $request)
    {
        Subscriber::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Subscribers ".$request->status." successfully."]);
    }

    public function destroy(Request $request)
    {
        Subscriber::whereIn("id", $request->id)->delete();

        return response()->json(["message" => "Subscribers deleted successfully."]);
    }
}