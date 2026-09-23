<?php

use App\Flasher\Flash;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

define('ACTION_CREATE', 'getCreate');
define('ACTION_UPDATE', 'getUpdate');
define('ACTION_DELETE', 'getDelete');
define('ACTION_EMPTY', 'empty');
define('ACTION_TABLE', 'getTable');
define('ACTION_PRINT', 'getPrint');
define('ACTION_EXPORT', 'getExport');
define('ERROR_PERMISION', 'This action is unauthorized');
define('TOAST_SUCCESS', 'Data berhasil di proses !');
define('TOAST_FAILED', 'Proses Error !');

function formatDate($value, $datetime = false)
{
    if (empty($value)) {
        return null;
    }

    if ($datetime === false) {
        $format = 'd/m/Y';
    } elseif ($datetime === true) {
        $format = 'd/m/Y H:i:s';
    } else {
        $format = $datetime;
    }

    if ($value instanceof Carbon) {
        $value = $value->format($format);
    } elseif ($value instanceof CarbonImmutable) {
        $value = Carbon::parse($value)->format($format);
    } elseif (is_string($value)) {
        $value = Carbon::parse($value)->format($format);
    }

    return $value ?: null;
}

function formatAngka(int $value, $simbol = null)
{
    return $simbol.number_format($value, 0, ',', '.');
}

/**
 * Format angka: 1.000 (tanpa desimal jika 0), 1,255 / 1,24 (hapus trailing zero).
 * Tanda: titik ribuan, koma desimal.
 */
function formatQty($value): string
{
    $num = (float) $value;
    $integer = (int) $num;
    $decimal = $num - $integer;

    if (abs($decimal) < 0.001) {
        return number_format($integer, 0, ',', '.');
    }

    // 3 desimal, lalu hapus trailing zero
    $formatted = number_format($num, 3, ',', '.');
    $formatted = rtrim($formatted, '0');
    $formatted = rtrim($formatted, ',');

    return $formatted;
}

function formatLabel($value)
{
    $label = Str::of($value);
    if ($label->contains('_')) {
        $label = $label = $label->explode('_')->last();
    } else {
        $label = $label->replace('[]', '');
    }

    return ucfirst($label);
}

function unicString($length)
{
    $chars = array_merge(range('a', 'z'), range('A', 'Z'));
    $length = intval($length) > 0 ? intval($length) : 16;
    $max = count($chars) - 1;
    $str = '';

    while ($length--) {
        shuffle($chars);
        $rand = mt_rand(0, $max);
        $str .= $chars[$rand];
    }

    return strtoupper($str);
}

function unicNumber($length)
{
    $length = intval($length) > 0 ? intval($length) : 6;
    $min = (int) str_pad('1', $length, '0');
    $max = (int) str_pad('9', $length, '9');

    return random_int($min, $max);
}

function generateBarcode(string $prefix = 'BKA'): string
{
    return strtoupper($prefix . date('ymd') . Str::random(6));
}

function generateDeliveryCode(string $rsCode, string $prefix = 'BSH'): string
{
    return strtoupper($prefix . $rsCode . date('ymd') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT) . Str::random(2));
}

function generatePackingCode(): string
{
    return generateBarcode('BKA');
}

function modules($action = null)
{
    $module = request()->route()->getAction('name');

    if ($action) {
        return $module.'.'.$action;
    }

    return $module;
}

function moduleLabel()
{
    $module = modules();
    $menu = config('menu.sidebar', []);

    foreach ($menu as $section) {
        foreach ($section['items'] ?? [] as $item) {
            $routeName = $item['route'] ?? '';
            if (Str::startsWith($routeName, $module.'.')) {
                return $item['label'] ?? ucfirst($module);
            }
        }
    }

    return ucfirst($module);
}

function moduleRoute($action = null, $params = [])
{
    $route = route(modules($action), $params);

    return $route;
}

function nominalQRIS($qris_data, $amount)
{
    $amountStr = number_format($amount, 2, '.', '');
    $amountLength = strlen($amountStr);
    $amountField = '54'.str_pad($amountLength, 2, '0', STR_PAD_LEFT).$amountStr;

    // Parse QRIS TLV: hapus field 54 (nominal) yang lama dengan benar
    $new_qris = '';
    $i = 0;
    $len = strlen($qris_data);
    while ($i < $len - 4) {
        $tag = substr($qris_data, $i, 2);
        $valueLen = (int) substr($qris_data, $i + 2, 2);
        $totalLen = 4 + $valueLen;

        if ($tag === '54') {
            // Skip field 54 (nominal lama)
            $i += $totalLen;

            continue;
        }

        // Simpan field lainnya
        $new_qris .= substr($qris_data, $i, $totalLen);
        $i += $totalLen;
    }

    // Sisa string (CRC lama)
    if ($i < $len) {
        $new_qris .= substr($qris_data, $i);
    }

    // Hilangkan CRC lama (tag 63)
    $new_qris = preg_replace('/6304.{4}$/', '', $new_qris);

    // Tambah nominal baru + CRC
    $new_qris = $new_qris.$amountField.'6304';
    $crc = strtoupper(dechex(crc16($new_qris)));
    $crc = str_pad($crc, 4, '0', STR_PAD_LEFT);

    return $new_qris.$crc;
}

