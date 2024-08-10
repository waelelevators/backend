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

        $client =  new Client;
        $client->type = $clientType;

        switch ($clientType) {
            case 1:

                if (
                    !empty($request['firstName']) &&
                    !empty($request['secondName']) &&
                    !empty($request['thirdName']) &&
                    !empty($request['forthName'])
                ) {
                    $client->name = "{$request['firstName']}
                    {$request['secondName']}
                    {$request['thirdName']}
                    {$request['forthName']}";
                } elseif (!empty($request['firstName']) && !empty($request['forthName'])) {
                    $client->name = "{$request['firstName']} {$request['forthName']}";
                }

                if (!empty($request['idNumber'])) {
                    $client->id_number = $request['idNumber'];
                }

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
                break;
        }

        $client->phone = $request['phone'];
        $client->phone2 = $request['phone2'];
        $client->whatsapp = $request['whatsapp'];
        $client->save();


        return response()->json([
            'status' => 'success',
            'message' => 'تم اضافة العميل بنجاح',
        ]);



        // return response()->json([
        //     'status' => 'failed',
        //     'error' => 'رقم الهوية مستخدم من قبل عميل اخر'
        // ]);
    }

    // public function update(Request $request, $id)
    function update(ClientUpdateResquest $request, $id)
    {

        $clientType = $request['clientType'];

        // $response = $this->checkClientId($clientType, $request['idNumber'], $id); //  بحث عن عميل برقم الهوية
        // if ($response) {

        $client = Client::findOrFail($id);

        if (isset($request['image'])) $image = $this->uploadBase64Image($request['image'], 'client');
        else $image = '';

        $client->type = $clientType;

        if ($clientType == 1) {
            $client->data = [
                'first_name' => $request['firstName'],
                'second_name' => $request['secondName'],
                'third_name' => $request['thirdName'],
                'last_name' => $request['forthName'],
                'phone' => $request['phone'],
                'phone2' => $request['anotherPhone'],
                'whatsapp' => $request['whatsappPhone'],
                'id_number' => $request['idNumber'],
                'image' => $image,
            ];
        } elseif ($clientType == 2) {

            $client->data = [
                'name' => $request['companyName'],
                'owner_name' => $request['represents'],
                'commercial_register' => $request['commercial_register'],
                'tax_number' => $request['taxNo'],
                'phone' => $request['phone'],
                'phone2' => $request['anotherPhone'],
                'whatsapp' => $request['whatsappPhone'],
                'id_number' => $request['idNumber'],
                'image' => $image,
            ];
        } elseif ($clientType == 3) {
            $client->data = [
                'name' => $request['entityName'],
                'id_number' => $request['idNumber'],
                'phone' => $request['phone'],
                'phone2' => $request['anotherPhone'],
                'whatsapp' => $request['whatsappPhone'],
                'image' => $image,
            ];
        }

        $client->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تعديل العميل بنجاح',
        ]);

        // } else {

        //     return response()->json([
        //         'status' => 'failed',
        //         'errors' => [
        //             'idNumber' =>  'رقم الهوية مستخدم من قبل عميل اخر'
        //         ],
        //         //'error' => 'رقم الهوية مستخدم من قبل عميل اخر'
        //     ]);
        // }
    }

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
    private function uploadBase64Image($base64Image, $path = 'logos')
    {
        // Decode the base64-encoded image
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Generate a unique filename
        $filename = uniqid() . '.png'; // You can adjust the extension based on the image format

        // Save the image to the storage directory
        Storage::disk('public')->put($path . '/' . $filename, $imageData);

        $fullPath = asset('storage/' . $path . '/' . $filename);

        return $fullPath;
    }
}
