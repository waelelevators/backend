<?php

namespace App\Http\Controllers;

use App\Helpers\PdfHelper;
use App\Models\InstallationLocationDetection;
use App\Models\Template;
use Illuminate\Http\Request;

class PdfInstallationLDController extends Controller
{
    //
    public function pdf($id)
    {
        $template = Template::findOrFail(7); // كشف موقع

        $model = InstallationLocationDetection::findOrfail($id);

        $name = $model->user->name;

        $doneBy = __('pdf.Export By') . ' ' . ($name ?? '');

        $template = $template->data['contract'];

        $mpdf = PdfHelper::generateContractPDF($name, $doneBy);

        $client =  $model->client; // بيانات العميل
        $well =  $model['well_data']; // ابعاد البئر
        $machine =  $model['machine_data'];  // ابعاد غرفة الماكينة
        $stopNumber = $model->stopsNumber->name; // عدد الوقفات
        $elevatorTrip = $model->elevatorTrip->name; // مشوار المصعد
        $dbgLocation = $well['elevator_weight_location_id']; // موقع الثقل 1 خلفي 2 يمين 3 يسار
        $date  = date('d-m-Y', strtotime($model->created_at));
        $phone  = $client->phone; // رقم الجوال
        $city = $model->city->name; // الحي 
        $neighborhood = $model->neighborhood->name; // الحي 
        $notes = $model->notes;

        $client_name = $client->name; // اسم العميل

        $imageHtml = $this->generateImageHtml($well['elevator_trips_id']); // صورة لابعاد كشف المصعد 

        $tableHtml  = $this->generateTable($well, $machine); // ابعاد كشف المصعد

        $dbgHtml = $this->generateDbgHtml($dbgLocation, $well); // ابعاد الكابينة 

        $doorSpecificationHtml = $this->generateDoorSpecificationHtml($model->floor_data); // مواصفات الباب

        $data = [
            'PHONE' => $phone,
            'DATE' => $date,
            'FIRST_NAME' => $client_name,
            'CITY' => $city, // اسم المدينة
            'NEIGHBORHOOD' => $neighborhood, // الحي
            'TABLE' => $tableHtml,
            'IMAGE' => $imageHtml,
            'DBG' => $dbgHtml, // ابعاد الكابينة
            'STOP_NUMBER' => $stopNumber, // عدد الوقفات 
            'ELEVATOR_TRIP' => $elevatorTrip,
            'DOOR_SPECIFICATION' => $doorSpecificationHtml, // مواصفات الباب
            'NOTES' => $notes
        ];

        $mpdf->WriteHTML(view(
            'installation.pdf.location',
            compact('data', 'template')
        )->render());

        return $mpdf->Output();
    }

    private function generateImageHtml($elevatorTripsId)
    {
        return '
            <div class="left-signature">
                <img style="width:100%" src="https://waelsoft.com/cms/images/' . $elevatorTripsId . '.png" alt="Example Image">
            </div>';
    }

    private function generateDbgHtml($dbgLocation, $well)
    {
        $dbgUrls = [
            1 => "https://waelsoft.com/cms/images/door_back_dbg.jpg",
            2 => "https://waelsoft.com/cms/images/right_dbg.png",
            3 => "https://waelsoft.com/cms/images/left_dbg.png",
            null => ''
        ];

        $dbgUrl = $dbgUrls[$dbgLocation] ?? '';

        return '
            <div class="page-break">
                <h3>' . __('pdf.Cabin Dimension') . '</h3>
                <div class="left-signature">
                    <img style="width:100%" src="' . $dbgUrl . '" alt="Example Image">
                </div>
                ' . $this->generateCabinDimensionTable($well) . '
            </div>';
    }

    private function generateTable($well, $machine)
    {
        return '
        <div class="right-signature">
            <table style="margin-top:30" id="example1" class="pdf">
                <thead>
                    ' . $this->generateTableHeaders() . '
                    ' . $this->generateTableRows($well, $machine) . '
                </thead>
            </table>
        </div>';
    }
    private function generateTableHeaders()
    {
        return '
        <tr>
            <th style="background-color:#20536b"><span style="color:white">#</span></th>
            <th>' . __('pdf.Item Name') . '</th>
            <th>' . __('pdf.Item Value') . '</th>
        </tr>';
    }

