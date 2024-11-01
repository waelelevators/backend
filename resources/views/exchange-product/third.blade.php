@extends('layouts.master')
@section('content')
    @include('exchange-product.client')
    <h3> الكنترول </h3>
    <table style="margin-bottom:30px;width:670px" class="pdf table table-bordered table-striped">
        <tbody>
            <tr style="background-color:#20536b">
                <td><span style="color:white"> نوع الكرت </span> </td>
                <td><span style="color:white">{{ $data['CONTROLE_CARD'] ?? '' }}</td>
                <td><span style="color:white"> الباب الداخلي </span></td>
                <td><span style="color:white">{{ $data['INTERNAL_DOOR_TYPE'] ?? '' }}</td>
            </tr>


        </tbody>
    </table>

    <h3> بضاعة المرحلة الثالثة </h3>

    @include('exchange-product.products')
@endsection
