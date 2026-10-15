<x-layout>
    <x-card>
        <x-form :model="$model">

            @bind($model)

            <x-form-input col="6" name="transaksi_id" label="ID Unik" />
            <x-form-input col="6" name="transaksi_delivery" label="Transaksi Delivery" />
            <x-form-input col="6" name="rs_nama" />
            <x-form-input col="6" name="transaksi_report" label="Tanggal" />
            <x-form-input col="6" name="username" label="User" />
            <x-form-input col="6" name="transaksi_barcode_at" label="Tgl & Jam Barcode" />

            @endbind

        </x-form>
    </x-card>

    <x-card label="Data Barcode">
        <div class="table-responsive" id="table_data">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No. RFID</th>
                        <th>No. Delivery</th>
                        <th>Nama Linen</th>
                        <th>Rumah Sakit</th>
                        <th>User</th>
                        <th class="text-center column-action">{{ __('Hapus') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $table)
                    <tr>
                        <td>{{ $table->field_rfid }}</td>
                        <td>{{ $table->field_delivery ?? '' }}</td>
                        <td>{{ $table->has_detail->field_name ?? '' }}</td>
                        <td>{{ $table->has_detail->field_rs_name ?? '' }}</td>
                        <td>{{ $table->has_created_barcode->field_name ?? '' }} </td>
                        <td class="col-md-2 text-center column-action">
                            <div>
                                <x-button module="getDeleteTransaksi" key="{{ $table->field_primary }}" color="danger" icon="trash3"  onclick="return confirm('Apakah anda yakin ingin menghapus ?')" class="button-delete" />
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