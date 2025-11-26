<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\Array_;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ObjectCheckTrait;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorInterface;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLCZVendor\Symfony\Component\Validator\Constraints\Count;
class MaxItemsValidator implements ValidatorInterface
{
    use ObjectCheckTrait;
    public function supports($object) : bool
    {
        return $this->checkObject($object) && (\is_array($object->getType()) ? \in_array('array', $object->getType()) : 'array' === $object->getType()) && null !== $object->getMaxItems();
    }
    /**
     * @param JsonSchema          $object
     * @param ClassGuess|Property $guess
     */
    public function guess($object, string $name, $guess) : void
    {
        $guess->addValidatorGuess(new ValidatorGuess(Count::class, ['max' => $object->getMaxItems(), 'maxMessage' => 'This array has too much values. It should have {{ limit }} values or less.']));
    }
}
