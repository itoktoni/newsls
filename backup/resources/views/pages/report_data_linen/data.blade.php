<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>MASTER DATA LINEN</b>
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
</table>

<br>

<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
			<tr>
				<th width="1">No. </th>
				<th>NO. RFID</th>
				<th>KATEGORI LINEN</th>
				<th>LINEN</th>
				<th>BERAT</th>
				<th>RUMAH SAKIT</th>
				<th>RUANGAN</th>
				<th>CUCI/RENTAL</th>
				<th>JUMLAH BERSIH</th>
				<th>JUMLAH REJECT</th>
				<th>JUMLAH REWASH</th>
				<th>POSISI TERAKHIR</th>
				<th>TGL POSISI TERAKHIR</th>
				<th>STATUS REGISTER</th>
				<th>TGL REGISTRASI</th>
				<th>OPERATOR REGISTRASI</th>
			</tr>
		</thead>
		<tbody>
			@php
			$total_berat = 0;
			@endphp

			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->field_primary }}</td>
				<td>{{ $table->view_kategori_nama }}</td>
				<td>{{ $table->field_name }}</td>
				<td>{{ $table->field_weight }}</td>
				<td>{{ $table->field_rs_name }}</td>
				<td>{{ $table->field_ruangan_name }}</td>
				<td>{{ $table->field_status_cuci_name }}</td>
				<td>{{ $table->field_bersih ?? 0 }}</td>
				<td>{{ $table->field_retur ?? 0 }}</td>
				<td>{{ $table->field_rewash ?? 0 }}</td>
				<td>{{ $table->field_status_process }}</td>
				<td>{{ formatDate($table->field_tanggal_update) }}</td>
				<td>{{ $table->field_status_register_name }}</td>
				<td>{{ formatDate($table->field_tanggal_create) }}</td>
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