<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>DETAIL REGISTER LINEN</b>
			</h3>
		</td>
		<td rowspan="3">
			<x-logo/>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				RUMAH SAKIT : {{ $rs->field_name ?? 'Semua Rumah Sakit' }}
			</h3>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="10">
			<h3>
				Periode : {{ formatDate(request()->get('start_date')) }} - {{ formatDate(request()->get('end_date')) }}
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
				<th>NO. RFID</th>
				<th>LINEN</th>
				<th>RUMAH SAKIT</th>
				<th>RUANGAN</th>
				<th>CUCI/RENTAL</th>
				<th>STATUS REGISTRASI</th>
				<th>TANGGAL REGISTER</th>
				<th>OPERATOR</th>
			</tr>
		</thead>
		<tbody>
			@php
			$total_berat = 0;
			@endphp

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->item_linen_rfid }}</td>
				<td>{{ $table->item_linen_product_name }}</td>
				<td>{{ $table->item_linen_company_name }}</td>
				<td>{{ $table->item_linen_location_name }}</td>
				<td>{{ ObsesimanType::getDescription($table->item_linen_rent) }}</td>
				<td>{{ ObsesimanType::getDescription($table->item_linen_status) }}</td>
				<td>{{ formatDate($table->item_linen_created_at) }}</td>
				<td>{{ $table->item_linen_created_name }}</td>
			</tr>
			@empty
			@endforelse

		</tbody>
		<tfoot>
			<tr>
				<th colspan="9" style="text-align: center;">
					Total RFID: {{ $data->count() }}
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