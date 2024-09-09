<div class="container" style="direction: rtl" id="container">
    <div>
        <div class="left">
            <h3 style="text-align:left">
                <span> رقم العقد : </span>
                {{ $data['contract_number'] }}
            </h3>
        </div>

        <div class="right">
            <h3 style="text-align:right">
                <span> التاريخ : </span>
                {{ $data['DATE'] }}
            </h3>
        </div>
        <div class="middle">
            <h3 style="text-align:center">
                صرف بضاعة {{ $data['stage'] }}
            </h3>
        </div>

    </div>

    <table style="margin-bottom:30px;width:670px" class="pdf table table-bordered table-striped">
        <tbody>
            <tr style="background-color:#20536b">
                <td>
                    <span style="color:white">
                        {!! __('pdf.Client Name') !!}
                    </span>
                    <span style="color:white">
                        {{ $data['name'] }}
                    </span>
                </td>
                <td>
                    <span style="color:white">
                        {!! __('pdf.Client Phone') !!}
                    </span>
                    <span style="color:white">{{ $data['phone'] }} </span>
                </td>
                <td>
                    <span style="color:white">
                        {!! __('pdf.Elevator Trip') !!} :
                    </span>
                    <span style="color:white">{{ $data['ELEVATOR_TRIP'] }} </span>
                </td>
            </tr>
            <tr>
                <td>
                    <span>{!! __('pdf.Tech Name') !!}</span>
                    <span>
                        @foreach ($data['TECHNICIANS'] as $tech)
                            {{ $tech->employee['name'] . '-' }}
                        @endforeach
                    </span>
                </td>
                <td>
                    <span>جوال الفني </span>
                    <span>{{ $data['phone'] }} </span>
                </td>

                <td>
                    <span> {!! __('pdf.Stopping Number') !!} </span>
                    <span>{{ $data['STOPS_NUMBERS'] }} </span>
                </td>
            </tr>

            <tr style="background-color:#20536b">
                <td>
                    <span style="color:white"> اتجاه فتح الباب الخارجي </span>
                    <span style="color:#FF0">{{ $data['DOORDIRECTIONS'] . ' - ' }} </span>
                    <span style="color:#FF0">{{ $data['DOOR_SIZE'] }} </span>
                </td>
                <td>
                    <span style="color:white"> نوع المصعد </span>
                    <span style="color:#FF0">{{ $data['ELEVATOR_TYPE'] }}</span>
                </td>

                <td>
                    <span style="color:white"> عدد المداخل </span>
                    <span style="color:#FF0">{{ $data['ENTRANCES_NUMBER'] }}</span>
                </td>
            </tr>

        </tbody>
    </table>

</div>
