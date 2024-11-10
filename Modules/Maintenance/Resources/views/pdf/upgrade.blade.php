@extends('maintenance::layouts.master')
@section('content')
				@php

								$contractText = str_replace('PHONE', $data['PHONE'], $template); // رقم الجوال
								$contractText = str_replace('FIRST_NAME', $data['FIRST_NAME'], $contractText); // اسم العميل
								$contractText = str_replace('ADDRESS', $data['ADDRESS'], $contractText); //اسم المدينة
								$contractText = str_replace('DATE', $data['DATE'], $contractText);

								$contractText = str_replace('ELEVATOR_TYPE', $data['ELEVATOR_TYPE'], $contractText);

								$contractText = str_replace('CONTRACT_NUMBER', $data['CONTRACT_NUMBER'], $contractText);

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
																				تحديث مصعد
																</h2>
												</div>

								</div>

								@include('maintenance::pdf.products')


								<p>{!! $contractText !!}</p>



				</div>
@endsection
