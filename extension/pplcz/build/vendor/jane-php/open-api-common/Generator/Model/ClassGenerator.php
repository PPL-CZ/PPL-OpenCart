<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Model;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Model\ClassGenerator as BaseClassGenerator;
use PPLCZVendor\PhpParser\Comment\Doc;
use PPLCZVendor\PhpParser\Node;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Stmt;
trait ClassGenerator
{
    use BaseClassGenerator {
        createModel as baseCreateModel;
    }
    /**
     * Return a model class.
     *
     * @param Node[] $properties
     * @param Node[] $methods
     */
    protected function createModel(string $name, array $properties, array $methods, bool $hasExtensions = \false, bool $deprecated = \false, ?string $extends = null) : Stmt\Class_
    {
        $classExtends = null;
        if (null !== $extends) {
            $classExtends = new Name($extends);
        } elseif ($hasExtensions) {
            $classExtends = new Name('\\ArrayObject');
        }
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
        return new Stmt\Class_(new Name($this->getNaming()->getClassName($name)), ['stmts' => \array_merge($this->getInitialized(), $properties, $methods), 'extends' => $classExtends], $attributes);
    }
}
