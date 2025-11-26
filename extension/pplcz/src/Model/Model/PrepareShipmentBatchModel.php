<?php

namespace PPLCZ\Model\Model;

class PrepareShipmentBatchModel extends \ArrayObject
{
    /**
     * @var array
     */
    protected $initialized = array();
    public function isInitialized($property) : bool
    {
        return array_key_exists($property, $this->initialized);
    }
    /**
     * 
     *
     * @var PrepareShipmentBatchItemModel[]|null
     */
    protected $items;
    /**
     * 
     *
     * @return PrepareShipmentBatchItemModel[]|null
     */
    public function getItems() : ?array
    {
        return $this->items;
    }
    /**
     * 
     *
     * @param PrepareShipmentBatchItemModel[]|null $items
     *
     * @return self
     */
    public function setItems(?array $items) : self
    {
        $this->initialized['items'] = true;
        $this->items = $items;
        return $this;
    }
}