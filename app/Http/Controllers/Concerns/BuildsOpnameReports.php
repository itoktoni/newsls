<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Data + share standar untuk semua Report Opname.
 *
 * Filter: opname_id (wajib). Export Excel = print view + header attachment.
 */
trait BuildsOpnameReports
{
    protected function reportShare(array $data = []): array
    {
        return array_merge([
            'opnameOptions' => Opname::with('hasRs')
                ->orderByDesc('opname_id')
                ->get()
                ->mapWithKeys(function (Opname $o) {
                    $label = sprintf('#%s %s', $o->opname_id, $o->opname_nama ?: 'Opname');
                    if ($o->hasRs?->rs_nama) {
                        $label .= ' — '.$o->hasRs->rs_nama;
                    }

                    return [$o->opname_id => $label];
                }),
            'rsOptions' => User::rsOptions(),
        ], $data);
    }

    protected function reportOpname(Request $request): Opname
    {
        $request->validate(['opname_id' => 'required|integer|exists:opname,opname_id']);

        return Opname::with('hasRs')->findOrFail((int) $request->input('opname_id'));
    }

    protected function reportDetails(Opname $opname)
    {
        return OpnameDetail::query()
            ->with([
                'hasView.hasJenis',
                'hasView.hasRuangan',
                'hasView.hasRs',
                'hasView.hasBahan',
            ])
            ->where('opname_detail_id_opname', $opname->opname_id)
            ->orderBy('opname_detail_rfid')
            ->get();
    }

    /**
     * Matriks rekap: baris = jenis, kolom = ruangan.
     * SA = snapshot capture (stok menurut config/detail), SO = ketemu saat scan.
     */
    protected function reportRekapMatrix($details): array
    {
        $locations = [];
        $linens = [];
        $sa = [];
        $so = [];

        foreach ($details as $row) {
            $view = $row->hasView;
            $jenisId = (int) ($view?->detail_id_jenis ?? 0);
            $locId = (int) ($view?->detail_id_ruangan ?? 0);
            $linens[$jenisId] = $view?->hasJenis?->jenis_nama ?? 'Belum teregister';
            $locations[$locId] = $view?->hasRuangan?->ruangan_nama ?? '-';
            $sa[$jenisId][$locId] = ($sa[$jenisId][$locId] ?? 0) + 1;
            if ((int) $row->opname_detail_ketemu === 1) {
                $so[$jenisId][$locId] = ($so[$jenisId][$locId] ?? 0) + 1;
            }
        }

        asort($linens);
        ksort($locations);

        return [
            'locations' => $locations,
            'linens' => $linens,
            'sa' => $sa,
            'so' => $so,
        ];
    }

    protected function reportExport(string $view, array $data, string $filename)
    {
        return response()->view($view, $this->reportShare($data), 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
