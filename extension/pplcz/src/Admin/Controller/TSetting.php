<?php

namespace PPLCZ\Admin\Controller;

use Opencart\Admin\Model\Setting\Setting;
use PPLCZ\Admin\Controller\BaseController;
use PPLCZ\Model\Model\MyApi2;
use PPLCZ\Model\Model\ParcelPlacesModel;
use PPLCZ\Model\Model\SenderAddressModel;
use PPLCZ\Model\Model\ShipmentMethodSettingModel;
use PPLCZ\Model\Model\ShipmentPhaseModel;
use PPLCZ\Model\Model\UpdateSyncPhasesModel;
use PPLCZ\Validator\WP_Error;

/**
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Setting $model_extension_pplcz_setting
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Normalizer $model_extension_pplcz_normalizer
 * @property-read  Setting $model_setting_setting
 * @mixin BaseController
 */
trait TSetting
{
    public function api()
    {
        if (!$this->validateToken()
            || !$this->validateMethod('GET'))
            return;

        $this->load->model("extension/pplcz/setting");
        $api = $this->model_extension_pplcz_setting->getApi();

        $this->sendJson($api);

    }

    public function apiEdit()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['POST', "PUT"]))
            return;

        $this->load->model("extension/pplcz/setting");
        $this->load->model("extension/pplcz/validator");
        $this->load->model("extension/pplcz/normalizer");

        $data = $this->getData();
        $data = $this->model_extension_pplcz_normalizer->denormalize($data, MyApi2::class);

        $errors = $this->model_extension_pplcz_validator->validate($data);

        if ($errors->errors)
        {
            $this->sendJson($errors);
            return;
        }

        $this->model_extension_pplcz_setting->setApi($data);

        $this->load->model("extension/pplcz/cploperation");

        $this->model_extension_pplcz_setting->resetAccessToken();

        if (!$this->model_extension_pplcz_cploperation->getAccessToken())
        {
            $error = new WP_Error();
            $error->add("", "PPL Plugin nebude fungovat, protože přihlašovací údaje nejsou správně nastaveny! Ujistěte se, že jsou zadány správně. Pokud je nemáte, prosím kontaktujte ithelp@ppl.cz");
            $this->sendJson($error, 400);
            return;
        }


        $this->sendJson(null, 204);
    }


    public function print()
    {
        if (!$this->validateToken()
            || !$this->validateMethod('GET'))
            return;
        $this->load->model("extension/pplcz/setting");
        $print = $this->model_extension_pplcz_setting->getPrint();
        $this->sendJson($print);
    }


    public function printEdit()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['POST', 'PUT', 'PATCH']))
            return;

        $data = $this->getData(true);

        if (isset($data['format'])) {
            $this->load->model("extension/pplcz/setting");
            $this->model_extension_pplcz_setting->setPrint($data['format']);
        }

        $this->sendJson(null, 204);
    }


    public function parcelplaces()
    {
        if (!$this->validateToken()
            || !$this->validateMethod("get"))
            return;

        $this->load->model("extension/pplcz/setting");
        $data = $this->model_extension_pplcz_setting->getParcelPlaces();

        $this->sendJson($data);
    }


    public function parcelplacesEdit()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(["post", "put", "patch"]))
            return;

        $data = $this->getData(true);

        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/validator");

        $parcelplaces = $this->model_extension_pplcz_normalizer->denormalize($data, ParcelPlacesModel::class);
        $errors = $this->model_extension_pplcz_validator->validate($parcelplaces);
        if ($errors->errors)
        {
            $this->sendJson($errors);
            return;
        }
        $this->load->model("extension/pplcz/setting");
        $this->model_extension_pplcz_setting->setParcelPaces($parcelplaces);
        $this->sendJson(null, 204);
    }


    public function senderAddresses()
    {
        if (!$this->validateToken()
            || !$this->validateMethod('GET'))
            return;

        $this->load->model("extension/pplcz/setting");


        $store_id = -1;
        if (isset($this->request->get[""]))
            $store_id = $this->request->get['store_id'];

        $addresses = $this->model_extension_pplcz_setting->getAddresses($store_id);

        $this->sendJson($addresses);
    }


    public function senderAddressesEdit()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['POST', 'PUT', "PATH"]))
            return;

        $this->load->model("extension/pplcz/setting");

        if (!isset($this->request->get["store_id"])) {
            $this->sendJson(null, 500);
            return;
        }

        $store_id = $this->request->get["store_id"];

        $data = $this->getData(true);
        if (!is_array($data))
        {
            $this->sendJson(null, 500);
            return;
        }

        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/validator");


        $wp_error = new WP_Error();

        foreach ($data as $key => $value)
        {
            $item = $this->model_extension_pplcz_normalizer->denormalize($value, SenderAddressModel::class);
            $this->model_extension_pplcz_validator->validate($item, $wp_error, "$key");
            $data[$key] = $item;
        }

        if ($wp_error->errors)
        {
            $this->sendJson($wp_error);
            return;
        }

        $this->model_extension_pplcz_setting->updateAddresses($store_id, $data);

        $this->sendJson(null, 204);

    }


    public function phases()
    {
        if (!$this->validateToken()
            || !$this->validateMethod('GET'))
            return;

        $this->load->model("extension/pplcz/setting");
        $phases = $this->model_extension_pplcz_setting->getSyncPhases();

        $this->sendJson($phases);
    }


    public function phaseEdit()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['POST','PUT', "PATH"]))
            return;

        $this->load->model("extension/pplcz/normalizer");

        $data = $this->getData(true);

        $this->load->model("extension/pplcz/setting");

        if (isset($data['maxSync']))
        {
            $this->model_extension_pplcz_setting->setSyncPhases((int)$data['maxSync']);
        }
        else if(isset($data['phases'])) {
            /**
             * @var ShipmentPhaseModel $phase
             */
            foreach ($data['phases'] as $phase) {
                $phase = $this->model_extension_pplcz_normalizer->denormalize($phase, ShipmentPhaseModel::class);
                $this->model_extension_pplcz_setting->setPhase($phase->getCode(), (int)$phase->getWatch());
            }
        }

        $this->sendJson(null, 204);

    }

    public function phasesEdit()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['POST','PUT', "PATH"]))
            return;

        $this->load->model("extension/pplcz/normalizer");

        $data = $this->getData(true);

        $phases = $this->model_extension_pplcz_normalizer->denormalize($data, UpdateSyncPhasesModel::class);

        if (!$this->validateDataSendIfError($phases))
            return;

        $this->load->model("extension/pplcz/setting");

        $this->model_extension_pplcz_setting->setPhases($phases);
    }

    public function shipmentSettings()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['GET']))
            return;

        $this->load->model("extension/pplcz/setting");

        $shipments = $this->model_extension_pplcz_setting->getShipments($this->request->get["store_id"]);

        $this->sendJson($shipments);
    }

    public function shipmentSettingEdit()
    {
        if (!$this->validateToken()
        || !$this->validateMethod(['PUT']))
        return;

        $this->load->model("extension/pplcz/setting");
        $this->load->model("extension/pplcz/validator");
        $this->load->model("extension/pplcz/normalizer");

        $data = $this->getData(true);
        $data = $this->model_extension_pplcz_normalizer->denormalize($data, ShipmentMethodSettingModel::class);

        $error = $this->model_extension_pplcz_validator->validate($data);
        if ($error->errors)
        {
            $this->sendJson($error);
            return;
        }

        $this->model_extension_pplcz_setting->updateShipment($data, $this->request->get['store_id']);
        $this->sendJson(null, 204);

    }
    public function shipmentSettingDelete()
    {
        if (!$this->validateToken()
            || !$this->validateMethod(['DELETE']))
            return;

        $this->load->model("extension/pplcz/setting");

        $this->model_extension_pplcz_setting->deleteShipment($this->request->get["guid"], $this->request->get['store_id']);

        $this->sendJson(null, 204);
    }


}