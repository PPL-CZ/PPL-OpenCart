<?php

namespace PPLCZVendor\Jane\Component\OpenApi3\Generator\Endpoint;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\Jane\Component\JsonSchemaRuntime\Reference;
use PPLCZVendor\Jane\Component\OpenApi3\Generator\RequestBodyGenerator;
use PPLCZVendor\Jane\Component\OpenApi3\Guesser\GuessClass;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\RequestBody;
use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLCZVendor\PhpParser\Node\Expr;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Param;
use PPLCZVendor\PhpParser\Node\Stmt;
use PPLCZVendor\Symfony\Component\Serializer\SerializerInterface;
trait GetGetBodyTrait
{
    public function getGetBody(OperationGuess $operation, Context $context, GuessClass $guessClass, RequestBodyGenerator $requestBodyGenerator) : Stmt\ClassMethod
    {
        $opRef = $operation->getReference() . '/requestBody';
        $requestBody = $operation->getOperation()->getRequestBody();
        if ($requestBody instanceof Reference) {
            [$_, $requestBody] = $guessClass->resolve($requestBody, RequestBody::class);
        }
        return new Stmt\ClassMethod('getBody', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'params' => [new Param(new Expr\Variable('serializer'), null, new Name\FullyQualified(SerializerInterface::class)), new Param(new Expr\Variable('streamFactory'), new Expr\ConstFetch(new Name('null')))], 'returnType' => new Name('array'), 'stmts' => $requestBodyGenerator->getSerializeStatements($requestBody, $opRef, $context)]);
    }
}
