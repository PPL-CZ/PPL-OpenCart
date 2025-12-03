<?php

namespace PPLCZ\Validator;
use Opencart\System\Engine\Registry;



class Validator extends ModelValidator
{
    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
    }

    public function validate($model, $errors, $path = "")
    {
        foreach (ModelValidator::$validators as $key => $validator) {
            if (is_string($validator)) {
                $validator = new $validator($this->registry);
                ModelValidator::$validators[$key] = $validator;
            }
            /**
             * @var ModelValidator $validator
             */
            if ($validator->canValidate($model))
                $validator->validate($model, $errors, $path);
        }

    }

    public function canValidate($model)
    {
        throw new \Exception("undefined");
    }

}