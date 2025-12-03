<?php

namespace PPLCZ\Model\Model;

class PrepareShipmentBatchItemModel extends \ArrayObject
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
     * @var int|null
     */
    protected $orderId;
    /**
     * 
     *
     * @var int|null
     */
    protected $shipmentId;
    /**
     * 
     *
     * @return int|null
     */
    public function getOrderId() : ?int
    {
        return $this->orderId;
    }
    /**
     * 
     *
     * @param int|null $orderId
     *
     * @return self
     */
    public function setOrderId(?int $orderId) : self
    {
        $this->initialized['orderId'] = true;
        $this->orderId = $orderId;
        return $this;
    }
    /**
     * 
     *
     * @return int|null
     */
    public function getShipmentId() : ?int
    {
        return $this->shipmentId;
    }
    /**
     * 
     *
     * @param int|null $shipmentId
     *
     * @return self
     */
    public function setShipmentId(?int $shipmentId) : self
    {
        $this->initialized['shipmentId'] = true;
        $this->shipmentId = $shipmentId;
        return $this;
    }
}