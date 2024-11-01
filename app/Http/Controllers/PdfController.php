<?php

namespace App\Http\Controllers;

use Alkoumi\LaravelArabicNumbers\Numbers;

use App\Models\Contract;
use App\Models\Setting;
use Mpdf\Mpdf;


class generatePDFController extends Controller
{
    function create($id)
    {
        $mpdf = new Mpdf([
            'default_font_size' => 8,
            'default_font' => 'changa',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch',
        ]);

        // Result => "فقط تسعمائة ألف ريال و أربعة و ثلاثون هللة لاغير"

        $mpdf->SetHTMLHeader('<div style="width:100%;height:30mm;padding-top:6mm"></div>');
        $mpdf->SetHTMLFooter('<div style="float:left;width:50%;height:30mm;">
                            <h5 style="text-algin:center">{PAGENO}/{nbpg}</h5></div>
                            <div style="float:right;width:50%;height:30mm;">
                            <h5>' .  'Done By' . ' ' . 'Hani' . '</h5></div>');

        $mpdf->SetDefaultBodyCSS('background', "url(https://waelsoft.com/cms/images/macca.png)");
        $mpdf->SetDefaultBodyCSS('background-image-resize', 6);


        $Setting = Setting::where('name', 'template')->first();

        $contract =  Contract::where('id', $id)->first();

        $installments =  $contract->installments;


        $data_payment_table = '';
        $amount = 0;
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
            'الدفعه الثانيه عشر',

        ];
        $index = 0;
        foreach ($installments as $installment) {
            $amount += $installment->amount;
            $tax += $installment->tax;
            $totalWithTaxPerRow = $installment->amount + $installment->tax;
            $totalWithTax = $amount + $tax;

            $arabicNumber = Numbers::TafqeetNumber($totalWithTaxPerRow);
            $totalAmountArabic = Numbers::TafqeetNumber($totalWithTax);

            $data_payment_table .= '<tr>
                                            <td>' . $payment_nots[$index++] . '</td>
                                            <td>' . $installment->amount . '</td>
                                            <td>' . $installment->tax . '</td>
                                            <td>' . $totalWithTaxPerRow . '</td>
                                            <td>' . $arabicNumber . ' ريال</td>
                                    </tr>';
        }

        $payment_table = '
        
        <table class="page-break" style="margin-bottom:30px;width:670px" class="pdf table table-bordered table-striped">
    
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
                                        <td>' . $amount . '</td>
                                        <td>' . $tax . '</td>
                                        <td>' . ($amount + $tax) . '</td>
                                        <td>' . $totalAmountArabic . ' ريال</td>
                        </tr>
                        <tr style="background-color: #20536b; color: white">
                                        <td colspan="4" style="text-align: center; color: white">شروط الدفع على النحو التالى</td>
                        </tr>';


        $payment_table .= $data_payment_table;
        $payment_table .= '</tbody></table>';
        // return $contract->MachineSpeed;
        // $contract =  ContractResource::make($contract);
        $client =  $contract->client;

        // return $client;

        $client_name = "";
        $id_number = '';
        $phone = '';

        //if ($client->type == "private") {

        $client_name = $client->type ==  1 ? $client->data['first_name'] . ' ' .
            $client->data['second_name'] . ' ' .
            $client->data['third_name']  . ' ' .
            $client->data['last_name'] : $client->data['name'];


        $id_number  = $client->data['id_number'];

        $phone  = $client->data['phone'];
        //}

        $template =  $Setting->data['contract'];
        $elevatorType = $contract->elevatorType->name ?? 'غير معروف';
        $outerDoorDirections = $contract->outerDoorDirections->name ?? 'غير معروف';
        $StopsNumbers = $contract->StopsNumbers->name ?? 'غير معروف';
        $EntrancesNumber = $contract->EntrancesNumber->name ?? 'غير معروف';
        $elevatorTrip = $contract->elevatorTrip->name ?? 'غير معروف';
        $elevatorRoom = $contract->elevatorRoom->name ?? 'غير معروف';

        $table = '
                <div class="page-break">
                <h2>المواصفات الفنية</h2>
                <div class="desc-box">
                    <div class="desc-header">
                        <p>
                        نوع المصعد
                        </p>
                    </div>
                    <div class="desc-body">
                        <span style="text-align:center;color:red">
                        ' . $elevatorType . '
                        </span>
                    </div>
                </div>
    
                <div class="desc-box">
                    <div class="desc-header">
                        <p>
                        عدد الوقفات
                          </p>
                    </div>
                    <div class="desc-body">
                        <span style="text-align:center;color:red">
                        ' . $StopsNumbers . '
                        </span>
                    </div>
                </div>
    
                <div class="desc-box">
                    <div class="desc-header">
                        <p>
                        مشوار المصعد
                        </p>
                    </div>
                    <div class="desc-body">
                        <span style="text-align:center;color:red">
                      
                        ' . $elevatorTrip . '
                        </span>
                    </div>
                </div>
    
                <div class="desc-box">
                    <div class="desc-header">
                        <p>
                        عدد المداخل
                        </p>
                    </div>
                    <div class="desc-body">
                        <span style="text-align:center;color:red">
                        ' . $EntrancesNumber . '
                        </span>
    
                    </div>
                </div>
    
                <div class="desc-box">
                    <div class="desc-header">
                        <p>
                        غرفه المصعد
                        </p>
                    </div>
                    <div class="desc-body">
                        <span style="text-align:center;color:red">
                        ' . $elevatorRoom . '
                        </span>
                    </div>
                </div>
    
            </div>
                ';


        $data = [
            'FIRST_NAME' => $client_name,
            'PHONE' => $phone,
            'create' => now(),
            'DATA' => $contract->created_at,
            'CARD_NUMBER' => $id_number,
            'TABLE' => $table,
            'ADDRESS' => $contract->city->name,
            'CONTRACT_NUMBER' => $contract->contract_number,
            'MACHINE_SPEED' => $contract->MachineSpeed->name,
            'ELEVATOR_TYPE' => $contract->elevatorType->name ?? 'غير معروف ',
            'MACHINE_LOAD' => $contract->MachineLoad->name ?? 'غير معروف ',
            'MACHINE_WARRANTY' => $contract->machine_warranty_id,
            'PEOPLE_LOAD' => $contract->PeopleLoad->name,
            'CONTROL_CARD' => $contract->ControlCard->name,
            'OTHER' => $contract->other_additions,
            'PAYMENT' => $payment_table,
            'elevator_warranty' => $contract->elevator_warranty_id,
        ];


        //  return view('welcome', compact('data', 'template'));

        $mpdf->WriteHTML(view('welcome', compact('data', 'template'))->render());

        return $mpdf->Output();
    }
}
