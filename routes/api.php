<?php

use App\Helpers\MyHelper;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\NotificationstController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RFQController;
use App\Http\Controllers\RFQResponseController;
use App\Http\Controllers\RfqSupplierLineItemController;
use App\Http\Controllers\SignedContractsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\contractQuotationsController;
use App\Http\Controllers\SettingsController;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Industry;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Region;
use App\Models\Representative;
use App\Models\Stage;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('users', function () {
    return User::all();
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('dashboard', function () {
        return [];
    });

    Route::post('supplier_payments', function (Request $request) {
        // {"invoice_id":"1","data":{"supplier_id":"5","amount":"","attachment":""}}

        $request->validate([
            'data.supplier_id' => 'required',
            'data.amount' => 'required',
        ], [
            'data.supplier_id.required' => 'عليك اختيار المورد',
            'data.amount.required' => 'المبلغ اجباري',
        ]);

        $supplier_payment = new \App\Models\SupplierPayment;
        $supplier_payment->invoice_id = $request->invoice_id;
        $supplier_payment->supplier_id = $request->data['supplier_id'];
        $supplier_payment->amount = $request->data['amount'];
        $supplier_payment->user_id = Auth::user()->id;

        $supplier_payment->save();

        $email = Supplier::find($request->data['supplier_id'])->user->email;

        MyHelper::pushNotification([$email], [
            'title' => 'تم عملية الدفع',
            'body' => 'تم دفع مبلغ ' . $request->data['amount'] . ' للفاتوره ' . $request->id,
        ], 'suppiers');


        return $request->all();
    });

    Route::get('supplier_payments/{invoice_id}', function ($invoice_id) {

        return SupplierPayment::with(['user', 'supplier'])->where('invoice_id', $invoice_id)->get();
    });

    Route::apiResource('contract', ContractController::class);

    // العقودات
    // Route::get('contract', [ContractController::class, 'index']);
    // Route::get('contract/{id?}', [ContractController::class, 'show']);
    // Route::post('contract', [ContractController::class, 'store']);
    // Route::put('contract/{id?}', [ContractController::class, 'update']);

    Route::post('contract/{contract}/assign', [ContractController::class, 'assign']);
    // Route::apiResource('contract_data', [ContractController::class, 'contract_data']);

    // العملاء 
    Route::get('client', [ClientsController::class, 'index']);
    Route::get('client/{id?}', [ClientsController::class, 'show']);
    Route::post('client', [ClientsController::class, 'store']);
    Route::put('client/{id?}', [ClientsController::class, 'update']);

    Route::post('clients/{type?}/{id?}', [ClientsController::class, 'search']);
    Route::post('client/{type?}/{phone?}', [ClientsController::class, 'searchPhone']);

    Route::get('signed_contracts', [SignedContractsController::class, 'signed']); // العقود الموقعة
    Route::get('installed', [SignedContractsController::class, 'installed']); // العقود التي تم تركيبها

    // معمولة في المودل
    Route::get('contracts/products/{contract_id}/{stage_id}', [ContractController::class, 'products']);
    // معمولة في المودل
    Route::post('contracts/products/{contract_id}', [ContractController::class, 'createQuotation']);

    Route::get('products', function () {return Product::all();});

    Route::post('products/create', function (Request $request) {

        $request->validate([
            'name' => 'required|unique:products,name',
            'stage' => 'required',
            'elevator_types_id' => 'required',
        ]);


        $product = new Product();

        $product->name = $request->name;
        $product->stage = $request->stage;
        $product->elevator_types_id = $request->elevator_types_id;
        $product->save();

        return Product::all();
    });

    Route::get('purchases/notfications', function () {
        return Invoice::whereHas('invoice_details', function ($query) {
            return $query->where('stock_qty', '>', 0);
        })->get();
    });

    // اضافة موظف
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::get('employees/{employee}', [EmployeeController::class, 'show']);
    Route::post('employees', [EmployeeController::class, 'store']);
    Route::post('employees/update/{employee}', [EmployeeController::class, 'update']);

    Route::post('notifications/make_as_read/{notification}', [NotificationstController::class, 'make_as_read']);

    Route::get('notifications/{all?}', [NotificationstController::class, 'index']);


    Route::apiResource('elevator-room-type', App\Http\Controllers\ElevatorRoomTypeController::class);

    Route::post('/customer', [CustomerController::class, 'searchCustomer']);

    // اضافة كيف وصلت لنا في العقد
    Route::post('contract/representative', function (Request $request) {

        // reachUs
        // socialName
        // webSiteName
        // employees
        // clients
        // representatives
        // contract_id

        $contract_id = $request->contract_id;

        if ($request->data['reachUs'] == 'ClientOfTheOrganization') {
            foreach ($request->data['representative'] as $representative) {
                Representative::firstOrCreate([
                    'representativeable_id' => $representative['id'],
                    'representativeable_type' => 'App\Models\Client',
                    'contract_id' => $contract_id,
                ]);
            }
        } elseif ($request->data['reachUs'] == 'InternalRepresentative') {

            foreach ($request->data['representative'] as $representative) {

                Representative::firstOrCreate([
                    'representativeable_id' => $representative['id'],
                    'representativeable_type' => 'App\Models\Employee',
                    'contract_id' => $contract_id,
                ]);
            }
        } elseif ($request->data['reachUs'] == 'ExternalRepresentative') {

            foreach ($request->data['representative'] as $representative) {
                $r = new Representative();
                // $r->representativeable_type = 'null';
                $r->representativeable_id = 0;
                $r->contract_id = $contract_id;
                $r->name = $representative['name'];
                $r->phone = $representative['phone'];
                $r->save();
            }
        }
    });


    Route::post('contract/representative-delete', function (Request $request) {

        return Representative::where(['id' => $request->id, 'contract_id' => $request->contract_id])->delete();
    });


    // suppliers
    Route::get('suppliers', [SupplierController::class, 'index']);
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show']);
    Route::get('suppliers/{supplier}/payments', [SupplierController::class, 'payments']);
    Route::get('suppliers/{supplier}/invoices', [SupplierController::class, 'invoices']);

    Route::post('suppliers', [SupplierController::class, 'store']);
    Route::put('suppliers/{id}', [SupplierController::class, 'update']);
    Route::get('supplier_requests', [SupplierController::class, 'supplier_requests']);

    Route::get('invoices/{stage}', [InvoiceController::class, 'index']);

    Route::get('invoices', [InvoiceController::class, 'list']);
    Route::post('invoices', [InvoiceController::class, 'store']);
    Route::post('create_invoice', [InvoiceController::class, 'create_invoice']);


    Route::get('invoice_list', function (Request $request) {
        $user =  Auth::guard('sanctum')->user();


        if ($user->level == 'supplier') {

            return Invoice::whereHas('invoice_details', function ($query) use ($user) {
                return $query->where('supplier_id', $user->supplier->id);
            })->get();
        } else {
            return Invoice::orderByDesc('created_at')->get();
        }
    });

    Route::get('stages', function () {
        return Stage::all();
    });

    Route::post('stages', function (Request $request) {

        $request->validate([
            '*.id' => 'required|exists:stages,id',
            '*.required_percentage' => 'required|numeric|between:0,100',
        ], [
            '*.required_percentage.required' => 'النسبه اجبارى',
            '*.required_percentage.numeric' => 'النسبه يجب ان تكون عدد',
            '*.required_percentage.between' => 'النسبه يجب ان تكون بين 0 و 100',
        ]);


        foreach ($request->all() as $value) {

            $stage = Stage::find($value['id']);
            $stage->required_percentage = $value['required_percentage'];
            $stage->save();
        }
        return Stage::all();
    });


    Route::get('invoice_list/{invoice}', function (Invoice $invoice) {

        $user =  Auth::guard('sanctum')->user();

        if ($user->level == 'supplier') {

            $invoiceData =  $invoice->load(
                [
                    'invoice_details' => function ($query) use ($user) {
                        $query->where('supplier_id', $user->supplier->id);
                    },
                    'rfq',
                    'invoice_details.product',
                    'invoice_details.supplier'
                ]
            );

            $suppliers = $invoiceData->invoice_details->pluck('supplier')->unique();
            return response([
                'invoices' => $invoiceData,
                'suppliers' => $suppliers,
            ]);
        } else

            $invoiceData = $invoice->load(
                'invoice_details',
                'rfq',
                'invoice_details.product',
                'invoice_details.supplier'
            );

        //  return $invoiceData;


        $supplier_payments = SupplierPayment::where('invoice_id', $invoice->id)->get()->groupBy('supplier_id');
        $suppliers = $invoiceData->invoice_details->pluck('supplier')->unique()->values();
        $suppliersTotals = $invoiceData->invoice_details->groupBy('supplier_id');

        $suppliers->map(function ($supplier) use ($suppliersTotals, $supplier_payments) {
            foreach ($suppliersTotals[$supplier->id] as $data) {

                $supplier->total += $data->price * $data->qty;
            }
            if (isset($supplier_payments[$supplier->id])) {

                $supplier->payed = $supplier_payments[$supplier->id]->sum('amount') ?? 0;
            } else {

                $supplier->payed = 0;
            }
            return $supplier;
        });


        return response([
            'invoices' => $invoiceData,
            'suppliers' => $suppliers,
        ]);
    });


    Route::get('rfq_responses', function () {
        return \App\Models\RFQResponse::with('supplier', 'rfq', 'rfqLineItem')->get();
    });

    // عرض العملاء
    Route::get('cliens', function () {
        $clients =  Client::all();
        $entities = [
            // Your data goes here...
        ];

        $entityList = [];

        foreach ($clients as $entity) {


            if ($entity['type'] == '1') {
                $entityList[] = [
                    'id' => $entity->id,
                    'name' => $entity['data']['first_name'] . ' ' . $entity['data']['last_name']
                ];
            }

            if (
                $entity['type'] == '2' ||
                $entity['type'] == '3'
            ) {
                $entityList[] = [
                    'id' => $entity->id,
                    'name' => $entity['data']['name'] ?? '',
                ];
            }
        }

        return $entityList;
    });
    // معمولة في المودل
    Route::post('serach_products', function (Request $request) {

        return response(\App\Models\Product::where('name', 'like', '%' . $request->name . '%')->get());
    });

    Route::get('get_rfq_products/{rFQ}/{supplier_id?}', [RFQController::class, 'rfq_products']);

    Route::post('create_response', [RFQResponseController::class, 'store']);

    Route::post('rfqs_supplisers', [RfqSupplierLineItemController::class, 'store']);



    // Route::get('representativess', function () {

    //     $contracts =  Contract::get();

    //     return $contracts->locationDetection->id;

    //     // return $contracts->map(function ($contract) {

    //     //     return [
    //     //         'id' => $contract->id,
    //     //         'number' => $contract->contract_number,
    //     //         'how_did_you_get_to_us' => $contract->how_did_you_get_to_us,
    //     //         'representatives' => $contract->representatives,
    //     //         'created_at' => $contract->created_at->format('Y-m-d')
    //     //     ];
    //     // });
    // });

    Route::post('representatives/delete', function (Request $request) {

        Representative::destroy($request->id);
        return Representative::all();
    });

    // اضافة بضاعة

    // Route::post('add_product_quantities', function () {

    //     $product_quantity = new ContractProductQuantity;
    //     $product_quantity->product_id = request()->product_id;
    //     $product_quantity->elevator_type_id = request()->elevator_type_id;
    //     $product_quantity->floor_id = request()->floor;
    //     $product_quantity->stage_id = request()->stage;
    //     $product_quantity->qty = request()->qty;
    //     $product_quantity->price = request()->price;
    //     $product_quantity->save();

    //     return $product_quantity;
    // });


    Route::get('industries', function () {

        $industries = DB::table('industries')->get();

        return $industries->map(function ($industry) {

            return [
                'id' => $industry->name,
                'name' => $industry->name,
            ];
        });
    });

    Route::get('all_users', function () {

        return  DB::table('users')->get(['id', 'name']);
    });

    Route::get('visit_status', function () {

        return  DB::table('visit_statuses')->get(['id', 'name']);
    });

    Route::get('all_industries', function () {

        return  DB::table('industries')->get();
    });

    Route::post('industries', function (Request $request) {

        $request->validate([
            'name' => 'required_without_all:edit_name|unique:industries,name',
            'edit_name' => 'required_without_all:name|unique:industries,name',
        ], [
            'name.required_without_all' => 'هذا الحقل مطلوب',
            'edit_name.required_without_all' => 'هذا الحقل مطلوب',
            'name.unique' => 'هذا الاسم موجود بالفعل',
            'edit_name.unique' => 'لم تقم بتعديل الاسم او الاسم موجود مسبقا',
        ]);

        if ($request->has('id')) {

            Industry::where('id', $request->id)->update([
                'name' => $request->edit_name
            ]);
        } else {

            DB::table('industries')->insert([
                'name' => $request->name
            ]);
        }

        return DB::table('industries')->get();
    });

    Route::post('update_elevator_data', function (Request $request) {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'currentType' => 'required|string',
            'updatedItem.id' => 'required|integer',
            'updatedItem.name' => 'required|string',
            'updatedItem.need_to_internal_door' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            // Assign internalDoor value
            $internalDoor = $request->updatedItem['need_to_internal_door'] ?? null;

            // Update database record
            $updateData = [
                'name' => $request->updatedItem['name'],
            ];

            if ($request->currentType == 'elevator_types') {
                $updateData['need_to_internal_door'] = $internalDoor;
            }

            $affectedRows = DB::table($request->currentType)
                ->where('id', $request->updatedItem['id'])
                ->update($updateData);

            return response()->json([
                'success' => true,
                'affectedRows' => $affectedRows,
                'message' => 'Data updated successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating elevator data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update data',
                'error' => $e->getMessage()
            ], 500);
        }
    });


    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('technicians', function () {
        return Employee::where('department', 'technician')->get();
    });


    // Route::post('clients/{type}/{id}', function ($type, $id) {


    //     if ($type == "1")

    //         return Client::whereJsonContains('data->id_number', $id)
    //             ->where('type', '1')
    //             ->first();
    //     elseif ($type == "2")

    //         return Client::whereJsonContains('data->commercial_register', $id)
    //             ->where('type', '2')
    //             ->first();
    //     elseif ($type == "3")

    //         return Client::whereJsonContains('data->id_number', $id)
    //             ->where('type', '3')
    //             ->first();
    // });

    Route::get('contract_header_information', function () {
        return Region::whereHas('cities')->with('cities')->get();
    });


    Route::get('settings/tax', [SettingsController::class, 'index']);
    Route::post('settings/tax', [SettingsController::class, 'update']);

    // Route::get('templateSettings', [SettingsController::class, 'getTemplate']);
    // Route::post('templateSettings', [SettingsController::class, 'updateTemplate']);

    // elevator_types
    Route::get('elevator_types', function () {
        return [
            ['id' => 1, 'name' => 'عادي'],
            ['id' => 2, 'name' => 'اتوماتيك'],
            ['id' => 3, 'name' => 'طعام'],
            ['id' => 4, 'name' => 'بضاعة'],
            ['id' => 5, 'name' => 'مخصص'],
        ];
    });

    // elevator_types
    Route::get('get_elevator_data/{type}', function ($type) {
        return DB::table($type)->get();
    });


    // Route::get('{type}', function ($type) {
    //     return DB::table($type)->get();
    // });


    Route::get('regions', [RegionController::class, 'index']);
    Route::post('regions', [RegionController::class, 'store']);
    Route::post('regions/delete', [RegionController::class, 'destroy']);

    Route::get('cities', [CityController::class, 'index']);
    Route::post('cities', [CityController::class, 'store']);
    Route::post('cities/delete', [CityController::class, 'destroy']);

    Route::get('neighborhoods', [NeighborhoodController::class, 'index']);
    Route::post('neighborhoods', [NeighborhoodController::class, 'store']);
    Route::post('neighborhoods/delete', [NeighborhoodController::class, 'destroy']);


    Route::post('create_elevator_data/{type}', function ($tableName, Request $request) {

        return DB::table($tableName)->insert($request->all());
    });
});




