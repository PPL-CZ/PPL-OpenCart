<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Property;
class ChainValidator implements ValidatorInterface
{
    /**
     * @var ValidatorInterface[]
     */
    private $validators = [];
    public function addValidator(ValidatorInterface $validator) : void
    {
        $this->validators[] = $validator;
    }
    public function supports($object) : bool
    {
        return \false;
    }
    /**
     * @param ClassGuess|Property $guess
     */
    public function guess($object, string $name, $guess) : void
    {
        foreach ($this->validators as $validator) {
            if ($validator->supports($object)) {
                $validator->guess($object, $name, $guess);
            }
        }
    }
}
