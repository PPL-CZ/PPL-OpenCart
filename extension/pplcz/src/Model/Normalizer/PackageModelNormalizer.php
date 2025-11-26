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
class PackageModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\PackageModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\PackageModel';
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
        $object = new \PPLCZ\Model\Model\PackageModel();
        if (\array_key_exists('weight', $data) && \is_int($data['weight'])) {
            $data['weight'] = (double) $data['weight'];
        }
        if (\array_key_exists('insurance', $data) && \is_int($data['insurance'])) {
            $data['insurance'] = (double) $data['insurance'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('id', $data) && $data['id'] !== null) {
            $object->setId($data['id']);
            unset($data['id']);
        }
        elseif (\array_key_exists('id', $data) && $data['id'] === null) {
            $object->setId(null);
        }
        if (\array_key_exists('shipmentNumber', $data) && $data['shipmentNumber'] !== null) {
            $object->setShipmentNumber($data['shipmentNumber']);
            unset($data['shipmentNumber']);
        }
        elseif (\array_key_exists('shipmentNumber', $data) && $data['shipmentNumber'] === null) {
            $object->setShipmentNumber(null);
        }
        if (\array_key_exists('referenceId', $data) && $data['referenceId'] !== null) {
            $object->setReferenceId($data['referenceId']);
            unset($data['referenceId']);
        }
        elseif (\array_key_exists('referenceId', $data) && $data['referenceId'] === null) {
            $object->setReferenceId(null);
        }
        if (\array_key_exists('labelId', $data) && $data['labelId'] !== null) {
            $object->setLabelId($data['labelId']);
            unset($data['labelId']);
        }
        elseif (\array_key_exists('labelId', $data) && $data['labelId'] === null) {
            $object->setLabelId(null);
        }
        if (\array_key_exists('importError', $data) && $data['importError'] !== null) {
            $object->setImportError($data['importError']);
            unset($data['importError']);
        }
        elseif (\array_key_exists('importError', $data) && $data['importError'] === null) {
            $object->setImportError(null);
        }
        if (\array_key_exists('importErrorCode', $data) && $data['importErrorCode'] !== null) {
            $object->setImportErrorCode($data['importErrorCode']);
            unset($data['importErrorCode']);
        }
        elseif (\array_key_exists('importErrorCode', $data) && $data['importErrorCode'] === null) {
            $object->setImportErrorCode(null);
        }
        if (\array_key_exists('weight', $data) && $data['weight'] !== null) {
            $object->setWeight($data['weight']);
            unset($data['weight']);
        }
        elseif (\array_key_exists('weight', $data) && $data['weight'] === null) {
            $object->setWeight(null);
        }
        if (\array_key_exists('insurance', $data) && $data['insurance'] !== null) {
            $object->setInsurance($data['insurance']);
            unset($data['insurance']);
        }
        elseif (\array_key_exists('insurance', $data) && $data['insurance'] === null) {
            $object->setInsurance(null);
        }
        if (\array_key_exists('insuranceCurrency', $data) && $data['insuranceCurrency'] !== null) {
            $object->setInsuranceCurrency($data['insuranceCurrency']);
            unset($data['insuranceCurrency']);
        }
        elseif (\array_key_exists('insuranceCurrency', $data) && $data['insuranceCurrency'] === null) {
            $object->setInsuranceCurrency(null);
        }
        if (\array_key_exists('phase', $data) && $data['phase'] !== null) {
            $object->setPhase($data['phase']);
            unset($data['phase']);
        }
        elseif (\array_key_exists('phase', $data) && $data['phase'] === null) {
            $object->setPhase(null);
        }
        if (\array_key_exists('phaseLabel', $data) && $data['phaseLabel'] !== null) {
            $object->setPhaseLabel($data['phaseLabel']);
            unset($data['phaseLabel']);
        }
        elseif (\array_key_exists('phaseLabel', $data) && $data['phaseLabel'] === null) {
            $object->setPhaseLabel(null);
        }
        if (\array_key_exists('lastUpdatePhase', $data) && $data['lastUpdatePhase'] !== null) {
            $object->setLastUpdatePhase($data['lastUpdatePhase']);
            unset($data['lastUpdatePhase']);
        }
        elseif (\array_key_exists('lastUpdatePhase', $data) && $data['lastUpdatePhase'] === null) {
            $object->setLastUpdatePhase(null);
        }
        if (\array_key_exists('ignorePhase', $data) && $data['ignorePhase'] !== null) {
            $object->setIgnorePhase($data['ignorePhase']);
            unset($data['ignorePhase']);
        }
        elseif (\array_key_exists('ignorePhase', $data) && $data['ignorePhase'] === null) {
            $object->setIgnorePhase(null);
        }
        if (\array_key_exists('canBeCanceled', $data) && $data['canBeCanceled'] !== null) {
            $object->setCanBeCanceled($data['canBeCanceled']);
            unset($data['canBeCanceled']);
        }
        elseif (\array_key_exists('canBeCanceled', $data) && $data['canBeCanceled'] === null) {
            $object->setCanBeCanceled(null);
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
        if ($object->isInitialized('id') && null !== $object->getId()) {
            $data['id'] = $object->getId();
        }
        if ($object->isInitialized('shipmentNumber') && null !== $object->getShipmentNumber()) {
            $data['shipmentNumber'] = $object->getShipmentNumber();
        }
        if ($object->isInitialized('referenceId') && null !== $object->getReferenceId()) {
            $data['referenceId'] = $object->getReferenceId();
        }
        if ($object->isInitialized('labelId') && null !== $object->getLabelId()) {
            $data['labelId'] = $object->getLabelId();
        }
        if ($object->isInitialized('importError') && null !== $object->getImportError()) {
            $data['importError'] = $object->getImportError();
        }
        if ($object->isInitialized('importErrorCode') && null !== $object->getImportErrorCode()) {
            $data['importErrorCode'] = $object->getImportErrorCode();
        }
        if ($object->isInitialized('weight') && null !== $object->getWeight()) {
            $data['weight'] = $object->getWeight();
        }
        if ($object->isInitialized('insurance') && null !== $object->getInsurance()) {
            $data['insurance'] = $object->getInsurance();
        }
        if ($object->isInitialized('insuranceCurrency') && null !== $object->getInsuranceCurrency()) {
            $data['insuranceCurrency'] = $object->getInsuranceCurrency();
        }
        if ($object->isInitialized('phase') && null !== $object->getPhase()) {
            $data['phase'] = $object->getPhase();
        }
        if ($object->isInitialized('phaseLabel') && null !== $object->getPhaseLabel()) {
            $data['phaseLabel'] = $object->getPhaseLabel();
        }
        if ($object->isInitialized('lastUpdatePhase') && null !== $object->getLastUpdatePhase()) {
            $data['lastUpdatePhase'] = $object->getLastUpdatePhase();
        }
        if ($object->isInitialized('ignorePhase') && null !== $object->getIgnorePhase()) {
            $data['ignorePhase'] = $object->getIgnorePhase();
        }
        if ($object->isInitialized('canBeCanceled') && null !== $object->getCanBeCanceled()) {
            $data['canBeCanceled'] = $object->getCanBeCanceled();
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
        return array('PPLCZ\\Model\\Model\\PackageModel' => false);
    }
}