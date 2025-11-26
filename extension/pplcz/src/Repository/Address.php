<?php
namespace PPLCZ\Repository;

use Opencart\System\Engine\Model;
use PPLCZ\Data\AddressData;

/**
 * @property-read $model_setting_setting
 */
class Address extends Data
{

    public function getDefaultSenderAddresses($store_id)
    {
        $this->load->model("setting/setting");

        $ids = $this->model_setting_setting->get('pplcz_shipping_default_address', $store_id);

        $ids = reset($ids);
        if ($ids)
            $ids = @json_decode($ids);
        else
            $ids = [];

        $output = [];

        foreach ($ids as $id)
        {
            $data = $this->load(AddressData::class, $id);
            if ($data)
            {
                $output[] = $data;
            }
        }

        return $output;
    }

    public function getAddress($id)
    {
        return $this->load(AddressData::class,$id);

    }

    public function deleteAddress($id)
    {
        $this->remove(AddressData::class, $id);
        $sql = "DELETE FROM `" . DB_PREFIX . "pplcz_address` `p` where pplcz_address_id = ". (int)$id;
        $this->db->query($sql);
    }

    public function updateAddress(AddressData $addressData)
    {
        $row = $addressData->toArray();
        if ($row['pplcz_address_id'] && $row['pplcz_address_id'] > 0)
        {
            $id = $row['pplcz_address_id'];
            unset($row['pplcz_address_id']);
            $this->update("pplcz_address", $row, ["pplcz_address_id" => $id]);
        }
        else {
            unset($row['pplcz_address_id']);
            $id = $this->insert('pplcz_address', $row);
            $addressData->id = $id;
        }
    }

}