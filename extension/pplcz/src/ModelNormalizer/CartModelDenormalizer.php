<?php

namespace PPLCZ\ModelNormalizer;


use Opencart\Catalog\Model\Catalog\Product;
use Opencart\Catalog\Model\Extension\Pplcz\Setting;
use Opencart\System\Engine\Registry;
use Opencart\System\Library\Cart\Cart;
use PPLCZ\Data\AddressData;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Model\Model\CountryModel;
use PPLCZ\Model\Model\ShipmentMethodSettingCurrencyModel;
use PPLCZ\Model\Model\ShipmentMethodSettingModel;
use PPLCZ\Repository\Config;
use PPLCZ\TLoader;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @property Registry $registry
 */
class CartModelDenormalizer implements DenormalizerInterface
{
    use TLoader;

    public function __construct($registry)
    {
        $this->setRegistry($registry);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ShipmentMethodSettingModel && $type === CartModel::class;
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /**
         * @var ShipmentMethodSettingModel $data
         * @var Cart $cart
         * @var Setting $setting
         * @var Config $config
         * @var Product $product
         */
        $shipmentCartModel = new CartModel();
        $cart = $this->registry->get("cart");
        $setting = $this->loadModel("extension/pplcz/setting");
        $config = $this->loadModel("extension/pplcz/config");
        $product = $this->loadModel("catalog/product");


        $parcelPlaces = $setting->getParcelPlaces();
        $shipmentCartModel->setServiceCode("pplcz.{$data->getGuid()}");
        $shipmentCartModel->setParcelRequired(false);
        $shipmentCartModel->setMapEnabled(false);
        $shipmentCartModel->setAgeRequired(false);
        $shipmentCartModel->setDisableCod(true);
        $shipmentCartModel->setDisabledByProduct(false);
        $shipmentCartModel->setDisabledByCountry(false);
        $shipmentCartModel->setDisabledByRules(false);
        $shipmentCartModel->setParcelShopEnabled(false);
        $shipmentCartModel->setParcelBoxEnabled(false);
        $shipmentCartModel->setAlzaBoxEnabled(false);


        $total = $this->getTotalWithoutShipping($cart);
        $total = $total['grand_total'];

        /**
         * @var AddressData $addressData
         */
        $addressData = $this->loadModel("extension/pplcz/normalizer")->denormalize($cart, AddressData::class);


        if (!$addressData) {
            $shipmentCartModel->setDisabledByRules(true);
            return $shipmentCartModel;
        }
        $country_code = $addressData->country;

        if (!$data->getCountries() ||
            !in_array($country_code, $data->getCountries(), true)) {
            $shipmentCartModel->setDisabledByRules(true);
            return $shipmentCartModel;
        }
        $currency = $this->getCurrentCurrency();
        if ($currency)
            $currency = $currency['code'];

        $shipmentCartModel->setCurrency($currency);

        $currencySetting = null;
        if (!$data->getCurrencies() ||
            !array_filter($data->getCurrencies(), function (ShipmentMethodSettingCurrencyModel $item) use ($currency) {
                if ($item->getCurrency() === $currency && $item->getEnabled())
                    return true;
                return false;
            })) {
            $shipmentCartModel->setDisabledByRules(true);
            return $shipmentCartModel;
        } else {
            $currencySetting = array_filter($data->getCurrencies(), function ($item) use ($currency) {
                return $item->getCurrency() === $currency;
            });
            $currencySetting = reset($currencySetting);
        }

        if ($data->getParcelBoxes()) {
            $shipmentCartModel->setParcelRequired(true);
            $shipmentCartModel->setParcelBoxEnabled(!$parcelPlaces->getDisabledParcelBox());
            $shipmentCartModel->setAlzaBoxEnabled(!$parcelPlaces->getDisabledAlzaBox());
            $shipmentCartModel->setParcelShopEnabled(!$parcelPlaces->getDisabledParcelShop());

            $countriesWithParcelshop = array_map(function (CountryModel $item) {
                return $item->getCode();
            }, array_filter($config->getCountries(), function (CountryModel $item) {
                return $item->getParcelAllowed();
            }));

            if ($parcelPlaces->getDisabledCountries()) {
                if (in_array($addressData->country, $parcelPlaces->getDisabledCountries(), true)) {
                    $shipmentCartModel->setDisabledByCountry(true);
                    return $shipmentCartModel;
                }
            }

            if (!in_array($addressData->country, $countriesWithParcelshop, true)) {
                $shipmentCartModel->setDisabledByCountry(true);
                return $shipmentCartModel;
            }

        } else {
            $shipmentCartModel->setParcelRequired(false);
        }


        $shipmentCartModel->setDisabledByWeight(true);

        $totalWeight = $this->getCartWeightInKg($cart);

        $totalWeight = $totalWeight ? $totalWeight['value'] : 1000000;

        $selectedWeightPrice = 0;

        if ($data->getCostByWeight()) {
            $selectedWeightRule = null;

            $activeCurrency = array_filter($data->getCurrencies(), function ($currencies) use ($currency) {
                return $currencies->getCurrency() === $currency;
            });

            $activeCurrency = reset($activeCurrency);

            if ($activeCurrency && $activeCurrency->getEnabled()) {

                foreach ($data->getWeights() as $weight) {
                    if (($weight->getFrom() == null || $weight->getFrom() <= $totalWeight)
                        && ($weight->getTo() == null || $weight->getTo() > $totalWeight)) {
                        //["EUR" => 6, "CZK" => 5]. ["CZK" => 5], 5
                        // [] => null
                        $weightPrice = array_filter($weight->getPrices(), function ($price) use ($currency) {
                            return $price->getCurrency() === $currency;
                        });
                        $weightPrice = reset($weightPrice);
                        if ($weightPrice && $selectedWeightPrice < $weightPrice->getPrice()) {
                            $selectedWeightPrice = $weightPrice->getPrice() ?: 0;
                            $shipmentCartModel->setDisabledByWeight(false);
                            $selectedWeightRule = $weight;
                        }
                    }
                }

                if ($selectedWeightRule) {
                    $shipmentCartModel->setAlzaBoxEnabled($shipmentCartModel->getAlzaBoxEnabled() && !$selectedWeightRule->getDisabledAlzaBox());
                    $shipmentCartModel->setParcelBoxEnabled($shipmentCartModel->getParcelBoxEnabled() && !$selectedWeightRule->getDisabledParcelBox());
                    $shipmentCartModel->setParcelShopEnabled($shipmentCartModel->getParcelShopEnabled() && !$selectedWeightRule->getDisabledParcelShop());
                }
            }

        } else {
            $selectedWeightRule = array_filter($data->getCurrencies(), function ($currencies) use ($currency) {
                return $currencies->getCurrency() === $currency;
            });

            $selectedWeightRule = reset($selectedWeightRule);
            if ($selectedWeightRule && $selectedWeightRule->getEnabled()) {
                $shipmentCartModel->setDisabledByWeight(false);
                /**
                 * @var ShipmentMethodSettingCurrencyModel $selectedWeightPrice
                 */
                $selectedWeightPrice = $selectedWeightRule->getCost() ?: 0;
            }
        }

        $session = $this->registry->get('session');
        $code = null;
        if (isset($session->data['payment_method']['code']))
            $code = $session->data['payment_method']['code'];

        if ($code && $data->getDisablePayments() &&
            in_array($code, $data->getDisablePayments(), true)) {
            $shipmentCartModel->setDisabledByRules(true);
            return $shipmentCartModel;
        }

        $limits = $config->getLimits();

        $codServiceCode = "";
        $serviceCode = "";
        if ($country_code === "CZ") {
            if ($data->getParcelBoxes()) {
                $codServiceCode = "SMAD";
                $serviceCode = "SMAR";
            } else {
                $codServiceCode = "PRID";
                $serviceCode = "PRIV";
            }
        } else {
            if ($data->getParcelBoxes()) {
                $codServiceCode = "SMED";
                $serviceCode = "SMEU";
            } else {
                $codServiceCode = "COND";
                $serviceCode = "CONN";
            }
        }

        $currentCurrenty = $this->getCurrentCurrency();

        $currentCurrentyCode = null;
        if ($currentCurrenty) {
            $currentCurrentyCode = $currentCurrenty['code'];
        }

        $maxCodPrice = array_values(array_filter($limits['COD'], function ($item) use ($country_code, $codServiceCode, $currentCurrentyCode) {
            if ($item['product'] === $codServiceCode && $item['currency'] === $currentCurrentyCode && $item['country'] === $country_code) {
                return true;
            }
            return false;
        }, true));


        if (!$maxCodPrice
            || !isset($maxCodPrice[0])
            || !isset($maxCodPrice[0]['max'])
            || $maxCodPrice[0]['max'] === ''
            || $maxCodPrice[0]['max'] === null
            || $total >= $maxCodPrice[0]['max']) {
            $shipmentCartModel->setDisableCod(true);
            if ($currencySetting->getCostOrderFree() != null
                && $currencySetting->getCostOrderFree() <= $total) {
                $shipmentCartModel->setCodFee(0);
                $shipmentCartModel->setCost(0);
            } else {
                $shipmentCartModel->setCodFee(0);
                $shipmentCartModel->setCost($selectedWeightPrice ?: 0);
            }
        } else {
            $max = @$maxCodPrice[0]['max'];
            if ($max !== '' && $max !== null && $total >= $max) {
                $shipmentCartModel->setDisableCod(true);
                $shipmentCartModel->setCodFee(100000);
                $shipmentCartModel->setCost(100000);
            } else {
                $isCod = !!$codServiceCode;
                $freeCodPrice = $currencySetting->getCostOrderFreeCod();


                if ($isCod
                    && $freeCodPrice != null
                    && $freeCodPrice <= $total) {
                    if ($currencySetting->getCostCodFeeAlways())
                        $shipmentCartModel->setCodFee($currencySetting->getCostCodFee() ?: 0);
                    else
                        $shipmentCartModel->setCodFee(0);
                    $shipmentCartModel->setCost(0);
                    $shipmentCartModel->setDisableCod(false);
                } else if ($isCod) {
                    $shipmentCartModel->setCodFee($currencySetting->getCostCodFee() ?: 0);
                    $shipmentCartModel->setCost($selectedWeightPrice ?: 0);
                    $shipmentCartModel->setDisableCod(false);
                } else {
                    $shipmentCartModel->setDisableCod(false);
                    $shipmentCartModel->setCodFee(0);
                    $costorderfree = $currencySetting->getCostOrderFree();

                    if ($costorderfree != null && floatval($costorderfree) <= $total)
                        $shipmentCartModel->setCost(0);
                    else
                        $shipmentCartModel->setCost($selectedWeightPrice ?: 0);
                }
            }
        }

        if (!$shipmentCartModel->getParcelShopEnabled() && !$shipmentCartModel->getParcelBoxEnabled() && !$shipmentCartModel->getAlzaBoxEnabled() && $shipmentCartModel->getParcelRequired()) {
            $shipmentCartModel->setDisabledByRules(true);
        }

        if (!$shipmentCartModel->getParcelShopEnabled() && $shipmentCartModel->getAgeRequired()) {
            $shipmentCartModel->setDisabledByRules(true);
        }

        $tax_class_ids = array_column($cart->getProducts(), 'tax_class_id');
        $max_rate = 0;
        $tax = $this->registry->get("tax");

        foreach ($tax_class_ids as $tax_class_id) {

            $rates = $tax->getRates(100, $tax_class_id); // 100 = dummy cena
            foreach ($rates as $rate) {
                if ($rate['amount'] > $max_rate) {
                    $max_rate = $rate['amount'];
                }
            }
        }

        $selected_tax_class_id = null;
        foreach ($tax_class_ids as $tax_class_id) {
            $rates = $tax->getRates(100, $tax_class_id);
            foreach ($rates as $rate) {
                if ($rate['amount'] == $max_rate) {
                    $selected_tax_class_id = $tax_class_id;
                    break 2;
                }
            }
        }

        $shipmentCartModel->setTaxId($selected_tax_class_id);

        if ($data->getIsPriceWithDph() && $shipmentCartModel->getCost() && $selected_tax_class_id)
        {
            $gross = $shipmentCartModel->getCost(); // tvoje částka vč. DPH

            // zjisti celkovou sazbu pro 100 NET
            $probe_rates = $this->registry->get('tax')->getRates(100, $selected_tax_class_id);
            $total_rate  = 0.0;
            foreach ($probe_rates as $r) {
                $total_rate += $r['amount']; // např. 21.0000 z 100 => 21 %
            }

            $factor = 1 + ($total_rate / 100);
            $net    = $factor > 0 ? round($gross / $factor, 4) : $gross;  // čistý základ
            $shipmentCartModel->setCost($net);
        }

        foreach ($cart->getProducts() as $productItem) {
            $productSetting = $setting->getProduct($productItem['product_id']);

            if ($productSetting->getPplConfirmAge18() && $country_code === 'CZ') {
                $shipmentCartModel->setAgeRequired(true);
                $shipmentCartModel->setAge18Required(true);
                $shipmentCartModel->setAlzaBoxEnabled(false);
                $shipmentCartModel->setParcelBoxEnabled(false);
            } else if ($productSetting->getPplConfirmAge15() && $country_code === 'CZ') {
                $shipmentCartModel->setAgeRequired(true);
                $shipmentCartModel->setAge15Required(true);
                $shipmentCartModel->setAlzaBoxEnabled(false);
                $shipmentCartModel->setParcelBoxEnabled(false);
            }

            foreach (($productSetting->getPplDisabledTransport() ?: []) as $transport) {
                if ($transport->getCode() === $serviceCode) {
                    $shipmentCartModel->setDisabledByRules(true);
                }
                if ($transport->getCode() === $codServiceCode) {
                    $shipmentCartModel->setDisableCod(true);
                }
            }


            foreach ($product->getCategories($productItem['product_id']) as $category) {
                $categorySetting = $setting->getCategory($category['category_id']);
                if ($categorySetting->getPplConfirmAge18() && $country_code === 'CZ') {
                    $shipmentCartModel->setAgeRequired(true);
                    $shipmentCartModel->setAge18Required(true);
                    $shipmentCartModel->setAlzaBoxEnabled(false);
                    $shipmentCartModel->setParcelBoxEnabled(false);
                } else if ($categorySetting->getPplConfirmAge15() && $country_code === 'CZ') {
                    $shipmentCartModel->setAgeRequired(true);
                    $shipmentCartModel->setAge15Required(true);
                    $shipmentCartModel->setAlzaBoxEnabled(false);
                    $shipmentCartModel->setParcelBoxEnabled(false);
                }
                foreach (($categorySetting->getPplDisabledTransport() ?: []) as $transport) {
                    if ($transport->getCode() === $serviceCode) {
                        $shipmentCartModel->setDisabledByRules(true);
                    }
                    if ($transport->getCode() === $codServiceCode) {
                        $shipmentCartModel->setDisableCod(true);
                    }
                }
            }
        }


        $shipmentCartModel->setMapEnabled(
            ($shipmentCartModel->getParcelShopEnabled() || $shipmentCartModel->getParcelBoxEnabled() || $shipmentCartModel->getAlzaBoxEnabled())
            && !$shipmentCartModel->getDisabledByCountry()
        );

        return $shipmentCartModel;
    }

