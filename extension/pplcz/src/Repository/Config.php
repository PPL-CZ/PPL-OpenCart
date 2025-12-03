<?php
namespace PPLCZ\Repository;


use Opencart\Admin\Model\Extension\Pplcz\Normalizer;
use Opencart\Admin\Model\Localisation\Country;
use Opencart\Admin\Model\Setting\Setting;
use Opencart\Admin\Model\Setting\Store;
use Opencart\System\Engine\Model;
use PPLCZ\Model\Model\CollectionAddressModel;
use PPLCZ\Model\Model\CountryModel;
use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\Model\Model\ShopModel;


/**
 * @property-read Setting $model_setting_setting
 * @property-read CPLOperation $model_extension_pplcz_cploperation
 * @property-read Normalizer $model_extesion_pplcz_normalizer
 * @property-read Store $model_setting_store
 * @property-read Country $model_localisation_country
 */
class Config extends Model
{

    use TRepositorySetting;

    public function getCodCurrencies()
    {
        $content = $this->getSetting("cod_currencies");
        if ($content)
        {
            $value = @json_decode($content, true);
            if ($value)
                return $value;
        }

        $this->load->model("extension/pplcz/cploperation");

        try {

            $currencies = $this->model_extension_pplcz_cploperation->getCodCurrencies();
            if ($currencies) {
                $this->setSetting("cod_currencies", json_encode($currencies));
                return $currencies;
            }
        }
        catch (\Exception $ex)
        {
            return [];
        }

        return  [];
    }

    public function getCollectionAddress()
    {

        $this->load->model("extesion/pplcz/normalizer");

        $content = $this->getSetting("collection_address");

        if ($content)
        {
            $value = @json_decode($content, true);
            if ($value)
                return $value;
        }

        $this->load->model("extension/pplcz/cploperation");

        try {

            $collectionAddresses = $this->model_extension_pplcz_cploperation->getCollectionAddress();
            foreach ($collectionAddresses as $key => $value) {
                $collectionAddresses[$key] = $this->model_extesions_pplcz_normalizer->denormalize($value, CollectionAddressModel::class);
            }

            if ($collectionAddresses) {
                $collectionAddress = array_filter($collectionAddresses, function (CollectionAddressModel $model) {
                    return $model->getCode() === "PICK";
                });
                if ($collectionAddress) {
                    $address = reset($collectionAddress);
                    $address = $this->model_extesions_pplcz_normalizer->normalize($address);
                    $this->setSetting("collection_address", json_encode($address));
                    return $address;
                }
            }
        }
        catch (\Exception $ex)
        {
            return [];
        }

        return  [];
    }

    /**
     * @return CountryModel[]
     */
    public function getCountries()
    {
        $countries = $this->getSetting("countries");
        if ($countries) {
            $countries = @json_decode($countries, true);
            if (!$countries)
                try {
                    $this->load->model("extension/pplcz/cploperation");
                    $countries = $this->model_extension_pplcz_cploperation->getCountries();
                    if ($countries) {
                        $this->setSetting("countries", json_encode($countries));
                    }
                }
                catch (\Exception $ex)
                {

                }
        }


        if (!$countries)
        {
            $countries = [
                'CZ' => true,
                'DE' => false,
                'GB' => false,
                'SK' => true,
                'AT' => false,
                'PL' => true,
                'CH' => false,
                'FI' => false,
                'HU' => true,
                'SI' => false,
                'LV' => false,
                'EE' => false,
                'LT' => false,
                'BE' => false,
                'DK' => false,
                'ES' => false,
                'FR' => false,
                'IE' => false,
                'IT' => false,
                'NL' => false,
                'NO' => false,
                'PT' => false,
                'SE' => false,
                'RO' => true,
                'BG' => false,
                'GR' => false,
                'LU' => false,
                'HR' => false,
            ];
        }

        $output = [];

        $this->load->model("localisation/country");
        $data_countries = $this->model_localisation_country->getCountries();
        $cod = $this->getCodCurrencies();

        foreach ($countries as $key => $country)
        {
            $c = new CountryModel();
            $c->setCode($key);

            $c->setCodAllowed(array_unique(array_map(function ($item) {
                return $item['currency'];
            }, array_filter($cod, function ($item) use ($key) {
                return $item['country'] === $key;
            }))));

            $data_country = array_filter($data_countries, function ($item) use($key)
            {
                return $item['iso_code_2'] ===  $key;
            });


            if ($data_country) {
                $data_country = reset($data_country);
                $c->setTitle($data_country['name']);
            } else {
                $c->setTitle($key);
            }
            $c->setParcelAllowed(in_array($key, ["CZ", "PL", "DE", "SK"], true));
            $output[] = $c;
        }

        return $output;
    }

