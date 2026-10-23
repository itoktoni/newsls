<x-layout>
    <x-card :label="$model->field_name">
        <x-form :model="$model" action="{{ moduleRoute('postJenis', $model->field_primary) }}">
            <x-action form="form" />

            @bind($model)

            <div class="container">
                <table class="table table-bordered table-responsive">
                    <tr>
                        <th>No.</th>
                        <th>Jenis</th>
                        <th>Parstok</th>
                        <th>Total Linen</th>
                        <th>Kekurangan</th>
                    </tr>
                    @foreach ($jenis as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->field_name }}</td>
                            <td>
                                <input type="hidden" name="jenis[{{ $loop->index }}][rs_id]" value="{{ $code }}">
                                <input type="hidden" name="jenis[{{ $loop->index }}][jenis_id]" value="{{ $item->field_primary }}">
                                <input class="form-control" style="width: 100%" type="text" name="jenis[{{ $loop->index }}][parstock]" value="{{ $item->pivot->parstock }}">
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                </table>
            </div>

        </x-form>
    </x-card>
</x-layout>
