<?php
namespace PPLCZ\Data;

/**
 * @property $id
 * @property $created_date
 * @property $send_date
 * @property $send_to_api_date
 * @property $reference_id
 * @property $remote_collection_id
 * @property $state
 * @property $shipment_count
 * @property $estimated_shipment_count
 * @property $contact
 * @property $telephone
 * @property $email
 * @property $note
 */
#[\AllowDynamicProperties]
class CollectionData extends PPLData
{
    public $table = 'pplcz_collections';

    public $key = 'pplcz_collection_id';

    protected $fields = [
        "pplcz_collection_id" => "id",
        "created_date",
        "send_date",
        "send_to_api_date",
        "reference_id",
        "remote_collection_id",
        "state",
        "shipment_count",
        "estimated_shipment_count",
        "contact",
        "telephone",
        "email",
        "note",
    ];

}