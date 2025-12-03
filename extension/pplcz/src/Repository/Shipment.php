<?php
namespace PPLCZ\Repository;

use Opencart\System\Library\DB;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\OrderProxy;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\PackageModel;
use PPLCZ\Model\Model\ShipmentModel;

/**
 * @property-read Package $model_extension_pplcz_package
 * @property-read Address $model_extension_pplcz_address
 */
class Shipment extends Data
{
    public function removeShipment($shipment_id)
    {
        $this->db->query("start transaction");
        $confirm = true;
        try {
            /**
             * @var ShipmentData $shipment
             * @var PackageData $package
             * @var AddressData $recipient
             */
            $shipment = $this->load(ShipmentData::class, $shipment_id);
            if (!$shipment && $shipment->lock)
                return;
            $ids = $shipment->get_package_ids();

            $packageLoader = $this->loadModel("extension/pplcz/package");

            foreach ($ids as $id) {
                $package = $packageLoader->load(PackageData::class, $id);

                if ($package->lock)
                {
                    $confirm =false;
                    return;
                }
                $packageLoader->remove($package);
            }

            $addressLoader = $this->loadModel("extension/pplcz/address");

            $address = $addressLoader->load(AddressData::class, $shipment->recipient_address_id);
            if ($address) {
                if ($address->lock)
                {
                    $confirm = false;
                    return;
                }
                $addressLoader->remove($address);
            }

            $this->remove($shipment);
        }
        catch (\Exception $e) {
            $confirm = false;
            $this->db->query("rollback");
        }
        finally {
            if ($confirm)
                $this->db->query("commit");
        }

    }

    /**
     * @param $orderId
     * @return ShipmentData[]
     */
    public function findShipmentByOrderId($orderId)
    {
        $prefix = DB_PREFIX;
        $rows = $this->db->query("SELECT * FROM {$prefix}pplcz_shipment WHERE wc_order_id = " . (int)$orderId);
        $output= [];

        foreach ($rows->rows as $row) {
            $output[] = ShipmentData::fromArray($row, $this->registry);
        }

        return $output;
    }

    public function findNextReferenceId($orderId)
    {
        $shipments = $this->findShipmentByOrderId($orderId);

        if (!count($shipments)) {
            return "$orderId";
        }

        $i = count($shipments);
        return $i;
    }

    public function findShipmentByBatchId($batchId)
    {
        $prefix = DB_PREFIX;
        $rows = $this->db->query("select * from {$prefix}pplcz_shipment where batch_id = '" . (int)($batchId). "' order by batch_order");

        $sort = array_map(function($item) {
            return ShipmentData::fromArray($item, $this->registry);
        }, $rows->rows);
        return $sort;

    }

    public function createShipment($order_id)
    {
        $shipmentLoader = $this->loadModel("extension/pplcz/shipment");
        $shipmentNormalizer = $this->loadModel("extension/pplcz/normalizer");

        $shipmentData = null;

        /**
         * @var ShipmentModel $shipment
         */
        $order = new OrderProxy();
        $order->id = $order_id;
        $shipment = $shipmentNormalizer->denormalize($order, ShipmentModel::class);

        $db = $this->db;
        $db->query("start transaction");
        try {
            $shipmentData = $shipmentNormalizer->denormalize($shipment, ShipmentData::class);
            $shipmentLoader->save($shipmentData);
            $db->query("commit");
            return $shipmentData->id;
        }
        catch (\Exception $e) {
            $db->query("rollback");
            throw $e;
        }
    }

    public function addPackage($shipment_id)
    {
        $shipmentData = $this->model_extension_pplcz_shipment->load(ShipmentData::class, $shipment_id);
        $shipment = $this->model_extension_pplcz_normalizer->denormalize($shipmentData, ShipmentModel::class);

        $packages = $shipment->getPackages();
        $packages[] = new PackageModel();
        $shipment->setPackages($packages);

        /**
         * @var DB $db
         */
        $db = $this->db;
        $db->query("start transaction");
        try {
            $shipmentData = $this->model_extension_pplcz_normalizer->denormalize($shipment, ShipmentData::class, ['data'=>$shipmentData]);
            $this->model_extension_pplcz_shipment->save($shipmentData);
            $db->query("commit");
        }
        catch (\Exception $e) {
            $db->query("rollback");
        }
    }

    public function addShipmentToBatch($shipment_id, $batch_id = null)
    {
        $shipmentData = $this->load(ShipmentData::class, $shipment_id);
        if ($shipmentData) {
            $shipmentData->batch_id = $batch_id;
            $prefix = DB_PREFIX;
            $result = $this->db->query("select max(batch_order) as maximum from {$prefix}pplcz_shipment where batch_id = '" . (int)($batch_id) . "'");
            $shipmentData->batch_order = ($result->row && $result->row['maximum'] ? $result->row['maximum'] : 0) + 100;
            $this->save($shipmentData);
        }
    }

    public function removeShipmentFromBatch($shipment_id)
    {
        /**
         * @var ShipmentData $shipmentData
         */
        $shipmentData = $this->load(ShipmentData::class, $shipment_id);
        if ($shipmentData) {
            $shipmentData->batch_id = null;
            $shipmentData->batch_order = null;
            $shipmentData->remote_batch_id = null;
            $this->save($shipmentData);
        }
    }

    public function updateShipmentPosition($shipment_id, $position)
    {
        foreach ($this->select("pplcz_shipment", ['pplcz_shipment_id' => $shipment_id]) as $item)
        {
            $item = ShipmentData::fromArray($item, $this->registry);
            $item->batch_order = $position;
            $this->save($item);
            return;
        }
    }
}