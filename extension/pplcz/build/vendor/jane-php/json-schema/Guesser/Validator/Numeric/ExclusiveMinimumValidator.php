<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\Numeric;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ObjectCheckTrait;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorInterface;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLCZVendor\Symfony\Component\Validator\Constraints\GreaterThan;
class ExclusiveMinimumValidator implements ValidatorInterface
{
    use ObjectCheckTrait;
    public function supports($object) : bool
    {
        return $this->checkObject($object) && (\is_array($object->getType()) ? \in_array('integer', $object->getType()) || \in_array('number', $object->getType()) : 'integer' === $object->getType() || 'number' === $object->getType()) && \is_numeric($object->getExclusiveMinimum());
    }
    /**
     * @param JsonSchema          $object
     * @param ClassGuess|Property $guess
     */
    public function guess($object, string $name, $guess) : void
    {
        $guess->addValidatorGuess(new ValidatorGuess(GreaterThan::class, ['value' => $object->getExclusiveMinimum()]));
    }
}
