<?php

namespace AlejoJPerez\LaravelBinder;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

abstract class ServiceProviderBinderManager
{
    /**
     * @var string
     */
    protected $serviceProviderInterface = \Illuminate\Support\ServiceProvider::class;

    /**
     * @var Str
     */
    private $str;

    /**
     * ServiceProviderBinderManager constructor.
     * @param Str $str
     */
    public function __construct(Str $str)
    {
        $this->str = $str;
    }

    /**
     * @param Application $app
     * @throws \Exception
     */
    public function registerServiceProviders(Application $app)
    {
        // Check if service provider binder manager posses the a properties called [$providersLocation] and [$providersNamespace]
        if( property_exists($this, 'providersLocation') && property_exists($this, 'providersNamespace') ) {

            $serviceProviders = $this->getValidServiceProviders($this->providersNamespace, $this->providersLocation, $this->serviceProviderInterface);

            foreach ($serviceProviders as $serviceProvider)
                $app->register($serviceProvider);

        } else {
            throw new \Exception('Sorry, the service provider binder manager ['.get_class($this).'] must have a property called [$providersLocation] and [$providersNamespace]');
        }
    }

    /**
     * @param $namespace
     * @param $path
     * @param $implements
     * @return array
     */
    protected function getValidServiceProviders($namespace, $path, $implements)
    {
        return $this->getValidFiles( config('alejojperez-laravel-binder.service_providers_ends_with') , $namespace, $path, $implements);
    }

    /**
     * @param $endsWith
     * @param $namespace
     * @param $path
     * @param $implements
     * @return array
     */
    protected function getValidFiles($endsWith, $namespace, $path, $implements)
    {
        // Get all the files
        $allFiles = scandir($path);

        // Check is a file and a Binder
        $validFiles = [];
        foreach($allFiles as $file)
        {
            if(is_file($file) || $this->str->endsWith($file, $endsWith))
                $validFiles[] = $file;
        }

        // Check there are valid binders
        if(count($validFiles) < 1)
            return [];
        else
        {
            // Itinerate all the valid files and get the classes
            $returnClasses = [];
            foreach($validFiles as $validFile)
            {
                $phpCode = file_get_contents($path.'/'.$validFile);
                $classes = $this->getPhpClasses($phpCode);

                // Get only the classes in the name space
                $namespaceClasses = $this->getClassesInNamespace($classes, $namespace);

                // Check if the classes in the namespace implements the binder interface
                foreach($namespaceClasses as $class)
                {
                    $reflectionClass = new \ReflectionClass($class);

                    if($reflectionClass->implementsInterface($implements))
                        $returnClasses[] = $reflectionClass->getName();
                }
            }

            return $returnClasses;
        }
    }

    /**
     * @param $phpCode
     * @return array
     */
    protected function getPhpClasses($phpCode) {
        $classes = array();

        $namespace = 0;
        $tokens = token_get_all($phpCode);
        $count = count($tokens);
        $dlm = false;
        for ($i = 2; $i < $count; $i++) {
            if ((isset($tokens[$i - 2][1]) && ($tokens[$i - 2][1] == "phpnamespace" || $tokens[$i - 2][1] == "namespace")) ||
                ($dlm && $tokens[$i - 1][0] == T_NS_SEPARATOR && $tokens[$i][0] == T_STRING)) {
                if (!$dlm) $namespace = 0;
                if (isset($tokens[$i][1])) {
                    $namespace = $namespace ? $namespace . "\\" . $tokens[$i][1] : $tokens[$i][1];
                    $dlm = true;
                }
            }
            elseif ($dlm && ($tokens[$i][0] != T_NS_SEPARATOR) && ($tokens[$i][0] != T_STRING)) {
                $dlm = false;
            }
            if (($tokens[$i - 2][0] == T_CLASS || (isset($tokens[$i - 2][1]) && $tokens[$i - 2][1] == "phpclass"))
                && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $class_name = $tokens[$i][1];
                if (!isset($classes[$namespace])) $classes[$namespace] = array();
                $classes[$namespace][] = $class_name;
            }
        }
        return $classes;
    }

    /**
     * @param $classes
     * @param $namespace
     * @return array
     */
    protected function getClassesInNamespace($classes, $namespace)
    {
        $returnClasses = [];

        if(isset($classes[$namespace]))
        {
            foreach($classes[$namespace] as $class)
            {
                $returnClasses[] = $namespace."\\".$class;
            }
        }

        return $returnClasses;
    }
}