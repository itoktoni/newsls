<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)

            <x-form-select col="6" class="search" name="opname_id_rs" label="Rumah Sakit" :options="$rs" />
            <x-form-input col="3" type="date" label="Tanggal Mulai" name="opname_mulai" />
            <x-form-input col="3" type="date" label="Tanggal Selesai" name="opname_selesai" />
            <x-form-select col="6" class="search" name="opname_status" label="Status" :options="$status" />
            <x-form-textarea col="6" rows="4" label="Keterangan" name="opname_nama" />

            @endbind

        </x-form>
    </x-card>

    @if (!empty($detail))
    <x-card label="Data Opname">
        <div class="table-responsive" id="table_data">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px;">No.</th>
                        <th style="width: 100px;">No. RFID</th>
                        <th>Jenis Linen</th>
                        <th>Ruangan</th>
                        <th>Pemakaian</th>
                        <th>Tgl Terakhir</th>
                        <th>Transaksi</th>
                        <th>Proses</th>
                        <th>Scan Opname</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detail as $table)
                    @php
                    $view = $table->has_view ?? false;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $table->field_rfid }}</td>
                        <td>{{ $view->field_name ?? '' }}</td>
                        <td>{{ $view->field_ruangan_name ?? '' }}</td>
                        <td>{{ $view->field_bersih ?? '0' }}</td>
                        <td>{{ $view ? formatDate($view->field_tanggal_update) : '' }} </td>
                        <td>{{ $table->opname_detail_transaksi ?? '' }}</td>
                        <td>{{ $table->opname_detail_proses ?? 'Belum Register' }} </td>
                        <td>{{ $table->field_ketemu == 1 ? 'Sudah Opname' : '-' }}</td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination :data="$detail" />
    </x-card>
    @endif


</x-layout>
