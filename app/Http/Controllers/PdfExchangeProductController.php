<?php

namespace App\Http\Controllers;

use App\Helpers\PdfHelper;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\WorkOrder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

// صرف البضاعة للفنيين
class PdfExchangeProductController extends Controller
{
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function pdf($id)
    {

        $workOrder = WorkOrder::with([
            'locationStatus',
            'technicians.employee'
        ])->findOrFail($id);

        $contract =  $workOrder->locationStatus->assignment->contract;

        $data = [
            'name' => $contract?->locationDetection?->client['name'],
            'contract_number' => $contract->contract_number,
            'phone' => $contract->locationDetection->client['phone'] ?? '',
            'DATE' => date('Y-m-d', strtotime($workOrder->created_at)),
            'elevator_trip_id' => $contract->elevator_trip_id, // مشوار المصعد 
            'stop_number_id' => $contract->stop_number_id, // عدد الوقفات
            'stage' => $workOrder->stage['name'], // اسم المرحلة
            'TECHNICIANS' => $workOrder->technicians, // الفنيين



            'ENTRANCES_NUMBER' => $contract->EntrancesNumber->name, // عدد المداخل 
            'DOORDIRECTIONS' => $contract->outerDoorDirections?->name, // اتجاه فتح الباب الخارجي 

            'ELEVATOR_TYPE' => $contract->elevatorType->name, // نوع المصعد
            'DOOR_SIZE' => $contract->doorSize->name, // مقاس الباب

            'ELEVATOR_TRIP' => $contract->elevatorTrip->name, // مشوار المصعد
            'STOPS_NUMBERS' => $contract->stopsNumbers->name, // عدد الوقفات 

            'WEIGHT_RAILS' => $contract->counterWeightRailsSize->name, // مقاس سكة الثقل
            'CABIN_RAILS' => $contract->cabinRailsSize->name, // مقاس سكة الكبينة
            'MACHINE_TYPE' => $contract->machineType->name, //   نوع الماكينة
            'MACHINE_SPEED' => $contract->machineSpeed->name, //  سرعة الماكينة 
            'MACHINE_LOAD' => $contract->machineLoad->name, //   حمولة الماكنية
            'PEOPLE_LOAD' => $contract->peopleLoad->name, //  حمولة الاشخاص 

            'CONTROLE_CARD' => $contract->controlCard->name, // نوع الكرت
            'INTERNAL_DOOR_TYPE' => $contract->innerDoorType->name, // نوع الباب الداخلي

            'DOOR_SPECIFICATIONS' => $contract->outerDoorSpecifications, // مواصفات الباب الخارجي
        ];

        $dispatchs = Dispatch::where('work_order_id', $id)->pluck('id');
        $products =  DispatchItem::whereIn('dispatch_id', $dispatchs)
            ->with('product', 'dispatch.employee')->get();

        $name = optional($contract?->createdBy?->employee)->name; // محتاجة الي تغيير 

        $doneBy = __('pdf.Export By') . ($name ?? '');

        $mpdf = PdfHelper::generateContractPDF($name, $doneBy);

        if ($workOrder->stage_id == 1) {
            $mpdf->WriteHTML(view(
                'exchange-product.first',
                compact('data', 'products')
            )->render());
        } else  if ($workOrder->stage_id == 2) {
            $mpdf->WriteHTML(view(
                'exchange-product.second',
                compact('data', 'products')
            )->render());
        } else {
            $mpdf->WriteHTML(view(
                'exchange-product.third',
                compact('data', 'products')
            )->render());
        }


        return $mpdf->Output();
    }
}
