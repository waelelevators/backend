<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\Client;
use App\Models\MaintenanceUpgrade;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Maintenance\Http\Resources\MaintenanceUpgradeResource;
use Modules\Maintenance\Services\UpgradeElevatorService;

class UpgradeElevatorController extends Controller
{
    protected $upgradeService;

    public function __construct(UpgradeElevatorService $upgradeService)
    {
        $this->upgradeService = $upgradeService;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return [
            "data" => MaintenanceUpgrade::with('city', 'neighborhood', 'speed', 'elevatorType', 'buildingType', 'user', 'client')->get(),
        ];
    }

    public function store(Request $request)
    {

        // $client_id = ApiHelper::handleClientData($request)->id;
        $client_id = 1;


        // return $request->all();
        $upgrade = $this->upgradeService->createUpgrade($request->all());
        return $upgrade;
        try {
            return new MaintenanceUpgradeResource($upgrade);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء إنشاء الترقية.'], 500);
        }
    }
}