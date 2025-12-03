<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Tools;

use PPLCZVendor\Doctrine\Inflector\Inflector;
use PPLCZVendor\Doctrine\Inflector\InflectorFactory;
trait InflectorTrait
{
    private $inflector;
    protected function getInflector() : Inflector
    {
        if (null === $this->inflector) {
            $this->inflector = InflectorFactory::create()->build();
        }
        return $this->inflector;
    }
}
