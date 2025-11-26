<?php

namespace PPLCZVendor\Jane\Component\JsonSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Console\Command\DumpConfigCommand;
use PPLCZVendor\Jane\Component\JsonSchema\Console\Command\GenerateCommand;
use PPLCZVendor\Jane\Component\JsonSchema\Console\Loader\ConfigLoader;
use PPLCZVendor\Jane\Component\JsonSchema\Console\Loader\SchemaLoader;
use PPLCZVendor\Symfony\Component\Console\Application as BaseApplication;
class Application extends BaseApplication
{
    public const VERSION = '6.x-dev';
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Jane', self::VERSION);
        $this->boot();
    }
    protected function boot() : void
    {
        $configLoader = new ConfigLoader();
        $this->add(new GenerateCommand($configLoader, new SchemaLoader()));
        $this->add(new DumpConfigCommand($configLoader));
    }
}
