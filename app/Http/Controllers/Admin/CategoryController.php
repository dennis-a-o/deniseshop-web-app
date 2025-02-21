<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $Categories = Category::where("parent_id", 0)
            ->get();
        return view('admin.category.index')
            ->with("categories",$Categories);
    }

    public function categoryList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Category::count();
        $totalRecordWithFilter = Category::where("name", "like", "%".$searchValue."%")->count();

        $categories = Category::select('categories.id','categories.name','categories.image', 'categories.slug', 'categories.icon','categories.description')
            ->selectRaw('count(products.id) as product_count')
            ->where("categories.name", "like", "%".$searchValue."%")
            ->leftJoin('product_category','product_category.category_id','categories.id')
            ->leftJoin("products","products.id", "=", "product_category.product_id")
            ->groupBy(['id','name','slug', 'icon', 'description'])
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($categories as $key => $category) {
            $data[] = array(
                "id" => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$category->id.'">
                        </div>',
                "name" => '<div class="d-flex align-items-center">
                                <img class="rounded-3 me-2" width="40" height="40" src="'.url('/assets/img/categories').'/'.$category->image.'">
                                <a href=""><h6>'.$category->name.'</h6></a>
                            </div>',
                "description" => $category->description,
                "product_count" => $category->product_count,
                "action" => '<a href="'.url('/product-category').'/'.$category->slug.'">
                                <span class=""><i class="bi-eye"></i></span>
                            </a>
                            <a href="'.url('/admin/product-category/edit').'/'.$category->id.'">
                                <span class="ms-3"><i class="bi-pen"></i></span>
                            </a>
                            <a href="Javascript:" id="delete-category" data-id="'.$category->id.'">
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name|max:255',
            'parent' => 'nullable|integer',
            'description' => 'nullable|max:4096',
            'image' => 'required|image|max:4096',
            'icon' => 'nullable|image|max:4096'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        $imageName = "";
        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/categories', $imageName);
        }

        $iconName = "";
        if ($request->hasFile("icon")) {
            $file = $request->file("icon");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $iconName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/categories', $iconName);
        }

        $category = new Category;
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->parent_id = $request->parent;
        $category->description = $request->description;
        $category->image = $imageName;
        $category->icon = $iconName;
        $category->save();

        $data = '<td>
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$category->id.'">
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                    <img class="rounded-3 me-2" width="40" height="40" src="'.url('/assets/img/categories').'/'.$category->image.'">
                    <a href=""><h6>'.$category->name.'</h6></a>
                    </div>
                </td>
                <td>'.$category->description.'</td>
                <td>0</td>
                <td>
                    <a href="'.url('/product-category').'/'.$category->slug.'">
                        <span class=""><i class="bi-eye"></i></span>
                    </a>
                    <a href="'.url('/admin/product-category/edit').'/'.$category->id.'">
                        <span class="ms-3"><i class="bi-pen"></i></span>
                    </a>
                    <a href="" id="delete-category" data-id="'.$category->id.'">
                        <span class="ms-3"><i class="bi-trash"></i></span>
                    </a>
                </td>';

        return response()->json(["data" => $data, "message" => "Category added successfully."]);
    }

    public function edit($id)
    {
        $category = Category::findorfail($id);
        $categories = Category::where("parent_id", 0)->get();
        return view('admin.category.edit')
            ->with("category", $category)
            ->with("categories",$categories);
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'name' => 'required|unique:categories,name,'.$id.'|max:255',
            'parent' => 'nullable|integer',
            'description' => 'nullable|max:4096',
            'image' => 'nullable|image|max:4096',
            'icon' => 'nullable|image|max:4096'
        ]);

        $imageName = "";
        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/categories', $imageName);
        }

        $iconName = "";
        if ($request->hasFile("icon")) {
            $file = $request->file("icon");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $iconName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/categories', $iconName);
        }

        $category = Category::findorfail($id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->parent_id = $request->parent;
        $category->description = $request->description;
        
        if ($request->hasFile("image")) {
            if ($category->image != "") {
                  File::delete(public_path('assets/img/categories/'.$category->image.''));
            }
            $category->image = $imageName;
        }

        if ($request->hasFile("icon")) {
            File::delete(public_path('assets/img/categories/'.$category->icon.''));
            $category->icon = $iconName;
        }

        $category->save();

        $data = '<td>
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$category->id.'">
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                    <img class="rounded-3 me-2" width="40" height="40" src="'.url('/assets/img/categories').'/'.$category->image.'">
                    <a href=""><h6>'.$category->name.'</h6></a>
                    </div>
                </td>
                <td>'.$category->description.'</td>
                <td>0</td>
                <td>
                    <a href="'.url('/product-category').'/'.$category->slug.'">
                        <span class=""><i class="bi-eye"></i></span>
                    </a>
                    <a href="'.url('/admin/product-category/edit').'/'.$category->id.'">
                        <span class="ms-3"><i class="bi-pen"></i></span>
                    </a>
                    <a href="" id="delete-category" data-id="'.$category->id.'">
                        <span class="ms-3"><i class="bi-trash"></i></span>
                    </a>
                </td>';

        return redirect()->back()->with("success", "Category updated successfully.");
    }

    public function destroy(Request $request)
    {
        foreach($request->id as $id){
            $category = Category::findorfail($id);
            if ($category->image != "") {
                File::delete(public_path('assets/img/categories/'.$category->image.''));
            }

            if ($category->icon != "") {
                File::delete(public_path('assets/img/categories/'.$category->icon.''));
            }
            $category->delete();
        }

        return response()->json(["message" => "Category (s) deleted successfully."]);
    }
}
