<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Tag;
use App\Models\ProductTag;
use App\Models\Brand;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;


class ProductsController extends Controller
{
    public function index()
    {
        $categories = Category::where('parent_id',0)->get();
        return view('admin.product.index')
            ->with('categories', $categories);
    }

    public function productList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Product::count();
        $totalRecordWithFilter = Product::where("name", "like", "%".$searchValue."%")
            ->when($request->has("filter"), function($query)use($request){
                $query->where($request->filter, $request->filterValue);
            })
            ->count();

        $products = Product::select('products.id', 'products.name','products.slug', 'products.image','price','sku', 'quantity', 'stock_status','quantity','status','products.created_at')
            ->selectRaw("group_concat(distinct categories.name separator ',') as 'categories'")
            ->where("products.name", "like", "%".$searchValue."%")
            ->when($request->has("filter"), function($query)use($request){
                $query->where($request->filter, $request->filterValue);
            })
            ->leftJoin('product_category', 'Products.id', '=', 'product_category.product_id')
            ->leftJoin("categories","product_category.category_id", "=", "categories.id")
            ->groupBy('products.id')
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($products as $product) {

            $status = ""; 
            $stockStatus = "";

            if ($product->stock_status == "in_stock") {
                $stockStatus = '<span class="badge badge-success">In stock</span>';
            }else{
                $stockStatus = '<span class="badge badge-warning">Out stock</span>';
            }

            if ($product->status == "published") {
                $status = '<span class="badge badge-success">Published</span>';
            }else{
                $status = '<span class="badge badge-warning">'.$product->status.'</span>';
            }

            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$product->id.'">
                        </div>' ,
                'name' => '<div class="d-flex align-items-center">
                                <img class="ms-2 rounded-3" src="'.url('assets/img/products').'/'.$product->image.'" height="35" width="35">
                                <a href="'.url('/admin/product/edit').'/'.$product->id.'">
                                    <h6 class="text-small mb-0 ms-2">'.$product->name.'</h6>
                                </a>
                            </div>',
                'category' => $product->categories ?? "Default",
                'price' => "Ksh ".$product->price,
                'sku' => $product->sku,
                'stock_status' => $stockStatus,
                'quantity' => $product->quantity,
                'created_at' => date("d/m/Y", strtotime($product->created_at)),
                'status' => $status,
                'action' => '<a href="'.url('/product').'/'.$product->slug.'">
                                <span class="me-2">
                                    <i class="bi-eye"></i>
                                </span>
                            </a>
                            <a href="'.url('/admin/product/edit').'/'.$product->id.'">
                                <span class="me-2">
                                    <i class="bi-pen"></i>
                                </span>
                            </a>
                            <a  href="Javascript:" id="delete-product" data-productid="'.$product->id.'">
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

        return response()->json($response, 200);
    }

    public function create()
    {
        $categories = Category::where("parent_id", 0)->get();
        $brands = Brand::get();
        return view('admin.product.create')
            ->with("categories", $categories)
            ->with("brands", $brands);

    }

    public function store(Request $request)
    {
        $validated =  $request->validate([
            'name' => 'required|unique:products,name|max:255',
            'description' => 'nullable|string',
            'description_summary' => 'nullable|string|max:500',
            'type' => 'required|string',
            'url' => 'nullable|url',
            'button_text' => 'nullable|string',
            'price' => 'required|integer',
            'sale_price' => 'nullable|integer',
            'quantity' => 'required|integer',
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'downloadable' => 'nullable|boolean',
            'download_file' => 'nullable|file|mimes:zip,rar,7z,mp4,mkv|max:105000',
            'download_limit' => 'nullable|integer',
            'download_expiry' => 'nullable|integer',
            'sku' => 'nullable|string',
            'stock_status' => 'nullable|string',
            'weight' => 'nullable|integer',
            'length' => 'nullable|integer',
            'width' => 'nullable|integer',
            'height' => 'nullable|integer',
            'size' => 'nullable|array',
            'ram' => 'nullable|integer',
            'rom' => 'nullable|integer',
            'screen_size' => 'nullable|integer',
            'status' => 'required|string',
            'image' => 'required|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'category.*' => 'nullable|integer',
            'tag' => 'nullable|array',
            'brand' => 'nullable|integer',
            'color' => 'nullable|array',
            'size' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'newsletter' => 'nullable|boolean'
        ]);

        $imageName = "default.jpg";
        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/products', $imageName);
        }

