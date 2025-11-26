<?php

namespace PPLCZ\Repository;

use Opencart\Admin\Model\Setting\Setting;
use Opencart\System\Engine\Model;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\BatchData;
use PPLCZ\Data\CollectionData;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\LabelPrintModel;
use PPLCZ\Serializer;
use PPLCZCPL\Api\AccessPointApi;
use PPLCZCPL\Api\CodelistApi;
use PPLCZCPL\Api\CustomerApi;
use PPLCZCPL\Api\OrderBatchApi;
use PPLCZCPL\Api\OrderEventApi;
use PPLCZCPL\Api\ShipmentApi;
use PPLCZCPL\Api\ShipmentBatchApi;
use PPLCZCPL\Api\ShipmentEventApi;
use PPLCZCPL\ApiException;
use PPLCZCPL\Model\EpsApiInfrastructureWebApiModelProblemJsonModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsEnumOrderType;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsOrderBatchCreateOrderBatchModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsOrderBatchOrderModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsOrderBatchOrderModelSender;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsOrderEventCancelOrderEventModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentResultChildItemModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentResultItemModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentShipmentModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentTrackAndTraceItemModel;
use PPLCZVendor\GuzzleHttp\HandlerStack;
use PPLCZVendor\Psr\Http\Message\RequestInterface;
use PPLCZCPL\Configuration;


/**
 * @property-read \PPLCZ\Repository\Setting $model_extension_pplcz_setting
 * @property-read Config $model_extension_pplcz_config
 * @property-read Shipment $model_extension_pplcz_shipment
 * @property-read Batch $model_extension_pplcz_batch
 * @property-read Package $model_extension_pplcz_package
 * @property-read Normalizer $model_extension_pplcz_normalizer
 * @property-read Address $model_extension_pplcz_address
 * @property-read Collection $model_extension_pplcz_collection
 */
class CPLOperation extends Model
{

    public const BASE_URL = "https://api.dhl.com/ecs/ppl/myapi2";
    public const ACCESS_TOKEN_URL = "https://api.dhl.com/ecs/ppl/myapi2/login/getAccessToken";
    public const PROD_VERSION = true;
    public const INTEGRATOR = "4542104";

    /**
     * @return LabelPrintModel[]
     */
    public function getAvailableLabelPrinters()
    {
        $available = [
            [
                "title" => "1x etiketa na stránku, tisk do PDF souboru",
                "code" => "1/PDF"
            ],
            [
                "title" => "A4 4x (začíná od 1. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4/PDF"
            ],
            [
                "title" => "A4 4x  (začíná od 2. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4.2/PDF"
            ],
            [
                "title" => "A4 4x  (začíná od 3. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4.3/PDF"
            ],
            [
                "title" => "A4 4x  (začíná od 4. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4.4/PDF"
            ]
        ];

        $this->load->model('extension/pplcz/normalizer');

        return array_map(function ($item) {
            return $this->model_extension_pplcz_normalizer->denormalize($item, LabelPrintModel::class);
        }, $available);
    }

    public function getFormat($format)
    {
        switch ($format) {
            case '1/PDF':
            case '4/PDF':
            case '4.2/PDF':
            case '4.3/PDF':
            case '4.4/PDF':
                return $format;
        }
        return "4/PDF";
    }

    public function reset()
    {
        $this->load->model('extension/pplcz/setting');
        $this->model_extension_pplcz_setting->resetAccessToken();
    }

