<?php

namespace Modules\Maintenance\Services;

use App\Helpers\ApiHelper;
use App\Models\Client;
use App\Models\MaintenanceUpgrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Maintenance\Enums\MaintenanceUpgradeStatus;

class UpgradeElevatorService
{

    public function createUpgrade($data)
    {
        $user = auth('sanctum')->user();
        return DB::transaction(function () use ($data, $user) {
            $client = ApiHelper::handleAddClient($data);
            $client_id = $client->id;

            $taxValue = 0.15;
            $total = $data['total'];
            $discount = $data['discount'] ?? 0;
            $netPrice = $total - $discount;
            $tax = $netPrice * $taxValue;
            $upgrade = new MaintenanceUpgrade();
            $upgrade->fill([
                'maintenance_contract_id' => 0,
                // 'latitude' => $data['latitude'],
                // 'longitude' => $data['longitude'],
                'client_id' => $client_id,
                'template_id' => $data['template_id'],
                'elevator_type_id' => $data['elevator_type_id'],
                'stops_count' => $data['stops_count'],
                // 'has_window' => $data['has_window'],
                // 'has_stairs' => $data['has_stairs'],
                'total' => $data['total'],
                'discount' => $data['discount'] ?? null,
                'net_price' => $data['net_price'],
                'user_id' => $user->id,
                'city_id' => $data['city_id'],
                'neighborhood_id' => $data['neighborhood_id'],
                'speed_id' => $data['machine_speed_id'],
                'building_type_id' => $data['building_type_id'],
                'status' => MaintenanceUpgradeStatus::PENDING,
                'tax' => $tax,


            ]);

            if (isset($data['site_images'])) {
                // $upgrade->site_images = $this->handleImageUploads($data['site_images']);
            }

            $upgrade->save();

            return $upgrade;
        });
    }

    private function getOrCreateClient(array $data)
    {
        if (isset($data['client_id'])) {
            return Client::findOrFail($data['client_id']);
        }

        return Client::firstOrCreate(
            ['phone' => $data['phone']],
            [
                'id_number' => $data['id_number'],
                'name' => $data['name'],
            ]
        );
    }

    private function handleImageUploads(array $images)
    {
        $uploadedImages = [];
        foreach ($images as $image) {
            $path = Storage::disk('public')->put('site_images', $image);
            $uploadedImages[] = $path;
        }
        return $uploadedImages;
    }
}