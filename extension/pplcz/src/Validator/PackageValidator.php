<?php
namespace PPLCZ\Validator;


use Opencart\System\Engine\Registry;

use PPLCZ\Model\Model\PackageModel;
use PPLCZ\Validator\ModelValidator;

class PackageValidator extends ModelValidator {
    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
    }

    public function canValidate($model)
    {
        return $model instanceof PackageModel;
    }

    public function validate($model, $errors, $path)
    {
        /**
         * @var PackageModel $model
         */
        if ($model->getWeight()) {
            if ($model->getWeight() <= 0)
            {
                $errors->add("$path.weight", 'Váha u zásilky musí být kladné číslo');
            }
        }
    }
}
