<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{   
    public function index()
    {
        $paypal = PaymentMethod::where('name', 'like', "%paypal%")->first();
        $cod = PaymentMethod::where('name', 'like', "%cash on delivery%")->first();
        return view("admin.payment-method.index")
            ->with("paypal", $paypal)
            ->with("cod", $cod);
    }

    public function updatePayPal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'name' => 'required|max:255',
            'description' => 'nullable|string|',
            'sandbox' => 'nullable|boolean',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        if ($request->id > 0) {
            $pMethod = PaymentMethod::findorfail($request->id);
        }else{
            $pMethod = new PaymentMethod;
        }

        $pMethod->name = $request->name;
        $pMethod->slug = Str::slug($request->name);
        $pMethod->description = $request->description;
        $pMethod->status = $request->status;
        $pMethod->logo = "paypal.png";
        $pMethod->credential = json_encode(['client_id' => $request->client_id, 'client_secret' => $request->client_secret]);
        $pMethod->is_sandbox = $request->sandbox; 
        $pMethod->created_at = now();   
        $pMethod->updated_at = now();
        $pMethod->save();  

        return response()->json(['error' => false, 'message' => 'Paypal credetials updated successfully.']);
    }

    public function updateCOD(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'name' => 'required|max:255',
            'description' => 'nullable|string|',
            'status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        if ($request->id > 0) {
            $pMethod = PaymentMethod::findorfail($request->id);
        }else{
            $pMethod = new PaymentMethod;
        }

        $pMethod->name = $request->name;
        $pMethod->slug = Str::slug($request->name);
        $pMethod->description = $request->description;
        $pMethod->status = $request->status;
        $pMethod->logo = "cod.png";
        $pMethod->created_at = now();   
        $pMethod->updated_at = now();
        $pMethod->save();  

        return response()->json(['error' => false, 'message' => 'Paypal credetials updated successfully.']);
    }
}
