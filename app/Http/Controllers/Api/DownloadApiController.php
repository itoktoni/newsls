<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

/**
 * GET /api/download/{rsid} — sinkronisasi daftar RFID milik satu RS ke desktop.
 *
 * Kontrak respons mengikuti `andalan/app/Http/Resources/DownloadCollection`
 * (desktop memetakan root object + key `data`, `rs`, `ruangan`, `opname`),
 * tapi sumbernya tabel BKA langsung (detail_linen/outstanding/transaksi/opname)
 * karena project ini tidak punya `view_detail_linen`.
 *
 * ponytail: 12rb+ RFID per RS dan file JSON-nya mudah "putus" di tengah.
 * Penyebabnya bukan besar payload, tapi cara ambil datanya:
 *   - `DetailLinen::with([...])->get()` menghidrasi 12rb model + relasi
 *     (~50rb objek) → memory_limit habis → fatal error saat response sudah
 *     setengah terkirim → JSON terpotong, desktop gagal deserialize.
 *   - seluruh payload di-encode dulu, baru dikirim: satu error = nol byte.
 * Di sini tiap chunk 2.000 baris dibaca dengan query builder (tanpa model),
 * status outstanding/transaksi di-preload per chunk, lalu baris di-yield dan
 * di-flush langsung keluar socket. Memory datar, jadi berapa pun jumlah
 * RFID-nya response tetap lengkap dan tidak menunggu sampai akhir.
 * Key tambahan `total` dikirim di depan `data` supaya desktop bisa memastikan
 * `data.length == total` (deteksi download terpotong tanpa harus parse gagal).
 */
class DownloadApiController extends Controller
{
    /**
     * Baris per chunk. Kecil = memory datar + flush lebih cepat sampai ke
     * desktop; 2.000 dipilih supaya `whereIn` untuk outstanding/transaksi
     * juga tetap ramping.
     */
    private const CHUNK = 2000;

    public function __invoke(string $rsid)
    {
        // ponytail: jangan biarkan max_execution_time memutus response di
        // tengah stream (itu penyebab JSON terpotong yang kedua).
        set_time_limit(0);
        // Query log menyimpan SQL + binding setiap query di memory.
        DB::disableQueryLog();
        // Compression buffering menahan output supaya tidak bisa di-flush.
        @ini_set('zlib.output_compression', '0');

        if (! ctype_digit($rsid)) {
            return Notes::failed(404, 'Rumah sakit tidak ditemukan.');
        }

        $rsId = (int) $rsid;

        // ponytail: desktop hanya boleh sync RS yang jadi hak user (rs_dan_user).
        User::ensureRsAccess($rsId);

        $rs = DB::table('rs')->where('rs_id', $rsId)->first(['rs_id', 'rs_nama']);
        if ($rs === null) {
            return Notes::failed(404, 'Rumah sakit tidak ditemukan.');
        }

        // Hitung dulu supaya RS kosong dapat envelope error biasa, bukan JSON
        // setengah jalan.
        $total = (int) $this->chunkQuery($rsId)->count();
        if ($total === 0) {
            return Notes::failed(404, 'Data Tidak Ditemukan !');
        }

        return $this->stream($rsId, $rs, $total);
    }

