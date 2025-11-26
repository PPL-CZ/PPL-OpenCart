<?php

declare (strict_types=1);
namespace PPLCZVendor\Jane\Component\OpenApiRuntime\Client;

use PPLCZVendor\Symfony\Component\OptionsResolver\Options;
interface CustomQueryResolver
{
    public function __invoke(Options $options, $value);
}
