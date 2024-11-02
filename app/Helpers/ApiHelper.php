<?php

namespace App\Helpers;

use App\Models\Client;
use App\Models\LocationAssignment;
use App\Models\LocationAssignmentsLog;
use App\Models\ManufactureResponses;
use App\Models\Representative;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApiHelper
{
    public static function uploadBase64Image($base64Image, $path)
    {
        // Decode the base64-encoded image
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Generate a unique filename
        $filename = uniqid() . '.png'; // You can adjust the extension based on the image format

        // Save the image to the storage directory
        Storage::disk('public')->put($path . '/' . $filename, $imageData);

        $fullPath = 'storage/' . $path . '/' . $filename;

        return $fullPath;
        // Return the full URL to the uploaded image
        //return response()->json(['url' => $fullPath], 201);
    }

    public static function uploadBase64Pdf($base64Pdf, $path)
    {

        // Check if the provided base64 string contains a valid PDF header
        if (!preg_match('#^data:application/pdf;base64,#i', $base64Pdf)) {
            throw new \Exception('Invalid PDF data.');
        }


        $pdfData = base64_decode(preg_replace('#^data:application/pdf;base64,#i', '', $base64Pdf));

        // Check if the decoding was successful
        if ($pdfData === false) {
            throw new \Exception('Failed to decode base64 PDF data.');
        }

        // Generate a unique filename
        $filename = uniqid() . '.pdf'; // You can adjust the extension based on the image format

        // Use Storage facade to store the PDF in the public disk
        try {
            Storage::disk('public')->put($path . '/' . $filename, $pdfData);
        } catch (\Exception $e) {
            // Handle any errors that might occur during file storage
            throw new \Exception('Failed to upload PDF: ' . $e->getMessage());
        }

        // $fullPath = 'storage/' . $path . '/' . $filename;

        $fullPath = Storage::url($path . '/' . $filename);

        return $fullPath;
    }

    public static function changeResponseStatus($status, $m_id, $type)
    {
        if ($status == 2) {

            $model = new ManufactureResponses();
            $model->accept_time =  now()->format('Y-m-d H:i:s');
            $model->m_id = $m_id;
            $model->type = $type;
            $model->accepted_by = Auth::guard('sanctum')->user()->id;
            $model->user_id = Auth::guard('sanctum')->user()->id;
            $model->save();
        } else if ($status == 3) {

            ManufactureResponses::where([
                'm_id' => $m_id,
                'type' => $type

            ])
                ->update([
                    'ended_time' =>  now()->format('Y-m-d H:i:s'),
                    'ended_by' => Auth::guard('sanctum')->user()->id
                ]);
        }
    }

    public static function updateUsData($request, $id)
    {
        // Attempt to find an existing Representative with the given contract_id and contract_type
        $r = Representative::findOrFail($id);

        $r->how_did_you_get_to_us = $request['reachUs'];
        $r->name = null;
        $r->representativeable_type = 0;
        $r->representativeable_id = null;

        // Update fields based on the request
        switch ($request->reachUs) {
            case 1: // موقع الكتروني
                $r->representativeable_id = 0;
                $r->name = $request['webSiteName'];
                break;
            case 2: // وسائل التواصل
                $r->representativeable_id = 0;
                $r->name = $request['socialName'];
                break;
            case 3: // عميل لدى المؤسسة
                $r->representativeable_type = 'App\Models\Client';
                $r->representativeable_id = $request['clients'];
                break;
            case 4: // مندوب داخلي
                $r->representativeable_type = 'App\Models\Employee';
                $r->representativeable_id = $request['employees'];
                break;
            case 5: // اخرى
                $r->representativeable_id = 0;
                $r->name = $request['others'];
                break;
        }

        // Save the updated/created model to the database
        $r->save();
    }

    public static function LocationAssignment($contract, $contract_id)
    { // اسناد الموقع

        $data = [
            'stage_id' => $contract->stage_id,
            'contract_id' => $contract_id
        ];

        // Check if the data already exists in the database
        $existingRecord = LocationAssignment::where($data)->first();

        $job_title_id = $contract->representatives->names->job_title_id ?? 0;
        $checkStatus = ($contract->representatives->how_did_you_get_to_us === 4 && $job_title_id === 1);
        $userId = $checkStatus ? $contract->representatives->names->id : null;

        $isReady = $contract->getIsReadyToStart($contract->stage_id);
        $paid = $contract->getPaidAmountInStage($contract->stage_id); // المدفوع

        // Set financial_status and status based on contract readiness and userId
        $financialStatus = $isReady ? 3 : ($paid > 0 ? 2 : 1);

        $status = ($isReady && $userId) ? 2 : 1;

        if ($existingRecord) {
            $existingRecord->financial_status = $financialStatus;
            $existingRecord->status = $status; // الحالة مسند ام غير مسند
            $existingRecord->representative_by = $userId; // أسناد الي المندوب
            $existingRecord->save();
        } else if (!$existingRecord) {

            $assignmentModel = new LocationAssignment();
            $assignmentModel->contract_id = $contract_id;
            $assignmentModel->stage_id = $contract->stage_id;
            $assignmentModel->financial_status = $financialStatus;
            $assignmentModel->status = $status; // الحالة مسند ام غير مسند
            $assignmentModel->representative_by = $userId; // أسناد الي المندوب
            $assignmentModel->user_id = Auth::guard('sanctum')->user()->id;
            $assignmentModel->save();
            $existingRecord = $assignmentModel;
        }

        $LocationLogo = new LocationAssignmentsLog();
        $LocationLogo->location_assignment_id = $existingRecord->id;
        $LocationLogo->representative_by = $userId;
        $LocationLogo->status = $status;
        $LocationLogo->user_id = Auth::guard('sanctum')->user()->id;
        $LocationLogo->save();
    }


    // كيف وصلت لنا
    public static function handleGetUsData($request, $contract_type)
    {
        $r = new Representative();
        $r->contract_type = $contract_type;
        $r->how_did_you_get_to_us = $request['reachUs'];

        switch ($request['reachUs']) {

            case 1: // موقع الكتروني
                $r->representativeable_id = 0;
                $r->name = $request['webSiteName'];
                break;
            case 2: // وسائل التواصل
                $r->representativeable_id = 0;
                $r->name = $request['socialName'];
                break;
            case 3: // عميل لدى المؤسسة
                $r->representativeable_type = 'App\Models\Client';
                $r->representativeable_id = $request['clients'];
                break;
            case 4: // مندوب داخلي
                $r->representativeable_type = 'App\Models\Employee';
                $r->representativeable_id = $request['employees'];
                break;
            case 5: // اخرى
                $r->representativeable_id = 0;
                $r->name = $request['others'];
                break;
        }
        $r->save();
        return $r->id;
    }

    public static function updateClientData($request)
    {
        $clientId = $request['clientId']; // رقم العميل

        $clientType = $request['clientType']; // نوع العميل

        $client = Client::findOrFail($clientId);

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

                $client->tax_number = $request['taxNo'];  // الرقم الضريبي
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

        return $client;
    }

    public static function handleAddClient($request)
    {
        $clientType = $request['clientType'];

        // first search the Client Exists of Not
        $findClient = Client::where('phone', $request['phone'])
            ->where('type', $clientType)
            ->first();

        if ($findClient) return $findClient; // العميل موجود مسبقا

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
                    $client->id_number = $request['taxNo'];
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
        $client->phone2 = $request['anotherPhone'] ?? '';
        $client->whatsapp = $request['whatsappPhone'] ?? '';
        $client->save();
        return $client;
    }

    public static  function handleLocationClientData($request)
    {
        $clientType = $request['clientType'];

        $findClient = Client::where('phone', $request['phone'])
            ->where('type', $clientType)
            ->first();

        if ($findClient) return $findClient; // العميل موجود مسبقا

        $client =  new Client;
        $clientType = $request['clientType'];
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

                $client->id_number = $request['idNumber'] ?? '';

                $client->first_name = $request['firstName'];
                $client->second_name = $request['secondName'] ?? '';
                $client->third_name = $request['thirdName'] ?? '';
                $client->last_name = $request['forthName'];
                $client->id_number = $request['idNumber'];


                break;
            case 2:
                $client->name = $request['companyName'];
                $client->owner_name = $request['represents'];
                $client->id_number = $request['commercialRegistrationNo'];
                $client->tax_number = $request['taxNo'];  // الرقم الضريبي
                break;
            case 3:
                $client->name = $request['entityName']; // اسم الجهة
                $client->id_number = $request['idNumber'];
                $client->owner_name = $request['represents']; // يمثلها
                break;
        }

        $client->phone = $request['phone'];
        $client->phone2 = $request['phone2'];
        $client->whatsapp = $request['whatsapp'];
        $client->save();
        return $client;
    }
    public static  function handleClientOfferData($request)
    {
        $clientType = $request['clientType'];

        if ($request->has('phone') && $request['phone'] !== '') {
            $findClient = Client::where(
                [
                    ['phone', $request['phone']],
                    ['type', $clientType]
                ]
            )
                ->first();

            if ($findClient) return $findClient;
        }

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
                    $client->id_number = $request['taxNo'];
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
        return $client;
    }
    public static  function handleClientData($request)
    {


        $clientType = $request['clientType'];

        if ($clientType == 1 &&  $request->has('idNumber') && $request['idNumber'] !== '') {

            $findClient = Client::whereJsonContains(
                'data->id_number',
                $request['idNumber']
            )
                ->where('type', $clientType)
                ->first();

            if ($findClient) return $findClient;
        } elseif ($clientType == 2 &&  $request->has('idNumber') && $request['idNumber'] !== '') { // قطاع خاص

            $findClient = Client::whereJsonContains(
                'data->commercial_register',
                $request['commercialRegistrationNo']
            )
                ->where('type', $clientType)
                ->first();

            if ($findClient) return $findClient;
        } elseif ($clientType == 3 &&  $request->has('idNumber') && $request['idNumber'] !== '') { // مؤسسة حكومية

            $findClient = Client::whereJsonContains(
                'data->id_number',
                $request['idNumber']
            )
                ->where('type', $clientType)
                ->first();

            if ($findClient) return $findClient;
        }


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
                    $client->id_number = $request['taxNo'];
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
        return $client;
    }
}
