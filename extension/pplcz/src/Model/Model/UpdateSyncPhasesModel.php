<?php

namespace PPLCZ\Model\Model;

class UpdateSyncPhasesModel extends \ArrayObject
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
    protected $maxSync;
    /**
     * 
     *
     * @var UpdateSyncPhasesModelPhasesItem[]|null
     */
    protected $phases;
    /**
     * 
     *
     * @return int|null
     */
    public function getMaxSync() : ?int
    {
        return $this->maxSync;
    }
    /**
     * 
     *
     * @param int|null $maxSync
     *
     * @return self
     */
    public function setMaxSync(?int $maxSync) : self
    {
        $this->initialized['maxSync'] = true;
        $this->maxSync = $maxSync;
        return $this;
    }
    /**
     * 
     *
     * @return UpdateSyncPhasesModelPhasesItem[]|null
     */
    public function getPhases() : ?array
    {
        return $this->phases;
    }
    /**
     * 
     *
     * @param UpdateSyncPhasesModelPhasesItem[]|null $phases
     *
     * @return self
     */
    public function setPhases(?array $phases) : self
    {
        $this->initialized['phases'] = true;
        $this->phases = $phases;
        return $this;
    }
}