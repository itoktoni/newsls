<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>DETAIL PENGIRIMAN LINEN RETUR </b>
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
				Periode : {{ formatDate(request()->get('start_delivery')) }} - {{ formatDate(request()->get('end_delivery')) }}
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
				<th>NO. DO</th>
				<th>NO. BARCODE</th>
				<th>NO. RFID</th>
				<th>LINEN </th>
				<th>RUMAH SAKIT</th>
				<th>RUANGAN</th>
				<th>TANGGAL DO BERSIH</th>
				<th>OPERATOR</th>
			</tr>
		</thead>
		<tbody>
			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->field_delivery }}</td>
				<td>{{ $table->field_barcode }}</td>
				<td>{{ $table->bersih_rfid }}</td>
				<td>{{ $table->jenis_nama }}</td>
				<td>{{ $table->rs_nama }}</td>
				<td>{{ $table->ruangan_nama }}</td>
				<td>{{ formatDate($table->bersih_report) }}</td>
				<td>{{ $table->name ?? '' }}</td>
			</tr>
			@empty
			@endforelse

		</tbody>
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