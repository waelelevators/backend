<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\InstallationLocationDetection;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Installation\Http\Resources\InstallationLocationDetectionResource;

class LocationDetectionTechController extends Controller
{

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $models = InstallationLocationDetection::where(['detection_by' => $id])
            ->orderByDesc('created_at')->get();

        return InstallationLocationDetectionResource::collection($models);
    }
}
