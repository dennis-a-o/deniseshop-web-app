<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\RecentViewed;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\Subscriber;
use App\Models\Address;
use App\Models\Review;
use App\Models\Notification;
use Validator;
use Hash;
use Exception;
use JWTAuth;

class ProfileController extends Controller
{
    public function profile(){
         $user = User::findorfail(auth('api')->user()->id);

        $user['image'] =  url('assets/img/users').'/'.$user->image;

        return response()->json($user);
    }

    public function update(Request $request)
    {
        $user_id =auth('api')->user()->id;

         $validator = Validator::make($request->all(), [
            "first_name" => "required|string",
            "last_name" => "required|string",
            "email" => "required|email|unique:users,email,".$user_id."",
            "phone" => "required|unique:users,phone,".$user_id."|string",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" =>  implode("\n", $validator->errors()->all()),
            ], 422);
        }

        $user = User::findorfail($user_id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->save();

        $user = User::find($user_id);
        $user['image'] =  url('assets/img/users').'/'.$user->image;

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => $user
        ], 200);
    }

    public function updateImage(Request $request)
    {
        $user_id =auth('api')->user()->id;

        $validator = Validator::make($request->all(), [
            "image" => "required|image|max:4096"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" =>  implode("\n", $validator->errors()->all()),
            ], 422);
        }

        $file = $request->file("image");

        $filenameWithExt = $file->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $imageName = $filename.'_'.time().'.'.$extension;

        $path = $file->move('assets/img/users', $imageName);

        $user = User::findorfail($user_id);

        if ($user->image != "default.jpg") {
            if ($user->image != "default.png") {
                 File::delete(public_path('assets/img/users/'.$user->image.''));
            }
        }

        $user->image = $imageName;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'image' => url('assets/img/users').'/'.$user->image
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $user_id =auth('api')->user()->id;

        $validator = Validator::make($request->all(), [
            "current_password" => "required|string|current_password",
            "new_password" => "required|string|min:8"
        ],
        [
            'current_password' => ['current_password' => "Invalid current password"]
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" =>  implode("\n", $validator->errors()->all()),
            ], 422);
        }

        $user = User::findorfail($user_id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    public function delete()
    {
        $user_id =auth('api')->user()->id;
        $user = User::find($user_id);

        if ($user->image != "") {
            File::delete(public_path('assets/img/users/'.$user->image.''));
        }

        $reviews = Review::where("user_id", $user_id)->get();
        if (count($reviews)) {
            foreach ($reviews as $review) {
                $images = json_decode($review->images);
                if ($images != null && count($images)) {
                    foreach ($$images as $image) {
                        File::delete(public_path('assets/img/reviews/'.$image.''));
                    }
                }
            }
        }
        Review::where("user_id", $user_id)->delete();
                
        Notification::where("user_id", $user_id)->delete();

        Address::where("user_id", $user_id)->delete();

        Subscriber::where("user_id", $user_id)->delete();

        RecentViewed::where("user_id", $user_id)->delete();
                
        Wishlist::where("user_id", $user_id)->delete();

        Cart::where("user_id", $user_id)->delete();

        $user->delete();
            
        return response()->json([
            'success' => true,
            'message' => 'Your account deleted successfully.'
        ]);
    }
}
