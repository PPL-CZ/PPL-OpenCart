<?php
namespace Opencart\Catalog\Model\Extension\Pplcz\Shipping;

use Opencart\Catalog\Model\Extension\Pplcz\Normalizer;
use Opencart\Catalog\Model\Extension\Pplcz\Setting;
use Opencart\System\Engine\Model;
use Opencart\System\Library\Cart\Cart;
use PPLCZ\Model\Model\CartModel;

require_once  __DIR__ . '/../../../autoload.php';

/**
 * @property-read Cart $cart
 * @property-read Setting $model_extension_pplcz_setting
 * @property-read Normalizer $model_extension_pplcz_normalizer
 */
class Pplcz extends Model
{
    public function  getQuote(array $address): array {

        $this->load->model("extension/pplcz/normalizer");
        $this->load->model("extension/pplcz/setting");



        $store_id = (int)$this->config->get('config_store_id');

        $shipmentSettings = $this->model_extension_pplcz_setting->getShipments($store_id);

        $quote_data = [];
        $method_data = [];

        foreach ($shipmentSettings as $setting) {
            /**
             * @var CartModel $cartSetting
             */
            $cartSetting = $this->model_extension_pplcz_normalizer->denormalize($setting, CartModel::class);

            if ($cartSetting->getDisabledByCountry()
                || $cartSetting->getDisabledByProduct()
                || $cartSetting->getDisabledByRules()
                || $cartSetting->getDisabledByWeight())
                continue;

            $currency = $this->registry->get('currency');
            $code = $session->data['currency'] ?? $this->config->get('config_currency');

            $symbol_left   = $currency->getSymbolLeft($cartSetting->getCurrency());
            $symbol_right  = $currency->getSymbolRight($cartSetting->getCurrency());


            $base = $this->config->get('config_currency'); // např. 'EUR'
            // Převod do základní měny:
            $cost_base = $currency->convert($cartSetting->getCost(), $cartSetting->getCurrency(), $base);

            $quote_data[$setting->getGuid()] = [
                'code'         => 'pplcz.' . $setting->getGuid(),
                'name'         => $cartSetting->getParcelRequired() ? "Výdejní místo" : "Doprava na adresu",
                'cost'         => $cost_base,
                'text'         => "{$symbol_left}{$cartSetting->getCost()}{$symbol_right}",
                "tax_class_id" => $cartSetting->getTaxId() ?: 0
            ];
        }

        if ($quote_data) {
            $method_data = [
                'code' => 'pplcz',
                'name' => "PPL.CZ",
                'quote' => $quote_data,
                'sort_order' => 0,
                'error' => false
            ];
        }

        return $method_data;
    }
}