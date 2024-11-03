<?php


use App\Http\Controllers\PdfContractController;
use App\Http\Controllers\PdfExchangeProductController;
use App\Http\Controllers\PdfInstallationLDController;
use App\Http\Controllers\PdfQuotationController;
use App\Models\Client;
use App\Models\Employee;
use App\Models\MaintenanceContractDetail;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceUpgrade;
use App\Models\MaintenanceVisit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Modules\Maintenance\Entities\MaintenanceContract;
use Modules\Maintenance\Entities\MaintenanceContractDetail as EntitiesMaintenanceContractDetail;
use Modules\Maintenance\Transformers\MaintenanceContractResource;

// use PDF;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('maintenance-contract-logs', function () {

    $ex = new EntitiesMaintenanceContractDetail();
    return $ex->getExpiredContracts(); // EntitiesMaintenanceContractDetail::getExpiredContracts();

    $contract =  MaintenanceContract::find(144)->logs;
    return $contract;
    return MaintenanceContractResource::make($contract);
    return Employee::whereHas('visits', function ($query) {
        $query->where('status', 'completed');
    })
        ->with('user.area')
        ->with('visits', function ($query) {
            $query->with('maintenanceContract', 'maintenanceContract.client', 'maintenanceContract.city', 'maintenanceContract.neighborhood', 'maintenanceContract.area');
        })
        ->get();

    // اجلب ال technician_id بدون تكرار من جدول الزيارات
    $technicianIds = MaintenanceVisit::where("status", "completed")
        ->with(
            'technician',
            'maintenanceContractDetail',
            'maintenanceContractDetail.client',
            'maintenanceContract.city',
            'maintenanceContract.neighborhood',
            'maintenanceContract.area'
        )
        ->get();



    return $technicianIds;


    $user =  User::find(1);

    $user->otp = 123456;
    $user->save();

    return $user;

    $years = MaintenanceContractDetail::selectRaw('YEAR(start_date) as year')
        ->distinct()
        ->orderBy('year')
        ->pluck('year');

    $renewalRates = [];
    foreach ($years as $year) {
        $previousYearClients = MaintenanceContractDetail::selectRaw('COUNT(DISTINCT client_id) as total')
            ->whereYear('end_date', $year - 1)
            ->first()
            ->total;

        $renewedClients = MaintenanceContractDetail::selectRaw('COUNT(DISTINCT client_id) as total')
            ->whereYear('start_date', $year)
            ->whereIn('client_id', function ($query) use ($year) {
                $query->select('client_id')
                    ->from('maintenance_contract_details')
                    ->whereYear('end_date', $year - 1);
            })
            ->first()
            ->total;

        if ($previousYearClients > 0) {
            $renewalRates[$year] = [
                'year' => $year,
                'previous_year_clients' => $previousYearClients,
                'renewed_clients' => $renewedClients,
                'renewal_rate' => round(($renewedClients / $previousYearClients) * 100, 2)
            ];
        }
    }
    $data = [];
    foreach ($renewalRates as $key => $value) {
        array_push($data, $value);
    }
    return $data;

    $impact =  DB::table('maintenance_contract_details as current')
        ->join('maintenance_contract_details as next', function ($join) {
            $join->on('current.client_id', '=', 'next.client_id')
                ->whereRaw('next.start_date >= current.end_date');
        })
        ->select(
            DB::raw('
            CASE
                WHEN (next.cost - current.cost) / current.cost * 100 <= 0 THEN "decrease"
                WHEN (next.cost - current.cost) / current.cost * 100 <= 10 THEN "slight_increase"
                WHEN (next.cost - current.cost) / current.cost * 100 <= 25 THEN "moderate_increase"
                ELSE "significant_increase"
            END as price_change_category
        '),
            DB::raw('COUNT(*) as total_renewals'),
            DB::raw('AVG(DATEDIFF(next.start_date, current.end_date)) as avg_renewal_gap_days'),
            DB::raw('AVG((next.cost - current.cost) / current.cost * 100) as avg_price_change_percentage')
        )
        ->whereRaw('next.start_date = (
        SELECT MIN(start_date)
        FROM maintenance_contract_details
        WHERE client_id = current.client_id
        AND start_date >= current.end_date
        AND id != current.id
    )')
        ->groupBy('price_change_category')
        ->get();



    $categoryTranslations = [
        'decrease' => 'انخفاض',
        'moderate_increase' => 'زيادة متوسطة',
        'significant_increase' => 'زيادة كبيرة',
        'slight_increase' => 'زيادة طفيفة'
    ];

    return $impact->map(function ($item) use ($categoryTranslations) {
        return [
            'category' => $categoryTranslations[$item->price_change_category],
            'renewals' => $item->total_renewals,
            'avg_gap' => round((float)$item->avg_renewal_gap_days, 2),
            'avg_change' => round((float)$item->avg_price_change_percentage, 1)
        ];
    });
    return array_map(function ($item) use ($categoryTranslations) {
        return [
            'category' => $categoryTranslations[$item->price_change_category],
            'renewals' => $item->total_renewals,
            'avg_gap' => round((float)$item->avg_renewal_gap_days, 2),
            'avg_change' => round((float)$item->avg_price_change_percentage, 1)
        ];
    }, $impact->toArray());


    MaintenanceUpgrade::with('logs', 'requiredProducts')->find(8);
    $report = MaintenanceReport::with('logs', 'requiredProducts')->find(2);
    return  $report->requiredProducts->sum('subtotal');

    $years = MaintenanceContractDetail::selectRaw('YEAR(start_date) as year')
        ->distinct()
        ->orderBy('year')
        ->pluck('year');



    $renewalRates = [];

    foreach ($years as $year) {
        $previousYearClients = MaintenanceContractDetail::selectRaw('COUNT(DISTINCT client_id) as total')
            ->whereYear('end_date', $year - 1)
            ->first()
            ->total;

        if ($previousYearClients == 0) {
            continue;
        }
        return [
            'year' => $year,
            'previous_year_clients' => $previousYearClients
        ];

        $renewedClients = MaintenanceContractDetail::selectRaw('COUNT(DISTINCT client_id) as total')
            ->whereYear('start_date', $year)
            ->whereIn('client_id', function ($query) use ($year) {
                $query->select('client_id')
                    ->from('maintenance_contract_details')
                    ->whereYear('end_date', $year - 1);
            })
            ->first()
            ->total;

        if ($previousYearClients > 0) {
            $renewalRates[$year] = [
                'year' => $year,
                'previous_year_clients' => $previousYearClients,
                'renewed_clients' => $renewedClients,
                'renewal_rate' => round(($renewedClients / $previousYearClients) * 100, 2)
            ];
        }
    }

    return $renewalRates;

    $customerRenewals = MaintenanceContractDetail::select(
        'client_id',
        DB::raw('COUNT(*) as total_contracts'),
        DB::raw('MIN(start_date) as first_contract_date'),
        DB::raw('MAX(end_date) as latest_contract_end'),
        DB::raw('COUNT(DISTINCT YEAR(start_date)) as active_years'),
        DB::raw('SUM(cost) as total_revenue'),
        DB::raw('GROUP_CONCAT(DISTINCT YEAR(start_date) ORDER BY start_date) as contract_years')
    )
        ->whereNotNull('start_date')
        ->whereNotNull('end_date')
        ->groupBy('client_id')
        ->having('total_contracts', '>', 0)
        ->get();

    return $customerRenewals;




    return DB::table('maintenance_contract_details as current')
        ->join('maintenance_contract_details as next', function ($join) {
            $join->on('current.client_id', '=', 'next.client_id')
                ->whereRaw('next.start_date >= current.end_date');
        })
        ->select(
            DB::raw('
                    CASE
                        WHEN (next.cost - current.cost) / current.cost * 100 <= 0 THEN "decrease"
                        WHEN (next.cost - current.cost) / current.cost * 100 <= 10 THEN "slight_increase"
                        WHEN (next.cost - current.cost) / current.cost * 100 <= 25 THEN "moderate_increase"
                        ELSE "significant_increase"
                    END as price_change_category
                '),
            DB::raw('COUNT(*) as total_renewals'),
            DB::raw('AVG(DATEDIFF(next.start_date, current.end_date)) as avg_renewal_gap_days'),
            DB::raw('AVG((next.cost - current.cost) / current.cost * 100) as avg_price_change_percentage')
        )
        ->whereRaw('next.start_date = (
                SELECT MIN(start_date)
                FROM maintenance_contract_details
                WHERE client_id = current.client_id
                AND start_date >= current.end_date
                AND id != current.id
            )')
        ->groupBy('price_change_category')
        ->get();

    return Client::find(5702);
    return MaintenanceContract::with('contractDetail.visits', 'activeContract')->latest()->first();
    return MaintenanceVisit::latest()->first();
    return MaintenanceReport::find(1)->load('requiredProducts', 'requiredProducts.product', 'technician', 'user');
});


