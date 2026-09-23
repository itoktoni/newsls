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
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }

    public function login(Request $request)
    {
        // Desktop kirim `username`, web lama validate `email` → 422 format tidak baku.
        // Sekarang terima `username` | `email` | `login` dan SELALU kembalikan envelope Notes (HTTP 200).
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'username' => 'nullable|string|max:255',
            'login' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return Notes::validation($validator->errors()->first(), $validator->errors()->toArray());
        }

        $login = trim((string) ($request->input('username') ?? $request->input('email') ?? $request->input('login') ?? ''));

        if ($login === '') {
            return Notes::validation('Kolom username wajib diisi.', ['login' => ['Kolom username wajib diisi.']]);
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
            return Notes::failed(401, 'Username atau password salah.');
        }

        return Notes::token(array_merge([
            'api_token' => $user->createToken('api_token')->plainTextToken,
            // ponytail: RS yang boleh dipakai user ini (pivot rs_dan_user) —
            // desktop batasi dropdown transaksi ke daftar ini. null = semua.
            'allowed_rs_ids' => User::rsIdsFor((int) $user->id) ?: null,
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
