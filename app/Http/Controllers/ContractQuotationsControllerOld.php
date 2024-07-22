<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContractQuotationsResource;


use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ContractQuotationsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ContractQuatationsCollection
     */
    public function index(Request $request)
    {
        $contractQuoations = ContractQuotations::orderByDesc('created_at')->get();

        //return  $contractQuoations;

        return  ContractQuotationsResource::collection($contractQuoations);
    }

    // private function handleClientData($request)
    private function handleClientData($request)
    {
        $clientType = $request->type;

        if ($clientType == 1) {
            $findClient = Client::whereJsonContains('data->id_number', $request['id_number'])
                ->where('type', $clientType)
                ->first();

            if ($findClient) {
                return $findClient;
            }
        } elseif ($clientType == 2) {
            $findClient = Client::whereJsonContains(
                'data->tax_number',
                $request['tax_number']
            )
                ->where('type', $clientType)
                ->first();
            if ($findClient) {
                return $findClient;
            }
        } else {
            $findClient = Client::whereJsonContains(
                'data->commercial_register',
                $request['commercial_register']
            )
                ->where('type', $clientType)
                ->first();
            if ($findClient) {
                return $findClient;
            }
        }


        if (isset($request['image'])) {

            $image = $this->uploadBase64Image($request['image'], 'client');
        } else {

            $image = '';
        }

        $client =  new Client;

        $client->type = $clientType;

        if ($clientType == 1) {
            $client->data = [
                'first_name' => $request['first_name'],
                'second_name' => $request['second_name'],
                'third_name' => $request['third_name'],
                'last_name' => $request['last_name'],
                'phone' => $request['phone'],
                'phone2' => $request['phone2'],
                'whatsapp' => $request['whatsapp'],
                'id_number' => $request['id_number'],
                'image' => $image,
            ];
        } elseif ($clientType == 2) {
            $client->data = [
                'name' => $request['name'],
                'owner_name' => $request['owner_name'],
                'commercial_register' => $request['commercial_register'],
                'tax_number' => $request['tax_number'],
                'phone' => $request['phone'],
                'phone2' => $request['phone2'],
                'whatsapp' => $request['whatsapp'],
                'id_number' => $request['id_number'],
                'image' => $image,
            ];
        } else {
            $client->data = [
                'name' => $request['name'],
                'id_number' => $request['id_number'],
                'phone' => $request['phone'],
                'phone2' => $request['phone2'],
                'whatsapp' => $request['whatsapp'],
                'image' => $image,
            ];
        }

        $client->save();
        return $client;
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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \DB::transaction(function () use ($request) {

            $client = $this->handleClientData($request);

            $model = new ContractQuotations;

            $model->client_id = $client->id;
            $model->region_id =  $request->region_id ?? '';
            $model->city_id =  $request->city_id ?? '';
            $model->neighboor_hood =  $request->neighboor_hood ?? '';
            $model->total_price =  $request->total_price ?? '';
            $model->tax =  $request->tax ?? '';
            $model->discount = $request->discount ?? '';
            $model->more_adds = $request->more_adds ?? '';
            $model->elevator_data = [
                'elevator_type_id'       => $request->elevator_type_id ?? '',
                'stops_number_id'       => $request->stops_number_id ?? '',
                'elevator_trip_id'       => $request->elevator_trip_id ?? '',
                'machine_load_id'       => $request->machine_load_id ?? '',
                'people_load_id'       => $request->people_load_id ?? '',
                'control_card_id'       => $request->control_card_id ?? '',
                'entrances_number_id'       => $request->entrances_number_id ?? '',
                'door_size_id'       => $request->door_size_id ?? '',
                'machine_type_id'       => $request->machine_type_id ?? '',
                'machine_speed_id'       => $request->machine_speed_id ?? '',
                'elevator_warranties_id'       => $request->elevator_warranties_id ?? '',
                'drive_type_id'       => $request->drive_type_id ?? '',
            ];
            $model->user_id  = Auth::guard('sanctum')->user()->id;
            $model->save();


            foreach ($request['installments'] as $oneIns) {

                $installmentModel = new ContractQuotationsInstallments();

                $installmentModel->contract_quotations_id = $model->id;
                $installmentModel->amount = $oneIns['amount'] ?? 0;
                $installmentModel->amount = $oneIns['payment_id'] ?? 0;
                $installmentModel->tax  = $oneIns['amount_tax'] ?? 0;
                $installmentModel->save();
            }

            return response()->json([
                'error' => false,
                'message' => 'تم اضافة العرض بنجاح'
            ]);
        });
    }
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $contractModel =  ContractQuotations::whereId($id)->update($request->all());


        //  $contractModel->region_id =  $request->region_id;
        // $contractModel->city_id =  $request->city_id ?? '';
        // $contractModel->neighboor_hood =  $request->neighboor_hood ?? '';
        // $contractModel->total_price =  $request->total_price ?? '';
        // $contractModel->tax =  $request->tax ?? '';
        // $contractModel->discount = $request->discount ?? '';
        // $contractModel->more_adds = $request->more_adds ?? '';
        // $contractModel->elevator_data = [
        //     'elevator_type_id'       => $request->elevator_type_id ?? '',
        //     'stops_number_id'       => $request->stops_number_id ?? '',
        //     'elevator_trip_id'       => $request->elevator_trip_id ?? '',
        //     'machine_load_id'       => $request->machine_load_id ?? '',
        //     'people_load_id'       => $request->people_load_id ?? '',
        //     'control_card_id'       => $request->control_card_id ?? '',
        //     'entrances_number_id'       => $request->entrances_number_id ?? '',
        //     'door_size_id'       => $request->door_size_id ?? '',
        //     'machine_type_id'       => $request->machine_type_id ?? '',
        //     'machine_speed_id'       => $request->machine_speed_id ?? '',
        //     'elevator_warranties_id'       => $request->elevator_warranties_id ?? '',
        //     'drive_type_id'       => $request->   ?? '',
        // ];

        //$contractModel->save();

        return $contractModel;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