// Route::get('/ahmedhmed/{id}', function ($id) {
//     return   Client::whereJsonContains('data->id_number', $id)
//         ->where('type', 'individual')
//         ->first();
//     return Contract::find($id)->payments;
//     $rfq_ids =  Invoice::all()->pluck('rfq_id');

//     RFQ::whereIn('id', $rfq_ids)->get();
// });


// Route::get('/representative', function () {

//     return Contract::whereDoesntHave('workOrders', function ($query) {
//         return $query->whereNull('end_at');
//     })->get();

//     return Employee::whereDoesntHave('techniciansWorkOrder.workOrder', function ($query) {
//         $query->whereNotNull('end_at');
//     })->with('techniciansWorkOrder.workOrder')->get();


//     return Employee::whereDoesntHave('techniciansWorkOrder.workOrder', function ($query) {
//         $query->whereNotNull('end_at');
//     })->with('techniciansWorkOrder.workOrder')->get();
// });

// Route::get('/sra', function ($given, $rate, $targetAmount, $numberOfDays) {

//     $investment = 217.11;
//     $interestRate = 0.0163;

//     if ($given == 'dayes') {
//         for ($i = 0; $i < $numberOfDays; $i++) {
//             $investment += $investment * $interestRate;
//         }
//         return $investment;
//     } else {
//         $numberOfDays = 1;
//         while ($investment <= $targetAmount) {
//             $investment += $investment * $interestRate;
//             $numberOfDays++;
//         }
//         return $numberOfDays;
//     }
// });



