<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class ReviewController extends Controller
{
    public function index()
    {
        return view("admin.review.index");
    }

    public function reviewList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Review::count();
        $totalRecordWithFilter = Review::where("comment", "like", "%".$searchValue."%")->count();

        $reviews = Review::select('reviews.id','reviews.star', 'reviews.comment', 'reviews.status','reviews.created_at', 'reviews.user_id','reviews.product_id', 'products.name as product_name', 'products.slug as product_slug', 'users.first_name', 'users.last_name')
            ->where("reviews.comment", "like", "%".$searchValue."%")
            ->leftJoin("products","products.id", "=", "reviews.product_id")
            ->leftJoin("users","users.id", "=", "reviews.user_id")
            ->groupBy(['id','star','comment', 'status', 'created_at', 'user_id', 'product_id', 'products.name', 'products.slug', 'users.first_name', 'users.last_name'])
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($reviews as $key => $review) {
            $status = match ($review->status) {
                "pending" => '<span class="badge badge-warning">pending</span>',
                "approved" => '<span class="badge badge-success">Approved</span>',
                "rejected" => '<span class="badge badge-danger">Rejected</span>'
            };

            $data[] = array(
                "id" => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="id" value="'.$review->id.'">
                        </div>',
                "product" => '<a href="'.url('/product').'/'.$review->product_slug.'"><h6>'.$review->product_name.'</h6></a>',
                "customer" => '<a href="'.url('/admin/user/').'/'.$review->user_id.'"><h6>'.$review->first_name.' '.$review->last_name.'</h6></a>',
                "star" => '<div class="d-inline">'.$this->rating($review->star).'</div>',
                "comment" => $review->comment,
                "status" => $status,
                "created_at" => date("d/m/Y", strtotime($review->created_at)),
                "action" => '<a href="'.url('/admin/review/detail').'/'.$review->id.'">
                                <span class=""><i class="bi-eye"></i></span>
                            </a>
                            <a href="Javascript:" id="delete-review" data-id="'.$review->id.'">
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

        return response()->json($response);
    }

    public function detail($id)
    {
        $review = Review::findorfail($id);
        $user = User::where("id", $review->user_id)->first();
        $product = Product::select('products.id', 'products.name', 'products.image')
            ->where('products.id', $review->product_id)
            ->selectRaw('avg(reviews.star) as product_rating')
            ->selectRaw('count(reviews.id) as review_count')
            ->leftJoin('reviews','reviews.product_id', '=', 'products.id')
            ->groupBy(['products.id', 'products.name', 'products.image'])
            ->first();

        return view('admin.review.detail')
                ->with("user", $user)
                ->with("product", $product)
                ->with("review", $review);
    }

    public function status(Request $request)
    {
        Review::whereIn("id", $request->id)->update(["status" => $request->status]);

        return response()->json(["message" => "Review status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        foreach($request->id as $id){
            $review = Review::find($id);
            $images = json_decode($review->images);

            if ($images != null) {
                foreach($images as $image){
                    File::delete(public_path('assets/img/reviews/'.$image.''));
                }
            }
           $review->delete();
        }

        return response()->json(["message" => "Review(s) deleted successfully."]);
    }

    private function rating($num)
    {
        $stars = "";

        for ($i=1; $i <= 5; $i++) { 
            if($i <= $num){
                $stars .= '<i class="bi-star-fill text-warning fs-10"></i>';
            }else{
                $stars .= '<i class="bi-star fs-10"></i>'; 
            }
        }

        return $stars;
    }
}
