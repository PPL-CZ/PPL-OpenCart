<?php

namespace PPLCZ\Model\Model;

class RefreshShipmentBatchReturnModel extends \ArrayObject
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
     * @var string[]|null
     */
    protected $batchs;
    /**
     * 
     *
     * @var ShipmentModel[]|null
     */
    protected $shipments;
    /**
     * 
     *
     * @return string[]|null
     */
    public function getBatchs() : ?array
    {
        return $this->batchs;
    }
    /**
     * 
     *
     * @param string[]|null $batchs
     *
     * @return self
     */
    public function setBatchs(?array $batchs) : self
    {
        $this->initialized['batchs'] = true;
        $this->batchs = $batchs;
        return $this;
    }
    /**
     * 
     *
     * @return ShipmentModel[]|null
     */
    public function getShipments() : ?array
    {
        return $this->shipments;
    }
    /**
     * 
     *
     * @param ShipmentModel[]|null $shipments
     *
     * @return self
     */
    public function setShipments(?array $shipments) : self
    {
        $this->initialized['shipments'] = true;
        $this->shipments = $shipments;
        return $this;
    }
}