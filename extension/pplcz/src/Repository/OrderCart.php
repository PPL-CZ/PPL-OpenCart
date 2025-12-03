<?php
namespace PPLCZ\Repository;

use Opencart\System\Engine\Model;
use PPLCZ\Data\OrderCartData;


class OrderCart extends Data
{
    public function getDataByOrderId($orderId)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pplcz_order_cart` `p` where order_id = ". (int)$orderId);
        $row = $query->row;
        if ($row)
        {
            return OrderCartData::fromArray($row, $this->registry);
        }
        return null;
    }


}