        $galleryNames = [];
        if ($request->hasFile("gallery")) {
            $files = $request->file("gallery");

            foreach($files as $key => $file){
                $filenameWithExt = $file->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $galleryName = $filename.'_'.time().'.'.$extension;
                //move image
                $path = $file->move('assets/img/products', $galleryName);
                $galleryNames[$key] = $galleryName;
            }
        }

        $downloadFileName = "";
        if ($request->hasFile("download_file")) {
            $file = $request->file("download_file");

            $downloadFileName = Storage::put("uploads", $file, "private");
        }

        $product = new Product;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name); 
        $product->sku = $request->sku; 
        $product->description = $request->description; 
        $product->description_summary = $request->description_summary; 
        $product->image = $imageName;
        $product->gallery = json_encode($galleryNames);
        $product->price = $request->price; 
        $product->sale_price = $request->sale_price;
        $product->start_date = $request->start_date; 
        $product->end_date = $request->end_date;
        $product->quantity = $request->quantity;
        $product->brand_id = $request->brand; 
        $product->stock_status = $request->stock_status;
        $product->status = $request->status; 
        $product->type = $request->type;
        $product->url = $request->url;
        $product->button_text = $request->button_text;
        $product->download_file = $downloadFileName; 
        $product->downloadable = $request->downloadable?? false; 
        $product->download_limit = $request->download_limit;
        $product->download_expiry = $request->download_expiry;
        $product->weight = $request->weight;
        $product->length = $request->length; 
        $product->width = $request->width;
        $product->height = $request->height;
        $product->ram = $request->ram;
        $product->rom = $request->rom;
        $product->is_featured = $request->is_featured;
        $product->screen_size = $request->screen_size;
        $product->save();

        if ($request->has("category")) {
            foreach($request->category as $category){
                $productCategory = new ProductCategory;
                $productCategory->product_id = $product->id;
                $productCategory->category_id = $category;
                $productCategory->save();
            }
        }

        if ($request->has("tag") && (count($request->tag))) {
            foreach($request->tag as $tag){
                $productTag = new ProductTag;
                $productTag->product_id = $product->id;
                $productTag->tag_id = $tag;
                $productTag->save();
            }
        }

        if ($request->has("color") && (count($request->color))) {
            foreach ($request->color as $key => $color) {
                $colorAttribute = new ProductAttribute;
                $colorAttribute->product_id = $product->id;
                $colorAttribute->type = "color";
                $colorAttribute->name = $key;
                $colorAttribute->slug = Str::slug($key);
                $colorAttribute->value = $color;
                $colorAttribute->save();
            }
        }

        if ($request->has("size") && (count($request->size))) {
            foreach ($request->size as $size) {
                $sizeAttribute = new ProductAttribute;
                $sizeAttribute->product_id = $product->id;
                $sizeAttribute->type = "size";
                $sizeAttribute->name =  $size;
                $sizeAttribute->slug  = $size;
                $sizeAttribute->save(); 
            }
        }

        if ($request->has("newsletter")) {
            //TODO
        }

        return redirect("/admin/products")->with('message', 'Product created successfully.');
    }

    public function edit($id)
    {   
        $product = Product::findorfail($id);
        $productColor = ProductAttribute::where("product_id", $product->id)
            ->where("type", "color")
            ->get();

        $productSize =  ProductAttribute::where("product_id", $product->id)
            ->where("type", "size")
            ->get();

        $productTag = ProductTag::select('tags.id', 'tags.name')
            ->where("product_id", $product->id)
            ->leftJoin("tags" ,"tags.id", "=", "product_tag.tag_id")
            ->get();

        $categories = Category::where("parent_id", 0)->get();

        $productCategories = Product::select('product_category.category_id')
            ->join('product_category','products.id', '=', 'product_category.product_id')
            ->where('product_id', $product->id)
            ->get()->map(function($item){
                return $item->category_id;
            })->toArray();

        $brands = Brand::get();

        return view('admin.product.edit')
            ->with("product", $product)
            ->with("productSize", $productSize)
            ->with("productColor", $productColor)
            ->with("productTag", $productTag)
            ->with("categories", $categories)
            ->with("productCategories", $productCategories)
            ->with("brands", $brands);
    }

    public function update(Request $request, $id)
    {
         $validated =  $request->validate([
            'name' => 'required|unique:products,name,'.$id.'|max:255',
            'description' => 'nullable|string',
            'description_summary' => 'nullable|string|max:500',
            'type' => 'required|string',
            'url' => 'nullable|url',
            'button_text' => 'nullable|string',
            'price' => 'required|integer',
            'sale_price' => 'nullable|integer',
            'quantity' => 'required|integer',
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'downloadable' => 'nullable|boolean',
            'download_file' => 'nullable|file|mimes:zip,rar,7z,mkv,mp4|max:105000',
            'download_limit' => 'nullable|integer',
            'download_expiry' => 'nullable|integer',
            'sku' => 'nullable|string',
            'stock_status' => 'nullable|string',
            'weight' => 'nullable|integer',
            'length' => 'nullable|integer',
            'width' => 'nullable|integer',
            'height' => 'nullable|integer',
            'size' => 'nullable|array',
            'ram' => 'nullable|integer',
            'rom' => 'nullable|integer',
            'screen_size' => 'nullable|integer',
            'status' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'category.*' => 'nullable|integer',
            'tag' => 'nullable|array',
            'brand' => 'nullable|integer',
            'color' => 'nullable|array',
            'size' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'newsletter' => 'nullable|boolean'
        ]);

        $imageName = "";
        if ($request->hasFile("image")) {
            $file = $request->file("image");

            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $imageName = $filename.'_'.time().'.'.$extension;
            //move image
            $path = $file->move('assets/img/products', $imageName);
        }

        $galleryNames = [];
        if ($request->hasFile("gallery")) {
            $files = $request->file("gallery");

            foreach($files as $key => $file){
                $filenameWithExt = $file->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $galleryName = $filename.'_'.time().'.'.$extension;
                //move image
                $path = $file->move('assets/img/products', $galleryName);
                $galleryNames[$key] = $galleryName;
            }
        }

        $downloadFileName = "";
        if ($request->hasFile("download_file")) {
            $file = $request->file("download_file");

            $downloadFileName = Storage::put("uploads", $file, "private");
        }

        $product = Product::findorfail($id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name); 
        $product->sku = $request->sku; 
        $product->description = $request->description; 
        $product->description_summary = $request->description_summary; 
        $product->price = $request->price; 
        $product->sale_price = $request->sale_price;
        $product->start_date = $request->start_date; 
        $product->end_date = $request->end_date;
        $product->quantity = $request->quantity;
        $product->brand_id = $request->brand;
        $product->stock_status = $request->stock_status;
        $product->status = $request->status; 
        $product->type = $request->type;
        $product->url = $request->url;
        $product->button_text = $request->button_text; 
        $product->downloadable = $request->downloadable ?? 0; 
        $product->download_limit = $request->download_limit;
        $product->download_expiry = $request->download_expiry;
        $product->weight = $request->weight;
        $product->length = $request->length; 
        $product->width = $request->width;
        $product->height = $request->height;
        $product->ram = $request->ram;
        $product->rom = $request->rom;
        $product->is_featured = $request->is_featured;
        $product->screen_size = $request->screen_size;

        if ($request->hasFile("image")) {
            if ($product->image != "") {
                File::delete(public_path('assets/img/products/'.$product->image.''));
            }
            $product->image = $imageName;
        }

        if ($request->hasFile("gallery")) {
            $galleryNamesOne = json_decode($product->gallery); 
            $combinedGallery = array_merge($galleryNamesOne, $galleryNames);

            $product->gallery = $combinedGallery;
        }

        if ($request->hasFile("download_file")) {
            Storage::delete($product->download_file);
            $product->download_file = $downloadFileName;
        }

        $product->save();

        if ($request->has("category")) {
            ProductCategory::where("product_id", $id)->delete();
            foreach($request->category as $category){
                $productCategory = new ProductCategory;
                $productCategory->product_id = $product->id;
                $productCategory->category_id = $category;
                $productCategory->save();
            }
        }

        if ($request->has("tag") && (count($request->tag))) {
            ProductTag::where("product_id", $id)->delete();
            foreach($request->tag as $tag){
                $productTag = new ProductTag;
                $productTag->product_id = $product->id;
                $productTag->tag_id = $tag;
                $productTag->save();
            }
        }

        if ($request->has("color") && (count($request->color))) {
            ProductAttribute::where("product_id", $id)->where("type","color")->delete();
            foreach ($request->color as $key => $color) {
                $colorAttribute = new ProductAttribute;
                $colorAttribute->product_id = $product->id;
                $colorAttribute->type = "color";
                $colorAttribute->name = $key;
                $colorAttribute->slug = Str::slug($key);
                $colorAttribute->value = $color;
                $colorAttribute->save();
            }
        }

        if ($request->has("size") && (count($request->size))) {
              ProductAttribute::where("product_id", $id)->where("type","size")->delete();
            foreach ($request->size as $size) {
                $sizeAttribute = new ProductAttribute;
                $sizeAttribute->product_id = $product->id;
                $sizeAttribute->type = "size";
                $sizeAttribute->name =  $size;
                $sizeAttribute->slug  = $size;
                $sizeAttribute->save(); 
            }
        }

       

        if ($request->has("newsletter")) {
            //TODO
        }

        return redirect()->back()->with('message', 'Product updated successfully.');
    }

    public function destroy(Request $request)
    {
        foreach ($request->id as $id) {
            $product = Product::findorfail($id);

            Review::where("product_id", $id)->delete();
            Wishlist::where("product_id", $id)->delete();
            Cart::where("product_id", $id)->delete();
            ProductTag::where("product_id", $id)->delete();
            ProductAttribute::where("product_id", $id)->delete();
            ProductCategory::where('product_id', $id)->delete();

            if ($product->image != "") {
                File::delete(public_path('assets/img/products/'.$product->image.''));
            }

            if ($product->download_file !== null) {
                Storage::delete($product->download_file);
            }

            $galleries = json_decode($product->gallery);
            foreach($galleries as $gallery){
                File::delete(public_path('assets/img/products/'.$gallery.''));
            }

            $product->delete();
        }

        return response()->json(["message" => "Products deleted successfully."],200);
    }

    public function status(Request $request)
    {
        Product::whereIn('id', $request->id)->update(["status" => $request->status]);

        return response()->json(['message' => "Products status updated successfully."],200);
    }

    public function tagCreate(Request $request)
    {
        $tag = Tag::where("name", $request->tag)->first();

        if ($tag != null) {
            return response()->json(["id" => $tag->id]);
        }else{
             $tagId = Tag::insertGetId(["name" => $request->tag, "slug" => Str::slug($request->tag)]);
            return response()->json(["id" => $tagId]);
        }
    }

    public function tagDestroy(Request $request)
    {
        ProductTag::where('product_id', $request->product_id)
            ->where('tag_id', $request->tag_id)
            ->delete();

        return response()->json(['message' => "ok"], 200);
    }


    public function galleryDestroy(Request $request)
    {
        File::delete(public_path('assets/img/products/'.$request->image_name.''));

        $product = Product::find($request->product_id);
        $galleries = json_decode($product->gallery);

        $newGallery = [];
        for ($i=0; $i < count($galleries); $i++) { 
            if ($galleries[$i] != $request->image_name) {
                $newGallery[$i] = $galleries[$i];
            }
        }

        $product->gallery = json_encode($newGallery);
        $product->save();

         return response()->json(['message' => "Gallery deleted successfully."], 200);
    }

}
