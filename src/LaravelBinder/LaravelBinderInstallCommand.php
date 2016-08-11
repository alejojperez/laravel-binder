<?php

namespace AlejoJPerez\LaravelBinder;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;

class LaravelBinderInstallCommand extends Command
{
    /**
     * @var \Illuminate\Filesystem\Filesystem|Filesystem
     */
    protected $files;

    /**
     * Create a new controller creator command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alejojperez-binder:install {location?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a copy of the package\'s config file in the config folder, and creates a new provider called BinderServiceProvider.php';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $location = $this->argument('location') ? $this->argument('location') : $this->laravel->basePath().'/app/Providers';

        $permission = $this->confirm('A new php class called BinderServiceProvider will be created in '.$location.'. Should we Proceed? [y/n]', 'n');

        if($permission)
        {
            $this->createBinder($location);
            $this->info('Remember to set the right namespace in the BinderServiceProvider');
        }
    }

    /**
     * @return mixed
     */
    public function getStub()
    {
        return __DIR__.'/stubs/binder-service-provider.stub';
    }

    /**
     * @param $location
     * @return bool
     */
    protected function createBinder($location)
    {
        if ($this->alreadyExists($location)) {
            $this->error('BinderServiceProvider already exists in the specified location!');

            return false;
        }

        $this->makeDirectory($location);

        $this->files->put($location, $this->buildClass());

        $this->info('Package installed successfully.');
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $location
     * @return bool
     */
    protected function alreadyExists($location)
    {
        return $this->files->exists($location);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Build the class with the given name.
     *
     * @return string
     */
    protected function buildClass()
    {
        return $this->files->get($this->getStub());
    }
}
