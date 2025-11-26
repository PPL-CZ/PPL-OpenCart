<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Property;
interface ValidatorInterface
{
    public function supports($object) : bool;
    /**
     * @param ClassGuess|Property $guess
     */
    public function guess($object, string $name, $guess) : void;
}
