<?php
namespace  PPLCZ\Validator;




use Opencart\Admin\Model\Extension\Pplcz\Config;
use Opencart\Admin\Model\Extension\Pplcz\Setting;
use Opencart\System\Engine\Registry;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;

class InsuranceValidator extends ModelValidator
{
    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
    }

    public function canValidate($model)
    {
        return $model instanceof ShipmentModel || $model instanceof UpdateShipmentModel;
    }


    public function validate($model, $errors, $path)
    {
        /**
         * @var ShipmentModel|UpdateShipmentModel $model
         */
        if (!$model->isInitialized("serviceCode"))
            return;

        $code = $model->getServiceCode();

        /**
         * @var Config $config
         */
        $config = $this->registry->get("model_extension_pplcz_config");

        $limits = $config->getLimits();
        $insuranceLimits = array_filter($limits["INSURANCE"], function ($item) use ($code) {
            return  $code === $item['product'];
        });
        $insuranceLimits = reset($insuranceLimits);
        if ($model->isInitialized("packages")) {
            $packages = $model->getPackages();

            foreach ($packages as $key => $package) {
                if ($package->getInsurance()) {
                    $insurance = $package->getInsurance();
                    if (!$insuranceLimits && $insurance) {
                        $errors->add("$path.packages.{$key}.insurance", "Nelze nastavovat pojištění.");
                        continue;
                    }
                    if ($insurance && $insurance < $insuranceLimits["min"]) {
                        $errors->add("$path.packages.{$key}.insurance", "Minimální částka při pojištění je {$insuranceLimits['min']}CZK.");
                        continue;
                    }
                    if ($insurance && $insurance > $insuranceLimits["max"]) {
                        $errors->add("$path.packages.{$key}.insurance", "Maximální částka při pojištění je {$insuranceLimits['max']}CZK.");
                        continue;
                    }
                }
            }
        }
    }
}