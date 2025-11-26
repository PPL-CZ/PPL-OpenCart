<?php
namespace Opencart\Admin\Controller\Extension\Pplcz\Api;

use PPLCZ\Admin\Controller\BaseController;
use PPLCZ\Admin\Controller\TOrder;


require_once  __DIR__ . '/../../../autoload.php';


class PplczCodFee extends BaseController
{
    use TOrder;


    public function index()
    {
        return $this->renderOrder();
    }
}
