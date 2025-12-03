<?php

namespace PPLCZ\Data;

/**
* @property $pplcz_order_cart_id
* @property $order_id
* @property $cart_setting
* @property $parcel_setting
* @property $contact_telephone
*/
#[\AllowDynamicProperties]
class OrderCartData extends PPLData
{

    protected $fields = [
        "pplcz_order_cart_id" => "id",
        "order_id",
        "cart_setting",
        "parcel_setting",
        "contact_telephone"
    ];

    public $key = 'pplcz_order_cart_id';

    public $table = 'pplcz_order_cart';

    public function set_cart_setting($setting)
    {
        if ($setting)
            $this->data['cart_setting'] = json_encode($setting);
        else
            $this->data['cart_setting'] = null;
    }

    public function set_parcel_setting($setting)
    {
        if ($setting)
            $this->data['parcel_setting'] = json_encode($setting);
        else
            $this->data['parcel_setting'] = null;
    }

    public function get_cart_setting()
    {
        if (isset($this->data['cart_setting']) && $this->data['cart_setting']) {
            return @json_decode($this->data['cart_setting'], true);
        }
        return null;
    }

    public function get_parcel_setting()
    {
        if (isset($this->data['parcel_setting']) && $this->data['parcel_setting']) {
            return @json_decode($this->data['parcel_setting'], true);
        }
        return null;
    }
}