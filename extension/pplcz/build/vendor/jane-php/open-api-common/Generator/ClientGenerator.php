<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\Jane\Component\JsonSchema\Generator\File;
use PPLCZVendor\Jane\Component\JsonSchema\Generator\GeneratorInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Schema;
use PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Client\ClientGenerator as CommonClientGenerator;
use PPLCZVendor\Jane\Component\OpenApiCommon\Generator\Client\HttpClientCreateGenerator;
use PPLCZVendor\Jane\Component\OpenApiCommon\Naming\OperationNamingInterface;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Stmt;
abstract class ClientGenerator implements GeneratorInterface
{
    use CommonClientGenerator;
    use HttpClientCreateGenerator;
    /** @var OperationGenerator */
    private $operationGenerator;
    /** @var OperationNamingInterface */
    private $operationNaming;
    public function __construct(OperationGenerator $operationGenerator, OperationNamingInterface $operationNaming)
    {
        $this->operationGenerator = $operationGenerator;
        $this->operationNaming = $operationNaming;
    }
    public function generate(Schema $schema, string $className, Context $context) : void
    {
        $statements = [];
        foreach ($schema->getOperations() as $operation) {
            $operationName = $this->operationNaming->getFunctionName($operation);
            $statements[] = $this->operationGenerator->createOperation($operationName, $operation, $context);
        }
        $client = $this->createResourceClass($schema, 'Client' . $this->getSuffix());
        $client->stmts = \array_merge($statements, [$this->getFactoryMethod($schema, $context)]);
        $node = new Stmt\Namespace_(new Name($schema->getNamespace()), [$client]);
        $schema->addFile(new File($schema->getDirectory() . \DIRECTORY_SEPARATOR . 'Client' . $this->getSuffix() . '.php', $node, 'client'));
    }
}