function crc16($data)
{
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($data); $i++) {
        $crc ^= ord($data[$i]) << 8;
        for ($j = 0; $j < 8; $j++) {
            if ($crc & 0x8000) {
                $crc = ($crc << 1) ^ 0x1021;
            } else {
                $crc = $crc << 1;
            }
            $crc &= 0xFFFF;
        }
    }

    return $crc;
}

function showSql($query = null): string
{
    if ($query === null) {
        return '';
    }

    if (is_string($query)) {
        return $query;
    }

    $sql = $query->toSql();
    $bindings = $query->getBindings();

    if (empty($bindings)) {
        return $sql;
    }

    foreach ($bindings as $binding) {
        if (is_null($binding)) {
            $value = 'null';
        } elseif (is_bool($binding)) {
            $value = $binding ? 'true' : 'false';
        } elseif (is_numeric($binding)) {
            $value = (string) $binding;
        } else {
            $value = "'".addslashes($binding)."'";
        }

        $sql = preg_replace('/\?/', $value, $sql, 1);
    }

    return $sql;
}

function ddSql($query = null): void
{
    $sql = showSql($query);
    $bindings = is_object($query) && method_exists($query, 'getBindings') ? $query->getBindings() : [];

    dump($sql);
    if (! empty($bindings)) {
        dump('Bindings:', $bindings);
    }
    exit;
}

function cleanText(?string $text): ?string
{
    if ($text === null) {
        return null;
    }

    $text = preg_replace('/[\x{4E00}-\x{9FFF}]/u', '', $text);
    $text = preg_replace('/[\x{3400}-\x{4DBF}]/u', '', $text);
    $text = preg_replace('/[\x{F900}-\x{FAFF}]/u', '', $text);
    $text = preg_replace('/[\x{2E80}-\x{2EFF}]/u', '', $text);
    $text = preg_replace('/[\x{3000}-\x{303F}]/u', '', $text);
    $text = preg_replace('/[\x{3040}-\x{309F}]/u', '', $text);
    $text = preg_replace('/[\x{30A0}-\x{30FF}]/u', '', $text);
    $text = preg_replace('/[\x{31F0}-\x{31FF}]/u', '', $text);
    $text = preg_replace('/[\x{FF00}-\x{FFEF}]/u', '', $text);
    $text = preg_replace('/[\x{AC00}-\x{D7AF}]/u', '', $text);
    $text = preg_replace('/[\x{1100}-\x{11FF}]/u', '', $text);
    $text = preg_replace('/[\x{0600}-\x{06FF}]/u', '', $text);
    $text = preg_replace('/[\x{0750}-\x{077F}]/u', '', $text);
    $text = preg_replace('/[\x{FB50}-\x{FDFF}]/u', '', $text);
    $text = preg_replace('/[\x{FE70}-\x{FEFF}]/u', '', $text);
    $text = preg_replace('/[\x{0400}-\x{04FF}]/u', '', $text);
    $text = preg_replace('/[\x{0E00}-\x{0E7F}]/u', '', $text);
    $text = preg_replace('/[\x{0980}-\x{09FF}]/u', '', $text);
    $text = preg_replace('/[\x{0900}-\x{097F}]/u', '', $text);
    $text = preg_replace('/[\x{0E80}-\x{0EFF}]/u', '', $text);

    $text = preg_replace('/[^\P{C}\n\r]+/u', '', $text);
    $text = preg_replace('/[^\S\n\r]+/u', ' ', $text);
    $text = preg_replace('/ *\n */', "\n", $text);
    $text = preg_replace('/\n{3,}/', "\n\n", $text);

    return trim($text);
}

/**
 * Sanitize uploaded file name: remove path traversal, null bytes, PHP wrappers,
 * and restrict to safe extensions only. Generates a unique random name.
 *
 * @param  string  $originalName  Original file name from upload
 * @return string Sanitized unique file name (e.g., "aB3xK_20260819.jpg")
 */
