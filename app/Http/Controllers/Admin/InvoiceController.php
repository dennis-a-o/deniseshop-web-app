<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use PDF;

class InvoiceController extends Controller
{
    public function index(Request $request, $id)
    { 
        $order = Order::findorfail($id);
        $setting = Setting::whereIn('key',['contact_address','contact_phone','contact_email'])->get();

        $business =  [
            'address' => $setting[2]->value,
            'phone' => $setting[1]->value,
            'email' => $setting[0]->value
        ];

        $pdf = PDF::loadView('templates.invoice', compact('order','business'));

        if ($request->has('type') && $request->type === "print") {
            return $pdf->stream('invoice.pdf');
        }
        
        return $pdf->download('invoice.pdf');
    }
}
