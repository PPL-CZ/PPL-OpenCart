<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Endpoint;

use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLCZVendor\PhpParser\Node\Expr;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Scalar;
use PPLCZVendor\PhpParser\Node\Stmt;
trait GetAuthenticationScopesTrait
{
    public function getAuthenticationScopesMethod(OperationGuess $operation) : Stmt\ClassMethod
    {
        $securityScopes = [];
        foreach ($operation->getSecurityScopes() as $scope) {
            $securityScopes[] = new Expr\ArrayItem(new Scalar\String_($scope));
        }
        return new Stmt\ClassMethod('getAuthenticationScopes', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'returnType' => new Name('array'), 'stmts' => [new Stmt\Return_(new Expr\Array_($securityScopes))]]);
    }
}
