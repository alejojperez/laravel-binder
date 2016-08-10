<?php

namespace AlejoJPerez\LaravelBinder\Contracts;

use Illuminate\Contracts\Foundation\Application;

interface Binder
{
    public function registerServiceProviders(Application $app);
}