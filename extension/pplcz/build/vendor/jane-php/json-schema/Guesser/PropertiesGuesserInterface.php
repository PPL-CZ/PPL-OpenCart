<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Registry;
interface PropertiesGuesserInterface
{
    /**
     * Return all properties guessed.
     *
     * @internal
     *
     * @return Property[]
     */
    public function guessProperties($object, string $name, string $reference, Registry $registry) : array;
}
