<?php

namespace PPLCZ\Data;

/**
 * @property $id
 * @property $wc_order_id
 * @property $reference_id
 * @property $pplcz_shipment_id
 * @property $shipment_number
 * @property $weight
 * @property $insurance
 * @property $insurance_currency
 * @property $phase
 * @property $status
 * @property $status_label
 * @property $phase_label
 * @property $last_update_phase
 * @property $last_test_phase
 * @property $ignore_phase
 * @property $import_error
 * @property $import_error_code
 * @property $lock
 * @property $draft
 * @property $label_id
 */
#[\AllowDynamicProperties]
class PackageData extends PPLData
{
    public $table = "pplcz_package";

    public $key = "pplcz_package_id";

    public $defaults = [
        'lock' => false,
    ];

    protected $fields = [
        "pplcz_package_id" => "id",
        "wc_order_id",
        "pplcz_shipment_id",
        "reference_id",
        "shipment_number",
        "weight",
        "insurance",
        "insurance_currency",
        "phase",
        "status",
        "status_label",
        "phase_label",
        "last_update_phase",
        "last_test_phase",
        "ignore_phase",
        "import_error",
        "import_error_code",
        "lock",
        "draft",
        "label_id"
    ];

    public function canBeCanceled()
    {
        return ($this->data['shipment_number'] && ($this->data['phase'] === 'Ordered' || !$this->data['phase'] || $this->data['phase'] === "None"));
    }
}