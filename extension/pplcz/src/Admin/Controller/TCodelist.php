<?php
namespace PPLCZ\Admin\Controller;

use Opencart\Admin\Model\Extension\Pplcz\Config;
use Opencart\Admin\Model\Extension\Pplcz\Cploperation;
use Opencart\System\Engine\Controller;
use PPLCZ\Model\Model\CurrencyModel;

/**
 * @property-read Config $model_extension_pplcz_config
 * @property-read Cploperation $model_extension_pplcz_cploperation
 * @mixin BaseController
 */
trait TCodelist
{
    public function methods()
    {
        $this->load->model("extension/pplcz/config");
        $services  =$this->model_extension_pplcz_config->getAllServices();
        $this->sendJson($services);
    }

    public function currencies()
    {
        $this->load->model('localisation/currency');
        $currencies = $this->model_localisation_currency->getCurrencies();

        foreach ($currencies as $currency)
        {
            $output[] = new CurrencyModel([
                "code" => $currency['code'],
                "title" => $currency['title'],
            ]);
        }
        $this->sendJson($output);
    }

    public function countries()
    {
        if (!$this->validateToken()
            || !$this->validateMethod("get"))
            return;

        $this->load->model('extension/pplcz/config');
        $countries = $this->model_extension_pplcz_config->getCountries();

        $this->sendJson($countries);
    }

    public function availablePrinters()
    {
        if (!$this->validateToken()
            || !$this->validateMethod("get"))
            return;

        $this->load->model('extension/pplcz/cploperation');

        $availablePrinters = $this->model_extension_pplcz_cploperation->getAvailableLabelPrinters();
        $this->sendJson($availablePrinters);

    }

    public function shops()
    {
        if (!$this->validateToken()
            ||!$this->validateMethod("get"))
            return;

        $this->load->model('extension/pplcz/config');
        $shops = $this->model_extension_pplcz_config->getShops();

        $this->sendJson($shops);
    }

    public function payments()
    {

        if (!$this->validateToken()
            ||!$this->validateMethod("get"))
            return;

        $this->load->model('setting/extension');
        $extensions = $this->model_setting_extension->getExtensionsByType('payment');
        $output = [];
        foreach ($extensions as $extension)
        {
            $output[] = $extension['code'];
        }

        $this->sendJson($output);

    }

}