    public function getCurrentShippingAddress(Cart $cart)
    {
        if (!$cart->hasShipping()) {
            return null;
        }
        $session = $this->registry->get("session");

        // 2) Session: shipping_address (checkout vyplněná adresa)
        if (!empty($session->data['shipping_address'])) {
            return $session->data['shipping_address'];
        }

        // 3) Guest: guest->shipping
        if (!empty($session->data['guest']['shipping'])) {
            return $session->data['guest']['shipping'];
        }

        $customer = $this->registry->get("customer");

        // 4) Přihlášený: default adresa
        if ($customer->isLogged() && $customer->getAddressId()) {
            $addrs = $this->loadModel("account/address");

            $addr = $addrs->getAddress($customer->getAddressId());
            if ($addr) return $addr;
        }

        return null;
    }

    private function getAddress(Cart $cart)
    {
        $addr = $this->getCurrentShippingAddress($cart);
        if (!$addr) {
            return null;
        }

        $localization = $this->loadModel("localisation/country");
        $zone = $this->loadModel("localisation/zone");

        $country = !empty($addr['country_id'])
            ? $localization->getCountry((int)$addr['country_id'])
            : null;

        $zone = !empty($addr['zone_id'])
            ? $zone->getZone((int)$addr['zone_id'])
            : null;

        return [
            'firstname' => $addr['firstname'] ?? '',
            'lastname' => $addr['lastname'] ?? '',
            'company' => $addr['company'] ?? '',
            'address_1' => $addr['address_1'] ?? '',
            'address_2' => $addr['address_2'] ?? '',
            'postcode' => $addr['postcode'] ?? '',
            'city' => $addr['city'] ?? '',
            'country_id' => (int)($addr['country_id'] ?? 0),
            'country' => $country['name'] ?? ($addr['country'] ?? ''),
            'iso_code_2' => $country['iso_code_2'] ?? '',
            'iso_code_3' => $country['iso_code_3'] ?? '',
            'address_format' => $country['address_format'] ?? '',
            'postcode_required' => isset($country['postcode_required']) ? (bool)$country['postcode_required'] : false,
            'zone_id' => (int)($addr['zone_id'] ?? 0),
            'zone' => $zone['name'] ?? ($addr['zone'] ?? ''),
            'zone_code' => $zone['code'] ?? '',
            'custom_field' => $addr['custom_field'] ?? [],
            'address_id' => $addr['address_id'] ?? null,
        ];

    }

