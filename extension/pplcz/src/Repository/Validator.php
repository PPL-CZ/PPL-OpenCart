<?php
namespace PPLCZ\Repository;

use Opencart\System\Engine\Model;
use PPLCZ\Serializer;
use PPLCZ\Validator\WP_Error;

require_once __DIR__ . '/../../build/vendor/autoload.php';

class Validator extends Model
{
    private $validator;

    /**
     * @param $data
     * @param ?WP_Error $errors
     * @param ?string $path

     */
    public function validate($data, $errors = null, $path = "")
    {
        if (!$this->validator)
            $this->validator = new \PPLCZ\Validator\Validator($this->registry);

        if (is_string($errors))
            list($errors, $path) = [$path, $errors];

        if ($errors === null)
            $errors = new WP_Error();

        $this->validator->validate($data, $errors, $path);
        return $errors;
    }

}