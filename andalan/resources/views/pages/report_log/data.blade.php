<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REPORT Log Book </b>
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
				<th>RUMAH SAKIT</th>
				<th>NAMA LINEN</th>
				<th>RUANGAN</th>
				<th>SALDO AWAL</th>
				<th>MASUK</th>
				<th>KELUAR</th>
				<th>SALDO AKHIR</th>
			</tr>
		</thead>
		<tbody>
			@php
			$stok_awal = $kotor = $bersih = $plus = $minus = 0;
			@endphp

				@foreach ($tanggal as $tgl)
				@php
				$single 	= $data->where('log_tanggal', $tgl->format('Y-m-d'))->first();
				$stok_awal 	= $data->where('log_tanggal', $tgl->format('Y-m-d'))->where('log_user', 1)->count();
				$in 		= $data->where('log_tanggal', $tgl->format('Y-m-d'))->whereNotNull('log_in')->where('log_user', '!=', 1)->count();
				$out 		= $data->where('log_tanggal', $tgl->format('Y-m-d'))->whereNotNull('log_out')->where('log_user', '!=', 1)->count();
				$akhir 		= ($stok_awal + $in) - $out;
				@endphp
				<tr>
					<td>{{ $loop->iteration }}</td>
					<td>{{ formatDate($tgl) }}</td>
					<td>{{ $single->rs_nama ?? '' }}</td>
					<td>{{ $single->jenis_nama ?? '' }}</td>
					<td>{{ $single->ruangan_nama ?? '' }}</td>
					<td>{{ $stok_awal }}</td>
					<td>{{ $in }}</td>
					<td>{{ $out }}</td>
					<td>{{ $akhir }}</td>
				</tr>
				@endforeach

		</tbody>

	</table>
</div>
