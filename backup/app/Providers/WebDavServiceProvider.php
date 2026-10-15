<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;

class WebDavServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('webdav', function ($app, $config) {
            $adapter = new WebDAVAdapter(
                new Client($config)
            );

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }

    public function register()
    {
    }
}
