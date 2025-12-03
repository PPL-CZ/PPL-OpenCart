<?php
namespace PPLCZ\Data;

use PPLCZ\TLoader;

#[\AllowDynamicProperties]
class PPLData
{

    use TLoader;

    protected $fields = [];

    protected $data = [];

    protected $defaults = [];

    public $key;

    public $table;

    protected $lockDisabled;

    public function isLockDisabled(): bool
    {
        return !!$this->lockDisabled;
    }

    public function disableLock()
    {
        $this->lockDisabled = true;
    }

    public function __construct($registry)
    {
        $this->setRegistry($registry);
        foreach ($this->defaults as $key=>$value)
            $this->data[$key] = $value;
    }

    /**
     * @param array $data
     */
    public static function fromArray($data, $registry)
    {
        $item = new static($registry);
        foreach ($item->fields as $key => $value)
        {
            if ((int)$key === $key)
                $item->data[$value] = $data[$value];
            else
                $item->data[$value] = $data[$key];
        }
        $item->registry = $registry;

        return $item;
    }

    public function toArray()
    {
        $output = [];
        foreach ($this->fields as $key =>$value)
        {
            if ((int)$key === $key)
            {
                if (isset($this->data[$value]))
                    $output[$value] = $this->data[$value];
                else
                    $output[$value] = null;
            }
            else {
                if (isset($this->data[$value]))
                    $output[$key] = $this->data[$value];
                else
                    $output[$key] = null;
            }
        }
        return $output;
    }

    public function __call($name, $arguments)
    {

        if (strpos($name, "get_") === 0) {
            $name = substr($name, 4);
            return $this->$name;
        }
        else if (strpos($name, "set_") === 0) {
            $name = substr($name, 4);
            $this->$name = reset($arguments);
        }
        else {
            throw new \Exception("Unknown method $name");
        }
    }

    public function __set($name, $value)
    {
        if (method_exists($this, "set_$name")) {
            $this->{"set_$name"}($value);
            return;
        }

        if (isset($this->fields[$name]) || in_array($name, $this->fields, true) )
        {
            $this->data[$name] = $value;
            return;
        }
        throw new \Exception("Undefined field $name");
    }

    public function __get($name)
    {
        if (method_exists($this, "get_$name")) {
            return $this->{"get_$name"}();

        }

        if (isset($this->fields[$name]) || in_array($name, $this->fields, true) )
        {
            if (isset($this->data[$name]))
                return $this->data[$name];
            return null;
        }
        throw new \Exception("Undefined field $name");
    }

    public function __isset($name)
    {
        return in_array($name, $this->fields, true);
    }


}