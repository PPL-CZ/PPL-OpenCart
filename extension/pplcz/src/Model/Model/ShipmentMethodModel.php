<?php

namespace PPLCZ\Model\Model;

class ShipmentMethodModel extends \ArrayObject
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
     * @var string
     */
    protected $code;
    /**
     * 
     *
     * @var string
     */
    protected $title;
    /**
     * 
     *
     * @var bool
     */
    protected $codAvailable;
    /**
     * 
     *
     * @var bool
     */
    protected $parcelRequired;
    /**
     * 
     *
     * @return string
     */
    public function getCode() : ?string
    {
        return $this->code;
    }
    /**
     * 
     *
     * @param string $code
     *
     * @return self
     */
    public function setCode(string $code) : self
    {
        $this->initialized['code'] = true;
        $this->code = $code;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }
    /**
     * 
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title) : self
    {
        $this->initialized['title'] = true;
        $this->title = $title;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getCodAvailable() : ?bool
    {
        return $this->codAvailable;
    }
    /**
     * 
     *
     * @param bool $codAvailable
     *
     * @return self
     */
    public function setCodAvailable(bool $codAvailable) : self
    {
        $this->initialized['codAvailable'] = true;
        $this->codAvailable = $codAvailable;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getParcelRequired() : ?bool
    {
        return $this->parcelRequired;
    }
    /**
     * 
     *
     * @param bool $parcelRequired
     *
     * @return self
     */
    public function setParcelRequired(bool $parcelRequired) : self
    {
        $this->initialized['parcelRequired'] = true;
        $this->parcelRequired = $parcelRequired;
        return $this;
    }
}