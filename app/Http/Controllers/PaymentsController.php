<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    function index()
    {
        $payments = Payment::with(
            'contract'
        )
            ->orderBy('created_at', 'desc')
            ->get();

        return PaymentResource::collection($payments);
    }
}
