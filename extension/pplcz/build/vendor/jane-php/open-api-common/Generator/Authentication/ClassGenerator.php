<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Authentication;

use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Stmt;
trait ClassGenerator
{
    protected function createClass(string $name, array $statements) : Stmt\Class_
    {
        return new Stmt\Class_($name, ['stmts' => $statements, 'implements' => [new Name\FullyQualified('PPLCZVendor\\Jane\\Component\\OpenApiRuntime\\Client\\AuthenticationPlugin')]]);
    }
}
