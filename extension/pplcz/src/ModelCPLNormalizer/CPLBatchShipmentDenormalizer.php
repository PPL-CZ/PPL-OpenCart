<?php
namespace PPLCZ\ModelCPLNormalizer;

use PPLCZ\Data\AddressData;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ParcelData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Repository\Address;
use PPLCZ\Repository\Normalizer;
use PPLCZ\TLoader;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


class CPLBatchShipmentDenormalizer implements DenormalizerInterface
{
    public function __construct($registry)
    {
        $this->setRegistry($registry);
    }

    use TLoader;



    public const INTEGRATOR = "4764562";

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /**
         * @var Address $addressLoader
         * @var Normalizer $normalizer
         */
        $normalizer = $this->loadModel("extension/pplcz/normalizer");
        $addressLoader = $this->loadModel("extension/pplcz/address");
        if ($data instanceof ShipmentData)
        {
            if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModel::class) {
                $shipmentBatch = new EpsApiMyApi2WebModelsShipmentBatchShipmentModel();
                $shipmentBatch->setReferenceId($data->get_reference_id());
                $shipmentBatch->setProductType($data->get_service_code());
                if ($data->get_cod_value()) {
                    $shipmentBatch->setCashOnDelivery($normalizer->denormalize($data, EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery::class));
                }
                $shipmentBatch->setIntegratorId(self::INTEGRATOR);
                $shipmentBatch->setReferenceId($data->get_reference_id());
                $shipmentBatch->setNote($data->get_note());

                if ($data->get_sender_address_id() === null)
                {
                    throw new \Exception("Sender address id cannot be null");
                }

                $senderData = $addressLoader->load(AddressData::class, $data->sender_address_id);
                $recipientData = $addressLoader->load(AddressData::class, $data->recipient_address_id);
                $shipmentBatch->setSender($normalizer->denormalize($senderData, EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender::class));
                $shipmentBatch->setRecipient($normalizer->denormalize($recipientData, EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel::class));

                if ($data->has_parcel)
                    $shipmentBatch->setSpecificDelivery($normalizer->denormalize($data, EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery::class ));
                if ($data->age) {
                    $shipmentBatch->setAgeCheck('A' . $data->get_age());
                }
                $shipmentBatch->setShipmentSet($normalizer->denormalize($data, EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet::class));

                return $shipmentBatch;
            }
            else if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery::class)
            {
                $cashOnDelivery = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery();
                $cashOnDelivery->setCodVarSym($data->get_cod_variable_number());
                $cashOnDelivery->setCodCurrency($data->get_cod_value_currency());
                $cashOnDelivery->setCodPrice($data->get_cod_value());
                return $cashOnDelivery;
            }
            else if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery::class )
            {
                $specific = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery();
                $parcelLoader = $this->loadModel("extension/pplcz/parcel");
                $parcel = $parcelLoader->load(ParcelData::class, $data->parcel_id);
                $specific->setParcelShopCode($parcel->code);
                return $specific;
            }
            else if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance::class) {
                $ids = $data->get_package_ids();
                $id = reset($ids);
                $package = new PackageData($id);
                if ($package->get_insurance()) {
                    $insurance = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance();
                    $insurance->setInsurancePrice($package->get_insurance());
                    return $insurance;
                }
                return null;
            }


        }
        throw new \Exception();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModel::class
                || $data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery::class
                || $data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery::class
                || $data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance::class;
    }
}