<?php
namespace PPLCZ\Repository;

use Opencart\System\Engine\Model;
use PPLCZ\Data\AddressData;
use PPLCZ\Model\Model\CategoryModel;
use PPLCZ\Model\Model\MyApi2;
use PPLCZ\Model\Model\ParcelPlacesModel;
use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\SenderAddressModel;
use PPLCZ\Model\Model\ShipmentMethodSettingModel;
use PPLCZ\Model\Model\ShipmentPhaseModel;
use PPLCZ\Model\Model\SyncPhasesModel;
use PPLCZ\Model\Model\UpdateSyncPhasesModel;


/**
 * @property-read CPLOperation $model_extension_pplcz_cploperation
 * @property-read Config $model_extension_pplcz_config
 * @property-read \Opencart\Admin\Model\Setting\Setting $model_setting_setting
 * @property-read Normalizer $model_extension_pplcz_normalizer
 * @property-read Address $model_extension_pplcz_address
 */
class Setting extends Model
{
    use TRepositorySetting;
    /**
     * @param MyApi2 $data
     * @return void@
     */
    public function setApi($data)
    {
        $this->setSettings(["client_id" => $data->getClientId(), 'client_secret'=>$data->getClientSecret()]);
    }

    public function getApi()
    {
        $clientId = $this->getSetting("client_id");
        $client_secret = $this->getSetting("client_secret");

        $data = new MyApi2();
        $data->setClientId($clientId ?: "");
        $data->setClientSecret($client_secret?: "");
        return $data;
    }
    public function getAccessToken()
    {
        return $this->getSetting("access_token");
    }

    public function setAccessToken($access_token)
    {
        $this->setSetting("access_token", $access_token);
    }

    public function resetAccessToken()
    {
        $this->setSetting("access_token", null);
    }

    public function setAccessTokenError($errors)
    {
        $this->setSetting('access_token_error', $errors);
    }

    public function getAccessTokenError()
    {
        return $this->getSetting('access_token_error');
    }


    public function getPrint()
    {

        $content = $this->getSetting("print");

        $this->load->model("extension/pplcz/cploperation");

        $printers = $this->model_extension_pplcz_cploperation->getAvailableLabelPrinters();
        foreach ($printers as $print)
        {
            if ($print->getCode() == $content)
            {
                return $content;
                break;
            }
        }

        return $this->model_extension_pplcz_cploperation->getFormat($content);

    }

    public function setPrint($format)
    {
        $this->load->model("extension/pplcz/cploperation");

        $printers = $this->model_extension_pplcz_cploperation->getAvailableLabelPrinters();

        foreach ($printers as $print)
        {
            if ($print->getCode() == $format)
            {
                $this->setSetting("print", $format);
                break;
            }
        }
    }

    public function getPhases()
    {
        $this->load->model("extension/pplcz/config");

        $phases = $this->model_extension_pplcz_config->getShipmentPhases();

        return array_map(function ($item, $key) {
            $value = $this->getSetting('phase_' . $key);

            if ($value !== null)
            {
                $value = (int)$value;
            }
            else {
                $value = true;
            }
            $phase = new ShipmentPhaseModel();
            $phase->setCode($key);
            $phase->setTitle($item);
            $phase->setWatch(!!$value);

            return $phase;

        }, $phases, array_keys($phases));
    }

    public function setPhase($code, $watch)
    {
        if ($watch) {
            $this->setSetting('phase_' . $code, "1");
        }
        else {
            $this->setSetting('phase_' . $code, "0");
        }
    }

    public function getSyncPhases() {
        $phases = $this->getPhases();
        $value = $this->getSetting('phase_maxsync' );

        if( $value !== null)
            $value = intval($value) ?: 200;

        $syncPhases = new SyncPhasesModel();
        $syncPhases->setMaxSync($value);
        $syncPhases->setPhases($phases);
        return $syncPhases;
    }

