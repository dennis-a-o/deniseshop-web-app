<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Brand;

class SliderController extends Controller
{
    public function index()
    {
        return view("admin.slider.index");
    }

    public function sliderList(Request $request)
    {
         $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Slider::count();
        $totalRecordWithFilter = Slider::where("title", "like", "%".$searchValue."%")->count();

        $sliders = Slider::where("title", "like", "%".$searchValue."%")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($sliders as $slider) {

            $status = match ($slider->status) {
                "pending" => '<span class="badge badge-warning">'.$slider->status.'</span>',
                "draft" => '<span class="badge badge-info">'.$slider->status.'</span>',
                "published" => '<span class="badge badge-success">'.$slider->status.'</span>',
                default => '<span class="badge badge-warning">'.$slider->status.'</span>',
            };

            
            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$slider->id.'">
                        </div>' ,
                'image' => '<img class="rounded-3 shadow-sm" src="'.url('/assets/img/sliders').'/'.$slider->image.'" width="70" height="70">',
                'title' => '<a href="'.url('/admin/slider/edit').'/'.$slider->id.'">'.$slider->title.'</a>',
                'order' => $slider->order,
                'status' => $status,
                'created_at' => date("d/m/Y", strtotime($slider->created_at)),
                'action' => ' <a  href="'.url('/admin/slider/edit').'/'.$slider->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a><a  href="Javascript:" id="delete-item" data-id="'.$slider->id.'">
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
        $categories = Category::get();
        $brands = Brand::get();

        return view("admin.slider.create")
            ->with('categories', $categories)
            ->with('brands', $brands);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string|unique:sliders,title",
            "sub_title" => "required|string",
            "highlight_text" => "required|string",
            "image" => "required|image|max:4096",
            "description" => "required|string",
            'type' => 'nullable|string',
            'type_id' => 'nullable|integer',
            "link" => "required|string",
            "status" => "required|string",
            "button_text" => "required|string",
            "order" => "nullable|integer",
        ]);

        $imageName = "";
        if ($request->hasFile("image")) {
             $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/sliders', $imageName);
        }

        $data = $request->except(["_token"]);
        $data["image"] = $imageName;
        $data["created_at"] = now();

        $id = Slider::insertGetId($data);

        return redirect("/admin/slider/edit"."/".$id)->with("success", "slider created successfully.");
    }


    public function edit($id)
    {
        $slider = Slider::findorfail($id);
        $categories = Category::get();

        $brands = Brand::get();
        return view("admin.slider.edit")
            ->with('categories', $categories)
            ->with('brands', $brands)
            ->with("slider",$slider);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "title" => "required|string|unique:sliders,title,".$id."",
            "sub_title" => "required|string",
            "highlight_text" => "required|string",
            "image" => "nullable|image|max:4096",
            "description" => "required|string",
            'type' => 'nullable|string',
            'type_id' => 'nullable|integer',
            "link" => "required|string",
            "status" => "required|string",
            "button_text" => "required|string",
            "order" => "nullable|integer"
        ]);

        $imageName = "";
        if ($request->hasFile("image")) {
             $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/sliders', $imageName);
        }

       $slider = Slider::findorfail($id);
       $slider->title = $request->title;
       $slider->sub_title = $request->sub_title;
       $slider->highlight_text = $request->highlight_text;
       $slider->link = $request->link;
       $slider->type = $request->type; 
       $slider->type_id = $request->type_id; 
       $slider->description = $request->description;
       $slider->status = $request->status;
       $slider->button_text = $request->button_text;
       $slider->order = $request->order;

        if ($request->hasFile("image")) {
            File::delete(public_path('assets/img/sliders/'.$slider->image.''));
            $slider->image = $imageName;
        }

        $slider->save();

        return redirect()->back()->with("success", "slider updated successfully.");
    }

    public function status(Request $request)
    {
        Slider::whereIn("id", $request->id)->update(["status" => $request->status]);
        
        return response()->json(["message" => "Sliders status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        foreach($request->id as $id){
            $slider = Slider::find($id);
            if ($slider->image != "") {
                 File::delete(public_path('assets/img/sliders/'.$slider->image.''));
            }
            $slider->delete();
        }

        return response()->json(["message" => "Sliders deleted successfully."]);
    }

}
