<table border="0" class="header">
	<tr>
		<td></td>
		<td colspan="6">
			<h3>
				<b>{{ strtoupper(moduleName()) }}
				@if($filter = request()->get('status'))
				 {{ Str::upper(FilterType::getDescription($filter)) }}
				@endif
				: {{ $opname->field_primary ?? '' }} </b>
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
				<th>NO. RFID</th>
				<th>LINEN </th>
				<th>RUMAH SAKIT</th>
				<th>RUANGAN</th>
				<th>TANGGAL BUAT OPNAME</th>
				<th>SUDAH DI OPNAME</th>
				<th>STATUS TRANSAKSI</th>
				<th>STATUS LINEN</th>
				<th>CUCI/RENTAL</th>
				<th>JUMLAH PEMAKAIAN LINEN</th>
				@if($filter == FilterType::Reject)
				<th>JUMLAH RETUR</th>
				@elseif($filter == FilterType::Rewash)
				<th>JUMLAH REWASH</th>
				@endif
				<th>TANGGAL REGISTER</th>
				<th>OPERATOR</th>
			</tr>
		</thead>
		<tbody>
			@forelse($data as $table)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $table->opname_detail_rfid }}</td>
				<td>{{ $table->opname_detail_transaksi ? $table->view_linen_nama : '' }}</td>
				<td>{{ $table->opname_detail_transaksi ? $table->view_rs_nama : '' }}</td>
				<td>{{ $table->opname_detail_transaksi ? $table->view_ruangan_nama : '' }}</td>
				<td>{{ formatDate($table->opname_detail_created_at) }}</td>
				<td>{{ $table->opname_detail_ketemu == 1 ? formatDate($table->opname_detail_waktu) : '' }}</td>
				<td>{{ $table->opname_detail_transaksi ? TransactionType::getDescription($table->opname_detail_transaksi) : 'Belum Register' }}</td>
				<td>{{ $table->opname_detail_proses ? $table->opname_detail_proses : 'Belum Register' }}</td>
				<td>{{ $table->opname_detail_transaksi ? CuciType::getDescription($table->view_status_cuci) : '' }}</td>
				<td>{{ $table->view_transaksi_bersih_total ?? 0 }}</td>
				@if($filter == FilterType::Reject)
				<td>{{ $table->view_transaksi_retur_total ?? 0 }}</td>
				@elseif($filter == FilterType::Rewash)
				<td>{{ $table->view_transaksi_rewash_total ?? 0 }}</td>
				@endif
				<td>{{ $table->opname_detail_transaksi ? formatDate($table->view_tanggal_create) : '' }}</td>
				<td>{{ $table->name }}</td>
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