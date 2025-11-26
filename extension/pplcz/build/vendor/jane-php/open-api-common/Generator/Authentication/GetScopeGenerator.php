<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Authentication;

use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\SecuritySchemeGuess;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Scalar;
use PPLCZVendor\PhpParser\Node\Stmt;
trait GetScopeGenerator
{
    protected function createGetScope(SecuritySchemeGuess $securityScheme) : Stmt\ClassMethod
    {
        return new Stmt\ClassMethod('getScope', ['returnType' => new Name('string'), 'stmts' => [new Stmt\Return_(new Scalar\String_($securityScheme->getName()))], 'type' => Stmt\Class_::MODIFIER_PUBLIC]);
    }
}
