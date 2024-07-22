<?php

use App\Models\ContractTechnicianAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// middleware with prfix
Route::middleware('auth:sanctum')->prefix('technician')->group(function () {
    Route::get('/contracts', function () {

        return ContractTechnicianAssignment::all();
        return auth()->user();
    });
});
