<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\Template;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

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
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'templateName' => 'required|string|unique:templates,name',
            'content' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'please Try other name the name already exists',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create and save the template
        $temp = new Template();
        $temp->name = $request->templateName;
        $temp->data = $request->content;
        $temp->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم الاضافة بنجاح',
        ]);
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
