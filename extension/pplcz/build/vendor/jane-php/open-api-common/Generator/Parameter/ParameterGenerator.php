<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Parameter;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\Jane\Component\JsonSchema\Tools\InflectorTrait;
use PPLCZVendor\PhpParser\Node;
use PPLCZVendor\PhpParser\Parser;
abstract class ParameterGenerator
{
    use InflectorTrait;
    /**
     * @var Parser
     */
    protected $parser;
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }
    public function generateMethodParameter($parameter, Context $context, string $reference) : ?Node\Param
    {
        return null;
    }
    public function generateMethodDocParameter($parameter, Context $context, string $reference) : string
    {
        return '';
    }
    /**
     * @return Node\Expr[]
     */
    protected function generateInputParamArguments($parameter) : array
    {
        return [];
    }
}
