<?php
namespace PPLCZ\ModelNormalizer;

use PPLCZ\Data\ParcelData;
use PPLCZ\Model\Model\ParcelAddressModel;
use PPLCZ\TLoader;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsAccessPointAccessPointModel;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ParcelModelDenormalizer implements DenormalizerInterface
{
    use TLoader;

    public function __construct($registry)
    {
        $this->setRegistry($registry);
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if (
            $data instanceof EpsApiMyApi2WebModelsAccessPointAccessPointModel
            && $type === ParcelData::class)
        {
            $model = $context['data'] ?: new ParcelData($this->registry);
            $model->set_type($data->getAccessPointType());
            $model->set_lat($data->getGps()->getLatitude());
            $model->set_lng($data->getGps()->getLongitude());
            $model->set_name($data->getName() );
            $model->set_name2($data->getName2());
            $model->set_street($data->getStreet());
            $model->set_city($data->getCity());
            $model->set_zip($data->getZipCode());
            $model->set_code($data->getAccessPointCode());
            $model->set_country($data->getCountry());
            $model->set_valid(true);



            return $model;
        }
        else if  ($data instanceof  ParcelData && $type === ParcelAddressModel::class)
        {
            $model = new ParcelAddressModel();
            $model->setCity($data->city);
            $model->setName($data->name);
            if ($data->name2)
                $model->setName2($data->name2);
            $model->setZip($data->zip);
            $model->setStreet($data->street);
            $model->setCountry($data->country);
            $model->setLat($data->lat);
            $model->setLng($data->lng);
            $model->setType($data->type);
            $model->setId($data->id);
            return $model;
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if (
            $data instanceof EpsApiMyApi2WebModelsAccessPointAccessPointModel
            && $type === ParcelData::class)
        {
            return true;
        } else if ($data instanceof  ParcelData && $type === ParcelAddressModel::class) {
            return true;
        }
        return false;
    }
}