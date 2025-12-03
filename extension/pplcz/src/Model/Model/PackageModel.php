<?php

namespace PPLCZ\Model\Model;

class PackageModel extends \ArrayObject
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
    protected $id;
    /**
     * 
     *
     * @var string|null
     */
    protected $shipmentNumber;
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
    protected $labelId;
    /**
     * 
     *
     * @var string|null
     */
    protected $importError;
    /**
     * 
     *
     * @var string|null
     */
    protected $importErrorCode;
    /**
     * 
     *
     * @var float|null
     */
    protected $weight;
    /**
     * 
     *
     * @var float|null
     */
    protected $insurance;
    /**
     * 
     *
     * @var string|null
     */
    protected $insuranceCurrency;
    /**
     * 
     *
     * @var string|null
     */
    protected $phase;
    /**
     * 
     *
     * @var string|null
     */
    protected $phaseLabel;
    /**
     * 
     *
     * @var string|null
     */
    protected $lastUpdatePhase;
    /**
     * 
     *
     * @var bool|null
     */
    protected $ignorePhase;
    /**
     * 
     *
     * @var bool|null
     */
    protected $canBeCanceled;
    /**
     * 
     *
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }
    /**
     * 
     *
     * @param int|null $id
     *
     * @return self
     */
    public function setId(?int $id) : self
    {
        $this->initialized['id'] = true;
        $this->id = $id;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getShipmentNumber() : ?string
    {
        return $this->shipmentNumber;
    }
    /**
     * 
     *
     * @param string|null $shipmentNumber
     *
     * @return self
     */
    public function setShipmentNumber(?string $shipmentNumber) : self
    {
        $this->initialized['shipmentNumber'] = true;
        $this->shipmentNumber = $shipmentNumber;
        return $this;
    }
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
    public function getLabelId() : ?string
    {
        return $this->labelId;
    }
    /**
     * 
     *
     * @param string|null $labelId
     *
     * @return self
     */
    public function setLabelId(?string $labelId) : self
    {
        $this->initialized['labelId'] = true;
        $this->labelId = $labelId;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getImportError() : ?string
    {
        return $this->importError;
    }
    /**
     * 
     *
     * @param string|null $importError
     *
     * @return self
     */
    public function setImportError(?string $importError) : self
    {
        $this->initialized['importError'] = true;
        $this->importError = $importError;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getImportErrorCode() : ?string
    {
        return $this->importErrorCode;
    }
    /**
     * 
     *
     * @param string|null $importErrorCode
     *
     * @return self
     */
    public function setImportErrorCode(?string $importErrorCode) : self
    {
        $this->initialized['importErrorCode'] = true;
        $this->importErrorCode = $importErrorCode;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getWeight() : ?float
    {
        return $this->weight;
    }
    /**
     * 
     *
     * @param float|null $weight
     *
     * @return self
     */
    public function setWeight(?float $weight) : self
    {
        $this->initialized['weight'] = true;
        $this->weight = $weight;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getInsurance() : ?float
    {
        return $this->insurance;
    }
    /**
     * 
     *
     * @param float|null $insurance
     *
     * @return self
     */
    public function setInsurance(?float $insurance) : self
    {
        $this->initialized['insurance'] = true;
        $this->insurance = $insurance;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getInsuranceCurrency() : ?string
    {
        return $this->insuranceCurrency;
    }
    /**
     * 
     *
     * @param string|null $insuranceCurrency
     *
     * @return self
     */
    public function setInsuranceCurrency(?string $insuranceCurrency) : self
    {
        $this->initialized['insuranceCurrency'] = true;
        $this->insuranceCurrency = $insuranceCurrency;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getPhase() : ?string
    {
        return $this->phase;
    }
    /**
     * 
     *
     * @param string|null $phase
     *
     * @return self
     */
    public function setPhase(?string $phase) : self
    {
        $this->initialized['phase'] = true;
        $this->phase = $phase;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getPhaseLabel() : ?string
    {
        return $this->phaseLabel;
    }
    /**
     * 
     *
     * @param string|null $phaseLabel
     *
     * @return self
     */
    public function setPhaseLabel(?string $phaseLabel) : self
    {
        $this->initialized['phaseLabel'] = true;
        $this->phaseLabel = $phaseLabel;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getLastUpdatePhase() : ?string
    {
        return $this->lastUpdatePhase;
    }
    /**
     * 
     *
     * @param string|null $lastUpdatePhase
     *
     * @return self
     */
    public function setLastUpdatePhase(?string $lastUpdatePhase) : self
    {
        $this->initialized['lastUpdatePhase'] = true;
        $this->lastUpdatePhase = $lastUpdatePhase;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getIgnorePhase() : ?bool
    {
        return $this->ignorePhase;
    }
    /**
     * 
     *
     * @param bool|null $ignorePhase
     *
     * @return self
     */
    public function setIgnorePhase(?bool $ignorePhase) : self
    {
        $this->initialized['ignorePhase'] = true;
        $this->ignorePhase = $ignorePhase;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getCanBeCanceled() : ?bool
    {
        return $this->canBeCanceled;
    }
    /**
     * 
     *
     * @param bool|null $canBeCanceled
     *
     * @return self
     */
    public function setCanBeCanceled(?bool $canBeCanceled) : self
    {
        $this->initialized['canBeCanceled'] = true;
        $this->canBeCanceled = $canBeCanceled;
        return $this;
    }
}