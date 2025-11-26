<?php

namespace PPLCZ\Admin\Controller;


use Opencart\Admin\Model\Extension\Pplcz\Config;
use Opencart\Admin\Model\Extension\Pplcz\Cploperation;
use Opencart\Admin\Model\Extension\Pplcz\Collection;
use PPLCZ\Data\CollectionData;
use PPLCZ\Model\Model\CollectionAddressModel;
use PPLCZ\Model\Model\CollectionModel;
use PPLCZ\Model\Model\NewCollectionModel;
use PPLCZ\Validator\WP_Error;

/**
 * @property-read Config $model_extension_pplcz_config
 * @property-read Cploperation $model_extension_pplcz_cploperation
 * @property-read Collection $model_extension_pplcz_collection
 * @mixin BaseController
 */
trait TCollection
{
    public function getCollectionAddress()
    {

        if (!$this->validateToken() || !$this->validateMethod(["GET"]))
            return;

        $this->load->model("extension/pplcz/cploperation");


        $address = $this->model_extension_pplcz_cploperation->getCollectionAddress();
        if (!$address)
            $this->sendJson(null, 404);
        $this->load->model("extension/pplcz/normalizer");
        $address = $this->model_extension_pplcz_normalizer->denormalize($address, CollectionAddressModel::class);

        $this->sendJson($address);
    }

    public function createCollection()
    {
        if (!$this->validateToken() || !$this->validateMethod(["POST"]))
            return;


        $this->load->model("extension/pplcz/validator");
        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/collection");
        $this->load->model("extension/pplcz/cplcollection");

        $data = $this->getData();
        $data = $this->model_extension_pplcz_normalizer->denormalize($data, NewCollectionModel::class);

        $errors = new WP_Error();
        $this->model_extension_pplcz_validator->validate($data, $errors);

        if ($errors->errors) {
            $this->sendJson($errors);
            return;
        }

        $data = $this->model_extension_pplcz_normalizer->denormalize($data, CollectionData::class);

        $this->model_extension_pplcz_collection->save($data);
        try {
            $this->model_extension_pplcz_cploperation->createCollection($data->id);
        }
        catch (\Exception $ex) {

        }
        $this->sendJson(null, 201);
    }

    public function getCollection()
    {
        $id = $this->request->get["collection_id"];

        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/collection");

        $collection = $this->model_extension_pplcz_collection->load(CollectionData::class, $id);
        if (!$collection)
            $this->sendJson(null, 404);

        $data = $this->model_extension_pplcz_normalizer->denormalize($collection, CollectionModel::class);

        $this->sendJson($data);
    }

    public function getLastCollection()
    {
        $this->load->model("extension/pplcz/collection");
        $last = $this->model_extension_pplcz_collection->getLastCollection();
        if (!$last) {
            $this->sendJson(null, 404);
            return;
        }
        $this->load->model("extension/pplcz/normalizer");
        $last = $this->model_extension_pplcz_normalizer->denormalize($last, CollectionModel::class);

        $this->sendJson($last);
    }

    public function getCollections()
    {
        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/collection");

        $collection = $this->model_extension_pplcz_collection->readCollections();

        foreach ($collection as $key => $coll) {
            $collection[$key] = $this->model_extension_pplcz_normalizer->denormalize($coll, CollectionModel::class);
        }

        $this->sendJson($collection);
    }

    public function orderCollection()
    {
        if (!$this->validateMethod(['DELETE', 'PUT'])
            || !$this->validateToken())
        {
            return;
        }

        $this->load->model("extension/pplcz/cploperation");

        if ($this->request->server['REQUEST_METHOD'] === 'DELETE')
        {
            $this->model_extension_pplcz_cploperation->cancelCollection($this->request->get['collection_id']);
            $this->sendJson(null, 204);
        }
        else {
            $this->model_extension_pplcz_cploperation->createCollection($this->request->get['collection_id']);
            $this->sendJson(null, 204);
        }

    }

}