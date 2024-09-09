<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\Contract;
use App\Models\Setting;
use Alkoumi\LaravelArabicNumbers\Numbers;
use App\Helpers\PdfHelper;
use App\Models\InstallationQuotation;
use Illuminate\Routing\Controller;
use App\Models\Template;

class PdfContractController extends Controller
{
    function offerNew($id) // عرض السعر الجديد لسع م خلص
    {

        $mpdf = new Mpdf([
            'default_font_size' => 8,
            'default_font' => 'changa',
            'orientation' => 'L', // Portrait orientation by default
        ]);

        $stylesheet = file_get_contents(public_path('css/pdf.css'));
        $stylesheet2 = file_get_contents(public_path('css/pdf-styles.css'));

        $mpdf->SetDirectionality('RTL');
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);

        $quotation =  InstallationQuotation::findOrFail($id);

        $aboutUs = '<div>
        <h2>من نحن</h2> 
        <p>
        ' . __('pdf.About Us Body') . '
        </p>
        </div>';

        $introduction = '
        <h2>المقدمة</h2>
        <p>
        ' . __('pdf.Intro Body') . '
        </p>
        <div class="box">
            <div>

                <div class="desc-box-intro">
                    <div class="desc-header">
                        <p>' . __('pdf.Stop Number') . '</p>
                    </div>
                    
                </div>

                <div class="desc-box-intro">
                    <div class="desc-header">
                        <p>' . __('pdf.City') . '</p>
                    </div>
                    
                </div>
            </div>
            <div>
        
                <div class="desc-box-intro">
                    <div class="desc-header">
                    <p>' . $quotation->elevator_data['stop_number_id'] . '</p>
                    </div>
                </div>

                <div class="desc-box-intro">
                    <div class="desc-header">
                    <p>' . $quotation->city->name . '</p>
                    </div>
                </div>
            </div>
        </div>';


        $specification = '

        <h1>المواصفات الفنية</h1>

        <div>

            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                  '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>

            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>
            
            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>

            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                  '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>

            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>
            
        </div>
        
        <div style="padding:10px 70px">

            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                  '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>

            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>
            
            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>

            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                  '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>

            <div class="desc-box">
                <div class="desc-header">
                    <p>' . __('pdf.Floor') . '</p>
                </div>
                <div class="desc-body">
                    <span style="text-align:center;color:red">
                '  . __('pdf.Floor') . '
                    </span>
                </div>
            </div>
            
        </div>
        ';

        // Example data for three pages with different background images
        $pages = [
            [
                'background_image' => 'https://store.waelelevators.net/public/images/1.jpg',
                'content' => '
                <div style="
                position: relative; 
                width: 100%;
                height: 100vh;">
                    <div style="
                        position: absolute;
                        bottom: 0; 
                        right: 0;
                        padding: 320px 10px 0 0;
                        text-align: right;">
                        <h1>عرض سعر</h1>
                        <h1>المشروع</h1>
                        <h1>اسم العميل</h1>
                        <h1>التاريخ</h1>
                    </div>
                </div>'
            ],
            [
                'background_image' => 'https://store.waelelevators.net/public/images/2.jpg',
                'content' => $aboutUs,
            ],
            [
                'background_image' => 'https://store.waelelevators.net/public/images/introduction.jpg',
                'content' =>  $introduction,
            ],
            [
                'background_image' => 'https://store.waelelevators.net/public/images/main.jpg',
                'content' => $specification,
            ],
        ];

        // $html = '<div style="background-image: url(' . $page['background_image'] . ');
        // background-size: cover; 
        // background-repeat: no-repeat; width: 100%; height: 100%;">';
        // Generate PDF content for each page
        foreach ($pages as $page) {
            // Set background image and content for the page
            $html = '<div>';
            $html .= $page['content'];
            $html .= '</div>';
            $mpdf->SetDefaultBodyCSS('background', "url('{$page['background_image']}')");
            $mpdf->SetDefaultBodyCSS('background-image-resize', 4);
            $mpdf->SetDefaultBodyCSS('background-size', 'cover');
            $mpdf->SetDefaultBodyCSS('background-repeat', 'no-repeat');
            $mpdf->SetDefaultBodyCSS('width', '100%');
            $mpdf->SetDefaultBodyCSS('height', '100%');

            $mpdf->WriteHTML($html);
            $mpdf->AddPage();
        }

