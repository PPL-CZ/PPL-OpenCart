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
class CollectionModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\CollectionModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\CollectionModel';
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
        $object = new \PPLCZ\Model\Model\CollectionModel();
        if (\array_key_exists('id', $data) && \is_int($data['id'])) {
            $data['id'] = (double) $data['id'];
        }
        if (\array_key_exists('estimatedShipmentCount', $data) && \is_int($data['estimatedShipmentCount'])) {
            $data['estimatedShipmentCount'] = (double) $data['estimatedShipmentCount'];
        }
        if (\array_key_exists('shipmentCount', $data) && \is_int($data['shipmentCount'])) {
            $data['shipmentCount'] = (double) $data['shipmentCount'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('id', $data)) {
            $object->setId($data['id']);
            unset($data['id']);
        }
        if (\array_key_exists('remoteCollectionId', $data) && $data['remoteCollectionId'] !== null) {
            $object->setRemoteCollectionId($data['remoteCollectionId']);
            unset($data['remoteCollectionId']);
        }
        elseif (\array_key_exists('remoteCollectionId', $data) && $data['remoteCollectionId'] === null) {
            $object->setRemoteCollectionId(null);
        }
        if (\array_key_exists('referenceId', $data)) {
            $object->setReferenceId($data['referenceId']);
            unset($data['referenceId']);
        }
        if (\array_key_exists('createdDate', $data)) {
            $object->setCreatedDate($data['createdDate']);
            unset($data['createdDate']);
        }
        if (\array_key_exists('sendDate', $data)) {
            $object->setSendDate($data['sendDate']);
            unset($data['sendDate']);
        }
        if (\array_key_exists('sendToApiDate', $data) && $data['sendToApiDate'] !== null) {
            $object->setSendToApiDate($data['sendToApiDate']);
            unset($data['sendToApiDate']);
        }
        elseif (\array_key_exists('sendToApiDate', $data) && $data['sendToApiDate'] === null) {
            $object->setSendToApiDate(null);
        }
        if (\array_key_exists('state', $data)) {
            $object->setState($data['state']);
            unset($data['state']);
        }
        if (\array_key_exists('estimatedShipmentCount', $data) && $data['estimatedShipmentCount'] !== null) {
            $object->setEstimatedShipmentCount($data['estimatedShipmentCount']);
            unset($data['estimatedShipmentCount']);
        }
        elseif (\array_key_exists('estimatedShipmentCount', $data) && $data['estimatedShipmentCount'] === null) {
            $object->setEstimatedShipmentCount(null);
        }
        if (\array_key_exists('shipmentCount', $data)) {
            $object->setShipmentCount($data['shipmentCount']);
            unset($data['shipmentCount']);
        }
        if (\array_key_exists('contact', $data) && $data['contact'] !== null) {
            $object->setContact($data['contact']);
            unset($data['contact']);
        }
        elseif (\array_key_exists('contact', $data) && $data['contact'] === null) {
            $object->setContact(null);
        }
        if (\array_key_exists('telephone', $data) && $data['telephone'] !== null) {
            $object->setTelephone($data['telephone']);
            unset($data['telephone']);
        }
        elseif (\array_key_exists('telephone', $data) && $data['telephone'] === null) {
            $object->setTelephone(null);
        }
        if (\array_key_exists('note', $data) && $data['note'] !== null) {
            $object->setNote($data['note']);
            unset($data['note']);
        }
        elseif (\array_key_exists('note', $data) && $data['note'] === null) {
            $object->setNote(null);
        }
        if (\array_key_exists('email', $data) && $data['email'] !== null) {
            $object->setEmail($data['email']);
            unset($data['email']);
        }
        elseif (\array_key_exists('email', $data) && $data['email'] === null) {
            $object->setEmail(null);
        }
        foreach ($data as $key => $value) {
            if (preg_match('/.*/', (string) $key)) {
                $object[$key] = $value;
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
        $data['id'] = $object->getId();
        if ($object->isInitialized('remoteCollectionId') && null !== $object->getRemoteCollectionId()) {
            $data['remoteCollectionId'] = $object->getRemoteCollectionId();
        }
        $data['referenceId'] = $object->getReferenceId();
        $data['createdDate'] = $object->getCreatedDate();
        $data['sendDate'] = $object->getSendDate();
        if ($object->isInitialized('sendToApiDate') && null !== $object->getSendToApiDate()) {
            $data['sendToApiDate'] = $object->getSendToApiDate();
        }
        if ($object->isInitialized('state') && null !== $object->getState()) {
            $data['state'] = $object->getState();
        }
        if ($object->isInitialized('estimatedShipmentCount') && null !== $object->getEstimatedShipmentCount()) {
            $data['estimatedShipmentCount'] = $object->getEstimatedShipmentCount();
        }
        $data['shipmentCount'] = $object->getShipmentCount();
        if ($object->isInitialized('contact') && null !== $object->getContact()) {
            $data['contact'] = $object->getContact();
        }
        if ($object->isInitialized('telephone') && null !== $object->getTelephone()) {
            $data['telephone'] = $object->getTelephone();
        }
        if ($object->isInitialized('note') && null !== $object->getNote()) {
            $data['note'] = $object->getNote();
        }
        if ($object->isInitialized('email') && null !== $object->getEmail()) {
            $data['email'] = $object->getEmail();
        }
        foreach ($object as $key => $value) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\CollectionModel' => false);
    }
}