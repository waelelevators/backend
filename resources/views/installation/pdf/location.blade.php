@extends('layouts.master')
@section('content')
    <!-- 'DBG' => $DBG, // ابعاد الكابينة -->
    @php
        $contractText = str_replace('FIRST_NAME', $data['FIRST_NAME'], $template);
        $contractText = str_replace('PHONE', $data['PHONE'], $contractText);
        $contractText = str_replace('DATE', $data['DATE'], $contractText);
        $contractText = str_replace('CITY', $data['CITY'], $contractText);
        $contractText = str_replace('NEIGHBORHOOD', $data['NEIGHBORHOOD'], $contractText);
        $contractText = str_replace('TABLE', $data['TABLE'], $contractText);
        $contractText = str_replace('IMAGE', $data['IMAGE'], $contractText);
        $contractText = str_replace('DBG', $data['DBG'], $contractText);
        $contractText = str_replace('ELEVATOR_TRIP', $data['ELEVATOR_TRIP'], $contractText);
        $contractText = str_replace('STOP_NUMBER', $data['STOP_NUMBER'], $contractText);
        $contractText = str_replace('DOOR_SPECIFICATION', $data['DOOR_SPECIFICATION'], $contractText);
        $contractText = str_replace('NOTES', $data['NOTES'], $contractText);
    @endphp

    <div class="container" style="direction: rtl" id="container">

        <div>
            <div class="left">
                <h3 style="text-align:center">
                    ###
                </h3>
            </div>

            <div class="right">
                <h3 style="text-align:right">
                    التاريخ
                    <span>{!! $data['DATE'] !!}</span>
                </h3>
            </div>
            <div class="middle">
                <h3 style="text-align:center">
                    كشف مصعد
                </h3>
            </div>

        </div>

        <p>{!! $contractText !!}</p>

    </div>
@endsection
