@extends('layouts.master')

@section('content')
    @include('exchange-product.client')
    <h3> المكينة </h3>
    <table style="margin-bottom:30px;width:670px" class="pdf table table-bordered table-striped">
        <tbody>
            <tr>
                <td><span> حمولة الاشخاص </span></td>
                <td><span>{{ $data['PEOPLE_LOAD'] }}</td>
                <td><span> سكك الثقل </span> </td>
                <td><span>{{ $data['WEIGHT_RAILS'] }}</td>
                <td><span> سكك الثقل </span> </td>
                <td><span>{{ $data['WEIGHT_RAILS'] }}</td>
            </tr>

            <tr style="background-color:#20536b">
                <td><span style="color:white">نوع الماكينة </span> </td>
                <td><span style="color:white">{{ $data['MACHINE_TYPE'] ?? '' }}</span></td>
                <td><span style="color:white"> سرعة الماكينة </span></td>
                <td><span style="color:white">{{ $data['MACHINE_SPEED'] ?? '' }}</span></td>
                <td><span style="color:white"> حمولة الماكينة </span></td>
                <td><span style="color:white">{{ $data['MACHINE_LOAD'] ?? '' }}</span></td>
            </tr>

        </tbody>
    </table>

    <h3> بضاعة المرحلة الثانية </h3>

    @include('exchange-product.products')
@endsection
