<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\Template;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TemplateController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ContractCollection
     */

    public function index()
    {
        return Template::get(['id', 'name']);
    }


    public function show($id)
    {
        return Template::findOrFail($id);
    }

    function update($type, Request $request)
    {
        // update settings when name = type
        $data =  $request->all();
        $Setting =  Template::findOrFail($type);
        $Setting->data =  $data;
        $Setting->save();

        return $Setting;
    }


    function updateTemplate(Request $request)
    {
        return $request;
    }
}
