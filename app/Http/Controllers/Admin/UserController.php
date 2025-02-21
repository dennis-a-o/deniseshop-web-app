<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\PasswordReset;
use App\Models\Subscriber;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\RecentViewed;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;

use App\Mail\LoginDetail;

use Hash;

class UserController extends Controller
{
    public function index()
    {
        return view("admin.user.index");
    }

    public function userList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = User::count();
        $totalRecordWithFilter = User::where("first_name", "like", "%".$searchValue."%")
            ->orWhere("last_name", "like", "%".$searchValue."%")
           ->count();

        $users = User::where("first_name", "like", "%".$searchValue."%")
            ->orWhere("last_name", "like", "%".$searchValue."%")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($users as $user) {

            $status = match ($user->status) {
                "locked" => '<span class="badge badge-warning">locked</span>',
                "activated" => '<span class="badge badge-success">activated</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$user->id.'">
                        </div>' ,
                'first_name' => '<div class="d-flex align-items-center">
                                    <img class="rounded-3 shadow-sm me-2" src="'.url('/assets/img/users').'/'.$user->image.'" width="30" height="30">
                                    <a href="'.url('/admin/user/edit').'/'.$user->id.'"><h6 class="m-0">'.$user->first_name.' '.$user->last_name.'</h6></a>
                                </div>',
                'role' => $user->role,
                'email' => '<a href="mailto:'.$user->email.'">'.$user->email.'</a>',
                'created_at' => date("d/m/Y", strtotime($user->created_at)),
                'email_verified_at' => date("d/m/Y", strtotime($user->email_verified_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/user/edit').'/'.$user->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-user" data-id="'.$user->id.'">
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
        return view("admin.user.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "first_name" => "required|string",
            "last_name" => "required|string",
            "image" => "nullable|image|max:4096",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:6",
            "role" => "required|string",
            "subscribe" => "nullable|boolean",
            "notify" => "nullable|boolean",
            "verification" => "required|string",
            "status" => "required|string",
        ]);

        $imageName = "default.jpg";

        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/users', $imageName);
        }

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username =  $request->email;
        $user->image = $imageName;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->email_verified_at = ($request->has("verification"))? now(): null;
        $user->status = $request->status;
        $user->save();

        if ($request->has("subscribe")) {
           Subscriber::insert([
            "user_id" => $user->id,
            "email" => $user->email,
            "status" => "subscribed",
            "created_at" => now()
           ]);
        }

         \App\Models\AdminNotification::insert([
            "title" => "New user",
             "action_label" => "View",
             "action_url" => "/admin/user/edit/".$user->id,
             "description" => Auth::user()->username." registered a new user.",
             "created_at" => now()
        ]);

        if ($request->has("notify")) {
            $token = Str::random(60);

            PasswordReset::insert([
                "email" => $user->email,
                "token" => $token,
                "created_at" => now()
            ]);

            $detail = [
                "name" => "Hello ".$user->first_name." ".$user->last_name,
                "message" => "Click the button to reset your passord for ".config('app.name')." account.",
                "url" => "".url("reset-password?token=").$token.""
            ];

            Mail::to("dennis@localhost.com")->send(new LoginDetail($detail));
        }

        return redirect("/admin/user/edit"."/".$user->id)->with("success", "User created successfully.");
    }

    public function edit($id)
    {
        $user = User::findorfail($id);
        $addresses = Address::where("user_id", $id)->get();
        $payments = Payment::select("payments.id","payments.transaction_id", "payments.currency","payments.amount","payments.status","orders.id as order_id","orders.code", "payment_methods.name")
        ->where("payments.user_id", $id)
        ->leftJoin("orders","orders.id","=","payments.order_id")
        ->leftJoin("payment_methods","payment_methods.id","=","payments.payment_method_id")
        ->orderBy("payments.id", "desc")
        ->get();

        return view("admin.user.edit")
            ->with("user", $user)
            ->with("addresses", $addresses)
            ->with("payments", $payments);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "first_name" => "required|string",
            "last_name" => "required|string",
            "image" => "nullable|image|max:4096",
            "email" => "required|email|unique:users,email,".$id."",
            "password" => "nullable|string|min:6",
            "role" => "required|string",
            "verification" => "required|string",
            "status" => "required|string",
        ]);

