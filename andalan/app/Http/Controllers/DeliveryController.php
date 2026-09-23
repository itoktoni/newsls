<?php

namespace App\Http\Controllers;

use App\Dao\Enums\CetakType;
use App\Dao\Enums\ProcessType;
use App\Dao\Models\Bersih;
use App\Dao\Models\Cetak;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use App\Dao\Models\ViewDelivery;
use App\Dao\Models\ViewDetailLinen;
use App\Dao\Repositories\TransaksiRepository;
use App\Http\Requests\DeliveryRequest;
use App\Http\Requests\GeneralRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateDeliveryService;
use App\Http\Services\UpdateService;
use Plugins\Alert;
use Plugins\History as PluginsHistory;
use Plugins\Notes;
use Plugins\Response;

class DeliveryController extends MasterController
{
    public function __construct(TransaksiRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    public function postCreate(GeneralRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code, GeneralRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);

        return Response::redirectBack($data);
    }

    public function getData()
    {
        $query = self::$repository->dataDelivery();

        return $query;
    }

    public function getTable()
    {
        $data = $this->getData();

        return moduleView(modulePathTable(), [
            'data' => $data,
            'fields' => self::$repository->delivery->getShowField(),
        ]);
    }

    private function getTransaksi($code)
    {
        $view = ViewDelivery::find($code);

        if ($view) {
            $transaksi = Transaksi::with([HAS_DETAIL, HAS_RS, 'has_created_delivery'])
                ->where(Transaksi::field_delivery(), $view->field_primary);

            return $transaksi;
        }

        return $view;
    }

    public function getUpdate($code)
    {
        $transaksi = $this->getTransaksi($code);
        if (! $transaksi) {
            return Response::redirectTo(moduleRoute('getTable'));
        }

        return moduleView(modulePathForm(), $this->share([
            'model' => ViewDelivery::find($code),
            'data' => $transaksi->get(),
        ]));
    }

    public function getDeleteTransaksi($code)
    {
        $transaksi = Transaksi::with([HAS_DETAIL])->findOrFail($code);
        if ($transaksi) {

            Detail::find($transaksi->field_rfid)->update([
                Detail::field_status_process() => ProcessType::PACKING,
            ]);

            Notes::delete($transaksi->get()->toArray());
            Alert::delete();

            $transaksi->transaksi_delivery_at = null;
            $transaksi->transaksi_delivery_by = null;
            $transaksi->transaksi_delivery = null;

            $transaksi->save();
        }

        return Response::redirectBack();
    }

    public function getDelete()
    {
        $code = request()->get('code');
        $transaksi = Transaksi::where(Transaksi::field_delivery(), $code);

        if ($transaksi->count() > 0) {

            $clone_rfid = clone $transaksi;
            $transaksi->update([
                Transaksi::field_delivery_at() => null,
                Transaksi::field_delivery_by() => null,
                Transaksi::field_delivery() => null,
            ]);

            $data_rfid = $clone_rfid->get();
            $rfid = $data_rfid->pluck(Transaksi::field_rfid());

            Detail::whereIn(Detail::field_primary(), $rfid)->update([
                Detail::field_status_process() => ProcessType::PACKING,
            ]);

            $bulk = $data_rfid->toArray();
            Notes::delete($bulk);
            Alert::delete();
        }

        return Response::redirectBack($transaksi);
    }

    public function delivery(DeliveryRequest $request, UpdateDeliveryService $service)
    {
        $check = $service->update($request->code, $request->status_transaksi);

        return $check;
    }

