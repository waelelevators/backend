<?php


use App\Http\Controllers\PdfContractController;
use App\Http\Controllers\PdfExchangeProductController;
use App\Http\Controllers\PdfInstallationLDController;
use App\Http\Controllers\PdfQuotationController;
use Illuminate\Support\Facades\Route;


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
