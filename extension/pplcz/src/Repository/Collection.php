<?php
namespace PPLCZ\Repository;

use PPLCZ\Data\CollectionData;

class Collection extends Data
{
    public function getLastCollection()
    {
        $prefix = DB_PREFIX;
        $result = $this->db->query("select * from {$prefix}pplcz_collections order by created_date desc limit 1");
        if ($result->row) {
            return CollectionData::fromArray($result->row, $this->registry);
        }
        return null;
    }

    public function findReferenceForDate($date)
    {
        $output = [];
        $prefix = DB_PREFIX;
        $row = $this->db->query("select reference_id from {$prefix}pplcz_collections where send_date = '". $this->db->escape($date ) . "'");
        foreach ($row->rows as $item) {
            $output[] = $item["reference_id"];
        }
        return $output;
    }

    public function availableCollections()
    {
        $collection = [];
        $prefix = DB_PREFIX;
        $row = $this->db->query("select * from {$prefix}pplcz_collections where state is null or state = '' order by created_date");

        foreach ($row->rows as $result)
        {
            $collection[] = CollectionData::fromArray($result, $this->registry);
        }

        return $collection;
    }


    public function readCollections($args = [])
    {


        $filter = ["1 = 1"];

        if (isset($args['state']) && $args["state"]) {
            $filter[] = " state in (" . join(',', array_map(function ($item) {
                    return "'" . $this->db->escape($item) . "'";
                }, $args['state'])) . ')';
        }

        $d = (new \DateTime())->sub(new \DateInterval('P10D'));

        $filter[] = " send_date > '" . $d->format("Y-m-d") ."'";

        $collection = [];

        $prefix = DB_PREFIX;

        $result = $this->db->query("select * from {$prefix}pplcz_collections where " . join(" AND ", $filter ). " order by send_date desc");

        foreach ($result->rows as $result)
        {
            $collection[] = CollectionData::fromArray($result, $this->registry);
        }

        return $collection;
    }

}