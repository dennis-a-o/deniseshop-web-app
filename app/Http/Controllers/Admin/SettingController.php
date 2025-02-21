<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;

class SettingController extends Controller
{
    public function general()
    {
        $arr = array(
                'logo_light',
                'logo_dark','favicon',
                'contact_email',
                'contact_email_text',
                'contact_phone',
                'contact_phone_text',
                'contact_address',
                'contact_address_text',
                'longitude',
                'latitude',
                'social_link'
            );

        $data = array();

        $settings = Setting::select('key', 'value')->whereIn('key', $arr)->get();
        foreach ($settings as $setting) {
            if ($setting->key == "social_link") {
                $data[$setting->key] = json_decode($setting->value);
                continue;
            }
            $data[$setting->key] = $setting->value;
        }

        return view('admin.setting.general')
            ->with('title', 'General Setting')
            ->with('setting', $data);
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'logo_light' => 'nullable|image|max:10240',
            'logo_dark' => 'nullable|image|max:10240',
            'favicon' => 'nullable|image|max:10240',
            'contact_email' => 'required|email',
            'contact_email_text' => 'required|string',
            'contact_phone' => 'required|string|max:15',
            'contact_phone_text' => 'required|string',
            'contact_address' => 'required|string',
            'contact_address_text' => 'required|string',
            'longitude' => 'required|integer',
            'latitude' => 'required|integer',
            'social_link' => 'required|array'
        ]);

        if ($request->hasFile('logo_light')) {
            $filenameWithExt = $request->file('logo_light')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('logo_light')->getClientOriginalExtension();
            $logo_light = 'logo_light'.'.'.$extension;
            // Upload Image
            $path = $request->file('logo_light')->move('assets/img/general', $logo_light);
        }

        if ($request->hasFile('logo_dark')) {
            $filenameWithExt = $request->file('logo_dark')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('logo_dark')->getClientOriginalExtension();
            $logo_dark = 'logo_dark'.'.'.$extension;
            // Upload Image
            $path = $request->file('logo_dark')->move('assets/img/general', $logo_dark);
        }

        if ($request->hasFile('favicon')) {
            $filenameWithExt = $request->file('favicon')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('favicon')->getClientOriginalExtension();
            $favicon = 'favicon'.'.'.$extension;
            // Upload Image
            $path = $request->file('favicon')->move('assets/img/general', $favicon);
        }

        $social_link = [];
        foreach ($request->social_link as $key => $value) {
            $social_link[$key] = $value;
        }
        $social_link_data = json_encode($social_link);

        $values = [
            'contact_email' => $request->contact_email,
            'contact_email_text' => $request->contact_email_text,
            'contact_phone' => $request->contact_phone,
            'contact_phone_text' => $request->contact_phone_text,
            'contact_address' => $request->contact_address,
            'contact_address_text' => $request->contact_address_text,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'social_link' => $social_link_data,
        ];

        if ($request->hasFile('logo_light')) {
            $values['logo_light'] = $logo_light;
        }
        if ($request->hasFile('logo_dark')) {
             $values['logo_dark'] = $logo_dark;
        }
         if ($request->hasFile('favicon')) {
             $values['favicon'] = $favicon;
        }

        foreach ($values as $key => $value) {
            Setting::UpdateOrCreate(['key' => $key],['value' => $value]);
        }
        
        return redirect('/admin/setting/general')->with('message', 'Settings updated successfully');
    }

    public function ecommerce()
    {
        $arr = array('currency_code', 'shop_name','weight_unit', 'length_unit','company_name','country');

        $data = array();

        $settings = Setting::select('key', 'value')->whereIn('key', $arr)->get();
        foreach ($settings as $setting) {
            $data[$setting->key] = $setting->value;
        }

        return view('admin.setting.ecommerce')
            ->with('title', 'Ecommerce Setting')
            ->with('setting', $data);
    }

    public function updateEcommerce(Request $request)
    {
        $validated = $request->validate([
            "currency_code" => "required|string",
            "shop_name" => "required|string|max:50",
            "weight_unit" => "required|string|max:10",
            "length_unit" => "required|string|max:10",
            "company_name" => "required|string",
            "country" => "required|string"
        ]);

        $data = $request->except(["_token"]);

        foreach ($data as $key => $value) {
            Setting::UpdateOrCreate(['key' => $key],['value' => $value]);
        }
        
       return redirect()->back()->with('message', 'Settings updated successfully');
    }

    public function email()
    {
        $arr = array('email_sender_name','contact_email','no_reply_email');

        $data = array();

        $settings = Setting::select('key', 'value')->whereIn('key', $arr)->get();
        foreach ($settings as $setting) {
            $data[$setting->key] = $setting->value;
        }

        return view('admin.setting.email')
            ->with('title', 'Email Setting')
            ->with('setting', $data);
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            "email_sender_name" => "required|string|max:100",
            "contact_email" => "required|email",
            "no_reply_email" => "required|email"
        ]);

        $data = $request->except(["_token"]);

        foreach ($data as $key => $value) {
            Setting::UpdateOrCreate(['key' => $key],['value' => $value]);
        }
        
       return redirect()->back()->with('message', 'Settings updated successfully');
    }

    public function setTheme(Request $request)
    {
       $validator = Validator::make($request->all(),[
            "theme" => "required|string|max:255"
       ]);

       if ($validator->fails()) {
            return response()->json(["error" => true, "message" => $validator->errors()->all()]);
        }

        session(['theme' => $request->theme]);

        return response()->json(["error" => false, "message" => "Theme set successfully"]);
    }

    public function setPalette(Request $request)
    {
        
       $validator = Validator::make($request->all(),[
            "palette" => "required|string|max:255"
       ]);

       if ($validator->fails()) {
            return response()->json(["error" => true, "message" => $validator->errors()->all()]);
        }

        session(['palette' => $request->palette]);
        
        return response()->json(["error" => false, "message" => "Palette set successfully"]);
    }
}
