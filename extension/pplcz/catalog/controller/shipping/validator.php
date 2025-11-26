<?php

namespace Opencart\Catalog\Controller\Extension\Pplcz\Shipping;


use Opencart\Catalog\Model\Extension\Pplcz\OrderCart;
use Opencart\System\Engine\Controller;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Model\Model\ShipmentMethodSettingModel;

require_once  __DIR__ . '/../../../autoload.php';

/**
 * @property OrderCart $model_extension_pplcz_order_cart
 */
class Validator extends Controller
{
    public function order(&$route, &$args)
    {
        if (strpos($route, ".confirm") > 0) {

            $this->load->model("extension/pplcz/order_cart");
            $errors = $this->model_extension_pplcz_order_cart->validate($this->session->data['order_id']);
            if ($errors) {
                $this->session->data['pplcz_errors'] = $errors->errors;
                $this->response->setOutput(json_encode(["pplcz_error" => true]));
                $this->response->addHeader("HTTP/1.1 400 Bad Request");
                $this->response->addHeader('Content-Type: application/json');
                $this->response->output();
                exit();

            }
        }
    }



    public function codvalidator(&$route, &$args, &$output)
    {
        // Je zvolená platba?
        if (!isset($this->session->data['shipping_method']['code']))
            return;

        $shippingCode = $this->session->data['shipping_method']['code'];

        list($extension, $type) = explode(".", $shippingCode);

        if ($extension !== "pplcz")
            return;

        $store_id = (int)$this->config->get('config_store_id');
        $this->load->model("extension/pplcz/setting");
        /**
         * @var ShipmentMethodSettingModel[] $shipmentSetting
         */
        $shipmentSetting = $this->model_extension_pplcz_setting->getShipments($store_id);

        foreach ($shipmentSetting as $key => $value) {
            if ($value->getGuid() === $type) {
                $this->load->model("extension/pplcz/normalizer");
                /**
                 * @var CartModel $cartmodel
                 */
                $cartmodel = $this->model_extension_pplcz_normalizer->denormalize($value, CartModel::class);
                if ($cartmodel->getDisableCod()) {
                    foreach ($output as $key2 => $value2) {
                        if ($key2 === $value->getCodPayment())
                            unset($output[$key2]);
                    }
                }
                if ($value->getDisablePayments())
                {
                    foreach ($value->getDisablePayments() as $disablePayment)
                    {
                        unset($output[$disablePayment]);
                    }
                }
            }
        }
        return;
    }
}