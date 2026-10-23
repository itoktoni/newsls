<?php

namespace App\Livewire;

use App\Dao\Models\JenisLinen;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use Livewire\Component;

class DropdownRs extends Component
{
    public $id_rs;

    public $id_ruangan;

    public $id_jenis;

    public $data_rs;

    public $data_ruangan;

    public $data_jenis;

    public $hide;

    public function mount()
    {
        $this->data_rs = Rs::getOptions()->toArray();
        $this->data_ruangan = Ruangan::getOptions();
        $this->data_jenis = JenisLinen::getOptions();
    }

    public function render()
    {
        if ($this->id_rs) {
            $rs_parse = Rs::with([HAS_RUANGAN, HAS_JENIS])->find($this->id_rs);

            $this->data_ruangan = $rs_parse->has_ruangan->pluck(Ruangan::field_name(), Ruangan::field_primary()) ?? [];
            $this->data_jenis = $rs_parse->has_jenis->pluck(JenisLinen::field_name(), JenisLinen::field_primary()) ?? [];
        }

        if ($rs_id = request()->get('view_rs_id')) {
            $this->id_rs = $rs_id;
        }

        if ($ruangan_id = request()->get('view_ruangan_id')) {
            $this->id_ruangan = $ruangan_id;
        }

        if ($jenis_id = request()->get('view_linen_id')) {
            $this->id_jenis = $jenis_id;
        }

        return view('livewire.dropdown-rs')->with([
            'id_ruangan' => $this->id_ruangan,
            'id_jenis' => $this->id_jenis,
            'rs_id' => $this->id_rs,
        ]);
    }
}
