<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				<b>REPORT PAR-STOK</b>
			</h3>
		</td>
		<td rowspan="3">
			<x-logo/>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				RUMAH SAKIT : {{ $rs->field_name ?? 'Semua Rumah Sakit' }}
			</h3>
		</td>
	</tr>
</table>

<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
			<tr>
				<th width="1">No. </th>
				<th>NAMA LINEN</th>
				<th>RUMAH SAKIT</th>
				<th>JUMLAH REGISTER</th>
				<th>BERAT (KG)</th>
				<th>TOTAL (KG)</th>
				<th>PAR-STOCK</th>
				<th>KURANG LEBIH</th>
			</tr>
		</thead>
		<tbody>
			@php
			$total_berat = 0;
			@endphp

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->jenis_nama }}</td>
				<td>{{ $table->field_rs_name }}</td>
				<td>{{ $table->qty }}</td>
				<td>{{ $table->jenis_berat }}</td>
				<td>{{ $table->qty * $table->jenis_berat }}</td>
				<td>{{ $table->jenis_parstok }}</td>
				<td>{{ $table->qty - $table->jenis_parstok }}</td>
			</tr>
			@empty
			@endforelse

		</tbody>
		<tfoot>
			<tr>
				<th colspan="3" style="text-align: right;">Total:</th>
				<th>
					@php
					$total_qty = $data->sum('qty');
					@endphp
					{{ $total_qty }}
				</th>
				<th></th>
				<th>
					@php
					$total_weight = $data->sum(function($item) {
						return $item->qty * $item->jenis_berat;
					});
					@endphp
					{{ $total_weight }}
				</th>
				<th>
					@php
					$total_parstock = $data->sum('jenis_parstok');
					@endphp
					{{ $total_parstock }}
				</th>
				<th>
					@php
					$total_difference = $data->sum(function($item) {
						return $item->qty - $item->jenis_parstok;
					});
					@endphp
					{{ $total_difference }}
				</th>
			</tr>
		</tfoot>
	</table>
</div>

<table class="footer">
	<tr>
		<td colspan="2" class="print-date">{{ env('APP_LOCATION') }}, {{ date('d F Y') }}</td>
	</tr>
	<tr>
		<td colspan="2" class="print-person">{{ auth()->user()->name ?? '' }}</td>
	</tr>
</table>