        // Output PDF
        $mpdf->Output();
    }


    //         </div>';
    //         $page2Content = '<div class="page2"><h1>Page 2 Content</h1></div>';
    //         // Generate content for more pages as needed
    //         // Combine HTML content for the entire document
    //         $html = $css . $page1Content; // Combine content for all pages

    //         // $mpdf->SetHTMLHeader('<div style="width:100%;height:30mm;padding-top:6mm"></div>');
    //         // $mpdf->SetHTMLFooter('<div style="float:left;width:50%;height:30mm;">
    //         //                     <h5 style="text-algin:center">{PAGENO}/{nbpg}</h5></div>
    //         //                     <div style="float:right;width:50%;height:30mm;">
    //         //                     <h5>' .  'Done By' . ' ' . 'Hani' . '</h5></div>');

    //         // $mpdf->SetDefaultBodyCSS('background', "url(https://store.waelelevators.net/public/images/1.jpg)");
    //         // $mpdf->SetDefaultBodyCSS('background-image-resize', 6);

    //         $Setting = Setting::where('name', 'template_2')->first();

    //         $template =  $Setting->data['contract'];

    //         $model = InstallationQuotation::findOrfail($id);

    //         // $html = "hani";

    //         $data = [
    //             'HOME' => $page1Content,
    //             'SECOND' => $page2Content,
    //         ];
    //         $mpdf->WriteHTML($html);
    //         // $mpdf->WriteHTML(view('offer', compact('data', 'template'))->render());

    //         return $mpdf->Output();
    //     }

    // عقد تركيب
    function pdf($id)
    {
        // Retrieve the contract and related entities efficiently
        $contract = Contract::with([
            'installments',
            'createdBy.employee',
            'locationDetection.client',
            'elevatorType',
            'outerDoorDirections',
            'StopsNumbers',
            'EntrancesNumber',
            'elevatorTrip',
            'elevatorRoom',
            'MachineSpeed',
            'MachineTYPE',
            'MachineLoad',
            'PeopleLoad',
            'ControlCard',
            'machineWarranty',
            'elevatorWarranty',
            'freeMaintenance',
            'cabinRailsSize',
            'counterWeightRailsSize',
            'innerDoorType',
            'doorSize'
        ])->findOrFail($id);

        $Setting = Template::findOrFail($contract->template_id);

        $installments =  $contract->installments;

        $name = optional($contract->createdBy->employee)->name;

        $doneBy = __('pdf.Export By') . ($name ?? '');

        $mpdf =   PdfHelper::generateContractPDF($name, $doneBy);

        list($payment_table, $totalAmountArabic) = $this->buildPaymentTable($contract);

        // Build the technical specifications table
        $technical_specs = $this->buildTechnicalSpecs($contract);

        // Extract client information
        $client = $contract->locationDetection->client;

        // return $client;
        $client_name = ($client->type == 2 || $client->type == 3)
            ? $client->name
            : implode(
                ' ',
                [
                    $client->first_name,
                    $client->second_name,
                    $client->third_name,
                    $client->last_name
                ]
            );

        $id_number = ($client->type == 2 ? 'رقم السجل التجاري: ' : 'رقم الهوية: ') .
            ($client->type == 2 ? $client->commercial_register : $client->id_number);
        $phone = $client->phone;

        // Prepare the final data set
        $data = [
            'FIRST_NAME' => $client_name,
            'PHONE' => $phone,
            'DATE' => $contract->created_at->format('Y-m-d'),
            'CARD_NUMBER' => $id_number,
            'TABLE' => $technical_specs,
            'ADDRESS' => $contract->locationDetection->city->name,
            'CONTRACT_NUMBER' => $contract->contract_number,
            'MACHINE_SPEED' => $contract->MachineSpeed->name,
            'ELEVATOR_TYPE' => $contract->elevatorType->name ?? 'غير معروف',
            'MACHINE_TYPE' => $contract->MachineTYPE->name,
            'MACHINE_LOAD' => $contract->MachineLoad->name ?? 'غير معروف',
            'PEOPLE_LOAD' => $contract->PeopleLoad->name,
            'CARD_TYPE' => $contract->ControlCard->name,
            'OTHER' => $contract->other_additions,
            'PAYMENT' => $payment_table,
            'MACHINE_WARRANTY' => $contract->machineWarranty->name,
            'ELEVATOR_WARRANTY' => $contract->elevatorWarranty->name,
            'VISIT_NUMBERS' => $contract->visits_number,
            'FREE_MAINTENANCE' => $contract->freeMaintenance->name,
            'CABIN_RAILS' => $contract->cabinRailsSize->name,
            'WEIGHT_RAILS' => $contract->counterWeightRailsSize->name,
            'OPEN_DIRECTION' => $contract->innerDoorType->name,
            'DOOR_SIZE' => $contract->doorSize->name,
        ];

        $template = $Setting->data['contract'];

        // Generate the PDF
        $mpdf->WriteHTML(view(
            'installation.pdf.contract',
            compact('data', 'template')
        )->render());

        return $mpdf->Output();
    }


    // Helper function to build payment table
    function buildPaymentTable($contract)
    {
        $amount = 0;
        $tax = 0;
        $data_payment_table = '';

        $payment_names = [
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
        foreach ($contract->installments as  $installment) {

            $amount += $installment->amount; // اجمالي المبلغ من غير ضريبة
            $tax += $installment->tax; // اجمالي المبلغ بالضريبة
            $taxOnly =  $installment->tax - $installment->amount; // ضريبة القيمة المضافة

            $arabicNumber = Numbers::TafqeetNumber($installment->tax);
            $data_payment_table .= "
            <tr>
               <td> {$payment_names[$index++]} </td>
                <td>{$installment->amount}</td>
                <td>{$taxOnly}</td>
                <td>{$installment->tax}</td>
                <td>{$arabicNumber} ريال</td>
            </tr>";
        }

        $totalTax = $tax - $amount;
        $totalAmountArabic = Numbers::TafqeetNumber($tax);

        $payment_table = "
            <div class='page-break'>
                <table style='margin-bottom:30px;width:670px' class='pdf table table-bordered table-striped'>
                    <tbody>
                        <tr style='background-color:#20536b'>
                            <td><span style='color:white'>البيان</span></td>
                            <td><span style='color:white'>اجمالى السعر</span></td>
                            <td><span style='color:white'>القيمه المضافه</span></td>
                            <td><span style='color:white'>المبلغ بالضريبة</span></td>
                            <td><span style='color:white'>المبلغ كتابتا</span></td>
                        </tr>
                        <tr>
                            <td>سعر المصعد</td>
                            <td>{$amount}</td>
                            <td>{$totalTax}</td>
                            <td>{$tax}</td>
                            <td>{$totalAmountArabic} ريال</td>
                        </tr>
                        <tr style='background-color: #20536b; color: white'>
                            <td colspan='4' style='text-align: center; color: white'>شروط الدفع على النحو التالي</td>
                        </tr>
                        {$data_payment_table}
                    </tbody>
                </table>
            </div>";

        return [$payment_table, $totalAmountArabic];
    }

    // Helper function to build technical specs
    function buildTechnicalSpecs($contract)
    {
        $specs = [
            'نوع المصعد' => $contract->elevatorType->name ?? 'غير معروف',
            'عدد الوقفات' => $contract->StopsNumbers->name ?? 'غير معروف',
            'مشوار المصعد' => $contract->elevatorTrip->name ?? 'غير معروف',
            'عدد المداخل' => $contract->EntrancesNumber->name ?? 'غير معروف',
            'غرفه المصعد' => $contract->elevatorRoom->name ?? 'غير معروف'
        ];

        $html = "<div class='page-break'><h2>المواصفات الفنية</h2>";
        foreach ($specs as $header => $value) {
            $html .= "
        <div class='desc-box'>
            <div class='desc-header'><p>{$header}</p></div>
            <div class='desc-body'><span style='text-align:center;color:red'>{$value}</span></div>
        </div>";
        }
        $html .= "</div>";

        return $html;
    }

    function createOld($id)
    {

        $data = ['name' => 'John Doe']; // Add any data you want to pass to the view.

        $mpdf = new Mpdf();

        // set font support arabic

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

            $data_payment_table .= '<tr>
                                            <td>' . $payment_nots[$index++] . '</td>
                                            <td>' . $installment->amount . '</td>
                                            <td>' . $installment->tax . '</td>
                                            <td>' . ($installment->amount + $installment->tax) . '</td>
                                </tr>';
        }

        $payment_table = '
        <div class="page-break">
        <table class="table">
        <tbody>
        <tr style="background-color: #20536b; color: white">
                                        <td>البيان</td>
                                        <td>اجمالى السعر</td>
                                        <td>القيمه المضافه</td>
                                        <td>المبلغ بالضريبة </td>
                        </tr>

                        <tr>
                                        <td>سعر المصعد</td>
                                        <td>' . $amount . '</td>
                                        <td>' . $tax . '</td>
                                        <td>' . ($amount + $tax) . '</td>
                        </tr>
                        <tr style="background-color: #20536b; color: white">
                                        <td colspan="4" style="text-align: center; color: white">شروط الدفع على النحو التالى</td>
                        </tr>';


        $payment_table .= $data_payment_table;
        $payment_table .= '</tbody></table></div>';
        // return $contract->MachineSpeed;
        // $contract =  ContractResource::make($contract);
        $client =  $contract->client;

        // return $client;

        $client_name = "";
        $id_number = '';
        $phone = '';

        if ($client->type == "1") {

            $client_name = $client->data['owner_name'];
            $id_number  = $client->data['id_number'];
            $phone  = $client->data['phone'];
        }

        $templatesddd =  $Setting->data['contract'];
        $elevatorType = $contract->elevatorType->name ?? 'غير معروف';
        $outerDoorDirections = $contract->outerDoorDirections->name ?? 'غير معروف';
        $StopsNumbers = $contract->StopsNumbers->name ?? 'غير معروف';
        $EntrancesNumber = $contract->EntrancesNumber->name ?? 'غير معروف';
        $elevatorTrip = $contract->elevatorTrip->name ?? 'غير معروف';
        $elevatorRoom = $contract->elevatorRoom->name ?? 'غير معروف';

        // return $contract;

        $table =  '<table class="table">
                    <thead class="thead-inverse">
                        <tr>
                            <th>نوع المصعد</th>
                            <th>عدد الوقفات</th>
                            <th>مشوار المصعد</th>
                            <th>عدد الداخل</th>
                            <th>غرفه المصعد</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr style=" color: red">
                                <td>' . $elevatorType . '</td>
                                <td>' . $StopsNumbers . '</td>
                                <td>' . $elevatorTrip . '</td>
                                <td>' . $EntrancesNumber . '</td>
                                <td>' . $elevatorRoom . '</td>
                            </tr>

                        </tbody>
                </table>';


        $data = [
            'FRIST_NAME' => $client_name,
            'PHONE' => $phone,
            'create' => now(),
            'DATA' => $contract->created_at,
            'CARD_NMUMBER' => $id_number,
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


        return view('welcome', compact('data', 'template'));

        $mpdf->WriteHTML(view('welcome', compact('data', 'template'))->render());

        return $mpdf->Output();
    }
}
