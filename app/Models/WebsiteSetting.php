<?php

namespace App\Models;

class WebsiteSetting
{
    public static function field_name(): string
    {
        return 'name';
    }

    public static function merged(): array
    {
        return config('website');
    }

    public static function primaryColor(): string
    {
        $settings = static::merged();

        return $settings['colors']['primary'] ?? '#00288e';
    }

    public static function fileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'public/')) {
            return '/storage/'.substr($path, 7);
        }

        if (str_starts_with($path, 'storage/')) {
            return '/storage/'.substr($path, 8);
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return '/'.ltrim($path, '/');
    }

    public static function generatePalette(string $hex): array
    {
        $hex = ltrim($hex, '#');

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch ($max) {
                case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g: $h = ($b - $r) / $d + 2;
                    break;
                case $b: $h = ($r - $g) / $d + 4;
                    break;
                default: $h = 0;
            }
            $h = round($h * 60);
        }

        $s = round($s * 100);
        $l = round($l * 100);

        return [
            'primary' => "hsl({$h}, {$s}%, {$l}%)",
            'on_primary' => $l > 50 ? "hsl({$h}, 10%, 10%)" : "hsl({$h}, 10%, 98%)",
            'primary_container' => "hsl({$h}, {$s}%, ".min($l + 25, 90).'%)',
            'on_primary_container' => $l > 50 ? "hsl({$h}, 30%, 15%)" : "hsl({$h}, 20%, 90%)",
            'primary_fixed' => "hsl({$h}, {$s}%, ".min($l + 35, 95).'%)',
            'primary_fixed_dim' => "hsl({$h}, {$s}%, ".min($l + 30, 90).'%)',
        ];
    }
}
