<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)

            <x-form-select col="6" class="search" label="Rumah sakit" name="detail_id_rs" :options="$rs" />
            <x-form-select col="6" class="search" name="detail_id_ruangan" :options="$ruangan" />
            <x-form-select col="6" class="search" name="detail_id_jenis" :options="$jenis" />
            <x-form-select col="6" class="search" name="detail_status_cuci" :options="$cuci" />
            <x-form-select col="6" class="search" name="detail_status_transaksi" :options="$transaction" />
            <x-form-select col="6" class="search" name="detail_status_proses" :options="$process" />

            <div class="form-group col-md-6 ">
                <label>RFID</label>
                <input type="text" {{ $model ? 'readonly' : '' }} class="form-control" value="{{ old('detail_rfid') ?? $model->detail_rfid ?? null }}" name="detail_rfid">
            </div>

            <x-form-textarea col="6" name="detail_deskripsi" />

            @endbind

        </x-form>
    </x-card>
</x-layout>
