<?php

namespace PPLCZ\Model\Model;

class CollectionModel extends \ArrayObject
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
     * @var float
     */
    protected $id;
    /**
     * 
     *
     * @var string|null
     */
    protected $remoteCollectionId;
    /**
     * 
     *
     * @var string
     */
    protected $referenceId;
    /**
     * 
     *
     * @var string
     */
    protected $createdDate;
    /**
     * 
     *
     * @var string
     */
    protected $sendDate;
    /**
     * 
     *
     * @var string|null
     */
    protected $sendToApiDate;
    /**
     * 
     *
     * @var string
     */
    protected $state;
    /**
     * 
     *
     * @var float|null
     */
    protected $estimatedShipmentCount;
    /**
     * 
     *
     * @var float
     */
    protected $shipmentCount;
    /**
     * 
     *
     * @var string|null
     */
    protected $contact;
    /**
     * 
     *
     * @var string|null
     */
    protected $telephone;
    /**
     * 
     *
     * @var string|null
     */
    protected $note;
    /**
     * 
     *
     * @var string|null
     */
    protected $email;
    /**
     * 
     *
     * @return float
     */
    public function getId() : ?float
    {
        return $this->id;
    }
    /**
     * 
     *
     * @param float $id
     *
     * @return self
     */
    public function setId(float $id) : self
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
    public function getRemoteCollectionId() : ?string
    {
        return $this->remoteCollectionId;
    }
    /**
     * 
     *
     * @param string|null $remoteCollectionId
     *
     * @return self
     */
    public function setRemoteCollectionId(?string $remoteCollectionId) : self
    {
        $this->initialized['remoteCollectionId'] = true;
        $this->remoteCollectionId = $remoteCollectionId;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getReferenceId() : ?string
    {
        return $this->referenceId;
    }
    /**
     * 
     *
     * @param string $referenceId
     *
     * @return self
     */
    public function setReferenceId(string $referenceId) : self
    {
        $this->initialized['referenceId'] = true;
        $this->referenceId = $referenceId;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getCreatedDate() : ?string
    {
        return $this->createdDate;
    }
    /**
     * 
     *
     * @param string $createdDate
     *
     * @return self
     */
    public function setCreatedDate(string $createdDate) : self
    {
        $this->initialized['createdDate'] = true;
        $this->createdDate = $createdDate;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getSendDate() : ?string
    {
        return $this->sendDate;
    }
    /**
     * 
     *
     * @param string $sendDate
     *
     * @return self
     */
    public function setSendDate(string $sendDate) : self
    {
        $this->initialized['sendDate'] = true;
        $this->sendDate = $sendDate;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getSendToApiDate() : ?string
    {
        return $this->sendToApiDate;
    }
    /**
     * 
     *
     * @param string|null $sendToApiDate
     *
     * @return self
     */
    public function setSendToApiDate(?string $sendToApiDate) : self
    {
        $this->initialized['sendToApiDate'] = true;
        $this->sendToApiDate = $sendToApiDate;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getState() : ?string
    {
        return $this->state;
    }
    /**
     * 
     *
     * @param string $state
     *
     * @return self
     */
    public function setState(string $state) : self
    {
        $this->initialized['state'] = true;
        $this->state = $state;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getEstimatedShipmentCount() : ?float
    {
        return $this->estimatedShipmentCount;
    }
    /**
     * 
     *
     * @param float|null $estimatedShipmentCount
     *
     * @return self
     */
    public function setEstimatedShipmentCount(?float $estimatedShipmentCount) : self
    {
        $this->initialized['estimatedShipmentCount'] = true;
        $this->estimatedShipmentCount = $estimatedShipmentCount;
        return $this;
    }
    /**
     * 
     *
     * @return float
     */
    public function getShipmentCount() : ?float
    {
        return $this->shipmentCount;
    }
    /**
     * 
     *
     * @param float $shipmentCount
     *
     * @return self
     */
    public function setShipmentCount(float $shipmentCount) : self
    {
        $this->initialized['shipmentCount'] = true;
        $this->shipmentCount = $shipmentCount;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getContact() : ?string
    {
        return $this->contact;
    }
    /**
     * 
     *
     * @param string|null $contact
     *
     * @return self
     */
    public function setContact(?string $contact) : self
    {
        $this->initialized['contact'] = true;
        $this->contact = $contact;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getTelephone() : ?string
    {
        return $this->telephone;
    }
    /**
     * 
     *
     * @param string|null $telephone
     *
     * @return self
     */
    public function setTelephone(?string $telephone) : self
    {
        $this->initialized['telephone'] = true;
        $this->telephone = $telephone;
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
     * @return string|null
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }
    /**
     * 
     *
     * @param string|null $email
     *
     * @return self
     */
    public function setEmail(?string $email) : self
    {
        $this->initialized['email'] = true;
        $this->email = $email;
        return $this;
    }
}