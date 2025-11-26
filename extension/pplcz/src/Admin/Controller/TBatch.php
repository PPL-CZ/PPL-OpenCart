<?php
namespace PPLCZ\Admin\Controller;

use Opencart\Admin\Model\Extension\Pplcz\Config;
use Opencart\Admin\Model\Extension\Pplcz\Cploperation;
use Opencart\System\Engine\Controller;
use PPLCZ\Data\BatchData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\BatchModel;
use PPLCZ\Model\Model\CreateShipmentLabelBatchModel;
use PPLCZ\Model\Model\CurrencyModel;
use PPLCZ\Model\Model\PrepareShipmentBatchModel;
use PPLCZ\Model\Model\PrepareShipmentBatchReturnModel;
use PPLCZ\Model\Model\RefreshShipmentBatchReturnModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\ShipmentWithAdditionalModel;
use PPLCZ\Repository\Batch;
use PPLCZ\Repository\Shipment;
use PPLCZ\Validator\WP_Error;


/**
 * @property-read Config $model_extension_pplcz_config
 * @property-read Cploperation $model_extension_pplcz_cploperation
 * @property-read Batch $model_extension_pplcz_batch
 * @property-read Shipment $model_extension_pplcz_shipment
 * @mixin BaseController
 */
trait TBatch
{
    public function getBatches()
    {
        if (!$this->validateMethod("GET" )
            || !$this->validateToken())
        {
            return;
        }

        $this->load->model("extension/pplcz/batch");
        $this->load->model("extension/pplcz/normalizer");

        $batches = $this->model_extension_pplcz_batch->getBatches();
        $batches = array_map(function ($batch) {
            return $this->model_extension_pplcz_normalizer->denormalize($batch, BatchModel::class);
        }, $batches);

        $this->sendJson($batches);
    }

    public function getBatchShipments()
    {
        if (!$this->validateMethod("GET" )
            || !$this->validateToken())
        {
            return;
        }

        $batch_id = $this->request->get['batch_id'];

        $this->load->model("extension/pplcz/shipment");
        $this->load->model("extension/pplcz/normalizer");

        $shipments = $this->model_extension_pplcz_shipment->findShipmentByBatchId($batch_id);
        $shipments = array_map(function ($batch) {
            return $this->model_extension_pplcz_normalizer->denormalize($batch, ShipmentWithAdditionalModel::class);
        }, $shipments);

        $this->sendJson($shipments);
    }

    public function reorderShipment()
    {
        if (!$this->validateMethod("PUT" )
            || !$this->validateToken())
        {
            return;
        }
        $this->load->model("extension/pplcz/shipment");
        $this->model_extension_pplcz_shipment->updateShipmentPosition($this->request->get['shipment_id'], $this->request->get['position']);
        $this->sendJson(null, 204);
    }

    public function removeBatchShipment()
    {
        if (!$this->validateMethod("DELETE" )
            || !$this->validateToken())
        {
            return;
        }

        $this->load->model("extension/pplcz/shipment");
        /**
         * @var ShipmentData $shipment
         */
        $this->model_extension_pplcz_shipment->removeShipmentFromBatch($this->request->get['shipment_id']);
        $this->sendJson(null, 204);
    }

    public function prepareBatchLabelShipments()
    {
        if (!$this->validateMethod("POST" ) || !$this->validateToken())
            return;

        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/validator");
        $this->load->model("extension/pplcz/shipment");
        $this->load->model("extension/pplcz/batch");

        /**
         * @var ShipmentData[] $shipments
         */
        $shipments = $this->model_extension_pplcz_shipment->findShipmentByBatchId($this->request->get['batch_id']);

        /**
         * @var PrepareShipmentBatchModel $prepareShipmentsModel
         */
        $prepareShipmentsModel = $this->model_extension_pplcz_normalizer->denormalize($this->getData(true), PrepareShipmentBatchModel::class);

        $wpError = new WP_Error();

        $items = $prepareShipmentsModel->getItems();


        foreach ($shipments as $shipment) {
            if (!$shipment->import_state || $shipment->import_state === 'None')
            {
                $shipmentModel = $this->model_extension_pplcz_normalizer->denormalize($shipment, ShipmentModel::class);
                $finded = array_filter($items, function($item) use($shipment) {
                   return "{$shipment->id}" === "{$item->getShipmentId()}";
                });

                $key = key($finded);
                if ($key !== null) {
                    unset($items[$key]);
                    $this->model_extension_pplcz_validator->validate($shipmentModel, $wpError, "item.$key");
                }
            }
        }

        if ($items)
        {
            $wpError->add("item", "Chybějící zásilky pro validaci");
        }

        if ($wpError->errors) {
            $this->sendJson($wpError);
            return;
        }

        $returnModel = new PrepareShipmentBatchReturnModel();
        $returnModel->setShipmentId(array_map(function($item){
            return $item->id;
        }, $shipments));

        $this->sendJson($returnModel);

    }

