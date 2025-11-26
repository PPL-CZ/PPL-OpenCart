<?php
namespace PPLCZ\Repository;

use PPLCZ\Data\BatchData;

class Batch extends Data {

    public function getBatches()
    {
        $prefix = DB_PREFIX;
        $result = $this->db->query("select * from {$prefix}pplcz_batch order by created_at desc");
        $output = [];

        foreach ($result->rows as $row) {
            $batch = BatchData::fromArray($row, $this->registry);
            $output[] = $batch;
        }

        return $output;
    }

    public function findFreeBatch()
    {
        $prefix = DB_PREFIX;

        $result = $this->db->query("select * from {$prefix}pplcz_batch where `lock` = 0");
        $output = [];

        foreach ($result->rows as $row) {
            $batch = BatchData::fromArray($row, $this->registry);
            $output[] = $batch;
        }

        return $output;
    }

}