<?php

namespace Modules\Cms\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Modules\Cms\Console\CmsImportOrbitCommand;
use Modules\Cms\Models\Category;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Media;
use Modules\Cms\Models\Menu;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Tag;
use Modules\Cms\Models\Type;
use Modules\Cms\Policies\CategoryPolicy;
use Modules\Cms\Policies\ContentPolicy;
use Modules\Cms\Policies\FieldPolicy;
use Modules\Cms\Policies\MediaPolicy;
use Modules\Cms\Policies\MenuPolicy;
use Modules\Cms\Policies\SectionPolicy;
use Modules\Cms\Policies\TagPolicy;
use Modules\Cms\Policies\TypePolicy;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CmsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Cms';

    protected string $nameLower = 'cms';

    protected array $commands = [
        CmsImportOrbitCommand::class,
    ];

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Gate::policy(Type::class, TypePolicy::class);
        Gate::policy(Section::class, SectionPolicy::class);
        Gate::policy(Field::class, FieldPolicy::class);
        Gate::policy(Content::class, ContentPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);
        Gate::policy(Menu::class, MenuPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);

        View::composer('cms::frontend.*', function ($view) {
            $view->with('menu', Menu::getByLocation('main'));
            $view->with('footerMenu', Menu::getByLocation('footer'));
        });
    }
}