    public function setSyncPhases($maxsync)
    {

        $value = intval($maxsync);

        if (!$value)
            $this->setSetting('phase_maxsync', "0");
        else
            $this->setSetting('phase_maxsync', "$value");
    }

    /**
     * @param UpdateSyncPhasesModel $phases
     * @return void
     */
    public function setPhases($phases)
    {
        $this->setSyncPhases($phases->getMaxSync());
        foreach($phases->getPhases() as $value)
        {
            $this->setPhase($value->getCode(), $value->getWatch());
        }
    }

    public function getAddresses($store_id)
    {

        $this->load->model("extension/pplcz/address");
        $this->load->model("extension/pplcz/normalizer");

        $ids = $this->getSetting('default_address', 0);

        if ($ids)
            $ids = @json_decode($ids);
        else
            $ids = [];

        $output = [];

        foreach ($ids as $id) {
            $address = $this->model_extension_pplcz_address->getAddress($id);
            if ($address) {
                $address = $this->model_extension_pplcz_normalizer->denormalize($address, SenderAddressModel::class);
                $output[] = $address;
            }
        }
        return $output;
    }

    /**
     * @param int $store_id
     * @param SenderAddressModel[] $addressesModel
     */
    public function updateAddresses($store_id, $addressesModel)
    {
        $this->load->model("extension/pplcz/address");
        $this->load->model("extension/pplcz/normalizer");

        $ids = [];
        foreach ($addressesModel as $value)
        {
            if ($value->getId())
            {
                $oldAddressModel = $this->model_extension_pplcz_address->getAddress($value->getId());
                $newAddressModel = $this->model_extension_pplcz_normalizer->denormalize($value, AddressData::class, ["data"=> $oldAddressModel]);
                $this->model_extension_pplcz_address->updateAddress($newAddressModel);
                $ids[] = $newAddressModel->id;
            }

        }

        $this->setSetting('default_address', json_encode($ids), $store_id);
    }


    public function getParcelPlaces()
    {

        $this->load->model("extension/pplcz/normalizer");

        $parcel = new ParcelPlacesModel();
        $parcelPlaces = $this->getSetting("parcel_places");

        if ($parcelPlaces)
            $parcelPlaces = @json_decode($parcelPlaces, true);

        if ($parcelPlaces) {
            $this->load->model("extension/pplcz/normalizer");
            $parcelPlaces = $this->model_extension_pplcz_normalizer->denormalize($parcelPlaces, ParcelPlacesModel::class);
            $parcelPlaces->setDisabledParcelShop($parcelPlaces->getDisabledParcelShop() ?: false);
            return $parcelPlaces;
        }

        $parcelPlaces = new ParcelPlacesModel();
        $parcelPlaces->setDisabledParcelShop(false);
        return $parcelPlaces;
    }

    /**
     * @param ParcelPlacesModel $parcelplaces
     * @return void
     */
    public function setParcelPaces($parcelplaces)
    {

        $this->load->model("extension/pplcz/normalizer");

        $parcelplaces = $this->model_extension_pplcz_normalizer->normalize($parcelplaces);
        $parcelplaces = json_encode($parcelplaces);
        $this->setSetting("parcel_places", $parcelplaces);
    }

    /**
     * @param $store_id
     * @return ShipmentMethodSettingModel[]
     */
    public function getShipments($store_id)
    {


        $data = $this->getSettingStartWith("shipment_settings", $store_id);
        $output = [];
        if (is_array($data))
        {
            $this->load->model("extension/pplcz/normalizer");
            foreach ($data as $key => $value)
            {
                $value = @json_decode($value, true);
                if ($value) {

                    $value = $this->model_extension_pplcz_normalizer->denormalize($value, ShipmentMethodSettingModel::class);
                    $guid = str_replace("shipment_settings_", "", $key);
                    $value->setGuid($guid);
                    $output[] = $value;
                }
            }
        }

        return $output;
    }

