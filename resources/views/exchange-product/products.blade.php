<table style="margin-bottom:30px;width:670px" class="pdf table table-bordered table-striped">
    <thead>
        <tr style="background-color:#20536b">
            <td><span style="color:white">المواد</span></td>
            <td><span style="color:white">الكميات</span></td>
            <td><span style="color:white">المواد</span></td>
            <td><span style="color:white">الكميات</span></td>
            <td><span style="color:white">المواد</span></td>
            <td><span style="color:white">الكميات</span></td>
            <td><span style="color:white">المواد</span></td>
            <td><span style="color:white">الكميات</span></td>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $index => $product)
            @if ($index % 4 == 0)
                <tr>
            @endif
            <td>{{ $product->product->name }}</td>
            <td>{{ $product->qty }}</td>
            @if (($index + 1) % 4 == 0)
                </tr>
            @endif
        @endforeach
        @if (count($products) % 4 != 0)
            </tr>
        @endif
    </tbody>
</table>

<p style="color:red">
    {{ $data['elevator_trip_id'] != $data['stop_number_id'] ? 'هنالك اختلاف بين عدد الادوار ومشوار المصعد يجب ان ياخذ في الاعتبار عند صرف البضاعة' : '' }}
</p>
