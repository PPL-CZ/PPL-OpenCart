<?php

namespace PPLCZ\Model\Normalizer;

use PPLCZVendor\Jane\Component\JsonSchemaRuntime\Reference;
use PPLCZ\Model\Runtime\Normalizer\CheckArray;
use PPLCZ\Model\Runtime\Normalizer\ValidatorTrait;
use PPLCZVendor\Symfony\Component\Serializer\Exception\InvalidArgumentException;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class RefreshShipmentBatchReturnModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\RefreshShipmentBatchReturnModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\RefreshShipmentBatchReturnModel';
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
        $object = new \PPLCZ\Model\Model\RefreshShipmentBatchReturnModel();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('batchs', $data) && $data['batchs'] !== null) {
            $values = array();
            foreach ($data['batchs'] as $value) {
                $values[] = $value;
            }
            $object->setBatchs($values);
            unset($data['batchs']);
        }
        elseif (\array_key_exists('batchs', $data) && $data['batchs'] === null) {
            $object->setBatchs(null);
        }
        if (\array_key_exists('shipments', $data) && $data['shipments'] !== null) {
            $values_1 = array();
            foreach ($data['shipments'] as $value_1) {
                $values_1[] = $this->denormalizer->denormalize($value_1, 'PPLCZ\\Model\\Model\\ShipmentModel', 'json', $context);
            }
            $object->setShipments($values_1);
            unset($data['shipments']);
        }
        elseif (\array_key_exists('shipments', $data) && $data['shipments'] === null) {
            $object->setShipments(null);
        }
        foreach ($data as $key => $value_2) {
            if (preg_match('/.*/', (string) $key)) {
                $object[$key] = $value_2;
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
        if ($object->isInitialized('batchs') && null !== $object->getBatchs()) {
            $values = array();
            foreach ($object->getBatchs() as $value) {
                $values[] = $value;
            }
            $data['batchs'] = $values;
        }
        if ($object->isInitialized('shipments') && null !== $object->getShipments()) {
            $values_1 = array();
            foreach ($object->getShipments() as $value_1) {
                $values_1[] = $this->normalizer->normalize($value_1, 'json', $context);
            }
            $data['shipments'] = $values_1;
        }
        foreach ($object as $key => $value_2) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value_2;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\RefreshShipmentBatchReturnModel' => false);
    }
}