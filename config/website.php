<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Website Configuration
    |--------------------------------------------------------------------------
    |
    | General website settings: name, description, logo, colors, and more.
    | These values can be overridden via the admin settings panel.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    'tagline' => env('WEBSITE_TAGLINE', 'Web Application'),

    'description' => env('WEBSITE_DESCRIPTION', 'A powerful web application built with Laravel.'),

    'alamat' => env('WEBSITE_ALAMAT', ''),

    'telepon' => env('WEBSITE_TELEPON', ''),

    'email' => env('WEBSITE_EMAIL', ''),

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    */

    'logo' => env('WEBSITE_LOGO', '/favicon.svg'),

    'favicon' => env('WEBSITE_FAVICON', '/favicon.ico'),

    /*
    |--------------------------------------------------------------------------
    | Theme Colors (Material Design 3)
    |--------------------------------------------------------------------------
    |
    | These colors drive the entire UI theme. Changing 'primary' will update
    | all buttons, links, sidebar active states, and accent elements.
    |
    | Format: hex color without # (or with #, both accepted)
    |
    */

    'colors' => [
        'primary' => env('WEBSITE_COLOR_PRIMARY', '#00288e'),
        'on_primary' => env('WEBSITE_COLOR_ON_PRIMARY', '#ffffff'),
        'primary_container' => env('WEBSITE_COLOR_PRIMARY_CONTAINER', '#1e40af'),
        'on_primary_container' => env('WEBSITE_COLOR_ON_PRIMARY_CONTAINER', '#a8b8ff'),
        'primary_fixed' => env('WEBSITE_COLOR_PRIMARY_FIXED', '#dde1ff'),
        'primary_fixed_dim' => env('WEBSITE_COLOR_PRIMARY_FIXED_DIM', '#b8c4ff'),
        'surface' => env('WEBSITE_COLOR_SURFACE', '#f7f9fb'),
        'surface_container' => env('WEBSITE_COLOR_SURFACE_CONTAINER', '#eceef0'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media / Footer
    |--------------------------------------------------------------------------
    */

    'social' => [
        'facebook' => env('WEBSITE_SOCIAL_FACEBOOK', ''),
        'instagram' => env('WEBSITE_SOCIAL_INSTAGRAM', ''),
        'twitter' => env('WEBSITE_SOCIAL_TWITTER', ''),
        'youtube' => env('WEBSITE_SOCIAL_YOUTUBE', ''),
        'tiktok' => env('WEBSITE_SOCIAL_TIKTOK', ''),
    ],

    'footer_text' => env('WEBSITE_FOOTER_TEXT', ''),
];
