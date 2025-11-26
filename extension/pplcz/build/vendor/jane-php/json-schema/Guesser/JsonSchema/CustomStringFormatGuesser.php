<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\CustomObjectType;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\TypeGuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Registry;
class CustomStringFormatGuesser implements GuesserInterface, TypeGuesserInterface
{
    /**
     * @var array<string, string> key: format, value: classname of the normalizer
     */
    protected $mapping;
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }
    public function supportObject($object) : bool
    {
        $class = $this->getSchemaClass();
        return $object instanceof $class && 'string' === $object->getType() && \array_key_exists($object->getFormat(), $this->mapping);
    }
    public function guessType($object, string $name, string $reference, Registry $registry) : Type
    {
        return new CustomObjectType($object, $this->mapping[$object->getFormat()], []);
    }
    protected function getSchemaClass() : string
    {
        return JsonSchema::class;
    }
}
