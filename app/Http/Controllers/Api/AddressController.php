<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Arr;
use Util;
use Validator;

class AddressController extends Controller
{
    public function getAll(){
        $user_id = auth('api')->user()->id;

        $address = Address::where('user_id',$user_id)->get();

        return response()->json($address);
    }

    public function getCountries(){
        $user_id = auth('api')->user()->id;

        $countries = Util::$countries;

        $countries = Arr::flatten($countries);

        return response()->json($countries);
    }

    public function add(Request $request){
        $user_id = auth('api')->user()->id;

        $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string|min:10|max:12',
                'country' => 'required|string',
                'state' => 'required|string',
                'city' => 'required|string',
                'address' => 'required|string',
                'zip_code' => 'required|string',
                'type' => 'required|string',
                'default' => 'required|boolean',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" =>  implode("\n", $validator->errors()->all()),
            ], 422);
        }


        $data = $request->merge(['user_id' => $user_id]);

        $address = Address::create($data->all());
    
        return response()->json([
            'success' => true,
            'message' => "Address added successfully.",
        ]);
    }

    public function update($address_id, Request $request){
        $user_id = auth('api')->user()->id;

        $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string|min:10|max:12',
                'country' => 'required|string',
                'state' => 'required|string',
                'city' => 'required|string',
                'address' => 'required|string',
                'zip_code' => 'required|string',
                'type' => 'required|string',
                'default' => 'required|boolean',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => implode("\n", $validator->errors()->all())." ".$request->default,
            ], 422);
        }

        $data = $request->merge(['user_id' => $user_id]);

        $address = Address::where('user_id',$user_id)->find($address_id);
        $address->update($data->all());

        return response()->json([
            'success' => true,
            'message' =>  "Address updated successfully."
        ]);
    }

    public function makeDefault($address_id){
        $user_id = auth('api')->user()->id;

        $allAddress = Address::where('user_id',$user_id)
            ->update(['default' => false]);

        $address = Address::where([
                'id' => $address_id,
                'user_id' => $user_id
        ])->update(['default' => true]);


        return response()->json([
            'success' => true,
            'message' => "Address updated successfully."
        ]);
    }

    public function remove($address_id){
        $user_id = auth('api')->user()->id;

        Address::where(['id' => $address_id, 'user_id' => $user_id])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Delete successfully."
        ]);
    }
}
