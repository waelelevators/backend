<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\MaintenanceUpgrade;
use App\Service\GeneralLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Maintenance\Http\Resources\MaintenanceUpgradeResource;
use Modules\Maintenance\Services\UpgradeElevatorService;
use Modules\Maintenance\Http\Requests\UpgradeElevatorStoreRequest;
use Modules\Maintenance\Enums\MaintenanceUpgradeStatus;

class UpgradeElevatorController extends Controller
{
    protected $upgradeService;

    public function __construct(UpgradeElevatorService $upgradeService)
    {
        $this->upgradeService = $upgradeService;
    }

    public function index()
    {
        $upgrades = MaintenanceUpgrade::with('city', 'neighborhood', 'speed', 'elevatorType', 'buildingType', 'user', 'client', 'logs')->get();
        return MaintenanceUpgradeResource::collection($upgrades);
    }

    public function show($id)
    {
        $upgrade = MaintenanceUpgrade::with('city', 'neighborhood', 'speed', 'elevatorType', 'buildingType', 'user', 'client', 'logs')->findOrFail($id);
        return new MaintenanceUpgradeResource($upgrade);
    }

    public function store(Request $request)
    {
<<<<<<< HEAD
        // dd($request->all());
=======
<<<<<<< HEAD
=======
        // dd($request->all());
>>>>>>> d2bf305c0ed65f619ac4b3659223ee169042aa1a
>>>>>>> 1ebb111 (Maintenance Part)
        $upgrade = $this->upgradeService->createUpgrade($request->all());

        return new MaintenanceUpgradeResource($upgrade);
        try {
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء إنشاء الترقية.'], 500);
        }
    }

    public function uploadAttachment(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:10240',
            'type' => 'required|in:contract,receipt',
            'upgrade_id' => 'required|exists:maintenance_upgrades,id',
        ]);


        try {
            $upgrade = MaintenanceUpgrade::findOrFail($request->upgrade_id);

            if ($request->hasFile('pdf_file')) {
                $path = $request->file('pdf_file')->store('upgrades/attachments', 'public');

                if ($request->type === 'contract') {
                    if ($upgrade->attachment_contract) {
                        Storage::disk('public')->delete($upgrade->attachment_contract);
                    }
                    GeneralLogService::log($upgrade, 'upgrade_contract', 'تم رفع العقد', $upgrade->status->value);
                    $upgrade->attachment_contract = $path;
                } else {
                    if ($upgrade->attachment_receipt) {
                        Storage::disk('public')->delete($upgrade->attachment_receipt);
                    }
                    GeneralLogService::log($upgrade, 'upgrade_paid', 'تم رفع ايصال الدفع', $upgrade->status->value);
                    $upgrade->attachment_receipt = $path;
                }

                $upgrade->save();

                return response()->json([
                    'message' => 'تم تحميل الملف بنجاح',
                    'path' => $path
                ], 200);
            }

            return response()->json(['error' => 'لم يتم العثور على الملف'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء تحميل الملف: ' . $e->getMessage()], 500);
        }
    }


    // reject upgrade
    public function rejectUpgrade(Request $request)
    {
        $request->validate([
            'upgrade_id' => 'required|exists:maintenance_upgrades,id',
            'reason' => 'string',
        ]);


        $upgrade = MaintenanceUpgrade::findOrFail($request->upgrade_id);
        $upgrade->status = MaintenanceUpgradeStatus::REJECTED;
        $upgrade->rejection_reason = $request->reason;
        $upgrade->save();
        GeneralLogService::log($upgrade, 'upgrade_rejected', 'تم رفض الترقية بسبب : ' . $request->reason, $upgrade->status->value);
        return response()->json(['message' => 'تم رفض الترقية بنجاح'], 200);
    }

    // accept upgrade
    public function acceptUpgrade(Request $request)
    {
        $request->validate([
            'upgrade_id' => 'required|exists:maintenance_upgrades,id',
        ]);

        $upgrade = MaintenanceUpgrade::findOrFail($request->upgrade_id);
        $upgrade->status = MaintenanceUpgradeStatus::ACCEPTED;
        $upgrade->save();
        GeneralLogService::log($upgrade, 'upgrade_accepted', 'تم قبول الترقية', $upgrade->status->value);
        return response()->json(['message' => 'تم قبول الترقية بنجاح'], 200);
    }

    public function addRequiredProducts(Request $request)
    {
        $request->validate([
            'upgrade_id' => 'required|exists:maintenance_upgrades,id',
            'products' => 'required|array',
            // 'products.*' => 'required|exists:products,id',
        ]);

        $upgrade = MaintenanceUpgrade::findOrFail($request->upgrade_id);

        $upgrade->products()->delete();

        $total = 0;
        $tax = 0;
        foreach ($request->products as $product) {

            $subtotal = $product['quantity'] * $product['price'];
            $tax = $subtotal * 0.15;
            $total += $subtotal;
            $tax += $tax;
            $upgrade->requiredProducts()->create([
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'tax' => $tax,
                'subtotal' => $subtotal
            ]);
        }

        $upgrade->save();

        // log the upgrade
        GeneralLogService::log($upgrade, 'upgrade_products_added', 'تم إضافة المنتجات المطلوبة', $upgrade->status->value);
        return response()->json(['message' => 'تم إضافة المنتجات المطلوبة بنجاح'], 200);
    }
}