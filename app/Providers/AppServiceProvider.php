<?php

namespace App\Providers;

use App\Boost\Agents\CustomAgent;
use App\Events\NotificationSent;
use App\Listeners\SendNotificationViaCentrifugo;
use App\Models\Menu;
use Buki\AutoRoute\AutoRoute;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Boost\BoostManager;
use Livewire\Blaze\Blaze;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ponytail: izniburak/laravel-auto-routes assumes App\Http\Controllers only.
        // Modules\Cms uses FQCN (Modules\Cms\...), so vendor resolveControllerName would
        // build App\Http\Controllers\Modules\Cms\... and throw ReflectionException.
        // FixedAutoRoute short-circuits when class_exists(FQCN). We replace the vendor
        // singleton instance and re-declare the Router::macro to close over $fixed
        // directly (not $app[AutoRoute::class] which vendor could re-bind later).
        // This survives composer install unlike a raw vendor edit.
        $fixed = new \App\Support\FixedAutoRoute($this->app);
        $fixed->setConfigurations($this->app['config']->get('auto-route', []));
        $this->app->instance(AutoRoute::class, $fixed);
        $this->app['router']->macro('auto', function (string $prefix, string $controller, array $options = []) use ($fixed) {
            return $fixed->auto($prefix, $controller, $options);
        });

        // Register custom Zoo Code agent for Laravel Boost (CustomAgent)
        // ponytail: boost doesn't natively support Zoo Code, custom agent needed. Remove if upstream adds support.
        if (app()->bound(BoostManager::class)) {
            try {
                $this->app->make(BoostManager::class)->registerAgent('zoo_code', CustomAgent::class);
            } catch (\InvalidArgumentException $e) {
                // Already registered - ignore
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerMacros();

        // Ensure Zoo Code agent (CustomAgent) registered even if register() called before BoostManager binding
        if (app()->bound(BoostManager::class)) {
            try {
                $this->app->make(BoostManager::class)->registerAgent('zoo_code', CustomAgent::class);
            } catch (\InvalidArgumentException $e) {
                // Already registered
            } catch (\Exception $e) {
                // Boost disabled
            }
        }

        Event::listen(NotificationSent::class, SendNotificationViaCentrifugo::class);

        // URL::forceScheme('https');
        // Blaze::optimize()->in(resource_path('views/components'));

        Blade::directive('bind', function ($expression) {
            return "<?php
                global \$activeBladeModel;
                \$activeBladeModel = $expression;
            ?>";
        });

        Blade::directive('endbind', function () {
            return '<?php
                global $activeBladeModel;
                $activeBladeModel = null;
            ?>';
        });

        // CMS Helper Directives
        Blade::directive('cmsField', function ($expression) {
            return "<?php echo \\App\\Helpers\\CmsHelper::getField($expression); ?>";
        });

        // Share main menu and footer menu with all frontend views
        View::composer('frontend.*', function ($view) {
            $view->with('menu', Menu::getByLocation('main'));
            $view->with('footerMenu', Menu::getByLocation('footer'));
        });
    }

    protected function registerMacros(): void
    {
        $macro = function ($callback = null) {
            $sql = $this->toSql();
            $bindings = $this->getBindings();

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

            if ($callback) {
                $callback($sql);

                return $this;
            }

            return $sql;
        };

        QueryBuilder::macro('showSql', $macro);
        EloquentBuilder::macro('showSql', $macro);
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
