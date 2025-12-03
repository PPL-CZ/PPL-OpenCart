<?php
namespace PPLCZ\Controller;

use Opencart\System\Engine\Controller;
use PPLCZ\Repository\Setting;

/**
 * @property Setting $model_extension_pplcz_setting
 */
class Map extends  Controller {

    private function getGetData($name)
    {
        if (isset($this->request->get[$name])) {
            return $this->request->get[$name];
        }
        return null;
    }

    public function index()
    {

        $lat = $this->getGetData('ppl_lat');
        $lng = $this->getGetData('ppl_lng');
        $withCard = $this->getGetData('ppl_withCard');
        $withCash = $this->getGetData('ppl_withCash');
        $country = $this->getGetData('ppl_country');
        $hiddenPoints = $this->getGetData('ppl_hiddenpoints');
        $countries = $this->getGetData('ppl_countries');
        $address =$this->getGetData('ppl_address');

        $data = ['mapsetting' => []];

        if (floatval($lat) && floatval($lng)) {
            $data['mapsetting']["data-lat"] = $lat;
            $data['mapsetting']["data-lng"] = $lng;
        }
        $data['mapsetting']['data-initialfilters'] = [];
        if (intval($withCard)) {
            $data['mapsetting']["data-initialfilters"][] = "CardPayment";
        }
        if (intval($withCash))
            $data['mapsetting']["data-initialfilters"][] = "ParcelShop";

        if (!$data['mapsetting']["data-initialfilters"]) {
            unset($data['mapsetting']["data-initialfilters"]);
        } else {
            $data['mapsetting']["data-initialfilters"] = join(',', $data['mapsetting']["data-initialfilters"]);
        }
        if (isset($data['mapsetting']["data-lat"]) && $data['mapsetting']['data-lat']) {

            $data['mapsetting']["data-mode"] = "static";
        }
        if ($hiddenPoints)
            $data['mapsetting']['data-hiddenpoints'] = $hiddenPoints;

        if ($countries)
            $data['mapsetting']['data-countries'] = $countries;

        if ($address)
        {
            $data['mapsetting']["data-address"] = $address;
        }

        if ($country)
        {
            $data['mapsetting']['data-country'] = $country;
        }

        $store_id = (int)$this->config->get('config_store_id');

        $this->load->model("extension/pplcz/setting");

        $parcelPlaces  = $this->model_extension_pplcz_setting->getParcelPlaces();
        $languageMap = $parcelPlaces->getMapLanguage();
        $lang = strtolower($languageMap ?: "");
        if (!in_array($lang, ["cs", "en"]))
            $lang = 'cs';

        $data['mapsetting']['data-language'] = $lang;
        if (defined('HTTP_CATALOG'))
            $httpcatalog = rtrim(HTTP_CATALOG, '/');
        else
            $httpcatalog = $this->config->get("config_url");
        $data['fronturl'] = $httpcatalog;
        $content = $this->load->view('extension/pplcz/shipping/map', $data);



        $this->response->setOutput($content);
    }
}