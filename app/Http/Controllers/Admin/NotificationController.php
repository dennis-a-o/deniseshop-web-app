<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\Contact;

class NotificationController extends Controller
{
    public function index()
    {
        $orderCount = Order::where('status', 'pending')->count();
        $orders = Order::select('orders.*', 'users.first_name','users.last_name','users.email')
            ->leftJoin('users','users.id','=','orders.user_id')
            ->where('orders.status', 'pending')
            ->limit(20)
            ->get();
        $contactCount = Contact::where('status', 'unread')->count();
        $contacts = Contact::where('status', 'unread')->limit(20)->get();
        $notificationCount = AdminNotification::where('read_at', null)->count();
        $notifications = AdminNotification::where('read_at', null)->limit(20)->get();

        $response = [
            "orderCount" => $orderCount,
            "orders" => $orders,
            "contactCount" => $contactCount,
            "contacts" => $contacts,
            "notificationCount" => $notificationCount,
            "notifications" => $notifications
        ];

        return response()->json($response,200);
    }

    public function clear(Request $request)
    {
        AdminNotification::whereIn('id', $request->id)->delete();
        AdminNotification::where('read_at','!=', null)->delete();//might change later
        return response()->json(['message' => 'Notifications cleared successfully.'],200);
    }

    public function read(Request $request)
    {
        AdminNotification::whereIn('id', $request->id)->update(['read_at' => now()]);
        return response()->json(['message' => 'Notifications read successfully.'],200);
    }

    public function view($id)
    {
        $notif = AdminNotification::find($id);
        $notif->read_at = now();
        $notif->save();

        return response()->json([
            'message' => 'Notifications read successfully.',
            'url' => $notif->action_url
        ],200);
    }

}
