<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

use Util;//Helper for locations and currency

class PaymentController extends Controller
{
    public function index()
    {
        return view("admin.payment.index");
    }

    public function paymentList(Request $request)
    {
        $draw = $request['draw'];
        $row = $request['start']; 
        $rowPerPage = $request['length'];
        $columnIndex = $request['order'][0]['column']; 
        $columnName =  $request['columns'][$columnIndex]['data']; 
        $columnSortOrder = $request['order'][0]['dir'];
        $searchValue = $request['search']['value'];

        $totalRecord = Payment::count();
        $totalRecordWithFilter = Payment::where("transaction_id", "like", "%".$searchValue."%")->count();

        $payments = Payment::select("payments.*", "users.first_name", "users.last_name")
            ->where("transaction_id", "like", "%".$searchValue."%")
            ->leftJoin("users", "users.id","=","payments.user_id")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($row)
            ->take($rowPerPage)
            ->get();

        $data = [];
        foreach ($payments as $payment) {

            $status = match ($payment->status) {
                "pending" => '<span class="badge badge-warning">'.$payment->status.'</span>',
                "refunded" => '<span class="badge badge-info">'.$payment->status.'</span>',
                "completed" => '<span class="badge badge-success">'.$payment->status.'</span>',
                "failed", "fraud" => '<span class="badge badge-danger">'.$payment->status.'</span>',
                default => '<span class="badge badge-warning">'.$payment->status.'</span>',
            };

            
            $data[] = [
                'id' => '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="check-item" name="" value="'.$payment->id.'">
                        </div>' ,
                'transaction_id' => '<a href="'.url('/admin/payment/transaction/edit').'/'.$payment->id.'">'.$payment->transaction_id.'</a>',
                'first_name' => $payment->first_name." ".$payment->last_name,
                'amount' => $payment->currency.' '.$payment->amount,
                'payment_channel' => $payment->payment_channel,
                'status' => $status,
                'created_at' => date("d/m/Y", strtotime($payment->created_at)),
                'action' => ' <a  href="'.url('/admin/payment/transaction/edit').'/'.$payment->id.'">
                                <span class="me-2">
                                    <i class="bi-pencil"></i>
                                </span>
                            </a><a  href="Javascript:" id="delete-item" data-id="'.$payment->id.'">
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

    public function edit(Request $request, $id)
    {
        $payment = Payment::findorfail($id);
        return view("admin.payment.edit")->with("payment", $payment);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "status" => "required|string",
        ]);

        $payment = Payment::findorfail($id);
        $payment->status = $request->status;
        $payment->save();

        return redirect()->back()->with("success", "Transaction updated successfully.");
    }

    public function status(Request $request)
    {
        Payment::whereIn("id", $request->id)->update(["status" => $request->status]);
        return response()->json(["message" => "Transaction(s) status updated successfully."]);
    }

    public function destroy(Request $request)
    {
        Payment::whereIn("id", $request->id)->delete();
        return response()->json(["message" => "Transaction(s) deleted successfully."]);
    }
}
