<?php

namespace Modules\Mobile\Http\Controllers;

use App\Models\MaintenanceReport;
use App\Models\MaintenanceVisit;
use App\Service\EnhancedRouteOptimizationService;
use App\Service\GeneralLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Mobile\Http\Resources\VisitResource;
use Modules\Mobile\Http\Resources\VisitsResource;
use Modules\Mobile\Services\VisitService;
use Illuminate\Support\Facades\Log;

class VisitController extends Controller
{
    protected $visitService;
    private $routeOptimizer;

    public function __construct(VisitService $visitService, EnhancedRouteOptimizationService $routeOptimizer)
    {
        $this->visitService = $visitService;

        $this->routeOptimizer = $routeOptimizer;
    }

    public function index()
    {

        $locations =  $this->visitService->getAllVisits();

        $latitude = 22.3021;
        $longitude = 39.0945;
        // $locations =  $locations =  [
        //     [
        //         "id" => 1,
        //         "contract_number" => "M-2024-10-08-2760",
        //         "latitude" => 22.3021,
        //         "longitude" => 39.0945
        //     ],
        //     [
        //         "id" => 2,
        //         "contract_number" => "M-2024-10-08-9030",
        //         "latitude" => 24.7136,
        //         "longitude" => 46.6753
        //     ],
        //     [
        //         "id" => 3,
        //         "contract_number" => "M-2024-10-08-7017",
        //         "latitude" => 24.7136,
        //         "longitude" => 46.6753
        //     ],
        //     [
        //         "id" => 4,
        //         "contract_number" => "M-2024-10-08-4104",
        //         "latitude" => 25.0321,
        //         "longitude" => 46.4121
        //     ],
        //     [
        //         "id" => 5,
        //         "contract_number" => "M-2024-10-08-1446",
        //         "latitude" => 21.5432,
        //         "longitude" => 39.8231
        //     ],
        //     [
        //         "id" => 6,
        //         "contract_number" => "M-2024-10-08-2455",
        //         "latitude" => 23.8765,
        //         "longitude" => 46.2342
        //     ]
        // ];



        // $optimizedRoute = $this->routeOptimizer->optimizeRoute(
        //     $locations,
        //     [
        //         'latitude' => $latitude,
        //         'longitude' => $longitude
        //     ]
        // );

        // return ($optimizedRoute);
        try {
            $visits = $this->visitService->getVisitsSortedByDistance($latitude, $longitude);
            return VisitResource::collection($visits);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in VisitController@index: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching visits.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $visit = $this->visitService->getAllVisits();
            $visit = $this->visitService->getVisitById($id);

            return new VisitsResource($visit);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch visit', 'error' => $e->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $this->visitService->createVisit($request->all());
            return response()->json(['message' => 'Visit created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create visit', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            return $this->visitService->updateVisit($id, $request->all());
            return response()->json(['message' => 'Visit updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update visit', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $updatedVisit = $this->visitService->updateLocation($id, $request->only(['latitude', 'longitude']));
            return new VisitResource($updatedVisit);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update location', 'error' => $e->getMessage()], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'visit_id' => 'required',
            'type' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            if ($request->has('type') and $request->type == 'report') {
                $path = $image->storeAs('public/visit_reports', $filename);
                $report = MaintenanceReport::findOrFail($request->visit_id);
                $report->images = array_merge($report->images ?? [], [Storage::url($path)]);;
                $report->save();

                GeneralLogService::log($report, 'report_image_uploaded', 'تم تعديل الصور ', [
                    'report_id' => $report->id,
                    'images' => $report->images,
                    'report' => $report,
                ]);
            } else {

                $path = $image->storeAs('public/visit_images', $filename);
                $visit = MaintenanceVisit::findOrFail($request->visit_id);
                $visit->images = array_merge($visit->images ?? [], [Storage::url($path)]);
                $visit->save();

                GeneralLogService::log($visit, 'report_image_uploaded', 'تم تعديل الصور ', [
                    'visit_id' => $visit->id,
                    'images' => $visit->images,
                    'visit' => $visit,
                ]);
            }

            return response()->json([
                'message' => 'Image uploaded successfully',
                'image_url' => Storage::url($path),
            ], 200);
        }

        return response()->json(['error' => 'No image file uploaded'], 400);
    }


    public function removeImage(Request $request)
    {
        $request->validate([
            'image_url' => 'required|string',
            'visit_id' => 'required|integer',
        ]);

        $visit = MaintenanceVisit::findOrFail($request->visit_id);
        $visit->images = array_filter($visit->images, function ($image) use ($request) {
            return $image !== $request->image_url;
        });
        $visit->save();

        if ($request->image_url && Storage::exists($request->image_url)) {
            try {
                Storage::delete($request->image_url);
            } catch (\Exception $e) {
                Log::error('Failed to delete file: ' . $e->getMessage());
                // Handle the error as needed, e.g., return an error response
            }
        }

        return response()->json([
            'message' => 'Image removed successfully',
            'images' => $visit->images,
        ], 200);
    }
}
