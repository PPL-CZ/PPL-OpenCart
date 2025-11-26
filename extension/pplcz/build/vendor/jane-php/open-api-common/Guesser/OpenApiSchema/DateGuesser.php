<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\OpenApiSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\JsonSchema\DateGuesser as BaseDateGuesser;
class DateGuesser extends BaseDateGuesser
{
    use SchemaClassTrait;
    public function __construct(string $schemaClass, string $dateFormat = 'Y-m-d', ?bool $preferInterface = null)
    {
        parent::__construct($dateFormat, $preferInterface);
        $this->schemaClass = $schemaClass;
    }
}
