<?php
namespace Opencart\Admin\Controller\Extension\Pplcz\Total;

use Opencart\System\Library\Document;
use Opencart\System\Library\Request;
use PPLCZ\Admin\Controller\BaseController;
use PPLCZ\Admin\Controller\TCodelist;
use PPLCZ\Admin\Controller\TSetting;

require_once  __DIR__ . '/../../../autoload.php';


class PplczCodFee extends BaseController
{
    public function index()
    {
        $this->load->language('extension/pplcz/shipping/pplcz');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping')
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/pplcz/shipping/pplcz', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping');


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['user_token'] = $this->session->data["user_token"];

        $this->response->setOutput($this->load->view('extension/pplcz/shipping/setting', $data));

    }
}