<?php

namespace PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Normalizer;

use PPLCZVendor\Jane\Component\JsonSchemaRuntime\Reference;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\CheckArray;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\ValidatorTrait;
use PPLCZVendor\Symfony\Component\Serializer\Exception\InvalidArgumentException;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class ResponsesNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, $context = []) : bool
    {
        return $type === 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Responses';
    }
    public function supportsNormalization($data, $format = null, $context = []) : bool
    {
        return $data instanceof \PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\Responses;
    }
    /**
     * @return mixed
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (isset($data['$ref'])) {
            return new Reference($data['$ref'], $context['document-origin']);
        }
        if (isset($data['$recursiveRef'])) {
            return new Reference($data['$recursiveRef'], $context['document-origin']);
        }
        $object = new \PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\Responses();
        if (null === $data || \false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('default', $data) && $data['default'] !== null) {
            $value = $data['default'];
            if (\is_array($data['default']) and isset($data['default']['$ref'])) {
                $value = $this->denormalizer->denormalize($data['default'], 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Reference', 'json', $context);
            } elseif (\is_array($data['default']) and isset($data['default']['description'])) {
                $value = $this->denormalizer->denormalize($data['default'], 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Response', 'json', $context);
            }
            $object->setDefault($value);
            unset($data['default']);
        } elseif (\array_key_exists('default', $data) && $data['default'] === null) {
            $object->setDefault(null);
        }
        foreach ($data as $key => $value_1) {
            if (\preg_match('/^[1-5](?:\\d{2}|XX)$/', (string) $key)) {
                $value_2 = $value_1;
                if (\is_array($value_1) and isset($value_1['$ref'])) {
                    $value_2 = $this->denormalizer->denormalize($value_1, 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Reference', 'json', $context);
                } elseif (\is_array($value_1) and isset($value_1['description'])) {
                    $value_2 = $this->denormalizer->denormalize($value_1, 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Response', 'json', $context);
                }
                $object[$key] = $value_2;
            }
            if (\preg_match('/^x-/', (string) $key)) {
                $object[$key] = $value_1;
            }
        }
        return $object;
    }
    /**
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data = array();
        if (null !== $object->getDefault()) {
            $value = $object->getDefault();
            if (\is_object($object->getDefault())) {
                $value = $this->normalizer->normalize($object->getDefault(), 'json', $context);
            } elseif (\is_object($object->getDefault())) {
                $value = $this->normalizer->normalize($object->getDefault(), 'json', $context);
            }
            $data['default'] = $value;
        }
        foreach ($object as $key => $value_1) {
            if (\preg_match('/^[1-5](?:\\d{2}|XX)$/', (string) $key)) {
                $value_2 = $value_1;
                if (\is_object($value_1)) {
                    $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                } elseif (\is_object($value_1)) {
                    $value_2 = $this->normalizer->normalize($value_1, 'json', $context);
                }
                $data[$key] = $value_2;
            }
            if (\preg_match('/^x-/', (string) $key)) {
                $data[$key] = $value_1;
            }
        }
        return $data;
    }
}