        $imageName = "default.jpg";

        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/users', $imageName);
        }

        $user = User::findorfail($request->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username =  $request->email;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->email_verified_at = ($request->has("verification"))? now(): null;
        $user->status = $request->status;

        if ($request->password != "") {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile("image")) {
            if ($user->image !== "default.jpg") {
                if ($user->image !== "default.png") {
                   File::delete(public_path('assets/img/users/'.$user->image.''));
                }
            }
            $user->image = $imageName;
        }

        $user->save();

        return redirect()->back()->with("success", "User updated successfully.");
    }

    public function reviewList(Request $request, $id)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        //$searchValue = $request['search']['value'];

        $totalRecord = Review::count();
        $totalRecordWithFilter = Review::count();

        $reviews = Review::select('reviews.id','reviews.star', 'reviews.comment', 'reviews.status','reviews.created_at', 'reviews.user_id','reviews.product_id', 'products.name as product_name', 'products.slug as product_slug')
            ->where("reviews.user_id", $id)
            ->leftJoin("products","products.id", "=", "reviews.product_id")
            ->leftJoin("users","users.id", "=", "reviews.user_id")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($reviews as $key => $review) {
            $status = match ($review->status) {
                "pending" => '<span class="badge badge-warning">pending</span>',
                "approved" => '<span class="badge badge-success">Approved</span>',
                "rejected" => '<span class="badge badge-danger">Rejected</span>'
            };

            $data[] = array(
                "product" => '<a href="'.url('/product').'/'.$review->product_slug.'"><h6>'.$review->product_name.'</h6></a>',
                "star" => '<div class="d-inline">'.$this->rating($review->star).'</div>',
                "comment" => $review->comment,
                "status" => $status,
                "created_at" => date("d/m/Y", strtotime($review->created_at)),
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

    public function addAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'name' => 'required|max:255',
            'phone' => 'required|max:20',
            'email' => 'required||email|max:255',
            'zip_code' => 'required|max:255',
            'address' => 'required|max:255',
            'country' => 'required|max:255',
            'state' => 'required|max:255',
            'city' => 'required|max:255',
            'type' => 'required|string|max:255',
        ],
        [
            'user_id.required' => 'Invalid form reload the page',
            'user_id.integer' => 'Invalid form reload the page'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        $data = $request->except(["_token", "id"]);
        $data["created_at"] = now();

        $id = Address::insertGetId($data);

        $response = '<td>'.$request->address.'</td>
                    <td>'.$request->zip_code.'</td>
                    <td>'.$request->country.'</td>
                    <td>'.$request->state.'</td>
                    <td>'.$request->city.'</td>
                    <td><span class="badge badge-info">'.$request->type.'</span></td>
                    <td>
                        <a id="edit-address" href="Javascript:">
                            <span id="address-data" style="display: none;">
                                '.$request->id.'='.$request->user_id.'=
                                '.$request->name.'='.$request->phone.'=
                                '.$request->zip_code.'='.$request->email.'=
                                '.$request->address.'='.$request->country.'=
                                '.$request->state.'='.$request->city.'='.$request->type.'
                            </span>
                            <span class="me-2">
                                <i class="bi-pencil"></i>
                            </span>
                        </a>
                        <a  href="Javascript:" id="delete-address" data-id="'.$id.'" data-userid="'.$request->user_id.'">
                            <span class="p-2">
                                <i class="bi-trash"></i>
                            </span>
                        </a>
                    </td>';
        return response()->json(["data" => $response, "message" => "Address created successfully."]);
    }

    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'user_id' => 'required|integer',
            'name' => 'required|max:255',
            'phone' => 'required|max:20',
            'email' => 'required||email|max:255',
            'zip_code' => 'required|max:255',
            'address' => 'required|max:255',
            'country' => 'required|max:255',
            'state' => 'required|max:255',
            'city' => 'required|max:255',
            'type' => 'required|string|max:255',
        ],
        [
            'user_id.required' => 'Invalid form reload the page',
            'user_id.integer' => 'Invalid form reload the page',
            'id.required' => 'Invalid form reload the page',
            'id.integer' => 'Invalid form reload the page'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        $data = $request->except(["_token"]);
        $data["updated_at"] = now();

        Address::where("id", $request->id)->update($data);

        $response = '<td>'.$request->address.'</td>
                    <td>'.$request->zip_code.'</td>
                    <td>'.$request->country.'</td>
                    <td>'.$request->state.'</td>
                    <td>'.$request->city.'</td>
                    <td><span class="badge badge-info">'.$request->type.'</span></td>
                    <td>
                        <a id="edit-address" href="Javascript:">
                            <span id="address-data" style="display: none;">
                                '.$request->id.'='.$request->user_id.'=
                                '.$request->name.'='.$request->phone.'=
                                '.$request->zip_code.'='.$request->email.'=
                                '.$request->address.'='.$request->country.'=
                                '.$request->state.'='.$request->city.'='.$request->type.'
                            </span>
                            <span class="me-2">
                                <i class="bi-pencil"></i>
                            </span>
                        </a>
                        <a  href="Javascript:" id="delete-address" data-id="'.$request->id.'" data-userid="'.$request->user_id.'">
                            <span class="p-2">
                                <i class="bi-trash"></i>
                            </span>
                        </a>
                    </td>';
        return response()->json(["data" => $response, "message" => "Address updated successfully."]);
    }

    public function deleteAddress(Request $request)
    {
        Address::where("id", $request->id)->delete();
        return response()->json(["message" => "Address deleted successfully."]);
    }


    public function verification(Request $request)
    {
        $status = match($request->status){
            "verify" => now(),
            "unverify" => null
        };

        User::whereIn("id", $request->id)->update(["email_verified_at" => $status]);

        return response()->json(["message" => "Users verified at status changed successfully."]);
    }

    public function status(Request $request)
    {
        User::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Users status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        $current_user = Auth::user();

        foreach ($request->id as $key => $id) {
            $user = User::find($id);
            if ($user->id != $current_user->id) {
                if ($user->image != "") {
                    File::delete(public_path('assets/img/users/'.$user->image.''));
                }

                $reviews = Review::where("user_id", $id)->get();
                if (count($reviews)) {
                    foreach ($reviews as $review) {
                        $images = json_decode($review->images);
                        if (count($images)) {
                            foreach ($$images as $image) {
                                File::delete(public_path('assets/img/reviews/'.$image.''));
                            }
                        }
                    }
                }

                Review::where("user_id", $id)->delete();
                
                Notification::where("user_id", $id)->delete();
                
                Address::where("user_id", $id)->delete();

                Subscriber::where("user_id", $id)->delete();

                RecentViewed::where("user_id", $id)->delete();
            
                Wishlist::where("user_id", $id)->delete();

                Cart::where("user_id", $id)->delete();

                $user->delete();
            }
        }

        return response()->json(["message" => "Users deleted successfully."]);
    }

    private function rating($num)
    {
        $stars = "";

        for ($i=1; $i <= 5; $i++) { 
            if($i <= $num){
                $stars .= '<i class="bi-star-fill text-warning fs-10"></i>';
            }else{
                $stars .= '<i class="bi-star fs-10"></i>'; 
            }
        }

        return $stars;
    }
}
