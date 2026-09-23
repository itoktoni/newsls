<table border="0" class="header">
    <tr>
        <td></td>
        <td colspan="6">
            <h3>
                <b>{{ strtoupper(moduleName()) }}</b>
            </h3>
        </td>
        <td rowspan="3">
            <x-logo />
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="10">
            <h3>
                OPNAME ID : {{ $opname->field_primary ?? '' }}
            </h3>
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="10">
            <h3>
                RUMAH SAKIT : {{ $opname->has_rs->field_name ?? 'Semua Rumah Sakit' }}
            </h3>
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="10">
            <h3>
                Periode : {{ formatDate($opname->field_start) }} - {{ formatDate($opname->field_end) }}
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
                <th>TANGGAL </th>
                <th>SCAN LINEN SAAT SO</th>
            </tr>
        </thead>
        <tbody>
			@php
			$map = [];
			if(!empty($data)){
				$map = $data->mapToGroups(function($item){
					return [formatDate($item->opname_detail_waktu) => $item];
				});
			}

            $grand_total = 0;
			@endphp
            @forelse($map as $key => $table)
			@php
			$tembak_so = $table
                        ->where('opname_detail_ketemu', 1)
                        ->where('opname_detail_transaksi', '!=', 0)
                        ->count();

			$not_register = $table->where('opname_detail_transaksi', BooleanType::NO)->count();
            $hilang_rs = $table->where('opname_detail_transaksi', TransactionType::BERSIH)
                        ->where('opname_detail_ketemu', 0)
                        ->count();

            $hilang_warehouse = $table->where('opname_detail_transaksi', '!=', TransactionType::BERSIH)
                        ->where('opname_detail_ketemu', 0)
                        ->count();

			$total = $tembak_so + $hilang_rs + $hilang_warehouse;
            $grand_total = $grand_total + $total;

			@endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $key ?? '' }}</td>
                <td>{{ $tembak_so }}</td>
            </tr>

            @empty
            @endforelse

			<tr>
				<td colspan="2">Total</td>
				@php
				$sub_tembak_so = $data
                    ->where('opname_detail_ketemu', 1)
                    ->where('opname_detail_transaksi', '!=', 0)
                    ->count();

				$sub_not_register = $data->where('opname_detail_transaksi', BooleanType::NO)->count();
				$sub_total = $data->count();

                $sub_hilang_rs = $data->where('opname_detail_transaksi', TransactionType::BERSIH)
                        ->where('opname_detail_ketemu', 0)
                        ->count();

                $sub_hilang_warehouse = $data->where('opname_detail_transaksi','!=', TransactionType::BERSIH)
                            ->where('opname_detail_ketemu', 0)
                            ->count();

				@endphp
				<td>{{ $sub_tembak_so }}</td>
			</tr>

        </tbody>
    </table>

    <br>

    <table id="export" border="1" style="border-collapse: collapse !important; border-spacing: 0 !important;"
        class="table table-bordered table-striped table-responsive-stack">

        <thead>
            <tr>
                <th>Total Register</th>
                <th>Total Scan Linen</th>
                <th>Total belum terbaca di Rs</th>
                <th>Total belum terbaca di Laundry</th>
                <th>Total Summary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $register }}</td>
                <td>{{ $sub_tembak_so }}</td>
                <td>{{ $sub_hilang_rs }}</td>
                <td>{{ $sub_hilang_warehouse }}</td>
                <td>{{ $grand_total }}</td>
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