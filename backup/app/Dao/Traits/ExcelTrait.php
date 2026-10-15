<?php

namespace App\Dao\Traits;

use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\Facades\FastExcel;
use Spatie\SimpleExcel\SimpleExcelWriter;

trait ExcelTrait
{
    public static function export($data, $name)
    {
        $handle = fopen(storage_path('app/'.$name.'.csv'), 'w');
        $data->chunk(2000, function ($model) use ($handle, $data) {
            fputcsv($handle, array_keys($data->getModel()->getExcelField()));
            foreach ($model->toArray() as $item) {
                fputcsv($handle, $item);
            }
        });

        fclose($handle);

        return Storage::disk()->download($name.'.csv');

        // return FastExcel::data($data)->download($name);
        // return SimpleExcelWriter::streamDownload($name)
        //     ->noHeaderRow()
        //     ->addRows($data->toArray());
    }
}
