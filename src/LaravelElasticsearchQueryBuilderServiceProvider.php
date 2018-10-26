<?php

namespace Shisun\LaravelElasticsearchQueryBuilder;

use Illuminate\Support\ServiceProvider;

class LaravelElasticsearchQueryBuilderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'shisun');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'shisun');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelelasticsearchquerybuilder.php', 'laravelelasticsearchquerybuilder');

        // Register the service the package provides.
        $this->app->singleton('laravelelasticsearchquerybuilder', function ($app) {
            return new LaravelElasticsearchQueryBuilder;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelelasticsearchquerybuilder'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravelelasticsearchquerybuilder.php' => config_path('laravelelasticsearchquerybuilder.php'),
        ], 'laravelelasticsearchquerybuilder.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/shisun'),
        ], 'laravelelasticsearchquerybuilder.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/shisun'),
        ], 'laravelelasticsearchquerybuilder.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/shisun'),
        ], 'laravelelasticsearchquerybuilder.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
