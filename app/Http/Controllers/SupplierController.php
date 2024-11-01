<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return Supplier::get();
        //  return User::where('level', 'supplier')->get();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSupplierRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'address' => 'required',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->level = 'supplier';
        $user->rule_category_id = 0;
        $user->area_id = 1;
        $user->password = bcrypt('password');
        $user->save();

        $supplier = new Supplier();
        $supplier->user_id = $user->id;
        $supplier->supplier_name = $user->name;
        $supplier->contact_name = $user->name;
        $supplier->email = $user->email;
        $supplier->phone = $user->phone;
        $supplier->note = '';
        $supplier->save();

        return $supplier;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */

    public function show(Supplier $supplier)
    {
        return $supplier;
    }
    public function payments(Supplier $supplier)
    {
        return $supplier->load('payments', 'payments.user', 'payments.invoice');
    }
    public function invoices(Supplier $supplier)
    {
        $invoices = Invoice::whereHas(
            'invoice_details',
            function ($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            }


        )
            ->with('rfq')
            ->get();

        return $invoices;

        return $supplier->load([
            'invoice_details' => function ($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            },
            'invoice_details.product',
            'invoice_details.supplier',
            'invoice_details.invoice',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSupplierRequest  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'email' => 'required|email|unique:suppliers,email,' . $id,
            'phone' => 'required|unique:suppliers,phone,' . $id,

        ]);

        $supplier  = Supplier::find($id);

        $supplier->name  = $request->name;
        $supplier->email  = $request->email;
        $supplier->phone  = $request->phone;
        $supplier->save();

        $supplier->user()->update([

            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,

        ]);

        return $supplier;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
