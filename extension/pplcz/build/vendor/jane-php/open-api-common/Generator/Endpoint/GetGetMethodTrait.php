<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Endpoint;

use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Scalar;
use PPLCZVendor\PhpParser\Node\Stmt;
trait GetGetMethodTrait
{
    public function getGetMethod(OperationGuess $operation) : Stmt\ClassMethod
    {
        return new Stmt\ClassMethod('getMethod', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'stmts' => [new Stmt\Return_(new Scalar\String_($operation->getMethod()))], 'returnType' => new Name('string')]);
    }
}
