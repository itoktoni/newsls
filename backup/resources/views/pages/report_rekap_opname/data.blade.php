<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>REKAP OPNAME</b>
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
				Periode : {{ $data->opname_mulai }} - {{ $data->opname_selesai }}
			</h3>
		</td>
	</tr>
</table>

<div class="table-responsive" id="table_data">
	<table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
		class="table table-bordered table-striped table-responsive-stack">
		<thead>
            <tr>
                <th rowspan="2" style="width: 10px" width="1">No. </th>
                <th rowspan="2" style="width: 200px" width="20">Nama Linen</th>
                @foreach($location as $loc_id => $loc_name)
                    <th colspan="2" class="text-center">{{ $loc_name }}</th>
                @endforeach
                <th colspan="2" class="text-center">Total</th>
                <th colspan="2" class="text-center">Selisih</th>
            </tr>
            <tr>
                @foreach($location as $loc_id => $loc_name)
                    <th>SA</th>
                    <th>SO</th>
                @endforeach
                <th>SA</th>
                <th>SO</th>
                <th>-</th>
                <th>+</th>
            </tr>
        </thead>
		<tbody>
            @php
                $total_number = $total_minus = $total_plus = 0;
            @endphp
            @forelse($linen as $linen_id => $name)
                @if(!empty($name))
                @php
                $total_number++;
                $total_register = $opname->where('jenis_id', $linen_id)
                                    ->sum('total_register');
                $total_opname = $opname->where('jenis_id', $linen_id)
                                    ->sum('total_opname');
                $selisih = $total_opname - $total_register;
                $plus = $minus = 0;

                if($selisih >= 0){
                    $plus = $selisih;
                }
                else {
                    $minus = $selisih;
                }

                $total_plus = $total_plus + $plus;
                $total_minus = $total_minus + $minus;

                @endphp
                <tr>
                    <td>{{ $total_number }}</td>
                    <td>{{ $name ?? 'Belum teregister' }}</td>
                    @foreach($location as $loc_id => $loc_name)
                    @php
                    $sa = $opname->where('ruangan_id', $loc_id)
                                    ->where('jenis_id', $linen_id)
                                    ->sum('total_register');

                    $so = $opname->where('ruangan_id', $loc_id)
                                    ->where('jenis_id', $linen_id)
                                    ->sum('total_opname');
                    @endphp
                    <td>
                        {{ $sa != 0 ? $sa : '' }}
                    </td>
                    <td>
                        {{ $so != 0 ? $so : '' }}
                    </td>
                    @endforeach
                    <td>{{ $total_register }}</td>
                    <td>{{ $total_opname }}</td>
                    <td>{{ $minus }}</td>
                    <td>{{ $plus }}</td>
                </tr>
                @endif
			@empty
			@endforelse
            <tr>
                <td colspan="2">Total</td>
                @foreach($location as $loc_id => $loc_name)
                @php
                $sum_register = $opname->where('ruangan_id', $loc_id)
                                    ->sum('total_register');
                $sum_opname = $opname->where('ruangan_id', $loc_id)
                                    ->sum('total_opname');
                @endphp

                <td>{{ $sum_register }}</td>
                <td>{{ $sum_opname }}</td>
                @endforeach

                @php
                $grand_register = $opname->sum('total_register');
                $grand_opname = $opname->sum('total_opname');
                @endphp

                <td>{{ $grand_register }}</td>
                <td>{{ $grand_opname }}</td>

                <td>{{ $total_minus }}</td>
                <td>{{ $total_plus }}</td>
            </tr>
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