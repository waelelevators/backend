<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\RuleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EmployeeStoreResquest;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Employee::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeStoreResquest $request)
    {

        DB::transaction(function () use ($request) {

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->level = $request->department;
            $user->rule_category_id = $request->rule_category_id;
            $user->save();

            $employee = new Employee;
            $employee->name            = $request->name;
            $employee->department      = $request->department;
            $employee->user_id         = $user->id;
            $employee->technician_info = [
                'mechanical' => $request->mechanical,
                'electricity' => $request->electricity,
            ];

            $employee->save();

            return response()->json([
                'status' => 'success',
                'message' => 'تم اضافة الموظف بنجاح',
            ]);
        });
    }


    public function update(Request $request)
    {
        // $request->validate([
        //     'name' => 'required',
        //     'department' => 'required',
        //     'department' => 'required',
        //     'mechanical' => 'nullable|integer',
        //     'electricity' => 'nullable|integer',
        // ]);

<<<<<<< HEAD

        $employee->name            = $request->employee['name'];
        $employee->department      = $request->employee['department'];
        $employee->technician_info = [
            'mechanical' => $request['mechanical'],
            'electricity' => $request['electricity'],
>>>>>>> 1ebb111 (Maintenance Part)
        ];

        $employee->save();
        $user = User::find($employee->user_id);

        $user->rule_category_id = $request['rule_category_id'];
        $user->email = $request['email'];
        $user->name = $request['name'];
        $user->save();




    function show(Employee $employee)
    {
            'employeeData' => $employee,
            'rules' => RuleCategory::all(),
            'user' => User::find($employee->user_id),
        ]);
    }
}
