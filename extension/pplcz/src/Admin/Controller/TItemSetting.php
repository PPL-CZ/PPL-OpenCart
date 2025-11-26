<?php

namespace PPLCZ\Admin\Controller;

use Opencart\Admin\Controller\Catalog\Product;
use Opencart\Admin\Model\Setting\Setting;
use Opencart\System\Library\Document;
use Opencart\System\Library\Request;
use PPLCZ\Admin\Controller\BaseController;
use PPLCZ\Model\Model\CategoryModel;
use PPLCZ\Model\Model\MyApi2;
use PPLCZ\Model\Model\ParcelPlacesModel;
use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\SenderAddressModel;
use PPLCZ\Model\Model\ShipmentMethodSettingModel;
use PPLCZ\Model\Model\ShipmentPhaseModel;
use PPLCZ\Model\Model\UpdateSyncPhasesModel;
use PPLCZ\Validator\WP_Error;

/**
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Setting $model_extension_pplcz_setting
 * @property-read  \Opencart\Admin\Model\Extension\Pplcz\Normalizer $model_extension_pplcz_normalizer
 * @property-read  Setting $model_setting_setting
 * @mixin BaseController
 */
trait TItemSetting
{
    public function saveItemSetting(&$route, &$args, &$content) {

        $operation = explode("/", $route)[1];

        $item_id = null;
        $type = null;

        switch ($operation) {
            case 'product.addProduct':
            case 'product.addVariant':
            case 'product.editProduct':
            case 'product.editVariant':
                $item_id = $content ?: $args[0];
                $type = 'product';
                break;
            case 'category.addCategory':
            case 'category.editCategory':
                $item_id = $content ?: $args[0];
                $type = 'category';
                break;
        }

        if ($type)
        {
            $data = $_POST;
            if (isset($this->request->post['ppl']))
            {
                $this->load->model("extension/pplcz/normalizer");
                $this->load->model("extension/pplcz/setting");
                $data = $this->request->post['ppl'];
                switch ($type)
                {
                    case 'category':
                        $category = $this->model_extension_pplcz_normalizer->denormalize($data, CategoryModel::class);
                        $this->model_extension_pplcz_setting->setCategory($item_id, $category);
                        break;
                    case 'product':
                        $product = $this->model_extension_pplcz_normalizer->denormalize($data, ProductModel::class);
                        $this->model_extension_pplcz_setting->setProduct($item_id, $product);
                        break;
                }
            }
        }
        return;
    }

    public function injectItemSettingAdminJS()
    {
        /**
         * @var Document $document
         * @var Request $request
         */
        $document = $this->document;
        $request = $this->request;

        if (!empty($this->request->get['route'])
            && ($this->request->get['route'] === 'catalog/product.form'
                || $this->request->get['route'] === 'catalog/category.form'
            )) {

            $server = HTTP_SERVER;

            $urls = parse_url($server);
            if (!isset($urls['path']) || !$urls['path'])
            {
                $path = [];
            }
            else
            {
                $path = explode('/', trim($urls['path'], '/'));
                array_pop($path);
            }

            $path[] = "extension/pplcz/src/Admin/MuiAdmin/build/static/js/bundle.js";
            $urls['path'] = join('/', $path);
            $server = $urls['scheme']  . '://'. $urls['host'] . '/' . $urls['path'];

            $document->addScript($server);
            return;
        }
    }

    public function injectItemSettingFooter(&$route, &$args, &$content) {

        if (isset($this->request->get['route'])) {
            $requestRoute = $this->request->get['route'];
            $data = ['product' => false, 'category'=> false];

            switch ($requestRoute) {
                case 'catalog/product.form':
                    $id = $this->request->get['product_id'];
                    $this->load->model("extension/pplcz/setting");
                    $this->load->model("extension/pplcz/normalizer");
                    $this->load->model("extension/pplcz/config");
                    $data['show'] = true;
                    $data['data'] = $this->model_extension_pplcz_normalizer->normalize($this->model_extension_pplcz_setting->getProduct($id));
                    $data['methods'] = array_map(function($item) {
                        return $this->model_extension_pplcz_normalizer->normalize($item);
                    }, $this->model_extension_pplcz_config->getAllServices());
                    break;
                case 'catalog/category.form':
                    $id = $this->request->get['category_id'];
                    $this->load->model("extension/pplcz/setting");
                    $this->load->model("extension/pplcz/normalizer");
                    $this->load->model("extension/pplcz/config");
                    $data['show'] = true;
                    $data['data'] = $this->model_extension_pplcz_normalizer->normalize($this->model_extension_pplcz_setting->getCategory($id));
                    $data['methods'] = array_map(function($item) {
                        return $this->model_extension_pplcz_normalizer->normalize($item);
                    }, $this->model_extension_pplcz_config->getAllServices());
                    break;
            }
            if (array_filter($data)) {
                $content = $this->load->view("extension/pplcz/shipping/footer", $data) . $content;
            }
        }
    }

}