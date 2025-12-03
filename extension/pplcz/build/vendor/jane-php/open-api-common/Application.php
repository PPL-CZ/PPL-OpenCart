<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon;

use PPLCZVendor\Jane\Component\JsonSchema\Application as JsonSchemaApplication;
use PPLCZVendor\Jane\Component\OpenApiCommon\Console\Command\DumpConfigCommand;
use PPLCZVendor\Jane\Component\OpenApiCommon\Console\Command\GenerateCommand;
use PPLCZVendor\Jane\Component\OpenApiCommon\Console\Loader\ConfigLoader;
use PPLCZVendor\Jane\Component\OpenApiCommon\Console\Loader\OpenApiMatcher;
use PPLCZVendor\Jane\Component\OpenApiCommon\Console\Loader\SchemaLoader;
class Application extends JsonSchemaApplication
{
    protected function boot() : void
    {
        $configLoader = new ConfigLoader();
        $this->add(new GenerateCommand($configLoader, new SchemaLoader(), new OpenApiMatcher()));
        $this->add(new DumpConfigCommand($configLoader));
    }
}
