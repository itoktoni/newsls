<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Plugins\Notes;

class AuthController extends Controller
{
    private function userResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function login(Request $request)
    {
        // Desktop kirim `username` (+ `email` isi yang sama untuk compat), web lama
        // validate `email|email` → username non-email ("admin") selalu 422.
        // Sekarang terima `username` | `email` | `login` sebagai string biasa dan
        // SELALU kembalikan SATU envelope gabungan:
        //   {status, code, name, message, data}  (format Notes, desktop baca ini)
        // + {message, errors:{username:[...]}}   (format validasi Laravel)
        // sehingga kedua format yang beredar sebelumnya menyatu di satu respons (HTTP 200).
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'login' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            // Samakan key ke `username` supaya desktop cukup cek errors.username
            // (legacy kadang kirim nilai via field `email`/`login`).
            if (! isset($errors['username'])) {
                $first = $validator->errors()->first();
                $errors = array_merge(['username' => [$first]], $errors);
            }

            return Notes::validation($validator->errors()->first(), $errors);
        }

        $login = trim((string) ($request->input('username') ?? $request->input('email') ?? $request->input('login') ?? ''));

        if ($login === '') {
            $msg = 'username yang dipilih tidak valid.';

            return Notes::validation($msg, ['username' => [$msg]]);
        }

        // Cari user: jika format email → by email, else by email|name|phone (name dipakai sebagai username)
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $login)->first();
        } else {
            $user = User::where('email', $login)
                ->orWhere('name', $login)
                ->orWhere('phone', $login)
                ->first();
        }

        // Fallback: jika request juga kirim `email` terpisah dan belum ketemu
        if (! $user && $request->filled('email') && $request->input('email') !== $login) {
            $user = User::where('email', $request->input('email'))->first();
        }

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            // Satu-satunya format error kredensial: envelope Notes (code 400,
            // message "Login Gagal" dibaca desktop) + errors.username ala Laravel
            // (message detail "username yang dipilih tidak valid.").
            $detail = 'username yang dipilih tidak valid.';

            return Notes::failed(400, 'Login Gagal', null, Notes::error, [
                'errors' => ['username' => [$detail]],
            ]);
        }

        return Notes::token(array_merge([
            'api_token' => $user->createToken('api_token')->plainTextToken,
            // ponytail: RS yang boleh dipakai user ini (pivot rs_dan_user) —
            // desktop batasi dropdown transaksi ke daftar ini. null = semua.
            'allowed_rs_ids' => User::rsIdsFor((int) $user->id) ?: null,
            // Form yang boleh tampil di aplikasi mobile (pivot
            // mobile_menu_dan_user). Pivot kosong = semua menu aktif.
            'menu' => User::menuNamesFor((int) $user->id),
            // Kolom legacy andalan yang dibaca LoginDAO desktop (id/name/phone/email/
            // email_verified_at/role/level/active/created_at/updated_at/vendor/rs_id/
            // api_token). bka tidak menyimpan username/level/active/vendor/rs_id,
            // jadi dikirim null supaya bentuk JSON-nya tetap sama.
            'username' => null,
            'level' => null,
            'active' => null,
            'vendor' => null,
            'rs_id' => null,
            'email_verified_at' => $user->email_verified_at?->format('Y-m-d H:i:s'),
            'updated_at' => $user->updated_at?->format('Y-m-d H:i:s'),
        ], $this->userResponse($user)));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return Notes::delete();
    }

    public function me(Request $request)
    {
        return Notes::single([
            'user' => $this->userResponse($request->user()),
            // ponytail: RS yang boleh dipakai token ini (pivot rs_dan_user).
            'allowed_rs_ids' => User::rsIdsFor((int) $request->user()->id) ?: null,
            // ponytail: form mobile yang boleh tampil (pivot mobile_menu_dan_user).
            'menu' => User::menuNamesFor((int) $request->user()->id),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'sometimes|nullable|string|max:20|unique:users,phone,'.$user->id,
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
            $user->email_verified_at = null;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        $user->save();

        return Notes::update([
            'user' => $this->userResponse($user),
        ]);
    }
}