    /**
     * @param $guid
     * @param $store_id
     * @return void
     */
    public function deleteShipment($guid, $store_id)
    {
        $data = $this->getSettingStartWith("shipment_settings", $store_id);
        if (is_array($data)) {
            foreach ($data as $key => $value)
            {
                if ($key === "shipment_settings_{$guid}") {
                    $data[$key] = null;
                }
            }
        }

        if (array_filter($data))
        {
            $data['status'] = '1';
        }
        else
        {
            $data['status'] = '0';
        }
        $this->setSettings($data, $store_id);

        $this->model_setting_setting->editSetting("total_pplcz_cod_fee", ["total_pplcz_cod_fee_status" => '1', 'total_pplcz_cod_fee_sort_order' => 2]);

    }

    /**
     * @param ShipmentMethodSettingModel $shipment
     * @return void
     */
    public function updateShipment($shipment,$store_id)
    {
        $data = $this->getSettingStartWith("shipment_settings", $store_id);

        if (is_array($data)) {
            $this->load->model("extension/pplcz/normalizer");
            foreach ($data as $key => $value)
            {
                $value = @json_decode($value, true);
                if ($value) {
                    $data[$key] = $this->model_extension_pplcz_normalizer->denormalize($value, ShipmentMethodSettingModel::class);
                    $guid = str_replace("shipment_settings_", "", $key);
                    $data[$key]->setGuid($guid);
                }
                else {
                    $data[$key] = null;
                }
            }
        }

        $guid = $shipment->getGuid();

        $data["shipment_settings_$guid"] = $shipment;
        foreach ($data as $key => $value)
        {
            if ($value !== null) {
                $value = $this->model_extension_pplcz_normalizer->normalize($value);
                $data[$key] = json_encode($value);
            }
        }

        if (array_filter($data))
        {
            $data['status'] = '1';
        }
        else
        {
            $data['status'] = '0';
        }


        $this->setSettings($data, $store_id);
        $this->model_setting_setting->editSetting("total_pplcz_cod_fee", ["total_pplcz_cod_fee_status" => '1', 'total_pplcz_cod_fee_sort_order' => 2]);

    }

    /**
     * @param int $product_id
     * @return ProductModel
     */
    public function getProduct($product_id)
    {
        $data = $this->getSetting("product_{$product_id}");
        if ($data
            && $data = @json_decode($data, true))
        {
            $this->load->model("extension/pplcz/normalizer");
            $data = $this->model_extension_pplcz_normalizer->denormalize($data, ProductModel::class);
        }
        else {
            $data = new ProductModel();
        }

        if (!$data->getPplConfirmAge15())
            $data->setPplConfirmAge15(false);

        return $data;
    }

    /**
     * @param $product_id
     * @param ProductModel $product
     * @return void
     */
    public function setProduct($product_id, $product)
    {
        if ($product) {
            $this->load->model("extension/pplcz/normalizer");
            $data = $this->model_extension_pplcz_normalizer->normalize($product);
            $data = json_encode($data);
            $this->setSetting("product_{$product_id}", $data);
        }
    }

    /**
     * @param int $category_id
     * @return CategoryModel
     */
    public function getCategory($category_id)
    {
        $data = $this->getSetting("category_{$category_id}");
        if ($data
            && $data = @json_decode($data, true))
        {
            $this->load->model("extension/pplcz/normalizer");
            $data = $this->model_extension_pplcz_normalizer->denormalize($data, CategoryModel::class);
        }
        else {
            $data = new CategoryModel();
        }
        if (!$data->getPplConfirmAge15())
            $data->setPplConfirmAge15(false);


        return $data;
    }

    /**
     * @param $category_id
     * @param CategoryModel $category
     * @return void
     */
    public function setCategory($category_id, $category)
    {
        if ($category) {
            $this->load->model("extension/pplcz/normalizer");
            $data = $this->model_extension_pplcz_normalizer->normalize($category);
            $data = json_encode($data);
            $this->setSetting("category_{$category_id}", $data);
        }
    }


}