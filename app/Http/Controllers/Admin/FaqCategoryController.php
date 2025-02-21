<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\FaqCategory;

class FaqCategoryController extends Controller
{
    public function index()
    {
        return view("admin.faqcategory.index");
    }

    public function faqCategoryList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = FaqCategory::count();
        $totalRecordWithFilter = FaqCategory::where("name", "like", "%".$searchValue."%")->count();

        $categories = FaqCategory::select("faq_categories.*")
            ->selectRaw('count(faqs.id) as faq_count')
            ->where("faq_categories.name", "like", "%".$searchValue."%")
            ->leftJoin("faqs","faqs.category_id", "=", "faq_categories.id")
            ->groupBy('faq_categories.id')
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
                "name" => $category->name,
                "description" => $category->description,
                "faq_count" => $category->faq_count,
                "action" => '<a href="'.url('/admin/faq-category/edit').'/'.$category->id.'">
                                <span class="ms-3"><i class="bi-pencil"></i></span>
                            </a>
                            <a href="Javascript:" id="delete-faq-category" data-id="'.$category->id.'">
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
            'name' => 'required|unique:faq_categories,name|max:255',
            'description' => 'nullable|max:4096'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => 1, "message" => $validator->errors()->all()]);
        }

        $faqCategory = new FaqCategory;
        $faqCategory->name = $request->name;
        $faqCategory->slug = Str::slug($request->name);
        $faqCategory->description = $request->description;
        $faqCategory->save();

        $data = '<td>
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$faqCategory->id.'">
                    </div>
                </td>
                <td>'.$faqCategory->name.'</td>
                <td>'.$faqCategory->description.'</td>
                <td>0</td>
                <td>
                    <a href="'.url('/admin/faq-category/edit').'/'.$faqCategory->id.'">
                        <span class="ms-3"><i class="bi-pencil"></i></span>
                    </a>
                    <a href="" id="delete-faq-category" data-id="'.$faqCategory->id.'">
                        <span class="ms-3"><i class="bi-trash"></i></span>
                    </a>
                </td>';

        return response()->json(["data" => $data, "message" => "Faq category added successfully."]);
    }

    public function edit($id)
    {
        $category = FaqCategory::findorfail($id);
        return view("admin.faqcategory.create")->with("category", $category);
    }

    public function update(Request $request, $id)
    {
         $validated = $request->validate([
            'name' => 'required|unique:faq_categories,name,'.$id.'|max:255',
            'description' => 'nullable|max:4096'
        ]);

        $faqCategory = FaqCategory::findorfail($id);
        $faqCategory->name = $request->name;
        $faqCategory->slug = Str::slug($request->name);
        $faqCategory->description = $request->description;
        $faqCategory->save();

        return redirect()->back()->with("success", "Faq category updated successfully");
    }

    public function destroy(Request $request)
    {
        FaqCategory::whereIn("id", $request->id)->delete();

        return response()->json(["message" => "Faq category deleted successfully."]);
    }
}
