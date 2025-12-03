<?php

namespace PPLCZ\Model\Model;

class UpdateParcelModel extends \ArrayObject
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
    protected $parcelCode;
    /**
     * 
     *
     * @return string|null
     */
    public function getParcelCode() : ?string
    {
        return $this->parcelCode;
    }
    /**
     * 
     *
     * @param string|null $parcelCode
     *
     * @return self
     */
    public function setParcelCode(?string $parcelCode) : self
    {
        $this->initialized['parcelCode'] = true;
        $this->parcelCode = $parcelCode;
        return $this;
    }
}