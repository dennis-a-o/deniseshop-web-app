<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::findorfail(Auth::user()->id);
        return view("admin.profile.index")->with("user", $user);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            "first_name" => "required|string",
            "last_name" => "required|string",
            "username" => "required|unique:users,username,".$user->id."|string",
            "email" => "required|email|unique:users,email,".$user->id."",
            "image" => "nullable|image|max:4096"
        ]);

        $imageName = "";
        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;

            $path = $file->move('assets/img/users', $imageName);
        }

        $profile = User::findorfail($user->id);
        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->username = $request->username;
        $profile->email = $request->email;

        if ($request->hasFile("image")) {
            if ($profile->image != "") {
                File::delete(public_path('assets/img/users/'.$profile->image.''));
            }
            $profile->image = $imageName;
        }
        
        $profile->save();

        return redirect()->back()->with("success", "You profile updated successfully.");
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            "current_password" => "required|string|current_password",
            "new_password" => "required|string|min:6",
        ]);

        $profile = User::findorfail(Auth::user()->id);
        $profile->password = Hash::make($request->new_password);
        $profile->save();

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    public function clearSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|current_password',
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        Auth::logoutOtherDevices($request->password);

        return response()->json(['message'=> 'Logged out of other devices successfully.'], 200);
    }
}
