@extends('maintenance::layouts.master')
@section('content')
				@php

								$contractText = str_replace('PHONE', $data['PHONE'], $template); // رقم الجوال
								$contractText = str_replace('FIRST_NAME', $data['FIRST_NAME'], $contractText); // اسم العميل
								$contractText = str_replace('ADDRESS', $data['ADDRESS'], $contractText); //اسم المدينة
								$contractText = str_replace('DATE', $data['DATE'], $contractText);

								$contractText = str_replace('ELEVATOR_TYPE', $data['ELEVATOR_TYPE'], $contractText);
								$contractText = str_replace('MACHINE_TYPE', $data['MACHINE_TYPE'], $contractText);
								$contractText = str_replace('MACHINE_SPEED', $data['MACHINE_SPEED'], $contractText);
								$contractText = str_replace('CONTROL_CARD', $data['CONTROL_CARD'], $contractText);
								$contractText = str_replace('CARD_NUMBER', $data['CARD_NUMBER'], $contractText);

								$contractText = str_replace('CONTRACT_NUMBER', $data['CONTRACT_NUMBER'], $contractText);
								$contractText = str_replace('VISIT_NUMBERS', $data['VISIT_NUMBERS'], $contractText);

								$contractText = str_replace('PAYMENT', $data['PAYMENT'], $contractText);

				@endphp


				<div class="container" style="direction: rtl" id="container">

								<div>
												<div class="left">
																<h3 style="text-align:center">
																				التاريخ :
																				<span>{!! $data['DATE'] !!}</span>
																</h3>
												</div>

												<div class="right">
																<h3 style="text-align:right">
																				الرقم الضريبي :
																				<span> {!! __('pdf.Tax Number') !!} </span>

																</h3>
												</div>

												<div class="middle">
																<h2 style="text-align:center">
																				عقد صيانة
																</h2>
												</div>

								</div>


								<p>{!! $contractText !!}</p>



				</div>
@endsection
