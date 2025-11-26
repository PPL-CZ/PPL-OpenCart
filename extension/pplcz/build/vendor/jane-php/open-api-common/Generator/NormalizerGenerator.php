<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\NormalizerGenerator as BaseNormalizerGenerator;
use PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Normalizer\DenormalizerGenerator as DenormalizerGeneratorTrait;
use PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Normalizer\NormalizerGenerator as NormalizerGeneratorTrait;
class NormalizerGenerator extends BaseNormalizerGenerator
{
    use DenormalizerGeneratorTrait;
    use NormalizerGeneratorTrait;
}