    public function getAccessToken()
    {
        $this->load->model('extension/pplcz/setting');
        $content = $this->model_extension_pplcz_setting->getAccessToken();

        if ($content) {

            list($a, $b, $c) = explode(".", $content);
            if ($b) {
                $b = json_decode(base64_decode($b), true);
                if ($b["exp"] > time() - 40) {
                    return $content;
                }
            }

        }

        $api = $this->model_extension_pplcz_setting->getApi();


        $client_id = $api->getClientId();
        $client_secret = $api->getClientSecret();

        if (!$client_id || strlen($client_id) < 5 || !$client_secret || strlen($client_secret) < 10) {
            return null;
        }

        $auth = "Basic " . base64_encode("$client_id:$client_secret");

        $headers = ["Content-Type: application/x-www-form-urlencoded"];
        if (strpos(self::ACCESS_TOKEN_URL, "getAccessToken") === false) {
            $headers[] = "Authorization: $auth";
        }

        $content = ["grant_type" => "client_credentials"];
        if (strpos(self::ACCESS_TOKEN_URL, "getAccessToken") !== false) {
            $content["client_id"] = $client_id;
            $content["client_secret"] = $client_secret;
        }

        $opts = array('http' =>
            array(
                'ignore_errors' => true,
                'timeout' => 5,
                'method' => 'POST',
                'header' => join("\r\n", $headers),
                'content' => http_build_query($content),
            ));

        $context = stream_context_create($opts);
        $url = self::ACCESS_TOKEN_URL;
        $content = @file_get_contents("{$url}", false, $context);

        if (strpos($http_response_header[0], "200 OK")) {
            if ($content) {
                $tokens = json_decode($content, true);
                $this->model_extension_pplcz_setting->setAccessToken($tokens['access_token']);
                $this->model_extension_pplcz_setting->setAccessTokenError(null);
                return $tokens["access_token"];
            }
        } else {
            $errorMaker = "Url: {$url}\n";
            $errorMaker .= join("\n", $http_response_header);
            if ($content)
                $errorMaker .= "\n" . $content;
            else
                $errorMaker .= "\nno content";
            $this->model_extension_pplcz_setting->setAccessTokenError($errorMaker);

        }
        return null;
    }

    public function createClientAndConfiguration()
    {
        $handler = HandlerStack::create();
        $handler->push(function ($handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if ($request->getMethod() === "GET" || $request->getMethod() === "OPTIONS" || $request->getMethod() === "HEAD") {
                    $request = $request->withoutHeader("Content-Type");
                } else if ($request->getMethod() === "POST" || $request->getMethod() === "PUT" || $request->getMethod() === "PATCH") {
                    if (!$request->hasHeader("Content-Length")) {
                        $request = $request->withAddedHeader("Content-Length", $request->getBody()->getSize());
                        if (!$request->getBody()->getSize())
                            $request = $request->withoutHeader("Content-Type");
                    }
                }
                return $handler($request, $options);
            };
        });


        $client = new \PPLCZVendor\GuzzleHttp\Client([
            "handler" => $handler
        ]);

        $configuration = new Configuration();
        $configuration->setAccessToken($this->getAccessToken());
        $url = self::BASE_URL;
        $configuration->setHost($url);

