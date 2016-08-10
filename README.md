# laravel-binder
A package to simplify the process of binding an interface to its implementation in the IoC Container 

###Include all the commands to the console

```php
// app/Console/Kernel.php

...
protected $commands = [
    \AlejoJPerez\LaravelBinder\LaravelBinderInstallCommand::class,
];
...
```