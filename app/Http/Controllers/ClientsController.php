<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateResquest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientsController extends Controller
{

    function show($id)
    {
        return Client::findOrFail($id);
    }

    public function store(ClientStoreRequest $request)
    {
        $clientType = $request['clientType'];

        $client = new Client();
        $client->type = $clientType;


        switch ($clientType) {
            case 1:
                $client->name = $this->formatClientName($request);
                $client->id_number = $request['idNumber'] ?? null;
                $client->first_name = $request['firstName'];
                $client->second_name = $request['secondName'] ?? '';
                $client->third_name = $request['thirdName'] ?? '';
                $client->last_name = $request['forthName'];

                break;
            case 2:
                $client->name = $request['companyName'];
                $client->owner_name = $request['represents'];

                if (!empty($request['commercialRegistrationNo'])) {
                    $client->id_number = $request['commercialRegistrationNo'];
                }
                if (!empty($request['taxNo'])) {
                    $client->tax_number = $request['taxNo'];
                }

                break;
            case 3:
                $client->name = $request['entityName']; // اسم الجهة

                if (!empty($request['idNumber'])) {
                    $client->id_number = $request['idNumber'];
                }
                $client->owner_name = $request['represents']; // يمثلها

        }




        // return response()->json([
        //     'status' => 'failed',
        //     'error' => 'رقم الهوية مستخدم من قبل عميل اخر'
        // ]);
    }

    // public function update(Request $request, $id)
    // function update(ClientUpdateResquest $request, $id)

    public function update(ClientUpdateResquest $request, $id)
    {
        // Find the client by ID
        $client = Client::findOrFail($id);

        $clientType = $request['clientType'];
        // $client->type = $clientType;
        // Handle different client types
        switch ($clientType) {
            case 1:
                $client->name = $this->formatClientName($request);
                $client->id_number = $request['idNumber'] ?? null;
                $client->first_name = $request['firstName'];
                $client->second_name = $request['secondName'] ?? '';
                $client->third_name = $request['thirdName'] ?? '';
                $client->last_name = $request['forthName'];
                break;

            case 2:
                $client->name = $request['companyName'];
                $client->owner_name = $request['represents'];
                $client->id_number = $request['commercialRegistrationNo'] ?? null;
                $client->tax_number = $request['taxNo'] ?? null;
                break;

            case 3:
                $client->name = $request['entityName'];
                $client->id_number = $request['idNumber'] ?? null;
                $client->owner_name = $request['represents'];
                break;
        }

        // Update the remaining fields
        $client->phone = $request['phone'];
        $client->phone2 = $request['anotherPhone'];
        $client->whatsapp = $request['whatsappPhone'];

        // Save the changes to the client
        $client->save();

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث العميل بنجاح',
        ]);
    }



    // } else {

    //     return response()->json([
    //         'status' => 'failed',
    //         'errors' => [
    //             'idNumber' =>  'رقم الهوية مستخدم من قبل عميل اخر'
    //         ],
    //         //'error' => 'رقم الهوية مستخدم من قبل عميل اخر'
    //     ]);
    // }
    //   }

    public function SearchPhone($type, $phone)
    {
        return Client::where('phone', $phone)
            ->where('type', $type)
            ->first();
    }

    public function search($type, $id)
    {
        if ($type == "1")

            return Client::whereJsonContains('data->id_number', $id)
                ->where('type', '1')
                ->first();
        elseif ($type == "2")

            return Client::whereJsonContains('data->commercial_register', $id)
                ->where('type', '2')
                ->first();

        elseif ($type == "3")

            return Client::whereJsonContains('data->id_number', $id)
                ->where('type', '3')
                ->first();
    }

    private function checkClientId($clientType, $idNumber, $clientId)
    {

        if ($clientType == 1) { // فرد
            $findClient = Client::whereJsonContains('data->id_number', $idNumber)
                ->where('type', $clientType)
                ->where('id', '!=', $clientId)
                ->first();

            if ($findClient) return false;
        } elseif ($clientType == 2) { // قطاع خاص
            $findClient = Client::whereJsonContains('data->commercial_register', $idNumber)
                ->where('type', $clientType)
                ->where('id', '!=', $clientId)
                ->first();

            if ($findClient) return false;
        } elseif ($clientType == 3) { // مؤسسة حكومية
            $findClient = Client::whereJsonContains('data->id_number', $idNumber)
                ->where('type', $clientType)
                ->where('id', '!=', $clientId)
                ->first();

            if ($findClient) return false;
        }
        return true;
    }



    function index()
    {
        return Client::orderByDesc('created_at')->get();
    }
    private function formatClientName($request)
    {
        if (
            !empty($request['firstName']) &&
            !empty($request['secondName']) &&
            !empty($request['thirdName']) &&
            !empty($request['forthName'])
        ) {
            return "{$request['firstName']} {$request['secondName']} {$request['thirdName']} {$request['forthName']}";
        } elseif (!empty($request['firstName']) && !empty($request['forthName'])) {
            return "{$request['firstName']} {$request['forthName']}";
        }

        return null;
    }
}
