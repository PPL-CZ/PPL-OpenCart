<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Generator\Model;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Naming;
use PPLCZVendor\PhpParser\Comment\Doc;
use PPLCZVendor\PhpParser\Node;
use PPLCZVendor\PhpParser\Node\Expr;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Stmt;
trait ClassGenerator
{
    /**
     * The naming service.
     */
    protected abstract function getNaming() : Naming;
    /**
     * Return a model class.
     *
     * @param Node[] $properties
     * @param Node[] $methods
     */
    protected function createModel(string $name, array $properties, array $methods, bool $hasExtensions = \false, bool $deprecated = \false) : Stmt\Class_
    {
        $attributes = [];
        if ($deprecated) {
            $attributes['comments'] = [new Doc(<<<EOD
/**
 *
 * @deprecated
 */
EOD
)];
        }
        return new Stmt\Class_($this->getNaming()->getClassName($name), ['stmts' => \array_merge($this->getInitialized(), $properties, $methods), 'extends' => $hasExtensions ? new Name('\\ArrayObject') : null], $attributes);
    }
    protected function getInitialized() : array
    {
        $initializedProperty = new Stmt\Property(Stmt\Class_::MODIFIER_PROTECTED, [new Stmt\PropertyProperty('initialized', new Expr\Array_())], ['comments' => [new Doc(<<<EOD
/**
 * @var array
 */
EOD
)]]);
        $initializedMethod = new Stmt\ClassMethod('isInitialized', [
            // public function
            'type' => Stmt\Class_::MODIFIER_PUBLIC,
            'params' => [new Node\Param($propertyVariable = new Expr\Variable('property'))],
            'stmts' => [new Stmt\Return_(new Expr\FuncCall(new Name('array_key_exists'), [new Node\Arg($propertyVariable), new Node\Arg(new Expr\PropertyFetch(new Expr\Variable('this'), 'initialized'))]))],
            'returnType' => new Name('bool'),
        ]);
        return [$initializedProperty, $initializedMethod];
    }
}
