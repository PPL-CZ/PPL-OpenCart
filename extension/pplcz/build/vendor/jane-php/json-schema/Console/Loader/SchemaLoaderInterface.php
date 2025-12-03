<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Console\Loader;

use PPLCZVendor\Jane\Component\JsonSchema\Registry\SchemaInterface;
interface SchemaLoaderInterface
{
    public function resolve(string $schema, array $options = []) : SchemaInterface;
}
