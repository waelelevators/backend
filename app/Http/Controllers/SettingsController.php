<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Template;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ContractCollection
     */
    public function index()
    {
        $rate = Setting::where('name', 'tax')->first();
        return $rate;
    }

    function update(Request $request)
    {
        $setting  =  Setting::find($request['id']);

        $setting->data = [
            'rate' => $request['rate'],
            'name' => $request['name']
        ];

        $setting->save();

        return response()->json(['success' => true, 'setting' => $setting]);
    }
}
