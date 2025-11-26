<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser;

use PPLCZVendor\Jane\Component\JsonSchema\Registry\Registry;
interface ClassGuesserInterface
{
    /**
     * Guess model.
     *
     * This guesser should create a Model and the associated File
     * The file must be inject into the context
     *
     * @internal
     */
    public function guessClass($object, string $name, string $reference, Registry $registry) : void;
}
