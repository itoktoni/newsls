<?php

namespace Plugins;

use Illuminate\Support\Facades\Log;

/**
 * Standar tunggal bentuk JSON untuk SELURUH request API.
 *
 * Setiap respons API memakai envelope:
 *
 *   {
 *     "status"  : bool,    // true = sukses, false = gagal
 *     "code"    : int,     // kode asli: 200 / 400 / 401 / 404 / 405 / 422 / 429 / 500
 *     "name"    : string,  // jenis respons: List|Data|Create|Update|Delete|Token|Validation|Error
 *     "message" : string,  // pesan untuk ditampilkan ke user
 *     "data"    : mixed    // isi; SELALU [] kalau code >= 400
 *   }
 *
 * ponytail: HTTP status-nya SELALU 200, walaupun isinya error. Ini sengaja
 * dipertahankan dari project lama (andalan) — klien mobile/scanner yang sudah
 * jalan membaca `code` di dalam body, bukan HTTP status. Kalau HTTP status
 * ikut diubah, klien lama bisa menganggapnya kegagalan transport.
 *
 * Pemakaian di controller:
 *   return Notes::data($rows);              // list / index
 *   return Notes::single($row);             // detail satu record
 *   return Notes::create($row);             // setelah insert
 *   return Notes::update($row);             // setelah update
 *   return Notes::delete();                 // setelah delete
 *   return Notes::token($payload);          // login / refresh token
 *   return Notes::error($data, $message);   // gagal (code 400)
 *   return Notes::validation($msg, $errs);  // gagal validasi (code 422)
 *   return Notes::notFound();               // code 404
 *   return Notes::failed(401, $message);    // kode lain: 401/405/429/500
 */
class Notes
{
    const create = 'Create';

    const update = 'Update';

    const delete = 'Delete';

    const validation = 'Validation';

    const error = 'Error';

    const data = 'List';

    const single = 'Data';

    const token = 'Token';

    /**
     * ponytail: legacy memakai env('APP_DEBUG') langsung. Itu bernilai null kalau
     * `php artisan config:cache` dipakai, jadi log debug-nya diam-diam mati.
     * config('app.debug') aman untuk keduanya.
     */
    public static function checkDebug()
    {
        return (bool) config('app.debug');
    }

    public static function data($data = null, $additional = [])
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::data;
        $log['message'] = 'Data berhasil diambil';
        $log['data'] = $data;

        return self::sentJson($log, 200, $additional);
    }

    public static function single($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::single;
        $log['message'] = 'Data di dapat';
        $log['data'] = $data;
        if (self::checkDebug()) {
            Log::info(self::single, $log);
        }

        return self::sentJson($log);
    }

    public static function create($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::create;
        $log['message'] = 'Data berhasil di buat';
        $log['data'] = $data;
        if (self::checkDebug()) {
            Log::info(self::create, $log);
        }

        return self::sentJson($log);
    }

    public static function token($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::token;
        $log['message'] = 'Data token '.self::token;
        $log['data'] = $data;
        if (self::checkDebug()) {
            Log::info(self::token, $log);
        }

        return self::sentJson($log);
    }

    public static function update($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::update;
        $log['message'] = 'Data berhasil di ubah';
        $log['data'] = $data;
        if (self::checkDebug()) {
            Log::info(self::update, $log);
        }

        return self::sentJson($log);
    }

    public static function delete($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::delete;
        $log['message'] = 'Data berhasil di hapus';
        $log['data'] = $data;
        if (self::checkDebug()) {
            Log::warning(self::delete, $log);
        }

        return self::sentJson($log, 204);
    }

    public static function error($data = null, $message = null)
    {
        $log['status'] = false;
        $log['code'] = 400;
        $log['name'] = self::error;
        $log['message'] = $message ?? $data;
        $log['data'] = [$data];
        if (self::checkDebug()) {
            Log::error(self::error, $log);
        }

        return self::sentJson($log, 400);
    }

    /**
     * $errors diisi map field => pesan, mis. ['rfid' => ['RFID sudah terdaftar.']].
     *
     * ponytail: legacy menaruh 'Validation Error' di `data`, lalu sentJson
     * mengosongkannya jadi [] karena code >= 400 — jadi detail error per field
     * hilang dan klien hanya dapat `message`. Supaya klien tetap bisa menandai
     * field mana yang salah, detailnya dikirim lewat key tambahan `errors`
     * (key lama tidak berubah, jadi tetap kompatibel).
     */
    public static function validation($message = null, $errors = null)
    {
        $log['status'] = false;
        $log['code'] = 422;
        $log['name'] = self::validation;
        $log['message'] = $message ?? 'Data yang diberikan tidak valid.';
        $log['data'] = 'Validation Error';
        if (self::checkDebug()) {
            Log::warning(self::validation, $log);
        }

        return self::sentJson($log, 422, $errors ? ['errors' => $errors] : []);
    }

    public static function notFound($data = null, $url = null)
    {
        $log['status'] = false;
        $log['code'] = 404;
        $log['name'] = self::error;
        $log['message'] = 'Url tidak ditemukan';
        $log['data'] = $data;
        if (self::checkDebug()) {
            Log::warning(self::error, $log);
        }

        return self::sentJson($log, 404);
    }

    /**
     * Kode selain 400/404/422 — dipakai handler global untuk 401, 405, 429, 500.
     *
     * @param  string  $name  label jenis respons; default 'Error' (sama seperti legacy).
     */
    public static function failed($code, $message = null, $data = null, $name = self::error)
    {
        $log['status'] = false;
        $log['code'] = $code;
        $log['name'] = $name;
        $log['message'] = $message ?? 'Terjadi kesalahan';
        $log['data'] = $data;
        if (self::checkDebug()) {
            Log::error($name, $log);
        }

        return self::sentJson($log, $code);
    }

    /**
     * @param  int  $status  kode asli untuk logika "kosongkan data kalau error".
     *                       HTTP status yang dikirim tetap 200 (lihat docblock class).
     */
    public static function sentJson($data, $status = 200, $additional = [])
    {
        if ($additional) {
            $data = array_merge($data, $additional);
        }

        // ponytail: legacy memakai wantsJson(). Ditambah is('api/*') supaya request
        // ke /api/* tanpa header Accept tetap dapat Response JSON yang rapi,
        // bukan array mentah yang nanti dibungkus Laravel dengan status berbeda.
        if (request()->wantsJson() || request()->is('api/*')) {

            if ($status >= 400) {
                // Fix: single-object endpoints (login/me/logout/grouping) — [] cannot deserialize to object → "cannot deserialize JSON array into Data"
                // List endpoints expect [], single-object endpoints expect null
                $isSingleObjectEndpoint = request()->is('api/login') || request()->is('api/me') || request()->is('api/logout') || request()->is('api/grouping/*');
                if ($isSingleObjectEndpoint) {
                    $data['data'] = null;
                } else {
                    $data['data'] = [];
                }
            }

            return response()->json($data, 200);
        }

        return $data;
    }
}
