<?php

namespace App\Http\Livewire;

use App\Exports\ExcelLinenDetail;
use App\Jobs\JobExportExcel;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class WireDetailLinen extends Component
{
    public $bus_id;

    public $exporting = false;

    public $finish = false;

    public $code;

    public $progress = 1;

    public function export()
    {
        $this->code = 'export_register_'.unic(10).'.xlsx';

        $bus = Bus::batch([
            new JobExportExcel($this->code, new ExcelLinenDetail('detail_linen')),
        ])->dispatch();

        $this->bus_id = $bus->id;

        $this->exporting = true;
        $this->finish = false;
    }

    public function getBus()
    {
        if (! $this->bus_id) {
            return false;
        }

        return Bus::findBatch($this->bus_id);
    }

    public function updateProgress()
    {
        $this->finish = $this->getBus() ? $this->getBus()->finished() : false;

        if ($this->finish) {
            $this->exporting = false;
        }

        if ($this->progress < 90) {
            $this->progress = $this->progress + 1;
        }

        if ($this->finish) {
            $this->progress = 100;
        }
    }

    public function downloadExport()
    {
        return Storage::download($this->code);
    }

    public function render()
    {
        return view('livewire.'.getLowerClass(__CLASS__));
    }
}
