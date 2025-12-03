<?php
namespace PPLCZ\Validator;

use Opencart\System\Engine\Registry;

use PPLCZ\Data\ParcelData;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;
use PPLCZ\Repository\Config;


class ParcelValidator extends ModelValidator
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
        $code = $this->getValue($model, 'serviceCode');
        /**
         * @var ShipmentModel|UpdateShipmentModel $model
         */

        if ($model->isInitialized("age")
            && $model->getAge()
            && in_array($code, ["SMEU", "SMED", "CONN", "COND"])) {
            //$errors->add("$path.age", "Mimo ČR nelze dělat kontrolu věku");
        }
        else if ($model->isInitialized("age") && $model->getAge()
            && $model->isInitialized("hasParcel") && $model->getHasParcel()) {
            if ($model->isInitialized("parcel") && $model->getParcel()) {
                $parcel = $model->getParcel();
                if ($parcel->getType() !== "ParcelShop") {
                    $errors->add("$path.hasParcel", "Výdejní misto může být pouze obchod kvůli kontrole věku");
                }
            }
        }

        $parcelid = $this->getValue($model, "parcelId") ?: $this->getValue($model, "parcel.id");
        $code = $this->getValue($model, 'serviceCode');

        $parcelLoader = $this->loadModel("extension/pplcz/parcel");

        if (in_array($code, ["SMAD", "SMAR"])) {
            if ($parcelid)
            {
                $parcelData = $parcelLoader->load(ParcelData::class, $parcelid);
                if ($parcelData->get_country() !== "CZ") {
                    $errors->add("$path.hasParcel", "Pro službu lze vybrat pouze české výdejní místo");
                }
            }
        } else if (in_array($code, ["SMEU", "SMED"])) {
            if ($parcelid) {
                $parcelData = $parcelLoader->load(ParcelData::class, $parcelid);
                if ($parcelData->get_country() === "CZ") {
                    $errors->add("$path.hasParcel", "Pro službu lze vybrat pouze zahraniční výdejní místo");
                }
            }
        }

        if ($model instanceof ShipmentModel) {
            if ($model->isInitialized("serviceCode") && $model->getServiceCode()) {
                $code = $model->getServiceCode();

                /**
                 * @var Config $configLoader
                 */
                $configLoader = $this->loadModel("extension/pplcz/config");
                $service = $configLoader->getService($code);

                if (!$service->getParcelRequired() && $this->getValue($model, "hasParcel")) {
                    $errors->add("$path.hasParcel", "Metoda neumožňuje výběr výdejního místa");
                } else if ($service->getParcelRequired() && (!$this->getValue($model, "hasParcel") || !$this->getValue($model, "parcel"))) {
                    $errors->add("$path.hasParcel", "Je potřeba vybrat výdejní místo");
                }

                if (in_array($code, ["PRIV", "PRID", "SMAR", "SMAD"]) && $model->isInitialized("recipient"))
                {
                    if ($this->getValue($model, "recipient.country") !== "CZ") {
                        $errors->add("$path.recipient.country", "Služba není určena pro dopravu z České republiky do zahraničí");
                    }
                }
                else if(in_array($code, ["SMEU", "SMED", "CONN", "COND"]) && $model->isInitialized("recipient"))
                {
                    if ($this->getValue($model, "recipient.country") === "CZ") {
                        $errors->add("$path.recipient.country", "Služba není určena pro dopravu v rámci České republiky");
                    }
                }
            }
        }

    }
}