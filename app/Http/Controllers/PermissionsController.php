<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Rule;
use App\Models\RuleCategory;
use App\Models\RuleItems;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return RuleCategory::all();
    }

    public function rules($id = null)
    {
        if ($id == null) {
            return Rule::all()->map(function ($rule) {
                return [
                    'id' => $rule->id,
                    'name' => $rule->display_name,
                    'display_name' => $rule->display_name
                ];
            });
        }
        $items = RuleItems::where('category_id', $id)->pluck('rule_id')->toArray();
        $rules = Rule::all();

        return $rules->map(function ($rule) use ($items) {
            return [
                'id' => $rule->id,
                'name' => $rule->name,
                'display_name' => $rule->display_name,
                'selected' => in_array($rule->id, $items)
            ];
        });
    }


    public function ruleItems($id)
    {

        return RuleItems::where('category_id', $id)->with('rule')->get();
    }

    function store(Request $request)
    {

        $user =  $request->user();

        $user->syncPermissions($request->permissions);


        return  $user->getPermissionNames();
    }

    function updateRule2(Request $request)
    {
        foreach ($request->rules as $rule) {
            $ruleItem = new RuleItems();
            $ruleItem->rule_id = $rule['id'];
            $ruleItem->category_id = $request->ruleCategoryId;
            $ruleItem->save();
        }
    }

    function updateRule(Request $request)
    {


        foreach ($request->rules as $rule) {

            $ruleItem = RuleItems::where('rule_id', $rule['id'])->where(
                'category_id',
                $request->ruleCategoryId
            )->first();
            if ($rule['selected'] == true) {

                if ($ruleItem == null) {
                    $ruleItem = new RuleItems();
                    $ruleItem->rule_id = $rule['id'];
                    $ruleItem->category_id = $request->ruleCategoryId;
                    $ruleItem->save();
                }
            } else {

                if ($ruleItem != null) {
                    $ruleItem->delete();
                }
            }
        }


        $rules = Rule::all();

        $items = RuleItems::where('category_id', $request->ruleCategoryId)->pluck('rule_id')->toArray();

        return $rules->map(function ($rule) use ($items) {
            return [
                'id' => $rule->id,
                'name' => $rule->name,
                'display_name' => $rule->display_name,
                'selected' => in_array($rule->id, $items)
            ];
        });
    }


    function ruleCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:rule_categories,name',
        ], [
            'name.required' => 'يرجى ادخال الاسم',
            'name.unique' => 'الاسم موجود مسبقا',
        ]);

        $cat = new RuleCategory();
        $cat->name = $request->name;
        $cat->save();


        return RuleCategory::all();
    }
}
