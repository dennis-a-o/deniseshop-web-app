<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

use Util; //custom helper

class CustomerController extends Controller
{
    public function index()
    {
        return view("admin.customer.index");
    }

    public function customerList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = User::join("orders", "orders.user_id", "=", "users.id")->count();
        $totalRecordWithFilter = User::where("first_name", "like", "%".$searchValue."%")
            ->orWhere("last_name", "like", "%".$searchValue."%")
            ->join("orders", "orders.user_id", "=", "users.id")
            ->count();

        $customers = User::select("users.*")
            ->Where("users.first_name", "like", "%".$searchValue."%")
            ->orWhere("users.last_name", "like", "%".$searchValue."%")
            ->selectRaw('count(orders.id) as order_count')
            ->selectRaw('sum(orders.amount) as total_spend')
            ->join("orders", "orders.user_id", "=", "users.id")
            ->groupBy("users.id")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($customers as $customer) {

            $data[] = [
                'id' => '<div>
                                <img class="rounded-2 shadow-sm me-2" src="'.url('/assets/img/users').'/'.$customer->image.'" width="30" height="30">
                                <a href="'.url('/admin/user/edit').'/'.$customer->id.'">'.$customer->first_name.' '.$customer->last_name.'</a>
                            </div>',
                'created_at' => date("F j, Y", strtotime($customer->created_at)),
                'email' => '<a href="mailto:'.$customer->email.'">'.$customer->email.'</span>',
                'order_count' => $customer->order_count,
                'total_spend' => Util::currencySymbol().' '.$customer->total_spend,
                'country' => $customer->country,  
            ];
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecord,
            "iTotalDisplayRecords" => $totalRecordWithFilter,
            "aaData" => $data,
        );

        return response()->json($response, 200);
    }
}
