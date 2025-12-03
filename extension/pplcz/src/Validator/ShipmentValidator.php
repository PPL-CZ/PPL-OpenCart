<?php
namespace PPLCZ\Validator;

use Opencart\Admin\Model\Extension\Pplcz\Config;
use Opencart\System\Engine\Registry;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;

class ShipmentValidator extends ModelValidator
{
    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
    }

    public function canValidate($model)
    {
        return $model instanceof ShipmentModel
                || $model instanceof UpdateShipmentModel;
    }



    public function validate($model, $errors, $path)
    {
        /**
         * @var Config $config
         * @var Validator $validator
         */
        $config = $this->loadModel("extension/pplcz/config");
        $validator = $this->loadModel("extension/pplcz/validator");

        if ($model instanceof UpdateShipmentModel) {
            foreach (["referenceId" => "Reference pro objednávku zásilky nesmí zůstat prázdná", "serviceCode" => "Není vybraná služba"] as $item => $message ) {
                if (!$this->getValue($model, $item)) {
                    $errors->add("$path.{$item}", $message);
                }
            }

            if ($model->getServiceCode()) {
                $code = $model->getServiceCode();
                $service = $config->getService($code);
                $isCod = $service->getCodAvailable();

                if ($isCod) {
                    foreach (["codVariableNumber" => "Variabilní číslo musí být vyplněno", "codValue" => "Hodnota dobírky není určena", "codValueCurrency" => "Není určena měna dobírky", "senderId" =>"Je potřeba určit odesílatele pro etiketu"] as $item => $message) {
                        if (!$this->getValue($model, $item)) {
                            $errors->add("$path.{$item}", $message);
                        }
                    }
                }
                if ($code) {
                    if (in_array($code, ["SMEU", "CONN", "SMED", "COND"])
                        && count($model->getPackages()) > 1) {
                        $errors->add("$path.packages", "Počet zásilek je omezen na jednu");
                    }
                }

            }
            if (!$model->getPackages())
            {
                $errors->add("$path.packages", "Přidejte aspoň jednu zásilku");
            }

            foreach ($model->getPackages() as $key=>$package) {
                $validator->validate($package, $errors, "{$path}.packages.{$key}");
            }


        }


        if ($model instanceof ShipmentModel) {
            /**
             * @var ShipmentModel $model
             */
            foreach (["referenceId" => "Je nutné vyplnit referenci zásilky",
                         "serviceCode" => "Je nutné vybrat službu",
                         "sender" => "Je nutné určit odesílatele pro etiketu",
                         "recipient" => "Není určen příjemce zásilky"] as $item => $message)
            {
                if (!$this->getValue($model, $item)) {
                    $errors->add("$path.{$item}", $message);
                }
                else if ($item === "sender" || $item === "recipient") {
                    $validator->validate($this->getValue($model, $item), $errors, "$path.$item");
                }
            }

            $code = $this->getValue($model, 'serviceCode');
            if ($code) {
                if (in_array($code, ["SMEU", "CONN", "SMED", "COND"])
                    && count($model->getPackages()) > 1) {
                    $errors->add("$path.packages", "Počet balíčku může být pouze 1");
                }
            }
        }
    }
}