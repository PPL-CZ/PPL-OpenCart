<?php

namespace PPLCZ\Model\Model;

class WhisperAddressModel extends \ArrayObject
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
    protected $city;
    /**
     * 
     *
     * @var string|null
     */
    protected $zipCode;
    /**
     * 
     *
     * @var string|null
     */
    protected $street;
    /**
     * 
     *
     * @var string|null
     */
    protected $houseNumber;
    /**
     * 
     *
     * @var string|null
     */
    protected $evidenceNumber;
    /**
     * 
     *
     * @return string|null
     */
    public function getCity() : ?string
    {
        return $this->city;
    }
    /**
     * 
     *
     * @param string|null $city
     *
     * @return self
     */
    public function setCity(?string $city) : self
    {
        $this->initialized['city'] = true;
        $this->city = $city;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getZipCode() : ?string
    {
        return $this->zipCode;
    }
    /**
     * 
     *
     * @param string|null $zipCode
     *
     * @return self
     */
    public function setZipCode(?string $zipCode) : self
    {
        $this->initialized['zipCode'] = true;
        $this->zipCode = $zipCode;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getStreet() : ?string
    {
        return $this->street;
    }
    /**
     * 
     *
     * @param string|null $street
     *
     * @return self
     */
    public function setStreet(?string $street) : self
    {
        $this->initialized['street'] = true;
        $this->street = $street;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getHouseNumber() : ?string
    {
        return $this->houseNumber;
    }
    /**
     * 
     *
     * @param string|null $houseNumber
     *
     * @return self
     */
    public function setHouseNumber(?string $houseNumber) : self
    {
        $this->initialized['houseNumber'] = true;
        $this->houseNumber = $houseNumber;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getEvidenceNumber() : ?string
    {
        return $this->evidenceNumber;
    }
    /**
     * 
     *
     * @param string|null $evidenceNumber
     *
     * @return self
     */
    public function setEvidenceNumber(?string $evidenceNumber) : self
    {
        $this->initialized['evidenceNumber'] = true;
        $this->evidenceNumber = $evidenceNumber;
        return $this;
    }
}