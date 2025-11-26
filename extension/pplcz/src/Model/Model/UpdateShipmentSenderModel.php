<?php

namespace PPLCZ\Model\Model;

class UpdateShipmentSenderModel extends \ArrayObject
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
     * @var float|null
     */
    protected $senderId;
    /**
     * 
     *
     * @return float|null
     */
    public function getSenderId() : ?float
    {
        return $this->senderId;
    }
    /**
     * 
     *
     * @param float|null $senderId
     *
     * @return self
     */
    public function setSenderId(?float $senderId) : self
    {
        $this->initialized['senderId'] = true;
        $this->senderId = $senderId;
        return $this;
    }
}