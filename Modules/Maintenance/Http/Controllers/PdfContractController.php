<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Helpers\PdfHelper;
use App\Models\MaintenanceContract;
use App\Models\Template;
use Illuminate\Routing\Controller;


class PdfContractController extends Controller
{
    //
    function pdf($id)
    {

        $contract = MaintenanceContract::with([
            'client',
            'ElevatorType',
            'MachineTYPE',
            'MachineSpeed',
            'doorSize',
            'stopsNumber',
            'ControlCard',
            'DriveType'
        ])->findOrFail($id);

        $Setting = Template::findOrFail($contract->template_id);
        $template = $Setting->data['contract'];

        $name = optional($contract->createdBy)->name;

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
                        <td style='text-align:center;color:red' width='33.33%'>{$contract->activeContract->cost}</td>
                        <td style='text-align:center;color:red' width='33.33%'>شامل الضريبة</td>
                        <td style='text-align:center;color:red' width='33.33%'>{$contract->activeContract->cost}</td>

                    </tr>

                </tbody>
            </table>";

        $data = [
            'FIRST_NAME' => $contract->client->name,
            'PHONE' => $contract->client->phone,
            'DATE' => $contract->created_at->format('Y-m-d'),
            'CARD_NUMBER' => $contract->contract_number,
            'ADDRESS' => $contract->city->name,
            'CONTRACT_NUMBER' => $contract->contract_number,
            'MACHINE_SPEED' => $contract->machineSpeed->name,
            'ELEVATOR_TYPE' => $contract->elevatorType->name,
            'MACHINE_TYPE' => $contract->MachineTYPE->name,
            'CONTROL_CARD' => $contract->ControlCard->name,
            'DRIVE_TYPE' => $contract->ControlCard->name,
            'DOOR_SIZE' => $contract->doorSize->name,
            'VISIT_NUMBERS' => $contract->visits_number,
            'PAYMENT' => $payment_table,
        ];
        // Generate the PDF
        $mpdf->WriteHTML(view(
            'maintenance::pdf.contract',
            compact('data', 'template')
        )->render());

        return $mpdf->Output();
    }
}
