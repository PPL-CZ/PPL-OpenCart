<?php

namespace PPLCZVendor\Jane\Component\OpenApi3\Generator;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\MediaType;
use PPLCZVendor\PhpParser\Node;
interface RequestBodyContentGeneratorInterface
{
    public function getTypes(MediaType $content, string $reference, Context $context) : array;
    public function getTypeCondition(MediaType $content, string $reference, Context $context) : Node;
    public function getSerializeStatements(MediaType $content, string $contentType, string $reference, Context $context) : array;
}
