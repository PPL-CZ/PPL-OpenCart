<?php
namespace PPLCZ\Data;

/**
 * @property $id
 * @property $timestamp
 * @property $message
 * @property $errorhash
 */
#[\AllowDynamicProperties]
class LogData extends PPLData
{
    public $table = 'pplcz_log';

    public $key = 'ppl_log_id';

    protected $fields = [
        "ppl_log_id" => "id",
        "timestamp",
        "message",
        "errorhash"
    ];


}