    private function getTotalWithoutShipping(Cart $cart): array
    {
        $totals = [];
        $taxes = $cart->getTaxes();
        $total = 0.0;

        //$exclude = ['shipping']; // přidej další kódy, pokud používáš alternativní dopravní moduly

        $extensionModel = $this->loadModel('setting/extension');
        $config = $this->registry->get('config');
        $currency = $this->registry->get('currency');

        $currentCurrency = $this->getCurrentCurrency();


        $extensions = $extensionModel->getExtensionsByType('total');

        // připrav pořadí a route
        $ordered = [];
        foreach ($extensions as $ext) {
            $code = $ext['code'];
            $ordered[] = [
                'code' => $code,
                'sort_order' => (int)($config->get('total_' . $code . '_sort_order') ?? 0),
                'status' => (bool)($config->get('total_' . $code . '_status') ?? false),
                'route' => "extension/{$ext['extension']}/total/" . $code
            ];
        }

        usort($ordered, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        foreach ($ordered as $ext) {
            if (!$ext['status']) continue;
            if ($ext['code'] !== 'sub_total') continue;
            //    if (in_array($ext['code'], $exclude, true)) continue; // přeskočit dopravu

            $model = $this->loadModel($ext['route']);
            ($model->getTotal)($totals, $taxes, $total);
        }

        $base = $config->get('config_currency');



        return [
            'grand_total' => $currency->convert($total, $base,  $currentCurrency['code'])   // celková částka BEZ dopravy (i bez její daně)
        ];
    }

    public function getCurrentCurrency(): array
    {
        $config = $this->registry->get('config');
        $currency = $this->registry->get('currency');
        $session = $this->registry->get('session');

        $code = $session->data['currency'] ?? $config->get('config_currency');

        $symbol_left = $currency->getSymbolLeft($code);
        $symbol_right = $currency->getSymbolRight($code);
        $value = $currency->getValue($code);
        $decimal_place = (int)$currency->getDecimalPlace($code);

        return [
            'code' => $code,
            'symbol_left' => $symbol_left,
            'symbol_right' => $symbol_right,
            'value' => $value,
            'decimal_place' => $decimal_place,
        ];
    }

    /**
     * Vrátí váhu košíku PŘEPOČTENOU do KG + naformátovaný text "x kg".
     */
    public function getCartWeightInKg(Cart $cart): array
    {

        $config = $this->registry->get("config");
        $weight = $this->registry->get("weight");


        $cart_weight = (float)$cart->getWeight();
        $from_id = (int)$config->get('config_weight_class_id');

        $kg_id = $this->getKgWeightClassId();

        if ($kg_id) {
            $weight_kg = (float)$weight->convert($cart_weight, $from_id, $kg_id);
            $text = $weight->format($weight_kg, $kg_id); // např. "1.25 kg"
            return ['value' => $weight_kg, 'text' => $text, 'class_id' => $kg_id];
        }

        $weightmodel = $this->loadModel("localisation/weight_class");

        // Fallback, kdyby v DB chyběla definice 'kg'

        $from = $weightmodel->getWeightClass($from_id);

        $unit = isset($from['unit']) ? mb_strtolower($from['unit']) : '';
        if ($unit === 'g') {
            $weight_kg = $cart_weight / 1000.0;
        } elseif ($unit === 'lb' || $unit === 'lbs') {
            $weight_kg = $cart_weight * 0.45359237;
        } else {
            // Poslední jistota – necháme původní hodnotu a jen přejmenujeme
            $weight_kg = $cart_weight;
        }

        // Jednoduché formátování, když chybí 'kg' class
        $text = rtrim(rtrim(number_format($weight_kg, 3, '.', ''), '0'), '.') . ' kg';
        return ['value' => $weight_kg, 'text' => $text, 'class_id' => null];
    }


    private function getKgWeightClassId()
    {

        $query = $this->registry->get('db')->query("SELECT weight_class_id 
                             FROM " . DB_PREFIX . "weight_class_description 
                             WHERE LOWER(unit) = 'kg' 
                             LIMIT 1");

        if ($query->num_rows) {
            return (int)$query->row['weight_class_id'];
        }
        return null;
    }
}