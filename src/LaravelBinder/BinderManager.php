<?php

namespace AlejoJPerez\LaravelBinder;

use Illuminate\Contracts\Foundation\Application;

class BinderManager
{
    /**
     * @param Application $app
     * @throws \Exception
     */
    public function register(Application $app)
    {
        $binders = config('alejojperez-binder.binders');

        foreach ($binders as $binder)
        {
            if( ! is_subclass_of($binder, ServiceProviderBinderManager::class) )
                throw new \Exception("Sorry, the binder [$binder] must extend [AlejoJPerez\\LaravelBinder\\ServiceProviderBinderManager].");

            app($binder)->registerServiceProviders($app);
        }
    }
}