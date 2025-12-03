<?php

namespace PPLCZ\Model\Model;

class NewCollectionModel extends \ArrayObject
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
    protected $sendDate;
    /**
     * 
     *
     * @var string|null
     */
    protected $contact;
    /**
     * 
     *
     * @var float|null
     */
    protected $estimatedShipmentCount;
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