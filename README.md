# laravel-binder
A package to simplify the process of binding an interface to its implementation in the IoC Container 

###Include the service provider

```php
// config/app.php

return [

    "providers" => [
        ...
        AlejoJPerez\LaravelBinder\LaravelBinderServiceProvider::class,
        ...
    ]

];
```

###Publish the package configuration
```bash
php artisan vendor:publish --provider="AlejoJPerez\LaravelBinder\LaravelBinderServiceProvider" --tag="config"
```

###Include all the commands to the console

```php
// app/Console/Kernel.php

...
protected $commands = [
    \AlejoJPerez\LaravelBinder\LaravelBinderInstallCommand::class,
];
...
```