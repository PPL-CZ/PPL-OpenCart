<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Generator;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Schema;
interface GeneratorInterface
{
    /**
     * Generate a set of files given an object and a context.
     */
    public function generate(Schema $object, string $className, Context $context) : void;
}
