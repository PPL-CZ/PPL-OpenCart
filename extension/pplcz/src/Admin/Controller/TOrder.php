<?php
namespace PPLCZ\Admin\Controller;


use Opencart\System\Library\DB;
use Opencart\System\Library\Document;
use Opencart\System\Library\Request;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\BatchData;
use PPLCZ\Data\OrderProxy;
use PPLCZ\Data\ParcelData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\PackageModel;
use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Model\RecipientAddressModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\ShipmentWithAdditionalModel;
use PPLCZ\Model\Model\UpdateParcelModel;
use PPLCZ\Model\Model\UpdateShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentSenderModel;


/**
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Setting $model_extension_pplcz_setting
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Normalizer $model_extension_pplcz_normalizer
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Parcel $model_extension_pplcz_parcel
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Cploperation $model_extension_pplcz_cploperation
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Address $model_extension_pplcz_address
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Batch $model_extension_pplcz_batch
 * @property-read  Setting $model_setting_setting
 * @mixin BaseController
 */
trait TOrder
{

    public function getShipment()
    {
        $this->load->model("extension/pplcz/shipment");
        $this->load->model("extension/pplcz/normalizer");

        $shipmentData = $this->model_extension_pplcz_shipment->load(ShipmentData::class, $this->request->get["shipment_id"]);
        $shipmentModel = $this->model_extension_pplcz_normalizer->denormalize($shipmentData, ShipmentModel::class);
        $this->sendJson($shipmentModel);
    }

