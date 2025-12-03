<?php

namespace PPLCZ\Data;

/**
 * @property $id
 * @property $lock
 * @property $remote_batch_id
 * @property $created_at
 * @property $name
 */
#[\AllowDynamicProperties]
class BatchData extends PPLData
{
    public $table = "pplcz_batch";

    public $key = "pplcz_batch_id";

    public $defaults = [
        'lock' => false,
    ];

    protected $fields = [
        "pplcz_batch_id" => "id",
        "name",
        "remote_batch_id",
        "created_at",
        "lock",
    ];

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->created_at =  date("Y-m-d H:i:s");
    }
}