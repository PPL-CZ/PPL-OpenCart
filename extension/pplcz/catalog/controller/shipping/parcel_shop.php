<?php

namespace Opencart\Catalog\Controller\Extension\Pplcz\Shipping;

use Opencart\System\Engine\Controller;
use PPLCZ\Data\AddressData;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Model\Model\ParcelDataModel;

require_once  __DIR__ . '/../../../autoload.php';

class ParcelShop extends Controller
{
    protected function getData($onlyBody = false)
    {

        $ctype = $this->request->server['CONTENT_TYPE'] ?? '';
        $raw = file_get_contents('php://input') ?: '';
        if (stripos($ctype, 'application/json') !== false) {
            if (!$onlyBody) {
                return (@json_decode($raw, true) ?: []) + $this->request->get;
            } else {
                return @json_decode($raw, true);
            }
        } else {
            if (!$onlyBody) {
                parse_str($raw, $put);
                return ($put ?? []) + $this->request->get;
            } else {
                return $put ?? [];
            }
        }

    }

    private function viewData()
    {
        $data = ['parcelRequired' => false, 'parcel' => false];

        if (isset($this->session->data['pplcz_errors']))
        {
            $data['errors'] = $this->session->data['pplcz_errors'];
            unset($this->session->data['pplcz_errors']);
        }

        if (isset($this->session->data['pplcz_cart_setting'])
            && isset($this->session->data['shipping_method']['code'])) {
            $this->load->model("extension/pplcz/normalizer");
            /**
             * @var CartModel $cart
             * @var AddressData $address
             * @var ParcelDataModel $parcel
             */

            $cart = $this->model_extension_pplcz_normalizer->denormalize($this->session->data['pplcz_cart_setting'], CartModel::class);
            $address = $this->model_extension_pplcz_normalizer->denormalize($this->cart, AddressData::class);

            if ($cart->getServiceCode() === $this->session->data['shipping_method']['code']) {

                $data['parcelRequired'] = $cart->getParcelRequired();
                $data['telefonRequired'] = true;
                if (isset($this->session->data['pplcz_cart_telephone']))
                    $data['telephone'] = $this->session->data['pplcz_cart_telephone'];
                else {
                    $data['telephone'] = '';
                }
                if ($data['parcelRequired']) {


                    $mapsetting = [];

                    if (isset($this->session->data['pplcz_cart_setting_parcel'])) {
                        $parcel = $this->session->data['pplcz_cart_setting_parcel'];
                        $parcel = $this->model_extension_pplcz_normalizer->denormalize($parcel, ParcelDataModel::class);
                    } else {
                        $parcel = null;
                    }

                    if ($address) {
                        $mapsetting['data-parcerRequired'] = true;
                        if ($parcel) {
                            $mapsetting['data-address'] = join(',', array_filter([$parcel->getStreet(), $parcel->getZipCode(), $parcel->getCity()]));
                            $mapsetting['data-country'] = $parcel->getCountry();
                        } else {
                            $mapsetting['data-address'] = join(',', array_filter([$address->street, $address->zip, $address->city]));
                            $mapsetting['data-country'] = $address->country;
                        }
                        if ($parcel)
                            $data['parcel'] = $parcel;

                        $parcels = ["ParcelBox" => "1", "AlzaBox" => "2", "ParcelShop" => "3"];

                        if ($cart->getParcelBoxEnabled()) {
                            unset($parcels["ParcelBox"]);
                        }
                        if ($cart->getAlzaBoxEnabled()) {
                            unset($parcels["AlzaBox"]);
                        }
                        if ($cart->getParcelShopEnabled()) {
                            unset($parcels["ParcelShop"]);
                        }

                        $mapsetting['data-countries'] = $cart->getEnabledParcelCountries();
                        $mapsetting['data-hidden-points'] = join(',', array_keys($parcels));
                        $data['mapsetting'] = $mapsetting;
                    }
                }
            }
        }

        return $data;
    }


    public function injectStyles(&$route, &$data)
    {
        $route_current = $this->request->get['route'] ?? '';
        if (strpos($route_current, 'checkout') === false) return;

        $this->document->addStyle('extension/pplcz/catalog/view/stylesheet/ppl-label.css');
        $this->document->addStyle('extension/pplcz/catalog/view/stylesheet/ppl-map.css');
        $this->document->addScript('extension/pplcz/catalog/view/javascript/ppl-map.js');
    }

    public function injectCheckout(&$route, &$data, &$output)
    {
        $data = $this->viewData();
        $output .= $this->load->view('extension/pplcz/shipping/parcelshop', $data);

    }


    public function injectConfirm()
    {
        return;
    }

    public function index()
    {
        $data = $this->viewData();
        $this->response->setOutput($this->load->view('extension/pplcz/shipping/parcelshop', $data));
    }

    public function telephoneSelect()
    {
        if (!isset($this->session->data['shipping_address'])) {
            $data = $this->viewData();
            $this->response->setOutput($this->load->view('extension/pplcz/shipping/parcelshop', $data));
            return;
        }

        $parcel = $this->getData(true);

        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/order_cart");


        $this->model_extension_pplcz_order_cart->setTelephone(isset($parcel['telephone']) ? $parcel['telephone'] : null);

        $data = $this->viewData();
        $this->response->setOutput($this->load->view('extension/pplcz/shipping/parcelshop', $data));
    }

    public function parcelSelect()
    {
        if (!isset($this->session->data['shipping_address'])) {
            $data = $this->viewData();
            $this->response->setOutput($this->load->view('extension/pplcz/shipping/parcelshop', $data));
            return;
        }

        $parcel = $this->getData(true);

        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/order_cart");

        if ($parcel) {
            $parcel = $this->model_extension_pplcz_normalizer->denormalize($parcel, ParcelDataModel::class);
        }

        $this->model_extension_pplcz_order_cart->setParcel($parcel);

        $data = $this->viewData();
        $this->response->setOutput($this->load->view('extension/pplcz/shipping/parcelshop', $data));
    }

    public function onShippingChange(&$route, &$data, &$output)
    {
        $this->load->model("extension/pplcz/order_cart");
        $this->model_extension_pplcz_order_cart->updateCart();
    }

    public function orderAdd(&$route, &$args, &$order_id)
    {
        $this->load->model("extension/pplcz/order_cart");
        $this->model_extension_pplcz_order_cart->updateCart($order_id);
    }

}