// Route::get('name/{id}', function ($id) {

//     $Setting = Setting::where('name', 'template')->first();

//     $contract =  Contract::where('id', $id)->first();
//     return $contract->outerDoorDirections;
//     // return $contract->MachineSpeed;
//     // $contract =  ContractResource::make($contract);
//     $client =  $contract->client;

//     // return $client;

//     $client_name = "";
//     $id_number = '';

//     if ($client->type == "private") {

//         $client_name = $client->data['owner_name'];
//         $id_number  = $client->data['id_number'];
//     }

//     $template =  $Setting->data['contract'];

//     // return $contract;

//     $table =  '<table class="table table-bordered ">
//                     <thead class="thead-inverse">
//                         <tr>
//                             <th>نوع المصعد</th>
//                             <th>عدد الوقفات</th>
//                             <th>مشوار المصعد</th>
//                             <th>عدد الداخل</th>
//                             <th>غرفه المصعد</th>
//                         </tr>
//                         </thead>
//                         <tbody>
//                             <tr>
//                                 <td>اتومتك</td>
//                                 <td>ثلاث وقفات</td>
//                                 <td>3 أدوار</td>
//                                 <td>مدخل واحد </td>
//                                 <td>اعلي البئر</td>
//                             </tr>

//                         </tbody>
//                 </table>';


//     $data = [
//         'name' => $client_name,
//         'create' => now(),
//         'DATA' => $contract->created_at,
//         'CARD_NMUMBER' => $id_number,
//         'TABLE' => $table,
//         'ADDRESS' => $contract->city->name,
//         'CONTRACT_NUMBER' => $contract->contract_number,
//         'MACHINE_SPEED' => $contract->MachineSpeed->name,
//         'ELEVATOR_TYPE' => $contract->elevatorType->name ?? 'غير معروف ',
//         'MACHINE_LOAD' => $contract->MachineLoad->name ?? 'غير معروف ',
//         'MACHINE_WARRANTY' => $contract->machine_warranty_id,
//         'PEOPLE_LOAD' => $contract->PeopleLoad->name,
//         'CONTROL_CARD' => $contract->ControlCard->name,
//         'OTHER' => $contract->other_additions,
//         'elevator_warranty' => $contract->elevator_warranty_id,
//     ];


//     return view('welcome', compact('data', 'template'));
// });

// Route::get('pdf', function () {
//     // var_dump(VAPID::createVapidKeys()); // store the keys afterwards
//     $image_path = "https://achishayari.com/wp-content/uploads/2023/04/Cute-DP-Image.webp";
//     return view('pdf', compact('image_path'));
// });
Route::get('demo', function () {
    return view('installation/pdf/demo');
});

Route::get('location-pdf/{id}', [PdfInstallationLDController::class, 'pdf']);  // كشف الموقع
Route::get('contract-pdf/{id}', [PdfContractController::class, 'pdf']); // عقد تركيب
Route::get('quotation-pdf/{id}', [PdfQuotationController::class, 'pdf']); // عرض السعر
Route::get('exchange-products-pdf/{id}', [PdfExchangeProductController::class, 'pdf']);

// Route::get('generatePDF', function () {


//     $data = [
//         'content' => 'بسم الله الرحمن الرحيم.',
//         'header' => 'Header content',
//         'footer' => 'Footer content',
//         'image_path' => ('https://images.pexels.com/photos/1553406/pexels-photo-1553406.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500'),
//     ];


//     // return view('pdf', $data);
//     $pdf = PDF::loadView('pdf', $data);
//     // Specify the path to the CSS file with header and footer styles
//     $pdf->setOption('user-style-sheet', public_path('css/pdf-styles.css'));

//     return $pdf->download('pdf.pdf');
// });
