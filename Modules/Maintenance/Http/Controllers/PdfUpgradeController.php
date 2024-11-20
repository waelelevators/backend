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

        $Setting = Template::findOrFail($upgrades->template_id);
        $template = $Setting->data['contract'];

        $name = $upgrades->user->employee->name;

        $doneBy = __('pdf.Export By') . ($name ?? '');


        $mpdf =   PdfHelper::generateContractPDF($name, $doneBy);

        $products = "
        <table style='margin-bottom:30px;width:670px' class='pdf table-bordered table-striped table'>
        <thead>
            <tr style='background-color:#20536b'>
                <td><span style='color:white'>#</span></td>
                <td><span style='color:white'>" . __('pdf.Statement') . "</span></td>
                <td><span style='color:white'>" . __('pdf.Quantity') . "</span></td>
                <td><span style='color:white'>" . __('pdf.Price') . "</span></td>
            </tr>
        </thead>
        <tbody>";

        foreach ($upgrades->products as $index => $product) {
            $products .= "
            <tr>
                <td>" . ($index + 1) . "</td>
                <td>" . $product->product->name . "</td>
                <td>" . $product->quantity . "</td>
                <td>" . $product->price . "</td>
            </tr>";
        }

        $products .= "
        </tbody>
        <tfoot>
            <tr style='background-color:#20536b'>
                <td><span style='color:white'>#</span></td>
                <td colspan='2'><span style='color:white'>" . __('pdf.Total') . "</span></td>
                <td><span style='color:white'> $upgrades->total  </span></td>
            </tr>
        </tfoot>
    </table>";

        $data = [
            'FIRST_NAME' => $upgrades->client->name ?? '',
            'PHONE' => $upgrades->client->phone,
            'DATE' => $upgrades->created_at->format('Y-m-d'),
            'CONTRACT_NUMBER' => $upgrades->contract_number ?? '',
            'CITY' => $upgrades->city->name,
            'NEIGHBORHOOD' => $upgrades->neighborhood->name,
            'STOPS_NUMBER' => $upgrades->stopsNumber->name,
            'ELEVATOR_TYPE' => $upgrades->elevatorType->name,
            'PRODUCTS' => $products ?? '',

        ];


        // Generate the PDF
        $mpdf->WriteHTML(view(
            'maintenance::pdf.upgrade',
            compact('data', 'template')
        )->render());

        return $mpdf->Output();
    }
}
