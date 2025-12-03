<?php

declare (strict_types=1);
namespace PPLCZVendor\Doctrine\Inflector\Rules\NorwegianBokmal;

use PPLCZVendor\Doctrine\Inflector\GenericLanguageInflectorFactory;
use PPLCZVendor\Doctrine\Inflector\Rules\Ruleset;
final class InflectorFactory extends GenericLanguageInflectorFactory
{
    protected function getSingularRuleset() : Ruleset
    {
        return Rules::getSingularRuleset();
    }
    protected function getPluralRuleset() : Ruleset
    {
        return Rules::getPluralRuleset();
    }
}
