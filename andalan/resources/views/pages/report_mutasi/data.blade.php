<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REPORT MUTASI </b>
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
				<th>TANGGAL</th>
				<th>NAMA LINEN</th>
				<th>RUMAH SAKIT</th>
				<th>STOCK AWAL REGISTER</th>
				<th>SALDO AWAL</th>
				<th>KOTOR</th>
				<th>BERSIH</th>
				<th>SELISIH (-)</th>
				<th>SELISIH (+)</th>
				<th>SALDO AKHIR</th>
			</tr>
		</thead>
		<tbody>
			@php
			$stok_awal_register = $kotor = $bersih = $plus = $minus = 0;
			@endphp
			@forelse($data->sortBy('field_linen_nama') as $table)
			@php
			$stok_awal_register = $table->field_register;
			$kotor = $kotor + $table->field_kotor;
			$bersih = $bersih + $table->field_bersih;
			$plus = $plus + $table->field_plus;
			$minus = $minus + $table->field_minus;
			@endphp
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ formatDate($table->field_tanggal) }}</td>
				<td>{{ $table->field_linen_nama }}</td>
				<td>{{ $table->field_rs_nama }}</td>
				<td>{{ $table->field_register }}</td>
				<td>{{ $table->field_saldo_awal }}</td>
				<td>{{ $table->field_kotor }}</td>
				<td>{{ $table->field_bersih }}</td>
				<td>{{ $table->field_minus }}</td>
				<td>{{ $table->field_plus }}</td>
				<td>{{ $table->field_saldo_akhir }}</td>
			</tr>
			@empty
			@endforelse
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">Resume</td>
				<td>{{ $stok_awal_register }}</td>
				<td></td>
				<td>{{ $kotor }}</td>
				<td>{{ $bersih }}</td>
				<td>{{ $minus }}</td>
				<td>{{ $plus }}</td>
				<td>{{ $stok_awal_register - ($bersih + $kotor) }}</td>
			</tr>
		</tfoot>
	</table>
</div>
