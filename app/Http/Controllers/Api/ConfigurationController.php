<?php

namespace App\Http\Controllers\Api;

use App\Enums\CuciEnum;
use App\Enums\RegisterEnum;
use App\Http\Controllers\Controller;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Supplier;

/**
 * Master dropdown untuk desktop (registrasi linen).
 *
 * Adopsi closure legacy di routes/api.php — kontrak dipertahankan persis:
 * mentah TANPA envelope Notes, status sebagai array [{status_id, status_name}].
 */
class ConfigurationController extends Controller
{
    public function __invoke()
    {
        // Urutan mengikuti kontrak legacy (lihat contoh respons desktop).
        return response()->json([
            'supplier' => Supplier::select('supplier_id', 'supplier_nama')->get(),
            'jenis_bahan' => JenisBahan::select('bahan_id', 'bahan_nama')->get(),
            'jenis_linen' => JenisLinen::select('jenis_id', 'jenis_nama')->get(),
            'status_proses' => self::statusList(['REGISTER', 'KOTOR', 'SCAN', 'QC', 'PACKING', 'BERSIH']),
            'status_transaksi' => self::statusList(['KOTOR', 'REJECT', 'REWASH', 'BERSIH', 'REGISTER']),
            'status_cuci' => self::statusList([CuciEnum::CUCI, CuciEnum::RENTAL]),
            'status_register' => self::statusList([RegisterEnum::REGISTER, RegisterEnum::GANTI_CHIP]),
        ]);
    }

    /**
     * Status dikirim sebagai array [{status_id, status_name}] (bukan map).
     *
     * @param  array<int,string>  $ids
     */
    private static function statusList(array $ids): array
    {
        return array_map(
            fn ($id) => ['status_id' => $id, 'status_name' => $id],
            $ids
        );
    }
}
