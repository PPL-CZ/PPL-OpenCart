<?php
namespace PPLCZ;



use PPLCZ\Model\Model\ErrorLogModel;
use PPLCZ\ModelCPLNormalizer\CPLBatchAddressDenormalizer;
use PPLCZ\ModelCPLNormalizer\CPLBatchCreateShipmentsDenormalizer;
use PPLCZ\ModelCPLNormalizer\CPLBatchPackageDenormalizer;
use PPLCZ\ModelCPLNormalizer\CPLBatchShipmentDenormalizer;
use PPLCZ\Model\Normalizer\JaneObjectNormalizer;
use PPLCZ\ModelNormalizer\AddressModelDenormalizer;
use PPLCZ\ModelNormalizer\BankModelDenormalizer;
use PPLCZ\ModelNormalizer\BatchModelDenormalizer;
use PPLCZ\ModelNormalizer\CartModelDenormalizer;
use PPLCZ\ModelNormalizer\CategoryModelDenormalizer;
use PPLCZ\ModelNormalizer\CollectionModelDenormalizer;
use PPLCZ\ModelNormalizer\ErrorLogDenormalizer;
use PPLCZ\ModelNormalizer\OrderAddressDataDenormalizer;
use PPLCZ\ModelNormalizer\PackageModelDenormalizer;
use PPLCZ\ModelNormalizer\PackageModelDernomalizer;
use PPLCZ\ModelNormalizer\ParcelDataModelDenormalizer;
use PPLCZ\ModelNormalizer\ParcelModelDenormalizer;
use PPLCZ\ModelNormalizer\ProductModelDenormalizer;
use PPLCZ\ModelNormalizer\CartModelDernomalizer;
use PPLCZ\ModelNormalizer\ShipmentDataDenormalizer;
use PPLCZ\ModelNormalizer\CollectionDataDenormalizer;
use PPLCZ\ModelNormalizer\ShipmentSettingDenormalizer;
use PPLCZ\ModelNormalizer\ShipmentWithAdditionalModelDenormalizer;
use PPLCZ\ModelNormalizer\WpErrorModelDenormalizer;

class Serializer extends \PPLCZVendor\Symfony\Component\Serializer\Serializer {
    public function __construct(\Opencart\System\Engine\Registry $registry)
    {
        parent::__construct([
            new AddressModelDenormalizer($registry),
            new CartModelDenormalizer($registry),
            new ShipmentDataDenormalizer($registry),
            new PackageModelDenormalizer($registry),
            new ParcelModelDenormalizer($registry),
            new BatchModelDenormalizer($registry),
            new ShipmentWithAdditionalModelDenormalizer($registry),
            new WpErrorModelDenormalizer($registry),
            new CPLBatchPackageDenormalizer($registry),
            new CPLBatchAddressDenormalizer($registry),
            new CPLBatchCreateShipmentsDenormalizer($registry),
            new CPLBatchShipmentDenormalizer($registry),
            new CollectionModelDenormalizer($registry),

            /*
            new ShipmentDataDenormalizer(),
            new CollectionDataDenormalizer(),
            new OrderAddressDataDenormalizer(),
            new AddressModelDenormalizer(),
            new BankModelDenormalizer(),
            new PackageModelDernomalizer(),
            new ShipmentDataDenormalizer(),
            new ParcelDataModelDenormalizer(),
            new ProductModelDenormalizer(),
            new CartModelDernomalizer(),
            new CategoryModelDenormalizer(),
            new ShipmentSettingDenormalizer(),
            new ErrorLogDenormalizer(),
            // cpl
            new CPLBatchAddressDenormalizer(),
            new CPLBatchCreateShipmentsDenormalizer(),
            new CPLBatchPackageDenormalizer(),
            new CPLBatchShipmentDenormalizer(),
            */
            // vygenerovano
            new JaneObjectNormalizer(),

        ]);
    }

}