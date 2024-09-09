@extends('layouts.master')
@section('content')
    @php
        $contractText = str_replace('PHONE', $data['PHONE'], $template);
        $contractText = str_replace('FIRST_NAME', $data['FIRST_NAME'], $template);
        $contractText = str_replace('DATE', $data['DATE'], $contractText);
        $contractText = str_replace('CARD_NUMBER', $data['CARD_NUMBER'], $contractText);

        $contractText = str_replace('CABIN_RAILS', $data['CABIN_RAILS'], $contractText); // سكك الكبينة
        $contractText = str_replace('WEIGHT_RAILS', $data['WEIGHT_RAILS'], $contractText); // سكك الثقل
        $contractText = str_replace('OPEN_DIRECTION', $data['OPEN_DIRECTION'], $contractText); // اتجاه فتح الباب
        $contractText = str_replace('DOOR_SIZE', $data['DOOR_SIZE'], $contractText); // مقاس فتحة الباب
        $contractText = str_replace('ELEVATOR_TYPE', $data['ELEVATOR_TYPE'], $contractText); // نوع المصعد

        $contractText = str_replace('MACHINE_TYPE', $data['MACHINE_TYPE'], $contractText); // نوع الماكينة
        $contractText = str_replace('MACHINE_SPEED', $data['MACHINE_SPEED'], $contractText); // سرعة الماكينة

        $contractText = str_replace('MACHINE_WARRANTY', $data['MACHINE_WARRANTY'], $contractText); // ضمان الماكينة
        $contractText = str_replace('ELEVATOR_WARRANTY', $data['ELEVATOR_WARRANTY'], $contractText); // ضمان المصعد
        $contractText = str_replace('FREE_MAINTENANCE', $data['FREE_MAINTENANCE'], $contractText); // سنيين الصيانة المجانية

        $contractText = str_replace('PEOPLE_LOAD', $data['PEOPLE_LOAD'], $contractText); // حمولة الاشخاص
        $contractText = str_replace('MACHINE_LOAD', $data['MACHINE_LOAD'], $contractText); // حمولة الماكينة

        $contractText = str_replace('VISIT_NUMBERS', $data['VISIT_NUMBERS'], $contractText); // عدد الزيارات المجانية

        $contractText = str_replace('OTHER', $data['OTHER'], $contractText); //
        $contractText = str_replace('CONTRACT_NUMBER', $data['CONTRACT_NUMBER'], $contractText); // رقم العقد
        $contractText = str_replace('CARD_TYPE', $data['CARD_TYPE'], $contractText); // نوع الكرت
        $contractText = str_replace('ADDRESS', $data['ADDRESS'], $contractText); //
        $contractText = str_replace('TABLE', $data['TABLE'], $contractText); //
        $contractText = str_replace('PAYMENT', $data['PAYMENT'], $contractText); //
    @endphp


    <div class="container" style="direction: rtl" id="container">

        {{-- <p style="padding-right:90px;backgroun-color:re"> والله الموفق </p> --}}

        <h3 style="text-align:center">بســـم الله الرحمـــن الرحيــــم</h3>

        <div>
            <div class="left">
                <h3 style="text-align:left">
                    {{ $data['CONTRACT_NUMBER'] }}
                </h3>
            </div>

            <div class="right">
                <h3 style="text-align:right">
                    التاريخ :
                    <span>{!! $data['DATE'] !!}</span>
                </h3>
            </div>
            <div class="middle">
                <h3 style="text-align:center">
                    عقد تركيب مصعد
                </h3>
            </div>

        </div>


        <p>{!! $contractText !!}</p>

        <div>
            <div class="right-signature">
                <p> {!! __('pdf.First Part') !!} </p>
                <p> {!! __('pdf.Institute Name') !!} </p>
                <p> {!! __('pdf.Representative') !!} <span>.................................... </span></p>
                <p> {!! __('pdf.Date') !!} <span>....................................</span></p>
                <p> {!! __('pdf.Sig') !!} <span>....................................</span></p>
            </div>

            <div class="left-signature">
                <p> {!! __('pdf.Second Part') !!}</p>
                <p>{!! __('pdf.Name') !!} {!! $data['FIRST_NAME'] !!}</p>
                <p>{!! __('pdf.Phone') !!} {!! $data['PHONE'] !!}</p>
                <p>{!! __('pdf.Date') !!} <span>....................................</span></p>
                <p>{!! __('pdf.Sig') !!} <span>....................................</span></p>
            </div>
        </div>

    </div>
@endsection
