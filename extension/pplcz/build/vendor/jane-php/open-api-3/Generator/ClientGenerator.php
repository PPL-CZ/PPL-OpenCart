<?php

namespace PPLCZVendor\Jane\Component\OpenApi3\Generator;

use PPLCZVendor\Jane\Component\OpenApi3\Generator\Client\ServerPluginGenerator;
use PPLCZVendor\Jane\Component\OpenApiCommon\Generator\ClientGenerator as BaseClientGenerator;
class ClientGenerator extends BaseClientGenerator
{
    use ServerPluginGenerator;
}
