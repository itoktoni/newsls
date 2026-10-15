<x-layout>

    <x-card>

        <x-form method="GET" action="{{ moduleRoute('getTable') }}">

            @livewire('dropdown-rs', ['label' => false])

            <div class="container-fluid">
                <div class="row">
                    <x-form-select prepend="Scan RS" col="4" :label=false name="rs_id" :options="$rs" />
                    <x-form-select prepend="Status" col="4" :label=false name="status" :options="$status" />
                    <x-form-select prepend="Operator" col="4" :label=false name="transaksi_created_by" :options="$user" />
                </div>
                <div class="row">
                    <x-form-input prepend="No. Transaksi" :label=false col="4" name="transaksi_key" />
                    <x-form-input prepend="No. DO" :label=false col="4" name="transaksi_delivery" />
                    <x-form-input prepend="No. Barcode" :label=false col="4" name="transaksi_barcode" />
                </div>
                <div class="row">
                    <x-form-input prepend="No. RFID" :label=false col="4" name="transaksi_rfid" />
                    <x-form-input type="date" prepend="Tanggal Awal" :label=false col="4" name="start_date" />
                    <x-form-input type="date" prepend="Tanggal Akhir" :label=false col="4" name="end_date" />
                </div>
            </div>

            <x-filter toggle="Filter" hide="false" :fields="$fields" />
    </x-form>

        <x-form method="POST" action="{{ moduleRoute('getTable') }}">

            <x-action/>

            <div class="container-fluid">
                <div class="table-responsive" id="table_data">
                    <table class="table table-bordered table-striped overflow max-content">
                        <thead>
                            <tr>
                                <th width="9" class="center">
                                    <input class="btn-check-d" type="checkbox">
                                </th>
                                <th class="text-center column-checkbox">{{ __('No.') }}</th>
                                <th>NO. DELIVERY</th>
                                <th>NO. PACKING</th>
                                <th>NO. RFID</th>
                                <th>TANGGAL</th>
                                <th>LINEN </th>
                                <th>RUMAH SAKIT</th>
                                <th>RUANGAN</th>
                                <th>STATUS TRANSAKSI</th>
                                <th>OPERATOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $key => $table)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox" name="code[]"
                                            value="{{ $table->field_primary }}">
                                    </td>
                                    <td>{{ iteration($data, $key) }}</td>
                                    <td>{{ $table->field_delivery }}</td>
                                    <td>{{ $table->field_barcode }}</td>
                                    <td>{{ $table->field_rfid }}</td>
                                    <td>{{ $table->field_report }}</td>
                                    <td>{{ $table->jenis_nama }}</td>
                                    <td>{{ $table->rs_nama }}</td>
                                    <td>{{ $table->ruangan_nama }}</td>
                                    <td>{{ $table->bersih_status }}</td>
                                    <td>{{ $table->name }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :data="$data" />
            </div>

        </x-form>

    </x-card>

</x-layout>