<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use Util;

class FlashSaleController extends Controller
{
    public function index()
    {
        return view("admin.flashsale.index");
    }

    public function flashSaleList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = FlashSale::count();
        $totalRecordWithFilter = FlashSale::where("name", "like", "%".$searchValue."%")
           ->count();

        $flashSales = FlashSale::select("flash_sales.*")
            ->where("name", "like", "%".$searchValue."%")
            ->selectRaw('sum(flash_sale_products.quantity) as quantity')
            ->selectRaw('sum(flash_sale_products.sold) as sold')
            ->leftJoin("flash_sale_products", "flash_sale_products.flash_sale_id", "=", "flash_sales.id")
            ->groupBy("flash_sales.id")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($flashSales as $flashSale) {

            $status = match ($flashSale->status) {
                "draft","pending" => '<span class="badge badge-warning">'.$flashSale->status.'</span>',
                "published" => '<span class="badge badge-success">published</span>',
            };

            $end_date = "";
            if (strtotime($flashSale->end_date) > time()) {
                $end_date = '<span class="text-success">'. date("d/m/Y", strtotime($flashSale->end_date)).'</span>';
            }else{
                 $end_date = '<span class="text-warning">'. date("d/m/Y", strtotime($flashSale->end_date)).'</span>';
            }

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$flashSale->id.'">
                        </div>' ,
                'name' => ' <a href="'.url('/admin/flash-sale/edit').'/'.$flashSale->id.'">'.$flashSale->name.'</a>',
                'sold' => "<span>".$flashSale->sold." of ".$flashSale->quantity."</span>",
                'end_date' => $end_date,
                'created_at' => date("d/m/Y", strtotime($flashSale->created_at)),
                'status' => $status,
                'action' => ' <a href="'.url('/admin/flash-sale/edit').'/'.$flashSale->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-flash-sale" data-id="'.$flashSale->id.'">
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

    public function searchProduct(Request $request)
    {
        $start = $request["offset"]-1;
        $perPage = $request["per_page"];
        $searchValue = $request["search_value"];

        $total = Product::where("name", "like", "%".$searchValue."%")->count();

        $products = Product::select("id", "name", "price", "image", "quantity")
            ->where("name", "like", "%".$searchValue."%")
            ->skip($start)
            ->take($perPage)
            ->orderBy("id", "desc")
            ->get();

        $data =  "";
        foreach ($products as $product) {
            $data .= '<li class="list-group-item pointer" id="search-item" data-id="'.$product->id.'" data-image="'.url('/assets/img/products').'/'.$product->image.'" data-price="'.$product->price.'" data-url="'.url('/admin/product/edit').'/'.$product->id.'" data-title="'.$product->name.' ('.Util::currencySymbol().$product->price.')" data-quantity="'.$product->quantity.'">
                            <div class="d-flex align-items-center">
                                <img class="rounded-2 me-4" src="'.url('/assets/img/products').'/'.$product->image.'" width="30" height="30">
                                <span>'.$product->name.'</span>
                            </div>
                        </li>';
        }

        return response()->json(["total" => $total,"data" => $data]);
    }

    public function create()
    {
         return view("admin.flashsale.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string",
            "description" => "nullable|string",
            "product" => "required|array",
            "status" => "required|string",
            "end_date" => "required|date",
            "image" => "nullable|image|max:4096"
        ]);

        $imageName = "default.jpg";
        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/flashsale', $imageName);
        }

        $flashSale = new FlashSale;
        $flashSale->name = $request->name;
        $flashSale->description = $request->description;
        $flashSale->image = $imageName;
        $flashSale->status = $request->status;
        $flashSale->end_date = $request->end_date;
        $flashSale->save();

        foreach($request->product as $key => $value){
            FlashSaleProduct::insert([
                "flash_sale_id" => $flashSale->id,
                "product_id" => $key,
                "price" => $value["price"],
                "quantity" => $value["quantity"]
            ]);
        }

        return redirect("/admin/flash-sale/edit"."/".$flashSale->id)->with("success", "Flash sale added successfully.");
    }

    public function edit($id)
    {
        $flashSale = FlashSale::findorfail($id);
        $flashSaleProduct = FlashSaleProduct::select("flash_sale_products.*", "products.image", "products.name", "products.price as product_price")
            ->where("flash_sale_id", $flashSale->id)
            ->leftJoin("products", "products.id", "=", "flash_sale_products.product_id")
            ->get();

        return view("admin.flashsale.edit")
            ->with("flashSale", $flashSale)
            ->with("flashSaleProduct", $flashSaleProduct);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name" => "required|string",
            "description" => "nullable|string",
            "product" => "required|array",
            "status" => "required|string",
            "end_date" => "required|date",
            "image" => "nullable|image|max:4096"
        ]);

        $imageName = "default.jpg";
        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/flashsale', $imageName);
        }

        $flashSale = FlashSale::findorfail($id);
        $flashSale->name = $request->name;
        $flashSale->description = $request->description;
        $flashSale->status = $request->status;
        $flashSale->end_date = $request->end_date;
       
        if ($request->hasFile("image")) {
            if ($flashSale->image !== "default.jpg") {
                File::delete(public_path('assets/img/flashsale/'.$flashSale->image.''));
            }
            $flashSale->image = $imageName;
        }

        $flashSale->save();

        FlashSaleProduct::where("flash_sale_id",$id)->delete();

        foreach($request->product as $key => $value){
            FlashSaleProduct::insert([
                "flash_sale_id" => $flashSale->id,
                "product_id" => $key,
                "price" => $value["price"],
                "quantity" => $value["quantity"]
            ]);
        }

        return redirect()->back()->with("success", "Flash sale updated successfully.");
    }

    public function status(Request $request)
    {
        FlashSale::whereIn("id", $request->id)->update(["status" => $request->status]);
        return response()->json(["message" => "Status updated successfully."]);
    }


    public function destroy(Request $request)
    {
        foreach($request->id as $id){
            $flashSale = FlashSale::find($id);
            if ($flashSale->image !== "default.jpg") {
                File::delete(public_path('assets/img/flashsale/'.$flashSale->image.''));
            }
            FlashSaleProduct::where("flash_sale_id", $id)->delete();
            $flashSale->delete();
        }

         return response()->json(["message" => "Flashsale(s) deleted successfully."]);
    }
}
