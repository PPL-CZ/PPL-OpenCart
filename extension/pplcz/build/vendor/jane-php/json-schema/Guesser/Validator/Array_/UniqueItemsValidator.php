<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\Array_;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ObjectCheckTrait;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorInterface;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLCZVendor\Symfony\Component\Validator\Constraints\Unique;
class UniqueItemsValidator implements ValidatorInterface
{
    use ObjectCheckTrait;
    public function supports($object) : bool
    {
        return $this->checkObject($object) && (\is_array($object->getType()) ? \in_array('array', $object->getType()) : 'array' === $object->getType()) && null !== $object->getUniqueItems();
    }
    /**
     * @param JsonSchema          $object
     * @param ClassGuess|Property $guess
     */
    public function guess($object, string $name, $guess) : void
    {
        if (!$object->getUniqueItems()) {
            return;
        }
        $guess->addValidatorGuess(new ValidatorGuess(Unique::class));
    }
}