    public function getCurrencies()
    {
        return [
            "AL" => "ALL",  // Albánie - Lek
            "AD" => "EUR",  // Andorra - Euro
            "AM" => "AMD",  // Arménie - Arménský dram
            "AT" => "EUR",  // Rakousko - Euro
            "AZ" => "AZN",  // Ázerbájdžán - Ázerbájdžánský manat
            "BY" => "BYN",  // Bělorusko - Běloruský rubl
            "BE" => "EUR",  // Belgie - Euro
            "BA" => "BAM",  // Bosna a Hercegovina - Konvertibilní marka
            "BG" => "BGN",  // Bulharsko - Bulharský lev
            "HR" => "HRK",  // Chorvatsko - Chorvatská kuna
            "CY" => "EUR",  // Kypr - Euro
            "CZ" => "CZK",  // Česká republika - Česká koruna
            "DK" => "DKK",  // Dánsko - Dánská koruna
            "EE" => "EUR",  // Estonsko - Euro
            "FI" => "EUR",  // Finsko - Euro
            "FR" => "EUR",  // Francie - Euro
            "GE" => "GEL",  // Gruzie - Gruzínský lari
            "DE" => "EUR",  // Německo - Euro
            "GR" => "EUR",  // Řecko - Euro
            "HU" => "HUF",  // Maďarsko - Maďarský forint
            "IS" => "ISK",  // Island - Islandská koruna
            "IE" => "EUR",  // Irsko - Euro
            "IT" => "EUR",  // Itálie - Euro
            "KZ" => "KZT",  // Kazachstán - Kazachstánský tenge
            "XK" => "EUR",  // Kosovo - Euro
            "LV" => "EUR",  // Lotyšsko - Euro
            "LI" => "CHF",  // Lichtenštejnsko - Švýcarský frank
            "LT" => "EUR",  // Litva - Euro
            "LU" => "EUR",  // Lucembursko - Euro
            "MT" => "EUR",  // Malta - Euro
            "MD" => "MDL",  // Moldavsko - Moldavský lei
            "MC" => "EUR",  // Monako - Euro
            "ME" => "EUR",  // Černá Hora - Euro
            "NL" => "EUR",  // Nizozemsko - Euro
            "MK" => "MKD",  // Severní Makedonie - Makedonský denár
            "NO" => "NOK",  // Norsko - Norská koruna
            "PL" => "PLN",  // Polsko - Polský zlotý
            "PT" => "EUR",  // Portugalsko - Euro
            "RO" => "RON",  // Rumunsko - Rumunský lei
            "RU" => "RUB",  // Rusko - Ruský rubl
            "SM" => "EUR",  // San Marino - Euro
            "RS" => "RSD",  // Srbsko - Srbský dinár
            "SK" => "EUR",  // Slovensko - Euro
            "SI" => "EUR",  // Slovinsko - Euro
            "ES" => "EUR",  // Španělsko - Euro
            "SE" => "SEK",  // Švédsko - Švédská koruna
            "CH" => "CHF",  // Švýcarsko - Švýcarský frank
            "TR" => "TRY",  // Turecko - Turecká lira
            "UA" => "UAH",  // Ukrajina - Ukrajinská hřivna
            "GB" => "GBP",  // Velká Británie - Britská libra
            "VA" => "EUR"   // Vatikán - Euro
        ];

    }


    public function getServiceCodes() {
        return [
            "SMAR" => "PPL Parcel CZ Smart",
            "PRIV" => "PPL Parcel CZ Private",

            "SMEU" => "PPL Parcel Smart Europe",
            "CONN" => "PPL Parcel Connect"
        ];
    }

    /**
     * @param $code
     * @return false|mixed|ShipmentMethodModel
     */
    public function getService($code)
    {
            $services = array_filter($this->getAllServices(), function ($item) use ($code) {
               if ($code === $item->getCode()) {
                   return true;
               }
               return false;
            });
        return reset($services);
    }

    /**
     * @return ShipmentMethodModel[]
     */
    public function getAllServices()
    {
        $methods =  [
            "SMAR" => "PPL Parcel CZ Smart",
            "SMAD" => "PPL Parcel CZ Smart (dobírka)",
            "PRIV" => "PPL Parcel CZ Private",
            "PRID" => "PPL Parcel CZ Private (dobírka)",

            "SMEU" => "PPL Parcel Smart Europe",
            "SMED" => "PPL Parcel Smart Europe (dobírka)",
            "CONN" => "PPL Parcel Connect",
            "COND" => "PPL Parcel Connect (dobírka)"
        ];

        $output = [];
        foreach ($methods as $key => $service)
        {
            $val= new ShipmentMethodModel();
            $val->setCode($key);
            $val->setTitle($service);
            if (substr($key, 3) === 'D')
                $val->setCodAvailable(true);
            else
                $val->setCodAvailable(false);
            $val->setParcelRequired(in_array($key, ["SMAR", "SMAD", "SMEU", "SMED"]));
            $output[] = $val;
        }

        return $output;

    }

    public function isParcelRequired($code)
    {
        return in_array($code, ["SMAD", "SMAR", "SMED","SMEU" ], true);
    }

    public function getCodName($code)
    {
        $services = $this->getServiceCodes();

        if (substr($code, -1) === "D")
            return $code;
        foreach ($services as $key=>$val) {
            if ($code === $key) {
                $asCod = substr($key, 0, 3) . 'D';
                return $asCod;
            }

        }
        return false;
    }