    public function printPacking($code)
    {
        $total = Bersih::where(Bersih::field_barcode(), $code)
            ->addSelect([
                'bersih_rfid',
                'bersih_id_rs',
                'bersih_id_ruangan',
                'bersih_barcode',
                'bersih_delivery',
                'bersih_status',
                'bersih_report',
                'rs_nama',
                'ruangan_nama',
                'jenis_id',
                'jenis_nama',
                'name',
            ])
            ->leftJoinRelationship('has_rs')
            ->leftJoinRelationship('has_ruangan')
            ->leftJoinRelationship('has_user')
            ->leftJoinRelationship('has_detail.has_jenis')
            ->get();

        $data = null;
        $passing = [];

        if ($total->count() > 0) {

            $cetak = Cetak::where(Cetak::field_name(), $code)->first();
            if (! $cetak) {
                $cetak = Cetak::create([
                    Cetak::field_date() => date('Y-m-d'),
                    Cetak::field_name() => $code,
                    Cetak::field_type() => CetakType::Delivery,
                    Cetak::field_user() => auth()->user()->name ?? null,
                    Cetak::field_rs_id() => $total[0]->bersih_id_rs ?? null,
                ]);
            }

            $data = $total->mapToGroups(function ($item) use ($cetak) {
                $parse = [
                    'id' => $item->jenis_id,
                    'nama' => $item->jenis_nama,
                    'rs' => $item->rs_nama,
                    'lokasi' => $item->ruangan_nama,
                    'status' => $item->bersih_status,
                    'tgl' => formatDate($cetak->cetak_date, 'd/M/Y'),
                ];

                return [$item['jenis_id'].'#'.$item['ruangan_id'] => $parse];
            });

            $no = 1;
            foreach ($data as $item) {
                $return[] = [
                    'id' => $no,
                    'code' => $code,
                    'tgl' => $item[0]['tgl'] ?? null,
                    'rs' => $item[0]['rs'] ?? null,
                    'nama' => $item[0]['nama'] ?? null,
                    'status' => $item[0]['status'] ?? null,
                    'lokasi' => $item[0]['lokasi'] ?? null,
                    'user' => auth()->user()->name ?? null,
                    'total' => count($item),
                ];

                $no++;
            }

            $passing = Notes::data($return, $passing);

        }

        return $passing;
    }

    public function printDelivery($code)
    {
        $total = Bersih::where(Bersih::field_delivery(), $code)
            ->addSelect([
                'bersih_rfid',
                'bersih_id_rs',
                'bersih_id_ruangan',
                'bersih_barcode',
                'bersih_delivery',
                'bersih_status',
                'bersih_report',
                'rs_nama',
                'ruangan_id',
                'ruangan_nama',
                'jenis_id',
                'jenis_nama',
                'name',
            ])
            ->leftJoinRelationship('has_rs')
            ->leftJoinRelationship('has_ruangan')
            ->leftJoinRelationship('has_user')
            ->leftJoinRelationship('has_detail.has_jenis')
            ->get();

        $data = null;
        $passing = [];

        if ($total->count() > 0) {

            $cetak = Cetak::where(Cetak::field_name(), $code)->first();
            if (! $cetak) {
                $cetak = Cetak::create([
                    Cetak::field_date() => date('Y-m-d'),
                    Cetak::field_name() => $code,
                    Cetak::field_type() => CetakType::Delivery,
                    Cetak::field_user() => auth()->user()->name ?? null,
                    Cetak::field_rs_id() => $total[0]->bersih_id_rs ?? null,
                ]);
            }

            $data = $total->mapToGroups(function ($item) {
                $parse = [
                    'id' => $item->jenis_id,
                    'nama' => $item->jenis_nama,
                    'rs' => $item->rs_nama,
                    'lokasi' => $item->ruangan_nama,
                    'status' => $item->bersih_status,
                    'tgl' => formatDate($item->bersih_report, 'd/M/Y'),
                ];

                return [$item['jenis_id'].'#'.$item['ruangan_id'] => $parse];
            });

            $no = 1;
            foreach ($data as $item) {
                $return[] = [
                    'id' => $no,
                    'code' => $code,
                    'tgl' => $item[0]['tgl'] ?? null,
                    'rs' => $item[0]['rs'] ?? null,
                    'nama' => $item[0]['nama'] ?? null,
                    'lokasi' => $item[0]['lokasi'] ?? null,
                    'status' => $item[0]['status'] ?? null,
                    'user' => auth()->user()->name ?? null,
                    'total' => count($item),
                ];

                $no++;
            }

            $passing = Notes::data($return, $passing);

        }

        return $passing;
    }
}
