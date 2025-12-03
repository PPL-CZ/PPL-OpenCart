<?php

declare (strict_types=1);
namespace PPLCZVendor\Doctrine\Inflector;

interface WordInflector
{
    public function inflect(string $word) : string;
}
