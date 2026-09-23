<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $sessionId = $request->session()->getId();

        $otherSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $sessionId)
            ->get();

        return view('pages.settings.profile', [
            'user' => $user,
            'otherSessions' => $otherSessions,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            try {
                $path = uploadFile($request->file('avatar'), 'avatars', [
                    'max_size' => 1024,
                    'max_width' => 800,
                    'max_height' => 800,
                ]);
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $path;
            } catch (\InvalidArgumentException $e) {
                flash()->error($e->getMessage());

                return Redirect::route('profile.edit');
            }
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        flash()->success(__('Profile updated successfully.'));

        return Redirect::route('profile.edit');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password),
        ]);

        flash()->success(__('Password updated successfully.'));

        return Redirect::route('profile.edit');
    }

    public function destroyOtherSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => __('The password is incorrect.')]);
        }

        $sessionId = $request->session()->getId();

        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $sessionId)
            ->delete();

        flash()->success(__('Other sessions deleted successfully.'));

        return Redirect::route('profile.edit');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