    private function generateTableRows($well, $machine)
    {
        $rows = [
            'A' => ['label' => __('pdf.Well Height'), 'value' => $well['well_height']],
            'B' => ['label' => __('pdf.Last Floor Height'), 'value' => $well['last_floor_height']],
            'C' => ['label' => __('pdf.Well Width'), 'value' => $well['well_width']],
            'D' => ['label' => __('pdf.Well Depth'), 'value' => $well['well_depth']],
            'E' => ['label' => __('pdf.Floor Measurement'), 'value' => $well['bottom_the_elevator']],
            'H' => ['label' => __('pdf.Mach Height'), 'value' => $machine['machine_room_height']],
            'J' => ['label' => __('pdf.Mach Width'), 'value' => $machine['machine_room_width']],
            'K' => ['label' => __('pdf.Mach Depth'), 'value' => $machine['machine_room_depth']],
        ];

        $html = '';
        foreach ($rows as $key => $row) {
            $html .= '
                <tr>
                    <th style="background-color:#20536b"><span style="color:white">' . $key . '</span></th>
                    <th>' . $row['label'] . '</th>
                    <th>' . $row['value'] . '</th>
                </tr>';
        }
        return $html;
    }
    private function generateCabinDimensionTable($well)
    {
        $rows = [
            'A' => ['label' => __('pdf.Car Dbg'), 'value' => $well['dbg_cabin']],
            'B' => ['label' => __('pdf.Car Depth'), 'value' => $well['cabin_depth']],
            'C' => ['label' => __('pdf.Car Width'), 'value' => $well['cabin_width']],
            'D' => ['label' => __('pdf.Weight Dbg'), 'value' => $well['dbg_weight']],
            'E' => ['label' => __('pdf.Machine Weight'), 'value' => $well['machine_load']],
            'F' => ['label' => __('pdf.People Weight'), 'value' => $well['people_load']]
        ];

        $html = '
            <div class="left-signature">
                <table style="margin-top:100" id="example2" class="pdf">
                    <thead>
                        <tr>
                            <th style="background-color:#20536b"><span style="color:white">#</span></th>
                            <th>' . __('pdf.Item Name') . '</th>
                            <th>' . __('pdf.Item Value') . '</th>
                        </tr>
                        ' . $this->generateCabinDimensionRows($rows) . '
                    </thead>
                </table>
            </div>';
        return $html;
    }
    private function generateCabinDimensionRows($rows)
    {
        $html = '';

        foreach ($rows as $key => $row) {
            $html .= '
                <tr>
                    <th style="background-color:#20536b"><span style="color:white">' . $key . '</span></th>
                    <th>' . $row['label'] . '</th>
                    <th>' . $row['value'] . '</th>
                </tr>';
        }
        return $html;
    }

    private function generateDoorSpecificationHtml($floorData)
    {
        $floors = [
            'كل الطوابق',
            'الطابق الاولى',
            'الطابق الثاني',
            'الطابق الثالث',
            'الطابق الرابع',
            'الطابق الخامس',
            'الطابق السادس',
            'الطابق السابع',
            'الطابق الثامن',
            'الطابق التاسع',
            'الطابق العاشر',
            'الطابق الحادي عشر',
            'الطابق الثاني عشر'
        ];

        $headerColumns = [
            '#',
            __('pdf.Floor'),
            __('pdf.Floor Well Width'),
            __('pdf.Floor Well Depth'),
            __('pdf.Right Shoulder'),
            __('pdf.Door Height'),
            __('pdf.Door Size'),
            __('pdf.Left Shoulder'),
            __('pdf.Floor Height')
        ];

        $rowsHtml = $this->generateDoorSpecificationRows($floorData, $floors);

        return '
        <div class="page-break">
            <h3>' . __('pdf.Door Specification') . '</h3>
            <table style="margin-bottom:30px;width:670px" class="pdf table table-bordered table-striped">
                <tbody>
                    <tr style="background-color:#20536b">
                        ' . $this->generateDoorTableHeaders($headerColumns) . '
                    </tr>
                    ' . $rowsHtml . '
                </tbody>
            </table>
        </div>';
    }

    private function generateDoorTableHeaders($columns)
    {
        $headersHtml = '';
        foreach ($columns as $column) {
            $headersHtml .= '<td><span style="color:white">' . $column . '</span></td>';
        }
        return $headersHtml;
    }

    private function generateDoorSpecificationRows($floorData, $floors)
    {
        $rowsHtml = '';
        foreach ($floorData as $index => $doorSize) {
            $rowsHtml .= '<tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . $floors[$doorSize['floor_id'] - 1] . '</td>
                        <td>' . $doorSize['well_width'] . '</td>
                        <td>' . $doorSize['well_depth'] . '</td>
                        <td>' . $doorSize['right_shoulder_size'] . '</td>
                        <td>' . $doorSize['door_height'] . '</td>
                        <td>' . $doorSize['door_size'] . '</td>
                        <td>' . $doorSize['left_shoulder_size'] . '</td>
                        <td>' . $doorSize['floor_height'] . '</td>
                      </tr>';
        }
        return $rowsHtml;
    }
}