    public function createBatchLabelShipments()
    {
        if (!$this->validateMethod("POST" ) || !$this->validateToken())
            return;

        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/shipment");
        $this->load->model("extension/pplcz/setting");
        $this->load->model("extension/pplcz/cploperation");




        $data = $this->getData(true);

        $createModel = $this->model_extension_pplcz_normalizer->denormalize($data, CreateShipmentLabelBatchModel::class);

        if ($createModel->getPrintSetting())
            $this->model_extension_pplcz_setting->setPrint($createModel->getPrintSetting());

        $result = $this->model_extension_pplcz_cploperation->createPackages($this->request->get['batch_id']);

        $shipments = $this->model_extension_pplcz_shipment->findShipmentByBatchId($this->request->get['batch_id']);

        foreach ($shipments as $key => $shipment) {
            $shipments[$key] = $this->model_extension_pplcz_normalizer->denormalize($shipment, ShipmentWithAdditionalModel::class);
        }

        $this->sendJson($shipments, $result ? 200: 400);
    }

    public function refreshBatchLabels()
    {


        if (!$this->validateMethod(["POST", "PUT"] ) || !$this->validateToken())
            return;

        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/shipment");
        $this->load->model("extension/pplcz/cploperation");

        try {
            $result = $this->model_extension_pplcz_cploperation->refreshLabels($this->request->get['batch_id']);
            $refresh = new RefreshShipmentBatchReturnModel();

            $shipments = $this->model_extension_pplcz_shipment->findShipmentByBatchId($this->request->get['batch_id']);

            foreach ($shipments as $key => $shipment) {
                $shipment = $this->model_extension_pplcz_normalizer->denormalize($shipment, ShipmentModel::class);
                $shipments[$key] = $shipment;
            }

            $refresh->setShipments($shipments);
            if ($result) {
                $refresh->setBatchs(
                    [
                        $this->createUrl("extension/pplcz/shipping/pplcz.printBatchShipment",
                                [ 'batch_id' => $this->request->get['batch_id'] ])
                    ]);
            }
            else
            {
                $refresh->setBatchs([]);
            }

            $this->sendJson($refresh);
        }
        catch (\Exception $e)
        {
            $this->sendJson(null, 500);
        }


    }

    public function cancelBatchShipment()
    {
        if (!$this->validateMethod(["PUT"] ) || !$this->validateToken())
            return;

        $this->load->model("extension/pplcz/cploperation");
        $this->model_extension_pplcz_cploperation->cancelPackage([$this->request->get['shipment_id']]);

        $this->sendJson(null, 204);
    }

    public function testBatchShipment()
    {
        if (!$this->validateMethod(["PUT"] ) || !$this->validateToken())
            return;

        $this->load->model("extension/pplcz/cploperation");
        $this->model_extension_pplcz_cploperation->testPackagesStates([$this->request->get['shipment_id']]);

        $this->sendJson(null, 204);
    }

    public function printBatchShipment()
    {
        if (!$this->validateMethod(["GET"] ) || !$this->validateToken())
            return;

        $this->load->model("extension/pplcz/cploperation");

        $this->load->model("extension/pplcz/batch");

        $batch = $this->model_extension_pplcz_batch->load(BatchData::class, $this->request->get['batch_id']);

        $shipment_id = null;
        if (isset($this->request->get['shipment_id'])) {
            $shipment_id = $this->request->get['shipment_id'];
        }

        $package_id = null;
        if (isset($this->request->get['package_id'])) {
            $package_id = $this->request->get['package_id'];
        }

        $print = null;
        if (isset($this->request->get['print'])) {
            $print = $this->request->get['print'];
        }

        $content = $this->model_extension_pplcz_cploperation->downloadLabel($batch->id, $shipment_id, $package_id, $print);

        $this->response->addHeader("Content-Type: {$content['content-type']}");
        $this->response->setOutput($content['body']);
        $this->response->output();
        exit;
    }
}