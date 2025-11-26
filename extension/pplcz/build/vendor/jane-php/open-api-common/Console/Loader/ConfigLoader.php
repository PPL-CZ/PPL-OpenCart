<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Console\Loader;

use PPLCZVendor\Jane\Component\JsonSchema\Console\Loader\ConfigLoader as BaseConfigLoader;
use PPLCZVendor\Jane\Component\JsonSchema\Console\Loader\ConfigLoaderInterface;
class ConfigLoader extends BaseConfigLoader implements ConfigLoaderInterface
{
    public function fileKey() : string
    {
        return 'openapi-file';
    }
    protected function resolveConfigurationRequired() : array
    {
        return [$this->fileKey(), 'namespace', 'directory'];
    }
    protected function resolveConfigurationDefaults() : array
    {
        return \array_merge(parent::resolveConfigurationDefaults(), ['whitelisted-paths' => null, 'endpoint-generator' => null, 'custom-query-resolver' => [], 'throw-unexpected-status-code' => \false]);
    }
}
