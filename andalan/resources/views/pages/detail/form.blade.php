<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)

            <x-form-select col="6" class="search" label="Rumah sakit" name="detail_id_rs" :options="$rs" />
            <x-form-select col="6" class="search" name="detail_id_ruangan" :options="$ruangan" />
            <x-form-select col="6" class="search" name="detail_id_jenis" :options="$jenis" />
            <x-form-select col="6" class="search" name="detail_id_bahan" :options="$bahan" />
            <x-form-select col="6" class="search" name="detail_id_supplier" :options="$supplier" />
            <x-form-select col="6" class="search" name="detail_status_cuci" :options="$cuci" />

            <div class="form-group col-md-6 ">
                <label>RFID Baru</label>
                <input type="text" class="form-control" value="{{ old('detail_rfid') ?? $model->detail_rfid ?? null }}" name="detail_rfid">
            </div>
            <div class="form-group col-md-6 ">
                <label>RFID Lama</label>
                <input type="text" class="form-control" value="{{ old('rfid_lama') ?? null }}" name="rfid_lama">
            </div>

            @endbind

        </x-form>
    </x-card>
</x-layout>
