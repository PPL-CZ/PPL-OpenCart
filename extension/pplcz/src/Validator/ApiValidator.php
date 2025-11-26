<?php
namespace PPLCZ\Validator;


use Opencart\System\Engine\Registry;
use PPLCZ\Model\Model\MyApi2;

class ApiValidator extends ModelValidator
{

    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
    }

    public function canValidate($model)
    {
        return $model instanceof MyApi2;
    }

    public function validate($model, $errors, $path)
    {
        /**
         * @var MyApi2 $model
         * @var WP_Error $errors
         */
        $clientId = $this->getValue($model, "clientId") ?: "";
        if (strlen($clientId) < 5)
            $errors->add($path . ".clientId", "Příliš krátké client Id  (min 5 znaků)");

        $clientSecret = $this->getValue($model, "clientSecret") ?: "";

        if (strlen($clientSecret) < 10)
            $errors->add($path . ".clientSecret", "Příliš krátké client secret (min 10 znaků)");
    }
}