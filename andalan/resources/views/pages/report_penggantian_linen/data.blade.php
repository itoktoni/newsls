<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>DETAIL PENGGANTIAN TAG LINEN</b>
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
				<th>LINEN LAMA</th>
				<th>LINEN BARU</th>
				<th>JENIS LINEN</th>
				<th>RUMAH SAKIT</th>
				<th>RUANGAN</th>
				<th>CUCI/RENTAL</th>
				<th>STATUS REGISTRASI</th>
				<th>TANGGAL PENGGANTIAN</th>
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
				<td>{{ $table->detail_lama }}</td>
				<td>{{ $table->field_primary }}</td>
				<td>{{ $table->field_name }}</td>
				<td>{{ $table->field_rs_name }}</td>
				<td>{{ $table->field_ruangan_name }}</td>
				<td>{{ $table->field_status_cuci }}</td>
				<td>{{ $table->field_status_register }}</td>
				<td>{{ formatDate($table->detail_pengantian_waktu) }}</td>
				<td>{{ $table->field_created_name }}</td>
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