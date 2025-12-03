<?php

namespace PPLCZ\ModelNormalizer;

use Opencart\Admin\Model\Localisation\Country;
use Opencart\Admin\Model\Sale\Order;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\OrderCartData;
use PPLCZ\Data\OrderProxy;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ParcelData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\PackageModel;
use PPLCZ\Model\Model\ParcelAddressModel;
use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Model\RecipientAddressModel;
use PPLCZ\Model\Model\SenderAddressModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentSenderModel;
use PPLCZ\Repository\Address;
use PPLCZ\Repository\Config;
use PPLCZ\Repository\Normalizer;
use PPLCZ\Repository\OrderCart;
use PPLCZ\Repository\Package;
use PPLCZ\Repository\Parcel;
use PPLCZ\Repository\Setting;
use PPLCZ\Repository\Shipment;
use PPLCZ\TLoader;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ShipmentDataDenormalizer implements DenormalizerInterface
{
    use TLoader;

    public function __construct($registry)
    {
        $this->setRegistry($registry);
    }

    private function getServiceCodeFromOrder($order)
    {
        /**
         * @var Setting $setting
         * @var OrderCart $ordercart
         * @var Config $config
         * @var Normalizer $normalizer
         */
        $setting = $this->loadModel("extension/pplcz/setting");
        $config = $this->loadModel("extension/pplcz/config");
        $shipmentSetting = $setting->getShipments($order['store_id']);
        list($master, $guid) = explode(".", $order['shipping_method']['code']);

        if ($master !== "pplcz")
            return  [null, null, false, null];

        foreach ($shipmentSetting as $key => $settingModel)
        {

            if ($guid === $settingModel->getGuid())
            {
                $payment = explode('.', $order['payment_method']['code']);
                $isCod = $payment[0] === $settingModel->getCodPayment();

                $methods = [
                    "SMAR", "PRIV", "SMEU", "CONN"
                ];

                if ($isCod)
                {
                    $methods = [
                        "SMAD", "PRID", "SMED", "COND"
                    ];
                }

                if ($order['shipping_iso_code_2'] === 'CZ')
                {
                    unset($methods[2], $methods[3]);
                } else {
                    unset($methods[0], $methods[1]);
                }

                $methods = array_values($methods);

                if ($settingModel->getParcelBoxes())
                {
                    unset($methods[1]);
                }
                else {
                    unset($methods[0]);
                }

                foreach ($config->getAllServices() as $service) {
                    if ($service->getCode() === reset($methods))
                    return [
                        reset($methods),
                        $service->getTitle(),
                        $isCod,
                        $service->getParcelRequired()
                    ];
                }
            }
        }

        return [null, null, false, null];
    }

    private function ShipmentDataToModel(ShipmentData $data, array $context = [])
    {
        $shipmentModel = new ShipmentModel();
        $shipmentModel->setId($data->id);
        $shipmentModel->setImportState($data->import_state);

        /**
         * @var Normalizer $normalizer
         */
        $normalizer = $this->loadModel("extension/pplcz/normalizer");


//        $shipmentModel->setPrintState(pplcz_get_shipment_print($data->get_id()) ?: pplcz_get_batch_print($data->get_batch_id()));

        if ($data->wc_order_id)
            $shipmentModel->setOrderId($data->wc_order_id);

        if ($data->note)
            $shipmentModel->setNote($data->note);

        if ($data->has_parcel)
            $shipmentModel->setHasParcel($data->has_parcel);

        if ($data->reference_id)
            $shipmentModel->setReferenceId($data->reference_id);

        if ($data->service_code)
            $shipmentModel->setServiceCode($data->service_code);

        if ($data->service_name)
            $shipmentModel->setServiceName($data->service_name);

        if ($data->remote_batch_id)
            $shipmentModel->setRemoteBatchId($data->remote_batch_id);

        if ($data->batch_order)
            $shipmentModel->setBatchOrder($data->batch_order);

        if ($data->batch_id)
            $shipmentModel->setBatchId($data->batch_id);

        if($data->cod_value)
            $shipmentModel->setCodValue($data->cod_value);

        if ($data->cod_value_currency)
            $shipmentModel->setCodValueCurrency($data->cod_value_currency);

        if ($data->cod_variable_number) {
            $cod = $data->cod_variable_number;
            $shipmentModel->setCodVariableNumber($cod);
        }

        /**
         * @var Address $addressLoader
         * @var Parcel $parcelLoader
         * @var Shipment $shipmentLoader
         */
        $addressLoader = $this->loadModel("extension/pplcz/address");
        $parcelLoader = $this->loadModel("extension/pplcz/parcel");
        $shipmentLoader = $this->loadModel("extension/pplcz/shipment");

        $id = $data->sender_address_id ?? $data->get_sender_address_id("default");

        if ($id)
        {
            $sender = $addressLoader->load(AddressData::class, $id);
            if ($sender && $sender->id)
                $shipmentModel->setSender($normalizer->denormalize($sender, SenderAddressModel::class));
        }

        $id = $data->recipient_address_id;
        if ($id) {
            $recipient = $addressLoader->load(AddressData::class, $id);
            if ($recipient && $recipient->id)
                $shipmentModel->setRecipient($normalizer->denormalize($recipient, RecipientAddressModel::class));
        }


        if ($data->parcel_id)
        {
            $parcel = $parcelLoader->load(ParcelData::class, $data->parcel_id);

            if ($parcel && $parcel->id) {
                $shipmentModel->setParcel($normalizer->denormalize($parcel, ParcelAddressModel::class));
            }
        }

        if ($data->note)
            $shipmentModel->setNote($data->note);
        if ($data->age)
            $shipmentModel->setAge($data->age);
        if ($data->lock)
            $shipmentModel->setLock($data->lock);

        if ($data->import_errors)
            $shipmentModel->setImportErrors(array_filter(explode("\n", $data->import_errors), "trim"));
        else
            $shipmentModel->setImportErrors([]);
        /**
         * @var Package $dataLoader
         */
        $packageLoader = $this->loadModel("extension/pplcz/package");
        $packages =array_map(function ($item) use ($packageLoader, $normalizer) {
            $content = $packageLoader->load(PackageData::class, $item);
            return $normalizer->denormalize($content, PackageModel::class);
        }, $data->package_ids);

        if (!$packages) {
            $orderId = $shipmentModel->getOrderId();

            $model = new PackageModel();
            $model->setReferenceId($orderId);
            $model->setPhase("None");
            $package = $normalizer->denormalize($model, PackageData::class);
            $package->set_phase("None");
            $packageLoader->save($package);
            $data->set_package_ids([$package->get_id()]);
            $shipmentLoader->save($data);
            $packages[] = $model;

        }
        $shipmentModel->setPackages($packages);
        return $shipmentModel;

    }


    private function OrderToModel(OrderProxy $orderProxy, array $context = [])
    {
        /**
         * @var Country $countryLoader
         * @var Order $orderLoader
         * @var Setting $settingLoader
         * @var Normalizer $normalizerLoader
         * @var Parcel $parcelLoader
         * @var Shipment $shipmentLoader
         */
        $orderLoader = $this->loadModel("sale/order");
        $countryLoader = $this->loadModel("localisation/country");
        $settingLoader = $this->loadModel("extension/pplcz/setting");
        $normalizerLoader = $this->loadModel("extension/pplcz/normalizer");
        $parcelLoader = $this->loadModel("extension/pplcz/parcel");
        $shipmentLoader = $this->loadModel("extension/pplcz/shipment");

        $shipmentModel = new ShipmentModel();

        $order = $orderLoader->getOrder($orderProxy->id);

        $shipmentModel->setImportState("None");
        $shipmentModel->setOrderId($orderProxy->id);
        if ($order['comment'])
            $shipmentModel->setNote($order['comment']);

        // $dm = gmdate("ymd");


        $reference = $order['order_id'];

        $shipmentModel->setReferenceId($reference);

        $shipmentModel->setImportErrors([]);

        list($code, $title, $isCod, $parcel) = $this->getServiceCodeFromOrder($order);

        if ($code)
            $shipmentModel->setServiceCode($code);
        if ($title)
            $shipmentModel->setServiceName($title);

        if ($isCod) {
            $shipmentModel->setCodVariableNumber($order['order_id']);
            $currency = $this->registry->get('currency');
            $base = $this->registry->get('config')->get('config_currency');

            $total = $currency->convert($order['total'], $base, $order['currency_code']);

           // $total = $order['total'];
            $shipmentModel->setCodValue($total);
            $shipmentModel->setCodValueCurrency($order['currency_code']);
            $shipmentModel->setCodVariableNumber($order['order_id']);
        }

        $addresses = $settingLoader->getAddresses($order['store_id']);

        if ($addresses)
        {
            $shipmentModel->setSender($addresses[0]);
        }

        $shipmentModel->setRecipient($normalizerLoader->denormalize($orderProxy, RecipientAddressModel::class));

        if ($parcel) {

            /**
             * @var OrderCartData $ordercartdata
             */
            $ordercartdata = $this->loadModel("extension/pplcz/order_cart")->getDataByOrderId($orderProxy->id);
            if ($ordercartdata && $ordercartdata->parcel_setting ) {
                $parcel = $normalizerLoader->denormalize($ordercartdata->parcel_setting, ParcelDataModel::class);
                if ($parcel) {
                    /**
                     * @var ParcelDataModel $parcel
                     */
                    $code = $parcel->getCode();
                    $founded = $parcelLoader->getAccessPointByCode($code);
                    if (!$founded) {
                        $founded = new ParcelData($this->registry);
                        $founded->country = $parcel->getCountry();
                        $founded->code = $parcel->getCode();
                        $founded->zip = $parcel->getZipCode();
                        $founded->city = $parcel->getCity();
                        $founded->type = $parcel->getAccessPointType();
                        $founded->street = $parcel->getStreet();
                        $founded->name = $parcel->getName();
                        $founded->lat = $parcel->getGps()->getLatitude();
                        $founded->lng = $parcel->getGps()->getLongitude();
                        $founded->valid = true;
                        $parcelLoader->save($founded);
                    }
                    $founded = $normalizerLoader->denormalize($founded, ParcelAddressModel::class);
                    $shipmentModel->setParcel($founded);
                }
            }

            $shipmentModel->setHasParcel(true);
        }

        if (isset($order['shipping_method']['age15Required'])
            && $order['shipping_method']['age15Required']) {
            $shipmentModel->setAge('15+');
        }
        else if (isset($order['shipping_method']['age18Required'])
            && $order['shipping_method']['age18Required']) {
            $shipmentModel->setAge('18+');
        }

        $packageModel = new PackageModel();
        $packageModel->setReferenceId("{$order['order_id']}");

        $shipmentModel->setPackages([
            $packageModel
        ]);

        return $shipmentModel;
    }

    public function ShipmentModelToShipmentData(ShipmentModel $model, $context = [])
    {
        $shipmentData = $context["data"] ??  new ShipmentData($this->registry);
        if ($shipmentData->get_lock())
        {
            $oldData = $shipmentData;
            $shipmentData = new ShipmentData($this->registry);
            $shipmentData->import_state = "None";
            $shipmentData->wc_order_id = $oldData->get_wc_order_id();
        } else if (!$shipmentData->get_id())
        {
            $shipmentData->set_import_state("None");
            if ($model->isInitialized("orderId"))
                $shipmentData->set_wc_order_id($model->getOrderId());
        }

        $shipmentData->set_reference_id($model->getReferenceId());
        if ($model->isInitialized("orderId"))
            $shipmentData->set_wc_order_id($model->getOrderId());

        if ($model->isInitialized("note"))
        {
            $shipmentData->set_note($model->getNote());
        } else {
            $shipmentData->set_note(null);
        }
        if ($model->isInitialized("sender"))
            $shipmentData->set_sender_address_id($model->getSender()->getId());

        /**
         * @var Config $config
         */
        $config = $this->loadModel("extension/pplcz/config");

        if ($model->isInitialized("serviceCode")) {
            $shipmentData->set_service_code($model->getServiceCode());
            $serviceCode = $model->getServiceCode();

            $services = $config->getAllServices();

            foreach($services as $service)
            {
                if ($service->getCode() == $serviceCode)
                    break;
            }

            $shipmentData->set_service_name($service->getTitle());

            if ($service->getCodAvailable()) {
                if ($model->isInitialized("codVariableNumber"))
                    $shipmentData->set_cod_variable_number($model->getCodVariableNumber());
                if ($model->isInitialized("codValue"))
                    $shipmentData->set_cod_value($model->getCodValue());
                if ($model->isInitialized("codValueCurrency"))
                    $shipmentData->set_cod_value_currency($model->getCodValueCurrency());
            } else {
                $shipmentData->set_cod_variable_number(null);
                $shipmentData->set_cod_value(null);
                $shipmentData->set_cod_bank_account_id(null);
                $shipmentData->set_cod_value_currency(null);
            }

            if ($service->getParcelRequired()) {
                if ($model->isInitialized("hasParcel") && $model->getHasParcel()) {
                    $shipmentData->set_has_parcel(true);
                    if ($model->isInitialized("parcel"))
                    {
                        $parcel = $model->getParcel();
                        $shipmentData->set_parcel_id($parcel->getId());
                    }

                } else {
                    $shipmentData->set_has_parcel(false);
                }
            } else {
                $shipmentData->set_has_parcel(false);
            }
        }

        /**
         * @var Normalizer $normalizer
         * @var Package $packageLoader
         * @var Address $addressLoader
         */
        $normalizer = $this->loadModel("extension/pplcz/normalizer");
        $packageLoader = $this->loadModel("extension/pplcz/package");
        $addressLoader = $this->loadModel("extension/pplcz/address");

        if ($model->isInitialized("packages"))
        {
            $modelPackages = $model->getPackages();
            foreach ($modelPackages as $key => $package) {
                if ($package->getId()) {
                    $packageData = $packageLoader->load(PackageData::class, $package->getId());
                } else {
                    $packageData = null;
                }
                $modelPackages[$key] = $normalizer->denormalize($package, PackageData::class, array("data" => $packageData));
                if (!$modelPackages[$key]->get_phase())
                    $modelPackages[$key]->set_phase("None");
                $packageLoader->save($modelPackages[$key]);


                $modelPackages[$key] = $modelPackages[$key]->get_id();
            }
            $shipmentData->set_package_ids($modelPackages);
        }

        if ($model->isInitialized("recipient")) {
            $recipient = null;
            if ($shipmentData->recipient_address_id ) {
               $recipient = $addressLoader->load(AddressData::class, $shipmentData->recipient_address_id);
            }

            $recipient = $normalizer->denormalize($model->getRecipient(), AddressData::class, ['data' => $recipient]);
            $addressLoader->save($recipient);
            $shipmentData->set_recipient_address_id($recipient->get_id());
        }

        return $shipmentData;

    }

    public function UpdateShipmentToData(UpdateShipmentModel $model, $context = [])
    {
        $shipmentData = $context["data"] ??  new ShipmentData($this->registry);
        if ($shipmentData->get_lock())
        {
            $oldData = $shipmentData;
            $shipmentData = new ShipmentData($this->registry);
            $shipmentData->import_state = ("None");
            $shipmentData->wc_order_id = $oldData->get_wc_order_id();
        } else if (!$shipmentData->get_id())
        {
            $shipmentData->import_state = ("None");
            if ($model->isInitialized("orderId"))
                $shipmentData->wc_order_id = ($model->getOrderId());
        }

        if ($model->getReferenceId())
            $shipmentData->reference_id = ($model->getReferenceId());
        if ($model->isInitialized("orderId"))
            $shipmentData->wc_order_id = ($model->getOrderId());

        if ($model->isInitialized("age")) {
            $shipmentData->age = ($model->getAge());
        } else {
            $shipmentData->age = (null);
        }

        if ($model->isInitialized("note"))
        {
            $shipmentData->note = ($model->getNote());
        } else {
            $shipmentData->note = (null);
        }

        if ($model->isInitialized("senderId"))
        {
            $shipmentData->sender_address_id = ($model->getSenderId());
        }

        if ($model->isInitialized("serviceCode"))
        {
            /**
             * @var Config $config
             */
            $config = $this->loadModel("extension/pplcz/config");


            $shipmentData->set_service_code($model->getServiceCode());
            $serviceCode = $model->getServiceCode();

            $services = $config->getAllServices();

            foreach($services as $service)
            {
                if ($service->getCode() == $serviceCode)
                    break;
            }



            $shipmentData->set_service_name($service->getTitle());

            if ($service->getCodAvailable()) {
                if ($model->isInitialized("codVariableNumber"))
                    $shipmentData->set_cod_variable_number($model->getCodVariableNumber());
                if ($model->isInitialized("codValue"))
                    $shipmentData->set_cod_value($model->getCodValue());
                if ($model->isInitialized("codValueCurrency"))
                    $shipmentData->set_cod_value_currency($model->getCodValueCurrency());
            }
            else {
                $shipmentData->set_cod_variable_number(null);
                $shipmentData->set_cod_value(null);
                $shipmentData->set_cod_bank_account_id(null);
                $shipmentData->set_cod_value_currency(null);
            }

            if ($service->getParcelRequired()) {
                if ($model->isInitialized("hasParcel") && $model->getHasParcel())
                {
                    $shipmentData->set_has_parcel(true);
                    if ($model->isInitialized("parcelId")) {
                        $parceldata = new ParcelData($this->registry, $model->getParcelId());
                        if ($parceldata->id)
                        {
                            $shipmentData->set_parcel_id($parceldata->id);
                        }

                    }
                }
                else
                {
                    $shipmentData->has_parcel = false;
                }
            }
            else {
                $shipmentData->has_parcel = (false);
                $shipmentData->parcel_id = null;
            }
        }

        if ($model->isInitialized("packages"))
        {
            /**
             * @var Normalizer $normalizer
             */
            $normalizer = $this->loadModel("extension/pplcz/normalizer");
            $packageLoader = $this->loadModel("extension/pplcz/package");
            $modelPackages = $model->getPackages();

            foreach ($modelPackages as $key => $package) {
                if ($package->isInitialized("id") && $package->getId()) {
                    $packageData = $packageLoader->load(PackageData::class, $package->getId());
                } else {
                    $packageData = null;
                }
                $modelPackages[$key] = $normalizer->denormalize($package, PackageData::class, array("data" => $packageData));
                if (!$modelPackages[$key]->phase)
                    $modelPackages[$key]->phase = "None";

                $packageLoader->save($modelPackages[$key]);

                $modelPackages[$key] = $modelPackages[$key]->get_id();
            }
            $shipmentData->set_package_ids($modelPackages);
        }

        if (!$shipmentData->get_id()) {
            if ($shipmentData->get_wc_order_id())
            {
                /**
                 * @var Normalizer $normalizer
                 */
                $normalizer = $this->loadModel("extension/pplcz/normalizer");

                $order = new OrderProxy();
                $order->id = $shipmentData->wc_order_id;
                /**
                 * @var Normalizer $normalizer
                 * @var ShipmentModel $shipmentModel
                 *
                 */
                $shipmentModel = $normalizer->denormalize($order, ShipmentModel::class);
                if ($shipmentModel->isInitialized('recipient'))
                {
                    $recipient = $shipmentModel->getRecipient();
                    $recipient = $normalizer->denormalize($recipient, AddressData::class);
                    $addressLoader = $this->loadModel("extension/pplcz/address");
                    $addressLoader->save($recipient);
                    $shipmentData->recipient_address_id = $recipient->get_id();
                }
            }
        }

        return $shipmentData;
    }

    public function UpdateShipmentSenderToData(UpdateShipmentSenderModel $sender, $context)
    {
        /**
         * @var ShipmentData $shipment
         */
        if (!isset($context['data']))
            throw new \Exception("Undefined shipment");

        $shipment = $context["data"];
        if ($sender->isInitialized("senderId")) {
            /**
             * @var Address $addressLoader
             */
            $addressLoader = $this->loadModel("extension/pplcz/address");
            $addresses = $addressLoader->getDefaultSenderAddresses(0);
            $address = reset($addresses);

            if ($address && $address->id && $address->id == $sender->getSenderId())
                $shipment->sender_address_id = null;
            else
                $shipment->sender_address_id = ($sender->getSenderId());
        }
        return $shipment;
    }

    public function UpdateRecipientToData(RecipientAddressModel $recipientAddressModel, $context)
    {
        if (!isset($context["data"]))
            throw new \Exception("Undefined shipment");;
        $shipment = $context["data"];

        /**
         * @var Address $addressLoader
         * @var Normalizer $normalizer
         */
        $addressLoader = $this->loadModel("extension/pplcz/address");
        $normalizer = $this->loadModel("extension/pplcz/normalizer");
        /**
         * @var ShipmentData $shipment
         */
        $id = $shipment->recipient_address_id;
        $founded = $addressLoader->load(AddressData::class, $id) ?: new AddressData($this->registry);
        $founded->type = "recipient";
        $address = $normalizer->denormalize($recipientAddressModel, AddressData::class, ["data" =>$founded]);
        $addressLoader->save($address);
        $shipment->recipient_address_id = $address->get_id();
        return $shipment;
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof ShipmentData && $type == ShipmentModel::class) {
            return $this->ShipmentDataToModel($data, $context);
        }
        else if ($data instanceof OrderProxy && $type == ShipmentModel::class) {
            return $this->OrderToModel($data, $context);
        }
        else if ($data instanceof ShipmentModel && $type == ShipmentData::class) {
            return $this->ShipmentModelToShipmentData($data, $context);
        }
        else if ($data instanceof UpdateShipmentModel && $type === ShipmentData::class)
        {
            return $this->UpdateShipmentToData($data, $context);
        }
        else if($data instanceof UpdateShipmentSenderModel && $type === ShipmentData::class)
        {
            return $this->UpdateShipmentSenderToData($data, $context);
        }
        else if ($data instanceof RecipientAddressModel && $type === ShipmentData::class)
        {
            return $this->UpdateRecipientToData($data, $context);
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        $stopka = 1;
        if($data instanceof ShipmentData && $type === ShipmentModel::class)
            return true;
        if ($data instanceof OrderProxy && $type === ShipmentModel::class)
            return true;
        if ($data instanceof UpdateShipmentModel && $type === ShipmentData::class)
            return true;
        if ($data instanceof UpdateShipmentSenderModel && $type === ShipmentData::class)
            return true;
        if ($data instanceof RecipientAddressModel && $type === ShipmentData::class)
            return true;
        if ($data instanceof ShipmentModel && $type == ShipmentData::class) {
            return true;
        }

        return false;

    }
}