<?php

namespace PPLCZ;


use Opencart\System\Engine\Loader;
use Opencart\System\Engine\Registry;

trait TLoader
{
    /**
     * @var Registry $registry
     */
    protected $registry;

    public function setRegistry($registry)
    {
        $this->registry = $registry;
        $this->loader = $registry->get("load");
    }

    /**
     * @var Loader $loader
     */
    protected $loader;

    public function loadModel($route)
    {
        $this->loader->model($route);
        $modelid = "model_" . str_replace("/", "_", $route);
        return $this->registry->get($modelid);
    }

    public function loadLibrary($route)
    {
        $this->loader->library($route);
        $modelid = "$route";
        return $this->registry->get($modelid);
    }

    public function simle_guid()
    {
        $timestamp = time();

        $raw_string = $timestamp;

        $hash = hash('sha256', $raw_string);
        $time = (new \DateTime())->format("YmdHis");
        $guid = sprintf('%s-%08s-%04s-%04s-%04s-%12s',
            substr($time, 2),
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            substr($hash, 12, 4),
            substr($hash, 16, 4),
            substr($hash, 20, 12)
        );

        return $guid;
    }
}