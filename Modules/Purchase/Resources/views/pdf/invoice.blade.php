@extends('purchase::layouts.master')
@section('content')
    <div>
        <div class="left">
            <h3 style="text-align:left">
                <span> {!! __('pdf.RFQ Number') !!} </span>
                {{ $data['RfqNumber'] }}
            </h3>
        </div>

        <div class="right">
            <h3 style="text-align:right">
                <span> {!! __('pdf.Date') !!} </span>
                {{ $data['Date'] }}
            </h3>
        </div>
        <div class="middle">
            <h2 style="text-align:center">
                <span> {!! __('pdf.RFQ') !!}</span>
            </h2>
        </div>
    </div>

    <h2>
        <span>{!! __('pdf.Supplier Name') !!}</span>
        <span>{{ $data['SupplierName'] }}</span>
    </h2>
    <h2 style="text-align: center"> {!! __('pdf.Greeting') !!} </h2>
    <h3 style="padding-top:10px">{!! __('pdf.Content') !!}</h3>

    <table style="margin-top:30px;width:670px" class="pdf table table-bordered table-striped">
        <thead>
            <tr style="background-color:#20536b">
                <td><span style="color:white">#</span> </td>
                <td><span style="color:white">المواد</span></td>
                <td><span style="color:white">الكميات</span> </td>
            </tr>
        </thead>

        <tbody>

            @foreach ($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->product->name }}</td>
                    <td>{{ $product->quantity }}</td>

                </tr>
            @endforeach
        </tbody>

        </thead>
    </table>
@endsection
