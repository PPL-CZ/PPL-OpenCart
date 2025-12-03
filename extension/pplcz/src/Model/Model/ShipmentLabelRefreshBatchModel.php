<?php

namespace PPLCZ\Model\Model;

class ShipmentLabelRefreshBatchModel extends \ArrayObject
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
     * @var int[]|null
     */
    protected $shipmentId;
    /**
     * 
     *
     * @return int[]|null
     */
    public function getShipmentId() : ?array
    {
        return $this->shipmentId;
    }
    /**
     * 
     *
     * @param int[]|null $shipmentId
     *
     * @return self
     */
    public function setShipmentId(?array $shipmentId) : self
    {
        $this->initialized['shipmentId'] = true;
        $this->shipmentId = $shipmentId;
        return $this;
    }
}