    public function updateShipment()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['POST','PUT',"PATCH"]))
            return;

        $data = $this->getData(true);
        $this->load->model("extension/pplcz/normalizer");

        /**
         * @var UpdateShipmentModel $updateShipment
         */
        $updateShipment = $this->model_extension_pplcz_normalizer->denormalize($data, UpdateShipmentModel::class);
        if (!$this->validateDataSendIfError($updateShipment))
        {
            return;
        }
        $shipment_id = $this->request->get['shipment_id'];

        $this->load->model("extension/pplcz/shipment");

        $shipment = $this->model_extension_pplcz_shipment->load(ShipmentData::class, $shipment_id);

        $this->load->model("extension/pplcz/normalizer");

        try {
            $this->db->query("start transaction");
            $shipment = $this->model_extension_pplcz_normalizer->denormalize($updateShipment, ShipmentData::class, ['data' => $shipment]);
            $shipment->import_errors = null;
            $this->model_extension_pplcz_shipment->save($shipment);
        }
        catch (\Exception $e) {
            $this->db->query("rollback");
            throw $e;
        }
        finally {
            $this->db->query("commit");
            $this->response->addHeader("Location: /location/" . $shipment->id);
        }
        $this->sendJson([], 204);

    }

    public function updateRecipient()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['POST','PUT',"PATCH"]))
            return;
        $data = $this->getData(true);
        $this->load->model("extension/pplcz/normalizer");


        /**
         * @var RecipientAddressModel $updateRecipient
         */
        $updateRecipient = $this->model_extension_pplcz_normalizer->denormalize($data, RecipientAddressModel::class);
        if (!$this->validateDataSendIfError($updateRecipient))
        {
            return;
        }
        $shipment_id = $this->request->get['shipment_id'];

        $this->load->model("extension/pplcz/shipment");

        $shipment = $this->model_extension_pplcz_shipment->load(ShipmentData::class, $shipment_id);

        try {
            $this->db->query("start transaction");
            $shipment = $this->model_extension_pplcz_normalizer->denormalize($updateRecipient, ShipmentData::class, ['data' => $shipment]);
            $shipment->import_errors = null;
            $this->model_extension_pplcz_shipment->save($shipment);
        }
        catch (\Exception $e) {
            $this->db->query("rollback");
            throw $e;
        }
        finally {
            $this->db->query("commit");
            $this->response->addHeader("Location: /location/" . $shipment->id);
        }
    }

    public function updateSender()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['POST','PUT',"PATCH"]))
            return;

        $data = $this->getData(true);
        $this->load->model("extension/pplcz/normalizer");

        /**
         * @var UpdateShipmentSenderModel $updateShipmentSenderModel
         */
        $updateShipmentSenderModel = $this->model_extension_pplcz_normalizer->denormalize($data, UpdateShipmentSenderModel::class);
        if (!$this->validateDataSendIfError($updateShipmentSenderModel))
        {
            return;
        }
        $shipment_id = $this->request->get['shipment_id'];

        $this->load->model("extension/pplcz/shipment");
        /**
         * @var ShipmentData $shipment
         */
        $shipment = $this->model_extension_pplcz_shipment->load(ShipmentData::class, $shipment_id);

        $this->load->model("extension/pplcz/address");

        try {
            $this->db->query("start transaction");
            $sender = $this->model_extension_pplcz_address->load(AddressData::class, $updateShipmentSenderModel->getSenderId());
            $shipment->sender_address_id = $sender->id;
            $shipment->import_errors = null;
            $this->model_extension_pplcz_shipment->save($shipment);
        }
        catch (\Exception $e) {
            $this->db->query("rollback");
            throw $e;
        }
        finally {
            $this->db->query("commit");
            $this->response->addHeader("Location: /location/" . $shipment->id);
        }
        $this->sendJson([], 204);

    }

    public function updateParcel()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['POST','PUT',"PATCH"]))
            return;

        $data = $this->getData(true);
        $this->load->model("extension/pplcz/normalizer");


        /**
         * @var UpdateParcelModel $updateParcel
         */
        $updateParcel = $this->model_extension_pplcz_normalizer->denormalize($data, UpdateParcelModel::class);

        if (!$this->validateDataSendIfError($updateParcel))
        {
            return;
        }

        $shipment_id = $this->request->get['shipment_id'];

        $this->load->model("extension/pplcz/shipment");
        $this->load->model("extension/pplcz/parcel");
        $this->load->model("extension/pplcz/cploperation");
        /**
         * @var ShipmentData $shipment
         * @var ParcelData $parcel
         */
        $shipment = $this->model_extension_pplcz_shipment->load(ShipmentData::class, $shipment_id);
        $parcel = $this->model_extension_pplcz_parcel->getAccessPointByCode($updateParcel->getParcelCode());

        if (!$parcel)
        {
            $findedParcel = $this->model_extension_pplcz_cploperation->findParcel($updateParcel->getParcelCode());
            if ($findedParcel)
            {
                $parcel = $this->model_extension_pplcz_normalizer->denormalize($findedParcel, ParcelData::class);
                $this->model_extension_pplcz_parcel->save($parcel);
            }
        }

        try {
            $this->db->query("start transaction");
            $shipment->parcel_id = $parcel->id;
            $shipment->import_errors = null;
            $this->model_extension_pplcz_shipment->save($shipment);
        }
        catch (\Exception $e) {
            $this->db->query("rollback");
            throw $e;
        }
        finally {
            $this->db->query("commit");
            $this->response->addHeader("Location: /location/" . $shipment->id);
        }
    }

    public function removeShipment()
    {
        if (!$this->validateMethod('DELETE'))
        {
            return;
        }

        $shipment_id = $this->request->get['shipment_id'];

        $this->load->model("extension/pplcz/shipment");

        $this->model_extension_pplcz_shipment->removeShipment($shipment_id);

        $data = $this->renderOrder();
        $this->sendJson([
            'content' =>$data
        ]);
    }

    public function createShipment()
    {
        if (!$this->validateMethod('POST'))
        {
            return;
        }

        $this->load->model("extension/pplcz/shipment");
        $shipment_id = $this->model_extension_pplcz_shipment->createShipment($this->request->get['order_id']);

        $shipmentData = $this->model_extension_pplcz_shipment->load(ShipmentData::class, $shipment_id);

        $this->load->model("extension/pplcz/normalizer");
        $shipmentModel = $this->model_extension_pplcz_normalizer->denormalize($shipmentData, ShipmentModel::class);

        $data = $this->renderOrder();
        $this->sendJson([
            'content' =>$data,
            'shipment' =>  $this->model_extension_pplcz_normalizer->normalize($shipmentModel),
        ]);
    }

    public function addShipmentPackage()
    {
        if (!$this->validateMethod('PUT') || !$this->validateToken())
        {
            return;
        }

        $order_id = $this->request->get['order_id'];
        $shipment_id = null;

        if (isset($this->request->get['shipment_id']))
            $shipment_id = $this->request->get['shipment_id'];

        $this->load->model("extension/pplcz/shipment");
        $this->load->model("extension/pplcz/normalizer");

        if (!$shipment_id) {
            $shipment_id = $this->model_extension_pplcz_shipment->createShipment($order_id);
        }

        $this->model_extension_pplcz_shipment->addPackage($shipment_id);

        $data = $this->renderOrder();
        $this->sendJson([
            'content' =>$data
        ]);

    }

    public function addToBatch()
    {
        if (!$this->validateMethod('PUT'))
        {
            return;
        }

        $shipment_id = null;
        if (isset($this->request->get['shipment_id']))
            $shipment_id = $this->request->get['shipment_id'];

        $this->load->model("extension/pplcz/shipment");
        if (!$shipment_id)
        {
            $order_id = $this->request->get['order_id'];
            $shipment_id = $this->model_extension_pplcz_shipment->createShipment($order_id);
        }

        $this->load->model("extension/pplcz/batch");

        $batch_id = $this->request->get['batch_id'];

        if ($batch_id)
            $batches = [$this->model_extension_pplcz_batch->load(BatchData::class, $batch_id)];
        else
        {
            $batches = $this->model_extension_pplcz_batch->findFreeBatch();
            if (!$batches) {
                $batches = [new BatchData($this->registry)];
                $this->model_extension_pplcz_batch->save($batches[0]);
            }
        }

        $batches = $batches[0];
        $this->model_extension_pplcz_shipment->addShipmentToBatch($shipment_id, $batches->id);

        $this->sendJson([
            "content" => $this->renderOrder()
        ]);
    }

    public function renderOrderAjax()
    {
        $content = $this->renderOrder();
        $this->sendJson([
            'content' =>$content
        ]);
    }

    public function renderOrder()
    {
        if (!isset($this->request->get['order_id']))
            return;

        $order_id = $this->request->get['order_id'];


        $order = new OrderProxy($order_id);
        $order->id = $order_id;

        $this->load->model("extension/pplcz/normalizer");

        $this->load->model("extension/pplcz/shipment");

        $this->load->model("extension/pplcz/batch");

        $this->load->model("extension/pplcz/setting");

        $this->load->model("extension/pplcz/normalizer");


        $data = $this->model_extension_pplcz_shipment->findShipmentByOrderId($order->id);
        $freeLabels = $this->model_extension_pplcz_batch->findFreeBatch();
        $addNew = true;
        $allowEdit = [];

        $batch = [];

        $label = reset($freeLabels);
        $label_id = [];
        if ($label)
            $label_id = [ 'batch_id' =>  $label->id];


        foreach ($data as $key => $item)
        {
            if ($item->import_state === "None" || !$item->import_state) {
                $addNew = false;
            }

            $data[$key] =  $this->model_extension_pplcz_normalizer->denormalize($item, ShipmentWithAdditionalModel::class);
            $allowEdit[$key] = !$item->lock || $item->import_state === "None" || !$item->import_state;
            $batch[$key] = null;

            if ($allowEdit[$key]) {

                if ($item->batch_id)
                {
                    $batch[$key] = $this->createUrl("extension/pplcz/shipping/pplcz.removeFromBatch", [
                        'shipment_id' => $item->id,
                        'order_id' => $order_id,
                    ]);
                }
                else
                {
                    $batch[$key] = $this->createUrl("extension/pplcz/shipping/pplcz.addToBatch", [
                        'shipment_id' => $item->id,
                        'order_id' => $order_id,
                    ] + $label_id);
                }
            }
        }

        if ($addNew) {
            $data[] = $this->model_extension_pplcz_normalizer->denormalize($order, ShipmentWithAdditionalModel::class);
            $allowEdit[] = true;
            $batch[] =  $this->createUrl("extension/pplcz/shipping/pplcz.addToBatch", [
                'order_id' => $order_id,
            ] + $label_id);
        }

        $printState = $this->model_extension_pplcz_setting->getPrint();

        foreach ($data as $key => $item)
        {
            /**
             * @var ShipmentWithAdditionalModel $item
             */
            $shipment_id = $item->getShipment()->getId();

            $addPackage = $this->createUrl("extension/pplcz/shipping/pplcz.addShipmentPackage", [
                'order_id' => $order_id,
                'shipment_id' => $shipment_id,
            ]);

            $delete = '';
            if (!$shipment_id) {
                $create = $this->createUrl("extension/pplcz/shipping/pplcz.createShipment", [
                    'order_id' => $order_id,
                ]);
            } else {
                $create = '';
            }

            if ($shipment_id)
            {
                $delete =  $this->createUrl("extension/pplcz/shipping/pplcz.removeShipment", [
                    'order_id' => $order_id,
                    'shipment_id' => $shipment_id,
                ]);
            }

            $content = [
                'data' => $item,
                'error' => [],
                'allowEdit' => $allowEdit[$key],
                'batch' => $batch[$key],
                'batch_remove' => $batch[$key] ? (strpos($batch[$key], 'remove') > -1): false,
                'addPackage' => $addPackage,
                'create' => $create,
                'delete' => $delete,
                'refresh' => $this->createUrl("extension/pplcz/shipping/pplcz.renderOrderAjax", [
                    'order_id' => $order_id,
                ]),
                'batchPrint' => $item->getShipment()->getBatchId() ? $this->createUrl("extension/pplcz/shipping/pplcz", [], "/batch/{$item->getShipment()->getBatchId()}") : null,
                'id' => uniqid('pplcz'),
                'packages' => []
            ];

            $currentPrintState = $item->getShipment()->getPrintState() ?: $printState;

            $packages = $content['data']->getShipment()->getPackages() ?: $printState;

            foreach ($packages as $keyPos => $package)
            {
                /**
                 * @var PackageModel $package
                 */
                if ($package->getShipmentNumber())
                    $content['packages'][] = [
                        "shipmentNumber" => $package->getShipmentNumber(),
                        "phaseLabel" => $package->getPhaseLabel() ?: "Objednáno",
                        "cancelUrl" => $package->getCanBeCanceled() ? $this->createUrl("extension/pplcz/shipping/pplcz.cancelOrderPackage", [
                            'order_id' => $order_id,
                            'package_id' => $package->getId(),
                        ]): null,
                        'testUrl' => $this->createUrl("extension/pplcz/shipping/pplcz.testOrderPhase", [
                            'order_id' => $order_id,
                            'shipment_id' => $shipment_id,
                            'package_id' => $package->getId()
                        ]),
                        'printUrl' => $this->createUrl("extension/pplcz/shipping/pplcz.printOrderPackages", [
                            'order_id' => $order_id,
                            'shipment_id' => $content['data']->getShipment()->getId(),
                            'package_id' => $package->getId(),
                            'print'=> $currentPrintState
                        ]),
                        'printUrlAll' =>  !$keyPos && count($packages) > 1 ? $this->createUrl("extension/pplcz/shipping/pplcz.printOrderPackages", [
                            'order_id' => $order_id,
                            'shipment_id' => $content['data']->getShipment()->getId(),
                            'print'=> $currentPrintState
                        ]): null,
                    ];
            }

            $content['data'] = $this->model_extension_pplcz_normalizer->normalize($item);

            $data[$key] = $content;
        }

        return $this->load->view('extension/pplcz/shipping/order', [
            'shipments' => $data,
            'user_token' => $this->session->data['user_token'],
            'print' => [
                "optionals" => array_map(function ($item) {
                    return $this->model_extension_pplcz_normalizer->normalize($item);
                },$this->model_extension_pplcz_cploperation->getAvailableLabelPrinters()),
            ]
        ]);


    }

    public function removeFromBatch()
    {
        if (!$this->validateMethod('PUT'))
        {
            return;
        }

        $shipment_id = $this->request->get['shipment_id'];
        $this->load->model('extension/pplcz/shipment');
        $this->model_extension_pplcz_shipment->removeShipmentFromBatch($shipment_id);

        $this->sendJson([
            'content' => $this->renderOrder()
        ]);

    }

    public function cancelOrderPackage()
    {
        if (!$this->validateMethod('PUT'))
        {
            return;
        }

        $package_id = $this->request->get['package_id'];
        $this->load->model('extension/pplcz/cploperation');
        $this->model_extension_pplcz_cploperation->cancelPackage([$package_id]);

        $this->sendJson([
            'content' => $this->renderOrder()
        ]);
    }

    public function testOrderPhase()
    {
        if (!$this->validateMethod('PUT'))
        {
            return;
        }

        $package_id = $this->request->get['package_id'];
        $this->load->model('extension/pplcz/cploperation');
        $this->model_extension_pplcz_cploperation->testPackagesStates([$package_id]);

        $this->sendJson([
            'content' => $this->renderOrder()
        ]);
    }

    public function printOrderPackages()
    {
        if (!$this->validateMethod('GET'))
            return;

        $shipment_id = $this->request->get['shipment_id'];

        $package_id = null;

        if (isset($this->request->get['package_id'])) {
            $package_id = $this->request->get['package_id'];
        }

        $this->load->model("extension/pplcz/shipment");

        $shipment = $this->model_extension_pplcz_shipment->load(ShipmentData::class, $shipment_id);

        $this->load->model('extension/pplcz/cploperation');

        $print = $this->request->get['print'];


        $output = $this->model_extension_pplcz_cploperation->downloadLabel($shipment->batch_id, $shipment->id, $package_id, $print);

        $this->response->addHeader("Content-Type: {$output['content-type']}");
        $this->response->setOutput($output['body']);
        $this->response->output();
        exit;

    }

    public function injectOrderSettingAdminJS()
    {
        /**
         * @var Document $document
         * @var Request $request
         */
        $document = $this->document;
        $request = $this->request;

        if (!empty($this->request->get['route'])
            && ($this->request->get['route'] === 'sale/order.info')) {

            $server = HTTP_SERVER;

            $urls = parse_url($server);
            if (!isset($urls['path']) || !$urls['path'])
            {
                $path = [];
            }
            else
            {
                $path = explode('/', trim($urls['path'], '/'));
                array_pop($path);
            }

            $path[] = "extension/pplcz/src/Admin/MuiAdmin/build/static/js/bundle.js";
            $urls['path'] = join('/', $path);
            $server = $urls['scheme']  . '://'. $urls['host'] . '/' . $urls['path'];

            $this->load->model("setting/store");

            $stores = $this->model_setting_store->getStores();

            $httpcatalog = HTTP_CATALOG;

            $document->addScript(rtrim($httpcatalog, '/'). '/extension/pplcz/catalog/view/javascript/ppl-map.js');
            $document->addScript($server);
            $document->addStyle(rtrim($httpcatalog, '/'). '/extension/pplcz/catalog/view/stylesheet/ppl-map.css');

            return;
        }
    }

}