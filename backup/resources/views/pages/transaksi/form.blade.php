<x-layout>
    <x-card>
        <x-form :model="$model">

            @bind($model)

            <x-form-input col="6" name="transaksi_id" label="ID Unik" />
            <x-form-input col="6" name="transaksi_key" label="Transaksi ID" />
            <x-form-input col="6" name="rs_nama" />
            <x-form-input type="date" col="6" name="transaksi_report" value="{{ $model->field_transaksi_report ?? formatDateMySql($model->field_created_at) }}" label="Tanggal" />
            <x-form-input col="6" name="username" label="User" />
            <x-form-input col="6" name="status" value="{{ $model->field_status_transaction_name ?? '' }}"
                label="Status" />

            @endbind

        </x-form>
    </x-card>

    <x-card label="Data Transaksi">
        <div class="table-responsive" id="table_data">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>No. RFID</th>
                        <th>Nama Linen</th>
                        <th>Rumah Sakit</th>
                        <th class="text-center column-action">{{ __('Hapus') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $table)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $table->field_rfid }}</td>
                        <td>{{ $table->has_detail->field_name ?? '' }}</td>
                        <td>{{ $table->has_detail->field_rs_name ?? '' }}</td>
                        <td class="col-md-2 text-center column-action">
                            <div>
                                <x-button module="getDeleteTransaksi" key="{{ $table->field_primary }}" color="danger" icon="trash"  onclick="return confirm('Apakah anda yakin ingin menghapus ?')" class="button-delete" />
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layout>