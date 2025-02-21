<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Tag;
use App\Models\ProductTag;

class TagController extends Controller
{
     public function index()
    {
        return view('admin.tag.index');
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

        $totalRecord = Tag::count();
        $totalRecordWithFilter = Tag::where("name", "like", "%".$searchValue."%")->count();

        $tags = Tag::select('tags.id','tags.name', 'tags.slug', 'tags.description')
            ->selectRaw('count(product_tag.id) as product_count')
            ->where("tags.name", "like", "%".$searchValue."%")
            ->leftJoin("product_tag","product_tag.tag_id", "=", "tags.id")
            ->groupBy('tags.id')
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($tags as $key => $tag) {
            $data[] = array(
                "id" => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$tag->id.'">
                        </div>',
                "name" => $tag->name,
                "description" => $tag->description,
                "product_count" => $tag->product_count,
                "action" => '<a href="'.url('/product-tag').'/'.$tag->slug.'">
                                <span class=""><i class="bi-eye"></i></span>
                            </a>
                            <a href="'.url('/admin/product-tag/edit').'/'.$tag->id.'">
                                <span class="ms-3"><i class="bi-pen"></i></span>
                            </a>
                            <a href="Javascript:" id="delete-tag" data-id="'.$tag->id.'">
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
            'name' => 'required|unique:tags,name|max:255',
            'description' => 'nullable|max:4096'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        $tag = new Tag;
        $tag->name = $request->name;
        $tag->slug = Str::slug($request->name);
        $tag->description = $request->description;
        $tag->save();

        $data = '<td>
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$tag->id.'">
                    </div>
                </td>
                <td>'.$tag->name.'</td>
                <td>'.$tag->description.'</td>
                <td>0</td>
                <td>
                    <a href="'.url('/product-category').'/'.$tag->slug.'">
                        <span class=""><i class="bi-eye"></i></span>
                    </a>
                    <a href="'.url('/admin/product-category/edit').'/'.$tag->id.'">
                        <span class="ms-3"><i class="bi-pen"></i></span>
                    </a>
                    <a href="" id="delete-category" data-id="'.$tag->id.'">
                        <span class="ms-3"><i class="bi-trash"></i></span>
                    </a>
                </td>';

        return response()->json(["data" => $data, "message" => "Tag added successfully."]);
    }

    public function edit($id)
    {
        $tag = Tag::findorfail($id);
        return view('admin.tag.edit')
            ->with("tag", $tag);
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'name' => 'required|unique:tags,name,'.$id.'|max:255',
            'description' => 'nullable|max:4096',
        ]);

        $tag = Tag::findorfail($id);
        $tag->name = $request->name;
        $tag->slug = Str::slug($request->name);
        $tag->description = $request->description;
        $tag->save();

        $data = '<td>
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$tag->id.'">
                    </div>
                </td>
                <td>'.$tag->name.'</td>
                <td>'.$tag->description.'</td>
                <td>0</td>
                <td>
                    <a href="'.url('/product-tag').'/'.$tag->slug.'">
                        <span class=""><i class="bi-eye"></i></span>
                    </a>
                    <a href="'.url('/admin/product-tag/edit').'/'.$tag->id.'">
                        <span class="ms-3"><i class="bi-pen"></i></span>
                    </a>
                    <a href="" id="delete-category" data-id="'.$tag->id.'">
                        <span class="ms-3"><i class="bi-trash"></i></span>
                    </a>
                </td>';

        return redirect()->back()->with("success", "Tag updated successfully.");
    }

    public function destroy(Request $request)
    {
        foreach($request->id as $id){
            Tag::where("id",$id)->delete();
            ProductTag::where("tag_id", $id)->delete();
        }

        return response()->json(["message" => "Tags (s) deleted successfully."]);
    }
}
