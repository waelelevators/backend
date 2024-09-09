<table style="margin-bottom:30px;width:670px" class="pdf table table-bordered table-striped">
    <thead>
        <tr style="background-color:#20536b">
            <td><span style="color:white">#</span></td>
            <td><span style="color:white">المواد</span></td>
            <td><span style="color:white">الاجمالي</span></td>
            <td><span style="color:white">المستلمة</span></td>

        </tr>
    </thead>
    <tbody>
        @foreach ($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->product->name }}</td>
                <td>{{ $product->contractProductItems->qty }}</td>
                <td>{{ $product->qty }}</td>

            </tr>
        @endforeach

    </tbody>
</table>

<p style="color:red">
    {{ $data['elevator_trip_id'] != $data['stop_number_id'] ? 'هنالك اختلاف بين عدد الادوار ومشوار المصعد يجب ان ياخذ في الاعتبار عند صرف البضاعة' : '' }}
</p>
