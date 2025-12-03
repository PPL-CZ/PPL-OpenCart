<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Authentication;

use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\HTTPSecurityScheme;
use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\SecuritySchemeGuess;
use PPLCZVendor\PhpParser\Node\Expr;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Param;
use PPLCZVendor\PhpParser\Node\Scalar;
use PPLCZVendor\PhpParser\Node\Stmt;
trait ConstructGenerator
{
    protected function createConstruct(SecuritySchemeGuess $securityScheme) : array
    {
        $needs = [];
        switch ($securityScheme->getType()) {
            case SecuritySchemeGuess::TYPE_HTTP:
                /** @var HTTPSecurityScheme $object */
                $object = $securityScheme->getObject();
                $scheme = $object->getScheme() ?? 'Bearer';
                $scheme = \ucfirst(\mb_strtolower($scheme));
                switch ($scheme) {
                    case 'Bearer':
                        $needs['token'] = new Name('string');
                        break;
                    case 'Basic':
                        $needs['username'] = new Name('string');
                        $needs['password'] = new Name('string');
                        break;
                }
                break;
            case SecuritySchemeGuess::TYPE_API_KEY:
                $needs['apiKey'] = new Name('string');
                break;
        }
        $constructStmts = [];
        $constructParams = [];
        $statements = [];
        foreach ($needs as $field => $type) {
            $statements[] = new Stmt\Property(Stmt\Class_::MODIFIER_PRIVATE, [new Stmt\PropertyProperty($field)]);
            $constructParams[] = new Param(new Expr\Variable($field), null, $type);
            $constructStmts[] = new Stmt\Expression(new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('this'), new Scalar\String_($field)), new Expr\Variable($field)));
        }
        $statements[] = new Stmt\ClassMethod('__construct', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'stmts' => $constructStmts, 'params' => $constructParams]);
        return $statements;
    }
}
