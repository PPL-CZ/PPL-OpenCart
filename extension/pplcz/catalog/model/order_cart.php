<?php

namespace Opencart\Catalog\Model\Extension\Pplcz;

use PPLCZ\Data\AddressData;
use PPLCZ\Data\CartProxy;
use PPLCZ\Data\OrderCartData;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Validator\WP_Error;

require_once  __DIR__ . '/../../autoload.php';
/**
 * @property \PPLCZ\Validator\Validator $model_extension_pplcz_validator
 * @property \PPLCZ\Repository\Setting $model_extension_pplcz_setting
 * @property \PPLCZ\Repository\Normalizer $model_extension_pplcz_normalizer
 */
class OrderCart extends \PPLCZ\Repository\OrderCart
{

    public function validate($order_id)
    {
        $shippingCode = $this->session->data['shipping_method']['code'];
        list($extension, $type) = explode(".", $shippingCode);

        if ($extension !== 'pplcz')
            return null;

        $cartProxy = new CartProxy();

        $cartProxy->storeId = (int)$this->config->get('config_store_id');
        $cartProxy->orderCart = $this->getOrder($order_id);

        $cartProxy->shipmentType = $type;

        $this->load->model("extension/pplcz/validator");

        $errors = new WP_Error();
        $this->model_extension_pplcz_validator->validate($cartProxy, $errors);

        if ($errors->errors)
        {
            return $errors;
        }
        return null;
    }

    /**
     * @param $order_id
     * @return null|OrderCartData
     */
    private function getOrder($order_id)
    {
        /**
         * @var OrderCartData $order
         */
        if ($order_id) {
            $order = $this->getDataByOrderId($order_id);
            if (!$order) {
                $order = $this->new(OrderCartData::class, [
                    'order_id' => $order_id
                ]);

                if (isset($this->session->data['pplcz_cart_setting']))
                    $order->cart_setting = $this->session->data['pplcz_cart_setting'];
                if (isset($this->session->data['pplcz_cart_setting_parcel']))
                    $order->parcel_setting = $this->session->data['pplcz_cart_setting_parcel'];
                if (isset($this->session->data['pplcz_telephone']))
                    $order->contact_telephone = $this->session->data['pplcz_telephone'];
            }
            return $order;
        }
        return null;
    }

    /**
     * @param ParcelDataModel $parcel
     * @return void
     */
    public function setTelephone($telephone)
    {
        $order_id = null;
        if (isset($this->session->data['order_id']))
            $order_id = $this->session->data['order_id'];

        $order = $this->getOrder($order_id);

        if ($telephone) {
            $this->load->model("extension/pplcz/normalizer");
            $this->session->data['pplcz_cart_telephone'] = $telephone;
            if ($order) {
                $order->contact_telephone = $this->session->data['pplcz_cart_telephone'];
            }
        } else {
            unset($this->session->data['pplcz_cart_telephone']);
            if ($order) {
                $order->contact_telephone = null;
            }
        }

        if ($order)
            $this->save($order);
    }



    /**
     * @param ParcelDataModel $parcel
     * @return void
     */
    public function setParcel($parcel)
    {
        $order_id = null;
        if (isset($this->session->data['order_id']))
            $order_id = $this->session->data['order_id'];

        $order = $this->getOrder($order_id);


        if ($parcel) {
            $this->load->model("extension/pplcz/normalizer");
            $this->session->data['pplcz_cart_setting_parcel'] = $this->model_extension_pplcz_normalizer->normalize($parcel);
            if ($order) {
                $order->parcel_setting = $this->session->data['pplcz_cart_setting_parcel'];
            }
        } else {
            unset($this->session->data['pplcz_cart_setting_parcel']);
            if ($order) {
                $order->parcel_setting = null;
            }
        }

        if ($order)
            $this->save($order);

    }

    public function updateCart($order_id = null)
    {
        if (isset($this->session->data['order_id']))
            $order_id = $this->session->data["order_id"];

        if (!isset($this->session->data['shipping_method']['code'])) {
            return;
        }

        $order = $this->getOrder($order_id);

        $shippingCode = $this->session->data['shipping_method']['code'];

        list($extension, $type) = explode(".", $shippingCode);

        if ($extension !== "pplcz") {
            return;

        }

        $this->load->model("extension/pplcz/setting");

        $store_id = (int)$this->config->get('config_store_id');

        $shipmentSetting = $this->model_extension_pplcz_setting->getShipments($store_id);

        foreach ($shipmentSetting as $value) {
            if ($value->getGuid() === $type) {
                $this->load->model("extension/pplcz/normalizer");
                /**
                 * @var CartModel $cart
                 * @var AddressData $address
                 */
                $cart = $this->model_extension_pplcz_normalizer->denormalize($value, CartModel::class);
                $this->session->data['pplcz_cart_setting'] = $this->model_extension_pplcz_normalizer->normalize($cart);

                if ($order) {

                    if (isset($this->session->data['pplcz_cart_setting']))
                        $order->cart_setting = $this->session->data['pplcz_cart_setting'];
                    if (isset($this->session->data['pplcz_cart_setting_parcel']))
                        $order->parcel_setting = $this->session->data['pplcz_cart_setting_parcel'];
                    if (isset($this->session->data['pplcz_cart_telephone']))
                        $order->contact_telephone = $this->session->data['pplcz_cart_telephone'];

                    $this->save($order);
                }
                return;

            }
        }
    }
}