<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class ContactController extends Controller
{
    public function getContact()
    {
        return response()->json([
            [
                'contact' => Setting::where('key', 'contact_email')->value('value')??"",
                'type' => 'email',
                'description' => Setting::where('key', 'contact_email_text')->value('value')??"",
            ],
            [
                'contact' => Setting::where('key', 'contact_phone')->value('value')??"",
                'type' => 'phone',
                'description' => Setting::where('key', 'contact_phone_text')->value('value')??"",
            ],
            [
                'contact' => Setting::where('key', 'contact_address')->value('value')??"",
                'type' => 'address',
                'description' => Setting::where('key', 'contact_address_text')->value('value')??"",
            ],
        ]);
    }
}
