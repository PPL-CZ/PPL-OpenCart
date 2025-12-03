<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\OpenApiSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Naming;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess as BaseClassGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\JsonSchema\AllOfGuesser as BaseAllOfGuesser;
use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\ClassGuess;
use PPLCZVendor\Symfony\Component\Serializer\SerializerInterface;
class AllOfGuesser extends BaseAllOfGuesser
{
    /** @var string */
    private $schemaClass;
    public function __construct(SerializerInterface $serializer, Naming $naming, string $schemaClass)
    {
        parent::__construct($serializer, $naming);
        $this->schemaClass = $schemaClass;
    }
    protected function createClassGuess($object, $reference, $name, $extensions) : BaseClassGuess
    {
        return new ClassGuess($object, $reference, $this->naming->getClassName($name), $extensions);
    }
    protected function getSchemaClass() : string
    {
        return $this->schemaClass;
    }
}
