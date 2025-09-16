<?php

namespace PreviewLinks;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Route;
use PreviewLinks\Models\PreviewLink;
use PreviewLinks\Commands\CleanupExpiredLinksCommand;
use Statamic\Facades\CP\Nav;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'preview-links');
        
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupExpiredLinksCommand::class,
            ]);
        }
        
        // Register routes
        $this->registerWebRoutes();
        $this->registerCPRoutes();
        
        // Extend Control Panel navigation
        $this->extendCP();
    }

    public function register()
    {
        //
    }

    protected function registerWebRoutes()
    {
        Route::group([
            'prefix' => 'preview',
            'middleware' => 'web'
        ], function () {
            Route::get('{token}', 'PreviewLinks\Http\Controllers\PreviewController@show')
                ->name('preview-links.show');
        });
    }

    protected function registerCPRoutes()
    {
        Route::group([
            'prefix' => 'cp/preview-links',
            'middleware' => ['statamic.cp.authenticated']
        ], function () {
            Route::get('/', 'PreviewLinks\Http\Controllers\CPController@index')
                ->name('preview-links.cp.index');
            Route::post('generate', 'PreviewLinks\Http\Controllers\CPController@generate')
                ->name('preview-links.cp.generate');
            Route::delete('{id}', 'PreviewLinks\Http\Controllers\CPController@destroy')
                ->name('preview-links.cp.destroy');
        });
    }

    protected function extendCP()
    {
        Nav::extend(function ($nav) {
            $nav->create('Preview Links')
                ->section('Tools')
                ->route('preview-links.cp.index')
                ->icon('external-link');
        });
    }
}