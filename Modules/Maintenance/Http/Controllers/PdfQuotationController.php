<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\PdfHelper;
use App\Models\MaintenanceContract;
use App\Models\Template;
use Illuminate\Routing\Controller;


class PdfQuotationController extends Controller
{
    //
    function pdf($id)
    {
        $quotation = MaintenanceContract::with([
            'client',
            'ElevatorType',
            'MachineTYPE',
            'MachineSpeed',
            'doorSize',
            'StopCount',
            'ControlCard',
            'DriveType'
        ])->findOrFail($id);

        $Setting = Template::findOrFail(10);
        $template = $Setting->data['contract'];

        $name = optional($quotation->createdBy)->name;

        $doneBy = __('pdf.Export By') . ($name ?? '');

        $mpdf =   PdfHelper::generateContractPDF($name, $doneBy);

        $payment_table = "
      
            <table  class='pdf'>
                <tbody>
                    <tr style='background:#20536b:white; text-align:center;'>
                       <td style='text-align:center;color:white' width='33.33%'>المبلغ</td>
                       <td style='text-align:center;color:white' width='33.33%'>ضريبة القيمة المضافة</td>
                       <td style='text-align:center;color:white' width='33.33%'>الاجمالي</td>
    
                    </tr>
                    <tr>
                        <td style='text-align:center;color:red' width='33.33%'>{$quotation->activeContract->cost}</td>
                        <td style='text-align:center;color:red' width='33.33%'>شامل الضريبة</td>
                        <td style='text-align:center;color:red' width='33.33%'>{$quotation->activeContract->cost}</td>

                    </tr>

                </tbody>
            </table>";

        $data = [
            'FIRST_NAME' => $quotation->client->name,
            'PHONE' => $quotation->client->phone,
            'DATE' => $quotation->created_at->format('Y-m-d'),
            'CARD_NUMBER' => $quotation->contract_number,
            'ADDRESS' => $quotation->city->name,
            'CONTRACT_NUMBER' => $quotation->quotation_number,
            'MACHINE_SPEED' => $quotation->machineSpeed->name,
            'ELEVATOR_TYPE' => $quotation->elevatorType->name,
            'MACHINE_TYPE' => $quotation->MachineTYPE->name,
            'CONTROL_CARD' => $quotation->ControlCard->name,
            'DRIVE_TYPE' => $quotation->ControlCard->name,
            'DOOR_SIZE' => $quotation->doorSize->name,
            'PAYMENT' => $payment_table,


            //   'VISIT_NUMBERS' => $quotation->visits_number,
        ];
        // Generate the PDF
        $mpdf->WriteHTML(view(
            'maintenance::pdf.quotation',
            compact('data', 'template')
        )->render());

        return $mpdf->Output();
    }
}
