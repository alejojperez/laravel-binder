<?php
/**
 * Created by Alejandro Perez on 8/10/16
 * github page: https://github.com/alejojperez
 */

namespace AlejoJPerez\LaravelBinder;

use Illuminate\Support\ServiceProvider;

class LaravelBinderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__."/config.php" => config_path('alejojperez-binder.php'),
        ], "config");
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // TODO: Implement register() method.
    }
}