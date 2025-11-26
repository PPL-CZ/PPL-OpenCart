<?php
namespace PPLCZ\Repository;

use Opencart\System\Engine\Model;
use PPLCZ\Data\PPLData;


class Data extends Model
{
    public function loadModel($route)
    {
        $loader = $this->registry->get("load");
        $loader->model($route);
        $modelid = "model_" . str_replace("/", "_", $route);
        return $this->registry->get($modelid);
    }

    protected function select($table, $where)
    {
        $where = join(" AND ", array_map(function ($item, $key) {
            $value = "null";

            if ($item !== null) {
                if (is_int($item))
                    $value = (int)$item;
                if (is_numeric($item))
                    $value =(float)$item;
                $value = "'" . $this->db->escape($item) . "'";
            }
            if ($value === 'null')
                return "`$key` is null";
            else
                return "`$key` = $value";
        }, $where, array_keys($where)));

        $prefix = DB_PREFIX;

        $query = $this->db->query("SELECT * FROM {$prefix}$table WHERE {$where}");
        return $query->rows;
    }

    protected function insert($table, $values)
    {
        $fields = join(', ', array_map(function ($item) {
            return "`$item`";
        }, array_keys($values)));

        $values = join(', ', array_map(function ($item) {
            if ($item !== null) {
                if (is_int($item))
                    return (int)$item;
                if (is_numeric($item))
                    return (float)$item;
                return "'" . $this->db->escape($item) . "'";
            }
            return "null";
        }, $values));

        $prefix = DB_PREFIX;

        $this->db->query("INSERT INTO `$prefix$table` ($fields) VALUES ($values)");
        return $this->db->getLastId();
    }

    protected function update($table, $values, $where)
    {
        $fields = join(", ", array_map(function ($item, $key) {
            $value = "null";

            if ($item !== null) {
                if (is_int($item))
                    $value = (int)$item;
                else if (is_numeric($item))
                    $value =(float)$item;
                else if (is_bool($value))
                    $value = (int)$value;
                else
                    $value = "'" . $this->db->escape($item) . "'";
            }
            return "`$key` = $value";
        }, $values, array_keys($values)));

        $prefix = DB_PREFIX;

        $where = join(" AND ", array_map(function ($item, $key) {
            $value = "null";

            if ($item !== null) {
                if (is_int($item))
                    $value = (int)$item;
                else if (is_numeric($item))
                    $value =(float)$item;
                else if (is_bool($value))
                    $value = (int)$value;
                else
                    $value = "'" . $this->db->escape($item) . "'";
            }
            if ($value === 'null')
                return "`$key` is null";
            else
                return "`$key` = $value";
        }, $where, array_keys($where)));

        $this->db->query("UPDATE `$prefix$table` SET $fields  WHERE $where");
    }

    protected function delete($table, $where)
    {

        $where = join(" AND ", array_map(function ($item, $key) {
            $value = "null";

            if ($item !== null) {
                if (is_int($item))
                    $value = (int)$item;
                else if (is_numeric($item))
                    $value =(float)$item;
                else if (is_bool($value))
                    $value = (int)$value;
                else
                    $value = "'" . $this->db->escape($item) . "'";
            }
            if ($value === 'null')
                return "`$key` is null";
            else
                return "`$key` = $value";
        }, $where, array_keys($where)));

        $prefix = DB_PREFIX;

        $this->db->query("DELETE FROM `$prefix$table` WHERE $where");
    }

    /**
     * @param PPLData|string $item
     * @param $id
     * @return void
     */
    public function remove($item, $id = null)
    {
        if ($item instanceof PPLData) {
            if (isset($item->lock) && $item->lock && !$item->isLockDisabled())
                throw new \Exception("locked");

            $key = $item->key;
            $table = $item->table;
            $fields = $item->toArray();
            if (isset($fields[$key]) && $fields[$key])
                $this->delete($table, [$key => $fields[$key]]);
        }
        else
        {
            $item = $this->load($item, $id);
            $this->remove($item);
        }
    }

    public function new($type, $defaults)
    {
        $data = new $type($this->registry);
        foreach ($defaults as $key => $value)
        {
            $data->$key = $value;
        }
        return $data;
    }

    public function load($type, $id)
    {
        $item = new $type($this->registry);
        $key = $item->key;
        $table = $item->table;
        $rows = $this->select($table, [
            $key => $id
        ]);
        if ($rows)
            return $type::fromArray(reset($rows), $this->registry);
        return null;
    }

    public function save(PPLData $ppldata)
    {
        $array = $ppldata->toArray();
        $key = $ppldata->key;
        $table = $ppldata->table;

        if (isset($array[$key]) && $array[$key])
        {
            if (isset($ppldata->lock) && $ppldata->lock && !$ppldata->isLockDisabled())
                throw new \Exception("locked");

            $this->update($table, $array, [$key => $array[$key]]);
        }
        else {
            unset($array[$key]);
            $id = $this->insert($table, $array);
            $ppldata->id = $id;
        }
    }
}