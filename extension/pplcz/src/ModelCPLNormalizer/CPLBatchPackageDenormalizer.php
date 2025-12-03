<?php
namespace PPLCZ\ModelCPLNormalizer;

use PPLCZ\Data\PackageData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\TLoader;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchExternalNumberModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModelWeighedShipmentInfo;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


class CPLBatchPackageDenormalizer implements DenormalizerInterface
{
    public function __construct($registry)
    {
        $this->setRegistry($registry);
    }

    use TLoader;



    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {

        $normalizer = $this->loadModel("extension/pplcz/normalizer");
        $packageLoader = $this->loadModel("extension/pplcz/package");

        if ($data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet::class)
        {
            $shipmentSet = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet();
            $ids = $data->get_package_ids();
            $shipmentSet->setNumberOfShipments(count($ids));
            foreach ($ids as $key => $id)
            {
                $package = $packageLoader->load(PackageData::class, $id);
                $ids[$key] = $normalizer->denormalize($package, EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel::class);
            }
            $shipmentSet->setShipmentSetItems($ids);
            return $shipmentSet;
        }
        else if ($data instanceof PackageData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel::class)
        {
            $shipment = new EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel();

            if ($data->get_weight()) {
                $info = new EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModelWeighedShipmentInfo();
                $info->setWeight($data->get_weight());
                $shipment->setWeighedShipmentInfo($info);
            }
            if ($data->get_insurance())
                $shipment->setInsurance($data->get_insurance());

            if ($data->get_reference_id())
            {
                $externalNumber = new EpsApiMyApi2WebModelsShipmentBatchExternalNumberModel();
                $externalNumber->setCode("CUST");
                $externalNumber->setExternalNumber($data->get_reference_id());
                $shipment->setExternalNumbers([$externalNumber]);
            }
            return $shipment;
        }
        throw new \Exception();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet::class
                || $data instanceof PackageData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel::class;

    }
}