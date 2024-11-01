<?php

namespace App\Http\Controllers;

use App\ElevatorRoomType;
use App\Http\Resources\ElevatorRoomTypeCollection;
use App\Models\Client;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function searchCustomer(Request $request)
    {

        if ($request->type == 'individual') {
            $client = Client::where('type', 'individual')->where('data->id_number', $request->data)->first();
            return response([
                'client' => $client
            ], 200);
        } elseif ($request->type == 'private_sector') {
            # code...
        } elseif ($request->type == 'government_sector') {
            # code...
        } else {
            return null;
        }
    }
}
