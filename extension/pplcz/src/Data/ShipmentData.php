<?php
namespace PPLCZ\Data;

/**
 * @property $id
 * @property $wc_order_id
 * @property $reference_id
 * @property $import_state
 * @property $service_code
 * @property $package_ids
 * @property $service_name
 * @property $recipient_address_id
 * @property $sender_address_id
 * @property $cod_bank_account_id
 * @property $cod_value
 * @property $cod_value_currency
 * @property $cod_variable_number
 * @property $parcel_id
 * @property $has_parcel
 * @property $batch_id
 * @property $batch_order
 * @property $remote_batch_id
 * @property $age
 * @property $note
 * @property $lock
 * @property $draft
 * @property $import_errors
 */
#[\AllowDynamicProperties]
class ShipmentData extends PPLData {

    public $table = "pplcz_shipment";

    public $key = "pplcz_shipment_id";

    public $defaults = [
        'lock' => false,
    ];

    protected $fields = [
        "pplcz_shipment_id" => "id",
        "wc_order_id",
        "reference_id",
        "import_state",
        "service_code",
        "package_ids",
        "service_name",
        "recipient_address_id",
        "sender_address_id",
        "cod_bank_account_id",
        "cod_value",
        "cod_value_currency",
        "cod_variable_number",
        "parcel_id",
        "has_parcel",
        "batch_id",
        "batch_order",
        "remote_batch_id",
        "age",
        "note",
        "lock",
        "draft",
        "import_errors",
    ];

    public function get_package_ids($context = 'view')
    {
        $ids = $this->data['package_ids'];
        if (!trim($ids))
        {
            return [];
        }

        return array_filter(array_map("trim", explode(',', $ids)), "ctype_digit");
    }

    public function set_package_ids(array $value)
    {
        $this->data['package_ids'] = join(",", array_filter(array_map("trim", $value), "ctype_digit"));
    }
}