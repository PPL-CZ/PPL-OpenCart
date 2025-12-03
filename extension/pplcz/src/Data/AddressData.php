<?php
namespace PPLCZ\Data;

/**
 * @property $id
 * @property $address_name
 * @property $name
 * @property $contact
 * @property $mail
 * @property $phone
 * @property $street
 * @property $city
 * @property $zip
 * @property $country
 * @property $note
 * @property $type
 * @property $hidden
 * @property $lock
 * @property $draft
 */
#[\AllowDynamicProperties]
class AddressData extends PPLData {



    protected $fields = [
        "pplcz_address_id" => "id",
        "address_name",
        "name",
        "contact",
        "mail",
        "phone",
        "street",
        "city",
        "zip",
        "country",
        "note",
        "type",
        "hidden",
        "lock",
        "draft",
    ];

    protected $defaults = [
        "lock" => false
    ];

    public $key = 'pplcz_address_id';

    public $table = 'pplcz_address';



}