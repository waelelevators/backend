<table style="margin-bottom:30px;width:670px" class="pdf table-bordered table-striped table">
				<thead>
								<tr style="background-color:#20536b">
												<td><span style="color:white">#</span></td>
												<td><span style="color:white">المواد</span></td>
												<td><span style="color:white">الكمية</span></td>
												<td><span style="color:white">السعر</span></td>

								</tr>
				</thead>
				<tbody>
								@foreach ($products as $index => $product)
												<tr>
																<td>{{ $index + 1 }}</td>
																<td>{{ $product->product->name }}</td>
																<td>{{ $product->quantity }}</td>
																<td>{{ $product->price }}</td>

												</tr>
								@endforeach

				</tbody>
</table>
