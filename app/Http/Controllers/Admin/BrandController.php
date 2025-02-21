<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryBrand;

class BrandController extends Controller
{
    public function index()
    {
        return view('admin.brand.index');
    }

    public function brandList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Brand::count();
        $totalRecordWithFilter = Brand::where("name", "like", "%".$searchValue."%")->count();

        $brands = Brand::select('*')
            ->where("name", "like", "%".$searchValue."%")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($brands as $key => $brand) {
            $featured = "";
            if ($brand->is_featured) {
                $featured = '<span class="badge badge-success">true</span>';
            }else{
                $featured = '<span class="badge badge-danger">false</span>';
            }

            $status = "";
            if ($brand->status == "published") {
                $status = '<span class="badge badge-success">published</span>';
            }else{
                $status = '<span class="badge badge-warning">draft</span>';
            }

            $data[] = array(
                "id" => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$brand->id.'">
                        </div>',
                "name" => '<div class="d-flex align-items-center">
                                <img class="rounded-3 me-2" width="40" height="40" src="'.url('/assets/img/brands').'/'.$brand->logo.'">
                                <a href="'.url('/admin/brand/edit').'/'.$brand->id.'"><h6>'.$brand->name.'</h6></a>
                            </div>',
                "description" => $brand->description?? "_",
                "is_featured" => $featured,
                "created_at" => date("d/m/Y", strtotime($brand->created_at)),
                "status" => $status,
                "action" => '<a href="'.url('/admin/brand/edit').'/'.$brand->id.'">
                                <span class="ms-3"><i class="bi-pen"></i></span>
                            </a>
                            <a href="Javascript:" id="delete-brand" data-id="'.$brand->id.'">
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
        $categories = Category::where('parent_id',0)->get();

        return view('admin.brand.create')->with('categories', $categories);
    }

    public function store(Request $request){
        $validator = $request->validate([
            'name' => 'required|unique:brands,name|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'status' => 'required|max:255',
            'category.*' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'logo' => 'required|image|max:4096'
        ]);

        $logoName = "";
        if ($request->hasFile("logo")) {
            $file = $request->file("logo");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $logoName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/brands', $logoName);
        }

        $brand = new Brand;
        $brand->name  = $request->name;
        $brand->slug  = Str::slug($request->name);
        $brand->url  = $request->url;
        $brand->description  = $request->description;
        $brand->status  = $request->status;
        $brand->is_featured  = $request->is_featured;
        $brand->logo  = $logoName;
        $brand->save();


        if ($request->has("category")) {
            foreach($request->category as $category){
                $categoryBrand = new CategoryBrand;
                $categoryBrand->brand_id = $brand->id;
                $categoryBrand->category_id = $category;
                $categoryBrand->save();
            }
        }

        return redirect("/admin/brand/edit"."/".$brand->id)->with("success", "Brand added successfully.");
    }

    public function edit($id)
    {
        $brand = Brand::findorfail($id);
        $categories = Category::where('parent_id',0)->get();
        $brandCategories = Brand::select('category_brand.category_id')
            ->join('category_brand','brands.id', '=', 'category_brand.brand_id')
            ->where('brand_id', $brand->id)
            ->get()->map(function($item){
                return $item->category_id;
            })->toArray();

        return view("admin.brand.edit")
            ->with("categories", $categories)
            ->with('brandCategories', $brandCategories)
            ->with("brand",$brand);
    }

    public function update(Request $request, $id)
    {
          $validator = $request->validate([
            'name' => 'required|unique:brands,name, '.$id.'|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'status' => 'required|max:255',
            'category.*' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'logo' => 'nullable|image|max:4096'
        ]);

        $logoName = "";
        if ($request->hasFile("logo")) {
            $file = $request->file("logo");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $logoName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/brands', $logoName);
        }

        $brand = Brand::findorfail($id);
        $brand->name  = $request->name;
        $brand->slug  = Str::slug($request->name);
        $brand->url  = $request->url;
        $brand->description  = $request->description;
        $brand->status  = $request->status;
        $brand->is_featured  = $request->is_featured;
        
        if ($request->hasFile("logo")) {
            File::delete(public_path('assets/img/brands/'.$brand->logo.''));
            $brand->logo = $logoName;
        }
        $brand->save();

        if ($request->has("category")) {
            CategoryBrand::where('brand_id', $brand->id)->delete();
            foreach($request->category as $category){
                $categoryBrand = new CategoryBrand;
                $categoryBrand->brand_id = $brand->id;
                $categoryBrand->category_id = $category;
                $categoryBrand->save();
            }
        }

        return redirect()->back()->with("success", "Brand updated successfully.");
    }

    public function status(Request $request)
    {
        Brand::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Brand(s) status updated successfully."]);
    }

    public function featured(Request $request)
    {
       Brand::whereIn("id", $request->id)->update(["is_featured" => $request->status]);

        return response()->json(["message" => "Brand(s) status updated successfully."]) ;
    }

    public function destroy(Request $request)
    {
        foreach($request->id as $id){
            CategoryBrand::where('brand_id', $id)->delete();
            $brand = Brand::find($id);
            File::delete(public_path('assets/img/brands/'.$brand->logo.''));
            $brand->delete();
        } 

        return response()->json(["message" => "Brand(s) deleted successfully."]);
    }
}
