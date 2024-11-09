<?php

namespace Modules\Installation\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Models\InstallationClientLocation;
use Illuminate\Support\Facades\Auth;
use Modules\Installation\Http\Requests\InstallationClientLocationStoreRequest;

class InstallationClientLocationController extends Controller
{

    // Get all installation client locations
    public function index()
    {

        $locations = InstallationClientLocation::orderByDesc('created_at')->get();

        return response()->json($locations);
    }
    // Method to create a new installation client location
    public function store(InstallationClientLocationStoreRequest $request)
    {
        $location = new InstallationClientLocation();

        if (isset($request['buildingImage'])) $location_image = ApiHelper::uploadBase64Image(
            $request['buildingImage'],
            'daily/visit'
        ); // صوة البئر من الداخل

        else $location_image = '';

        // بحث عن العميل موجود ام لا
        $client =  ApiHelper::handleAddClient($request);

        if ($request['visitReason'] === '1') {

            $location->region_id = $request['region'];
            $location->city_id = $request['city'];
            $location->neighborhood_id = $request['neighborhood'];

            $location->elevator_trip_id = $request['floor_number'];
            $location->height = $request['height'];
            $location->width = $request['width'];
            $location->length = $request['length'];

            $location->location_image = $location_image;
            $location->lat = $request['lat'];
            $location->long = $request['long'];
        }

        if ($request['visitReason'] === '2') {

            $location->region_id = $request['region'];
            $location->city_id = $request['city'];
            $location->neighborhood_id = $request['neighborhood'];

            $location->location_image = $location_image;
            $location->lat = $request['lat'];
            $location->long = $request['long'];
        }

        $location->client_id = $client->id;
        $location->visit_reason_id = $request['visitReason'];
        $location->assigned_to = $request['detectionBy'];
        $location->description = $request['description'];
        $location->user_id = Auth::guard('sanctum')->user()->id;
        $location->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة  الزيارة  بنجاح',
        ]);
    }
}
