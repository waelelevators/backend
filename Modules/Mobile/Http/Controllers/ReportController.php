<?php

namespace Modules\Mobile\Http\Controllers;

use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return [
            'data' => [
                'total' => 10,
                'items' => [
                    'total' => 10,
                    'items' => []
                ]
            ]

        ];
    }
}