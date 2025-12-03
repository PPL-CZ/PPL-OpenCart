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
class UpdateShipmentModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\UpdateShipmentModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\UpdateShipmentModel';
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
        $object = new \PPLCZ\Model\Model\UpdateShipmentModel();
        if (\array_key_exists('codValue', $data) && \is_int($data['codValue'])) {
            $data['codValue'] = (double) $data['codValue'];
        }
        if (\array_key_exists('codBankAccountId', $data) && \is_int($data['codBankAccountId'])) {
            $data['codBankAccountId'] = (double) $data['codBankAccountId'];
        }
        if (\array_key_exists('senderId', $data) && \is_int($data['senderId'])) {
            $data['senderId'] = (double) $data['senderId'];
        }
        if (\array_key_exists('orderId', $data) && \is_int($data['orderId'])) {
            $data['orderId'] = (double) $data['orderId'];
        }
        if (\array_key_exists('parcelId', $data) && \is_int($data['parcelId'])) {
            $data['parcelId'] = (double) $data['parcelId'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('referenceId', $data) && $data['referenceId'] !== null) {
            $object->setReferenceId($data['referenceId']);
            unset($data['referenceId']);
        }
        elseif (\array_key_exists('referenceId', $data) && $data['referenceId'] === null) {
            $object->setReferenceId(null);
        }
        if (\array_key_exists('codVariableNumber', $data) && $data['codVariableNumber'] !== null) {
            $object->setCodVariableNumber($data['codVariableNumber']);
            unset($data['codVariableNumber']);
        }
        elseif (\array_key_exists('codVariableNumber', $data) && $data['codVariableNumber'] === null) {
            $object->setCodVariableNumber(null);
        }
        if (\array_key_exists('codValue', $data) && $data['codValue'] !== null) {
            $object->setCodValue($data['codValue']);
            unset($data['codValue']);
        }
        elseif (\array_key_exists('codValue', $data) && $data['codValue'] === null) {
            $object->setCodValue(null);
        }
        if (\array_key_exists('codValueCurrency', $data) && $data['codValueCurrency'] !== null) {
            $object->setCodValueCurrency($data['codValueCurrency']);
            unset($data['codValueCurrency']);
        }
        elseif (\array_key_exists('codValueCurrency', $data) && $data['codValueCurrency'] === null) {
            $object->setCodValueCurrency(null);
        }
        if (\array_key_exists('codBankAccountId', $data) && $data['codBankAccountId'] !== null) {
            $object->setCodBankAccountId($data['codBankAccountId']);
            unset($data['codBankAccountId']);
        }
        elseif (\array_key_exists('codBankAccountId', $data) && $data['codBankAccountId'] === null) {
            $object->setCodBankAccountId(null);
        }
        if (\array_key_exists('senderId', $data) && $data['senderId'] !== null) {
            $object->setSenderId($data['senderId']);
            unset($data['senderId']);
        }
        elseif (\array_key_exists('senderId', $data) && $data['senderId'] === null) {
            $object->setSenderId(null);
        }
        if (\array_key_exists('serviceCode', $data) && $data['serviceCode'] !== null) {
            $object->setServiceCode($data['serviceCode']);
            unset($data['serviceCode']);
        }
        elseif (\array_key_exists('serviceCode', $data) && $data['serviceCode'] === null) {
            $object->setServiceCode(null);
        }
        if (\array_key_exists('orderId', $data) && $data['orderId'] !== null) {
            $object->setOrderId($data['orderId']);
            unset($data['orderId']);
        }
        elseif (\array_key_exists('orderId', $data) && $data['orderId'] === null) {
            $object->setOrderId(null);
        }
        if (\array_key_exists('hasParcel', $data) && $data['hasParcel'] !== null) {
            $object->setHasParcel($data['hasParcel']);
            unset($data['hasParcel']);
        }
        elseif (\array_key_exists('hasParcel', $data) && $data['hasParcel'] === null) {
            $object->setHasParcel(null);
        }
        if (\array_key_exists('parcelId', $data) && $data['parcelId'] !== null) {
            $object->setParcelId($data['parcelId']);
            unset($data['parcelId']);
        }
        elseif (\array_key_exists('parcelId', $data) && $data['parcelId'] === null) {
            $object->setParcelId(null);
        }
        if (\array_key_exists('age', $data) && $data['age'] !== null) {
            $object->setAge($data['age']);
            unset($data['age']);
        }
        elseif (\array_key_exists('age', $data) && $data['age'] === null) {
            $object->setAge(null);
        }
        if (\array_key_exists('note', $data) && $data['note'] !== null) {
            $object->setNote($data['note']);
            unset($data['note']);
        }
        elseif (\array_key_exists('note', $data) && $data['note'] === null) {
            $object->setNote(null);
        }
        if (\array_key_exists('packages', $data) && $data['packages'] !== null) {
            $values = array();
            foreach ($data['packages'] as $value) {
                $values[] = $this->denormalizer->denormalize($value, 'PPLCZ\\Model\\Model\\PackageModel', 'json', $context);
            }
            $object->setPackages($values);
            unset($data['packages']);
        }
        elseif (\array_key_exists('packages', $data) && $data['packages'] === null) {
            $object->setPackages(null);
        }
        foreach ($data as $key => $value_1) {
            if (preg_match('/.*/', (string) $key)) {
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
        $data['referenceId'] = $object->getReferenceId();
        if ($object->isInitialized('codVariableNumber') && null !== $object->getCodVariableNumber()) {
            $data['codVariableNumber'] = $object->getCodVariableNumber();
        }
        if ($object->isInitialized('codValue') && null !== $object->getCodValue()) {
            $data['codValue'] = $object->getCodValue();
        }
        if ($object->isInitialized('codValueCurrency') && null !== $object->getCodValueCurrency()) {
            $data['codValueCurrency'] = $object->getCodValueCurrency();
        }
        if ($object->isInitialized('codBankAccountId') && null !== $object->getCodBankAccountId()) {
            $data['codBankAccountId'] = $object->getCodBankAccountId();
        }
        if ($object->isInitialized('senderId') && null !== $object->getSenderId()) {
            $data['senderId'] = $object->getSenderId();
        }
        $data['serviceCode'] = $object->getServiceCode();
        if ($object->isInitialized('orderId') && null !== $object->getOrderId()) {
            $data['orderId'] = $object->getOrderId();
        }
        if ($object->isInitialized('hasParcel') && null !== $object->getHasParcel()) {
            $data['hasParcel'] = $object->getHasParcel();
        }
        if ($object->isInitialized('parcelId') && null !== $object->getParcelId()) {
            $data['parcelId'] = $object->getParcelId();
        }
        if ($object->isInitialized('age') && null !== $object->getAge()) {
            $data['age'] = $object->getAge();
        }
        if ($object->isInitialized('note') && null !== $object->getNote()) {
            $data['note'] = $object->getNote();
        }
        $values = array();
        foreach ($object->getPackages() as $value) {
            $values[] = $this->normalizer->normalize($value, 'json', $context);
        }
        $data['packages'] = $values;
        foreach ($object as $key => $value_1) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value_1;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\UpdateShipmentModel' => false);
    }
}