<?php

namespace PPLCZ\Model\Model;

class CountryModel extends \ArrayObject
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
    protected $parcelAllowed;
    /**
     * 
     *
     * @var string[]
     */
    protected $codAllowed;
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
    public function getParcelAllowed() : ?bool
    {
        return $this->parcelAllowed;
    }
    /**
     * 
     *
     * @param bool $parcelAllowed
     *
     * @return self
     */
    public function setParcelAllowed(bool $parcelAllowed) : self
    {
        $this->initialized['parcelAllowed'] = true;
        $this->parcelAllowed = $parcelAllowed;
        return $this;
    }
    /**
     * 
     *
     * @return string[]
     */
    public function getCodAllowed() : ?array
    {
        return $this->codAllowed;
    }
    /**
     * 
     *
     * @param string[] $codAllowed
     *
     * @return self
     */
    public function setCodAllowed(array $codAllowed) : self
    {
        $this->initialized['codAllowed'] = true;
        $this->codAllowed = $codAllowed;
        return $this;
    }
}