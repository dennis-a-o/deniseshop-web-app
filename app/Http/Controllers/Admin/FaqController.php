<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Faq;
use App\Models\FaqCategory;

class FaqController extends Controller
{
    public function index()
    {
        return view("admin.faq.index");
    }

    public function faqList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Faq::count();
        $totalRecordWithFilter = Faq::where("question", "like", "%".$searchValue."%")
           ->count();

        $faqs = Faq::select("faqs.*","faq_categories.name as category_name")
            ->where("question", "like", "%".$searchValue."%")
            ->leftJoin("faq_categories", "faq_categories.id", "=", "faqs.category_id")
            ->groupBy("faqs.id")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($faqs as $faq) {

            $status = match ($faq->status) {
                "draft","pending" => '<span class="badge badge-warning">'.$faq->status.'</span>',
                "published" => '<span class="badge badge-success">published</span>',
            };

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$faq->id.'">
                        </div>' ,
                'question' => ' <a href="'.url('/admin/faq/edit').'/'.$faq->id.'">'.$faq->question.'</a>',
                'category_id' => $faq->category_name,
                'created_at' => date("d/m/Y", strtotime($faq->created_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/faq/edit').'/'.$faq->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-faq" data-id="'.$faq->id.'">
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
         $categories = FaqCategory::get();
        return view("admin.faq.create")->with("categories", $categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "question" => "required|unique:faqs,question",
            "category_id" => "required|integer",
            "answer" => "required|string",
            "status" => "required|string"
        ]);

        $data = $request->except(["_token"]);
        $data["created_at"] = now();
        $id = Faq::insertGetId($data);

        return redirect("/admin/faq/edit"."/".$id)->with("success", "Faq added successfully.");
    }

    public function edit($id)
    {
        $faq = Faq::findorfail($id);
        $categories = FaqCategory::get();

        return view("admin.faq.edit")
            ->with("faq", $faq)
            ->with("categories", $categories);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "question" => "required|unique:faqs,question,".$id."",
            "category_id" => "required|integer",
            "answer" => "required|string",
            "status" => "required|string"
        ]);

        $data = $request->except(["_token"]);
        $data["updated_at"] = now();
        $faq = Faq::findorfail($id);
        $faq->update($data);

        return redirect()->back()->with("success", "Faq updated successfully.");
    }

    public function status(Request $request)
    {
        Faq::whereIn("id", $request->id)->update(["status" => $request->status]);
        return response()->json(["message" => "Faqs status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        Faq::whereIn("id", $request->id)->delete();
        return response()->json(["message" => "Faqs status deleted successfully."]);
    }
}
