<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $envPath = base_path('.env');
        $envContent = File::exists($envPath) ? File::get($envPath) : '';

        return view('pages.settings.env', [
            'envContent' => $envContent,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $request->validate([
            'env_content' => ['required', 'string'],
        ]);

        $envPath = base_path('.env');
        $content = $request->env_content;

        if (! File::isWritable($envPath)) {
            flash()->error(__('The .env file is not writable.'));

            return Redirect::route('settings.env');
        }

        File::put($envPath, $content);

        flash()->success(__('Settings saved successfully. Some changes may require a server restart.'));

        return Redirect::route('settings.env');
    }
}
