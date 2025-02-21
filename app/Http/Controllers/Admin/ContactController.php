<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Models\Contact;
use App\Models\ContactReply;

class ContactController extends Controller
{
    public function index()
    {
        return view("admin.contact.index");
    }

    public function contactList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Contact::count();
        $totalRecordWithFilter = Contact::where("name", "like", "%".$searchValue."%")
           ->count();

        $contacts = Contact::where("name", "like", "%".$searchValue."%")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($contacts as $contact) {

            $status = match ($contact->status) {
                "unread" => '<span class="badge badge-warning">unread</span>',
                "read" => '<span class="badge badge-success">read</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$contact->id.'">
                        </div>' ,
                'name' => ' <a href="'.url('/admin/contact/read').'/'.$contact->id.'">'.$contact->name.'</a>',
                'email' => ' <a href="mailto:'.$contact->email.'">'.$contact->email.'</a>',
                'phone' => ' <a href="tel:'.$contact->phone.'">'.$contact->phone.'</a>',
                'created_at' => date("d/m/Y", strtotime($contact->created_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/contact/read').'/'.$contact->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-contact" data-id="'.$contact->id.'">
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

    public function read($id)
    {
        $contact = Contact::findorfail($id);
        $replies = ContactReply::where("contact_id", $id)->get();

        return view("admin.contact.read")
            ->with("contact", $contact)
            ->with("replies", $replies);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate(["status" => "required|string"]);

        $contact = Contact::where("id", $id)->update(["status" => $request->status]);

        return redirect()->back()->with("Success", "Contact status updated successfully.");
    }

    public function reply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|integer",
            'message' => 'required|string'
        ],
        [
            "id.required" => "Invalid credentials",
             "id.integer" => "Invalid credentials"
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        $contact = Contact::findorfail($request->id);

        Mail::to($contact->email)->send(new \App\Mail\ContactReply($request->message));

        $reply = new ContactReply;
        $reply->user_id = Auth::user()->id;
        $reply->contact_id = $contact->id;
        $reply->message = $request->message;
        $reply->created_at = now();
        $reply->save();

        $data = '<div class="mt-4">
                    <p>Time: <i>'.date("F j, Y H:m:s",strtotime($reply->created_at)).'</i></p>
                    <p>Message:</p>
                    <div class="rounded-3 bg-light p-4">
                        <p>'.$reply->message.'</p>
                    </div>
                </div>';

        return response()->json([
            "message" => "Reply send successfully.",
            "data" => $data
        ]);
    }

    public function status(Request $request)
    {
        Contact::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Contacts status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        Contact::whereIn("id", $request->id)->delete();
        ContactReply::whereIn("contact_id", $request->id)->delete();

        return response()->json(["message" => "Contacts deleted successfully."]);
    }

}
