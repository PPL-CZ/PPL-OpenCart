<?php
namespace PPLCZ\ModelNormalizer;

use PPLCZ\Data\PackageData;
use PPLCZ\Model\Model\PackageModel;
use PPLCZ\TLoader;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PackageModelDenormalizer implements DenormalizerInterface
{
    use TLoader;

    public function __construct($registry)
    {
        $this->setRegistry($registry);
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof PackageData) {
            $model = new PackageModel();
            if($data->id)
                $model->setId($data->id);
            if ($data->weight)
                $model->setWeight($data->weight);
            if ($data->insurance_currency)
                $model->setInsuranceCurrency($data->insurance_currency);
            if ($data->insurance)
                $model->setInsurance($data->insurance);

            if ($data->phase_label)
                $model->setPhaseLabel($data->phase_label);
            else
                $model->setPhaseLabel("");

            $model->setPhase($data->phase ?: "None");

            $model->setCanBeCanceled(false);

            if ($data->phase === "None" ||  !$data->phase  || $data->phase === "Order")
                if ($data->shipment_number)
                    $model->setCanBeCanceled(true);


            if ($data->reference_id)
                $model->setReferenceId($data->reference_id);


            if ($data->ignore_phase)
                $model->setIgnorePhase($data->ignore_phase);
            if ($data->last_update_phase)
                $model->setLastUpdatePhase($data->last_update_phase);

            $model->setShipmentNumber($data->shipment_number ?: "");
            $model->setLabelId($data->label_id ?: "");
            $model->setImportError($data->import_error ?: "");
            $model->setImportErrorCode($data->import_error_code ?: "");

            return $model;
        } else if ($data instanceof  PackageModel) {
            $model = $context['data'] ?? new PackageData($this->registry);
            if ($model->lock)
                $model = new PackageData($this->registry);

            if ($data->isInitialized("referenceId"))
                $model->reference_id = "{$data->getReferenceId()}";

            $model->weight = ($data->getWeight() ?: null);
            $model->insurance_currency = ($data->getInsuranceCurrency() ?: null);
            $model->insurance = ($data->getInsurance() ?: null);
            return $model;
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if ($data instanceof PackageData && $type === PackageModel::class)
            return true;

        if ($data instanceof PackageModel && $type === PackageData::class)
        {
            return true;
        }
        return false;
    }
}