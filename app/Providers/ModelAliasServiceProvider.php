<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class ModelAliasServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Path to your models directory
        $modelsPath = app_path('Models');

        // Check if the directory exists to prevent errors
        if (! File::isDirectory($modelsPath)) {
            return;
        }

        // Get all PHP files in the Models directory
        $files = File::allFiles($modelsPath);

        foreach ($files as $file) {
            // Get the relative path to handle subdirectories (e.g., "Tenant/User.php")
            $relativePath = $file->getRelativePathname();

            // Convert the file path to a fully qualified class name
            // e.g., "Tenant/User.php" -> "App\Models\Tenant\User"
            $className = 'App\\Models\\'.str_replace(['/', '.php'], ['\\', ''], $relativePath);

            if (class_exists($className)) {
                // Get just the class name without the namespace (e.g., "User")
                $alias = class_basename($className);

                // Create the global alias if it doesn't already exist
                if (! class_exists($alias)) {
                    class_alias($className, $alias);
                }
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
