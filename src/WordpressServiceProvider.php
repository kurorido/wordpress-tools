<?php

namespace Roliroli\WordpressTools;

use Illuminate\Support\ServiceProvider;
use Roliroli\WordpressTools\PostTransformer;
use Roliroli\WordpressTools\CategoryTransformer;

class WordpressServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PostTransformer::class, function () {
            return function ($config) {
                return new PostTransformer($config);
            };
        });

        $this->app->singleton(CategoryTransformer::class, function () {
            return new CategoryTransformer();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [PostTransformer::class, CategoryTransformer::class];
    }
}