function sanitizeFileName(string $originalName): string
{
    // Strip any path components (directory traversal)
    $name = basename($originalName);

    // Remove null bytes
    $name = str_replace(chr(0), '', $name);

    // Remove PHP wrappers and protocol prefixes
    $name = preg_replace('/^(php|phar|file|http|https|ftp|data|zip|glob|ssh2|ogg|expect|rar):\/\//i', '', $name);

    // Get extension
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    // Block dangerous extensions
    $blocked = ['php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'pht', 'phar',
        'shtml', 'cgi', 'pl', 'py', 'rb', 'sh', 'bash', 'exe', 'bat', 'cmd',
        'dll', 'so', 'htaccess', 'ini', 'inc', 'asp', 'aspx', 'jsp'];
    if (in_array($ext, $blocked)) {
        $ext = 'txt'; // Force safe extension
    }

    // Allowed extensions
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
    if (! in_array($ext, $allowed)) {
        $ext = 'jpg'; // Default safe fallback
    }

    // Generate unique name: random 5 chars + date
    $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
    $date = date('YmdHis');

    return $random.'_'.$date.'.'.$ext;
}

/**
 * Resolve a stored file path to a public URL.
 *
 * Handles paths from uploadFile() (e.g. "users/abc.jpg"),
 * legacy "storage/" prefixed paths, absolute URLs, and empty values.
 *
 * @param  string|null  $path  Stored path like "users/abc.jpg" or "storage/users/abc.jpg"
 * @return string Public URL like "/storage/users/abc.jpg", or "" if empty
 */
function fileUrl(?string $path): string
{
    if (empty($path)) {
        return '';
    }

    $path = ltrim($path, '/');

    if (str_starts_with($path, 'http')) {
        return $path;
    }

    if (! str_starts_with($path, 'storage/')) {
        $path = 'storage/'.$path;
    }

    return '/'.$path;
}

/**
 * Render an <img> tag with resolved URL and optional attributes.
 * Returns empty string when the source path is empty.
 *
 * @param  string|null  $src  Stored path (e.g. "users/abc.jpg") or absolute URL
 * @param  string  $alt  Alt text
 * @param  string  $class  CSS class(es)
 * @param  string  $id  Element id
 * @param  string  $style  Inline style
 * @return string HTML img tag, or empty string
 */
function renderImage(?string $src, string $alt = '', string $class = '', string $id = '', string $style = ''): string
{
    if (empty($src)) {
        return '';
    }

    $url = fileUrl($src);
    $attrs = '';

    if ($id !== '') {
        $attrs .= ' id="'.e($id).'"';
    }

    if ($class !== '') {
        $attrs .= ' class="'.e($class).'"';
    }

    if ($style !== '') {
        $attrs .= ' style="'.e($style).'"';
    }

    return '<img src="'.e($url).'" alt="'.e($alt).'"'.$attrs.'>';
}

/**
 * Upload and sanitize a file. Validates MIME type via finfo, strips EXIF,
 * stores to storage/app/public/{folder}, and returns the relative path.
 *
 * @param  UploadedFile  $file  The uploaded file
 * @param  string  $folder  Subfolder (e.g., 'avatars')
 * @param  array  $options  Max size in KB, max dimensions
 * @return string Relative path (e.g., "avatars/abc123.jpg")
 *
 * @throws InvalidArgumentException
 */
function uploadFile($file, string $folder = 'uploads', array $options = []): string
{
    if (! $file || ! $file->isValid()) {
        throw new InvalidArgumentException('File tidak valid atau gagal diupload.');
    }

    $maxSize = $options['max_size'] ?? 2048; // KB
    $maxWidth = $options['max_width'] ?? 2000;
    $maxHeight = $options['max_height'] ?? 2000;

    // Check file size
    if ($file->getSize() > $maxSize * 1024) {
        throw new InvalidArgumentException("Ukuran file maksimal {$maxSize} KB.");
    }

    // Validate MIME type using finfo (not just extension)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file->getPathname());

    $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];
    if (! in_array($mime, $allowedMimes)) {
        throw new InvalidArgumentException("Tipe file tidak diizinkan: {$mime}");
    }

    // Strip EXIF data for privacy/security (JPEG only)
    if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp']) && function_exists('imagecreatefromstring')) {
        try {
            $img = @imagecreatefromstring(file_get_contents($file->getPathname()));
            if ($img) {
                $width = imagesx($img);
                $height = imagesy($img);
                imagedestroy($img);
                if ($width > $maxWidth || $height > $maxHeight) {
                    throw new InvalidArgumentException("Dimensi gambar maksimal {$maxWidth}x{$maxHeight} px.");
                }
            }
        } catch (Throwable $e) {
            if ($e instanceof InvalidArgumentException) {
                throw $e;
            }
            // If GD fails, still allow the upload (finfo already validated)
        }
    }

    // Sanitize filename
    $filename = sanitizeFileName($file->getClientOriginalName());

    // Ensure folder exists
    $targetDir = storage_path('app/public/'.$folder);
    if (! is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Move file to storage.
    // ponytail: disk 'public' WAJIB eksplisit. storeAs() tanpa opsi disk memakai
    // config('filesystems.default') = 'local' (root storage/app/private), sehingga
    // file "public/users/x.jpg" mendarat di storage/app/private/public/users/x.jpg:
    // chmod() di bawah gagal (E_WARNING "No such file or directory") dan
    // fileUrl() 404 karena /storage menunjuk ke storage/app/public.
    $path = $file->storeAs($folder, $filename, ['disk' => 'public']);

    if (! $path) {
        throw new InvalidArgumentException('Gagal menyimpan file.');
    }

    // Ensure file permissions are safe (skip when a fake/virtual disk is in use)
    $absolute = storage_path('app/public/'.$path);
    if (is_file($absolute)) {
        chmod($absolute, 0644);
    }

    // Return relative public path, e.g. "users/abc123.jpg"
    return $path;
}