    public function getLimits()
    {
        $this->load->model("setting/setting");

        $limits = $this->getSetting("limits") ;
        if ($limits) {

            $limits = @json_decode($limits, true);
            if ($limits)
                return $limits;
        }

        $this->load->model("extension/pplcz/cploperation");

        try {
            $limits = $this->model_extension_pplcz_cploperation->getLimits();
            if ($limits) {
                $this->setSetting("limits", json_encode($limits));
                return $limits;
            }
        }
        catch (\Exception $ex)
        {

        }

        return [
            "INSURANCE"=> [
                [ "product"=>"BUSS", "min"=> 50000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"BUSD", "min"=> 50000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"DOPO", "min"=> 50000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"DOPD", "min"=> 50000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"COPL", "min"=> 100000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"PRIV", "min"=> 50000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"PRID", "min"=> 50000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"CONN", "min"=> 100000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"COND", "min"=> 100000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"RETD", "min"=> 50000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"SMAR", "min"=> 20000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"SMAD", "min"=> 20000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"SMEU", "min"=> 100000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"SMED", "min"=> 100000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"RECI", "min"=> 100000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"RECE", "min"=> 100000.01, "max"=>500000, 'country' => "CZ", 'currency'=>"CZK" ],
            ],
            "COD" => [
                [ "product"=>"BUSD", "min"=> 0.01, "max"=>100000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"DOPD", "min"=> 0.01, "max"=>100000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"PRID", "min"=> 0.01, "max"=>100000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"SMAD", "min"=> 0.01, "max"=>100000, 'country' => "CZ", 'currency'=>"CZK" ],
                [ "product"=>"COND", "min"=> 0.01, "max"=>80000, 'country' => "SK", 'currency'=>"CZK" ],
                [ "product"=>"SMED", "min"=> 0.01, "max"=>80000, 'country' => "SK", 'currency'=>"CZK" ],
                [ "product"=>"COND", "min"=> 0.01, "max"=>3000, 'country' => "SK", 'currency'=>"EUR" ],
                [ "product"=>"SMED", "min"=> 0.01, "max"=>3000, 'country' => "SK", 'currency'=>"EUR" ],
                [ "product"=>"COND", "min"=> 0.01, "max"=>6500, 'country' => "PL", 'currency'=>"PLN" ],
                [ "product"=>"SMED", "min"=> 0.01, "max"=>6500, 'country' => "PL", 'currency'=>"PLN" ],
                [ "product"=>"COND", "min"=> 1, "max"=>600000, 'country' => "HU", 'currency'=>"HUF" ],
                [ "product"=>"COND", "min"=> 0.01, "max"=>10500, 'country' => "HR", 'currency'=>"HRK" ],
                [ "product"=>"COND", "min"=> 0.01, "max"=>7300, 'country' => "RO", 'currency'=>"RON" ],
                [ "product"=>"COND", "min"=> 0.01, "max"=>500, 'country' => "SI", 'currency'=>"EUR" ],
                [ "product"=>"COND", "min"=> 0.01, "max"=>2900, 'country' => "BG", 'currency'=>"BGN" ],
            ]
        ];
    }

    public function getShipmentPhases()
    {
        $phases = $this->getSetting("phases");
        if ($phases) {
            $phases = @json_decode($phases, true);
            if ($phases)
                return $phases;
        }

        $this->load->model("extension/pplcz/cploperation");
        try {
            $phases = $this->model_extension_pplcz_cploperation->getShipmentPhases();
            if ($phases)
            {
                $this->setSetting("phases", json_encode($phases));
                return $phases;
            }
        }
        catch (\Exception $ex)
        {

        }

        return [
            "Order" => "Objednávka",
            "InTransport" => "V přepravě",
            "Delivering" => "Na cestě",
            "PickupPoint" => "Na výdejním místě",
            "CodPayed" => "Zaplacená dobírka",
            "Delivered" => "Doručeno",
            "Returning"=> "Na cestě zpět odesílateli",
            "BackToSender" => "Vráceno odesílateli",
        ];

    }

    public function getStatuses()
    {


        $statuses = $this->getSetting("statuses");
        if ($statuses)
        {
            $statuses = @json_decode($statuses, true);
            return $statuses;
        }

        $this->load->model("extension/pplcz/cploperation");

        try {
            $statuses = $this->model_extension_pplcz_cploperation->getStatuses();
            if ($statuses)
            {
                $this->setSetting("statuses", json_encode($statuses));
                return $statuses;
            }
        }
        catch (\Exception $ex)
        {

        }
        return $statuses;
    }

    public function getShops()
    {
        $this->load->model("setting/store");
        $stores = $this->model_setting_store->getStores();

        $base = new ShopModel();
        $base->setId(0);
        $base->setName( $this->config->get('config_name'));

        $output = [
            $base
        ];

        foreach ($stores as $store)
        {
            $shopmodel = new ShopModel();
            $shopmodel->setName($store['name']);
            $shopmodel->setId($store['store_id']);
            $output[] = $shopmodel;
        }

        return $output;
    }

}