Route::get('rule_categories', [PermissionsController::class, 'index']);
Route::post('rule_categories', [PermissionsController::class, 'ruleCategory']);

Route::post('rules', [PermissionsController::class, 'updateRule']);
Route::get('rules/{id?}', [PermissionsController::class, 'rules']);
Route::get('rules/{ruleId}/items', [PermissionsController::class, 'ruleItems']);

Route::post('login', [AuthController::class, 'login']);


Route::post('your-laravel-upload-endpoint', function (Request $request) {
    $base64File = $request->input('file');

    // Decode the base64 string to get the binary data
    $fileData = base64_decode($base64File);

    // Save the file to storage or perform any other necessary operations
    // Example: Save the file to storage
    $filePath = 'uploads/' . time() . '_' . 'ahmed' . '.png';
    Storage::disk('public')->put($filePath, $fileData);

    // You can return a response as needed
    return response()->json(['message' => 'File uploaded successfully', 'path' => $filePath]);
});


/************************************** quotations  ***************************************************************/

Route::apiResource('contract_quotations', contractQuotationsController::class);

// Route::get('contract_quotations', [contractQuotationsController::class, 'index']);
// Route::get('contract_quotations/{id?}', [contractQuotationsController::class, 'show']);
// Route::post('contract_quotations', [contractQuotationsController::class, 'store']);
// Route::put('contract_quotations/{id}', [contractQuotationsController::class, 'update']);
// Route::delete('contract_quotations/{id}', [contractQuotationsController::class, 'destroy']);

Route::get('contract_quotations_data', function () {
    $data = [];

    $tables = [
        "elevator_types", "stops_numbers", "elevator_trips", "machine_loads", "people_loads",
        'additions', "control_cards", "entrances_numbers", "door_sizes",
        "machine_types", "machine_speeds", "elevator_warranties", "drive_types"
    ];

    $regionsWithCity =  Region::whereHas('cities')->with('cities')->get();

    foreach ($tables as $table) {
        // get name and id for each table
        $data[$table] = DB::table($table)->get();
    }

    return response()->json(['elevator' => $data, 'regionsWithCities' => $regionsWithCity]);

    //return response($data);
});

Route::get('maintenance_data', function () {
    $data = [];

    $tables = [
        "elevator_types", "machine_types", "machine_speeds", "door_sizes",
        "stops_numbers", "control_cards",
        "drive_types", "maintenance_types", "building_types"
    ];

    $regionsWithCity =  Region::whereHas('cities')->with('cities')->get();

    foreach ($tables as $table) {
        // get name and id for each table
        $data[$table] = DB::table($table)->get();
    }

    return response()->json(['elevator' => $data, 'regionsWithCities' => $regionsWithCity]);
});

/************************************** quotations  ***************************************************************/
