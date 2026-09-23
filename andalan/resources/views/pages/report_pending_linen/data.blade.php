<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP PENDING LINEN</b>
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
				Periode : {{ formatDate(request()->get('start_pending')) }} - {{ formatDate(request()->get('end_pending')) }}
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
				<th>JUMLAH PEMAKAIAN LINEN</th>
				<th>TRANSAKSI</th>
				<th>TANGGAL KOTOR</th>
				<th>TANGGAL BERSIH</th>
			</tr>
		</thead>
		<tbody>
			@php
			$total_berat = 0;
			@endphp

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->pending_rfid }}</td>
				<td>{{ $table->jenis_nama }}</td>
				<td>{{ $table->rs_nama }}</td>
				<td>{{ $table->ruangan_nama }}</td>
				<td class="text-right">{{ $table->view_transaksi_bersih_total ?? 0 }}</td>
				<td>{{ $table->pending_transaksi }}</td>
				<td>{{ formatDate($table->pending_kotor_at) }}</td>
				<td>{{ formatDate($table->pending_bersih_at) }}</td>
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