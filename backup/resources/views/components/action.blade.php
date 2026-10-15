@props([
    'label' => null,
    'model' => null,
    'form' => null,
    'override' => false
    ])

    @php
        $attributes = $attributes->class([
        'button button-action',
        ])->merge([
        //
        ]);

        if($form == null){
        $form = 'table';
        }
    @endphp

    @section('action')
    <div {{ $attributes }}>
        @if($form == 'table')
            <input class="btn-check-m d-lg-none form-check-input" type="checkbox">
            @can(ACTION_EMPTY)
            <x-button onclick="return confirm('Apakah anda yakin ingin menghapus ?')" name="delete" type="submit" color="danger" label="Kosongkan"/>
            @endcan
            @can(ACTION_CREATE)
            <x-button :module="ACTION_CREATE" color="success" label="Buat" />
            @endcan
        @elseif($form == 'print')
            @can(ACTION_PRINT)
            <x-button name="type" value="report" type="submit" label="Print" />
            @endcan
        @elseif($form == 'form')
            @can(ACTION_CREATE || ACTION_UPDATE)
            <x-button type="submit" label="Simpan" />
            @endcan
        @endif

        {{ $slot }}
    </div>
    @endsection
