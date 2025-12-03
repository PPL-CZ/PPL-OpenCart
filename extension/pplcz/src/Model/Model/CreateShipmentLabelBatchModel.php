<?php

namespace PPLCZ\Model\Model;

class CreateShipmentLabelBatchModel extends \ArrayObject
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
     * @var string|null
     */
    protected $printSetting;
    /**
     * 
     *
     * @var int[]|null
     */
    protected $shipmentId;
    /**
     * 
     *
     * @return string|null
     */
    public function getPrintSetting() : ?string
    {
        return $this->printSetting;
    }
    /**
     * 
     *
     * @param string|null $printSetting
     *
     * @return self
     */
    public function setPrintSetting(?string $printSetting) : self
    {
        $this->initialized['printSetting'] = true;
        $this->printSetting = $printSetting;
        return $this;
    }
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