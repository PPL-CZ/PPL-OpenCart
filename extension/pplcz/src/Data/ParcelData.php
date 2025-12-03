<?php

namespace PPLCZ\Data;

/**
 * @property $id
 * @property $name
 * @property $name2
 * @property $street
 * @property $city
 * @property $zip
 * @property $type
 * @property $country
 * @property $code
 * @property $lat
 * @property $lng
 * @property $lock
 * @property $draft
 * @property $hidden
 * @property $valid
 */
#[\AllowDynamicProperties]
class ParcelData extends PPLData
{
    public $table = 'pplcz_parcel';

    public $key = 'pplcz_parcel_id';

    public $defaults = [
        'lock' => false,
        'hidden' => false,
        'draft' => false,
    ];

    protected $fields = [
        "pplcz_parcel_id" => "id",
        "name",
        "name2",
        "street",
        "city",
        "zip",
        "country",
        "code",
        "lat",
        "lng",
        "lock",
        "draft",
        "type",
        "hidden",
        "valid"
    ];
}