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
                                <th class="text-center column-action">{{ __('Action') }}</th>
                                <th class="text-center column-checkbox">{{ __('No.') }}</th>
                                <th>NO. TRANSAKSI</th>
                                <th>NO. RFID</th>
                                <th>TANGGAL</th>
                                <th>LINEN </th>
                                <th>RUMAH SAKIT</th>
                                <th>RUANGAN</th>
                                <th>LOKASI SCAN RS</th>
                                <th>STATUS TRANSAKSI</th>
                                <th>STATUS PROSES</th>
                                <th>PENDING</th>
                                <th>HILANG</th>
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
                                    <td class="col-md-2 text-center column-action">
                                        <x-crud :model="$table" />
                                    </td>
                                    <td>{{ iteration($data, $key) }}</td>
                                    <td>{{ $table->field_key }}</td>
                                    <td>{{ $table->field_primary }}</td>
                                    <td>{{ $table->field_created_at }}</td>
                                    <td>{{ $table->field_linen_name }}</td>
                                    <td>{{ $table->field_rs_ori_name }}</td>
                                    <td>{{ $table->field_location_name }}</td>
                                    <td>{{ $table->field_rs_scan_name }}</td>
                                    <td>{{ $table->field_status_transaction }}</td>
                                    <td>{{ $table->field_status_process }}</td>
                                    <td>{{ $table->field_pending_at }}</td>
                                    <td>{{ $table->field_hilang_at }}</td>
                                    <td>{{ $table->field_operator }}</td>
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