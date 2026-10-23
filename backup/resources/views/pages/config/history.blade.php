<x-layout>
    <x-card>
        <x-form :model="$model">

            @bind($model)

            <x-form-select col="6" class="search" label="Rumah sakit" name="detail_id_rs" :options="$rs" />
            <x-form-select col="6" class="search" name="detail_id_ruangan" :options="$ruangan" />
            <x-form-select col="6" class="search" name="detail_id_jenis" :options="$jenis" />

            <div class="form-group col-md-6 ">
                <label>RFID</label>
                <input type="text" {{ $model ? 'readonly' : '' }} class="form-control" value="{{ old('detail_rfid') ?? $model->detail_rfid ?? null }}" name="detail_rfid">
            </div>

            @endbind

        </x-form>
    </x-card>

    <x-card label="History Linen">

        <div class="table-responsive" id="table_data">
            <table class="table table-bordered table-striped overflow max-content">
                <thead>
                    <tr>
                        <th>Rumah Sakit</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $table)
                        <tr>
                            <td>{{ $table->rs_nama}}</td>
                            <td>{{ $table->field_created_at }}</td>
                            <td>{{ $table->field_created_by }}</td>
                            <td>{{ $table->field_created_by }}</td>
                            <td>{{ $table->field_status }}</td>
                            <td>{{ $table->field_description }}</td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>

    </x-card>
</x-layout>
