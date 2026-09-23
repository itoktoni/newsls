<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class WebsiteSettingController extends Controller
{
    public function index(): View
    {
        $settings = config('website');

        return view('pages.settings.website', [
            'settings' => $settings,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:512'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'footer_text' => ['nullable', 'string'],
        ]);

        // Robust: public/storage adalah junction ke storage/app/public — gunakan File facade yang handle junction + race
        // Fallback ke storage_path jika junction belum ada (mis. fresh clone tanpa storage:link)
        $dir = public_path('storage/website');
        $storageDir = storage_path('app/public/website');

        // Pastikan target storage ada dulu (sumber junction)
        File::ensureDirectoryExists($storageDir, 0755, true);

        // Pastikan junction/link ada — jika tidak, fallback pakai storageDir langsung
        if (! is_dir($dir)) {
            // Coba buat junction link jika hilang (Windows) atau symlink
            if (! File::exists(public_path('storage'))) {
                try {
                    Artisan::call('storage:link');
                } catch (\Throwable $e) {
                }
            }
            // Tetap ensure public dir (akan lewat junction jika sudah ada)
            try {
                File::ensureDirectoryExists($dir, 0755, true);
            } catch (\Throwable $e) {
                // Fallback: pakai storageDir langsung agar upload tetap jalan meski public/storage bermasalah
                $dir = $storageDir;
                File::ensureDirectoryExists($dir, 0755, true);
            }
        }

        $envUpdates = [];

        // Text fields → .env
        $envUpdates['APP_NAME'] = $validated['name'];
        $envUpdates['WEBSITE_TAGLINE'] = $validated['tagline'] ?? '';
        $envUpdates['WEBSITE_DESCRIPTION'] = $validated['description'] ?? '';
        $envUpdates['WEBSITE_ALAMAT'] = $validated['alamat'] ?? '';
        $envUpdates['WEBSITE_TELEPON'] = $validated['telepon'] ?? '';
        $envUpdates['WEBSITE_EMAIL'] = $validated['email'] ?? '';
        $envUpdates['APP_LOCATION'] = $validated['location'] ?? '';
        $envUpdates['WEBSITE_FOOTER_TEXT'] = $validated['footer_text'] ?? '';

        // Color → .env
        if (! empty($validated['primary_color'])) {
            $envUpdates['WEBSITE_COLOR_PRIMARY'] = $validated['primary_color'];
        }

        // Logo file upload
        if ($request->hasFile('logo')) {
            $this->deleteOld(config('website.logo'));
            $envUpdates['WEBSITE_LOGO'] = 'storage/website/'.$this->storeFile($request->file('logo'), $dir);
        } elseif (! empty($validated['remove_logo'])) {
            $this->deleteOld(config('website.logo'));
            $envUpdates['WEBSITE_LOGO'] = '';
        }

        // Favicon file upload
        if ($request->hasFile('favicon')) {
            $this->deleteOld(config('website.favicon'));
            $envUpdates['WEBSITE_FAVICON'] = 'storage/website/'.$this->storeFile($request->file('favicon'), $dir);
        } elseif (! empty($validated['remove_favicon'])) {
            $this->deleteOld(config('website.favicon'));
            $envUpdates['WEBSITE_FAVICON'] = '';
        }

        $this->writeToEnv($envUpdates);

        flash()->success('Website settings saved.');

        return Redirect::route('settings.website');
    }

    private function writeToEnv(array $updates): void
    {
        $path = base_path('.env');
        $content = file_get_contents($path);

        foreach ($updates as $key => $value) {
            $escaped = str_replace('"', '\\"', $value);
            $line = $key.'="'.$escaped.'"';

            if (preg_match('/^'.preg_quote($key, '/').'.*/m', $content)) {
                $content = preg_replace('/^'.preg_quote($key, '/').'.*/m', $line, $content);
            } else {
                $content .= PHP_EOL.$line.PHP_EOL;
            }
        }

        file_put_contents($path, $content);
        Artisan::call('config:clear');
    }

    private function storeFile($file, string $dir): string
    {
        $name = uniqid().'_'.preg_replace('/[^a-zA-Z0-9.]/', '', $file->getClientOriginalName());
        $file->move($dir, $name);

        return $name;
    }

    private function deleteOld(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $file = public_path($path);
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
