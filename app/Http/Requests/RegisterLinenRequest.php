<?php

namespace App\Http\Requests;

use App\Enums\CuciEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi payload register linen (scan masuk, API mobile/scanner).
 *
 * Nama field sengaja dibuat sama dengan endpoint register project lama
 * (andalan/routes/api.php → POST register) supaya klien yang sudah jalan
 * tidak perlu diubah: rfid[], rs_id, ruangan_id, jenis_id, bahan_id,
 * supplier_id, status_cuci.
 *
 * Tambahan project ini (semua opsional):
 *   - status_register    → REGISTER | GANTI_CHIP
 *   - status_kepemilikan → FREE | DEDICATED | GROUP (kalau kosong ditebak dari jumlah rs_id)
 *   - deskripsi, tgl_cek
 *
 * rs_id yang dikirim akan jadi baris master `config_linen` (lihat RegisterLinenAction).
 */
class RegisterLinenRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route sudah dibungkus auth:sanctum; di sini cukup pastikan ada user.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'rfid' => 'required|array|min:1',
            'rfid.*' => 'required|string|max:255|distinct',

            'jenis_id' => 'required|integer|exists:jenis_linen,jenis_id',
            'bahan_id' => 'required|integer|exists:jenis_bahan,bahan_id',
            'supplier_id' => 'required|integer|exists:supplier,supplier_id',
            'status_cuci' => ['required', Rule::in(CuciEnum::getValues())],

            'status_register' => ['nullable', Rule::in(RegisterEnum::getValues())],
            'status_kepemilikan' => ['nullable', Rule::in(RsStatusEnum::getValues())],

            // rs_id = RS pemilik linen (master config_linen), mengikuti andalan:
            //   - skalar        → 1 RS  → DEDICATED
            //   - array (>= 2)  → GROUP  (mis. siloam A + siloam B)
            //   - tidak dikirim → FREE   (boleh dipakai semua RS, nol baris config)
            'rs_id' => 'nullable',
            'rs_id.*' => 'integer|distinct|exists:rs,rs_id',

            'ruangan_id' => 'nullable|integer|exists:ruangan,ruangan_id',
            'deskripsi' => 'nullable|string|max:255',
            'tgl_cek' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'rfid.required' => 'Kirim minimal satu RFID pada field rfid[].',
            'rfid.array' => 'Field rfid harus berupa array RFID.',
            'rfid.*.distinct' => 'Ada RFID yang dikirim lebih dari sekali.',
            'jenis_id.required' => 'Jenis linen wajib diisi.',
            'jenis_id.exists' => 'Jenis linen tidak ditemukan.',
            'bahan_id.required' => 'Jenis bahan wajib diisi.',
            'bahan_id.exists' => 'Jenis bahan tidak ditemukan.',
            'supplier_id.required' => 'Supplier wajib diisi.',
            'supplier_id.exists' => 'Supplier tidak ditemukan.',
            'status_cuci.required' => 'Status cuci wajib diisi.',
            'status_cuci.in' => 'Status cuci harus salah satu dari: '.implode(', ', CuciEnum::getValues()).'.',
            'status_register.in' => 'Status register harus salah satu dari: '.implode(', ', RegisterEnum::getValues()).'.',
            'status_kepemilikan.in' => 'Status kepemilikan harus salah satu dari: '.implode(', ', RsStatusEnum::getValues()).'.',
            'ruangan_id.exists' => 'Ruangan tidak ditemukan.',
            'rs_id.*.exists' => 'Ada RS pemilik yang tidak ditemukan.',
        ];
    }
}
