<?php

namespace Modules\Maintenance\Http\Controllers;


use App\Models\MonthlyMaintenance;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Maintenance\Http\Requests\MonthlyMaintenanceTechnicantStoreResquest;

class MonthlyMaintenanceTechnicantController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        return 'di';
        $model = MonthlyMaintenance::orderBy("created_at")->get();

        return $model;

        // return MonthlyMaintenance::with('tech', 'visitStatus')
        //     ->orderByDesc('created_at')->get();
    }

    // public function uploadImage()
    // {
    //     $model = new MonthlyMaintenance();
    // }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    

    private function uploadBase64Image($base64Image, $path)
    {
        // Decode the base64-encoded image
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // get the extension from filename
        $extension = explode('/', mime_content_type($base64Image))[1];

        // Generate a unique filename
        $filename = uniqid() . '.' . $extension; // You can adjust the extension based on the image format

        // Save the image to the storage directory
        Storage::disk('public')->put($path . '/' . $filename, $imageData);

        $fullPath = asset('storage/app/public/' . $path . '/' . $filename);

        return $fullPath;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        //return $id;
        return MonthlyMaintenance::where('m_id', $id)->get();
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
