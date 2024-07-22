@extends('layouts.master')

@section('content')
    @include('exchange-product.client')

    {{-- <h3>مواصفات الباب الخارجي</h3>

    <table style="margin-bottom:30px;width:670px" class="pdf table table-bordered table-striped">
        <thead>
            <tr style="background-color:#20536b">
                <td><span style="color:white">#</span> </td>
                <td><span style="color:white">الطوابق</span> </td>
                <td><span style="color:white"> عدد الفتحات</span></td>
                <td><span style="color:white"> مواصفات الباب الاول </span></td>
                <td><span style="color:white">اتجاه فتحة الباب</span></td>
                <td><span style="color:white">مواصفات الباب الثاني </span></td>
                <td><span style="color:white">اتجاه فتحة الباب الثاني</span></td>
            </tr>
        </thead>

        <tbody>
            {{ $x = 1 }}
            @foreach ($data['DOOR_SPECIFICATIONS'] as $index => $door)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $door->floor_name['name'] ?? '' }}</td>
                    <td>{{ $door->number_of_doors ?? '' }}</td>
                    <td>{{ $door->door_specification['name'] ?? '' }}</td>
                    <td>{{ $door->opening_direction['name'] ?? '' }}</td>
                    <td>{{ $door->door_specification_tow['name'] ?? '' }}</td>
                    <td>{{ $door->opening_direction_tow['name'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>

        </thead>
    </table> --}}

    <div>
        <div class="right-signature">
            <img style="height:300px;width:100%" src="https://waelsoft.com/cms/images/back_dbg.svg" alt="Example Image">
        </div>
        <div class="left-signature" style="padding-top:7px">

            <table class="pdf table table-bordered">
                <thead>
                    <tr style="background-color:#20536b">
                        <td style="color:white">موقع الثقل </td>
                        <td style="background-color:white;width:90px"> </td>
                    </tr>

                </thead>

                <tbody>

                    <tr style="background-color:#20536b">
                        <td style="color:white">مقاس الباب </td>
                        <td style="background-color:white;width:90px"> </td>
                    </tr>

                    <tr style="background-color:#20536b">
                        <td style="color:white"> العمق </td>
                        <td style="background-color:white;width:90px"> </td>

                    </tr>

                    <tr style="background-color:#20536b">
                        <td style="color:white"> العرض </td>
                        <td style="background-color:white;width:90px"> </td>
                    </tr>

                    <tr style="background-color:#20536b">

                        <td style="color:white"> DBG الكبينة </td>
                        <td style="background-color:white;width:90px"> </td>
                    </tr>

                    <tr style="background-color:#20536b">

                        <td style="color:white"> DBG الثقل </td>
                        <td style="background-color:white;width:90px"> </td>
                    </tr>

                </tbody>

            </table>

        </div>
    </div>
    <h3> بضاعة المرحلة الاولى </h3>

    @include('exchange-product.products')
@endsection
