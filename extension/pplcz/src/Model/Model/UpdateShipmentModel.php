<?php

namespace PPLCZ\Model\Model;

class UpdateShipmentModel extends \ArrayObject
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
    protected $referenceId;
    /**
     * 
     *
     * @var string|null
     */
    protected $codVariableNumber;
    /**
     * 
     *
     * @var float|null
     */
    protected $codValue;
    /**
     * 
     *
     * @var string|null
     */
    protected $codValueCurrency;
    /**
     * 
     *
     * @var float|null
     */
    protected $codBankAccountId;
    /**
     * 
     *
     * @var float|null
     */
    protected $senderId;
    /**
     * 
     *
     * @var string|null
     */
    protected $serviceCode;
    /**
     * 
     *
     * @var float|null
     */
    protected $orderId;
    /**
     * 
     *
     * @var bool|null
     */
    protected $hasParcel;
    /**
     * 
     *
     * @var float|null
     */
    protected $parcelId;
    /**
     * 
     *
     * @var string|null
     */
    protected $age;
    /**
     * 
     *
     * @var string|null
     */
    protected $note;
    /**
     * 
     *
     * @var PackageModel[]|null
     */
    protected $packages;
    /**
     * 
     *
     * @return string|null
     */
    public function getReferenceId() : ?string
    {
        return $this->referenceId;
    }
    /**
     * 
     *
     * @param string|null $referenceId
     *
     * @return self
     */
    public function setReferenceId(?string $referenceId) : self
    {
        $this->initialized['referenceId'] = true;
        $this->referenceId = $referenceId;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getCodVariableNumber() : ?string
    {
        return $this->codVariableNumber;
    }
    /**
     * 
     *
     * @param string|null $codVariableNumber
     *
     * @return self
     */
    public function setCodVariableNumber(?string $codVariableNumber) : self
    {
        $this->initialized['codVariableNumber'] = true;
        $this->codVariableNumber = $codVariableNumber;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getCodValue() : ?float
    {
        return $this->codValue;
    }
    /**
     * 
     *
     * @param float|null $codValue
     *
     * @return self
     */
    public function setCodValue(?float $codValue) : self
    {
        $this->initialized['codValue'] = true;
        $this->codValue = $codValue;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getCodValueCurrency() : ?string
    {
        return $this->codValueCurrency;
    }
    /**
     * 
     *
     * @param string|null $codValueCurrency
     *
     * @return self
     */
    public function setCodValueCurrency(?string $codValueCurrency) : self
    {
        $this->initialized['codValueCurrency'] = true;
        $this->codValueCurrency = $codValueCurrency;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getCodBankAccountId() : ?float
    {
        return $this->codBankAccountId;
    }
    /**
     * 
     *
     * @param float|null $codBankAccountId
     *
     * @return self
     */
    public function setCodBankAccountId(?float $codBankAccountId) : self
    {
        $this->initialized['codBankAccountId'] = true;
        $this->codBankAccountId = $codBankAccountId;
        return $this;
    }
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
    /**
     * 
     *
     * @return string|null
     */
    public function getServiceCode() : ?string
    {
        return $this->serviceCode;
    }
    /**
     * 
     *
     * @param string|null $serviceCode
     *
     * @return self
     */
    public function setServiceCode(?string $serviceCode) : self
    {
        $this->initialized['serviceCode'] = true;
        $this->serviceCode = $serviceCode;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getOrderId() : ?float
    {
        return $this->orderId;
    }
    /**
     * 
     *
     * @param float|null $orderId
     *
     * @return self
     */
    public function setOrderId(?float $orderId) : self
    {
        $this->initialized['orderId'] = true;
        $this->orderId = $orderId;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getHasParcel() : ?bool
    {
        return $this->hasParcel;
    }
    /**
     * 
     *
     * @param bool|null $hasParcel
     *
     * @return self
     */
    public function setHasParcel(?bool $hasParcel) : self
    {
        $this->initialized['hasParcel'] = true;
        $this->hasParcel = $hasParcel;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getParcelId() : ?float
    {
        return $this->parcelId;
    }
    /**
     * 
     *
     * @param float|null $parcelId
     *
     * @return self
     */
    public function setParcelId(?float $parcelId) : self
    {
        $this->initialized['parcelId'] = true;
        $this->parcelId = $parcelId;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getAge() : ?string
    {
        return $this->age;
    }
    /**
     * 
     *
     * @param string|null $age
     *
     * @return self
     */
    public function setAge(?string $age) : self
    {
        $this->initialized['age'] = true;
        $this->age = $age;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getNote() : ?string
    {
        return $this->note;
    }
    /**
     * 
     *
     * @param string|null $note
     *
     * @return self
     */
    public function setNote(?string $note) : self
    {
        $this->initialized['note'] = true;
        $this->note = $note;
        return $this;
    }
    /**
     * 
     *
     * @return PackageModel[]|null
     */
    public function getPackages() : ?array
    {
        return $this->packages;
    }
    /**
     * 
     *
     * @param PackageModel[]|null $packages
     *
     * @return self
     */
    public function setPackages(?array $packages) : self
    {
        $this->initialized['packages'] = true;
        $this->packages = $packages;
        return $this;
    }
}