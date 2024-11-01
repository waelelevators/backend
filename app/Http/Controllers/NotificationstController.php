<?php

namespace App\Http\Controllers;


use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationstController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($fliter = 'unread')
    {


        if ($fliter == 'all') {
            $notifications = Notification::latest()
                ->where('user_id', auth('sanctum')->user()->id)
                ->get();
        } else if ($fliter == "unread-count") {
            $notifications = Notification::latest()
                ->where('user_id', auth('sanctum')->user()->id)
                ->where('read_at', NULL)
                ->count();
        } else {
            $notifications = Notification::latest()
                ->where('user_id', auth('sanctum')->user()->id)
                ->where('read_at', NULL)
                ->take(10)
                ->get();
        }
        return $notifications;
    }


    // how can I make notifications as read
    function make_as_read(Notification $notification)
    {

        $notification->read_at = now();
        $notification->save();


        return  Notification::latest()
            ->where('user_id', auth('sanctum')->user()->id)
            ->where('read_at', NULL)
            ->take(10)
            ->get();

        return response()->noContent();
    }
}
