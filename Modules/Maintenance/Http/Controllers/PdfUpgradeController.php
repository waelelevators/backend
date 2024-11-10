<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\PdfHelper;
use App\Models\MaintenanceUpgrade;
use App\Models\Template;
use Illuminate\Routing\Controller;

class PdfUpgradeController extends Controller
{

    function pdf($id)
    {
        $upgrades = MaintenanceUpgrade::with('city', 'neighborhood', 'speed', 'elevatorType', 'buildingType', 'user', 'client', 'logs', 'products')->findOrFail($id);



        $Setting = Template::findOrFail(1);
        $template = $Setting->data['contract'];

        $name =  'Hani';

        $doneBy = __('pdf.Export By') . ($name ?? '');


        $mpdf =   PdfHelper::generateContractPDF($name, $doneBy);


        $data = [
            'FIRST_NAME' => $upgrades->client->name ?? '',
            'PHONE' => $upgrades->client->phone,
            'DATE' => $upgrades->created_at->format('Y-m-d'),
            'CARD_NUMBER' => $upgrades->contract_number,
            'ADDRESS' => $upgrades->city->name,
            'CONTRACT_NUMBER' => $upgrades->contract_number,
            'MACHINE_SPEED' => $upgrades->speed->name,
            'ELEVATOR_TYPE' => $upgrades->elevatorType->name,

            'PAYMENT' => $payment_table ?? '',


            //   'VISIT_NUMBERS' => $upgrades->visits_number,
        ];


        $products = $upgrades->products;

        // Generate the PDF
        $mpdf->WriteHTML(view(
            'maintenance::pdf.upgrade',
            compact('data', 'template', 'products')
        )->render());

        return $mpdf->Output();
    }
}
