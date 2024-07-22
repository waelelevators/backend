<?php

namespace App\Http\Controllers;

use Alkoumi\LaravelArabicNumbers\Numbers;
use App\Helpers\PdfHelper;
use App\Models\InstallationQuotation;
use App\Models\Template;

class PdfQuotationController extends Controller
{
    //
    public function pdf($id)
    {

        $quotation =  InstallationQuotation::findOrFail($id);

        $Setting = Template::findOrFail($quotation->template_id);

        $installments =  $quotation->installments;

        $name = $quotation->user->name ?? '';

        $doneBy = __('pdf.Export By') . ' ' . ($name ?? '');

        $mpdf = PdfHelper::generateContractPDF($name, $doneBy);

        $data_payment_table = '';

        $tax = 0;
        $payment_nots = [
            'الدفعه الاولى',
            'الدفعه الثانيه',
            'الدفعه الثالثه',
            'الدفعه الرابعه',
            'الدفعه الخامسه',
            'الدفعه السادسه',
            'الدفعه السابعه',
            'الدفعه الثامنه',
            'الدفعه التاسعه',
            'الدفعه العاشره',
            'الدفعه الحاديه عشر',
            'الدفعه الثانيه عشر'
        ];

        $index = 0;
        foreach ($installments as $installment) {

            $totalWithTaxPerRow = $installment['amountWithTaxed'];

            $taxPerRow = $installment['amountWithTaxed'] -  $installment['amount'];

            $arabicNumber = Numbers::TafqeetNumber($totalWithTaxPerRow);

            $totalAmountArabic = Numbers::TafqeetNumber($quotation->total_price);


            $data_payment_table .= '<tr>
                                            <td>' . $payment_nots[$index++] . '</td>
                                            <td>' . $installment['amount'] . '</td>
                                            <td>' . $taxPerRow . '</td>
                                            <td>' . $installment['amountWithTaxed'] . '</td>
                                            <td>' . $arabicNumber . ' ريال</td>
                                    </tr>';
        }

        $payment_table = '
        <div>
        <table style="margin-bottom:30px;width:670px" class="pdf table table-bordered table-striped">
        <tbody>
        <tr style="background-color:#20536b">
                                        <td><span style="color:white">البيان </span> </td>
                                        <td><span style="color:white"> اجمالى السعر </span></td>
                                        <td><span style="color:white">القيمه المضافه </span></td>
                                        <td><span style="color:white">المبلغ بالضريبة </span> </td>
                                        <td><span style="color:white"> المبلغ كتابتا  </span> </td>
                        </tr>

                        <tr>
                                        <td>سعر المصعد</td>
                                        <td>' . $quotation->total_price - $quotation->tax . '</td>
                                        <td>' . $quotation->tax . '</td>
                                        <td>' .  $quotation->total_price . '</td>
                                        <td>' . $totalAmountArabic . ' ريال</td>
                        </tr>
                        <tr style="background-color: #20536b; color: white">
                        <td colspan="4" style="text-align: center; color: white">شروط الدفع على النحو التالى</td>
                        </tr>';


        $payment_table .= $data_payment_table;
        $payment_table .= '</tbody></table></div>';

        $client =  $quotation->client;

        $client_name = "";
        $id_number = '';
        $phone = '';

        ($client->type == 2 || $client->type == 3) ?
            $client_name = $client->data['name'] :
            $client_name = $client->data['first_name'] . ' ' .
            $client->data['second_name'] . ' ' .
            $client->data['third_name'] . ' ' .
            $client->data['last_name'];

        $id_number  = $client->type == 2 ? $client->data['commercial_register'] :
            $client->data['id_number'];

        $phone  = $client->data['phone'];
        $template =  $Setting->data['contract'];
        $elevatorType = $quotation->elevator_type->name ?? 'غير معروف';
        $machineType = $quotation->machine_type->name ?? 'غير معروف';
        $machineSpeed = $quotation->machine_speed->name ?? 'غير معروف';
        $machineLoad = $quotation->machine_load->name ?? 'غير معروف';
        $machineWarranty = $quotation->machine_warranty->name ?? 'غير معروف';
        $peopleLoad = $quotation->people_load->name ?? 'غير معروف';
        $doorSize = $quotation->door_size->name ?? 'غير معروف';
        $stopsNumbers = $quotation->stops_number->name ?? 'غير معروف';
        $controlCard = $quotation->control_card->name ?? 'غير معروف';
        $driveType = $quotation->drive_type->name ?? 'غير معروف';
        $entrancesNumber = $quotation->entrances_number->name ?? 'غير معروف';
        $elevatorTrip = $quotation->elevator_trip->name ?? 'غير معروف';
        $elevatorRoom = $quotation->elevator_room->name ?? 'غير معروف';

        $table = '
        <h2>المواصفات الفنية</h2>
        <div style="margin-right:90px">
         <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
            <div style="text-align:center;display: block;">
                    <div class="desc-box">
                        <div class="desc-header">
                            <p> ' . __("pdf.Elevator Type") . ' </p>
                        </div>
                        <div class="desc-body">
                            <span style="text-align:center;color:red">
                                ' . $elevatorType . '
                            </span>
                        </div>
                    </div>

                    <div class="desc-box">
                        <div class="desc-header">
                        <p> ' . __("pdf.Stopping Number") . ' </p>
                        </div>
                        <div class="desc-body">
                            <span style="text-align:center;color:red">
                                ' . $stopsNumbers . '
                            </span>
                        </div>
                    </div>

                    <div class="desc-box">
                        <div class="desc-header">
                        <p> ' . __("pdf.Elevator Trip") . ' </p>
                        </div>
                        <div class="desc-body">
                            <span style="text-align:center;color:red">
                                ' . $elevatorTrip . '
                            </span>
                        </div>
                    </div>

                    <div class="desc-box">
                        <div class="desc-header">
                        <p> ' . __("pdf.Entry Number") . ' </p>
                        </div>
                        <div class="desc-body">
                            <span style="text-align:center;color:red">
                                ' . $entrancesNumber . '
                            </span>
                        </div>
                    </div>
             </div>

            <div style="text-align:center;display: block;">
                <div style="margin-right:50;text-align:center" class="desc-box">
                    <div class="desc-header">
                        <p> ' . __("pdf.Control Card") . ' </p>
                        </div>
                        <div class="desc-body">
                            <span style="text-align:center;color:red">
                                ' . $controlCard . '
                            </span>
                        </div>
                    </div>


                    <div class="desc-box">
                        <div class="desc-header">
                        <p> ' . __("pdf.Drive Type") . ' </p>
                        </div>
                        <div class="desc-body">
                            <span style="text-align:center;color:red">
                                ' . $driveType . '
                            </span>
                        </div>
                    </div>

                    <div class="desc-box">
                        <div class="desc-header">
                        <p> ' . __("pdf.Door Size") . ' </p>
                        </div>
                        <div class="desc-body">
                            <span style="text-align:center;color:red">
                                ' . $doorSize . '
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align:center;display: block;">
                <div style="text-align:center" class="desc-box">
                        <div class="desc-header">
                            <p> ' . __("pdf.Machine Type") . ' </p>
                        </div>
                        <div class="desc-body">
                            <span style="text-align:center;color:red">
                                ' . $machineType . '
                            </span>
                        </div>
                 </div>

                <div class="desc-box">
                    <div class="desc-header">
                    <p> ' . __("pdf.Machine Speed") . ' </p>
                    </div>
                    <div class="desc-body">
                        <span style="text-align:center;color:red">
                            ' . $machineSpeed . '
                        </span>
                    </div>
                </div>

                <div class="desc-box">
                    <div class="desc-header">
                    <p> ' . __("pdf.Machine Load") . ' </p>
                    </div>
                    <div class="desc-body">
                        <span style="text-align:center;color:red">
                            ' . $machineLoad . '
                        </span>
                    </div>
                </div>

                <div class="desc-box">
                    <div class="desc-header">
                    <p> ' . __("pdf.Machine Warranty") . ' </p>
                    </div>
                    <div class="desc-body">
                        <span style="text-align:center;color:red">
                            ' . $machineWarranty . '
                        </span>
                    </div>
                </div>
            </div>
        </div>
        </div>';

        $data = [
            'FIRST_NAME' => $client_name,
            'PHONE' => $phone,
            'DATE' => date('Y-m-d', strtotime($quotation->created_at)),
            'TABLE' => $table,
            'CARD_NUMBER' => $id_number,
            'ADDRESS' => $quotation->city->name,
            'CONTRACT_NUMBER' => $quotation->q_number,
            'MACHINE_SPEED' => $machineSpeed,
            'ELEVATOR_TYPE' => $elevatorType,
            'MACHINE_LOAD' => $machineLoad,
            'MACHINE_TYPE' => $machineType,
            'MACHINE_WARRANTY' => $machineWarranty,
            'PEOPLE_LOAD' => $peopleLoad,
            'CONTROL_CARD' => $controlCard,
            'OTHER' => $quotation->other_additions,
            'PAYMENT' => $payment_table,
            'elevator_warranty' => $quotation->elevator_warranty_id
        ];

        $mpdf->WriteHTML(view(
            'installation.pdf.quotation',
            compact('data', 'template')
        )->render());

        return $mpdf->Output();
    }
}