        return [$client, $configuration];
    }


    public function getCountries()
    {

        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistCountryGet(300, 0);

        $output = [];

        foreach ($limitApi as $key => $val) {
            $output[$val->getCode()] = $val->getCashOnDelivery();
        }

        return $output;

    }

    public function getCollectionAddress()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CustomerApi($client, $configuration);
        $addresses = $codelistApi->customerAddressGet();

        foreach ($addresses as $address) {
            if ($address->getCode() === 'PICK')
                return $address;
        }

        return null;
    }

    public function cancelCollection($colletion_id)
    {
        $this->load->model("extension/pplcz/collection");
        $collection = $this->model_extension_pplcz_collection->load(CollectionData::class, $colletion_id);
        list($client, $configuration) = $this->createClientAndConfiguration();
        $order = new OrderEventApi($client, $configuration);
        $ev = new EpsApiMyApi2WebModelsOrderEventCancelOrderEventModel();
        $ev->setNote("Zrušeno na vyžádání");
        try {
            $order->orderCancelPost(null, $collection->get_reference_id(), null, null, null, $ev);
            $collection->state = "Canceled";
            $this->model_extension_pplcz_collection->save($collection);
        }
        catch (\Exception $ex)
        {
            throw $ex;
        }

    }

    public function createCollection($colletion_id)
    {
        $this->load->model("extension/pplcz/collection");

        $collection = $this->model_extension_pplcz_collection->load(CollectionData::class, $colletion_id);


        list($client, $configuration) = $this->createClientAndConfiguration();

        $order = new OrderBatchApi($client, $configuration);
        $modelBatch = new EpsApiMyApi2WebModelsOrderBatchCreateOrderBatchModel();

        $model = new EpsApiMyApi2WebModelsOrderBatchOrderModel();
        $model->setOrderType(EpsApiMyApi2WebModelsEnumOrderType::COLLECTION_ORDER);
        $model->setSendDate(new \DateTime($collection->get_send_date()));
        $model->setProductType("BUSS");
        $model->setReferenceId($collection->get_reference_id());

        $sender = new EpsApiMyApi2WebModelsOrderBatchOrderModelSender();
        $sender->setEmail($collection->get_email());
        $sender->setPhone($collection->get_telephone());
        $sender->setContact($collection->get_contact());

        $address = $this->getCollectionAddress();

        $sender->setCity($address->getCity());
        $sender->setZipCode($address->getZipCode());
        $sender->setCountry($address->getCountry());
        $sender->setStreet($address->getStreet());
        $sender->setName($address->getName());

        $model->setSender($sender);

        $model->setShipmentCount($collection->get_estimated_shipment_count());
        $model->setNote($collection->get_note());
        $model->setEmail($collection->get_email());
        $modelBatch->setOrders([$model]);

        $output = $order->createOrdersWithHttpInfo($modelBatch);

        $location = reset($output[2]["Location"]);
        $location = explode("/", $location);
        $batch_id = end($location);



        $collection->remote_collection_id = $batch_id;
        $collection->state = "Created";
        $collection->send_to_api_date = gmdate("Y-m-d");
        $this->model_extension_pplcz_collection->save($collection);

    }

    public function getLimits()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();
        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistServicePriceLimitGet(300, 0);
        $insrs = [];
        $cods = [];

        foreach ($limitApi as $item) {
            if ($item->getService() === "INSR") {
                $insrs[] = [

                    "product" => $item->getProduct(),
                    "min" => $item->getMinPrice(),
                    "max" => $item->getMaxPrice(),
                    "currency" => $item->getCurrency(),
                    "country" => $item->getCountry(),
                ];
            } else if ($item->getService() === "COD") {
                $cods[] = [
                    "product" => $item->getProduct(),
                    "min" => $item->getMinPrice(),
                    "max" => $item->getMaxPrice(),
                    "currency" => $item->getCurrency(),
                    "country" => $item->getCountry(),
                ];
            }
        }
        return [
            'COD' => $cods,
            "INSURANCE" => $insrs
        ];
    }

    public function getCodCurrencies()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $customerApi = new CustomerApi($client, $configuration);
        $content = $customerApi->customerGet();
        $currencies = [];
        foreach ($content->getAccounts() as $item) {
            $currencies[] = [
                'country' => $item->getCountry(),
                'currency' => $item->getCurrency(),
            ];
        }
        return $currencies;
    }

    public function getShipmentPhases()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CodelistApi($client, $configuration);
        $phases = $codelistApi->codelistShipmentPhaseGet(300, 0);

        $output = [];

        foreach ($phases as $key => $val) {
            $output[$val->getCode()] = $val->getName();
        }

        return $output;
    }

    public function getStatuses()
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CodelistApi($client, $configuration);
        $statuses = $codelistApi->codelistStatusGet(300, 0);

        $output = [];

        foreach ($statuses as $key => $val) {
            $output[$val->getCode()] = $val->getName();
        }

        return $output;
    }


    public function findParcel($code)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return null;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $accessPointApi = new AccessPointApi($client, $configuration);
        $founded = $accessPointApi->accessPointGet(100, 0, $code);
        if (is_array($founded) && isset($founded[0])) {
            return $founded[0];
        }
        return null;
    }

    public function refreshLabels($batch_id)
    {
        list($client, $configuration) = $this->createClientAndConfiguration();

        $this->load->model("extension/pplcz/batch");
        $this->load->model("extension/pplcz/shipment");
        $this->load->model("extension/pplcz/package");

        $batch = $this->model_extension_pplcz_batch->load(BatchData::class, $batch_id);

        if (!$batch->remote_batch_id)
        {
            throw new \Exception("batch id has not batch id");
        }

        $shipmentBatchApi = new ShipmentBatchApi($client, $configuration);

        $batchData = $shipmentBatchApi->getShipmentBatchWithHttpInfo($batch->remote_batch_id);

        $shipments = $this->model_extension_pplcz_shipment->findShipmentByBatchId($batch->id);

        $batchData = $batchData[0];

        foreach ($batchData->getItems() as $batchItem)
        {
            if (!$batchItem->getShipmentNumber())
                return false;
        }

        foreach ($batchData->getItems() as $batchItem) {
            $referenceId = $batchItem->getReferenceId();
            $referenceShipments = array_filter($shipments, function ($item) use ($referenceId) {
                return $item->get_reference_id() == $referenceId;
            });

            $baseShipmentNumber = $batchItem->getShipmentNumber();
            $errorCode = $batchItem->getErrorCode();
            $errorMessage = $batchItem->getErrorMessage();


            foreach ($referenceShipments as $shipment) {
                $packages = $shipment->get_package_ids();
                foreach ($packages as $key => $package)
                {
                    $packages[$key] = $this->model_extension_pplcz_package->load(PackageData::class, $package);
                }

                $package = array_filter($packages, function ($item) use ($baseShipmentNumber) {
                    return $item->get_shipment_number() && $item->get_shipment_number() === $baseShipmentNumber;
                });

                if (!$package) {
                    $package = array_filter($packages, function ($item) use ($baseShipmentNumber) {
                        return !$item->get_shipment_number();
                    });
                }
                if ($package) {
                    $package = reset($package);
                    $package->disableLock();
                    $package->set_wc_order_id($shipment->get_wc_order_id());
                    if ($batchItem->getLabelUrl()) {
                        $label_id = explode("/", $batchItem->getLabelUrl());
                        $label_id = end($label_id);
                        $package->set_label_id($label_id);
                    }
                    $package->set_shipment_number($baseShipmentNumber);
                    $package->set_import_error($errorMessage);
                    $package->set_import_error_code($errorCode);

                    $this->model_extension_pplcz_package->save($package);
                }

                $packages = array_filter($packages, function ($item) use($baseShipmentNumber) {
                    return $item->get_shipment_number() !== $baseShipmentNumber;
                });

                foreach ($batchItem->getRelatedItems() as $relatedItem) {
                    $shipmentNumber = $relatedItem->getShipmentNumber();

                    $package = array_filter($packages, function ($item) use ($shipmentNumber) {
                        return $item->get_shipment_number() && $item->get_shipment_number() === $shipmentNumber;
                    });

                    if (!$package) {
                        $package = array_filter($packages, function ($item) use ($shipmentNumber) {
                            return !$item->get_shipment_number();
                        });
                    }

                    if ($package) {
                        $package = reset($package);
                        $package->disableLock();

                        if ($relatedItem->getLabelUrl()) {
                            $label_id = explode("/", $relatedItem->getLabelUrl());
                            $label_id = end($label_id);
                            $package->set_label_id($label_id);
                        }
                        $package->set_shipment_number($relatedItem->getShipmentNumber());
                        $package->set_import_error($relatedItem->getErrorMessage());
                        $package->set_import_error_code($relatedItem->getErrorCode());
                        $this->model_extension_pplcz_package->save($package);
                    }
                }
                $shipment->set_import_state($batchItem->getImportState());
                $shipment->disableLock();
                $this->model_extension_pplcz_shipment->save($shipment);

            }
        }
        return true;
    }

    public function createPackages($batch_id)
    {
        $this->load->model("extension/pplcz/shipment");
        $this->load->model("extension/pplcz/address");
        $this->load->model("extension/pplcz/batch");
        $this->load->model("extension/pplcz/setting");
        $this->load->model("extension/pplcz/package");

        /**
         * @var BatchData $batch
         * @var ShipmentData[] $shipments
         */
        $batch = $this->model_extension_pplcz_batch->load(BatchData::class, $batch_id);
        $shipments = $this->model_extension_pplcz_shipment->findShipmentByBatchId($batch_id);

        try {
            $this->db->query('start transaction');

            $batch->lock = true;
            $batch->disableLock();
            $this->model_extension_pplcz_batch->save($batch);
            $pad = 1;
            $count = (int)count($shipments) ;


            while($count > 10) {
                $count /= 10;
                $pad++;
            }

            $position = 1;

            foreach ($shipments as $key => $value) {

                $shipments[$key]->lock = true;
                $shipments[$key]->import_state = 'InProgress';
                $shipments[$key]->reference_id = str_pad($position, $pad, '0', STR_PAD_LEFT ) . '#' . $value->wc_order_id;
                $position++;
                $shipments[$key]->disableLock();
                $this->model_extension_pplcz_shipment->save($shipments[$key]);

                foreach ($shipments[$key]->get_package_ids() as $packageId) {
                    /**
                     * @var PackageData $package
                     */
                    $package = $this->model_extension_pplcz_package->load(PackageData::class, $packageId);
                    $package->lock = true;
                    $package->disableLock();
                    $package->pplcz_shipment_id = $value->id;
                    $package->wc_order_id = $value->wc_order_id;
                    $this->model_extension_pplcz_package->save($package);
                };


                $recipient = $this->model_extension_pplcz_address->load(AddressData::class, $shipments[$key]->recipient_address_id);
                $recipient->lock = true;
                $recipient->disableLock();
                $this->model_extension_pplcz_address->save($recipient);

                $sender = $this->model_extension_pplcz_address->load(AddressData::class, $shipments[$key]->sender_address_id);
                $sender->lock = true;
                $sender->disableLock();
                $this->model_extension_pplcz_address->save($sender);


            }
            $send = $this->model_extension_pplcz_normalizer->denormalize($shipments, EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel::class);

        }
        catch (\Exception $exception)
        {
            $this->db->query('rollback transaction');
            throw $exception;
        }

        try {
            list($client, $configuration) = $this->createClientAndConfiguration();
            $shipmentBatchApi = new ShipmentBatchApi($client, $configuration);

            $output = $shipmentBatchApi->createShipmentsWithHttpInfo($send, "cs-CZ");
            $location = reset($output[2]["Location"]);
            $location = explode("/", $location);
            $batch_id = end($location);

            $batch->remote_batch_id = $batch_id;
            $batch->lock = true;
            $batch->disableLock();
            $this->model_extension_pplcz_batch->save($batch);

            foreach ($shipments as $shipment) {
                $shipment->import_state = ("InProgress");
                $shipment->remote_batch_id = $batch_id;
                $shipment->lock = true;
                $shipment->import_errors = null;
                $shipment->disableLock();
                $this->model_extension_pplcz_shipment->save($shipment);
            }

            $this->db->query('commit');
            return true;
        }
        catch (\Throwable $ex) {
            $batch->lock = false;
            $batch->disableLock();
            $this->model_extension_pplcz_batch->save($batch);

            foreach ($shipments as $position => $shipment) {
                if ($shipment->lock) {
                    $shipment->lock = false;
                    $shipment->disableLock();
                    $this->model_extension_pplcz_shipment->save($shipment);
                    $address = $shipment->get_recipient_address_id();
                    $address = $this->model_extension_pplcz_address->load(AddressData::class, $address);
                    $address->lock = false;
                    $address->disableLock();
                    $this->model_extension_pplcz_address->save($address);
                }

                if ($ex instanceof  ApiException && $ex->getResponseObject() instanceof  EpsApiInfrastructureWebApiModelProblemJsonModel) {
                    /**
                     * @var array<string,string[]> $error
                     */
                    $errors = [];
                    $responseErrors = $ex->getResponseObject()->getErrors();
                    if ($responseErrors === null)
                        $responseErrors = [];

                    foreach ($responseErrors as $errorKey =>$error )
                    {
                        $arguments = [];
                        if (preg_match('/^shipments\[([0-9]+)]($|\.)/i', $errorKey, $arguments )){
                            if ("{$arguments[1]}" === "$position") {
                                foreach ($error as $err) {
                                    $errors[] = "{$err}";
                                }
                            }

                        }
                    }
                    if (!$responseErrors)
                        $errors[] = $ex->getMessage();
                    if ($errors) {
                        $errors = join("\n", $errors);
                        $shipment->import_errors = $errors;
                        $shipment->import_state = ("None");
                        $shipment->disableLock();
                        $shipment->lock = false;
                        $this->model_extension_pplcz_shipment->save($shipment);
                    }
                } else {
                    $shipment->import_errors = ($ex->getMessage());
                    $shipment->import_state = ("None");
                    $shipment->disableLock();
                    $shipment->lock = false;
                    $this->model_extension_pplcz_shipment->save($shipment);
                }
            }
            $this->db->query('commit');
            return false;
        }

    }

    public function downloadLabel($batch_id, $shipment_id, $package_id, $print_format = null)
    {
        list($client, $configuration) = $this->createClientAndConfiguration();

        $shipmentApi = new ShipmentBatchApi($client, $configuration);

        $this->load->model("extension/pplcz/setting");

        $format = ($print_format ?: $this->model_extension_pplcz_setting->getPrint());
        $format = $this->getFormat($format);

        $this->model_extension_pplcz_setting->setPrint($format);

        $this->load->model("extension/pplcz/batch");

        $batch = $this->model_extension_pplcz_batch->load(BatchData::class, $batch_id);

        switch($format) {
            case '1/PDF':
                $position = 1;
                $format = 'default';
                break;
            case "4.2/PDF":
                $position = 2;
                $format = 'A4';
                break;
            case "4.3/PDF":
                $position = 3;
                $format = 'A4';
                break;
            case "4.4/PDF":
                $position = 4;
                $format = 'A4';
                break;
            default:
                $position = 1;
                $format = 'A4';
                break;
        }

        $context = stream_context_create([
            "http" => [
                "ignore_errors" => true,
                "header" => "Authorization: Bearer " . $this->getAccessToken()
            ]
        ]);


        if (!$shipment_id) {
            $response = file_get_contents(self::BASE_URL . '/shipment/batch/' . $batch->remote_batch_id . '/label?' . http_build_query([
                    "limit" => 100,
                    "offset" => 0,
                    "pageSize" => $format,
                    "position" => $position,
                ], "", "&", PHP_QUERY_RFC3986), false, $context);

            $statusLine = $http_response_header[0];

            if (preg_match('{HTTP/\S+ (\d{3}) (.*)}', $statusLine, $matches)) {
                $statusCode = (int)$matches[1];
                $statusMessage = $matches[2];

                if ($statusCode !== 200) {
                    throw new \Exception("Error $statusCode, $statusMessage");
                }
            }

            $contentType = null;
            foreach ($http_response_header as $item)
            {
                if (preg_match('~content-type:[\s]*(.+)~i', $item, $matches))
                {
                    $contentType = $matches[1];
                    break;
                }
            }

            return [
                'content-type' => $contentType,
                'body' => $response,
            ];

        }
        else {
            $this->load->model("extension/pplcz/shipment");
            /**
             * @var ShipmentData $shipment
             */
            $shipment = $this->model_extension_pplcz_shipment->load(ShipmentData::class, $shipment_id);

            $reference_id = $shipment->reference_id;
            if ($package_id)
            {
                $this->load->model("extension/pplcz/package");
                $package_id = $this->model_extension_pplcz_package->load(PackageData::class, $package_id)->shipment_number;
            }


            // načtu si info kolem batch
            $data = $shipmentApi->getShipmentBatch($batch->remote_batch_id);
            $items = $data->getItems();
            usort($items, function (EpsApiMyApi2WebModelsShipmentBatchShipmentResultItemModel $first, EpsApiMyApi2WebModelsShipmentBatchShipmentResultItemModel $second) {
                return strcmp($first->getReferenceId(), $second->getReferenceId());
            });

            $offset = 0;
            $founded = false;

            foreach ($items as $item) {
                $isReference = $item->getReferenceId() === $reference_id;
                if ($isReference && $package_id && $item->getShipmentNumber() === $package_id) {
                    $founded = $item;
                    break;
                }

                if (!$package_id && $isReference) {
                    $founded = $item;
                    break;
                }

                $offset++;
                $items2 = $item->getRelatedItems() ?? [];

                usort($items2, function (EpsApiMyApi2WebModelsShipmentBatchShipmentResultChildItemModel $a, EpsApiMyApi2WebModelsShipmentBatchShipmentResultChildItemModel $b) {
                    return strcmp($a->getShipmentNumber(), $b->getShipmentNumber());
                });

                foreach ($items2 as $item2) {
                    if ($isReference && $item2->getShipmentNumber() === $package_id) {
                        $founded = $item;
                        break 2;
                    }
                    $offset++;
                }

                if ($isReference)
                    throw new \Exception("Problem s nalezením zásilky k tisku");
            }

            if (!$founded)
                throw new \Exception("Problem s nalezením zásilky k tisku");

            $items = $founded->getRelatedItems() ?? [];
            $max = $package_id ? 1 : (count($items) + 1);

            $response = file_get_contents(self::BASE_URL . '/shipment/batch/' . $batch->remote_batch_id . '/label?' . http_build_query([
                    "limit" => $max,
                    "offset" => $offset,
                    "pageSize" => $format,
                    "position" => $position,
                    "orderBy" => "ReferenceId,ShipmentNumber"
                ], "", "&", PHP_QUERY_RFC3986), false, $context);

            $statusLine = $http_response_header[0];

            if (preg_match('{HTTP/\S+ (\d{3}) (.*)}', $statusLine, $matches)) {
                $statusCode = (int)$matches[1];
                $statusMessage = $matches[2];

                if ($statusCode !== 200) {
                    throw new \Exception("Error $statusCode, $statusMessage");
                }
            }

            $contentType = null;
            foreach ($http_response_header as $item)
            {
                if (preg_match('~content-type:[\s]*(.+)~i', $item, $matches))
                {
                    $contentType = $matches[1];
                    break;
                }
            }

            return [
                'content-type' => $contentType,
                'body' => $response,
            ];
        }
    }

    public function testPackagesStates($packages)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return [];


        if (!$packages) {
            return [];
        }

        $this->load->model("extension/pplcz/config");
        $this->load->model("extension/pplcz/package");

        $statuses = $this->model_extension_pplcz_config->getStatuses();

        list($client, $configuration) = $this->createClientAndConfiguration();

        $accessPointApi = new ShipmentApi($client, $configuration);

        $min = count($packages);

        $content = $accessPointApi->shipmentGetWithHttpInfo($min, 0, array_map(function ($item) {
            return $this->model_extension_pplcz_package->load(PackageData::class, $item)->shipment_number;
        }, $packages));

        $data = $content[0];

        $returnData = [];

        /**
         * @var EpsApiMyApi2WebModelsShipmentShipmentModel[] $data
         */
        foreach ($data as $item) {
            $trackAndTrace = $item->getTrackAndTrace();
            $shipmentNumber = $item->getShipmentNumber();
            $url = $trackAndTrace->getPartnerUrl();
            $events = $trackAndTrace->getEvents();


            /**
             * @var EpsApiMyApi2WebModelsShipmentTrackAndTraceItemModel $lastEvent
             */

            $lastEvent = end($events);
            $codPayed = array_filter($events, function ($item) {
                return $item->getPhase() === "CodPaidDate";
            });
            if ($lastEvent) {
                $returnData[$shipmentNumber] = [
                    'phase' => $lastEvent->getPhase() === null ? "Canceled" : $lastEvent->getPhase(),
                    'name' => $lastEvent->getName(),
                    "code" => $lastEvent->getCode(),
                    "status"=> $lastEvent->getStatusId(),
                    'url' => $url,
                    'payed' => $codPayed
                ];
            }
        }

        $db = array_map(function ($item) {
            return $this->model_extension_pplcz_package->load(PackageData::class, $item);
        }, $packages);


        foreach ($returnData as $shipmentNumber => $data) {

            foreach (array_filter($db,function (PackageData $package) use ($shipmentNumber) {
                return "{$package->get_shipment_number()}" === "$shipmentNumber";
            }) as $key => $package) {
                unset($db[$key]);

                $phase = $data['phase'];
                if ($phase === null)
                    $phase = 'Canceled';
                $status = $data['status'];

                /**
                 * @var PackageData $package
                 */
                if ($package->get_phase() !== $phase
                    || (''.$package->get_status()) !== (''.$status) ) {

                    $package->set_status($status);
                    $package->set_status_label(@$statuses[$data['status']]);
                    $package->set_phase($phase);
                    $package->set_phase_label($data['name']);
                    $package->set_last_update_phase(gmdate("Y-m-d H:i:s"));
                    $package->set_last_test_phase(gmdate("Y-m-d H:i:s"));
                    $package->set_import_error(null);
                    $package->set_import_error_code(null);
                    $package->disableLock();
                    $this->model_extension_pplcz_package->save($package);
                    /*
                    if ($data["payed"]) {
                        $shipmentId = $package->get_ppl_shipment_id();
                        $shipment = new ShipmentData($shipmentId);
                        $order = $shipment->get_wc_order_id();
                        if ($order) {
                            $order = new \WC_Order($order);
                            $hasCodStatus = $order->get_meta("_" . pplcz_create_name("_cod_change_status"));
                            if (!$hasCodStatus) {
                                $order->set_meta_data(["_" . pplcz_create_name("_cod_change_status") => true]);
                                $order->set_status("Completed");
                                $order->save();
                            }
                        }
                    }*/
                } else {
                    $package->disableLock();
                    $package->set_import_error(null);
                    $package->set_import_error_code(null);
                    if (!$package->get_last_update_phase())
                        $package->set_last_update_phase(gmdate("Y-m-d H:i:s"));
                    $package->set_last_test_phase(gmdate("Y-m-d H:i:s"));
                    $this->model_extension_pplcz_package->save($package);
                }
            }
        }

        foreach ($db as $package)
        {
            $package->disableLock();
            $package->set_import_error("NotFound");
            $package->set_import_error_code("NotFound");
            if (!$package->get_last_update_phase())
                $package->set_last_update_phase(gmdate("Y-m-d H:i:s"));
            $package->set_last_test_phase(gmdate("Y-m-d H:i:s"));
            $this->model_extension_pplcz_package->save($package);
        }
    }

    public function cancelPackage($packages)
    {
        $this->load->model("extension/pplcz/package");
        foreach ($packages as $shipment) {
            $package = $this->model_extension_pplcz_package->load(PackageData::class, $shipment);
            list($client, $configuration) = $this->createClientAndConfiguration();
            $shipmentApi = new ShipmentEventApi($client, $configuration);
            $shipmentNumber = $package->get_shipment_number();
            $shipmentApi->shipmentShipmentNumberCancelPost($shipmentNumber);
            $package->set_phase("Canceled");
            $package->set_phase_label("Canceled");
            $package->disableLock();
            $this->model_extension_pplcz_package->save($package);
        }
    }
}