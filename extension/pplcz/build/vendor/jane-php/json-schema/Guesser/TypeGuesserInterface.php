<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Registry;
interface TypeGuesserInterface
{
    /**
     * Return all types guessed.
     *
     * @internal
     */
    public function guessType($object, string $name, string $reference, Registry $registry) : Type;
}
