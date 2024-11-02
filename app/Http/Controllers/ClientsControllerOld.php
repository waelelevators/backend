<?php

namespace App\Http\Controllers;

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

        $response =  $this->checkClientId($clientType, $request['idNumber'], 0);

        if ($response) {

            if (isset($request['image'])) $image = $this->uploadBase64Image($request['image'], 'client');
            else $image = '';

            $client =  new Client();
            $client->type = $clientType;

            if ($clientType == 1) {
                $client->data = [
                    'first_name' => $request['firstName'],
                    'second_name' => $request['secondName'],
                    'third_name' => $request['thirdName'],
                    'last_name' => $request['forthName'],
                    'id_number' => $request['idNumber'],
                    'phone' => $request['phone'],
                    'phone2' => $request['anotherPhone'],
                    'whatsapp' => $request['whatsappPhone'],
                    'image' => $image
                ];
            } elseif ($clientType == 2) {
                $client->data = [
                    'name' => $request['companyName'],
                    'owner_name' => $request['represents'],
                    'id_number' => $request['idNumber'],
                    'commercial_register' => $request['commercialRegistrationNo'],
                    'tax_number' => $request['taxNo'],
                    'phone' => $request['phone'],
                    'phone2' => $request['anotherPhone'],
                    'whatsapp' => $request['whatsappPhone'],
                    'image' => $image,
                ];
            } else {
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
                'message' => 'تم اضافة العميل بنجاح',
            ]);
        } else return response()->json([
            'status' => 'failed',
            'message' => 'unprocessable entity',
            'error' => 'idNumber["cant not be Null"]'
        ], 422);


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

                'owner_name' => $request['represents'],
                'commercial_register' => $request['commercial_register'],
                'tax_number' => $request['taxNo'],
                'phone' => $request['phone'],
                'phone2' => $request['anotherPhone'],
                'whatsapp' => $request['whatsappPhone'],
                'id_number' => $request['idNumber']
            ];
        } elseif ($clientType == 3) {
            $client->data = [
                'name' => $request['entityName'],
                'id_number' => $request['idNumber'],
                'phone' => $request['phone'],
                'phone2' => $request['anotherPhone'],
                'whatsapp' => $request['whatsappPhone']
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