    /**
     * Envelope legacy + `data` berupa generator → StreamedJsonResponse
     * (lihat vendor/symfony/http-foundation/StreamedJsonResponse.php).
     */
    private function stream(int $rsId, object $rs, int $total)
    {
        return response()->streamJson(
            [
                'status' => true,
                'code' => 200,
                'name' => Notes::data,
                'message' => 'Data berhasil diambil',
                // Bukan bagian legacy — dipakai desktop untuk cek kelengkapan.
                'total' => $total,
                'data' => $this->rows($rsId, (string) $rs->rs_nama),
                'rs' => [
                    'rs_id' => (int) $rs->rs_id,
                    'rs_nama' => $rs->rs_nama,
                ],
                'ruangan' => $this->ruanganList($rsId),
                'opname' => $this->opnameRfids($rsId),
            ],
            200,
            [
                'Content-Type' => 'application/json',
                // Hasil sync tidak boleh di-cache proxy/browser.
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                // nginx: kirim tiap flush, jangan tahan sampai response selesai.
                'X-Accel-Buffering' => 'no',
            ],
            // JSON_INVALID_UTF8_SUBSTITUTE: data legacy utf8mb3 kadang bukan
            // UTF-8 valid → tanpa opsi ini json_encode melempar JsonException
            // di tengah stream (JSON terpotong).
            JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    /**
     * Generator baris `data[]` — field persis milik DownloadCollection:
     * rfid, rs_id, rs_nama, ruangan_id, ruangan_nama, jenis_id, jenis_nama,
     * status_transaksi, status_proses, tanggal.
     */
    private function rows(int $rsId, string $rsNama): \Generator
    {
        // Master nama: ratusan baris, sekali load lebih murah daripada join
        // per baris (dan menghindari duplikasi baris kalau pivot dobel).
        $jenisNama = DB::table('jenis_linen')->pluck('jenis_nama', 'jenis_id');
        $ruanganNama = DB::table('ruangan')->pluck('ruangan_nama', 'ruangan_id');

        $bersih = TransactionType::BERSIH;
        $sekarang = now()->format('Y-m-d H:i:s');
        $offset = 0;

        while (true) {
            $chunk = $this->chunkQuery($rsId)
                ->offset($offset)
                ->limit(self::CHUNK)
                ->get();

            if ($chunk->isEmpty()) {
                return;
            }

            $rfids = $chunk->pluck('detail_rfid')->all();

            $outstanding = DB::table('outstanding')
                ->whereIn('outstanding_rfid', $rfids)
                ->get(['outstanding_rfid', 'outstanding_status_transaksi', 'outstanding_status_proses'])
                ->keyBy('outstanding_rfid');

            // Legacy menghitung `isset($transaksi[$rfid])` dari seluruh history
            // (group by rfid) — yang dipakai hanya ADA/TIDAK, jadi cukup
            // SELECT DISTINCT transaksi_rfid (index transaksi_rfid).
            $adaTransaksi = array_flip(
                DB::table('transaksi')
                    ->whereIn('transaksi_rfid', $rfids)
                    ->distinct()
                    ->pluck('transaksi_rfid')
                    ->all()
            );

            foreach ($chunk as $row) {
                $rfid = (string) $row->detail_rfid;
                $out = $outstanding[$rfid] ?? null;

                // Aturan legacy DownloadCollection:
                // - default BERSIH (linen dianggap ada di RS),
                // - ada baris outstanding → status dari outstanding,
                //   tanggal = waktu generate (state "sekarang"),
                // - RFID belum pernah punya transaksi → dipaksa BERSIH.
                $statusTransaksi = $bersih;
                $statusProses = $bersih;
                $tanggal = $row->detail_updated_at;

                if ($out !== null) {
                    $statusTransaksi = $out->outstanding_status_transaksi ?: $bersih;
                    $statusProses = $out->outstanding_status_proses ?: $bersih;
                    $tanggal = $sekarang;
                }
                if (! isset($adaTransaksi[$rfid])) {
                    $statusTransaksi = $bersih;
                }

                $ruanganId = $row->detail_id_ruangan === null ? null : (int) $row->detail_id_ruangan;
                $jenisId = $row->detail_id_jenis === null ? null : (int) $row->detail_id_jenis;

                yield [
                    'rfid' => $rfid,
                    'rs_id' => $rsId,
                    'rs_nama' => $rsNama,
                    'ruangan_id' => $ruanganId,
                    'ruangan_nama' => $ruanganId === null ? null : ($ruanganNama[$ruanganId] ?? null),
                    'jenis_id' => $jenisId,
                    'jenis_nama' => $jenisId === null ? null : ($jenisNama[$jenisId] ?? null),
                    'status_transaksi' => $statusTransaksi,
                    'status_proses' => $statusProses,
                    // Normalisasi 'Y-m-d H:i:s' (legacy: Carbon ISO8601 saat
                    // tanpa outstanding, string saat ada outstanding).
                    'tanggal' => $tanggal === null ? null : (string) $tanggal,
                ];
            }

            $offset += self::CHUNK;
            unset($outstanding, $adaTransaksi, $rfids, $chunk);

            $this->flushChunk();

            // Desktop batal/mati → berhenti, jangan lanjut query chunk berikutnya.
            if (connection_aborted()) {
                return;
            }
        }
    }

    /**
     * Query dasar baris RFID yang boleh dipakai RS ini: anggota `config_linen`
     * untuk RS tsb, ATAU linen FREE (tidak punya baris config_linen sama sekali).
     * Urut `detail_rfid` (unique, ada index) supaya offset chunk stabil — tidak
     * ada baris terlewat/duplikat.
     */
    private function chunkQuery(int $rsId)
    {
        return DB::table('detail_linen')
            ->where(function ($q) use ($rsId) {
                $q->whereIn('detail_linen.detail_rfid', function ($sub) use ($rsId) {
                    $sub->select('detail_rfid')->from('config_linen')->where('rs_id', $rsId);
                })->orWhereNotIn('detail_linen.detail_rfid', function ($sub) {
                    $sub->select('detail_rfid')->from('config_linen');
                });
            })
            ->orderBy('detail_rfid')
            ->select(['detail_rfid', 'detail_id_ruangan', 'detail_id_jenis', 'detail_updated_at']);
    }

    /**
     * Ruangan milik RS (pivot rs_dan_ruangan) — dipakai desktop untuk
     * melengkapi dropdown lokasi.
     */
    private function ruanganList(int $rsId): array
    {
        return DB::table('ruangan')
            ->join('rs_dan_ruangan', 'rs_dan_ruangan.ruangan_id', '=', 'ruangan.ruangan_id')
            ->where('rs_dan_ruangan.rs_id', $rsId)
            ->orderBy('ruangan.ruangan_nama')
            ->get(['ruangan.ruangan_id', 'ruangan.ruangan_nama'])
            ->map(fn ($row) => [
                'ruangan_id' => (int) $row->ruangan_id,
                'ruangan_nama' => $row->ruangan_nama,
            ])
            ->all();
    }

    /**
     * RFID yang sudah ketemu di opname AKTIF (opname_status = 1/Proses) milik
     * RS ini — legacy mengirim daftar ini supaya desktop bisa menandai linen
     * yang sudah discan saat stock opname.
     */
    private function opnameRfids(int $rsId): array
    {
        $opnameId = DB::table('opname')
            ->where('opname_id_rs', $rsId)
            ->where('opname_status', 1)
            ->orderByDesc('opname_id')
            ->value('opname_id');

        if ($opnameId === null) {
            return [];
        }

        return DB::table('opname_detail')
            ->where('opname_detail_id_opname', $opnameId)
            ->where('opname_detail_ketemu', 1)
            ->pluck('opname_detail_rfid')
            ->all();
    }

    /**
     * Dorong buffer ke socket tiap chunk: desktop mulai menerima data sejak
     * chunk pertama, dan proxy/browser tidak menahan response sampai selesai
     * (penyebab "download putus" di sisi klien).
     */
    private function flushChunk(): void
    {
        if (ob_get_level() > 0) {
            @ob_flush();
        }

        flush();
    }
}
