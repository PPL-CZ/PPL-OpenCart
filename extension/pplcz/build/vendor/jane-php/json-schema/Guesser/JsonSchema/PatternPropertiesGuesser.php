<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareTrait;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\PatternMultipleType;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\TypeGuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Registry;
class PatternPropertiesGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;
    /**
     * {@inheritdoc}
     */
    public function supportObject($object) : bool
    {
        if (!$object instanceof JsonSchema) {
            return \false;
        }
        if ('object' !== $object->getType()) {
            return \false;
        }
        if (null !== $object->getProperties()) {
            return \false;
        }
        if (!$object->getPatternProperties() instanceof \ArrayObject || 0 == \count($object->getPatternProperties())) {
            return \false;
        }
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function guessType($object, string $name, string $reference, Registry $registry) : Type
    {
        $type = new PatternMultipleType($object);
        foreach ($object->getPatternProperties() as $pattern => $patternProperty) {
            $type->addType($pattern, $this->chainGuesser->guessType($patternProperty, $name, $reference . '/patternProperties/' . $pattern, $registry));
        }
        return $type;
    }
}
