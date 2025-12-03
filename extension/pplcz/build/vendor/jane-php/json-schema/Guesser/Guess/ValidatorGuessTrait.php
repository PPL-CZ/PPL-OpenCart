<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorGuess;
trait ValidatorGuessTrait
{
    /** @var ValidatorGuess[] */
    private $validators = [];
    public function addValidatorGuess(ValidatorGuess $validatorGuess) : void
    {
        $this->validators[] = $validatorGuess;
    }
    public function getValidatorGuesses() : array
    {
        return $this->validators;
    }
}
