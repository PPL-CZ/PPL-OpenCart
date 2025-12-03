<?php
namespace PPLCZ\Repository;

use Opencart\System\Engine\Model;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\ParcelData;

/**
 * @property-read $model_setting_setting
 */
class Parcel extends Data
{
    public function getAccessPointByCode($code)
    {
        $data = $this->select("pplcz_parcel", ["code" => $code]);

        if ($data) {
            return ParcelData::fromArray(reset($data), $this->registry);
        }
        return null;
    }
}