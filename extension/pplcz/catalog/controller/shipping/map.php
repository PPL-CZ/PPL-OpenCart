<?php
namespace Opencart\Catalog\Controller\Extension\Pplcz\Shipping;

use Opencart\System\Engine\Controller;
use PPLCZ\Controller\TMap;

require_once  __DIR__ . '/../../../autoload.php';

class Map extends Controller {
    use TMap;

    public function index()
    {
        $this->map();
    }
}