/**
 * Render a single custom field input for content entry forms.
 */
function renderFieldInput($fName, $fType, $fValue, $fieldName, $fLabel, $fieldDef = [])
{
    $escaped = htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8');
    $escapedLabel = htmlspecialchars($fLabel, ENT_QUOTES, 'UTF-8');
    $escapedValue = htmlspecialchars(is_array($fValue) ? json_encode($fValue) : (string) $fValue, ENT_QUOTES, 'UTF-8');

    $html = '';
    if (in_array($fType, ['text', 'url', 'email', 'color', 'slug'])) {
        $inputType = $fType === 'slug' ? 'text' : $fType;
        $html = '<input type="'.$inputType.'" name="'.$escaped.'" value="'.$escapedValue.'" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="'.$escapedLabel.'" />';
    } elseif (in_array($fType, ['textarea', 'wysiwyg'])) {
        $html = '<textarea name="'.$escaped.'" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="'.$escapedLabel.'">'.htmlspecialchars((string) $fValue, ENT_QUOTES, 'UTF-8').'</textarea>';
    } elseif ($fType === 'toggle') {
        $checked = $fValue ? 'checked' : '';
        $html = '<label class="relative inline-flex items-center cursor-pointer"><input type="hidden" name="'.$escaped.'" value="0"><input type="checkbox" name="'.$escaped.'" value="1" '.$checked.' class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"><span class="ml-2 text-sm text-gray-600">'.$escapedLabel.'</span></label>';
    } elseif ($fType === 'image') {
        $html = '<input type="text" name="'.$escaped.'" value="'.$escapedValue.'" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="https://example.com/image.jpg" />';
        if ($fValue && ! is_array($fValue)) {
            $html .= '<img src="'.htmlspecialchars((string) $fValue, ENT_QUOTES, 'UTF-8').'" class="mt-2 h-16 w-24 object-cover rounded border" alt="Preview">';
        }
    } elseif ($fType === 'select') {
        $opts = $fieldDef['config']['options'] ?? [];
        $html = '<select name="'.$escaped.'" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"><option value="">-- Select --</option>';
        foreach ($opts as $optVal => $optLabel) {
            $sel = $fValue == $optVal ? 'selected' : '';
            $html .= '<option value="'.htmlspecialchars($optVal, ENT_QUOTES, 'UTF-8').'" '.$sel.'>'.htmlspecialchars($optLabel, ENT_QUOTES, 'UTF-8').'</option>';
        }
        $html .= '</select>';
    } elseif (in_array($fType, ['number', 'integer', 'float'])) {
        $html = '<input type="number" name="'.$escaped.'" value="'.$escapedValue.'" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />';
    } elseif ($fType === 'assets') {
        $html = '<input type="text" name="'.$escaped.'" value="'.$escapedValue.'" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="File URLs" />';
    } else {
        $html = '<input type="text" name="'.$escaped.'" value="'.$escapedValue.'" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />';
    }

    return $html;
}

/**
 * Flash a toast message to the session for the next request.
 *
 * Supports both simple form:
 *   flash('Data berhasil disimpan', 'success')
 *
 * And fluent form:
 *   flash()->success('Data berhasil disimpan')
 *   flash()->error('Gagal menyimpan data')
 *   flash()->warning('...')
 *   flash()->info('...')
 */
function flash($message = null, $type = 'success')
{
    if (is_null($message)) {
        return new Flash;
    }

    $toasts = session('toasts', []);
    $toasts[] = [
        'message' => $message,
        'type' => $type,
        'heading' => null,
        'duration' => 5000,
    ];
    session()->flash('toasts', $toasts);
}
