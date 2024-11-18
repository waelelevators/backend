<?php


use App\Http\Controllers\PdfContractController;
use App\Http\Controllers\PdfExchangeProductController;
use App\Http\Controllers\PdfInstallationLDController;
use App\Http\Controllers\PdfQuotationController;
use App\Models\BuildingType;
use App\Models\City;
use App\Models\Client;
use App\Models\ElevatorType;
use App\Models\Employee;
use App\Models\MachineSpeed;
use App\Models\MaintenanceContractDetail;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceUpgrade;
use App\Models\MaintenanceVisit;
use App\Models\Neighborhood;
use App\Models\Product;
use App\Models\User;
use App\Service\EnhancedRouteOptimizationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Modules\Maintenance\Entities\MaintenanceContract;
use Modules\Maintenance\Entities\MaintenanceContractDetail as EntitiesMaintenanceContractDetail;
use Modules\Maintenance\Transformers\MaintenanceContractResource;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Modules\Mobile\Http\Controllers\RouteOptimizerController;

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



Route::get('map', [RouteOptimizerController::class, 'optimizeRoute']);
Route::get('maintenance-contract-logs', function () {

    // $contracts = MaintenanceContract::paginate(10)->map(function ($contract) {
    //     return [
    //         'id' => $contract->id,
    //         'contract_number' => $contract->contract_number,
    //         'latitude' => $contract->latitude,
    //         'longitude' => $contract->longitude
    //     ];
    // })
    //     ->toArray();

    $locations =  [
        [
            "id" => 1,
            "contract_number" => "M-2024-10-08-2760",
            "latitude" => 22.3021,
            "longitude" => 39.0945
        ],
        [
            "id" => 2,
            "contract_number" => "M-2024-10-08-9030",
            "latitude" => 24.7136,
            "longitude" => 46.6753
        ],
        [
            "id" => 3,
            "contract_number" => "M-2024-10-08-7017",
            "latitude" => 24.7136,
            "longitude" => 46.6753
        ],
        [
            "id" => 4,
            "contract_number" => "M-2024-10-08-4104",
            "latitude" => 25.0321,
            "longitude" => 46.4121
        ],
        [
            "id" => 5,
            "contract_number" => "M-2024-10-08-1446",
            "latitude" => 21.5432,
            "longitude" => 39.8231
        ],
        [
            "id" => 6,
            "contract_number" => "M-2024-10-08-2455",
            "latitude" => 23.8765,
            "longitude" => 46.2342
        ]
    ];

    $originsStr =   '21.3761739, 39.7639038';

    $destinationsStr = implode('|', array_map(function ($point) {
        return "{$point['latitude']},{$point['longitude']}";
    }, $locations));




    $matrix = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
        'origins' => $originsStr,
        'destinations' => $destinationsStr,
        'mode' => 'driving',
        'key' => "AIzaSyDhjUlU89FeHPvu-urVVTckiGKW0rmm1D8"
    ])->json();

    // if ($matrix->successful() && $matrix['status'] === 'OK') {
    //     return $matrix->json();
    // }


    $distances = [];
    $durations = [];
    if ($matrix) {

        foreach ($matrix['rows'] as $i => $row) {

            foreach ($row['elements'] as $j => $element) {

                if ($element['status'] === 'OK') {
                    $distances[$i][$j] = $element['distance']['value'];
                    $durations[$i][$j] = $element['duration']['value'];
                }
            }
        }
    }

    return  [
        'distances' => $distances,
        'durations' => $durations
    ];
    $routeOptimizer = new EnhancedRouteOptimizationService();

    $optimizedRoute = $this->routeOptimizer->optimizeRoute(
        $locations,
        [
            'latitude' => '21.3761739',
            'longitude' => '39.7639038'
        ]
    );

    return response()->json($optimizedRoute);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/directions/json?key=AIzaSyDhjUlU89FeHPvu-urVVTckiGKW0rmm1D8&origin=21.3761739%2C39.7639038&destinations=24.7136%2C46.6753%7C22.3021%2C39.0945%7C25.0321%2C46.4121%7C21.5432%2C39.8231%7C23.8765%2C46.2342&mode=driving&optimize=true');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;

    $api_key = 'AIzaSyDhjUlU89FeHPvu-urVVTckiGKW0rmm1D8'; // استبدل bằng مفتاح واجهة برمجة التطبيقات الخاص بك

    $origin = '21.3761739,39.7639038'; // الإحداثيات الجغرافية للنقطة الأصلية
    $destinations = [
        '24.7136,46.6753',
        '22.3021,39.0945',
        '25.0321,46.4121',
        '21.5432,39.8231',
        '23.8765,46.2342',
    ];

    $url = 'https://maps.googleapis.com/maps/api/directions/json?';
    $params = [
        'key' => $api_key,
        'origin' => $origin,
        'destinations' => implode('|', $destinations),
        'mode' => 'driving',
        'optimize' => 'true',
    ];

    $response = json_decode(file_get_contents($url . http_build_query($params)), true);

    $routes = $response['routes'];
    $sorted_destinations = [];

    foreach ($routes as $route) {
        $legs = $route['legs'];
        foreach ($legs as $leg) {
            $sorted_destinations[] = $leg['end_address'];
        }
    }

    print_r($sorted_destinations);

    return;
    return MaintenanceContract::paginate(10)->map(function ($contract) {
        return [
            'id' => $contract->id,
            'contract_number' => $contract->contract_number,
            'latitude' => $contract->latitude,
            'longitude' => $contract->longitude
        ];
    })
        ->toArray();


    return MaintenanceContract::where('template_id', '!=', 0)
        ->with('template')
        ->get();

    MaintenanceContractDetail::query()
        ->whereRaw('DATE(end_date) < CURDATE()')
        ->where('status', '!=', 'expired')
        ->update(['status' => 'expired']);

    $inactiveContracts = MaintenanceContractDetail::query()
        ->where('status', 'expired')
        ->whereNotExists(function ($query) {
            $query->from('maintenance_contract_details as mcd')
                ->whereColumn('mcd.maintenance_contract_id', 'maintenance_contract_details.maintenance_contract_id')
                ->where('mcd.status', 'active');
        })
        ->count();
    return $inactiveContracts;

    return Client::where('name', "LIKE", '%هاني%')
        // ->update(['name' => 'هاني عبد الوهاب سليمان عزالدين'])
        ->get();
    return MaintenanceContractDetail::with('logs')->find(1045);
    return \DB::statement("
    UPDATE maintenance_upgrades
    SET created_at = DATE_ADD(
        '2020-01-01',
        INTERVAL FLOOR(RAND() * TIMESTAMPDIFF(DAY, '2020-01-01', '2024-12-31')) DAY
    )
");
    return [];

    $productsIds = Product::all()->pluck('id')->toArray();
    $clientIds = Client::all()->pluck('id')->toArray();


    $cities =  [
        2,
        11,
        36,
        67
    ];

    $statuss = ['pending', 'accepted'];

    $elevator_type_ids = ElevatorType::all()->pluck('id')->toArray();
    $building_type_ids = BuildingType::all()->pluck('id')->toArray();
    $stops_counts = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    $speed_ids = MachineSpeed::all()->pluck('id')->toArray();

    for ($i = 0; $i < 1000; $i++) {
        // maintenance_upgrades `status`, `city_id`, `user_id`, `maintenance_contract_id`, `neighborhood_id`,
        // `latitude`, `longitude`, `client_id`, `elevator_type_id`, `building_type_id`, `stops_count`, `has_window`,
        // `has_stairs`, `site_images`, `total`
        $city_id = $cities[array_rand($cities)];
        $neighborhoods = Neighborhood::all()->pluck('id')->toArray();
        $upgrade_total = 0;
        $upgrade_tax = 0;
        $upgrade_discount = 0;

        $maintenanceUpgrade = MaintenanceUpgrade::create([
            'status' => $statuss[array_rand($statuss)],
            'city_id' => $city_id,
            'user_id' => 1,
            'maintenance_contract_id' => 1,
            'neighborhood_id' => $neighborhoods[array_rand($neighborhoods)],
            'latitude' => 1,
            'longitude' => 1,
            'client_id' => $clientIds[array_rand($clientIds)],
            'elevator_type_id' => $elevator_type_ids[array_rand($elevator_type_ids)],
            'building_type_id' => $building_type_ids[array_rand($building_type_ids)],
            'stops_count' => $stops_counts[array_rand($stops_counts)],
            'has_window' => rand(0, 1),
            'has_stairs' => rand(0, 1),
            'speed_id' => $speed_ids[array_rand($speed_ids)],
            'site_images' => '[]',
            'total' => $upgrade_total,
            'tax' => $upgrade_tax,
            'discount' => $upgrade_discount,
            'net_price' => $upgrade_total - $upgrade_tax - $upgrade_discount,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // `id`, `productable_type`, `productable_id`, `product_id`, `quantity`, `price`, `tax`, `subtotal`, `status`, `notes`, `created_at`, `updated_at`
        for ($i = 0; $i < rand(4, 10); $i++) {
            $quantity = rand(1, 10);
            $price = rand(100, 1000);
            $subtotal = $quantity * $price;
            $tax = $subtotal * 0.15;
            $maintenanceUpgrade->requiredProducts()->create([
                'product_id' => $productsIds[array_rand($productsIds)],
                'quantity' => $quantity,
                'price' => $price,
                'tax' => $tax,
                'subtotal' => $subtotal

            ]);

            $upgrade_total += $subtotal;
            $upgrade_tax += $tax;
        }

        $maintenanceUpgrade->total = $upgrade_total;
        $maintenanceUpgrade->tax = $upgrade_tax;
        $maintenanceUpgrade->discount = $upgrade_discount;
        $maintenanceUpgrade->net_price = $upgrade_total - $upgrade_tax - $upgrade_discount;
        $maintenanceUpgrade->save();
    }

    $data = MaintenanceUpgrade::with('requiredProducts.product', 'city', 'neighborhood')
        ->get()
        ->groupBy('city.name') // تجميع البيانات حسب اسم المدينة
        ->map(function ($items, $cityName) {
            return [
                'city_name' => $cityName,
                'neighborhoods' => $items->groupBy('neighborhood.name')->map(function ($neighborhoodItems, $neighborhoodName) {
                    return [
                        'name' => $neighborhoodName,
                        'products' => $neighborhoodItems->flatMap(function ($maintenanceUpgrade) {
                            return $maintenanceUpgrade->requiredProducts->map(function ($requiredProduct) {
                                return [
                                    'name' => $requiredProduct->product->name,
                                    'count' => 1, // يمكنك تخصيص العدد حسب الحاجة
                                ];
                            });
                        })->values(),
                    ];
                })->values(),
            ];
        })->values();

    return $data;



    // $result = MaintenanceUpgrade::with('requiredProducts.product')
    $resultss = MaintenanceReport::with('requiredProducts.product')
        ->get()
        ->groupBy('city_id')
        ->map(function ($group) {
            return $group->groupBy('neighborhood_id')
                ->map(function ($neighborhoodGroup) {
                    $products = $neighborhoodGroup->flatMap->requiredProducts->groupBy('product_id')
                        ->map(function ($productGroup) {
                            return [
                                'product_name' => $productGroup->first()->product->name,
                                'count' => $productGroup->sum('quantity'),
                            ];
                        });
                    return $products;
                });
        });

    return ($result);
    dd($result);

    // required_products `id`, `productable_type`, `productable_id`, `product_id`, `quantity`, `price`, `tax`, `subtotal`, `status`, `notes`, `created_at`, `updated_at`
    // `id`, `status`, `city_id`, `user_id`, `maintenance_contract_id`, `neighborhood_id`, `latitude`, `longitude`, `client_id`, `elevator_type_id`, `building_type_id`, `stops_count`, `has_window`, `has_stairs`, `site_images`, `total`, `tax`, `discount`, `net_price`, `attachment_contract`, `attachment_receipt`, `speed_id`, `rejection_reason`, `created_at`, `updated_at`
    return MaintenanceUpgrade::with('requiredProducts.product')
        ->get()
        ->groupBy('city_id')
        ->map(function ($group) {
            return $group->groupBy('neighborhood_id');
        });

    return (Client::find(5122));

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
