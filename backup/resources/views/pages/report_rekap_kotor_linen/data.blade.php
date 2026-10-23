<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP KOTOR BERDASARKAN JENIS LINEN</b>
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
				RUANGAN : {{ $ruangan->field_name ?? 'Semua Ruangan' }}
			</h3>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="10">
            <h3>
				Tanggal Kotor : {{ formatDate(request()->get('start_rekap')) }} - {{ formatDate(request()->get('end_rekap')) }}
			</h3>
		</td>
	</tr>
</table>
<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
            <tr>
                <th style="width: 200px" width="20">Nama Ruangan</th>
                <th style="width: 200px" width="20">Nama Linen</th>
				@foreach($tanggal as $tgl)
                <th>{{ formatDate($tgl, 'd') }}</th>
                @endforeach
				<th>Max</th>
				<th>Min</th>
				<th>Rata rata kotor</th>
				<th>Drop Stock</th>
				<th>Par Max</th>
				<th>Par Min</th>
            </tr>
        </thead>
		<tbody>
            @php
                $sum_kurang = $sum_lebih = $sum_per_linen = $sum_kotor = $sum_beda_rs = $sum_kg = $sum_lawan = 0;
                $total_number = $selisih = 0;
                $total_lawan = 0;
            @endphp

			@forelse ($location as $loc_id => $loc_name)
			<tr>
				<td rowspan="{{ count($linen) + 1 }}">{{ $loc_name }}</td>
			</tr>

				@forelse ($linen as $jenis_id => $jenis_name)
					<tr>
						<td>{{ $jenis_name }}</td>
						@foreach($tanggal as $tgl)
						@php
						$total_tanggal = $kotor
							->where('view_tanggal', $tgl->format('Y-m-d'))
							->where('view_ruangan_id', $loc_id)
							->where('view_linen_id', $jenis_id)
							;

						$average = $kotor->where('view_linen_id', $jenis_id)->avg('view_qty') ?? 0;
						$max = $kotor->where('view_linen_id', $jenis_id)->max('view_qty') ?? 0;
						$min = $kotor->where('view_linen_id', $jenis_id)->min('view_qty') ?? 0;
						$stock = $register->where('view_linen_id', $jenis_id)->count();

						$drop_max = (!empty($stock) && !empty($max)) ? round($stock / $max, 2) : 0;
						$drop_min = (!empty($stock) && !empty($min)) ? round($stock / $min, 2) : 0;

						@endphp

						<td>
							{{ $total_tanggal->sum('view_qty') }}
						</td>
						@endforeach
						<td>{{ $max }}</td>
						<td>{{ $min }}</td>
						<td>{{ round($average, 2) }}</td>
						<td>{{ $stock }}</td>
						<td>{{ $drop_max }}</td>
						<td>{{ $drop_min }}</td>
					</tr>
				@empty
				@endforelse

			@empty

			@endforelse


		</tbody>
	</table>
</div>