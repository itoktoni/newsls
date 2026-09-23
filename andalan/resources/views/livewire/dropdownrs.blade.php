<?php

use function Livewire\Volt\{state};
use function Livewire\Volt\{computed};

use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\JenisLinen;

state(['hide' => false]);

state(['id_rs' => function() {
    if($rs = request()->view_rs_id){
        return $rs;
    }

    return null;
}]);

state(['id_ruangan' => function() {
    if($ruangan = request()->view_ruangan_id){
        return $ruangan;
    }

    return null;
}]);

state(['id_jenis' => function() {
    if($jenis = request()->view_linen_id){
        return $jenis;
    }

    return null;
}]);

$data_rs = computed(function () {
    return $this->data_rs = Rs::get()->pluck(Rs::field_name(), Rs::field_primary())->toArray() ?? [];
});

$data_ruangan = computed(function () {
    if($this->id_rs){
        return Rs::find($this->id_rs)->has_ruangan()->get()->pluck(Ruangan::field_name(), Ruangan::field_primary())->toArray() ?? [];
    }

    return Ruangan::get()->pluck(Ruangan::field_name(), Ruangan::field_primary())->toArray();
});

$data_jenis = computed(function () {

    if($this->id_rs){
        return Rs::find($this->id_rs)->has_jenis()->get()->pluck(JenisLinen::field_name(), JenisLinen::field_primary())->toArray() ?? [];
    }

    return JenisLinen::get()->pluck(JenisLinen::field_name(), JenisLinen::field_primary())->toArray();
});

?>

<div class="container-fluid">

    <div class="row">
        <x-form-select col="{{ $hide ? 6 : 4 }}" wire:model.live="id_rs" label="Rumah sakit" name="view_rs_id" value="{{ $id_rs }}" :options="$this->data_rs" />
        @if($hide != 'ruangan')
        <x-form-select col="{{ $hide ? 6 : 4 }}" wire:model="id_ruangan" label="Ruangan" name="view_ruangan_id" value="{{ $id_ruangan }}" :options="$this->data_ruangan" />
        @endif
        @if($hide != 'jenis')
        <x-form-select col="{{ $hide ? 6 : 4 }}" wire:model="id_jenis" label="Jenis" name="view_linen_id" value="{{ $id_jenis }}" :options="$this->data_jenis" />
        @endif
    </div>

</div>
