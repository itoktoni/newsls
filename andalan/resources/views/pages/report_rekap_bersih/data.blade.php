<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP BERSIH </b>
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
				Tanggal Bersih : {{ formatDate(request()->get('start_rekap')) }} - {{ formatDate(request()->get('end_rekap')) }}
			</h3>
		</td>
	</tr>
</table>

<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
            <tr>
                <th style="width: 10px" width="1">No. </th>
                <th style="width: 200px" width="20">Nama Linen</th>
                @foreach($location as $loc_id => $loc_name)
                    <th>{{ $loc_name }}</th>
                @endforeach
                <th>Total Bersih (Pcs)</th>
                <th>(Kg) Bersih</th>
            </tr>
        </thead>
		<tbody>
            @php
                $sum_kurang = $sum_lebih = $sum_per_linen = $sum_kotor = $sum_beda_rs = $sum_kg = $sum_lawan = 0;
                $total_number = $selisih = 0;
                $total_lawan = 0;
            @endphp
            @forelse($linen as $jenis_id => $jenis_nama)
                @php
                $name = $jenis_nama;
                $total_number++;
                @endphp
                <tr>
                    <td>{{ $total_number }}</td>
                    <td>{{ $name }}</td>
                    @foreach($location as $loc_id => $loc_name)
                        <td>
                            @php
                            $total_ruangan = $bersih
                            ->where('view_ruangan_id', $loc_id)
                            ->where('view_linen_id', $jenis_id)
                            ->sum('view_qty');
                            @endphp
                            {{ $total_ruangan > 0 ? $total_ruangan : '0' }}
                        </td>
                    @endforeach
                    <td>
                        @php
                        $total_bersih = $bersih
                        ->where('view_linen_id', $jenis_id)
                        ->sum('view_qty');
                        @endphp
                        {{ $total_bersih > 0 ? $total_bersih : '0' }}
                    </td>
                    <td>
                        @php
                        $total_kg = $bersih
                        ->where('view_linen_id', $jenis_id)
                        ->sum('view_kg');
                        @endphp
                        {{ $total_kg > 0 ? $total_kg : '0' }}
                    </td>
                </tr>
			@empty
			@endforelse

		</tbody>
		<tfoot>
			<tr>
				<th colspan="2" style="text-align: right;">Total:</th>
				@foreach($location as $loc_id => $loc_name)
					<th>
						@php
						$total_location = $bersih
						->where('view_ruangan_id', $loc_id)
						->sum('view_qty');
						@endphp
						{{ $total_location > 0 ? $total_location : '0' }}
					</th>
				@endforeach
				<th>
					@php
					$total_all = $bersih->sum('view_qty');
					@endphp
					{{ $total_all > 0 ? $total_all : '0' }}
				</th>
				<th>
					@php
					$total_kg_all = $bersih->sum('view_kg');
					@endphp
					{{ $total_kg_all > 0 ? $total_kg_all : '0' }}
				</th>
			</tr>
		</tfoot>
	</table>
</div>