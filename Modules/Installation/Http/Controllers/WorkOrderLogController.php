<?php

namespace Modules\Installation\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Helpers\MyHelper;
use App\Models\WorkOrder;
use App\Models\WorkOrderComment;
use App\Models\WorkOrderLog;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class WorkOrderLogController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return WorkOrder::with('contract', 'stage')->get();
    }

    function store(Request $request)
    {
        // Start a transaction
        DB::beginTransaction();

        try {
            if (isset($request['attachment'])) $attachment = ApiHelper::uploadBase64Image(
                $request['attachment'],
                'work-order'
            );
            else $attachment = '';

            $work_comment = new WorkOrderComment;
            $work_comment->work_order_id = $request->id;
            $work_comment->comment = $request->comment ?? '';
            $work_comment->attachment = $attachment;
            $work_comment->user_id = auth('sanctum')->user()->id;
            $work_comment->save();


            $WorkOrder = WorkOrder::find($request->id);

            if ($request->status == 'ready for delivery') {

                $WorkOrder->status_id  = $request->status;
                $WorkOrder->save();

                $log = new WorkOrderLog();
                $log->work_order_id = $WorkOrder->id;
                $log->status = $request->status;
                $log->user_id = auth('sanctum')->user()->id;
                $log->comment = $request->comment ?? '';
                $log->save();


                MyHelper::pushNotification([$WorkOrder->user->email], [
                    'title' => 'تم تعديل حاله امر العمل رقم #' . $WorkOrder->id,
                    'body' => 'تم ارسال امر العمل الى الموافقه الرجاء فتح التاكد من جوده امر العمل ',
                    'deep_link' => 'http://localhost:3000/installations/work-orders/' . $WorkOrder->id
                ]);

                // Commit the transaction
                DB::commit();

                return response([
                    'status' => 'success',
                    'message' => 'comment added successfully',
                    'workOrder' => $WorkOrder,
                    'comments' => $WorkOrder->comments
                ], 200);
            } else {

                $emails = $WorkOrder->technicians->pluck('employee.user.email')->toArray();

                MyHelper::pushNotification($emails, [
                    'title' => 'اضافة تعليق على امر العمل رقم #' . $WorkOrder->id,
                    'body' => 'تم اضافة تعليق على امر العمل رقم #' . $WorkOrder->id,
                    'deep_link' => 'http://localhost:3000/installations/work-orders/' . $WorkOrder->id
                ]);

                // Commit the transaction
                DB::commit();

                return WorkOrderComment::where('work_order_id', $request->id)->get();
            }
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();
            // Handle the exception, you can log it or return an error response
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }


    public function attachment(Request $request, $work_order_id)
    {


        $request->validate([
            'attachment' => 'nullable|mimes:pdf,jpg,jpeg,png,gif,svg,mp4,mp3,m4a,word,doc,docx,xls,xlsx,ppt,pptx', // Adjust the allowed file types and size as needed
        ]);

        // Get the uploaded file
        $file = $request->file('attachment');

        // Determine the storage path
        $storagePath = 'work-order/' . $work_order_id; // You can customize the path as needed

        // Store the uploaded file in the storage disk
        $filePath = $file->store($storagePath, 'public');
        return $filePath;
        return response()->json(['message' => 'File uploaded successfully', 'file_path' => $filePath]);
    }
}
