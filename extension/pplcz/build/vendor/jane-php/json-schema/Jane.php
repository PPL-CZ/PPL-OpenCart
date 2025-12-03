<?php

namespace PPLCZVendor\Jane\Component\JsonSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\ChainGenerator;
use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\Jane\Component\JsonSchema\Generator\ModelGenerator;
use PPLCZVendor\Jane\Component\JsonSchema\Generator\Naming;
use PPLCZVendor\Jane\Component\JsonSchema\Generator\NormalizerGenerator;
use PPLCZVendor\Jane\Component\JsonSchema\Generator\RuntimeGenerator;
use PPLCZVendor\Jane\Component\JsonSchema\Generator\ValidatorGenerator;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ChainGuesser;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\JsonSchema\JsonSchemaGuesserFactory;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ChainValidatorFactory;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Normalizer\JaneObjectNormalizer;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Registry;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Schema;
use PPLCZVendor\PhpParser\ParserFactory;
use PPLCZVendor\Symfony\Component\Serializer\Encoder\JsonDecode;
use PPLCZVendor\Symfony\Component\Serializer\Encoder\JsonEncode;
use PPLCZVendor\Symfony\Component\Serializer\Encoder\JsonEncoder;
use PPLCZVendor\Symfony\Component\Serializer\Serializer;
use PPLCZVendor\Symfony\Component\Serializer\SerializerInterface;
class Jane extends ChainGenerator
{
    public const VERSION = '4.x-dev';
    private $serializer;
    private $chainGuesser;
    private $strict;
    private $naming;
    public function __construct(SerializerInterface $serializer, ChainGuesser $chainGuesser, Naming $naming, bool $strict = \true)
    {
        $this->serializer = $serializer;
        $this->chainGuesser = $chainGuesser;
        $this->strict = $strict;
        $this->naming = $naming;
    }
    public function createContext(Registry $registry) : Context
    {
        // List of schemas can evolve, but we don't want to generate new schema dynamically added, so we "clone" the array
        // to have a fixed list of schemas
        $schemas = \array_values($registry->getSchemas());
        /** @var Schema $schema */
        foreach ($schemas as $schema) {
            $jsonSchema = $this->serializer->deserialize(\file_get_contents($schema->getOrigin()), 'PPLCZVendor\\Jane\\Component\\JsonSchema\\JsonSchema\\Model\\JsonSchema', 'json', ['document-origin' => $schema->getOrigin()]);
            $this->chainGuesser->guessClass($jsonSchema, $schema->getRootName(), $schema->getOrigin() . '#', $registry);
        }
        $chainValidator = ChainValidatorFactory::create($this->naming, $registry, $this->serializer);
        foreach ($registry->getSchemas() as $schema) {
            foreach ($schema->getClasses() as $class) {
                $properties = $this->chainGuesser->guessProperties($class->getObject(), $schema->getRootName(), $class->getReference(), $registry);
                $names = [];
                foreach ($properties as $property) {
                    $deduplicatedName = $this->naming->getDeduplicatedName($property->getName(), $names);
                    $property->setAccessorName($deduplicatedName);
                    $property->setPhpName($this->naming->getPropertyName($deduplicatedName));
                    $property->setType($this->chainGuesser->guessType($property->getObject(), $property->getName(), $property->getReference(), $registry));
                }
                $class->setProperties($properties);
                $schema->addClassRelations($class);
                $extensionsTypes = [];
                foreach ($class->getExtensionsObject() as $pattern => $extensionData) {
                    $extensionsTypes[$pattern] = $this->chainGuesser->guessType($extensionData['object'], $class->getName(), $extensionData['reference'], $registry);
                }
                $class->setExtensionsType($extensionsTypes);
                $chainValidator->guess($class->getObject(), $class->getName(), $class);
            }
        }
        return new Context($registry, $this->strict);
    }
    public static function build(array $options = []) : self
    {
        $serializer = self::buildSerializer();
        $chainGuesser = JsonSchemaGuesserFactory::create($serializer, $options);
        $naming = new Naming();
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $self = new self($serializer, $chainGuesser, $naming, $options['strict']);
        $self->addGenerator(new ModelGenerator($naming, $parser));
        $self->addGenerator(new NormalizerGenerator($naming, $parser, $options['reference'], $options['use-cacheable-supports-method'] ?? \false, $options['skip-null-values'] ?? \true, $options['skip-required-fields'] ?? \false, $options['validation'] ?? \false));
        $self->addGenerator(new RuntimeGenerator($naming, $parser));
        if ($options['validation'] ?? \false) {
            $self->addGenerator(new ValidatorGenerator($naming));
        }
        return $self;
    }
    public static function buildSerializer() : SerializerInterface
    {
        $encoders = [new JsonEncoder(new JsonEncode([JsonEncode::OPTIONS => \JSON_UNESCAPED_SLASHES]), new JsonDecode([JsonDecode::ASSOCIATIVE => \true]))];
        return new Serializer([new JaneObjectNormalizer()], $encoders);
    }
}
