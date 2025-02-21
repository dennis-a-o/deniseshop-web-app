<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Util;//Custom helper

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.coupon.index');
    }

    public function couponList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Coupon::count();
        $totalRecordWithFilter = Coupon::where("code", "like", "%".$searchValue."%")->count();

        $coupons = Coupon::select("coupons.*")
            ->where("code", "like", "%".$searchValue."%")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($coupons as $key => $coupon) {
            $status = match ($coupon->status) {
                1 => '<span class="badge badge-success">published</span>',
                default => '<span class="badge badge-warning">unpublished</span>',
            };

            $type = match($coupon->type){
                "percent" => "Percentage (%)",
                "amount" => "Amount",
                "free_shipping" => "Free shipping",
            };

            $value = match ($coupon->type) {
                "percent" => $coupon->value."%",
                default => Util::currencySymbol().$coupon->value,
            };

            $usage_limit = ($coupon->usage_limit > 0)? $coupon->usage_limit: "unlimited";

            $data[] = array(
                "id" => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="id" value="'.$coupon->id.'">
                        </div>',
                "code" => '<a href="'.url('/admin/coupon/edit').'/'.$coupon->id.'">'.$coupon->code.'</a>',
                "type" => $type,
                "value" => $value,
                "description" => $coupon->description,
                "used" => $coupon->used." of ".$usage_limit,
                "status" => $status,
                "end_date" => date("d/m/Y", strtotime($coupon->end_date)),
                "action" => '<a href="'.url('/admin/coupon/edit').'/'.$coupon->id.'">
                                <span class=""><i class="bi-pencil"></i></span>
                            </a>
                            <a href="Javascript:" id="delete-coupon" data-id="'.$coupon->id.'">
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

    public function create()
    {
        return view("admin.coupon.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "code" => "required|string|max:9|unique:coupons,code",
            "description" => "nullable|string",
            "type" => "required|string",
            "value" => "required|integer",
            "user_limit" => "nullable|integer",
            "usage_limit" => "nullable|integer",
            "minimum_spend" => "nullable|integer",
            "maximum_spend" => "nullable|integer",
            "start_date" => "required|date",
            "end_date" => "required|date",
            "status" => "nullable|boolean"
        ]);

        $input = $request->except('_token');
        $couponId = Coupon::insertGetId($input);

        return redirect("/admin/coupon/edit/".$couponId)->with("success", "Coupon added successfully");
    }

    public function edit($id)
    {
        $coupon = Coupon::findorfail($id);
        return view("admin.coupon.edit")->with("coupon", $coupon);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "code" => "required|string|max:9|unique:coupons,code,".$id."",
            "description" => "nullable|string",
            "type" => "required|string",
            "value" => "required|integer",
            "user_limit" => "nullable|integer",
            "usage_limit" => "nullable|integer",
            "minimum_spend" => "nullable|integer",
            "maximum_spend" => "nullable|integer",
            "start_date" => "required|date",
            "end_date" => "required|date",
            "status" => "nullable|boolean"
        ]);

        $input = $request->except('_token');
        Coupon::where("id", $id)->update($input);

        return redirect()->back()->with("success", "Coupon updated successfully");
    }

    public function status(Request $request)
    {
        coupon::whereIn("id", $request->id)->update(["status" => $request->status]);
        return response()->json(["message" => "Coupon status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        coupon::whereIn("id", $request->id)->delete();
        return response()->json(["message" => "Coupon(s) deleted successfully."]);
    }
}
