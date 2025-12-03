<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Normalizer;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\Jane\Component\JsonSchema\Generator\Normalizer\DenormalizerGenerator as JsonSchemaDenormalizerGenerator;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\ParentClass;
use PPLCZVendor\PhpParser\Node\Arg;
use PPLCZVendor\PhpParser\Node\Expr;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Scalar;
use PPLCZVendor\PhpParser\Node\Stmt;
trait DenormalizerGenerator
{
    use JsonSchemaDenormalizerGenerator {
        denormalizeMethodStatements as jsonSchemaDenormalizeMethodStatements;
    }
    protected function denormalizeMethodStatements(ClassGuess $classGuess, Context $context) : array
    {
        $statements = $this->jsonSchemaDenormalizeMethodStatements($classGuess, $context);
        if ($classGuess instanceof ParentClass) {
            foreach ($classGuess->getChildEntryKeys() as $discriminatorValue) {
                $statements[] = new Stmt\If_(new Expr\BinaryOp\LogicalAnd(new Expr\FuncCall(new Name('array_key_exists'), [new Arg(new Scalar\String_($classGuess->getDiscriminator())), new Arg(new Expr\Variable('data'))]), new Expr\BinaryOp\Identical(new Scalar\String_($discriminatorValue), new Expr\ArrayDimFetch(new Expr\Variable('data'), new Scalar\String_($classGuess->getDiscriminator())))), ['stmts' => [new Stmt\Return_(new Expr\MethodCall(new Expr\PropertyFetch(new Expr\Variable('this'), 'denormalizer'), 'denormalize', [new Expr\Variable('data'), new Scalar\String_(\sprintf('%s\\Model\\%s', $context->getCurrentSchema()->getNamespace(), $this->getNaming()->getClassName($classGuess->getChildEntryClassNameByKey($discriminatorValue)))), new Expr\Variable('format'), new Expr\Variable('context')]))]]);
            }
        }
        return $statements;
    }
}
