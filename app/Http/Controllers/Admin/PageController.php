<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        return view("admin.page.index");
    }

    public function pageList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Page::count();
        $totalRecordWithFilter = Page::where("name", "like", "%".$searchValue."%")
           ->count();

        $pages = Page::where("name", "like", "%".$searchValue."%")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($pages as $page) {

            $status = match ($page->status) {
                "draft","pending" => '<span class="badge badge-warning">'.$page->status.'</span>',
                "published" => '<span class="badge badge-success">published</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$page->id.'">
                        </div>' ,
                'name' => ' <a href="'.url('/admin/page/edit').'/'.$page->id.'">'.$page->name.'</a>',
                'created_at' => date("d/m/Y", strtotime($page->created_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/page/edit').'/'.$page->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-page" data-id="'.$page->id.'">
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
        return view("admin.page.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:pages,name",
            "description" => "nullable|string",
            "content" => "required|string",
            "image" => "nullable|image|max:4096",
            "status" => "required|string"
        ]);

        $imageName = "";

        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/pages', $imageName);
        }

        $data = $request->except(["_token"]);
        $data["slug"] = Str::slug($data["name"]);
        $data["image"] = $imageName;
        $data["created_at"] = now();

        $id = Page::insertGetId($data);

        \App\Models\AdminNotification::insert([
            "title" => "New page",
             "action_label" => "View",
             "action_url" => "/admin/page/edit/".$id,
             "description" => Auth::user()->username." created a new page.",
             "created_at" => now()
        ]);

        return redirect("/admin/page/edit"."/".$id)->with("success", "Page created successfully.");
    }

    public function edit($id)
    {
        $page = Page::findorfail($id);
        return view("admin.page.edit")->with("page", $page);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name" => "required|string|unique:pages,name,".$id."",
            "description" => "nullable|string",
            "content" => "required|string",
            "image" => "nullable|image|max:4096",
            "status" => "required|string"
        ]);

        $imageName = "";
        
        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/pages', $imageName);
        }

        $page = Page::findorfail($id);

        $data = $request->except(["_token"]);
        $data["slug"] = Str::slug($data["name"]);
        $data["updated_at"] = now();

        if ($request->hasFile("image")) {
            File::delete(public_path('assets/img/pages/'.$page->image.''));
            $data["image"] = $imageName;
        }
       
       $page->update($data);

        return redirect()->back()->with("success", "Page updated successfully.");
    }

    public function status(Request $request)
    {
        Page::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Pages status updated successfully.".$request->status]);
    }

    public function destroy(Request $request)
    {
        foreach ($request->id as $key => $id) {
            $page  = Page::find($id);

            if ($page->image != "") {
                File::delete(public_path('assets/img/pages/'.$page->image.''));
            }

            $page->delete();
        }

        return response()->json(["message" => "Pages deleted successfully."]);
    }
}
