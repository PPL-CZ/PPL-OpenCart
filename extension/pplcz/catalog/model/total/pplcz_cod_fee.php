<?php
namespace Opencart\Catalog\Model\Extension\Pplcz\Total;

use Opencart\Catalog\Model\Extension\Pplcz\Setting;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Repository\Normalizer;

require_once  __DIR__ . '/../../../autoload.php';

/**
 * @property-read Normalizer $model_extension_pplcz_normalizer
 * @property-read Setting $model_extension_pplcz_setting
 */
class PplczCodFee extends \Opencart\System\Engine\Model {
    public function getTotal(array &$totals, array &$taxes, float &$total) {

        // Je zvolená platba?
        if (!isset($this->session->data['payment_method']['code']))
            return;

        $code = $this->session->data['payment_method']['code'];

        if (!$code)
            return;

        if (!isset($this->session->data['shipping_method']['code']))
            return;

        $shippingCode = $this->session->data['shipping_method']['code'];

        list($extension, $type) = explode(".", $shippingCode);

        if ($extension !== "pplcz")
            return;

        $this->load->model("extension/pplcz/setting");


        $store_id = (int)$this->config->get('config_store_id');

        $shipmentSetting = $this->model_extension_pplcz_setting->getShipments($store_id);
        foreach ($shipmentSetting as $key => $value)
        {
            if ($value->getGuid() === $type)
            {
                $this->load->model("extension/pplcz/normalizer");
                /**
                 * @var CartModel $cartmodel
                 */
                $cartmodel = $this->model_extension_pplcz_normalizer->denormalize($value, CartModel::class);
                if ($cartmodel)
                {
                    $sort  = (int)($this->config->get('total_pplcz_cod_fee_order') ?? 2); // hned za shipping
                    $tax_class_id = $cartmodel->getTaxId();
                    $codFee = $cartmodel->getCodFee();

                    $currency = $this->registry->get('currency');

                    $base = $this->config->get('config_currency');

                    $cost_base = $currency->convert($codFee, $cartmodel->getCurrency(), $base);
                    $totals[] = [
                        'extension'  => 'pplcz',
                        'code'       => 'pplcz_cod_fee',
                        'title'      => "Příplatek za dobírku",
                        'value'      => (float)$cost_base,
                        'sort_order' => (int)$sort,
                    ];
                    $total += $cost_base;

                    if ($cartmodel->getTaxId()) {
                        if ($cartmodel->getIsPriceWithDph())
                        {
                            $gross = $cost_base; // tvoje částka vč. DPH

                            // zjisti celkovou sazbu pro 100 NET
                            $probe_rates = $this->tax->getRates(100, $cartmodel->getTaxId());
                            $total_rate  = 0.0;
                            foreach ($probe_rates as $r) {
                                $total_rate += $r['amount']; // např. 21.0000 z 100 => 21 %
                            }

                            $factor = 1 + ($total_rate / 100);
                            $net    = $factor > 0 ? round($gross / $factor, 4) : $gross;  // čistý základ
                            $vat    = $gross - $net;

                            $totals[] = [
                                'extension' => 'pplcz',
                                'code' => 'pplcz_cod_fee',
                                'title' => "Příplatek za dobírku (DPH)",
                                'value' => (float)$vat,
                                'sort_order' => (int)$sort,
                            ];
                            $total += $cost_base;
                        }
                        else {
                            $tax_class_id = $cartmodel->getTaxId(); // nebo ze session metody dopravy
                            $tax_rates = $this->tax->getRates($cost_base, $tax_class_id);

                            $dphp = 0;
                            foreach ($tax_rates as $rate) {
                                $dphp += $rate['amount']; // sečte všechny sazby z té tax class
                            }

                            $totals[] = [
                                'extension' => 'pplcz',
                                'code' => 'pplcz_cod_fee',
                                'title' => "Příplatek za dobírku (DPH)",
                                'value' => (float)$dphp,
                                'sort_order' => (int)$sort,
                            ];

                            $total += $dphp;
                        }

                    }
                }
            }
        }

    }
}